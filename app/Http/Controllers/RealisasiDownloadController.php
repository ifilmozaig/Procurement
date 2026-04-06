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

class RealisasiDownloadController
{
    public function download(Request $request): StreamedResponse
    {
        $type     = $request->get('type', 'excel');
        $dateFrom = $request->get('dateFrom', '');
        $dateTo   = $request->get('dateTo', '');
        $company  = $request->get('company', 'all');

        $tableData = $this->getTableData($company, $dateFrom, $dateTo);

        if ($type === 'pdf') {
            return $this->streamPdf($tableData, $dateFrom, $dateTo, $company);
        }

        return $this->streamExcel($tableData, $dateFrom, $dateTo, $company);
    }

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
            if (in_array($type, ['CAPEX', 'CASH_ADVANCE'])) {
                return in_array($status, ['APPROVED', 'COMPLETED']);
            }
            if ($type === 'OPEX') {
                return in_array($status, ['PROCESSING', 'APPROVED', 'COMPLETED']);
            }
            return false;
        });
        $estQty = (int) $estQtyItems->sum('quantity');
        if ($estQty === 0 && $estQtyItems->isNotEmpty()) $estQty = $estQtyItems->count();

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

        $isDone        = $completedProcs->isNotEmpty();
        $completedProc = $completedProcs->sortByDesc(
            fn ($p) => $p->procurement?->payment_proof_uploaded_at
        )->first();

        $hasProcurement = $linkedForCo->isNotEmpty();

        // Total estimasi hanya dihitung jika ada procurement request
        $totalEst = $hasProcurement
            ? $masterItem->estimated_price * max($totalQty, 1)
            : 0;

        $realisasiAmount = 0;

        if ($isDone) {
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
        }

        $showEstQty = $linkedForCo->contains(function ($p) {
            $status = $p->procurement?->status ?? '';
            $type   = strtoupper($p->procurement?->type ?? '');
            if (in_array($type, ['CAPEX', 'CASH_ADVANCE'])) return in_array($status, ['APPROVED', 'COMPLETED']);
            if ($type === 'OPEX') return in_array($status, ['PROCESSING', 'APPROVED', 'COMPLETED']);
            return false;
        });

        $realisasiQty = 0;
        if ($isDone) {
            $realisasiQty = (int) $completedProcs->sum('quantity');
            if ($realisasiQty === 0) $realisasiQty = $completedProcs->count();
        }

                // ✅ Selisih dihitung dari: realisasiAmount - totalEst (dari backend)
        $selisih = $isDone ? ($realisasiAmount - $totalEst) : null;

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
            'show_est_qty'         => $showEstQty,
            'realisasi_qty'        => $realisasiQty,
            'total_estimasi'       => $totalEst,
            'is_done'              => $isDone,
            'realisasi'            => $realisasiAmount,
            'selisih'              => $selisih, // ✅ pakai kalkulasi baru
            'struk_date'           => $completedProc?->procurement?->payment_proof_uploaded_at,
            'struk_list'           => $strukList,
            'has_procurement'      => $hasProcurement,
            'company_id'           => $co?->id,
            'company_target'       => $co ? strtolower($co->name) : null,
            'count_in_grand_total' => true,
            'company_label'        => ($isDone || $hasProcurement) ? $companyLabel : '—',
            'company_color'        => $companyColor,
        ]);
    }

    protected function streamPdf(
        Collection $tableData,
        string $dateFrom,
        string $dateTo,
        string $company
    ): StreamedResponse {
        $dateLabel = ($dateFrom && $dateTo)
            ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($dateTo)->format('d M Y')
            : 'Semua Periode';

        $companyModel = ($company !== 'all') ? Company::find((int) $company) : null;
        $companyLabel = $companyModel ? $companyModel->name : 'Semua Perusahaan';

        $html = view('pdf.realisasi-pengadaan-pdf', [
            'tableData'    => $tableData,
            'dateLabel'    => $dateLabel,
            'companyLabel' => $companyLabel,
        ])->render();

        $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        $fileName = 'realisasi-pengadaan_' . now()->format('d-m-Y') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }

    protected function streamExcel(
        Collection $tableData,
        string $dateFrom,
        string $dateTo,
        string $company
    ): StreamedResponse {
        $grandEst = 0; $grandReal = 0; $totalItems = 0; $doneCount = 0;
        $realPerCompany = [];

        foreach ($tableData as $items) {
            foreach ($items as $item) {
                $grandEst   += $item->total_estimasi;
                $grandReal  += $item->realisasi;
                $totalItems++;
                if ($item->is_done) {
                    $doneCount++;
                    $key = $item->company_label ?? '—';
                    $realPerCompany[$key] = ($realPerCompany[$key] ?? 0) + $item->realisasi;
                }
            }
        }
        $selisihG = $grandReal - $grandEst;

        $companyModel = ($company !== 'all') ? Company::find((int) $company) : null;
        $companyStr   = $companyModel ? ('🏢 ' . $companyModel->name) : 'Semua Perusahaan';

        $periodeLabel = ($dateFrom && $dateTo)
            ? 'Periode: ' . \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y')
              . ' s/d '   . \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y')
            : 'Semua Periode';

        $dateLabel = ($dateFrom && $dateTo)
            ? \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') . '_sd_' . \Carbon\Carbon::parse($dateTo)->format('d-m-Y')
            : now()->format('d-m-Y');
        $fileName = 'realisasi-pengadaan_' . $dateLabel . '.xlsx';

        $AMBER_DARK  = 'FFD97706'; $AMBER_LIGHT = 'FFFEF3C7'; $AMBER_BDR = 'FFFDE68A';
        $GREEN_BG    = 'FFF0FDF4'; $GREEN_TEXT  = 'FF166534'; $RED_TEXT   = 'FFDC2626';
        $WHITE       = 'FFFFFFFF'; $GRAY_BG     = 'FFF9FAFB'; $FOOTER_BG  = 'FFD97706';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Realisasi Pengadaan');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);
        $sheet = $spreadsheet->getActiveSheet();

        // Baris 1: Judul
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN REALISASI PENGADAAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_DARK]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Baris 2: Sub-judul
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2',
            $periodeLabel . '   |   ' . $companyStr
            . '   |   Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB'
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['argb' => $AMBER_DARK], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFFF8E1']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Baris 3: Summary
        $selisihColor  = $selisihG > 0 ? $RED_TEXT : ($selisihG < 0 ? 'FF16A34A' : 'FFA8A29E');
        $selisihPrefix = $selisihG > 0 ? '▲ Over  ' : ($selisihG < 0 ? '▼ Hemat  ' : '');

        $sheet->mergeCells('A3:C3'); $sheet->mergeCells('D3:F3');
        $sheet->mergeCells('G3:I3'); $sheet->mergeCells('J3:K3');
        $sheet->setCellValue('A3', '💰 Total Estimasi:  Rp ' . number_format($grandEst, 0, ',', '.'));
        $sheet->setCellValue('D3', '✅ Total Realisasi:  Rp ' . number_format($grandReal, 0, ',', '.'));
        $sheet->setCellValue('G3', '📦 ' . $doneCount . ' / ' . $totalItems . ' item terealisasi');
        $sheet->setCellValue('J3', '⚖️ Selisih:  ' . $selisihPrefix . 'Rp ' . number_format(abs($selisihG), 0, ',', '.'));
        $summaryBase = [
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['argb' => $AMBER_DARK]],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFFFBEB']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $AMBER_BDR]]],
        ];
        foreach (['A3', 'D3', 'G3'] as $c) $sheet->getStyle($c)->applyFromArray($summaryBase);
        $sheet->getStyle('J3')->applyFromArray(
            array_replace_recursive($summaryBase, ['font' => ['color' => ['argb' => $selisihColor]]])
        );
        $sheet->getRowDimension(3)->setRowHeight(24);

        // Baris 4: Breakdown per perusahaan
        if ($company === 'all' && count($realPerCompany) > 0) {
            $breakStyle = [
                'font'      => ['bold' => true, 'size' => 9, 'name' => 'Arial', 'color' => ['argb' => $AMBER_DARK]],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFFF8E1']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $AMBER_BDR]]],
            ];
            $coEntries  = array_map(
                fn ($k, $v) => $k . '     Rp ' . number_format($v, 0, ',', '.'),
                array_keys($realPerCompany),
                array_values($realPerCompany)
            );
            $chunks     = array_chunk($coEntries, 2);
            $currentRow = 4;
            foreach ($chunks as $pair) {
                if (count($pair) === 2) {
                    $sheet->mergeCells("A{$currentRow}:F{$currentRow}");
                    $sheet->mergeCells("G{$currentRow}:K{$currentRow}");
                    $sheet->setCellValue("A{$currentRow}", $pair[0]);
                    $sheet->setCellValue("G{$currentRow}", $pair[1]);
                    $sheet->getStyle("A{$currentRow}")->applyFromArray($breakStyle);
                    $sheet->getStyle("G{$currentRow}")->applyFromArray($breakStyle);
                } else {
                    $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                    $sheet->setCellValue("A{$currentRow}", $pair[0]);
                    $sheet->getStyle("A{$currentRow}")->applyFromArray($breakStyle);
                }
                $sheet->getRowDimension($currentRow)->setRowHeight(18);
                $currentRow++;
            }
            $headerRow = $currentRow;
        } else {
            $headerRow = 4;
        }

        // Header kolom
        $headers = [
            'A' => '#',
            'B' => 'Perusahaan',
            'C' => 'Nama Item',
            'D' => 'Est. Harga / Satuan (Rp)',
            'E' => 'Unit',
            'F' => 'Est. QTY',
            'G' => 'Total Estimasi (Rp)',
            'H' => 'Real. QTY',
            'I' => 'Realisasi (Rp)',
            'J' => 'Selisih (Rp)',
            'K' => 'Status Realisasi',
        ];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$headerRow}", $label);
        }
        $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_DARK]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFF59E0B']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);
        $sheet->freezePane('A' . ($headerRow + 1));

        // Data rows
        $row = $headerRow + 1;
        $no  = 0;

        foreach ($tableData as $categoryName => $items) {
            // Category: hitung estimasi & selisih hanya dari item yang ada procurement
            $cEstProc  = $items->filter(fn($i) => $i->has_procurement || $i->is_done)
                               ->sum('total_estimasi');
            $cReal     = $items->sum('realisasi');
            $cDone     = $items->where('is_done', true)->count();
            $cAll      = $items->count();
            $cPct      = $cAll > 0 ? round($cDone / $cAll * 100) : 0;
            // Selisih = realisasi aktual - total estimasi procurement (konsisten dengan backend)
            $cSel      = $cReal - $cEstProc;

            // Category row
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", "◆  {$categoryName}  ({$cAll} item)");
            $sheet->setCellValue("F{$row}", "{$cDone}/{$cAll}");
            $sheet->setCellValue("G{$row}", $cEstProc > 0 ? $cEstProc : null);
            $sheet->setCellValue("I{$row}", $cReal > 0 ? $cReal : null);
            // Selisih category dengan tanda ▲▼
            if ($cReal > 0 && $cEstProc > 0) {
                if ($cSel > 0)       $sheet->setCellValue("J{$row}", '▲ +' . number_format($cSel, 0, ',', '.'));
                elseif ($cSel < 0)   $sheet->setCellValue("J{$row}", '▼ ' . number_format($cSel, 0, ',', '.'));
                else                 $sheet->setCellValue("J{$row}", 'Rp0');
            } else {
                $sheet->setCellValue("J{$row}", null);
            }
            $sheet->setCellValue("K{$row}", $cPct . '%');
            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                'font'    => ['bold' => true, 'size' => 9, 'color' => ['argb' => $AMBER_DARK], 'name' => 'Arial'],
                'fill'    => ['fillType' => 'solid', 'startColor' => ['argb' => $AMBER_LIGHT]],
                'borders' => [
                    'top'    => ['borderStyle' => 'medium', 'color' => ['argb' => $AMBER_BDR]],
                    'bottom' => ['borderStyle' => 'thin',   'color' => ['argb' => $AMBER_BDR]],
                ],
            ]);
            $sheet->getStyle("G{$row}:I{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G{$row}:I{$row}")->getAlignment()->setHorizontal('right');
            // Kolom J pakai text (sudah ada tanda ▲▼) dan center
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('@');
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal('center');
            foreach (['F', 'H', 'K'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal('center');
            }
            if ($cEstProc > 0 && $cSel > 0) {
                $sheet->getStyle("J{$row}")->getFont()->getColor()->setARGB($RED_TEXT);
            } elseif ($cEstProc > 0 && $cSel < 0) {
                $sheet->getStyle("J{$row}")->getFont()->getColor()->setARGB('FF16A34A');
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            foreach ($items as $item) {
                $no++;
                $namaItem = $item->item_name;
                if ($item->specification) $namaItem .= "\n" . $item->specification;
                if ($item->vendor)        $namaItem .= "\n🏪 " . $item->vendor;

                // ✅ Pakai selisih dari backend (realisasi - total_estimasi)
                $itemSelisih = $item->selisih;

                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $item->company_label);
                $sheet->setCellValue("C{$row}", $namaItem);
                $sheet->setCellValue("D{$row}", $item->estimated_price);
                $sheet->setCellValue("E{$row}", $item->unit ?? '—');
                $sheet->setCellValue("F{$row}",
                    ($item->show_est_qty && $item->est_qty > 0) ? $item->est_qty : null
                );

                // Total Estimasi hanya tampil jika ada procurement
                if ($item->has_procurement || $item->is_done) {
                    $sheet->setCellValue("G{$row}", $item->total_estimasi);
                } else {
                    $sheet->setCellValue("G{$row}", '—');
                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFE5E7EB']]],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                    $sheet->getStyle("G{$row}")->getFont()->getColor()->setARGB('FFD1D5DB');
                }

                $sheet->setCellValue("H{$row}",
                    ($item->is_done && $item->realisasi_qty > 0) ? $item->realisasi_qty : null
                );
                $sheet->setCellValue("I{$row}", $item->is_done ? $item->realisasi : null);
                // Selisih per item dengan tanda ▲▼
                if ($item->is_done && $itemSelisih !== null) {
                    if ($itemSelisih > 0)       $sheet->setCellValue("J{$row}", '▲ +' . number_format($itemSelisih, 0, ',', '.'));
                    elseif ($itemSelisih < 0)   $sheet->setCellValue("J{$row}", '▼ ' . number_format($itemSelisih, 0, ',', '.'));
                    else                        $sheet->setCellValue("J{$row}", 'Rp0');
                } else {
                    $sheet->setCellValue("J{$row}", $item->is_done ? null : null);
                }

                if ($item->is_done)             $statusLabel = '✅ Sudah Terealisasi';
                elseif ($item->has_procurement) $statusLabel = '⏳ Belum Terealisasi';
                else                            $statusLabel = '— Belum Diajukan';
                $sheet->setCellValue("K{$row}", $statusLabel);

                $fill = $item->is_done
                    ? $GREEN_BG
                    : ($item->has_procurement ? 'FFFFFBEB' : $GRAY_BG);

                $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                    'font'    => ['size' => 9, 'name' => 'Arial'],
                    'fill'    => ['fillType' => 'solid', 'startColor' => ['argb' => $fill]],
                    'borders' => ['bottom' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFF5F5F4']]],
                ]);
                $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("G{$row}:I{$row}")->getNumberFormat()->setFormatCode('#,##0');
                // Kolom J pakai text (sudah ada tanda ▲▼)
                $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('@');

                foreach (['A', 'B', 'E', 'F', 'H', 'K'] as $c) {
                    $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal('center');
                }
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal('right');
                foreach (['G', 'I'] as $c) {
                    $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal('right');
                }
                // Selisih center seperti di blade
                $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);

                // Warna kolom Perusahaan
                if ($item->company_label === '—') {
                    $sheet->getStyle("B{$row}")->getFont()->getColor()->setARGB('FF78716C');
                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                } else {
                    $coArgb = 'FF' . ltrim(str_replace('#', '', $item->company_color), 'FF');
                    $sheet->getStyle("B{$row}")->getFont()->getColor()->setARGB($coArgb);
                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                }

                // Warna status
                if ($item->is_done) {
                    $sheet->getStyle("K{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => $GREEN_TEXT]],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFDCFCE7']],
                    ]);
                    $sheet->getStyle("I{$row}")->getFont()->getColor()->setARGB($GREEN_TEXT);
                    $sheet->getStyle("I{$row}")->getFont()->setBold(true);
                    if ($item->realisasi_qty > 0) {
                        $sheet->getStyle("H{$row}")->getFont()->getColor()->setARGB($GREEN_TEXT);
                        $sheet->getStyle("H{$row}")->getFont()->setBold(true);
                    }
                } elseif ($item->has_procurement) {
                    $sheet->getStyle("K{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFF59E0B']],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFEF3C7']],
                    ]);
                } else {
                    $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB('FFA8A29E');
                }

                // ✅ Warna selisih pakai itemSelisih
                if ($item->is_done && $itemSelisih !== null) {
                    if ($itemSelisih > 0) {
                        $sheet->getStyle("J{$row}")->getFont()->getColor()->setARGB($RED_TEXT);
                        $sheet->getStyle("J{$row}")->getFont()->setBold(true);
                    } elseif ($itemSelisih < 0) {
                        $sheet->getStyle("J{$row}")->getFont()->getColor()->setARGB('FF16A34A');
                        $sheet->getStyle("J{$row}")->getFont()->setBold(true);
                    }
                }

                $sheet->getRowDimension($row)->setRowHeight(
                    ($item->specification || $item->vendor) ? 32 : 18
                );
                $row++;
            }
        }

        // Grand total
        $pct = $grandEst > 0 ? round($grandReal / $grandEst * 100, 2) : 0;

        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'GRAND TOTAL  (' . $totalItems . ' baris)');
        $sheet->setCellValue("G{$row}", $grandEst > 0 ? $grandEst : null);
        $sheet->setCellValue("I{$row}", $grandReal);
        $sheet->setCellValue("J{$row}", $selisihG);
        $sheet->setCellValue("K{$row}", $pct . '%');
        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $FOOTER_BG]],
            'alignment' => ['vertical' => 'center'],
            'borders'   => ['top' => ['borderStyle' => 'medium', 'color' => ['argb' => 'FFF59E0B']]],
        ]);
        $sheet->getStyle("G{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
        foreach (['G', 'I', 'J'] as $c) $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal('right');
        foreach (['F', 'H', 'K'] as $c) $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal('center');
        if ($selisihG > 0) $sheet->getStyle("J{$row}")->getFont()->getColor()->setARGB('FFFCA5A5');
        elseif ($selisihG < 0) $sheet->getStyle("J{$row}")->getFont()->getColor()->setARGB('FF6EE7B7');
        $sheet->getRowDimension($row)->setRowHeight(26);

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(7);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(22);

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