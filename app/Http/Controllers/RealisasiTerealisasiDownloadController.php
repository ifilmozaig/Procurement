<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ExpenseMasterItem;
use App\Models\ProcurementItem;
use App\Models\ProcurementPaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RealisasiTerealisasiDownloadController
{
    public function download(Request $request): StreamedResponse
    {
        $dateFrom = $request->get('dateFrom', '');
        $dateTo   = $request->get('dateTo', '');
        $company  = $request->get('company', 'all'); // 'all' atau ID integer as string

        $tableData = $this->getTableData($company, $dateFrom, $dateTo);

        return $this->streamExcel($tableData, $dateFrom, $dateTo, $company);
    }

    // =========================================================================
    // GET TABLE DATA — IDENTIK dengan RealisasiPengadaan Livewire page
    // Hanya ambil item yang is_done = true (ada payment proof)
    // =========================================================================
    protected function getTableData(string $company, string $dateFrom, string $dateTo): Collection
    {
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
                $q->whereIn('status', ['APPROVED', 'COMPLETED', 'PROCESSING'])
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
        }

        $procItems = $procQuery->get()->groupBy('expense_master_item_id');

        // Kumpulkan semua company dari KEDUA sumber
        if ($company === 'all') {
            $legacyIds = $procItems->flatten()->pluck('company_id')->filter();
            $pivotIds  = DB::table('procurement_item_companies')
                ->whereIn('procurement_item_id', $procItems->flatten()->pluck('id'))
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
                    // Tidak ada procurement → skip (laporan terealisasi hanya butuh yang done)
                    continue;
                }
                foreach ($companies as $co) {
                    $linkedForCo = $linked->filter(
                        fn ($p) => $this->itemBelongsToCompany($p, $co->id)
                    );
                    if ($linkedForCo->isEmpty()) continue;
                    $this->pushRow($rows, $masterItem, $linkedForCo, $co);
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
            ->sortBy(fn ($item) => [$item->category_sort_order, $item->sort_order])
            ->groupBy('category_name');
    }

    // =========================================================================
    // HELPER: apakah ProcurementItem milik company tertentu (pivot + legacy)
    // =========================================================================
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

    // =========================================================================
    // HELPER: build 1 baris data — IDENTIK dengan Livewire page
    // Hanya push jika is_done = true
    // =========================================================================
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

        // Completed: status COMPLETED + ada payment proof untuk company ini
        $completedProcs = $linkedForCo->filter(function ($p) use ($co) {
            $proc = $p->procurement;
            if (!$proc || $proc->status !== 'COMPLETED') return false;
            if ($co) {
                return ProcurementPaymentProof::where('procurement_id', $proc->id)
                    ->where('company_id', $co->id)->exists()
                    || $proc->payment_proof !== null;
            }
            return $proc->payment_proof !== null;
        });

        // Hanya item yang is_done = true
        if ($completedProcs->isEmpty()) return;

        $completedProc = $completedProcs->sortByDesc(
            fn ($p) => $p->procurement?->payment_proof_uploaded_at
        )->first();

        $totalEst        = $masterItem->estimated_price * max($totalQty, 1);
        $realisasiAmount = 0;

        foreach ($completedProcs as $p) {
            $proc = $p->procurement;
            if (!$proc) continue;
            if ($co) {
                $proof = ProcurementPaymentProof::where('procurement_id', $proc->id)
                    ->where('company_id', $co->id)->first();
                $realisasiAmount += $proof
                    ? ($proof->realisasi_amount ?? 0)
                    : ($proc->realisasi_amount ?? 0);
            } else {
                $realisasiAmount += $proc->realisasi_amount ?? 0;
            }
        }
        if ($realisasiAmount === 0) $realisasiAmount = $totalEst;

        // Struk list dari ProcurementPaymentProof
        $strukList = $completedProcs->map(function ($p, $index) use ($co) {
            $proc = $p->procurement;
            $url = null; $amount = 0;
            if ($co && $proc) {
                $proof  = ProcurementPaymentProof::where('procurement_id', $proc->id)
                    ->where('company_id', $co->id)->first();
                $url    = $proof?->payment_proof
                    ? Storage::url($proof->payment_proof)
                    : ($proc->payment_proof ? Storage::url($proc->payment_proof) : null);
                $amount = $proof ? ($proof->realisasi_amount ?? 0) : ($proc->realisasi_amount ?? 0);
            } else {
                $url    = $proc?->payment_proof ? Storage::url($proc->payment_proof) : null;
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

        $rows->push((object) [
            'item_name'           => $masterItem->item_name,
            'specification'       => $masterItem->specification,
            'vendor'              => $masterItem->vendor,
            'category_name'       => $masterItem->category?->name ?? 'Tanpa Kategori',
            'category_sort_order' => $masterItem->category?->sort_order ?? 999,
            'sort_order'          => $masterItem->sort_order,
            'realisasi'           => $realisasiAmount,
            'total_estimasi'      => $totalEst,
            'selisih'             => $realisasiAmount - $totalEst,
            'struk_date'          => $completedProc?->procurement?->payment_proof_uploaded_at,
            'struk_list'          => $strukList,
            'company_id'          => $co?->id,
            'company_label'       => $companyLabel,
            'company_color'       => $companyColor,
        ]);
    }

    // =========================================================================
    // STREAM EXCEL
    // =========================================================================
    protected function streamExcel(
        Collection $tableData,
        string $dateFrom,
        string $dateTo,
        string $company
    ): StreamedResponse {
        // Ekspansi ke baris flat per struk
        $rows = collect();
        foreach ($tableData as $categoryName => $items) {
            foreach ($items as $item) {
                if ($item->struk_list && $item->struk_list->isNotEmpty()) {
                    foreach ($item->struk_list as $struk) {
                        $rows->push((object) [
                            'tanggal'    => $struk->date
                                ? \Carbon\Carbon::parse($struk->date)
                                : ($item->struk_date ? \Carbon\Carbon::parse($item->struk_date) : now()),
                            'keterangan' => $item->item_name
                                . ($item->specification ? "\n" . $item->specification : '')
                                . ($item->vendor        ? "\n🏪 " . $item->vendor      : ''),
                            'debit'      => $struk->amount > 0
                                ? $struk->amount
                                : round($item->realisasi / max($item->struk_list->count(), 1)),
                            'kategori'   => $item->category_name,
                            'nota'       => $struk->proc_number ?? null,
                            'perusahaan' => $item->company_label,
                            'co_color'   => $item->company_color,
                        ]);
                    }
                } else {
                    $rows->push((object) [
                        'tanggal'    => $item->struk_date
                            ? \Carbon\Carbon::parse($item->struk_date)
                            : now(),
                        'keterangan' => $item->item_name
                            . ($item->specification ? "\n" . $item->specification : '')
                            . ($item->vendor        ? "\n🏪 " . $item->vendor      : ''),
                        'debit'      => $item->realisasi,
                        'kategori'   => $item->category_name,
                        'nota'       => null,
                        'perusahaan' => $item->company_label,
                        'co_color'   => $item->company_color,
                    ]);
                }
            }
        }

        $rows        = $rows->sortBy('tanggal')->values();
        $grandTotal  = $rows->sum('debit');
        $jumlahBaris = $rows->count();

        $companyModel = ($company !== 'all') ? Company::find((int) $company) : null;
        $companyLabel = $companyModel ? ('🏢 ' . $companyModel->name) : 'Semua Perusahaan';

        $periodeLabel = ($dateFrom && $dateTo)
            ? \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y')
              . ' s/d '
              . \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y')
            : 'Semua Periode';

        $dateLabel = ($dateFrom && $dateTo)
            ? \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') . '_sd_' . \Carbon\Carbon::parse($dateTo)->format('d-m-Y')
            : now()->format('d-m-Y');

        $fileName = 'realisasi-terealisasi_' . $dateLabel . '.xlsx';

        // ── Warna ─────────────────────────────────────────────────────────────
        $AMBER_MAIN  = 'FFD97706';
        $AMBER_BDR   = 'FFFDE68A';
        $AMBER_500   = 'FFF59E0B';
        $AMBER_50    = 'FFFFFBEB';
        $AMBER_INFO  = 'FFFFF8E1';
        $GREEN_BG    = 'FFF0FDF4';
        $GREEN_TEXT  = 'FF166534';
        $WHITE       = 'FFFFFFFF';
        $INK         = 'FF1C1917';
        $INK2        = 'FF44403C';
        $INK4        = 'FFA8A29E';
        $BLUE_LINK   = 'FF1D4ED8';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Sistem Pengadaan')->setTitle('Realisasi Terealisasi');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);

        // ── SHEET 1: Detail ───────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Realisasi Terealisasi');

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(48);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(32);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Header
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN REALISASI PENGADAAN — SUDAH TEREALISASI');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2',
            'Perusahaan: ' . $companyLabel
            . '   |   Periode: ' . $periodeLabel
            . '   |   Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB'
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['argb' => $AMBER_MAIN], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_INFO]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3',
            '✅ Total Transaksi Terealisasi:  ' . $jumlahBaris . ' transaksi'
            . '          💰 Total Realisasi:  Rp ' . number_format($grandTotal, 0, ',', '.')
        );
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => $AMBER_MAIN], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_INFO]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $AMBER_BDR]]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(24);

        $sheet->mergeCells('A4:F4');
        $sheet->getRowDimension(4)->setRowHeight(5);
        $sheet->getStyle('A4:F4')->getFill()->setFillType('solid')->getStartColor()->setARGB($AMBER_INFO);

        // Header kolom
        $headers = [
            'A' => 'Tanggal',
            'B' => 'Keterangan / Nama Item',
            'C' => 'Debit (Rp)',
            'D' => 'Kategori',
            'E' => 'Nota',
            'F' => 'Perusahaan',
        ];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}5", $label);
            $sheet->getStyle("{$col}5")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $AMBER_500]]],
            ]);
        }
        $sheet->getRowDimension(5)->setRowHeight(28);
        $sheet->freezePane('A6');

        // Data rows
        $rowStart = 6;
        foreach ($rows as $i => $item) {
            $r = $rowStart + $i;

            $tanggal = $item->tanggal instanceof \Carbon\Carbon
                ? $item->tanggal->format('d/m/Y')
                : date('d/m/Y', strtotime((string) $item->tanggal));

            $baseStyle = [
                'fill'    => ['fillType' => 'solid', 'startColor' => ['argb' => $GREEN_BG]],
                'borders' => ['bottom' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFF5F5F4']]],
            ];

            $sheet->setCellValue("A{$r}", $tanggal);
            $sheet->getStyle("A{$r}")->applyFromArray(array_merge($baseStyle, [
                'font'      => ['size' => 9, 'color' => ['argb' => $INK2], 'name' => 'Arial'],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]));

            $sheet->setCellValue("B{$r}", $item->keterangan ?? '');
            $sheet->getStyle("B{$r}")->applyFromArray(array_merge($baseStyle, [
                'font'      => ['size' => 9, 'color' => ['argb' => $INK], 'name' => 'Arial'],
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 1, 'wrapText' => true],
            ]));

            $sheet->setCellValue("C{$r}", (float) ($item->debit ?? 0));
            $sheet->getStyle("C{$r}")->applyFromArray(array_merge($baseStyle, [
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => $GREEN_TEXT], 'name' => 'Arial'],
                'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
            ]));
            $sheet->getStyle("C{$r}")->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue("D{$r}", $item->kategori ?? '—');
            $sheet->getStyle("D{$r}")->applyFromArray(array_merge($baseStyle, [
                'font'      => ['size' => 9, 'color' => ['argb' => $INK], 'name' => 'Arial'],
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 1, 'wrapText' => true],
            ]));

            $nota = $item->nota ?? '';
            $sheet->setCellValue("E{$r}", $nota ?: '—');
            $sheet->getStyle("E{$r}")->applyFromArray(array_merge($baseStyle, [
                'font'      => $nota
                    ? ['size' => 9, 'bold' => true, 'color' => ['argb' => $BLUE_LINK], 'underline' => true, 'name' => 'Arial']
                    : ['size' => 9, 'color' => ['argb' => $INK4], 'name' => 'Arial'],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]));

            // Perusahaan — gunakan company_color dari data
            $perusahaan = $item->perusahaan ?? '—';
            $coHex      = ltrim(str_replace('#', '', $item->co_color ?? '#a8a29e'), '#');
            $coArgb     = 'FF' . strtoupper(strlen($coHex) === 6 ? $coHex : 'a8a29e');
            $sheet->setCellValue("F{$r}", $perusahaan);
            $sheet->getStyle("F{$r}")->applyFromArray(array_merge($baseStyle, [
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => $coArgb], 'name' => 'Arial'],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]));

            $hasDetail = str_contains($item->keterangan ?? '', "\n");
            $sheet->getRowDimension($r)->setRowHeight($hasDetail ? 32 : 18);
        }

        // Grand total row
        $rTotal = $rowStart + $rows->count();
        $sheet->mergeCells("A{$rTotal}:B{$rTotal}");
        $sheet->setCellValue("A{$rTotal}", 'GRAND TOTAL  (' . $jumlahBaris . ' transaksi terealisasi)');
        $sheet->getStyle("A{$rTotal}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['top' => ['borderStyle' => 'medium', 'color' => ['argb' => $AMBER_500]]],
        ]);
        $sheet->setCellValue("C{$rTotal}", "=SUM(C{$rowStart}:C" . ($rTotal - 1) . ')');
        $sheet->getStyle("C{$rTotal}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
            'borders'   => ['top' => ['borderStyle' => 'medium', 'color' => ['argb' => $AMBER_500]]],
        ]);
        $sheet->getStyle("C{$rTotal}")->getNumberFormat()->setFormatCode('#,##0');
        foreach (['D', 'E', 'F'] as $col) {
            $sheet->getStyle("{$col}{$rTotal}")->applyFromArray([
                'fill'    => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
                'borders' => ['top' => ['borderStyle' => 'medium', 'color' => ['argb' => $AMBER_500]]],
            ]);
        }
        $sheet->getRowDimension($rTotal)->setRowHeight(26);
        $sheet->setAutoFilter("A5:F" . ($rTotal - 1));

        // ── SHEET 2: Ringkasan per Kategori ───────────────────────────────────
        $ws2 = $spreadsheet->createSheet();
        $ws2->setTitle('Ringkasan Kategori');
        $ws2->getColumnDimension('A')->setWidth(34);
        $ws2->getColumnDimension('B')->setWidth(16);
        $ws2->getColumnDimension('C')->setWidth(24);

        $ws2->mergeCells('A1:C1');
        $ws2->setCellValue('A1', 'RINGKASAN REALISASI PER KATEGORI');
        $ws2->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws2->getRowDimension(1)->setRowHeight(32);

        $ws2->mergeCells('A2:C2');
        $ws2->setCellValue('A2', 'Periode: ' . $periodeLabel . '   |   Perusahaan: ' . $companyLabel);
        $ws2->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['argb' => $AMBER_MAIN], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_INFO]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws2->getRowDimension(2)->setRowHeight(18);

        foreach (['A3' => 'Kategori', 'B3' => 'Jml Transaksi', 'C3' => 'Total Realisasi (Rp)'] as $cell => $label) {
            $ws2->setCellValue($cell, $label);
            $ws2->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $AMBER_500]]],
            ]);
        }
        $ws2->getRowDimension(3)->setRowHeight(28);

        $ringkasan = [];
        foreach ($rows as $item) {
            $kat = $item->kategori ?? 'Lainnya';
            $ringkasan[$kat]['count'] = ($ringkasan[$kat]['count'] ?? 0) + 1;
            $ringkasan[$kat]['total'] = ($ringkasan[$kat]['total'] ?? 0) + (float) $item->debit;
        }
        uasort($ringkasan, fn ($a, $b) => $b['total'] <=> $a['total']);

        $AMBER_50_2 = 'FFFFFBEB';
        $ri = 0;
        foreach ($ringkasan as $kat => $val) {
            $r2    = 4 + $ri;
            $bgRow = ($ri % 2 === 0) ? $AMBER_50_2 : $WHITE;

            $ws2->setCellValue("A{$r2}", $kat);
            $ws2->getStyle("A{$r2}")->applyFromArray([
                'font'      => ['size' => 9, 'color' => ['argb' => $INK], 'name' => 'Arial'],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $bgRow]],
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 1],
                'borders'   => ['bottom' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFF5F5F4']]],
            ]);

            $ws2->setCellValue("B{$r2}", $val['count']);
            $ws2->getStyle("B{$r2}")->applyFromArray([
                'font'      => ['size' => 9, 'color' => ['argb' => $INK2], 'name' => 'Arial'],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $bgRow]],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders'   => ['bottom' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFF5F5F4']]],
            ]);

            $ws2->setCellValue("C{$r2}", $val['total']);
            $ws2->getStyle("C{$r2}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => $GREEN_TEXT], 'name' => 'Arial'],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $GREEN_BG]],
                'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
                'borders'   => ['bottom' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFF5F5F4']]],
            ]);
            $ws2->getStyle("C{$r2}")->getNumberFormat()->setFormatCode('#,##0');
            $ws2->getRowDimension($r2)->setRowHeight(20);
            $ri++;
        }

        $rTot2 = 4 + $ri;
        $ws2->mergeCells("A{$rTot2}:B{$rTot2}");
        $ws2->setCellValue("A{$rTot2}", 'TOTAL KESELURUHAN');
        $ws2->getStyle("A{$rTot2}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['top' => ['borderStyle' => 'medium', 'color' => ['argb' => $AMBER_500]]],
        ]);
        $ws2->setCellValue("C{$rTot2}", "=SUM(C4:C" . ($rTot2 - 1) . ')');
        $ws2->getStyle("C{$rTot2}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_MAIN]],
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
            'borders'   => ['top' => ['borderStyle' => 'medium', 'color' => ['argb' => $AMBER_500]]],
        ]);
        $ws2->getStyle("C{$rTot2}")->getNumberFormat()->setFormatCode('#,##0');
        $ws2->getRowDimension($rTot2)->setRowHeight(26);
        $ws2->freezePane('A4');

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}