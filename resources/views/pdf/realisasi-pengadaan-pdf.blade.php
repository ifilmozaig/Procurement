<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9.5px; color: #1a1a2e; background: #ffffff; }

/* ── HEADER ── */
.header {
    text-align: center;
    margin-bottom: 12px;
    padding: 20px 24px 14px;
    border-bottom: 1px solid #fde68a;
}
.header img { width: 50px; height: 50px; display: inline-block; margin-bottom: 5px; }
.header-company { font-size: 18px; font-weight: bold; color: #D97706; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 2px; }
.header-title { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; color: #888; margin-bottom: 9px; }
.header-meta { display: inline-block; background: #fffbeb; border: 1px solid #fde68a; border-radius: 20px; padding: 4px 16px; font-size: 9px; color: #888; }

.gold-line { height: 3px; background: linear-gradient(90deg, #D97706, #fcd34d, #D97706); margin-bottom: 14px; }

.section { margin-bottom: 14px; }
.section-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #D97706; margin-bottom: 8px; padding: 0 24px 5px; border-bottom: 1px solid #fde68a; }

/* ── KPI CARDS ── */
.kpi-wrap { padding: 0 24px; margin-bottom: 12px; page-break-inside: avoid; }
.kpi-grid { width: 100%; border-collapse: separate; border-spacing: 8px 0; table-layout: fixed; }
.kpi-card {
    width: 25%;
    background: #ffffff;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 10px 12px;
    vertical-align: top;
}
.kpi-icon-amber  { background: #fef3c7; }
.kpi-icon-green  { background: #dcfce7; }
.kpi-icon-blue   { background: #dbeafe; }
.kpi-icon-yellow { background: #fff3e0; }

.kpi-label { font-size: 7.5px; color: #9ca3af; margin-bottom: 3px; text-transform: none; letter-spacing: 0; }
.kpi-value { font-size: 14px; font-weight: 700; color: #1a1a2e; line-height: 1.2; }
.kpi-value-unit { font-size: 11px; font-weight: 400; color: #9ca3af; }
.kpi-sub { font-size: 7.5px; color: #9ca3af; margin-top: 3px; }
.kpi-over { color: #dc2626; font-weight: 700; }
.kpi-under { color: #059669; font-weight: 700; }

/* ── COMPANY BANNER ── */
.co-banner-wrap { padding: 0 24px; margin-bottom: 14px; page-break-inside: avoid; }
.co-banner { border-radius: 8px; overflow: hidden; }
.co-header {
    background: #D97706;
    padding: 9px 14px;
    display: table;
    width: 100%;
    box-sizing: border-box;
}
.co-header-left  { display: table-cell; vertical-align: middle; }
.co-header-right { display: table-cell; vertical-align: middle; text-align: right; }
.co-title   { font-size: 10px; font-weight: bold; color: #ffffff; margin-bottom: 1px; }
.co-subtitle { font-size: 8px; color: rgba(255,255,255,0.75); }
.co-total-label { font-size: 7.5px; color: rgba(255,255,255,0.75); }
.co-total-val   { font-size: 12px; font-weight: bold; color: #ffffff; }

.co-rows { background: #ffffff; border: 1px solid #fde68a; border-top: none; border-radius: 0 0 8px 8px; }
.co-row-table { width: 100%; border-collapse: collapse; }
.co-row-td {
    padding: 8px 14px;
    border-bottom: 1px solid #fef9c3;
    vertical-align: middle;
}
.co-row-td:last-child { border-right: none; }
.co-row-table tr:last-child td { border-bottom: none; }

.co-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
.co-name { font-size: 9px; color: #1a1a2e; vertical-align: middle; }
.co-pct  { display: inline-block; font-size: 7.5px; font-weight: 700; padding: 1px 6px; border-radius: 10px; margin-right: 5px; }
.co-amt  { font-size: 9px; font-weight: 700; }

/* ── DIVIDER between columns ── */
.col-divider { border-right: 1px solid #fef9c3; }

/* ── DETAIL TABLE ── */
.table-wrap { padding: 0 24px; }
table.main { width: 100%; border-collapse: collapse; font-size: 8.5px; }
table.main thead {
    display: table-row-group;
}
table.main thead tr th {
    background: #D97706;
    color: #fff;
    padding: 8px;
    font-size: 7.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
    border-bottom: 2px solid #B45309;
}
table.main thead tr th:last-child { border-right: none; }
table.main thead tr th.left  { text-align: left; }
table.main thead tr th.right { text-align: right; }

tr.cat td {
    background: #fffbeb; color: #B45309; font-weight: 700; font-size: 9px;
    padding: 7px 8px; border-top: 2px solid #fde68a; border-bottom: 1px solid #fde68a;
}
tr.cat td.r { text-align: right; }
tr.cat td.c { text-align: center; }

tr.dr td { padding: 6px 8px; border-bottom: 1px solid #fde68a; vertical-align: top; }
tr.cat td { }
tr.cat td:last-child { }
tr.dr.done    td { background: #f0fdf4; }
tr.dr.pending td { background: #fffbeb; }
tr.dr.none    td { background: #fafafa; }
tr.dr.alt     td { background: #fffbeb; }

td.r { text-align: right; }
td.c { text-align: center; }

.iname   { font-weight: 600; color: #1a1a2e; line-height: 1.3; }
.ispec   { font-size: 7.5px; color: #9ca3af; margin-top: 1px; }
.ivendor { font-size: 7.5px; color: #9ca3af; }

.co-badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 7.5px; font-weight: 700; }
.co-konnco  { background: #fef3c7; color: #B45309; }
.co-kodemee { background: #d1fae5; color: #065f46; }
.co-none    { color: #9ca3af; }

.badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 7.5px; font-weight: 700; }
.badge-done    { background: #dcfce7; color: #166534; }
.badge-pending { background: #fef3c7; color: #B45309; }
.badge-none    { background: #f0f0f0; color: #555; }

.rqty { display: inline-block; padding: 2px 6px; border-radius: 10px; background: #dcfce7; border: 1px solid #bbf7d0; font-size: 8px; font-weight: 700; color: #166534; }

.over  { color: #b91c1c; font-weight: 700; }
.under { color: #166534; font-weight: 700; }

tfoot tr td { background: #D97706; color: #fff; font-weight: 700; padding: 8px; font-size: 9px; border-top: 2px solid #B45309; }
tfoot td.r    { text-align: right; }
tfoot td.c    { text-align: center; }
tfoot td.gold { color: #fef3c7; }

.doc-footer { margin-top: 12px; padding: 8px 24px; text-align: center; font-size: 7.5px; color: #bbb; letter-spacing: 0.3px; border-top: 1px solid #fde68a; }
</style>
</head>
<body>

@php
    $logoPath   = public_path('images/logo.png');
    $logoBase64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<div class="header">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="Konnco Studio"><br>
    @endif
    <div class="header-company">Konnco Studio</div>
    <div class="header-title">Laporan Realisasi Pengadaan</div>
    <div class="header-meta">
        {{ $dateLabel }}
        @if(isset($companyLabel) && $companyLabel !== 'Semua Perusahaan')
            &nbsp;&bull;&nbsp; {{ $companyLabel }}
        @else
            &nbsp;&bull;&nbsp; Semua Perusahaan
        @endif
        &nbsp;&bull;&nbsp; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>
</div>

<div class="gold-line"></div>

@php
    $grandEst    = 0;
    $grandEstReq = 0;
    $grandReal   = 0;
    $totalItems  = 0;
    $doneCount   = 0;
    $realPerCompany = [];

    foreach ($tableData as $items) {
        foreach ($items as $item) {
            $grandEst  += $item->total_estimasi;
            if ($item->has_procurement || $item->is_done) {
                $grandEstReq += $item->total_estimasi;
            }
            $grandReal += $item->realisasi;
            $totalItems++;
            if ($item->is_done) {
                $doneCount++;
                $key = $item->company_label ?? '—';
                if (!isset($realPerCompany[$key])) {
                    $cn = strtolower($key);
                    if (str_contains($cn, 'konnco'))                                   { $bg='#FDE8D4'; $tc='#B45309'; $dot='#D97706'; }
                    elseif (str_contains($cn, 'kodemee'))                              { $bg='#D1FAE5'; $tc='#065F46'; $dot='#059669'; }
                    elseif (str_contains($cn, 'mozaigg')||str_contains($cn,'mozaig')) { $bg='#EDE9FE'; $tc='#6D28D9'; $dot='#6D28D9'; }
                    elseif (str_contains($cn, 'farizz')||str_contains($cn,'fariz'))   { $bg='#DBEAFE'; $tc='#1E40AF'; $dot='#1E40AF'; }
                    else                                                                { $bg='#F3F4F6'; $tc='#374151'; $dot='#6B7280'; }
                    $realPerCompany[$key] = ['amount'=>0,'bg'=>$bg,'tc'=>$tc,'dot'=>$dot];
                }
                $realPerCompany[$key]['amount'] += $item->realisasi;
            }
        }
    }

    foreach ($realPerCompany as $k => $co) {
        $realPerCompany[$k]['pct'] = $grandReal > 0 ? round($co['amount']/$grandReal*100,1) : 0;
    }

    $selisihG    = $grandReal - $grandEstReq;
    $pct         = $grandEstReq > 0 ? round($grandReal/$grandEstReq*100,2) : 0;
    $pendingCount = $totalItems - $doneCount;
@endphp

{{-- ── KPI CARDS ── --}}
<div class="section">
    <div class="section-title">Ringkasan Anggaran</div>
    <div class="kpi-wrap">
        <table class="kpi-grid">
            <tr>
                {{-- Kartu 1: Estimasi Harga --}}
                <td class="kpi-card">
                    <div class="kpi-label">Estimasi Harga</div>
                    <div class="kpi-value">Rp{{ number_format($grandEst, 0, ',', '.') }}</div>
                    <div class="kpi-sub">Seluruh anggaran master beban</div>
                </td>

                {{-- Kartu 2: Total Realisasi --}}
                <td class="kpi-card">
                    <div class="kpi-label">Total Realisasi</div>
                    <div class="kpi-value" style="color:{{ $selisihG > 0 ? '#dc2626' : '#059669' }};">
                        Rp{{ number_format($grandReal, 0, ',', '.') }}
                    </div>
                    <div class="kpi-sub">
                        @if($selisihG > 0)
                            <span class="kpi-over">&#9650; Over Rp{{ number_format($selisihG, 0, ',', '.') }}</span>
                        @elseif($selisihG < 0)
                            <span class="kpi-under">&#9660; Hemat Rp{{ number_format(abs($selisihG), 0, ',', '.') }}</span>
                        @else
                            Sesuai estimasi
                        @endif
                    </div>
                </td>

                {{-- Kartu 3: Item Terealisasi --}}
                <td class="kpi-card">
                    <div class="kpi-label">Item Terealisasi</div>
                    <div class="kpi-value">
                        {{ $doneCount }}
                        <span class="kpi-value-unit">item</span>
                    </div>
                    <div class="kpi-sub">dari {{ $totalItems }} total item</div>
                </td>

                {{-- Kartu 4: Belum Terealisasi --}}
                <td class="kpi-card">
                    <div class="kpi-label">Belum Terealisasi</div>
                    <div class="kpi-value">
                        {{ $pendingCount }}
                        <span class="kpi-value-unit">item</span>
                    </div>
                    <div class="kpi-sub">Struk belum diunggah</div>
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- ── COMPANY BANNER (hanya jika Semua Perusahaan & ada data) ── --}}
@if((!isset($companyLabel) || $companyLabel === 'Semua Perusahaan') && count($realPerCompany) > 0)
<div class="co-banner-wrap">
    <div class="co-banner">
        {{-- Header oranye --}}
        <div class="co-header">
            <div class="co-header-left">
                <div class="co-title">Realisasi Per Perusahaan</div>
                <div class="co-subtitle">{{ count($realPerCompany) }} perusahaan</div>
            </div>
            <div class="co-header-right">
                <div class="co-total-label">Total</div>
                <div class="co-total-val">Rp{{ number_format($grandReal, 0, ',', '.') }}</div>
            </div>
        </div>
        {{-- Rows perusahaan: 2 kolom --}}
        <div class="co-rows">
            @php
                $coEntries  = array_values($realPerCompany);
                $coKeys     = array_keys($realPerCompany);
                $coCount    = count($coEntries);
                $stripEmoji = fn($s) => trim(preg_replace('/[\x{1F300}-\x{1FFFF}|\x{2600}-\x{27FF}]\s*/u', '', $s));
            @endphp
            <table class="co-row-table">
                @for($i = 0; $i < $coCount; $i += 2)
                @php $isLastOdd = !isset($coEntries[$i+1]) && ($coCount % 2 !== 0); @endphp
                <tr>
                    @if($isLastOdd)
                    {{-- Item ganjil terakhir: full width rata tengah --}}
                    <td class="co-row-td" colspan="2" style="text-align:center;">
                        <span class="co-dot" style="background:{{ $coEntries[$i]['dot'] }}; display:inline-block; vertical-align:middle;"></span>
                        &nbsp;
                        <span class="co-name" style="display:inline; vertical-align:middle;">{{ $stripEmoji($coKeys[$i]) }}</span>
                        &nbsp;&nbsp;
                        <span class="co-pct" style="background:{{ $coEntries[$i]['bg'] }}; color:{{ $coEntries[$i]['tc'] }}; display:inline-block; vertical-align:middle;">
                            {{ $coEntries[$i]['pct'] }}%
                        </span>
                        &nbsp;
                        <span class="co-amt" style="color:{{ $coEntries[$i]['tc'] }}; display:inline; vertical-align:middle;">
                            Rp{{ number_format($coEntries[$i]['amount'], 0, ',', '.') }}
                        </span>
                    </td>
                    @else
                    {{-- Kolom kiri --}}
                    <td class="co-row-td col-divider" style="width:50%;">
                        <span class="co-dot" style="background:{{ $coEntries[$i]['dot'] }};"></span>
                        <span class="co-name">{{ $stripEmoji($coKeys[$i]) }}</span>
                        &nbsp;&nbsp;
                        <span class="co-pct" style="background:{{ $coEntries[$i]['bg'] }}; color:{{ $coEntries[$i]['tc'] }};">
                            {{ $coEntries[$i]['pct'] }}%
                        </span>
                        <span class="co-amt" style="color:{{ $coEntries[$i]['tc'] }};">
                            Rp{{ number_format($coEntries[$i]['amount'], 0, ',', '.') }}
                        </span>
                    </td>
                    {{-- Kolom kanan --}}
                    <td class="co-row-td" style="width:50%;">
                        <span class="co-dot" style="background:{{ $coEntries[$i+1]['dot'] }};"></span>
                        <span class="co-name">{{ $stripEmoji($coKeys[$i+1]) }}</span>
                        &nbsp;&nbsp;
                        <span class="co-pct" style="background:{{ $coEntries[$i+1]['bg'] }}; color:{{ $coEntries[$i+1]['tc'] }};">
                            {{ $coEntries[$i+1]['pct'] }}%
                        </span>
                        <span class="co-amt" style="color:{{ $coEntries[$i+1]['tc'] }};">
                            Rp{{ number_format($coEntries[$i+1]['amount'], 0, ',', '.') }}
                        </span>
                    </td>
                    @endif
                </tr>
                @endfor
            </table>
        </div>
    </div>
</div>
@endif

{{-- ── DETAIL TABLE ── --}}
<div class="section" style="page-break-inside: auto;">
    <div class="section-title" style="page-break-after: avoid;">Detail Item Pengadaan</div>
    <div class="table-wrap">
    <table class="main" style="page-break-inside: auto;">
        <thead>
            <tr>
                <th style="width:20px;">#</th>
                <th style="width:58px;">Perusahaan</th>
                <th class="left">Nama Item</th>
                <th style="width:28px;">Unit</th>
                <th style="width:22px;">Est. QTY</th>
                <th class="right" style="width:80px;">Est. Harga</th>
                <th class="right" style="width:85px;">Total Est.</th>
                <th style="width:22px;">Real. QTY</th>
                <th class="right" style="width:85px;">Realisasi</th>
                <th class="right" style="width:70px;">Selisih</th>
                <th style="width:72px;">Status</th>
            </tr>
        </thead>
        <tbody>
        @php $no = 0; $alt = false; @endphp
        @foreach($tableData as $catName => $items)
            @php
                $cEstProc = $items->filter(fn($i) => $i->has_procurement || $i->is_done)->sum('total_estimasi');
                $cReal    = $items->sum('realisasi');
                $cDone    = $items->where('is_done', true)->count();
                $cAll     = $items->count();
                $cSel     = $cReal - $cEstProc;
                $cPct     = $cAll > 0 ? round($cDone / $cAll * 100) : 0;
            @endphp
            <tr class="cat">
                <td class="c">&bull;</td>
                <td></td>
                <td style="font-size:9px;">
                    {{ $catName }}
                    <span style="font-weight:400; font-size:7.5px;">({{ $cAll }} item)</span>
                </td>
                <td></td>
                <td class="c" style="font-size:8px; color:#B45309;">{{ $cDone }}/{{ $cAll }}</td>
                <td></td>
                <td class="r">{{ $cEstProc > 0 ? 'Rp'.number_format($cEstProc,0,',','.') : '—' }}</td>
                <td></td>
                <td class="r" style="color:#059669;">{{ $cReal > 0 ? 'Rp'.number_format($cReal,0,',','.') : '—' }}</td>
                <td class="r" style="font-size:8px;">
                    @if($cReal > 0 && $cEstProc > 0)
                        @if($cSel > 0)<span class="over">+Rp{{ number_format($cSel,0,',','.') }}</span>
                        @elseif($cSel < 0)<span class="under">Rp{{ number_format($cSel,0,',','.') }}</span>
                        @else<span style="color:#9ca3af;">Rp0</span>@endif
                    @else &mdash; @endif
                </td>
                <td class="c" style="color:#B45309;">{{ $cPct }}%</td>
            </tr>

            @foreach($items as $item)
            @php
                $no++;
                $alt      = !$alt;
                $rowClass = $item->is_done ? 'done' : ($item->has_procurement ? 'pending' : ($alt ? 'alt' : 'none'));
                $coLabel  = $item->company_label ?? '';
                $coClass  = ($item->company_target ?? '') === 'konnco' ? 'co-konnco' : 'co-kodemee';
            @endphp
            <tr class="dr {{ $rowClass }}">
                <td class="c" style="color:#9ca3af;">{{ $no }}</td>
                <td class="c">
                    @if($coLabel && $coLabel !== '—')
                        <span class="co-badge {{ $coClass }}">{{ preg_replace('/[\x{1F300}-\x{1FFFF}|\x{2600}-\x{27FF}]\s*/u', '', $coLabel) }}</span>
                    @else
                        <span class="co-none">&mdash;</span>
                    @endif
                </td>
                <td>
                    <div class="iname">{{ $item->item_name }}</div>
                    @if($item->specification)<div class="ispec">{{ $item->specification }}</div>@endif
                    @if($item->vendor)<div class="ivendor">{{ $item->vendor }}</div>@endif
                </td>
                <td class="c" style="color:#6b7280;">{{ $item->unit ?? '—' }}</td>
                <td class="c" style="font-weight:700;">
                    @if(($item->show_est_qty ?? false) && ($item->est_qty ?? 0) > 0){{ $item->est_qty }}@else —@endif
                </td>
                <td class="r" style="color:#374151;">Rp{{ number_format($item->estimated_price,0,',','.') }}</td>
                <td class="r" style="font-weight:600;">
                    @if($item->is_done || $item->has_procurement)
                        Rp{{ number_format($item->total_estimasi,0,',','.') }}
                    @else
                        <span style="color:#1a1a2e;">—</span>
                    @endif
                </td>
                <td class="c">
                    @if($item->is_done && ($item->realisasi_qty ?? 0) > 0)
                        <span class="rqty">{{ $item->realisasi_qty }}</span>
                    @else —@endif
                </td>
                <td class="r" style="font-weight:700; color:{{ $item->is_done ? '#059669' : '#9ca3af' }};">
                    {{ $item->is_done ? 'Rp'.number_format($item->realisasi,0,',','.') : '—' }}
                </td>
                <td class="r" style="font-size:8px;">
                    @if($item->is_done && $item->selisih !== null)
                        @if($item->selisih > 0)<span class="over">+Rp{{ number_format($item->selisih,0,',','.') }}</span>
                        @elseif($item->selisih < 0)<span class="under">Rp{{ number_format($item->selisih,0,',','.') }}</span>
                        @else<span style="color:#9ca3af;">Rp0</span>@endif
                    @else &mdash; @endif
                </td>
                <td class="c">
                    @if($item->is_done)<span class="badge badge-done">Terealisasi</span>
                    @elseif($item->has_procurement)<span class="badge badge-pending">Proses</span>
                    @else<span class="badge badge-none">Belum</span>@endif
                </td>
            </tr>
            @endforeach
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right; padding-right:10px; font-size:8.5px; letter-spacing:1px; text-transform:uppercase;" class="gold">Grand Total</td>
                <td class="r">Rp{{ number_format($grandEstReq,0,',','.') }}</td>
                <td></td>
                <td class="r" style="color:#fef3c7;">Rp{{ number_format($grandReal,0,',','.') }}</td>
                <td class="r" style="font-size:8px;">
                    @if($selisihG > 0)<span style="color:#fca5a5;">+Rp{{ number_format($selisihG,0,',','.') }}</span>
                    @elseif($selisihG < 0)<span style="color:#a7f3d0;">Rp{{ number_format($selisihG,0,',','.') }}</span>
                    @else<span style="color:#fef3c7;">Rp0</span>@endif
                </td>
                <td class="c gold">{{ $pct }}%</td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>

<div class="doc-footer">
    Dokumen ini digenerate secara otomatis oleh sistem Konnco Studio
    &bull; Laporan Realisasi Pengadaan &bull; {{ $dateLabel }}
    &bull; {{ now()->format('d M Y, H:i') }} WIB
</div>

</body>
</html>