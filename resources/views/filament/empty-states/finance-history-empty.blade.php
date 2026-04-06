{{--
    resources/views/filament/empty-states/finance-history-empty.blade.php
    Digunakan di: ProcurementHistoryWidget (Finance Dashboard)
    Tema: Arsip/Riwayat kosong — warna Amber
--}}
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:2rem 1rem 2.5rem; text-align:center; position:relative;">

    {{-- Partikel melayang --}}
    <div style="position:absolute;inset:0;pointer-events:none;overflow:visible;">
        <div style="position:absolute;top:10%;left:6%;width:6px;height:6px;background:#FCD34D;border-radius:50%;animation:fhFloat1 3s ease-in-out infinite;"></div>
        <div style="position:absolute;top:18%;right:8%;width:5px;height:5px;background:#F59E0B;border-radius:50%;animation:fhFloat2 2.8s ease-in-out infinite .5s;"></div>
        <div style="position:absolute;top:55%;left:4%;width:5px;height:5px;background:#FDE68A;border-radius:50%;animation:fhFloat1 3.4s ease-in-out infinite 1s;"></div>
        <div style="position:absolute;top:60%;right:5%;width:6px;height:6px;background:#FCD34D;border-radius:50%;animation:fhFloat2 3.1s ease-in-out infinite .3s;"></div>
        <div style="position:absolute;top:35%;left:2%;width:4px;height:4px;background:#F59E0B;border-radius:50%;animation:fhFloat1 2.6s ease-in-out infinite .8s;"></div>
        <div style="position:absolute;top:42%;right:3%;width:4px;height:4px;background:#FDE68A;border-radius:50%;animation:fhFloat2 2.9s ease-in-out infinite 1.4s;"></div>
    </div>

    {{-- SVG Utama --}}
    <div style="animation:fhEntrance .8s cubic-bezier(.34,1.56,.64,1) both;">
        <svg viewBox="-30 -30 310 300" xmlns="http://www.w3.org/2000/svg"
             style="width:210px;height:auto;overflow:visible;">

            <defs>
                <radialGradient id="fhGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#F59E0B" stop-opacity="0.18"/>
                    <stop offset="100%" stop-color="#F59E0B" stop-opacity="0"/>
                </radialGradient>
                <filter id="fhShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="3" dy="5" stdDeviation="5" flood-color="#D97706" flood-opacity="0.15"/>
                </filter>
                <filter id="fhBlur">
                    <feGaussianBlur stdDeviation="3"/>
                </filter>
            </defs>

            {{-- Glow latar --}}
            <circle cx="120" cy="130" r="108" fill="url(#fhGlow)"
                style="animation:fhGlowPulse 2.6s ease-in-out infinite;"/>

            {{-- ── Jam / Waktu melayang kanan ── --}}
            <g style="animation:fhClockFloat 3.2s ease-in-out infinite .4s; transform-origin:215px 55px;">
                <circle cx="215" cy="55" r="24" fill="#FFFBEB" stroke="#FCD34D" stroke-width="2"/>
                <circle cx="215" cy="55" r="18" fill="#FEF3C7" opacity="0.5"/>
                {{-- jarum jam --}}
                <line x1="215" y1="55" x2="215" y2="42" stroke="#16A34A" stroke-width="2.5" stroke-linecap="round"
                    style="transform-origin:215px 55px; animation:fhClockHour 6s linear infinite;"/>
                <line x1="215" y1="55" x2="225" y2="55" stroke="#22C55E" stroke-width="2" stroke-linecap="round"
                    style="transform-origin:215px 55px; animation:fhClockMin 3s linear infinite;"/>
                <circle cx="215" cy="55" r="2.5" fill="#15803D"/>
                {{-- tik-tik angka --}}
                <circle cx="215" cy="34" r="1.5" fill="#FCD34D"/>
                <circle cx="215" cy="76" r="1.5" fill="#FCD34D"/>
                <circle cx="194" cy="55" r="1.5" fill="#FCD34D"/>
                <circle cx="236" cy="55" r="1.5" fill="#FCD34D"/>
            </g>

            {{-- ── Kaca pembesar melayang kiri ── --}}
            <g style="animation:fhMagFloat 3.6s ease-in-out infinite; transform-origin:28px 65px;">
                <circle cx="28" cy="52" r="22" fill="#FFFBEB" stroke="#FCD34D" stroke-width="2.5"/>
                <circle cx="28" cy="52" r="15" fill="#FEF3C7" opacity="0.5"/>
                <circle cx="21" cy="45" r="5"  fill="white" opacity="0.55"/>
                {{-- garis di dalam kaca --}}
                <line x1="18" y1="52" x2="38" y2="52" stroke="#FCD34D" stroke-width="1.5" opacity="0.6"/>
                <line x1="28" y1="42" x2="28" y2="62" stroke="#FCD34D" stroke-width="1.5" opacity="0.6"/>
                {{-- gagang --}}
                <line x1="44" y1="68" x2="56" y2="82" stroke="#D97706" stroke-width="4" stroke-linecap="round"/>
                <line x1="44" y1="68" x2="56" y2="82" stroke="#FBBF24" stroke-width="2" stroke-linecap="round"/>
            </g>

            {{-- ── TUMPUKAN DOKUMEN (arsip) ── --}}

            {{-- Dokumen paling belakang --}}
            <g style="animation:fhDoc3 2.8s ease-in-out infinite .4s;">
                <rect x="72" y="82" width="130" height="158" rx="8"
                      fill="#FEF3C7" stroke="#FCD34D" stroke-width="1.5" opacity="0.6"/>
            </g>

            {{-- Dokumen tengah --}}
            <g style="animation:fhDoc2 2.8s ease-in-out infinite .2s;">
                <rect x="60" y="70" width="130" height="158" rx="8"
                      fill="#FFFBEB" stroke="#FCD34D" stroke-width="1.8"/>
                <rect x="74" y="90"  width="102" height="5" rx="2.5" fill="#FDE68A"/>
                <rect x="74" y="102" width="80"  height="5" rx="2.5" fill="#FDE68A"/>
                <rect x="74" y="114" width="90"  height="5" rx="2.5" fill="#FDE68A"/>
            </g>

            {{-- Dokumen depan (utama) --}}
            <g filter="url(#fhShadow)" style="animation:fhDoc1 2.8s ease-in-out infinite;">
                <rect x="48" y="58" width="130" height="160" rx="8"
                      fill="#ffffff" stroke="#FBBF24" stroke-width="2"/>

                {{-- Header dokumen --}}
                <rect x="48" y="58" width="130" height="28" rx="8" fill="#D97706"/>
                <rect x="48" y="72"  width="130" height="14" rx="0" fill="#D97706"/>
                <text x="113" y="77" text-anchor="middle" font-size="9" font-weight="700"
                      fill="white" letter-spacing="0.5">PROCUREMENT HISTORY</text>

                {{-- Baris data kosong --}}
                {{-- Row 1 --}}
                <g style="animation:fhRowIn .4s ease-out .5s both; opacity:0;">
                    <rect x="58" y="96"  width="14" height="14" rx="3" fill="#FEF3C7" stroke="#FCD34D" stroke-width="1"/>
                    <rect x="78" y="99"  width="55" height="5"  rx="2.5" fill="#FEF3C7"/>
                    <rect x="138" y="99" width="30" height="5"  rx="2.5" fill="#FEF3C7"/>
                </g>

                {{-- Row 2 --}}
                <g style="animation:fhRowIn .4s ease-out .65s both; opacity:0;">
                    <rect x="58" y="118" width="14" height="14" rx="3" fill="#FEF3C7" stroke="#FCD34D" stroke-width="1"/>
                    <rect x="78" y="121" width="45" height="5"  rx="2.5" fill="#FEF3C7"/>
                    <rect x="138" y="121" width="30" height="5" rx="2.5" fill="#FEF3C7"/>
                </g>

                {{-- Row 3 --}}
                <g style="animation:fhRowIn .4s ease-out .8s both; opacity:0;">
                    <rect x="58" y="140" width="14" height="14" rx="3" fill="#FEF3C7" stroke="#FCD34D" stroke-width="1"/>
                    <rect x="78" y="143" width="60" height="5"  rx="2.5" fill="#FEF3C7"/>
                    <rect x="138" y="143" width="30" height="5" rx="2.5" fill="#FEF3C7"/>
                </g>

                {{-- Divider --}}
                <line x1="58" y1="164" x2="168" y2="164"
                      stroke="#FDE68A" stroke-width="1.5" stroke-dasharray="4 3"/>

                {{-- Area kosong bergaris putus --}}
                <rect x="58" y="174" width="110" height="10" rx="3"
                      fill="#FFFBEB" stroke="#FDE68A" stroke-width="1" stroke-dasharray="4 2"/>
                <rect x="58" y="192" width="88"  height="10" rx="3"
                      fill="#FFFBEB" stroke="#FDE68A" stroke-width="1" stroke-dasharray="4 2"/>

                {{-- Ikon jam kecil di pojok kanan bawah --}}
                <circle cx="158" cy="206" r="9" fill="#FEF3C7"/>
                <line x1="158" y1="206" x2="158" y2="200" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round"
                    style="transform-origin:158px 206px; animation:fhClockMin 3s linear infinite;"/>
                <line x1="158" y1="206" x2="164" y2="206" stroke="#22C55E" stroke-width="1.5" stroke-linecap="round"
                    style="transform-origin:158px 206px; animation:fhClockHour 6s linear infinite;"/>
                <circle cx="158" cy="206" r="1.5" fill="#15803D"/>

                {{-- Teks "No Data" samar --}}
                <text x="113" y="220" text-anchor="middle" font-size="8" fill="#FDE68A"
                      font-weight="600" letter-spacing="1">KOSONG</text>
            </g>

            {{-- Bintang berputar --}}
            <g style="animation:fhStar1 4s ease-in-out infinite;">
                <polygon points="255,35 257,42 264,42 258,47 260,54 255,49 250,54 252,47 246,42 253,42"
                         fill="#FCD34D" opacity="0.7" transform="scale(0.75) translate(85,15)"/>
            </g>
            <g style="animation:fhStar2 3.5s ease-in-out infinite .9s;">
                <polygon points="25,195 27,201 33,201 28,205 30,211 25,207 20,211 22,205 17,201 23,201"
                         fill="#FBBF24" opacity="0.6" transform="scale(0.65)"/>
            </g>

            {{-- Titik sudut --}}
            <circle cx="-18" cy="85"  r="5" fill="#FCD34D" opacity="0.6" style="animation:fhDot1 2.3s ease-in-out infinite alternate;"/>
            <circle cx="265" cy="95"  r="4" fill="#F59E0B" opacity="0.55" style="animation:fhDot2 2.7s ease-in-out infinite alternate .5s;"/>
            <circle cx="-15" cy="190" r="4" fill="#FDE68A" opacity="0.6" style="animation:fhDot1 3s ease-in-out infinite alternate 1s;"/>
            <circle cx="268" cy="205" r="5" fill="#FCD34D" opacity="0.5" style="animation:fhDot2 2.5s ease-in-out infinite alternate 1.3s;"/>

        </svg>
    </div>

    {{-- Teks --}}
    <div style="margin-top:.8rem; animation:fhFadeUp .7s ease .3s both;">
        <h3 style="font-size:1.1rem; font-weight:800; color:#92400E; margin:0 0 .45rem; letter-spacing:-.015em;">
            🗂️ Riwayat Masih Kosong
        </h3>
        <p style="font-size:.83rem; color:#78716c; line-height:1.75; margin:0;">
            Belum ada riwayat procurement yang bisa ditampilkan.<br>
            Data akan muncul setelah ada yang di-approve,<br>completed, atau rejected.
        </p>
    </div>

    {{-- Badge --}}
    <div style="margin-top:.9rem; display:inline-flex; align-items:center; gap:.4rem;
                padding:.45rem 1.15rem; background:#FFFBEB; border:1.5px solid #FCD34D;
                border-radius:9999px; font-size:.76rem; font-weight:600; color:#92400E;
                box-shadow:0 2px 10px rgba(245,158,11,.15);
                animation:fhFadeUp .7s ease .45s both;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16A34A"
             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
        </svg>
        Menunggu riwayat procurement
    </div>

</div>

<style>
@keyframes fhEntrance {
    from { opacity:0; transform:scale(.65) translateY(18px); }
    to   { opacity:1; transform:scale(1)   translateY(0); }
}
@keyframes fhFadeUp {
    from { opacity:0; transform:translateY(12px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes fhFloat1 {
    0%,100% { transform:translate(0,0); }
    33%      { transform:translate(-7px,-11px); }
    66%      { transform:translate(5px,-6px); }
}
@keyframes fhFloat2 {
    0%,100% { transform:translate(0,0); }
    33%      { transform:translate(7px,-9px); }
    66%      { transform:translate(-5px,-7px); }
}
@keyframes fhGlowPulse {
    0%,100% { transform:scale(.9); opacity:.6; }
    50%      { transform:scale(1.1); opacity:1; }
}
@keyframes fhDoc1 {
    0%,100% { transform:translateY(0) rotate(-.5deg); }
    50%      { transform:translateY(-8px) rotate(.5deg); }
}
@keyframes fhDoc2 {
    0%,100% { transform:translateY(0) rotate(-1.5deg); }
    50%      { transform:translateY(-5px) rotate(1deg); }
}
@keyframes fhDoc3 {
    0%,100% { transform:translateY(0) rotate(2deg); }
    50%      { transform:translateY(-3px) rotate(-1.5deg); }
}
@keyframes fhClockHour {
    from { transform:rotate(0deg); }
    to   { transform:rotate(360deg); }
}
@keyframes fhClockMin {
    from { transform:rotate(0deg); }
    to   { transform:rotate(360deg); }
}
@keyframes fhMagFloat {
    0%,100% { transform:translate(0,0) rotate(8deg); }
    50%      { transform:translate(6px,-9px) rotate(-5deg); }
}
@keyframes fhClockFloat {
    0%,100% { transform:translate(0,0) rotate(-5deg); }
    50%      { transform:translate(-5px,-10px) rotate(5deg); }
}
@keyframes fhRowIn {
    from { opacity:0; transform:translateX(-8px); }
    to   { opacity:1; transform:translateX(0); }
}
@keyframes fhStar1 {
    0%,100% { transform:rotate(0deg)   scale(1);   opacity:.7; }
    50%      { transform:rotate(180deg) scale(1.2); opacity:1; }
}
@keyframes fhStar2 {
    0%,100% { transform:rotate(0deg)    scale(1);   opacity:.6; }
    50%      { transform:rotate(-180deg) scale(1.15); opacity:1; }
}
@keyframes fhDot1 {
    from { opacity:.3; transform:translateY(0); }
    to   { opacity:.9; transform:translateY(-6px); }
}
@keyframes fhDot2 {
    from { opacity:.3; transform:translateX(0); }
    to   { opacity:.9; transform:translateX(6px); }
}
</style>