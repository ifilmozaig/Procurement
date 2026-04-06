{{--
    resources/views/filament/empty-states/hrga-empty.blade.php
    Tema: Clipboard detektif — HRGA mencatat & memantau
    Warna: Amber/Kuning
--}}
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:2rem 1rem 2.5rem; text-align:center; position:relative;">

    {{-- Partikel melayang latar --}}
    <div style="position:absolute;inset:0;pointer-events:none;overflow:visible;">
        <div style="position:absolute;top:12%;left:6%;width:7px;height:7px;background:#FCD34D;border-radius:50%;animation:cbFloat1 3s ease-in-out infinite;"></div>
        <div style="position:absolute;top:18%;right:8%;width:5px;height:5px;background:#F59E0B;border-radius:50%;animation:cbFloat2 2.8s ease-in-out infinite .5s;"></div>
        <div style="position:absolute;top:58%;left:4%;width:6px;height:6px;background:#FDE68A;border-radius:50%;animation:cbFloat1 3.4s ease-in-out infinite 1s;"></div>
        <div style="position:absolute;top:62%;right:5%;width:5px;height:5px;background:#FCD34D;border-radius:50%;animation:cbFloat2 3.1s ease-in-out infinite .3s;"></div>
        <div style="position:absolute;top:35%;left:2%;width:4px;height:4px;background:#F59E0B;border-radius:50%;animation:cbFloat1 2.6s ease-in-out infinite .8s;"></div>
        <div style="position:absolute;top:42%;right:3%;width:4px;height:4px;background:#FBBF24;border-radius:50%;animation:cbFloat2 2.9s ease-in-out infinite 1.4s;"></div>
    </div>

    {{-- SVG Utama --}}
    <div style="animation:cbEntrance .8s cubic-bezier(.34,1.56,.64,1) both;">
        <svg viewBox="-30 -30 310 320" xmlns="http://www.w3.org/2000/svg"
             style="width:210px;height:auto;overflow:visible;">

            <defs>
                <radialGradient id="cbGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#F59E0B" stop-opacity="0.2"/>
                    <stop offset="100%" stop-color="#F59E0B" stop-opacity="0"/>
                </radialGradient>
                <filter id="cbShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="3" dy="5" stdDeviation="4" flood-color="#D97706" flood-opacity="0.2"/>
                </filter>
                <filter id="cbGlowF">
                    <feGaussianBlur stdDeviation="4"/>
                </filter>
            </defs>

            {{-- Glow background --}}
            <circle cx="125" cy="145" r="110" fill="url(#cbGlow)"
                style="animation:cbGlowPulse 2.5s ease-in-out infinite;"/>

            {{-- ── Pensil melayang kanan ── --}}
            <g style="animation:cbPencilFloat 3s ease-in-out infinite; transform-origin:220px 60px;">
                {{-- badan pensil --}}
                <rect x="205" y="20" width="14" height="80" rx="3" fill="#FDE68A" stroke="#F59E0B" stroke-width="1.5"/>
                {{-- ujung pensil --}}
                <polygon points="205,100 219,100 212,118" fill="#FCA5A5" stroke="#F87171" stroke-width="1"/>
                {{-- tip pensil --}}
                <polygon points="209,112 215,112 212,118" fill="#1C1917"/>
                {{-- karet --}}
                <rect x="205" y="20" width="14" height="12" rx="3" fill="#FDA4AF" stroke="#F43F5E" stroke-width="1.5"/>
                {{-- garis dekorasi --}}
                <line x1="208" y1="35" x2="216" y2="35" stroke="#F59E0B" stroke-width="1.5" opacity="0.6"/>
                <line x1="208" y1="40" x2="216" y2="40" stroke="#F59E0B" stroke-width="1.5" opacity="0.6"/>
            </g>

            {{-- ── Kaca pembesar kecil melayang kiri ── --}}
            <g style="animation:cbMagFloat 3.5s ease-in-out infinite .5s; transform-origin:30px 70px;">
                {{-- lingkaran kaca --}}
                <circle cx="30" cy="55" r="22" fill="#FFFBEB" stroke="#F59E0B" stroke-width="2.5"/>
                <circle cx="30" cy="55" r="16" fill="#FEF3C7" opacity="0.6"/>
                {{-- kilap kaca --}}
                <circle cx="23" cy="48" r="5" fill="white" opacity="0.6"/>
                {{-- gagang --}}
                <line x1="46" y1="71" x2="58" y2="85" stroke="#D97706" stroke-width="4" stroke-linecap="round"/>
                <line x1="46" y1="71" x2="58" y2="85" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
            </g>

            {{-- ── CLIPBOARD UTAMA ── --}}
            <g style="animation:cbMainFloat 2.8s ease-in-out infinite;" filter="url(#cbShadow)">

                {{-- Papan clipboard --}}
                <rect x="45" y="55" width="160" height="200" rx="10" fill="#FFFBEB" stroke="#F59E0B" stroke-width="2.5"/>

                {{-- Clip penjepit atas --}}
                <rect x="95" y="42" width="60" height="22" rx="8" fill="#F59E0B"/>
                <rect x="103" y="46" width="44" height="14" rx="5" fill="#FDE68A"/>
                <rect x="108" y="49" width="34" height="8"  rx="3" fill="#FFFBEB"/>

                {{-- ── Baris teks & checkbox ── --}}

                {{-- Baris 1 — DONE ✓ --}}
                <g style="animation:cbRow1 .5s ease-out .6s both; opacity:0;">
                    <rect x="62" y="88" width="14" height="14" rx="3" fill="#D1FAE5" stroke="#34D399" stroke-width="1.5"/>
                    <path d="M65 95 L68 98 L74 91" stroke="#059669" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="84" y="91" width="80" height="5" rx="2.5" fill="#D1FAE5"/>
                    <rect x="84" y="91" width="80" height="5" rx="2.5" fill="none" stroke="#6EE7B7" stroke-width=".5"/>
                    {{-- coret --}}
                    <line x1="84" y1="93.5" x2="164" y2="93.5" stroke="#34D399" stroke-width="1.5" opacity="0.6"/>
                </g>

                {{-- Baris 2 — DONE ✓ --}}
                <g style="animation:cbRow1 .5s ease-out .75s both; opacity:0;">
                    <rect x="62" y="110" width="14" height="14" rx="3" fill="#D1FAE5" stroke="#34D399" stroke-width="1.5"/>
                    <path d="M65 117 L68 120 L74 113" stroke="#059669" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="84" y="113" width="65" height="5" rx="2.5" fill="#D1FAE5"/>
                    <line x1="84" y1="115.5" x2="149" y2="115.5" stroke="#34D399" stroke-width="1.5" opacity="0.6"/>
                </g>

                {{-- Baris 3 — DONE ✓ --}}
                <g style="animation:cbRow1 .5s ease-out .9s both; opacity:0;">
                    <rect x="62" y="132" width="14" height="14" rx="3" fill="#D1FAE5" stroke="#34D399" stroke-width="1.5"/>
                    <path d="M65 139 L68 142 L74 135" stroke="#059669" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="84" y="135" width="72" height="5" rx="2.5" fill="#D1FAE5"/>
                    <line x1="84" y1="137.5" x2="156" y2="137.5" stroke="#34D399" stroke-width="1.5" opacity="0.6"/>
                </g>

                {{-- Divider --}}
                <line x1="62" y1="158" x2="185" y2="158" stroke="#FDE68A" stroke-width="1.5" stroke-dasharray="4 3"/>

                {{-- Baris 4 — EMPTY (belum ada data) --}}
                <g style="animation:cbRow1 .5s ease-out 1.05s both; opacity:0;">
                    <rect x="62" y="168" width="14" height="14" rx="3" fill="#FEF9C3" stroke="#FDE047" stroke-width="1.5" stroke-dasharray="3 2"/>
                    <rect x="84" y="171" width="88" height="5" rx="2.5" fill="#FEF9C3" stroke="#FDE047" stroke-width=".5" stroke-dasharray="3 2"/>
                </g>

                {{-- Baris 5 — EMPTY --}}
                <g style="animation:cbRow1 .5s ease-out 1.2s both; opacity:0;">
                    <rect x="62" y="190" width="14" height="14" rx="3" fill="#FEF9C3" stroke="#FDE047" stroke-width="1.5" stroke-dasharray="3 2"/>
                    <rect x="84" y="193" width="70" height="5" rx="2.5" fill="#FEF9C3" stroke="#FDE047" stroke-width=".5" stroke-dasharray="3 2"/>
                </g>

                {{-- Kursor menulis berkedip --}}
                <rect x="84" y="215" width="2.5" height="14" rx="1" fill="#F59E0B"
                    style="animation:cbCursor 1s ease-in-out infinite;"/>

                {{-- Tanda tanya besar di bawah --}}
                <text x="125" y="238" text-anchor="middle" font-size="22" font-weight="800"
                      fill="#F59E0B" opacity="0.25">???</text>

            </g>

            {{-- ── Bintang berputar ── --}}
            <g style="animation:cbStar1 4s ease-in-out infinite;">
                <polygon points="250,40 253,50 263,50 255,56 258,66 250,60 242,66 245,56 237,50 247,50"
                         fill="#FCD34D" opacity="0.7" transform="scale(0.7) translate(100,20)"/>
            </g>
            <g style="animation:cbStar2 3.5s ease-in-out infinite .8s;">
                <polygon points="30,185 32,191 38,191 33,195 35,201 30,197 25,201 27,195 22,191 28,191"
                         fill="#FBBF24" opacity="0.6" transform="scale(0.65)"/>
            </g>

            {{-- titik dekor sudut --}}
            <circle cx="-18" cy="80"  r="5" fill="#FCD34D" opacity="0.6" style="animation:cbDot1 2.3s ease-in-out infinite alternate;"/>
            <circle cx="265" cy="95"  r="4" fill="#F59E0B" opacity="0.55" style="animation:cbDot2 2.7s ease-in-out infinite alternate .5s;"/>
            <circle cx="-15" cy="195" r="4" fill="#FDE68A" opacity="0.6" style="animation:cbDot1 3s ease-in-out infinite alternate 1s;"/>
            <circle cx="268" cy="210" r="5" fill="#FCD34D" opacity="0.5" style="animation:cbDot2 2.5s ease-in-out infinite alternate 1.3s;"/>

        </svg>
    </div>

    {{-- Teks --}}
    <div style="margin-top:.8rem; animation:cbFadeUp .7s ease .3s both;">
        <h3 style="font-size:1.1rem; font-weight:800; color:#92400E; margin:0 0 .45rem; letter-spacing:-.015em;">
            📋 Belum Ada requester
        </h3>
        <p style="font-size:.83rem; color:#78716c; line-height:1.75; margin:0;">
            Belum ada data procurement yang bisa dipantau.<br>
            Data akan muncul setelah ada yang di-approve,<br>completed, atau rejected.
        </p>
    </div>

    {{-- Badge --}}
    <div style="margin-top:.9rem; display:inline-flex; align-items:center; gap:.4rem;
                padding:.45rem 1.15rem; background:#FFFBEB; border:1.5px solid #FCD34D;
                border-radius:9999px; font-size:.76rem; font-weight:600; color:#92400E;
                box-shadow:0 2px 10px rgba(245,158,11,.18);
                animation:cbFadeUp .7s ease .45s both;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/>
            <path d="M9 12h6M9 16h4"/>
        </svg>
        Menunggu data procurement
    </div>

</div>

<style>
@keyframes cbEntrance {
    from { opacity:0; transform:scale(.65) translateY(18px); }
    to   { opacity:1; transform:scale(1)   translateY(0); }
}
@keyframes cbFadeUp {
    from { opacity:0; transform:translateY(12px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes cbFloat1 {
    0%,100% { transform:translate(0,0); opacity:.7; }
    33%      { transform:translate(-7px,-11px); }
    66%      { transform:translate(5px,-6px); }
}
@keyframes cbFloat2 {
    0%,100% { transform:translate(0,0); opacity:.6; }
    33%      { transform:translate(7px,-9px); }
    66%      { transform:translate(-5px,-7px); }
}
@keyframes cbGlowPulse {
    0%,100% { transform:scale(.9); opacity:.6; }
    50%      { transform:scale(1.1); opacity:1; }
}
@keyframes cbMainFloat {
    0%,100% { transform:translateY(0) rotate(-.5deg); }
    50%      { transform:translateY(-8px) rotate(.5deg); }
}
@keyframes cbPencilFloat {
    0%,100% { transform:translate(0,0) rotate(-5deg); }
    50%      { transform:translate(-5px,-10px) rotate(5deg); }
}
@keyframes cbMagFloat {
    0%,100% { transform:translate(0,0) rotate(8deg); }
    50%      { transform:translate(6px,-9px) rotate(-5deg); }
}
@keyframes cbRow1 {
    from { opacity:0; transform:translateX(-10px); }
    to   { opacity:1; transform:translateX(0); }
}

@keyframes cbCursor {
    0%,100% { opacity:1; }
    50%      { opacity:0; }
}
@keyframes cbStar1 {
    0%,100% { transform:rotate(0deg)   scale(1);    opacity:.7; }
    50%      { transform:rotate(180deg) scale(1.2);  opacity:1; }
}
@keyframes cbStar2 {
    0%,100% { transform:rotate(0deg)    scale(1);   opacity:.6; }
    50%      { transform:rotate(-180deg) scale(1.15); opacity:1; }
}
@keyframes cbDot1 {
    from { opacity:.3; transform:translateY(0); }
    to   { opacity:.9; transform:translateY(-6px); }
}
@keyframes cbDot2 {
    from { opacity:.3; transform:translateX(0); }
    to   { opacity:.9; transform:translateX(6px); }
}
</style>