{{--
    resources/views/filament/empty-states/manager-empty.blade.php
    Digunakan di: ManagerProcurementTableWidget
    Tema: Briefcase/Koper — Manager siap untuk approval
    Warna: Amber dominan + aksen hijau
--}}
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:2rem 1rem 2.5rem; text-align:center; position:relative;">

    {{-- Partikel melayang --}}
    <div style="position:absolute;inset:0;pointer-events:none;overflow:visible;">
        <div style="position:absolute;top:10%;left:6%;width:6px;height:6px;background:#FCD34D;border-radius:50%;animation:mgFloat1 3s ease-in-out infinite;"></div>
        <div style="position:absolute;top:18%;right:8%;width:5px;height:5px;background:#F59E0B;border-radius:50%;animation:mgFloat2 2.8s ease-in-out infinite .5s;"></div>
        <div style="position:absolute;top:55%;left:4%;width:5px;height:5px;background:#FDE68A;border-radius:50%;animation:mgFloat1 3.4s ease-in-out infinite 1s;"></div>
        <div style="position:absolute;top:60%;right:5%;width:6px;height:6px;background:#FCD34D;border-radius:50%;animation:mgFloat2 3.1s ease-in-out infinite .3s;"></div>
        <div style="position:absolute;top:35%;left:2%;width:4px;height:4px;background:#F59E0B;border-radius:50%;animation:mgFloat1 2.6s ease-in-out infinite .8s;"></div>
        <div style="position:absolute;top:42%;right:3%;width:4px;height:4px;background:#FDE68A;border-radius:50%;animation:mgFloat2 2.9s ease-in-out infinite 1.4s;"></div>
    </div>

    {{-- SVG Utama --}}
    <div style="animation:mgEntrance .8s cubic-bezier(.34,1.56,.64,1) both;">
        <svg viewBox="-30 -30 310 290" xmlns="http://www.w3.org/2000/svg"
             style="width:210px;height:auto;overflow:visible;">

            <defs>
                <radialGradient id="mgGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#F59E0B" stop-opacity="0.2"/>
                    <stop offset="100%" stop-color="#F59E0B" stop-opacity="0"/>
                </radialGradient>
                <filter id="mgShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="3" dy="6" stdDeviation="6" flood-color="#D97706" flood-opacity="0.2"/>
                </filter>
            </defs>

            {{-- Glow latar --}}
            <circle cx="120" cy="130" r="108" fill="url(#mgGlow)"
                style="animation:mgGlowPulse 2.6s ease-in-out infinite;"/>

            {{-- Lingkaran latar --}}
            <circle cx="120" cy="130" r="100" fill="#FFFBEB"
                style="transform-origin:120px 130px; animation:mgRing1 3s ease-in-out infinite;"/>
            <circle cx="120" cy="130" r="78" fill="#FEF3C7" opacity="0.7"
                style="transform-origin:120px 130px; animation:mgRing2 3s ease-in-out infinite .4s;"/>

            {{-- ── Dokumen melayang kiri ── --}}
            <g style="animation:mgFloatDocL 3.4s ease-in-out infinite; transform-origin:18px 62px;">
                <rect x="-4" y="32" width="44" height="56" rx="5" fill="#fff" stroke="#FCD34D" stroke-width="1.8"/>
                <rect x="5"  y="44" width="26" height="3" rx="1.5" fill="#FDE68A"/>
                <rect x="5"  y="52" width="20" height="3" rx="1.5" fill="#FDE68A"/>
                <rect x="5"  y="60" width="24" height="3" rx="1.5" fill="#FDE68A"/>
                {{-- stamp CAPEX amber --}}
                <rect x="5" y="70" width="32" height="12" rx="3" fill="#FEF3C7" stroke="#F59E0B" stroke-width="1"/>
                <text x="21" y="80" text-anchor="middle" font-size="6.5" font-weight="800"
                      fill="#D97706" letter-spacing=".5">CAPEX</text>
            </g>

            {{-- ── Dokumen melayang kanan ── --}}
            <g style="animation:mgFloatDocR 3.8s ease-in-out infinite .7s; transform-origin:222px 65px;">
                <rect x="200" y="35" width="44" height="56" rx="5" fill="#fff" stroke="#FCD34D" stroke-width="1.8"/>
                <rect x="208" y="47" width="28" height="3" rx="1.5" fill="#FDE68A"/>
                <rect x="208" y="55" width="22" height="3" rx="1.5" fill="#FDE68A"/>
                <rect x="208" y="63" width="26" height="3" rx="1.5" fill="#FDE68A"/>
                {{-- stamp CASH ADV amber --}}
                <rect x="208" y="73" width="32" height="12" rx="3" fill="#FEF3C7" stroke="#F59E0B" stroke-width="1"/>
                <text x="224" y="83" text-anchor="middle" font-size="5.5" font-weight="800"
                      fill="#D97706" letter-spacing=".3">CASH ADV</text>
            </g>

            {{-- ── BRIEFCASE UTAMA ── --}}
            <g filter="url(#mgShadow)" style="animation:mgBriefcaseFloat 2.8s ease-in-out infinite;">

                {{-- Bayangan --}}
                <ellipse cx="120" cy="248" rx="58" ry="10" fill="#D97706" opacity="0.1"/>

                {{-- Handle/pegangan tas --}}
                <path d="M96 82 Q96 68 120 68 Q144 68 144 82"
                      fill="none" stroke="#D97706" stroke-width="7" stroke-linecap="round"/>
                <path d="M96 82 Q96 68 120 68 Q144 68 144 82"
                      fill="none" stroke="#FBBF24" stroke-width="4" stroke-linecap="round"/>

                {{-- Badan briefcase --}}
                <rect x="52" y="82" width="136" height="110" rx="12" fill="#F59E0B"/>
                <rect x="52" y="82" width="136" height="110" rx="12"
                      fill="none" stroke="#D97706" stroke-width="2"/>

                {{-- Gradasi badan atas --}}
                <rect x="52" y="82" width="136" height="55" rx="12" fill="#FBBF24" opacity="0.45"/>

                {{-- Garis tengah / seam --}}
                <line x1="52" y1="137" x2="188" y2="137" stroke="#D97706" stroke-width="2.5"/>

                {{-- Kunci tengah — HIJAU --}}
                <rect x="104" y="125" width="32" height="24" rx="6" fill="#15803D"/>
                <rect x="108" y="129" width="24" height="16" rx="4" fill="#166534"/>
                {{-- lubang kunci --}}
                <circle cx="120" cy="135" r="4" fill="#22C55E"/>
                <rect x="118" y="135" width="4" height="6" rx="1" fill="#22C55E"/>

                {{-- Engsel kiri & kanan — hijau gelap --}}
                <rect x="60" y="133" width="16" height="8" rx="3" fill="#15803D"/>
                <rect x="164" y="133" width="16" height="8" rx="3" fill="#15803D"/>

                {{-- Dekorasi jahitan --}}
                <rect x="62" y="92"  width="116" height="4" rx="2" fill="#FDE68A" opacity="0.5"/>
                <rect x="62" y="148" width="116" height="4" rx="2" fill="#FDE68A" opacity="0.5"/>

                {{-- Tanda tanya di badan tas --}}
                <circle cx="120" cy="108" r="18" fill="#D97706" opacity="0.45"/>
                <text x="120" y="115" text-anchor="middle" font-size="22" font-weight="900"
                      fill="white" opacity="0.95">?</text>

                {{-- Label nama --}}
                <rect x="80" y="162" width="80" height="20" rx="5" fill="#D97706" opacity="0.5"/>
                <text x="120" y="176" text-anchor="middle" font-size="8" font-weight="700"
                      fill="#FFFBEB" letter-spacing="1">MANAGER</text>

            </g>

            {{-- ── Koin centang HIJAU melayang ── --}}
            <g style="animation:mgCoinFloat 2.4s ease-in-out infinite .3s; transform-origin:185px 82px;">
                <circle cx="185" cy="82" r="18" fill="#DCFCE7" stroke="#86EFAC" stroke-width="2"/>
                <circle cx="185" cy="82" r="13" fill="#BBF7D0"/>
                <text x="185" y="87" text-anchor="middle" font-size="13" font-weight="800" fill="#16A34A">✓</text>
            </g>

            {{-- ── Jam melayang kiri atas — jarum hijau ── --}}
            <g style="animation:mgClockFloat 3.2s ease-in-out infinite .8s; transform-origin:55px 68px;">
                <circle cx="55" cy="68" r="20" fill="#FFFBEB" stroke="#FCD34D" stroke-width="2"/>
                <circle cx="55" cy="68" r="14" fill="#FEF3C7" opacity="0.5"/>
                {{-- jarum hijau --}}
                <line x1="55" y1="68" x2="55" y2="57" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round"
                    style="transform-origin:55px 68px; animation:mgClockHour 6s linear infinite;"/>
                <line x1="55" y1="68" x2="64" y2="68" stroke="#22C55E" stroke-width="1.8" stroke-linecap="round"
                    style="transform-origin:55px 68px; animation:mgClockMin 3s linear infinite;"/>
                <circle cx="55" cy="68" r="2" fill="#15803D"/>
                {{-- titik jam --}}
                <circle cx="55" cy="50" r="1.2" fill="#FCD34D"/>
                <circle cx="55" cy="86" r="1.2" fill="#FCD34D"/>
                <circle cx="37" cy="68" r="1.2" fill="#FCD34D"/>
                <circle cx="73" cy="68" r="1.2" fill="#FCD34D"/>
            </g>

            {{-- Bintang berputar --}}
            <g style="animation:mgStar1 4s ease-in-out infinite;">
                <polygon points="262,38 264,46 272,46 266,51 268,59 262,54 256,59 258,51 252,46 260,46"
                         fill="#FCD34D" opacity="0.7" transform="scale(0.7) translate(100,10)"/>
            </g>
            <g style="animation:mgStar2 3.5s ease-in-out infinite .9s;">
                <polygon points="20,195 22,202 29,202 24,207 26,214 20,209 14,214 16,207 11,202 18,202"
                         fill="#FBBF24" opacity="0.55" transform="scale(0.6)"/>
            </g>

            {{-- Titik sudut --}}
            <circle cx="-18" cy="88"  r="5" fill="#FCD34D" opacity="0.6" style="animation:mgDot1 2.3s ease-in-out infinite alternate;"/>
            <circle cx="265" cy="98"  r="4" fill="#F59E0B" opacity="0.55" style="animation:mgDot2 2.7s ease-in-out infinite alternate .5s;"/>
            <circle cx="-15" cy="195" r="4" fill="#FDE68A" opacity="0.6" style="animation:mgDot1 3s ease-in-out infinite alternate 1s;"/>
            <circle cx="268" cy="210" r="5" fill="#FCD34D" opacity="0.5" style="animation:mgDot2 2.5s ease-in-out infinite alternate 1.3s;"/>

        </svg>
    </div>

    {{-- Teks --}}
    <div style="margin-top:.8rem; animation:mgFadeUp .7s ease .3s both;">
        <h3 style="font-size:1.1rem; font-weight:800; color:#92400E; margin:0 0 .45rem; letter-spacing:-.015em;">
            💼 Belum Ada yang Perlu Disetujui
        </h3>
        <p style="font-size:.83rem; color:#78716c; line-height:1.75; margin:0;">
            Belum ada procurement CAPEX atau Cash Advance<br>
            yang perlu ditinjau atau disetujui saat ini.
        </p>
    </div>

    {{-- Badge --}}
    <div style="margin-top:.9rem; display:inline-flex; align-items:center; gap:.4rem;
                padding:.45rem 1.15rem; background:#FFFBEB; border:1.5px solid #FCD34D;
                border-radius:9999px; font-size:.76rem; font-weight:600; color:#92400E;
                box-shadow:0 2px 10px rgba(245,158,11,.15);
                animation:mgFadeUp .7s ease .45s both;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16A34A"
             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Tidak ada procurement untuk ditinjau
    </div>

</div>

<style>
@keyframes mgEntrance {
    from { opacity:0; transform:scale(.65) translateY(18px); }
    to   { opacity:1; transform:scale(1)   translateY(0); }
}
@keyframes mgFadeUp {
    from { opacity:0; transform:translateY(12px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes mgFloat1 {
    0%,100% { transform:translate(0,0); }
    33%      { transform:translate(-7px,-11px); }
    66%      { transform:translate(5px,-6px); }
}
@keyframes mgFloat2 {
    0%,100% { transform:translate(0,0); }
    33%      { transform:translate(7px,-9px); }
    66%      { transform:translate(-5px,-7px); }
}
@keyframes mgGlowPulse {
    0%,100% { transform:scale(.9); opacity:.6; }
    50%      { transform:scale(1.1); opacity:1; }
}
@keyframes mgRing1 {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.04); }
}
@keyframes mgRing2 {
    0%,100% { transform:scale(1);    opacity:.7; }
    50%      { transform:scale(1.06); opacity:.5; }
}
@keyframes mgBriefcaseFloat {
    0%,100% { transform:translateY(0) rotate(-.5deg); }
    50%      { transform:translateY(-10px) rotate(.5deg); }
}
@keyframes mgFloatDocL {
    0%,100% { transform:translate(0,0)       rotate(-3deg); }
    50%      { transform:translate(-6px,-10px) rotate(3deg); }
}
@keyframes mgFloatDocR {
    0%,100% { transform:translate(0,0)      rotate(3deg); }
    50%      { transform:translate(6px,-8px) rotate(-3deg); }
}
@keyframes mgCoinFloat {
    0%,100% { transform:translateY(0)    rotate(0deg); }
    50%      { transform:translateY(-8px) rotate(10deg); }
}
@keyframes mgClockFloat {
    0%,100% { transform:translate(0,0)      rotate(-5deg); }
    50%      { transform:translate(5px,-8px) rotate(5deg); }
}
@keyframes mgClockHour {
    from { transform:rotate(0deg); }
    to   { transform:rotate(360deg); }
}
@keyframes mgClockMin {
    from { transform:rotate(0deg); }
    to   { transform:rotate(360deg); }
}
@keyframes mgStar1 {
    0%,100% { transform:rotate(0deg)   scale(1);   opacity:.7; }
    50%      { transform:rotate(180deg) scale(1.2); opacity:1; }
}
@keyframes mgStar2 {
    0%,100% { transform:rotate(0deg)    scale(1);   opacity:.55; }
    50%      { transform:rotate(-180deg) scale(1.15); opacity:1; }
}
@keyframes mgDot1 {
    from { opacity:.3; transform:translateY(0); }
    to   { opacity:.9; transform:translateY(-6px); }
}
@keyframes mgDot2 {
    from { opacity:.3; transform:translateX(0); }
    to   { opacity:.9; transform:translateX(6px); }
}
</style>