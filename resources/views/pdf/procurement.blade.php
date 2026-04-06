<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Procurement {{ $procurement->procurement_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11.5px; color: #1a1a2e; background: #ffffff; padding: 28px 32px; }
        .header { text-align: center; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #e8e0d5; }
        .header img { width: 48px; height: 48px; display: inline-block; margin-bottom: 4px; }
        .header-company { font-size: 18px; font-weight: bold; color: #c8860a; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 1px; }
        .header-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; color: #888; margin-bottom: 8px; }
        .header-meta { display: inline-block; background: #faf6f0; border: 1px solid #e8e0d5; border-radius: 20px; padding: 4px 15px; font-size: 9.5px; color: #888; }
        .section { margin-bottom: 16px; }
        .section-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #c8860a; margin-bottom: 7px; padding-bottom: 5px; border-bottom: 1px solid #f0e8d8; }
        .info-table { width: 100%; border-collapse: separate; border-spacing: 0 0; margin-bottom: 0; }
        .info-card { background: #faf6f0; border: 1px solid #ede5d8; border-radius: 5px; padding: 8px 12px; }
        .info-card-label { font-size: 7.5px; text-transform: uppercase; letter-spacing: 1px; color: #aaa; margin-bottom: 2px; font-weight: bold; }
        .info-card-value { font-size: 11.5px; color: #1a1a2e; font-weight: bold; }
        .info-card-value.normal { font-weight: normal; }
        .badge { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 9px; font-weight: bold; letter-spacing: 0.5px; }
        .badge-draft     { background: #f0f0f0; color: #555; }
        .badge-pending   { background: #fff4e0; color: #b45309; }
        .badge-approved  { background: #e6f9f0; color: #1a7a4a; }
        .badge-rejected  { background: #fdecea; color: #b91c1c; }
        .badge-processing{ background: #e8f0fe; color: #1d4ed8; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .company-chips { margin-top: 4px; }
        .company-chip { display: inline-block; background: #fff8ee; border: 1px solid #f0d498; border-radius: 12px; padding: 2px 8px; font-size: 9px; color: #92400e; font-weight: bold; margin: 2px 2px 0 0; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #c8860a; }
        th { color: #fff; padding: 8px 10px; text-align: left; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 6px 10px; font-size: 10.5px; color: #333; border-bottom: 1px solid #f0ebe2; }
        tbody tr:nth-child(even) td { background: #faf6f0; }
        tbody tr:last-child td { border-bottom: none; }
        .td-company-badge { display: inline-block; background: #fff8ee; border: 1px solid #f0d498; border-radius: 10px; padding: 1px 7px; font-size: 8.5px; color: #92400e; font-weight: bold; margin: 1px 2px 1px 0; }
        .row-total td { font-weight: bold; background: #ffffff !important; color: #1a1a2e; border-top: 2px solid #e8e0d5; border-bottom: none; font-size: 9.5px; letter-spacing: 1px; text-transform: uppercase; }
        .row-realisasi-header td { font-weight: bold; background: #fdf6e3 !important; color: #92400e; border-bottom: none; font-size: 9px; letter-spacing: 0.5px; }
        .row-realisasi-company td { font-weight: bold; background: #fff8ee !important; color: #92400e; border-bottom: 1px solid #f0d498; font-size: 10px; }
        .row-realisasi-total td { font-weight: bold; background: #faf6f0 !important; color: #1a1a2e; border-bottom: none; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .row-selisih td { font-weight: bold; background: #ffffff !important; border-bottom: none; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .timeline-item { display: table; width: 100%; margin-bottom: 7px; }
        .timeline-left { display: table-cell; width: 22px; vertical-align: top; padding-top: 1px; }
        .timeline-dot-wrap { text-align: center; }
        .timeline-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; border: 2px solid #c8860a; background: #fff; }
        .timeline-right { display: table-cell; vertical-align: top; padding-left: 4px; padding-bottom: 4px; }
        .timeline-title { font-size: 10.5px; font-weight: bold; color: #1a1a2e; margin-bottom: 1px; }
        .timeline-meta { font-size: 9px; color: #999; }
        .timeline-note { font-size: 8px; color: #666; margin-top: 2px; padding: 3px 7px; background: #faf6f0; border-left: 2px solid #e8d8b8; border-radius: 0 3px 3px 0; }
        .footer { margin-top: 14px; padding-top: 11px; border-top: 1px solid #e8e0d5; }
        .sign-grid { display: table; width: 100%; }
        .sign-col { display: table-cell; width: 33%; text-align: center; padding: 0 10px; }
        .sign-col-half { display: table-cell; width: 50%; text-align: center; padding: 0 10px; }
        .sign-col:first-child, .sign-col-half:first-child { padding-left: 0; }
        .sign-col:last-child, .sign-col-half:last-child  { padding-right: 0; }
        .sign-label { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #c8860a; margin-bottom: 38px; }
        .sign-line { border-top: 1px solid #bbb; padding-top: 5px; font-size: 10.5px; color: #333; font-weight: bold; }
        .footer-note { margin-top: 10px; text-align: center; font-size: 7.5px; color: #bbb; letter-spacing: 0.3px; }
        .text-right { text-align: right; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="header">
        @php
            $logoPath   = public_path('images/logo.png');
            $logoBase64 = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null;
        @endphp
        @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="Konnco Studio"><br>
        @endif
        <div class="header-company">Konnco Studio</div>
        <div class="header-title">Procurement Request</div>
        <div class="header-meta">
            {{ $procurement->procurement_number }}
            &nbsp;&bull;&nbsp;
            Dicetak: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

    {{-- ── INFO PROCUREMENT ─────────────────────────────────────── --}}
    @php
        // ✅ FIX PERUSAHAAN: gabungkan dari pivot (companies) + legacy (company_id)
        $itemCompanies = $procurement->items->flatMap(function ($item) {
            // Dari relasi pivot baru (many-to-many)
            $pivotCompanies = $item->companies ?? collect();
            // Dari relasi legacy (single company_id)
            $legacyCompany  = ($item->company && !$pivotCompanies->contains('id', $item->company->id))
                ? collect([$item->company])
                : collect();
            return $pivotCompanies->merge($legacyCompany);
        })->unique('id')->sortBy('name')->values();

        // ✅ FIX VENDOR: pakai vendorModel (relasi baru) dengan fallback kolom lama
        // Gunakan vendor_name attribute (getVendorNameAttribute) dari model
        $itemVendors = $procurement->items->map(function ($item) {
            return $item->vendorModel?->name ?? $item->vendor ?? null;
        })->filter()->unique()->sort()->values();

        // Cek apakah ada item yang punya vendor
        $hasVendor = $procurement->items->contains(function ($item) {
            return $item->vendorModel?->name || $item->vendor;
        });

        $paymentProofs = $procurement->paymentProofs()->with('company')->get();
        $hasPaymentProofs = $paymentProofs->isNotEmpty();
        $isPisah          = $hasPaymentProofs && $paymentProofs->count() > 1;
        $realisasiAmt     = $procurement->realisasi_amount;
        $totalEstimasi    = $procurement->items->sum(fn($i) => $i->quantity * $i->estimated_price);
        $hasRealisasi     = $procurement->status === 'COMPLETED' && $realisasiAmt !== null;
        $selisih          = $hasRealisasi ? ($realisasiAmt - $totalEstimasi) : null;
    @endphp

    <div class="section">
        <div class="section-title">Informasi Procurement</div>
        <table class="info-table">
            <tr>
                <td style="width:50%;padding:0 4px 8px 0;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Nomor Procurement</div>
                        <div class="info-card-value">{{ $procurement->procurement_number }}</div>
                    </div>
                </td>
                <td style="width:50%;padding:0 0 8px 4px;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Requester</div>
                        <div class="info-card-value normal">{{ $procurement->user->name ?? '-' }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding:0 4px 8px 0;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Tipe</div>
                        <div class="info-card-value">{{ $procurement->type }}</div>
                    </div>
                </td>
                <td style="padding:0 0 8px 4px;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Status</div>
                        <div class="info-card-value">
                            @php
                                $badgeClass = match($procurement->status) {
                                    'DRAFT'      => 'badge-draft',
                                    'PENDING'    => 'badge-pending',
                                    'APPROVED'   => 'badge-approved',
                                    'REJECTED'   => 'badge-rejected',
                                    'PROCESSING' => 'badge-processing',
                                    'COMPLETED'  => 'badge-completed',
                                    default      => 'badge-draft',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $procurement->status }}</span>
                        </div>
                    </div>
                </td>
            </tr>

            @if($itemCompanies->isNotEmpty())
            <tr>
                <td colspan="2" style="padding:0 0 8px 0;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Perusahaan Pengaju</div>
                        <div class="company-chips">
                            @foreach($itemCompanies as $co)
                                <span class="company-chip">{{ $co->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
            @endif

            @if($itemVendors->isNotEmpty())
            <tr>
                <td colspan="2" style="padding:0 0 8px 0;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Vendor / Supplier</div>
                        <div class="company-chips">
                            @foreach($itemVendors as $v)
                                <span class="company-chip" style="background:#f0f9ff;border-color:#bae6fd;color:#0369a1;">{{ $v }}</span>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
            @endif

            <tr>
                <td colspan="2" style="padding:0 0 8px 0;vertical-align:top;">
                    <div class="info-card">
                        <div class="info-card-label">Alasan Pengajuan</div>
                        <div class="info-card-value normal">{{ $procurement->reason }}</div>
                    </div>
                </td>
            </tr>

            @if($procurement->rejection_reason)
            <tr>
                <td colspan="2" style="padding:0 0 8px 0;vertical-align:top;">
                    <div class="info-card" style="border-color:#fca5a5;background:#fff5f5;">
                        <div class="info-card-label" style="color:#b91c1c;">Alasan Penolakan</div>
                        <div class="info-card-value normal" style="color:#b91c1c;">{{ $procurement->rejection_reason }}</div>
                    </div>
                </td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ── DAFTAR ITEM ───────────────────────────────────────────── --}}
    @if($procurement->items->count() > 0)
    <div class="section">
        <div class="section-title">Daftar Item</div>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:22%">Nama Item</th>
                    <th style="width:20%">Spesifikasi</th>
                    <th style="width:6%">Qty</th>
                    <th style="width:16%">Harga Estimasi</th>
                    <th style="width:{{ $hasVendor ? '16%' : '32%' }}">Perusahaan</th>
                    @if($hasVendor)<th style="width:16%">Vendor</th>@endif
                </tr>
            </thead>
            <tbody>
                @foreach($procurement->items as $i => $item)
                @php
                    // ✅ FIX PERUSAHAAN per baris: cek pivot companies dulu, fallback ke company_id
                    $rowCompanies = $item->companies ?? collect();
                    if ($rowCompanies->isEmpty() && $item->company) {
                        $rowCompanies = collect([$item->company]);
                    }

                    // ✅ FIX VENDOR per baris: vendorModel dengan fallback kolom lama
                    // $item->vendor langsung mengakses kolom string (tidak konflik karena ada di $fillable)
                    $rowVendor = $item->vendorModel?->name ?? $item->vendor ?? '-';
                @endphp
                <tr>
                    <td style="color:#999;">{{ $i + 1 }}</td>
                    <td style="font-weight:bold;">{{ $item->item_name }}</td>
                    <td style="color:#666;font-size:9.5px;">{{ $item->specification ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</td>
                    <td>
                        @if($rowCompanies->isNotEmpty())
                            @foreach($rowCompanies as $co)
                                <span class="td-company-badge">{{ $co->name }}</span>
                            @endforeach
                        @else
                            <span style="color:#bbb;">-</span>
                        @endif
                    </td>
                    @if($hasVendor)<td style="color:#555;font-size:9.5px;">{{ $rowVendor !== '-' ? $rowVendor : '' }}</td>@endif
                </tr>
                @endforeach

                <tr class="row-total">
                    <td colspan="5" class="text-right">Total Estimasi</td>
                    <td colspan="2">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</td>
                </tr>

                @if($hasRealisasi)
                    @php
                        $selisihColor = $selisih > 0 ? '#b91c1c' : '#166534';
                        $selisihLabel = $selisih > 0 ? 'Over Budget' : ($selisih < 0 ? 'Under Budget (Hemat)' : 'Sesuai Estimasi');
                        $selisihSign  = $selisih > 0 ? '+' : '';
                    @endphp

                    @if($isPisah)
                        <tr class="row-realisasi-header">
                            <td colspan="7" style="font-size:8px;letter-spacing:1px;padding-top:10px;padding-bottom:4px;">
                                REALISASI PER PERUSAHAAN
                            </td>
                        </tr>
                        @foreach($paymentProofs as $proof)
                        <tr class="row-realisasi-company">
                            <td colspan="5" class="text-right" style="font-size:9px;">
                                Realisasi {{ $proof->company->name ?? '-' }}
                                @if($proof->updated_at)
                                    &nbsp;&bull;&nbsp; {{ $proof->updated_at->format('d M Y') }}
                                @endif
                            </td>
                            <td colspan="2">Rp {{ number_format($proof->realisasi_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="row-realisasi-total">
                            <td colspan="5" class="text-right">Total Realisasi</td>
                            <td colspan="2">Rp {{ number_format($realisasiAmt, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr class="row-realisasi-total">
                            <td colspan="5" class="text-right">
                                Total Realisasi
                                @if($procurement->payment_proof_uploaded_at)
                                    &nbsp;&bull;&nbsp; {{ $procurement->payment_proof_uploaded_at->format('d M Y') }}
                                @endif
                                @if($hasPaymentProofs && $paymentProofs->first()->company)
                                    &nbsp;&bull;&nbsp; {{ $paymentProofs->first()->company->name }}
                                @endif
                            </td>
                            <td colspan="2">Rp {{ number_format($realisasiAmt, 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    <tr class="row-selisih">
                        <td colspan="5" class="text-right" style="color:{{ $selisihColor }};">
                            Selisih ({{ $selisihLabel }})
                        </td>
                        <td colspan="2" style="color:{{ $selisihColor }};">
                            {{ $selisihSign }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    {{-- ── RIWAYAT STATUS ───────────────────────────────────────── --}}
    <div class="section">
        <div class="section-title">Riwayat Status</div>

        @if($procurement->submitted_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#c8860a;background:#fff8ee;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title">Submitted</div>
                <div class="timeline-meta">{{ $procurement->submitted_at->format('d M Y, H:i') }} WIB &nbsp;&bull;&nbsp; {{ $procurement->user->name ?? '-' }}</div>
            </div>
        </div>
        @endif

        @if($procurement->reviewed_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#0ea5e9;background:#f0f9ff;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title">Reviewed oleh Finance</div>
                <div class="timeline-meta">{{ $procurement->reviewed_at->format('d M Y, H:i') }} WIB &nbsp;&bull;&nbsp; {{ $procurement->reviewer->name ?? '-' }}</div>
            </div>
        </div>
        @endif

        @if($procurement->forwarded_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#f59e0b;background:#fffbeb;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title">Diteruskan ke Manager</div>
                <div class="timeline-meta">{{ $procurement->forwarded_at->format('d M Y, H:i') }} WIB &nbsp;&bull;&nbsp; {{ $procurement->forwarder->name ?? '-' }}</div>
                @if($procurement->finance_comment)
                <div class="timeline-note">{{ $procurement->finance_comment }}</div>
                @endif
            </div>
        </div>
        @endif

        @if($procurement->manager_approved_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#8b5cf6;background:#f5f3ff;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title">{{ $procurement->status === 'REJECTED' ? 'Ditolak' : 'Disetujui' }} oleh Manager</div>
                <div class="timeline-meta">{{ $procurement->manager_approved_at->format('d M Y, H:i') }} WIB &nbsp;&bull;&nbsp; {{ $procurement->managerApprover->name ?? '-' }}</div>
                @if($procurement->manager_comment)
                <div class="timeline-note">{{ $procurement->manager_comment }}</div>
                @endif
            </div>
        </div>
        @endif

        @if($procurement->approved_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#10b981;background:#f0fdf4;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title">Approved</div>
                <div class="timeline-meta">{{ $procurement->approved_at->format('d M Y, H:i') }} WIB</div>
            </div>
        </div>
        @endif

        @if($procurement->rejected_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#ef4444;background:#fff5f5;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title">Rejected</div>
                <div class="timeline-meta">{{ $procurement->rejected_at->format('d M Y, H:i') }} WIB</div>
                @if($procurement->rejection_reason)
                <div class="timeline-note" style="border-color:#fca5a5;">{{ $procurement->rejection_reason }}</div>
                @endif
            </div>
        </div>
        @endif

        @if($procurement->completed_at)
        <div class="timeline-item">
            <div class="timeline-left"><div class="timeline-dot-wrap">
                <span class="timeline-dot" style="border-color:#10b981;background:#f0fdf4;"></span>
            </div></div>
            <div class="timeline-right">
                <div class="timeline-title" style="color:#166534;">Completed</div>
                <div class="timeline-meta">
                    {{ $procurement->completed_at->format('d M Y, H:i') }} WIB
                    @if($isPisah)
                        @foreach($paymentProofs as $proof)
                            &nbsp;&bull;&nbsp;
                            <span style="color:#92400e;font-weight:bold;">
                                {{ $proof->company->name ?? '?' }}: Rp {{ number_format($proof->realisasi_amount, 0, ',', '.') }}
                            </span>
                        @endforeach
                    @elseif($realisasiAmt)
                        &nbsp;&bull;&nbsp;
                        <span style="color:#166534;font-weight:bold;">
                            Realisasi: Rp {{ number_format($realisasiAmt, 0, ',', '.') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── TANDA TANGAN ─────────────────────────────────────────── --}}
    <div class="footer">
        @php $needsManager = in_array($procurement->type, ['CAPEX', 'CASH_ADVANCE']); @endphp
        <div class="sign-grid">
            <div class="{{ $needsManager ? 'sign-col' : 'sign-col-half' }}">
                <div class="sign-label">Requester</div>
                <div class="sign-line">{{ $procurement->user->name ?? '-' }}</div>
            </div>
            <div class="{{ $needsManager ? 'sign-col' : 'sign-col-half' }}">
                <div class="sign-label">Finance</div>
                <div class="sign-line">{{ $procurement->reviewer->name ?? '-' }}</div>
            </div>
            {{-- Manager hanya ditampilkan jika tipe memerlukan persetujuan manager (CAPEX / CASH_ADVANCE) --}}
            @if($needsManager)
            <div class="sign-col">
                <div class="sign-label">Manager</div>
                <div class="sign-line">{{ $procurement->managerApprover->name ?? '-' }}</div>
            </div>
            @endif
        </div>
        <div class="footer-note">
            Dokumen ini digenerate secara otomatis oleh sistem Konnco Studio &bull; {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

</body>
</html>