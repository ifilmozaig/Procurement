<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\ExpenseMasterResource;
use App\Models\Company;
use App\Models\ExpenseMasterItem;
use App\Models\ProcurementItem;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RealisasiPengadaan extends Page
{
    protected static string $resource = ExpenseMasterResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return true;
    }

    public function getView(): string
    {
        return 'Admin.realisasi-pengadaan';
    }

    public string $filterCompany  = 'all';
    public string $filterMonth    = '';
    public string $filterYear     = '';
    public string $filterDateFrom = '';
    public string $filterDateTo   = '';

    public bool   $showDownloadModal = false;
    public string $downloadType      = '';
    public string $downloadDateFrom  = '';
    public string $downloadDateTo    = '';

    public function mount(): void
    {
        $this->filterYear       = now()->format('Y');
        $this->filterMonth      = now()->format('m');
        $this->downloadDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->downloadDateTo   = now()->format('Y-m-d');
    }

    public function getTableData(): Collection
    {
        return $this->getTableDataWithFilters(
            $this->filterCompany,
            $this->filterMonth,
            $this->filterYear,
            $this->filterDateFrom,
            $this->filterDateTo
        );
    }

    public function getActiveCompanies(): Collection
    {
        // Ambil company dari DUA sumber: pivot baru + kolom lama
        // Hanya dari procurement yang sudah APPROVED atau COMPLETED
        $fromPivot = DB::table('procurement_item_companies')
            ->join('procurement_items', 'procurement_items.id', '=', 'procurement_item_companies.procurement_item_id')
            ->join('procurements', 'procurements.id', '=', 'procurement_items.procurement_id')
            ->whereIn('procurements.status', ['APPROVED', 'COMPLETED'])
            ->distinct()
            ->pluck('procurement_item_companies.company_id');

        $fromLegacy = ProcurementItem::whereNotNull('company_id')
            ->whereHas('procurement', fn ($q) =>
                $q->whereIn('status', ['APPROVED', 'COMPLETED'])
            )
            ->distinct()
            ->pluck('company_id');

        $allIds = $fromPivot->merge($fromLegacy)->unique()->values();

        return Company::whereIn('id', $allIds)->orderBy('name')->get();
    }

    protected function getTableDataWithFilters(
        string $company,
        string $month,
        string $year,
        string $dateFrom,
        string $dateTo
    ): Collection {
        $masterItems = ExpenseMasterItem::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->sortBy(fn ($item) => [
                $item->category?->sort_order ?? 999,
                $item->sort_order,
            ]);

        $procQuery = ProcurementItem::with(['procurement', 'company', 'companies'])
            ->whereNotNull('expense_master_item_id')
            ->whereHas('procurement', fn ($q) =>
                $q->whereIn('status', ['APPROVED', 'COMPLETED'])
            );

        // Filter company: cek KEDUA sumber (pivot + legacy)
        if ($company !== 'all') {
            $companyId = (int) $company;
            $procQuery->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                  ->orWhereHas('companies', fn ($sq) =>
                      $sq->where('companies.id', $companyId)
                  );
            });
        }

        if ($dateFrom && $dateTo) {
            $procQuery->whereHas('procurement', fn ($q) =>
                $q->whereBetween('created_at', [
                    $dateFrom . ' 00:00:00',
                    $dateTo   . ' 23:59:59',
                ])
            );
        } elseif ($month && $year) {
            $procQuery->whereHas('procurement', fn ($q) =>
                $q->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year)
            );
        }

        $procItems = $procQuery->get()->groupBy('expense_master_item_id');

        // Kumpulkan semua company ID dari KEDUA sumber
        if ($company === 'all') {
            $legacyIds = $procItems->flatten()->pluck('company_id')->filter();

            $pivotIds = DB::table('procurement_item_companies')
                ->whereIn(
                    'procurement_item_id',
                    $procItems->flatten()->pluck('id')
                )
                ->distinct()
                ->pluck('company_id');

            $allCompanyIds = $legacyIds->merge($pivotIds)->unique()->values();
            $companies     = Company::whereIn('id', $allCompanyIds)->orderBy('name')->get();
        } else {
            $companies = Company::where('id', (int) $company)->get();
        }

        $rows = collect();

        foreach ($masterItems as $masterItem) {
            $linked = $procItems->get($masterItem->id, collect());

            if ($company === 'all') {
                if ($linked->isEmpty() || $companies->isEmpty()) {
                    // Item belum ada procurement yang approved → has_procurement = false
                    $this->pushRow($rows, $masterItem, collect(), null);
                } else {
                    $hasAnyLinked = false;
                    foreach ($companies as $co) {
                        $linkedForCo = $linked->filter(
                            fn ($p) => $this->itemBelongsToCompany($p, $co->id)
                        );
                        if ($linkedForCo->isEmpty()) continue;
                        $hasAnyLinked = true;
                        $this->pushRow($rows, $masterItem, $linkedForCo, $co);
                    }
                    if (!$hasAnyLinked) {
                        $this->pushRow($rows, $masterItem, collect(), null);
                    }
                }
            } else {
                $co          = $companies->first();
                $linkedForCo = $co
                    ? $linked->filter(fn ($p) => $this->itemBelongsToCompany($p, $co->id))
                    : collect();
                $this->pushRow($rows, $masterItem, $linkedForCo, $co);
            }
        }

        return $rows
            ->sortBy(fn ($item) => [
                $item->category_sort_order,
                $item->sort_order,
            ])
            ->groupBy('category_name');
    }

    /**
     * Cek apakah ProcurementItem milik company tertentu,
     * dari KEDUA sumber: pivot baru (companies) + kolom lama (company_id).
     */
    private function itemBelongsToCompany(ProcurementItem $item, int $companyId): bool
    {
        if ($item->relationLoaded('companies')) {
            if ($item->companies->contains('id', $companyId)) {
                return true;
            }
        } else {
            if (DB::table('procurement_item_companies')
                ->where('procurement_item_id', $item->id)
                ->where('company_id', $companyId)
                ->exists()) {
                return true;
            }
        }

        return (int) $item->company_id === $companyId;
    }

    /**
     * Build 1 baris data untuk masterItem + company.
     */
    private function pushRow(
        Collection        $rows,
        ExpenseMasterItem $masterItem,
        Collection        $linkedForCo,
        ?Company          $co
    ): void {
        $totalQty = $linkedForCo->sum('quantity');
        if ($totalQty === 0 && $linkedForCo->isNotEmpty()) {
            $totalQty = $linkedForCo->count();
        }

        $estQtyItems = $linkedForCo->filter(function ($p) {
            $status = $p->procurement?->status ?? '';
            $type   = strtoupper($p->procurement?->type ?? '');
            // Semua yang masuk sini sudah APPROVED/COMPLETED (dijamin oleh query di atas)
            if (in_array($type, ['CAPEX', 'CASH_ADVANCE'])) {
                return in_array($status, ['APPROVED', 'COMPLETED']);
            }
            if ($type === 'OPEX') {
                return in_array($status, ['APPROVED', 'COMPLETED']);
            }
            return false;
        });
        $estQty = (int) $estQtyItems->sum('quantity');
        if ($estQty === 0 && $estQtyItems->isNotEmpty()) $estQty = $estQtyItems->count();

        // Completed: status COMPLETED + ada payment proof untuk company ini
        $completedProcs = $linkedForCo->filter(function ($p) use ($co) {
            $proc = $p->procurement;
            if (!$proc || $proc->status !== 'COMPLETED') return false;
            if ($co) {
                return \App\Models\ProcurementPaymentProof::where('procurement_id', $proc->id)
                    ->where('company_id', $co->id)->exists()
                    || $proc->payment_proof !== null;
            }
            return $proc->payment_proof !== null;
        });

        $isDone        = $completedProcs->isNotEmpty();
        $completedProc = $completedProcs->sortByDesc(fn ($p) => $p->procurement?->payment_proof_uploaded_at)->first();
        $approvedProc  = !$isDone ? $linkedForCo->first() : null;
        $procStatus    = $approvedProc?->procurement?->status;
        $totalEst      = $masterItem->estimated_price * max($totalQty, 1);

        // Realisasi dari procurement_payment_proofs
        $realisasiAmount = 0;
        if ($isDone) {
            foreach ($completedProcs as $p) {
                $proc = $p->procurement;
                if (!$proc) continue;
                if ($co) {
                    $proof = \App\Models\ProcurementPaymentProof::where('procurement_id', $proc->id)
                        ->where('company_id', $co->id)->first();
                    $realisasiAmount += $proof ? ($proof->realisasi_amount ?? 0) : ($proc->realisasi_amount ?? 0);
                } else {
                    $realisasiAmount += $proc->realisasi_amount ?? 0;
                }
            }
            if ($realisasiAmount === 0) $realisasiAmount = $totalEst;
        }

        $showEstQty = $linkedForCo->contains(function ($p) {
            $status = $p->procurement?->status ?? '';
            $type   = strtoupper($p->procurement?->type ?? '');
            return in_array($status, ['APPROVED', 'COMPLETED']);
        });

        $realisasiQty = 0;
        if ($isDone) {
            $realisasiQty = (int) $completedProcs->sum('quantity');
            if ($realisasiQty === 0) $realisasiQty = $completedProcs->count();
        }

        // Struk URL dari procurement_payment_proofs
        $strukUrl = null;
        if ($isDone && $completedProc) {
            $proc = $completedProc->procurement;
            if ($co && $proc) {
                $proof = \App\Models\ProcurementPaymentProof::where('procurement_id', $proc->id)
                    ->where('company_id', $co->id)->first();
                $strukUrl = $proof?->payment_proof
                    ? \Storage::url($proof->payment_proof)
                    : ($proc->payment_proof ? \Storage::url($proc->payment_proof) : null);
            } elseif ($proc?->payment_proof) {
                $strukUrl = \Storage::url($proc->payment_proof);
            }
        }

        $strukList = $completedProcs->map(function ($p, $index) use ($co) {
            $proc = $p->procurement;
            $url = null; $amount = 0;
            if ($co && $proc) {
                $proof = \App\Models\ProcurementPaymentProof::where('procurement_id', $proc->id)
                    ->where('company_id', $co->id)->first();
                $url    = $proof?->payment_proof ? \Storage::url($proof->payment_proof) : ($proc->payment_proof ? \Storage::url($proc->payment_proof) : null);
                $amount = $proof ? ($proof->realisasi_amount ?? 0) : ($proc->realisasi_amount ?? 0);
            } else {
                $url    = $proc?->payment_proof ? \Storage::url($proc->payment_proof) : null;
                $amount = $proc?->realisasi_amount ?? 0;
            }
            return (object) [
                'no'          => $index + 1,
                'url'         => $url,
                'date'        => $proc?->payment_proof_uploaded_at,
                'amount'      => $amount,
                'proc_number' => $proc?->procurement_number ?? '-',
                'qty'         => (int) ($p->quantity > 0 ? $p->quantity : 1),
            ];
        })->values();

        // Label & warna company
        $companyName  = $co?->name ?? '—';
        $companyLabel = $co ? "🏢 {$companyName}" : '—';
        $companyColor = '#6b7280';
        if ($co) {
            $n = strtolower($co->name);
            if (str_contains($n, 'konnco'))      $companyColor = '#d97706';
            elseif (str_contains($n, 'kodemee')) $companyColor = '#16a34a';
            elseif (str_contains($n, 'mozaigg')) $companyColor = '#7c3aed';
            else                                  $companyColor = '#0369a1';
        }

        $hasProcurement = $linkedForCo->filter(function ($p) {
            return in_array($p->procurement?->status, ['APPROVED', 'COMPLETED']);
        })->isNotEmpty();

        $rows->push((object) [
            'id'                   => $masterItem->id,
            'item_name'            => $masterItem->item_name,
            'specification'        => $masterItem->specification,
            'unit'                 => $masterItem->unit,
            'estimated_price'      => $masterItem->estimated_price,
            'vendor'               => $masterItem->vendor,
            'category_name'        => $masterItem->category?->name ?? 'Tanpa Kategori',
            'category_sort_order'  => $masterItem->category?->sort_order ?? 999,
            'sort_order'           => $masterItem->sort_order,
            'total_qty'            => $totalQty,
            'est_qty'              => $estQty,
            'realisasi_qty'        => $realisasiQty,
            'show_est_qty'         => $showEstQty,
            'total_estimasi'       => $totalEst,
            'is_done'              => $isDone,
            'realisasi'            => $realisasiAmount,
            'selisih'              => $isDone ? ($realisasiAmount - $totalEst) : null,
            'struk_url'            => $strukUrl,
            'struk_date'           => $completedProc?->procurement?->payment_proof_uploaded_at,
            'struk_list'           => $strukList,
            'proc_status'          => $isDone ? 'COMPLETED' : $procStatus,
            'has_procurement'      => $hasProcurement,
            'company_id'           => $co?->id,
            'company_target'       => $co ? strtolower($co->name) : null,
            'count_in_grand_total' => true,
            'company_label'        => $companyLabel,
            'company_color'        => $companyColor,
        ]);
    }

    public function setFilterCompany(string $value): void
    {
        $this->filterCompany = $value;
    }

    public function resetFilter(): void
    {
        $this->filterMonth    = now()->format('m');
        $this->filterYear     = now()->format('Y');
        $this->filterDateFrom = '';
        $this->filterDateTo   = '';
        $this->filterCompany  = 'all';
    }

    public function openDownloadModal(string $type): void
    {
        $this->downloadType      = $type;
        $this->showDownloadModal = true;
    }

    public function closeDownloadModal(): void
    {
        $this->showDownloadModal = false;
        $this->downloadType      = '';
    }

    public function confirmDownload(): mixed
    {
        $this->showDownloadModal = false;
        $params = http_build_query([
            'type'     => $this->downloadType,
            'dateFrom' => $this->downloadDateFrom,
            'dateTo'   => $this->downloadDateTo,
            'company'  => $this->filterCompany,
        ]);
        return redirect(route('realisasi.download') . '?' . $params);
    }

    public function downloadExcel(): StreamedResponse
    {
        $tableData = $this->getTableDataWithFilters(
            $this->filterCompany, '', '',
            $this->downloadDateFrom, $this->downloadDateTo
        );
        $dateLabel = $this->downloadDateFrom && $this->downloadDateTo
            ? \Carbon\Carbon::parse($this->downloadDateFrom)->format('d-m-Y') . '_sd_' . \Carbon\Carbon::parse($this->downloadDateTo)->format('d-m-Y')
            : now()->format('d-m-Y');
        $fileName = 'realisasi-pengadaan_' . $dateLabel . '.xlsx';

        $grandEst = 0; $grandReal = 0; $totalItems = 0; $doneCount = 0;
        foreach ($tableData as $items) {
            foreach ($items as $item) {
                $grandEst += $item->total_estimasi; $grandReal += $item->realisasi; $totalItems++;
                if ($item->is_done) $doneCount++;
            }
        }
        $selisihG = $grandReal - $grandEst;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Realisasi Pengadaan');
        $sheet = $spreadsheet->getActiveSheet();

        $AMBER_DARK='FF92400E'; $AMBER_LIGHT='FFFEF3C7'; $AMBER_BDR='FFFDE68A';
        $GREEN_BG='FFF0FDF4'; $GREEN_TEXT='FF166534'; $RED_TEXT='FFDC2626';
        $WHITE='FFFFFFFF'; $GRAY_BG='FFF9FAFB'; $FOOTER_BG='FF78350F';

        $sheet->mergeCells('A1:M1'); $sheet->setCellValue('A1','LAPORAN REALISASI PENGADAAN');
        $sheet->getStyle('A1')->applyFromArray(['font'=>['bold'=>true,'size'=>14,'color'=>['argb'=>$WHITE],'name'=>'Arial'],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:M2');
        $periodeLabel = ($this->downloadDateFrom && $this->downloadDateTo)
            ? 'Periode: ' . \Carbon\Carbon::parse($this->downloadDateFrom)->translatedFormat('d F Y') . ' s/d ' . \Carbon\Carbon::parse($this->downloadDateTo)->translatedFormat('d F Y')
            : 'Semua Periode';
        $sheet->setCellValue('A2', $periodeLabel . '   |   Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray(['font'=>['size'=>9,'color'=>['argb'=>'FFD97706'],'name'=>'Arial'],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>'FFFFF8E1']],'alignment'=>['horizontal'=>'center']]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $sheet->setCellValue('A3','Total Estimasi'); $sheet->setCellValue('B3',$grandEst);
        $sheet->setCellValue('D3','Total Realisasi'); $sheet->setCellValue('E3',$grandReal);
        $sheet->setCellValue('G3','Item Terealisasi'); $sheet->setCellValue('H3',$doneCount.' / '.$totalItems.' item');
        $sheet->setCellValue('J3','Selisih'); $sheet->setCellValue('K3',$selisihG);
        foreach(['A3','D3','G3','J3'] as $c) $sheet->getStyle($c)->applyFromArray(['font'=>['bold'=>true,'size'=>8,'color'=>['argb'=>'FFA8A29E'],'name'=>'Arial']]);
        $sheet->getStyle('B3')->applyFromArray(['font'=>['bold'=>true,'size'=>10,'name'=>'Arial'],'number'=>['format'=>'#,##0']]);
        $sheet->getStyle('E3')->applyFromArray(['font'=>['bold'=>true,'size'=>10,'name'=>'Arial'],'number'=>['format'=>'#,##0']]);
        $sheet->getStyle('K3')->applyFromArray(['font'=>['bold'=>true,'size'=>10,'name'=>'Arial','color'=>['argb'=>$selisihG>0?$RED_TEXT:($selisihG<0?'FF16A34A':'FFA8A29E')]],'number'=>['format'=>'#,##0']]);
        $sheet->getStyle('A3:M3')->applyFromArray(['fill'=>['fillType'=>'solid','startColor'=>['argb'=>'FFFFFBEB']]]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        $headers=['No','Kategori','Nama Item','Spesifikasi','Vendor','Unit','Est. QTY','Real. QTY','Est. Harga/Satuan (Rp)','Total Estimasi (Rp)','Realisasi (Rp)','Selisih (Rp)','Status'];
        foreach($headers as $i=>$h) $sheet->setCellValue(chr(65+$i).'4',$h);
        $sheet->getStyle('A4:M4')->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['argb'=>$WHITE],'name'=>'Arial'],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'center','vertical'=>'center','wrapText'=>true],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['argb'=>'FFB45309']]]]);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row=5; $no=0;
        foreach($tableData as $categoryName=>$items) {
            $cEst=$items->sum('total_estimasi'); $cReal=$items->sum('realisasi');
            $cDone=$items->where('is_done',true)->count(); $cAll=$items->count();
            $cPct=$cAll>0?round($cDone/$cAll*100):0; $cSel=$cReal-$cEst;
            $sheet->mergeCells("A{$row}:H{$row}"); $sheet->setCellValue("A{$row}","◆  {$categoryName}  ({$cAll} item)");
            $sheet->setCellValue("I{$row}",$cEst); $sheet->setCellValue("J{$row}",$cReal>0?$cReal:null); $sheet->setCellValue("K{$row}",$cReal>0?$cSel:null); $sheet->setCellValue("M{$row}",$cPct.'%  ('.$cDone.'/'.$cAll.')');
            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['argb'=>$AMBER_DARK],'name'=>'Arial'],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_LIGHT]],'borders'=>['top'=>['borderStyle'=>'medium','color'=>['argb'=>$AMBER_BDR]],'bottom'=>['borderStyle'=>'thin','color'=>['argb'=>$AMBER_BDR]]]]);
            $sheet->getStyle("I{$row}:K{$row}")->getNumberFormat()->setFormatCode('#,##0'); $sheet->getStyle("I{$row}:K{$row}")->getAlignment()->setHorizontal('right');
            if($cReal>0&&$cSel>0) $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB($RED_TEXT);
            elseif($cReal>0&&$cSel<0) $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB('FF16A34A');
            $sheet->getRowDimension($row)->setRowHeight(20); $row++;
            foreach($items as $item) {
                $no++;
                $statusLabel=$item->is_done?'Sudah Terealisasi':($item->has_procurement?'Belum Terealisasi':'Belum Diajukan');
                $sheet->setCellValue("A{$row}",$no); $sheet->setCellValue("B{$row}",$item->category_name);
                $sheet->setCellValue("C{$row}",$item->item_name); $sheet->setCellValue("D{$row}",$item->specification??'');
                $sheet->setCellValue("E{$row}",$item->vendor??''); $sheet->setCellValue("F{$row}",$item->unit??'');
                $sheet->setCellValue("G{$row}",$item->total_qty>0?$item->total_qty:1);
                $sheet->setCellValue("H{$row}",$item->is_done&&$item->realisasi_qty>0?$item->realisasi_qty:null);
                $sheet->setCellValue("I{$row}",$item->estimated_price); $sheet->setCellValue("J{$row}",$item->total_estimasi);
                $sheet->setCellValue("K{$row}",$item->is_done?$item->realisasi:null); $sheet->setCellValue("L{$row}",$item->selisih); $sheet->setCellValue("M{$row}",$statusLabel);
                $fill=$item->is_done?$GREEN_BG:($item->has_procurement?'FFFFFBEB':$GRAY_BG);
                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray(['font'=>['size'=>9,'name'=>'Arial'],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$fill]],'borders'=>['bottom'=>['borderStyle'=>'thin','color'=>['argb'=>'FFF5F5F4']]]]);
                $sheet->getStyle("I{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0'); $sheet->getStyle("I{$row}:L{$row}")->getAlignment()->setHorizontal('right');
                foreach(['A','F','G','H','M'] as $c) $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal('center');
                if($item->is_done&&$item->realisasi_qty>0) $sheet->getStyle("H{$row}")->getFont()->getColor()->setARGB($GREEN_TEXT)->setBold(true);
                if($item->is_done) { $sheet->getStyle("M{$row}")->applyFromArray(['font'=>['bold'=>true,'color'=>['argb'=>$GREEN_TEXT]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>'FFDCFCE7']]]); $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB($GREEN_TEXT)->setBold(true); }
                elseif($item->has_procurement) $sheet->getStyle("M{$row}")->applyFromArray(['font'=>['bold'=>true,'color'=>['argb'=>'FFB45309']],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>'FFFEF3C7']]]);
                else $sheet->getStyle("M{$row}")->getFont()->getColor()->setARGB('FFA8A29E');
                if($item->selisih!==null) { if($item->selisih>0) $sheet->getStyle("L{$row}")->getFont()->getColor()->setARGB($RED_TEXT)->setBold(true); elseif($item->selisih<0) $sheet->getStyle("L{$row}")->getFont()->getColor()->setARGB('FF16A34A')->setBold(true); }
                $sheet->getRowDimension($row)->setRowHeight(18); $row++;
            }
        }
        $sheet->mergeCells("A{$row}:I{$row}"); $sheet->setCellValue("A{$row}",'GRAND TOTAL  ('.$totalItems.' item)');
        $sheet->setCellValue("J{$row}",$grandEst); $sheet->setCellValue("K{$row}",$grandReal); $sheet->setCellValue("L{$row}",$selisihG);
        $sheet->setCellValue("M{$row}",($grandEst>0?round($grandReal/$grandEst*100,2):0).'%');
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['argb'=>$WHITE],'name'=>'Arial'],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$FOOTER_BG]],'alignment'=>['vertical'=>'center'],'borders'=>['top'=>['borderStyle'=>'medium','color'=>['argb'=>'FFB45309']]]]);
        $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0'); $sheet->getStyle("J{$row}:L{$row}")->getAlignment()->setHorizontal('right');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('right'); $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal('center');
        if($selisihG>0) $sheet->getStyle("L{$row}")->getFont()->getColor()->setARGB('FFFCA5A5');
        elseif($selisihG<0) $sheet->getStyle("L{$row}")->getFont()->getColor()->setARGB('FF6EE7B7');
        $sheet->getRowDimension($row)->setRowHeight(24);
        foreach(['A'=>6,'B'=>18,'C'=>28,'D'=>22,'E'=>18,'F'=>7,'G'=>8,'H'=>10,'I'=>20,'J'=>20,'K'=>20,'L'=>18,'M'=>22] as $col=>$w) $sheet->getColumnDimension($col)->setWidth($w);
        $sheet->freezePane('A5');

        $writer=new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function() use($writer){ $writer->save('php://output'); }, $fileName, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','Cache-Control'=>'max-age=0','Content-Disposition'=>'attachment; filename="'.$fileName.'"']);
    }

    public function downloadExcelRealisasi(): StreamedResponse
    {
        $tableData = $this->getTableDataWithFilters($this->filterCompany,'','',$this->downloadDateFrom,$this->downloadDateTo);
        $dateLabel = $this->downloadDateFrom&&$this->downloadDateTo ? \Carbon\Carbon::parse($this->downloadDateFrom)->format('d-m-Y').'_sd_'.\Carbon\Carbon::parse($this->downloadDateTo)->format('d-m-Y') : now()->format('d-m-Y');
        $fileName = 'realisasi-terealisasi_'.$dateLabel.'.xlsx';

        $dataRealisasi=collect();
        foreach($tableData as $categoryName=>$items) {
            foreach($items as $item) {
                if(!$item->is_done) continue;
                if($item->struk_list&&$item->struk_list->isNotEmpty()) {
                    foreach($item->struk_list as $struk) {
                        $dataRealisasi->push((object)['tanggal'=>$struk->date?\Carbon\Carbon::parse($struk->date):($item->struk_date?\Carbon\Carbon::parse($item->struk_date):now()),'keterangan'=>$item->item_name.($item->specification?' — '.$item->specification:'').($item->vendor?' ['.$item->vendor.']':''),'debit'=>$struk->amount>0?$struk->amount:$item->realisasi,'kategori'=>$item->category_name,'nota'=>$struk->proc_number??null,'perusahaan'=>$item->company_label]);
                    }
                } else {
                    $dataRealisasi->push((object)['tanggal'=>$item->struk_date?\Carbon\Carbon::parse($item->struk_date):now(),'keterangan'=>$item->item_name.($item->specification?' — '.$item->specification:'').($item->vendor?' ['.$item->vendor.']':''),'debit'=>$item->realisasi,'kategori'=>$item->category_name,'nota'=>null,'perusahaan'=>$item->company_label]);
                }
            }
        }
        $dataRealisasi=$dataRealisasi->sortBy('tanggal')->values();

        $AMBER_DARK='FF92400E'; $AMBER_MED='FFB45309'; $AMBER_500='FFF59E0B'; $AMBER_400='FFFBBF24';
        $AMBER_100='FFFEF3C7'; $AMBER_50='FFFFFBEB'; $WHITE='FFFFFFFF'; $INK='FF1C1917';
        $INK2='FF44403C'; $INK4='FFA8A29E'; $BLUE_LINK='FF1D4ED8';

        $kategoriWarna=['Beban Air/PDAM'=>['FFDBEAFE','FF1D4ED8'],'Beban Adm Lainnya'=>['FFFEE2E2','FFDC2626'],'Beban Rumah Tangga'=>['FFFCE7F3','FF9D174D'],'Beban Konsumsi'=>['FFFEF3C7','FFB45309'],'Beban Adm Antar bank'=>['FFE0E7FF','FF4338CA'],'Beban Operasional Lainnya'=>['FFF3E8FF','FF7E22CE'],'Beban Listrik'=>['FFD1FAE5','FF065F46'],'Beban Perbaikan & Pemeliharaan'=>['FFFEF08A','FF854D0E'],'Beban Alat Tulis Kantor'=>['FFFFEDD5','FFC2410C'],'Peralatan Kantor'=>['FFE2E8F0','FF334155'],'Beban Peralatan & Perlengkapan'=>['FFECFDF5','FF065F46']];
        $warnaFn=function(string $nama) use($kategoriWarna):array{ foreach($kategoriWarna as $key=>[$bg,$fg]){ if(stripos($nama,$key)!==false||stripos($key,$nama)!==false) return[$bg,$fg]; } foreach($kategoriWarna as $key=>[$bg,$fg]){ foreach(explode(' ',strtolower($key)) as $word){ if(strlen($word)>4&&str_contains(strtolower($nama),$word)) return[$bg,$fg]; } } return['FFF3F4F6','FF374151']; };
        $borderFn=function(string $color='FFE2E8F0'):array{ return['borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['argb'=>$color]]]]; };

        $spreadsheet=new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Sistem Pengadaan')->setTitle('Realisasi Terealisasi');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);
        $ws=$spreadsheet->getActiveSheet(); $ws->setTitle('Realisasi Terealisasi');
        $ws->getColumnDimension('A')->setWidth(14); $ws->getColumnDimension('B')->setWidth(52); $ws->getColumnDimension('C')->setWidth(22); $ws->getColumnDimension('D')->setWidth(34); $ws->getColumnDimension('E')->setWidth(18);
        $ws->mergeCells('A1:E1'); $ws->setCellValue('A1','LAPORAN REALISASI PENGADAAN — SUDAH TEREALISASI');
        $ws->getRowDimension(1)->setRowHeight(38);
        $ws->getStyle('A1')->applyFromArray(['font'=>['bold'=>true,'size'=>13,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);

        $periodeLabel=($this->downloadDateFrom&&$this->downloadDateTo) ? \Carbon\Carbon::parse($this->downloadDateFrom)->translatedFormat('d F Y').' s/d '.\Carbon\Carbon::parse($this->downloadDateTo)->translatedFormat('d F Y') : 'Semua Periode';
        $perusahaanLabel = $this->filterCompany==='all' ? 'Semua Perusahaan' : (Company::find((int)$this->filterCompany)?->name ?? 'Semua Perusahaan');

        $ws->mergeCells('A2:E2'); $ws->setCellValue('A2','Perusahaan: '.$perusahaanLabel.'   |   Periode: '.$periodeLabel.'   |   Dicetak: '.now()->translatedFormat('d F Y, H:i').' WIB');
        $ws->getRowDimension(2)->setRowHeight(22); $ws->getStyle('A2')->applyFromArray(['font'=>['italic'=>true,'size'=>9,'color'=>['argb'=>$AMBER_MED]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_100]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);
        $ws->mergeCells('A3:E3'); $ws->getRowDimension(3)->setRowHeight(6); $ws->getStyle('A3:E3')->getFill()->setFillType('solid')->getStartColor()->setARGB($AMBER_50);

        $headers=['A4'=>'Tanggal','B4'=>'Keterangan / Nama Item','C4'=>'Debit (Rp)','D4'=>'Kategori','E4'=>'Nota'];
        foreach($headers as $cell=>$label){ $ws->setCellValue($cell,$label); $ws->getStyle($cell)->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_500]],'alignment'=>['horizontal'=>'center','vertical'=>'center','wrapText'=>true],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['argb'=>$AMBER_400]]]]); }
        $ws->getRowDimension(4)->setRowHeight(26);

        $rowStart=5; $grandTotal=0;
        foreach($dataRealisasi as $i=>$item) {
            $r=$rowStart+$i; $bgRow=($i%2===0)?$AMBER_50:$WHITE;
            $tanggal=$item->tanggal instanceof \Carbon\Carbon?$item->tanggal->format('d/m/Y'):date('d/m/Y',strtotime($item->tanggal));
            $ws->setCellValue("A{$r}",$tanggal); $ws->getStyle("A{$r}")->applyFromArray(['font'=>['size'=>9,'color'=>['argb'=>$INK2]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgRow]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]+$borderFn());
            $ws->setCellValue("B{$r}",$item->keterangan??''); $ws->getStyle("B{$r}")->applyFromArray(['font'=>['size'=>9,'color'=>['argb'=>$INK]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgRow]],'alignment'=>['horizontal'=>'left','vertical'=>'center','indent'=>1,'wrapText'=>true]]+$borderFn());
            $debit=(float)($item->debit??0); $grandTotal+=$debit;
            $ws->setCellValue("C{$r}",$debit); $ws->getStyle("C{$r}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['argb'=>$INK2]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgRow]],'alignment'=>['horizontal'=>'right','vertical'=>'center']]+$borderFn()); $ws->getStyle("C{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $kategori=$item->kategori??'—'; [$bgKat,$fgKat]=$warnaFn($kategori);
            $ws->setCellValue("D{$r}",$kategori); $ws->getStyle("D{$r}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['argb'=>$fgKat]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgKat]],'alignment'=>['horizontal'=>'center','vertical'=>'center','wrapText'=>true],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['argb'=>$bgKat]]]]);
            $nota=$item->nota??''; $ws->setCellValue("E{$r}",$nota?:'—');
            $notaStyle=['fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgRow]],'alignment'=>['horizontal'=>'center','vertical'=>'center']];
            $notaStyle['font']=$nota?['size'=>9,'color'=>['argb'=>$BLUE_LINK],'underline'=>true]:['size'=>9,'color'=>['argb'=>$INK4]];
            $ws->getStyle("E{$r}")->applyFromArray($notaStyle+$borderFn()); $ws->getRowDimension($r)->setRowHeight(22);
        }
        $rTotal=$rowStart+$dataRealisasi->count();
        $ws->mergeCells("A{$rTotal}:B{$rTotal}"); $ws->setCellValue("A{$rTotal}",'GRAND TOTAL  ('.$dataRealisasi->count().' transaksi terealisasi)');
        $ws->getStyle("A{$rTotal}")->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'right','vertical'=>'center']]+$borderFn($AMBER_500));
        $ws->setCellValue("C{$rTotal}","=SUM(C{$rowStart}:C".($rTotal-1).')'); $ws->getStyle("C{$rTotal}")->applyFromArray(['font'=>['bold'=>true,'size'=>11,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'right','vertical'=>'center']]+$borderFn($AMBER_500)); $ws->getStyle("C{$rTotal}")->getNumberFormat()->setFormatCode('#,##0');
        foreach(['D','E'] as $col) $ws->getStyle("{$col}{$rTotal}")->applyFromArray(['fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]]]+$borderFn($AMBER_500));
        $ws->getRowDimension($rTotal)->setRowHeight(28); $ws->setAutoFilter("A4:E".($rTotal-1)); $ws->freezePane('A5');

        $ws2=$spreadsheet->createSheet(); $ws2->setTitle('Ringkasan Kategori');
        $ws2->getColumnDimension('A')->setWidth(36); $ws2->getColumnDimension('B')->setWidth(16); $ws2->getColumnDimension('C')->setWidth(24);
        $ws2->mergeCells('A1:C1'); $ws2->setCellValue('A1','RINGKASAN REALISASI PER KATEGORI'); $ws2->getRowDimension(1)->setRowHeight(34);
        $ws2->getStyle('A1')->applyFromArray(['font'=>['bold'=>true,'size'=>13,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);
        $ws2->mergeCells('A2:C2'); $ws2->setCellValue('A2','Periode: '.$periodeLabel.'   |   Perusahaan: '.$perusahaanLabel); $ws2->getRowDimension(2)->setRowHeight(20);
        $ws2->getStyle('A2')->applyFromArray(['font'=>['italic'=>true,'size'=>9,'color'=>['argb'=>$AMBER_MED]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_100]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);
        $hdrs2=['A3'=>'Kategori','B3'=>'Jml Transaksi','C3'=>'Total Realisasi (Rp)'];
        foreach($hdrs2 as $cell=>$label){ $ws2->setCellValue($cell,$label); $ws2->getStyle($cell)->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_500]],'alignment'=>['horizontal'=>'center','vertical'=>'center'],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['argb'=>$AMBER_400]]]]); }
        $ws2->getRowDimension(3)->setRowHeight(24);
        $ringkasan=[];
        foreach($dataRealisasi as $item){ $kat=$item->kategori??'Lainnya'; $ringkasan[$kat]['count']=($ringkasan[$kat]['count']??0)+1; $ringkasan[$kat]['total']=($ringkasan[$kat]['total']??0)+(float)$item->debit; }
        uasort($ringkasan,fn($a,$b)=>$b['total']<=>$a['total']);
        $ri=0;
        foreach($ringkasan as $kat=>$val){ $r2=4+$ri; $bgRow2=($ri%2===0)?$AMBER_50:$WHITE; [$bgKat2,$fgKat2]=$warnaFn($kat);
            $ws2->setCellValue("A{$r2}",$kat); $ws2->getStyle("A{$r2}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['argb'=>$fgKat2]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgKat2]],'alignment'=>['horizontal'=>'left','vertical'=>'center','indent'=>1]]+$borderFn());
            $ws2->setCellValue("B{$r2}",$val['count']); $ws2->getStyle("B{$r2}")->applyFromArray(['font'=>['size'=>9,'color'=>['argb'=>$INK2]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgRow2]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]+$borderFn());
            $ws2->setCellValue("C{$r2}",$val['total']); $ws2->getStyle("C{$r2}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['argb'=>$INK2]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$bgRow2]],'alignment'=>['horizontal'=>'right','vertical'=>'center']]+$borderFn()); $ws2->getStyle("C{$r2}")->getNumberFormat()->setFormatCode('#,##0');
            $ws2->getRowDimension($r2)->setRowHeight(20); $ri++; }
        $rTot2=4+$ri; $ws2->mergeCells("A{$rTot2}:B{$rTot2}"); $ws2->setCellValue("A{$rTot2}",'TOTAL KESELURUHAN');
        $ws2->getStyle("A{$rTot2}")->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'right','vertical'=>'center']]+$borderFn($AMBER_500));
        $ws2->setCellValue("C{$rTot2}","=SUM(C4:C".($rTot2-1).')'); $ws2->getStyle("C{$rTot2}")->applyFromArray(['font'=>['bold'=>true,'size'=>11,'color'=>['argb'=>$WHITE]],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>$AMBER_DARK]],'alignment'=>['horizontal'=>'right','vertical'=>'center']]+$borderFn($AMBER_500)); $ws2->getStyle("C{$rTot2}")->getNumberFormat()->setFormatCode('#,##0');
        $ws2->getRowDimension($rTot2)->setRowHeight(26); $ws2->freezePane('A4'); $spreadsheet->setActiveSheetIndex(0);

        $writer=new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function() use($writer){ $writer->save('php://output'); }, $fileName, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','Cache-Control'=>'max-age=0','Content-Disposition'=>'attachment; filename="'.$fileName.'"']);
    }

    public function downloadPdf(): StreamedResponse
    {
        $tableData=$this->getTableDataWithFilters($this->filterCompany,'','',$this->downloadDateFrom,$this->downloadDateTo);
        $dateLabel=($this->downloadDateFrom&&$this->downloadDateTo) ? \Carbon\Carbon::parse($this->downloadDateFrom)->format('d M Y').' s/d '.\Carbon\Carbon::parse($this->downloadDateTo)->format('d M Y') : 'Semua Periode';
        $html=view('pdf.realisasi-pengadaan-pdf',['tableData'=>$tableData,'dateLabel'=>$dateLabel])->render();
        $pdf=\Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4','landscape');
        $fileName='realisasi-pengadaan_'.now()->format('d-m-Y').'.pdf';
        return response()->streamDownload(fn()=>print($pdf->output()),$fileName,['Content-Type'=>'application/pdf']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('← Kembali ke Daftar')->color('gray')->url(ExpenseMasterResource::getUrl('index')),
        ];
    }

    public function getTitle(): string { return 'Realisasi Pengadaan'; }

    public function getBreadcrumbs(): array
    {
        return [ExpenseMasterResource::getUrl('index') => 'Master Data Beban', '#' => 'Realisasi Pengadaan'];
    }
}