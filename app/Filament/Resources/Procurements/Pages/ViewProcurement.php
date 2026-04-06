<?php

namespace App\Filament\Resources\Procurements\Pages;

use App\Filament\Resources\Procurements\ProcurementResource;
use App\Models\Company;
use App\Models\ProcurementItem;
use App\Models\ProcurementPaymentProof;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ViewProcurement extends ViewRecord
{
    protected static string $resource = ProcurementResource::class;

    public function getBreadcrumbs(): array
    {
        $user   = auth()->user();
        $record = $this->getRecord();

        if ($user->hasRole('super_admin')) {
            $dashboardName = 'Procurement';
            $dashboardUrl  = '/admin/procurements';
        } elseif ($user->hasRole(['hrga'])) {
            $dashboardName = 'HRGA';
            $dashboardUrl  = '/admin/procurements';
        } elseif ($user->hasRole(['finance', 'finance_staff'])) {
            $dashboardName = 'Finance Dashboard';
            $dashboardUrl  = '/admin/dashboard-finance-staff';
        } elseif ($user->hasRole(['finance_manager', 'atasan_finance'])) {
            $dashboardName = 'Manager Dashboard';
            $dashboardUrl  = '/admin/dashboard-manager';
        } else {
            $dashboardName = 'Procurement';
            $dashboardUrl  = '/admin/procurements';
        }

        return [
            $dashboardUrl => $dashboardName,
            '#'           => $record->procurement_number,
        ];
    }

    /**
     * Membaca perusahaan dari DUA sumber:
     *   1. Tabel pivot baru  procurement_item_companies  (multi-company)
     *   2. Kolom lama        company_id                  (backward compat)
     */
    private function getCompaniesInProcurement($record): \Illuminate\Support\Collection
    {
        $itemIds = ProcurementItem::where('procurement_id', $record->id)->pluck('id');

        // 1️⃣ Dari pivot table baru (many-to-many)
        $pivotCompanyIds = DB::table('procurement_item_companies')
            ->whereIn('procurement_item_id', $itemIds)
            ->distinct()
            ->pluck('company_id');

        // 2️⃣ Fallback: dari kolom lama company_id (single)
        $legacyCompanyIds = ProcurementItem::where('procurement_id', $record->id)
            ->whereNotNull('company_id')
            ->distinct()
            ->pluck('company_id');

        // Gabungkan, hilangkan duplikat
        $allCompanyIds = $pivotCompanyIds->merge($legacyCompanyIds)->unique()->values();

        return Company::whereIn('id', $allCompanyIds)->orderBy('name')->get();
    }

    private function buildPaymentProofFormFields($record): array
    {
        $companies = $this->getCompaniesInProcurement($record);
        $fields    = [];

        if ($companies->count() <= 1) {
            $fields[] = $this->makeNominalField('realisasi_amount', 'Nominal Realisasi (Rp)', $record);
            $fields[] = $this->makeFileField('payment_proof', 'Struk Pembayaran');
            return $fields;
        }

        // ✅ Filament v5: Radio ada di Filament\Forms\Components\Radio
        $fields[] = Radio::make('upload_mode')
            ->label('Mode Upload')
            ->options([
                'gabung' => 'Upload Gabung (1 Struk untuk semua perusahaan)',
                'pisah'  => 'Upload Pisah (Struk per Perusahaan)',
            ])
            ->default('gabung')
            ->live();

        $fields[] = $this->makeNominalField('realisasi_amount', 'Nominal Realisasi (Rp)', $record)
            ->required(fn (Get $get) => $get('upload_mode') !== 'pisah')
            ->visible(fn (Get $get) => $get('upload_mode') !== 'pisah');

        $fields[] = $this->makeFileField('payment_proof', 'Struk Pembayaran')
            ->required(fn (Get $get) => $get('upload_mode') !== 'pisah')
            ->visible(fn (Get $get) => $get('upload_mode') !== 'pisah');

        foreach ($companies as $company) {
            $safeKey = 'company_' . $company->id;

            // ✅ Filament v5: Section ada di Filament\Schemas\Components\Section
            $fields[] = Section::make($company->name)
                ->icon('heroicon-o-building-office')
                ->schema([
                    $this->makeNominalField(
                        "{$safeKey}_amount",
                        "Nominal Realisasi {$company->name} (Rp)",
                        $record
                    )
                    ->required(fn (Get $get) => $get('upload_mode') === 'pisah')
                    ->visible(fn (Get $get) => $get('upload_mode') === 'pisah'),

                    $this->makeFileField(
                        "{$safeKey}_proof",
                        "Struk Pembayaran {$company->name}"
                    )
                    ->required(fn (Get $get) => $get('upload_mode') === 'pisah')
                    ->visible(fn (Get $get) => $get('upload_mode') === 'pisah'),
                ])
                ->visible(fn (Get $get) => $get('upload_mode') === 'pisah');
        }

        return $fields;
    }

    private function makeNominalField(string $name, string $label, $record): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->prefix('Rp')
            ->placeholder('Masukkan nominal pembayaran...')
            ->hint(function () use ($record) {
                $total = ProcurementItem::where('procurement_id', $record->id)
                    ->get()
                    ->sum(fn ($i) => $i->quantity * $i->estimated_price);
                return 'Estimasi total: Rp ' . number_format($total, 0, ',', '.');
            })
            ->hintColor('warning')
            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : '')
            ->dehydrateStateUsing(fn ($state) => (int) str_replace('.', '', $state ?? ''))
            ->extraInputAttributes([
                'x-data'     => '{}',
                'x-on:input' => "
                    let raw = \$event.target.value.replace(/\\./g, '').replace(/[^0-9]/g, '');
                    if (raw === '') { \$event.target.value = ''; return; }
                    let formatted = parseInt(raw, 10).toLocaleString('id-ID');
                    \$event.target.value = formatted;
                    \$event.target.dispatchEvent(new Event('change'));
                ",
                'inputmode' => 'numeric',
            ]);
    }

    private function makeFileField(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
            ->acceptedFileTypes(['image/*', 'application/pdf'])
            ->maxSize(5120)
            ->disk('public')
            ->directory('payment-proofs')
            ->helperText('Unggah struk pembayaran (Gambar atau PDF, maks. 5MB)');
    }

    protected function getHeaderActions(): array
    {
        return [

            // ── BACK TO DASHBOARD ─────────────────────────────────────────
            Actions\Action::make('back_to_dashboard')
                ->label('Back to Dashboard')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(function () {
                    $user = auth()->user();
                    if ($user->hasRole('super_admin'))       return '/admin/procurements';
                    if ($user->hasRole(['hrga']))             return '/admin/procurements';
                    if ($user->hasRole(['finance']))          return '/admin/dashboard-finance-staff';
                    if ($user->hasRole(['finance_manager'])) return '/admin/dashboard-manager';
                    return '/admin/procurements';
                }),

            // ── DOWNLOAD PDF ──────────────────────────────────────────────
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->visible(fn ($record) =>
                    $record->status === 'COMPLETED' ||
                    auth()->user()->hasRole('super_admin')
                )
                ->action(function ($record) {
                    $procurement = $record->load([
                        'user',
                        'items.vendorModel',
                        'items.company',
                        'items.companies',
                        'attachments',
                        'reviewer',
                        'forwarder',
                        'managerApprover',
                        'paymentProofs.company',
                    ]);

                    $pdf = Pdf::loadView('pdf.procurement', compact('procurement'))
                        ->setPaper('a4', 'portrait')
                        ->setOptions([
                            'defaultFont'          => 'Arial',
                            'isRemoteEnabled'      => false,
                            'isHtml5ParserEnabled' => true,
                        ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "procurement-{$procurement->procurement_number}.pdf"
                    );
                }),

            // ── EDIT ──────────────────────────────────────────────────────
            Actions\EditAction::make()
                ->label('Edit')
                ->visible(fn ($record) =>
                    $record->status === 'DRAFT' &&
                    ($record->user_id === auth()->id() || auth()->user()->hasRole('super_admin'))
                ),

            // ── CATATAN REVISI ────────────────────────────────────────────
            Actions\Action::make('show_revision_note')
                ->label('⚠ Lihat Catatan Revisi')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('warning')
                ->modalHeading('Catatan Revisi dari Finance')
                ->modalDescription(fn ($record) => $record->revision_note)
                ->modalSubmitActionLabel('Tutup')
                ->modalCancelAction(false)
                ->visible(fn ($record) =>
                    $record->status === 'DRAFT' &&
                    !empty($record->revision_note) &&
                    $record->user_id === auth()->id()
                )
                ->action(fn () => null),

            // ── SUBMIT ────────────────────────────────────────────────────
            Actions\Action::make('submit')
                ->label('Submit Procurement')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Ajukan Procurement')
                ->modalDescription('Apakah Anda yakin ingin mengajukan procurement ini? Setelah diajukan, procurement akan dikirim ke Finance untuk ditinjau.')
                ->modalSubmitActionLabel('Ya, Ajukan')
                ->modalCancelActionLabel('Batal')
                ->visible(fn ($record) =>
                    $record->status === 'DRAFT' &&
                    ($record->user_id === auth()->id() || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record) {
                    $record->update([
                        'status'        => 'PENDING',
                        'submitted_at'  => now(),
                        'revision_note' => null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Procurement Diajukan')
                        ->body("Procurement {$record->procurement_number} telah berhasil diajukan untuk ditinjau.")
                        ->send();
                }),

            // ── CANCEL SUBMISSION ─────────────────────────────────────────
            Actions\Action::make('cancel_submission')
                ->label('Cancel Submission')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Pengajuan')
                ->modalDescription('Apakah Anda yakin ingin membatalkan pengajuan ini? Procurement akan kembali ke status DRAFT dan dapat diedit kembali.')
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->modalCancelActionLabel('Tidak')
                ->visible(fn ($record) =>
                    $record->status === 'PENDING' &&
                    (
                        ($record->user_id === auth()->id() && !auth()->user()->hasRole(['finance', 'finance_staff', 'finance_manager'])) ||
                        auth()->user()->hasRole('super_admin')
                    )
                )
                ->action(function ($record) {
                    $record->update([
                        'status'       => 'DRAFT',
                        'submitted_at' => null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Pengajuan Dibatalkan')
                        ->body("Procurement {$record->procurement_number} telah dikembalikan ke status DRAFT.")
                        ->send();
                }),

            // ── MINTA REVISI (Finance → HRGA) ─────────────────────────────
            Actions\Action::make('request_revision')
                ->label('Minta Revisi')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Minta Revisi ke HRGA')
                ->modalSubmitActionLabel('Kirim Revisi')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('revision_note')
                        ->label('Catatan Revisi')
                        ->required()
                        ->rows(4)
                        ->placeholder('Tuliskan apa yang perlu direvisi oleh HRGA...'),
                ])
                ->visible(fn ($record) =>
                    $record->status === 'PENDING' &&
                    (auth()->user()->hasRole(['finance', 'finance_staff', 'finance_manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record, array $data) {
                    $record->update([
                        'status'        => 'DRAFT',
                        'revision_note' => $data['revision_note'],
                        'revised_by'    => auth()->id(),
                        'revised_at'    => now(),
                    ]);

                    Notification::make()
                        ->warning()
                        ->title('Revisi Diminta')
                        ->body("Procurement {$record->procurement_number} dikembalikan ke HRGA untuk direvisi.")
                        ->send();
                }),

            // ── APPROVE (Finance) ─────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Procurement')
                ->modalDescription(fn ($record) => "Apakah Anda yakin ingin menyetujui procurement {$record->procurement_number}?")
                ->modalSubmitActionLabel('Ya, Setujui')
                ->modalCancelActionLabel('Batal')
                ->visible(fn ($record) =>
                    $record->status === 'PENDING' &&
                    $record->type === 'OPEX' &&
                    (auth()->user()->hasRole(['finance', 'finance_staff', 'finance_manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record) {
                    $record->update([
                        'status'      => 'APPROVED',
                        'approved_at' => now(),
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Procurement Disetujui')
                        ->body("Procurement {$record->procurement_number} telah disetujui.")
                        ->send();
                }),

            // ── FORWARD TO MANAGER ────────────────────────────────────────
            Actions\Action::make('forward_to_manager')
                ->label('Forward to Manager')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Teruskan ke Manager')
                ->modalSubmitActionLabel('Ya, Teruskan')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('finance_comment')
                        ->label('Catatan untuk Manager')
                        ->required()
                        ->rows(3)
                        ->placeholder('Masukkan catatan atau alasan meneruskan ke Manager...'),
                ])
                ->visible(fn ($record) =>
                    $record->status === 'PENDING' &&
                    in_array($record->type, ['CAPEX', 'CASH_ADVANCE']) &&
                    (auth()->user()->hasRole(['finance', 'finance_manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record, array $data) {
                    $record->update([
                        'status'          => 'PROCESSING',
                        'finance_comment' => $data['finance_comment'],
                        'forwarded_by'    => auth()->id(),
                        'forwarded_at'    => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Diteruskan ke Manager')
                        ->body("Procurement {$record->procurement_number} telah diteruskan ke Manager untuk persetujuan.")
                        ->send();
                }),

            // ── REJECT (Finance) ──────────────────────────────────────────
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Procurement')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Masukkan alasan penolakan...'),
                ])
                ->visible(fn ($record) =>
                    $record->status === 'PENDING' &&
                    (auth()->user()->hasRole(['finance', 'finance_staff', 'finance_manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record, array $data) {
                    $record->update([
                        'status'           => 'REJECTED',
                        'rejected_at'      => now(),
                        'rejection_reason' => $data['rejection_reason'],
                        'reviewed_by'      => auth()->id(),
                        'reviewed_at'      => now(),
                    ]);

                    Notification::make()
                        ->warning()
                        ->title('Procurement Ditolak')
                        ->body("Procurement {$record->procurement_number} telah ditolak.")
                        ->send();
                }),

            // ── APPROVE (Manager) ─────────────────────────────────────────
            Actions\Action::make('manager_approve')
                ->label('Approve')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Procurement (Manager)')
                ->modalDescription('Apakah Anda yakin ingin menyetujui procurement ini?')
                ->modalSubmitActionLabel('Ya, Setujui')
                ->modalCancelActionLabel('Batal')
                ->visible(fn ($record) =>
                    $record->status === 'PROCESSING' &&
                    (auth()->user()->hasRole(['finance_manager', 'atasan_finance', 'manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record) {
                    $record->update([
                        'status'              => 'APPROVED',
                        'approved_at'         => now(),
                        'approved_by_manager' => auth()->id(),
                        'manager_approved_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Procurement Disetujui oleh Manager')
                        ->body("Procurement {$record->procurement_number} telah disetujui oleh Manager.")
                        ->send();
                }),

            // ── REJECT (Manager) ──────────────────────────────────────────
            Actions\Action::make('manager_reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Procurement (Manager)')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Masukkan alasan penolakan...'),
                ])
                ->visible(fn ($record) =>
                    $record->status === 'PROCESSING' &&
                    (auth()->user()->hasRole(['finance_manager', 'atasan_finance', 'manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record, array $data) {
                    $record->update([
                        'status'              => 'REJECTED',
                        'rejected_at'         => now(),
                        'rejection_reason'    => $data['rejection_reason'],
                        'manager_comment'     => $data['rejection_reason'],
                        'approved_by_manager' => auth()->id(),
                        'manager_approved_at' => now(),
                    ]);

                    Notification::make()
                        ->warning()
                        ->title('Procurement Ditolak oleh Manager')
                        ->body("Procurement {$record->procurement_number} telah ditolak oleh Manager.")
                        ->send();
                }),

            // ── CANCEL REJECT ─────────────────────────────────────────────
            Actions\Action::make('cancel_reject')
                ->label('Cancel Reject')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Penolakan')
                ->modalDescription('Apakah Anda yakin ingin membatalkan penolakan ini? Procurement akan dikembalikan ke status PROCESSING untuk ditinjau ulang.')
                ->modalSubmitActionLabel('Ya, Batalkan Penolakan')
                ->modalCancelActionLabel('Tidak')
                ->visible(fn ($record) =>
                    $record->status === 'REJECTED' &&
                    (auth()->user()->hasRole(['finance_manager', 'atasan_finance', 'manager']) || auth()->user()->hasRole('super_admin'))
                )
                ->action(function ($record) {
                    $record->update([
                        'status'              => 'PROCESSING',
                        'rejected_at'         => null,
                        'rejection_reason'    => null,
                        'manager_comment'     => null,
                        'approved_by_manager' => null,
                        'manager_approved_at' => null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Penolakan Dibatalkan')
                        ->body("Penolakan procurement {$record->procurement_number} telah dibatalkan. Status dikembalikan ke PROCESSING untuk ditinjau ulang.")
                        ->send();

                    return redirect()->to(ViewProcurement::getUrl(['record' => $record->id]));
                }),

            // ── DELETE ────────────────────────────────────────────────────
            Actions\DeleteAction::make()
                ->label('Delete')
                ->successRedirectUrl(function () {
                    $user = auth()->user();
                    if ($user->hasRole('super_admin'))                         return '/admin/procurements';
                    if ($user->hasRole(['hrga']))                               return '/admin/procurements';
                    if ($user->hasRole(['finance', 'finance_staff']))           return '/admin/dashboard-finance-staff';
                    if ($user->hasRole(['finance_manager', 'atasan_finance'])) return '/admin/dashboard-manager';
                    return '/admin/procurements';
                })
                ->visible(fn ($record) =>
                    auth()->user()->hasRole('super_admin') ||
                    (in_array($record->status, ['DRAFT', 'REJECTED', 'COMPLETED']) && $record->user_id === auth()->id())
                ),

            // ── UPLOAD STRUK PEMBAYARAN ───────────────────────────────────
            Actions\Action::make('upload_payment_proof')
                ->label('Upload Struk Pembayaran')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->visible(fn ($record) =>
                    $record->status === 'APPROVED' &&
                    (
                        // OPEX & CASH_ADVANCE: hanya HRGA yang membuat procurement
                        (
                            in_array($record->type, ['OPEX', 'CASH_ADVANCE']) &&
                            auth()->user()->hasRole(['hrga']) &&
                            $record->user_id === auth()->id()
                        ) ||
                        // CAPEX: hanya finance & finance_staff (tanpa cek user_id), setelah approve manager
                        (
                            $record->type === 'CAPEX' &&
                            (
                                auth()->user()->hasRole('finance') ||
                                auth()->user()->hasRole('finance_staff')
                            )
                        ) ||
                        auth()->user()->hasRole('super_admin')
                    )
                )
                ->form(fn ($record) => $this->buildPaymentProofFormFields($record))
                ->action(function ($record, array $data) {
                    $companies      = $this->getCompaniesInProcurement($record);
                    $uploadMode     = $data['upload_mode'] ?? 'gabung';
                    $totalRealisasi = 0;

                    // Hapus struk lama
                    $oldProofs = ProcurementPaymentProof::where('procurement_id', $record->id)->get();
                    foreach ($oldProofs as $old) {
                        if ($old->payment_proof) {
                            Storage::disk('public')->delete($old->payment_proof);
                        }
                    }
                    ProcurementPaymentProof::where('procurement_id', $record->id)->delete();

                    if ($companies->count() <= 1 || $uploadMode === 'gabung') {
                        $totalRealisasi = $data['realisasi_amount'] ?? 0;

                        $record->update([
                            'payment_proof'             => $data['payment_proof'] ?? null,
                            'payment_proof_uploaded_at' => now(),
                            'realisasi_amount'          => $totalRealisasi,
                            'status'                    => 'COMPLETED',
                            'completed_at'              => now(),
                        ]);

                        if ($companies->count() === 1) {
                            ProcurementPaymentProof::create([
                                'procurement_id'   => $record->id,
                                'company_id'       => $companies->first()->id,
                                'payment_proof'    => $data['payment_proof'] ?? null,
                                'realisasi_amount' => $totalRealisasi,
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Struk Pembayaran Diunggah')
                            ->body("Struk pembayaran untuk {$record->procurement_number} berhasil diunggah. Realisasi: Rp " . number_format($totalRealisasi, 0, ',', '.'))
                            ->send();

                    } else {
                        // Mode pisah — 1 struk per perusahaan
                        foreach ($companies as $company) {
                            $safeKey        = 'company_' . $company->id;
                            $amount         = $data["{$safeKey}_amount"] ?? 0;
                            $proof          = $data["{$safeKey}_proof"]  ?? null;
                            $totalRealisasi += $amount;

                            ProcurementPaymentProof::create([
                                'procurement_id'   => $record->id,
                                'company_id'       => $company->id,
                                'payment_proof'    => $proof,
                                'realisasi_amount' => $amount,
                            ]);
                        }

                        $record->update([
                            'payment_proof_uploaded_at' => now(),
                            'realisasi_amount'          => $totalRealisasi,
                            'status'                    => 'COMPLETED',
                            'completed_at'              => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Struk Pembayaran Pisah Diunggah')
                            ->body("Struk pisah untuk {$record->procurement_number} berhasil diunggah. Total Realisasi: Rp " . number_format($totalRealisasi, 0, ',', '.'))
                            ->send();
                    }

                    return redirect()->to(ViewProcurement::getUrl(['record' => $record->id]));
                }),

            // ── CANCEL STRUK PEMBAYARAN ───────────────────────────────────
            Actions\Action::make('cancel_payment_proof')
                ->label('Cancel Payment Proof')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Struk Pembayaran')
                ->modalDescription('Apakah Anda yakin ingin menghapus struk pembayaran ini? Status akan kembali ke APPROVED.')
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->modalCancelActionLabel('Tidak')
                ->visible(fn ($record) =>
                    $record->status === 'COMPLETED' &&
                    (
                        $record->payment_proof !== null ||
                        ProcurementPaymentProof::where('procurement_id', $record->id)->exists()
                    ) &&
                    (
                        // OPEX & CASH_ADVANCE: hanya HRGA yang membuat procurement
                        (
                            in_array($record->type, ['OPEX', 'CASH_ADVANCE']) &&
                            auth()->user()->hasRole(['hrga']) &&
                            $record->user_id === auth()->id()
                        ) ||
                        // CAPEX: hanya finance & finance_staff (tanpa cek user_id)
                        (
                            $record->type === 'CAPEX' &&
                            (
                                auth()->user()->hasRole('finance') ||
                                auth()->user()->hasRole('finance_staff')
                            )
                        ) ||
                        auth()->user()->hasRole('super_admin')
                    )
                )
                ->action(function ($record) {
                    if ($record->payment_proof) {
                        Storage::disk('public')->delete($record->payment_proof);
                    }

                    $proofs = ProcurementPaymentProof::where('procurement_id', $record->id)->get();
                    foreach ($proofs as $proof) {
                        if ($proof->payment_proof) {
                            Storage::disk('public')->delete($proof->payment_proof);
                        }
                    }
                    ProcurementPaymentProof::where('procurement_id', $record->id)->delete();

                    $record->update([
                        'payment_proof'             => null,
                        'payment_proof_uploaded_at' => null,
                        'realisasi_amount'          => null,
                        'payment_proof_konnco'      => null,
                        'payment_proof_kodemee'     => null,
                        'realisasi_amount_konnco'   => null,
                        'realisasi_amount_kodemee'  => null,
                        'status'                    => 'APPROVED',
                        'completed_at'              => null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Struk Pembayaran Dibatalkan')
                        ->body("Struk pembayaran telah dihapus. Anda dapat mengunggah struk baru.")
                        ->send();

                    return redirect()->to(ViewProcurement::getUrl(['record' => $record->id]));
                }),
        ];
    }
}