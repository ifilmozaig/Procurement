<?php

namespace App\Filament\Resources\Procurements\Schemas;

use App\Filament\Resources\Procurements\Support\ProcurementStatus;
use App\Models\Company;
use App\Models\ProcurementPaymentProof;
use App\Models\Vendor;
use App\Models\VendorBankAccount;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ProcurementInfolist
{
    // ── Helper: format rupiah tanpa desimal ──────────────────────────────
    private static function rupiah(?float $amount): string
    {
        if ($amount === null) return '-';
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('procurement_number')
                    ->label('Procurement Number'),

                TextEntry::make('user.name')
                    ->label('Requested By'),

                TextEntry::make('type')
                    ->label('Procurement Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'OPEX'         => 'info',
                        'CAPEX'        => 'warning',
                        'CASH_ADVANCE' => 'success',
                        default        => 'gray',
                    }),

                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => ProcurementStatus::color($state))
                    ->formatStateUsing(fn (string $state): string => ProcurementStatus::label($state)),

                TextEntry::make('reason')
                    ->label('Reason for Procurement')
                    ->columnSpanFull(),

                // ── ITEM PROCUREMENT ─────────────────────────────────────────────
                Section::make('Item Procurement')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('item_name')
                                    ->label('Nama Item')
                                    ->weight('bold'),

                                TextEntry::make('companies_label')
                                    ->label('Untuk Perusahaan')
                                    ->html()
                                    ->state(function ($record) {
                                        $companies = $record->companies()->get();

                                        if ($companies->isNotEmpty()) {
                                            $badges = $companies->map(function ($company) {
                                                return "<span style='
                                                    display:inline-block;
                                                    padding:2px 10px;
                                                    border-radius:9999px;
                                                    background:#dbeafe;
                                                    color:#1d4ed8;
                                                    font-size:12px;
                                                    font-weight:600;
                                                    margin:2px 4px 2px 0;
                                                    border:1px solid #bfdbfe;
                                                '>{$company->name}</span>";
                                            })->implode('');

                                            return new HtmlString(
                                                "<div style='display:flex;flex-wrap:wrap;align-items:center;gap:2px;'>"
                                                . $badges
                                                . "</div>"
                                            );
                                        }

                                        $singleCompany = $record->company?->name;
                                        if ($singleCompany) {
                                            return new HtmlString(
                                                "<span style='
                                                    display:inline-block;
                                                    padding:2px 10px;
                                                    border-radius:9999px;
                                                    background:#dbeafe;
                                                    color:#1d4ed8;
                                                    font-size:12px;
                                                    font-weight:600;
                                                    border:1px solid #bfdbfe;
                                                '>{$singleCompany}</span>"
                                            );
                                        }

                                        return '-';
                                    }),

                                TextEntry::make('unit')
                                    ->label('Satuan')
                                    ->placeholder('-'),

                                TextEntry::make('specification')
                                    ->label('Spesifikasi')
                                    ->placeholder('-'),

                                TextEntry::make('quantity')
                                    ->label('Qty'),

                                // ✅ FIX: format Rp tanpa desimal
                                TextEntry::make('estimated_price')
                                    ->label('Harga Estimasi')
                                    ->formatStateUsing(fn ($state) => self::rupiah((float) $state)),

                                // ✅ FIX: format Rp tanpa desimal
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->state(fn ($record) => $record->quantity * $record->estimated_price)
                                    ->formatStateUsing(fn ($state) => self::rupiah((float) $state)),

                                TextEntry::make('vendor_info')
                                    ->label('Vendor')
                                    ->placeholder('-')
                                    ->state(function ($record) {
                                        if ($record->vendor_id) {
                                            $v = Vendor::find($record->vendor_id);
                                            if ($v) {
                                                return collect([$v->name, $v->business_type, $v->city])
                                                    ->filter()
                                                    ->implode(' • ');
                                            }
                                        }
                                        return $record->getRawOriginal('vendor') ?: null;
                                    }),

                                TextEntry::make('vendor_contact')
                                    ->label('Kontak Vendor')
                                    ->placeholder('-')
                                    ->html()
                                    ->state(function ($record) {
                                        if (!$record->vendor_id) return null;
                                        $v = Vendor::find($record->vendor_id);
                                        if (!$v) return null;

                                        $parts = [];
                                        if ($v->pic_name) $parts[] = "<span style='display:inline-flex;align-items:center;gap:4px;'>👤 <span>{$v->pic_name}</span></span>";
                                        if ($v->phone)    $parts[] = "<span style='display:inline-flex;align-items:center;gap:4px;'>📞 <span>{$v->phone}</span></span>";
                                        if ($v->email)    $parts[] = "<span style='display:inline-flex;align-items:center;gap:4px;'>✉️ <span>{$v->email}</span></span>";

                                        if (empty($parts)) return null;

                                        return new HtmlString(
                                            "<div style='display:flex;flex-wrap:wrap;gap:12px;align-items:center;'>"
                                            . implode("<span style='color:#d1d5db;'>|</span>", $parts)
                                            . "</div>"
                                        );
                                    })
                                    ->hidden(fn ($record) => !$record->vendor_id),

                                TextEntry::make('payment_method')
                                    ->label('Metode Pembayaran')
                                    ->placeholder('-')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'transfer' => '🏦 Transfer Bank',
                                        'cash'     => '💵 Tunai (Cash)',
                                        'cek'      => '📄 Cek',
                                        'giro'     => '📋 Giro',
                                        'lainnya'  => '🔖 Lainnya',
                                        default    => $state ?? '-',
                                    })
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'transfer' => 'info',
                                        'cash'     => 'success',
                                        'cek'      => 'warning',
                                        'giro'     => 'warning',
                                        default    => 'gray',
                                    }),

                                TextEntry::make('bank_account_info')
                                    ->label('Rekening Bank Tujuan')
                                    ->placeholder('-')
                                    ->state(function ($record) {
                                        if (!$record->bank_account_id) return null;
                                        $acc = VendorBankAccount::find($record->bank_account_id);
                                        if (!$acc) return null;
                                        return $acc->bank_name
                                            . ' — ' . $acc->account_number
                                            . ' a/n ' . $acc->account_name
                                            . ($acc->is_primary ? ' ⭐' : '');
                                    })
                                    ->hidden(fn ($record) => $record->payment_method !== 'transfer'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),

                        // ✅ FIX: format Rp tanpa desimal
                        TextEntry::make('total_estimated')
                            ->label('Total Estimasi')
                            ->state(fn ($record) =>
                                $record->items->sum(fn ($i) => $i->quantity * $i->estimated_price)
                            )
                            ->formatStateUsing(fn ($state) => self::rupiah((float) $state))
                            ->weight('bold')
                            ->color('warning'),
                    ])
                    ->columnSpanFull()
                    ->hidden(fn ($record) => $record->items->isEmpty()),

                // ── ATTACHMENTS ──────────────────────────────────────────────────
                RepeatableEntry::make('attachments')
                    ->label('Quotation/Proposal')
                    ->schema([
                        TextEntry::make('file_path')
                            ->label('File')
                            ->html()
                            ->formatStateUsing(function ($state, $record) {
                                $fileType = strtolower($record->file_type ?? '');
                                $isImage  = in_array($fileType, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $url      = asset('storage/' . $state);

                                if ($isImage) {
                                    return new HtmlString("
                                        <div style='max-width: 350px; margin: 0;'>
                                            <div style='text-align: center; background: #f9fafb; padding: 20px; border-radius: 8px; border: 2px solid #e5e7eb;'>
                                                <img src='{$url}' alt='Preview' style='max-width: 100%; max-height: 300px; height: auto; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);' />
                                            </div>
                                            <div style='margin-top: 12px; padding: 8px 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px;'>
                                                <p style='font-size: 12px; color: #6b7280; font-weight: 500; word-break: break-all; text-align: left; margin: 0;'>{$record->file_name}</p>
                                            </div>
                                        </div>
                                    ");
                                }

                                return new HtmlString("
                                    <div style='max-width: 350px; margin: 0;'>
                                        <div style='padding: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; text-align: center; border: 2px solid #e5e7eb;'>
                                            <svg xmlns='http://www.w3.org/2000/svg' style='width: 64px; height: 64px; margin: 0 auto; color: white;' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' />
                                            </svg>
                                            <p style='margin-top: 16px; color: white; font-weight: 600; font-size: 18px;'>PDF Document</p>
                                        </div>
                                        <div style='margin-top: 12px; padding: 8px 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px;'>
                                            <p style='font-size: 12px; color: #6b7280; font-weight: 500; word-break: break-all; text-align: left; margin: 0;'>{$record->file_name}</p>
                                        </div>
                                    </div>
                                ");
                            }),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->hidden(fn ($record) => $record->attachments->isEmpty()),

                // ── CATATAN FINANCE ──────────────────────────────────────────────
                Section::make('Catatan Finance')
                    ->schema([
                        TextEntry::make('finance_comment')
                            ->label('Catatan dari Finance')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),

                        TextEntry::make('forwardedBy.name')
                            ->label('Diteruskan oleh')
                            ->placeholder('-'),

                        TextEntry::make('forwarded_at')
                            ->label('Diteruskan pada')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn ($record) =>
                        !empty($record->finance_comment) &&
                        in_array($record->status, ['PROCESSING', 'APPROVED', 'REJECTED', 'COMPLETED']) &&
                        (
                            auth()->user()->hasRole(['finance_manager', 'atasan_finance', 'manager']) ||
                            auth()->user()->hasRole('super_admin')
                        )
                    ),

                // ── PAYMENT PROOF ─────────────────────────────────────────────────
                TextEntry::make('id')
                    ->label('Payment Proof / Bukti Pembayaran')
                    ->columnSpanFull()
                    ->html()
                    ->formatStateUsing(function ($state, $record) {

                        $renderCard = function (
                            string  $filePath,
                            string  $label,
                            ?float  $amount,
                            $uploadedAt
                        ) {
                            $url           = asset('storage/' . $filePath);
                            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $fileName      = basename($filePath);

                            // ✅ FIX: format Rp tanpa desimal di payment proof card
                            $amountHtml = $amount
                                ? "<div style='padding:8px 12px;background:rgba(255,255,255,0.5);border-radius:6px;margin-top:8px;'>
                                       <p style='font-size:12px;color:#065f46;margin:0 0 4px 0;'><strong>Realisasi:</strong></p>
                                       <p style='font-size:13px;color:#047857;font-weight:600;margin:0;'>Rp "
                                       . number_format($amount, 0, ',', '.') .
                                   "</p></div>"
                                : '';

                            $uploadInfoHtml = $uploadedAt
                                ? "<div style='flex:1;min-width:220px;'>
                                       <div style='padding:16px;background:#d1fae5;border:2px solid #10b981;border-radius:8px;'>
                                           <div style='display:flex;align-items:center;gap:8px;margin-bottom:8px;'>
                                               <svg xmlns='http://www.w3.org/2000/svg' style='width:20px;height:20px;color:#059669;' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                   <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/>
                                               </svg>
                                               <p style='font-size:14px;color:#065f46;font-weight:600;margin:0;'>Payment proof uploaded</p>
                                           </div>
                                           <div style='padding:8px 12px;background:rgba(255,255,255,0.5);border-radius:6px;margin-top:8px;'>
                                               <p style='font-size:12px;color:#065f46;margin:0 0 4px 0;'><strong>Uploaded:</strong></p>
                                               <p style='font-size:13px;color:#047857;font-weight:500;margin:0;'>"
                                               . $uploadedAt->format('d M Y H:i') .
                                           "</p></div>
                                           <div style='padding:8px 12px;background:rgba(255,255,255,0.5);border-radius:6px;margin-top:8px;'>
                                               <p style='font-size:12px;color:#065f46;margin:0 0 4px 0;'><strong>File Name:</strong></p>
                                               <p style='font-size:11px;color:#047857;word-break:break-all;margin:0;'>{$fileName}</p>
                                           </div>
                                           {$amountHtml}
                                       </div>
                                   </div>"
                                : '';

                            $previewHtml = ($fileExtension === 'pdf')
                                ? "<div style='padding:40px;background:linear-gradient(135deg,#10b981 0%,#059669 100%);border-radius:8px;text-align:center;border:2px solid #10b981;'>
                                       <svg xmlns='http://www.w3.org/2000/svg' style='width:64px;height:64px;margin:0 auto;color:white;' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                           <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'/>
                                       </svg>
                                       <p style='margin-top:16px;color:white;font-weight:600;font-size:18px;'>PDF Document</p>
                                       <a href='{$url}' target='_blank'
                                          style='display:inline-block;margin-top:16px;padding:10px 24px;background:white;color:#059669;border-radius:6px;text-decoration:none;font-weight:600;'>
                                           Open PDF
                                       </a>
                                   </div>"
                                : "<div style='text-align:center;background:#f9fafb;padding:20px;border-radius:8px;border:2px solid #10b981;'>
                                       <img src='{$url}' alt='Payment Proof'
                                            style='max-width:100%;max-height:400px;height:auto;border-radius:6px;box-shadow:0 2px 4px rgba(0,0,0,0.1);'/>
                                   </div>";

                            return "
                                <div style='margin-bottom:24px;'>
                                    <p style='font-size:13px;font-weight:700;color:#374151;margin:0 0 10px 0;
                                              padding:6px 12px;background:#f3f4f6;border-left:4px solid #10b981;border-radius:4px;'>
                                        {$label}
                                    </p>
                                    <div style='display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start;'>
                                        <div style='flex:1;min-width:280px;max-width:400px;'>{$previewHtml}</div>
                                        {$uploadInfoHtml}
                                    </div>
                                </div>
                            ";
                        };

                        $paymentProofs = ProcurementPaymentProof::where('procurement_id', $record->id)
                            ->with('company')
                            ->get();

                        if ($paymentProofs->count() > 1) {
                            $html = "<div>";
                            foreach ($paymentProofs as $proof) {
                                if (empty($proof->payment_proof)) continue;
                                $companyName = $proof->company?->name ?? 'Perusahaan';
                                $label       = "🏢 Struk {$companyName}";
                                $html       .= $renderCard(
                                    $proof->payment_proof,
                                    $label,
                                    $proof->realisasi_amount ?? null,
                                    $record->payment_proof_uploaded_at
                                );
                            }
                            $html .= "</div>";
                            return new HtmlString($html);
                        }

                        if ($paymentProofs->count() === 1 && !empty($paymentProofs->first()->payment_proof)) {
                            $proof       = $paymentProofs->first();
                            $companyName = $proof->company?->name;
                            $label       = $companyName
                                ? "🏢 Struk Pembayaran — {$companyName}"
                                : '🧾 Struk Pembayaran';

                            return new HtmlString($renderCard(
                                $proof->payment_proof,
                                $label,
                                $proof->realisasi_amount ?? $record->realisasi_amount,
                                $record->payment_proof_uploaded_at
                            ));
                        }

                        $gabungPath = $record->payment_proof ?? null;
                        if (empty($gabungPath)) return '';

                        return new HtmlString($renderCard(
                            $gabungPath,
                            '🧾 Struk Pembayaran',
                            $record->realisasi_amount ?? null,
                            $record->payment_proof_uploaded_at
                        ));
                    })
                    ->visible(fn ($record) =>
                        in_array($record->status, ['APPROVED', 'COMPLETED']) &&
                        (
                            !empty($record->payment_proof) ||
                            ProcurementPaymentProof::where('procurement_id', $record->id)->exists()
                        )
                    ),

                ViewEntry::make('image_lightbox')
                    ->label('')
                    ->view('Admin.image-lightbox')
                    ->columnSpanFull(),

                TextEntry::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->visible(fn ($record) => !empty($record->rejection_reason)),

                TextEntry::make('submitted_at')->label('Submitted At')->dateTime()->placeholder('-'),
                TextEntry::make('approved_at')->label('Approved At')->dateTime()->placeholder('-'),
                TextEntry::make('rejected_at')->label('Rejected At')->dateTime()->placeholder('-'),
                TextEntry::make('created_at')->label('Created At')->dateTime(),
                TextEntry::make('updated_at')->label('Updated At')->dateTime(),
            ])
            ->columns(2);
    }
}