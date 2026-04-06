<div style="display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:2.5rem 1rem 3rem; text-align:center; position:relative;">

    <div style="position:absolute;inset:0;pointer-events:none;overflow:visible;">
        <div style="position:absolute;top:8%;left:5%;width:7px;height:7px;background:#FCD34D;border-radius:50%;animation:pmPart1 3.2s ease-in-out infinite;"></div>
        <div style="position:absolute;top:14%;right:7%;width:5px;height:5px;background:#FB923C;border-radius:50%;animation:pmPart2 2.9s ease-in-out infinite .5s;"></div>
        <div style="position:absolute;top:55%;left:3%;width:6px;height:6px;background:#FDE68A;border-radius:50%;animation:pmPart1 3.5s ease-in-out infinite 1s;"></div>
        <div style="position:absolute;top:60%;right:4%;width:5px;height:5px;background:#FCD34D;border-radius:50%;animation:pmPart2 3.1s ease-in-out infinite .3s;"></div>
        <div style="position:absolute;top:32%;left:1%;width:4px;height:4px;background:#F97316;border-radius:50%;animation:pmPart1 2.7s ease-in-out infinite .8s;"></div>
        <div style="position:absolute;top:40%;right:2%;width:4px;height:4px;background:#FDBA74;border-radius:50%;animation:pmPart2 3s ease-in-out infinite 1.4s;"></div>
    </div>

    <div style="animation:pmEntrance .8s cubic-bezier(.34,1.56,.64,1) both; position:relative;">
        <svg viewBox="-40 -40 400 380" xmlns="http://www.w3.org/2000/svg"
             style="width:260px;height:auto;overflow:visible;">

            <defs>
                <radialGradient id="pmGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#F97316" stop-opacity="0.18"/>
                    <stop offset="100%" stop-color="#F97316" stop-opacity="0"/>
                </radialGradient>
                <filter id="pmShadow">
                    <feDropShadow dx="2" dy="6" stdDeviation="6" flood-color="#EA580C" flood-opacity="0.18"/>
                </filter>
                <filter id="pmShadowSoft">
                    <feDropShadow dx="1" dy="3" stdDeviation="4" flood-color="#000" flood-opacity="0.08"/>
                </filter>
            </defs>

            <circle cx="160" cy="170" r="125" fill="url(#pmGlow)"
                style="animation:pmGlowPulse 2.8s ease-in-out infinite;"/>

            <circle cx="160" cy="165" r="118" fill="#FFF7ED"
                style="transform-origin:160px 165px; animation:pmRing1 3.2s ease-in-out infinite;"/>
            <circle cx="160" cy="165" r="92" fill="#FFEDD5" opacity="0.6"
                style="transform-origin:160px 165px; animation:pmRing2 3.2s ease-in-out infinite .5s;"/>

            <circle cx="160" cy="165" r="108" fill="none" stroke="#FED7AA" stroke-width="1"
                    stroke-dasharray="6 8" opacity="0.5"
                    style="transform-origin:160px 165px; animation:pmOrbit 18s linear infinite;"/>

            <g style="animation:pmPlaneOrbit 5s linear infinite; transform-origin:160px 165px;">
                <g transform="translate(160, 57)">
                    <g style="animation:pmPlaneTilt 5s linear infinite;">
                        <polygon points="0,0 -18,10 0,6" fill="#EA580C" opacity="0.95"/>
                        <polygon points="0,0 18,10 0,6" fill="#C2410C" opacity="0.95"/>
                        <polygon points="0,-14 -6,10 0,6 6,10" fill="#F97316"/>
                        <line x1="0" y1="-14" x2="0" y2="6" stroke="#FFF7ED" stroke-width="1" opacity="0.7"/>
                        <polygon points="0,-14 3,0 0,-2" fill="white" opacity="0.3"/>
                    </g>
                </g>
            </g>

            <g style="animation:pmTrailOrbit 5s linear infinite; transform-origin:160px 165px; opacity:0.5;">
                <g transform="translate(160, 57)">
                    <line x1="0" y1="8" x2="0" y2="22" stroke="#FED7AA" stroke-width="1.5"
                          stroke-dasharray="3 3" stroke-linecap="round"/>
                </g>
            </g>

            <g style="animation:pmPlaneOrbit2 5s linear infinite; transform-origin:160px 165px;">
                <g transform="translate(160, 57)">
                    <g style="animation:pmPlaneTilt2 5s linear infinite;" opacity="0.35">
                        <polygon points="0,0 -14,8 0,5"  fill="#EA580C"/>
                        <polygon points="0,0 14,8 0,5"   fill="#C2410C"/>
                        <polygon points="0,-10 -5,8 0,5 5,8" fill="#F97316"/>
                        <line x1="0" y1="-10" x2="0" y2="5" stroke="#FFF7ED" stroke-width="1" opacity="0.6"/>
                    </g>
                </g>
            </g>

            <g filter="url(#pmShadow)" style="animation:pmPandaBob 2.6s ease-in-out infinite;">

                <ellipse cx="160" cy="298" rx="52" ry="10" fill="#1C1917" opacity="0.08"/>

                <ellipse cx="210" cy="258" rx="28" ry="12" fill="#EA580C" opacity="0.7"
                    transform="rotate(-20,210,258)"/>
                <ellipse cx="205" cy="262" rx="19" ry="8" fill="#FEF9C3" opacity="0.65"
                    transform="rotate(-20,205,262)"/>

                <ellipse cx="160" cy="240" rx="52" ry="46" fill="#EA580C"/>

                <ellipse cx="160" cy="250" rx="34" ry="30" fill="#FEF9C3"/>

                <ellipse cx="126" cy="278" rx="20" ry="13" fill="#C2410C" transform="rotate(15,126,278)"/>
                <ellipse cx="123" cy="283" rx="15" ry="9"  fill="#1C1917" transform="rotate(15,123,283)"/>
                <circle cx="114" cy="286" r="3.5" fill="#292524"/>
                <circle cx="122" cy="289" r="3.5" fill="#292524"/>
                <circle cx="130" cy="287" r="3.5" fill="#292524"/>

                <ellipse cx="194" cy="278" rx="20" ry="13" fill="#C2410C" transform="rotate(-15,194,278)"/>
                <ellipse cx="197" cy="283" rx="15" ry="9"  fill="#1C1917" transform="rotate(-15,197,283)"/>
                <circle cx="190" cy="289" r="3.5" fill="#292524"/>
                <circle cx="198" cy="289" r="3.5" fill="#292524"/>
                <circle cx="206" cy="286" r="3.5" fill="#292524"/>

                <g style="animation:pmHandWave 1.4s ease-in-out infinite; transform-origin:108px 218px;">
                    <ellipse cx="108" cy="230" rx="13" ry="24" fill="#EA580C" transform="rotate(-35,108,230)"/>
                    <ellipse cx="100" cy="222" rx="9"  ry="6"  fill="#1C1917" transform="rotate(-35,100,222)"/>
                    <circle cx="94"  cy="215" r="4" fill="#292524"/>
                    <circle cx="101" cy="211" r="4" fill="#292524"/>
                    <circle cx="108" cy="210" r="4" fill="#292524"/>
                </g>

                <g style="animation:pmHandHold 2.6s ease-in-out infinite;">
                    <ellipse cx="212" cy="230" rx="13" ry="24" fill="#EA580C" transform="rotate(35,212,230)"/>
                    <g transform="rotate(15,228,215)">
                        <rect x="218" y="205" width="28" height="34" rx="4" fill="white" stroke="#FED7AA" stroke-width="1.5"/>
                        <rect x="222" y="212" width="20" height="2.5" rx="1.2" fill="#FED7AA"/>
                        <rect x="222" y="218" width="16" height="2.5" rx="1.2" fill="#FED7AA"/>
                        <rect x="222" y="224" width="18" height="2.5" rx="1.2" fill="#FED7AA"/>
                        <text x="232" y="234" text-anchor="middle" font-size="9"
                              font-weight="800" fill="#EA580C">?</text>
                    </g>
                </g>

                <circle cx="160" cy="178" r="48" fill="#EA580C"/>

                <circle cx="122" cy="142" r="18" fill="#1C1917"/>
                <circle cx="122" cy="142" r="11" fill="#EA580C"/>
                <circle cx="198" cy="142" r="18" fill="#1C1917"/>
                <circle cx="198" cy="142" r="11" fill="#EA580C"/>

                <g style="animation:pmCrown 2.6s ease-in-out infinite;">
                    <polygon points="160,118 155,130 160,127 165,130" fill="#F97316" opacity="0.8"/>
                    <circle cx="160" cy="117" r="4" fill="#FCD34D"/>
                </g>

                <circle cx="160" cy="184" r="32" fill="#FEF9C3"/>

                <circle cx="147" cy="175" r="11" fill="white"/>
                <circle cx="173" cy="175" r="11" fill="white"/>

                <circle cx="148" cy="176" r="6.5" fill="#1C1917"/>
                <circle cx="174" cy="176" r="6.5" fill="#1C1917"/>

                <circle cx="150" cy="174" r="2.5" fill="white"/>
                <circle cx="176" cy="174" r="2.5" fill="white"/>
                <circle cx="146" cy="178" r="1.2" fill="white" opacity="0.6"/>
                <circle cx="172" cy="178" r="1.2" fill="white" opacity="0.6"/>

                <ellipse cx="160" cy="186" rx="6" ry="4.5" fill="#1C1917"/>

                <path d="M149 195 Q160 208 171 195" stroke="#C2410C" stroke-width="2.5"
                      fill="none" stroke-linecap="round"/>

                <rect x="155" y="195" width="5" height="6" rx="1.5" fill="white"/>
                <rect x="161" y="195" width="5" height="6" rx="1.5" fill="white"/>

                <circle cx="136" cy="187" r="10" fill="#FCA5A5" opacity="0.55"/>
                <circle cx="184" cy="187" r="10" fill="#FCA5A5" opacity="0.55"/>

                <path d="M140 165 Q147 160 154 165" stroke="#1C1917" stroke-width="3"
                      fill="none" stroke-linecap="round"/>
                <path d="M166 165 Q173 160 180 165" stroke="#1C1917" stroke-width="3"
                      fill="none" stroke-linecap="round"/>

                <g style="animation:pmSweat 2s ease-in-out infinite .4s;">
                    <path d="M202 148 Q209 140 203 132" stroke="#60A5FA" stroke-width="2.5"
                          fill="none" stroke-linecap="round" opacity="0.8"/>
                    <path d="M210 154 Q218 145 212 136" stroke="#60A5FA" stroke-width="2"
                          fill="none" stroke-linecap="round" opacity="0.5"/>
                </g>

            </g>

            <g style="animation:pmStarTwinkle 2.2s ease-in-out infinite alternate;">
                <circle cx="72"  cy="210" r="4"   fill="#FCD34D" opacity="0.8"/>
                <circle cx="252" cy="200" r="3"   fill="#FCD34D" opacity="0.7"/>
                <circle cx="58"  cy="175" r="2.5" fill="#FB923C" opacity="0.6"/>
                <circle cx="265" cy="165" r="2.5" fill="#FB923C" opacity="0.55"/>
            </g>

            <g style="animation:pmStarSpin 4s ease-in-out infinite;">
                <polygon points="88,50 91,60 101,60 93,66 96,76 88,70 80,76 83,66 75,60 85,60"
                         fill="#FCD34D" opacity="0.65" transform="scale(0.7)"/>
            </g>
            <g style="animation:pmStarSpin 3.5s ease-in-out infinite .9s;">
                <polygon points="250,240 252,248 260,248 254,253 256,261 250,256 244,261 246,253 240,248 248,248"
                         fill="#FDBA74" opacity="0.55" transform="scale(0.6)"/>
            </g>

            <circle cx="-28" cy="90"  r="5.5" fill="#FCD34D" opacity="0.6" style="animation:pmDot1 2.3s ease-in-out infinite alternate;"/>
            <circle cx="355" cy="100" r="4.5" fill="#F97316" opacity="0.55" style="animation:pmDot2 2.7s ease-in-out infinite alternate .5s;"/>
            <circle cx="-25" cy="230" r="4.5" fill="#FDE68A" opacity="0.6" style="animation:pmDot1 3s ease-in-out infinite alternate 1s;"/>
            <circle cx="358" cy="240" r="5.5" fill="#FCD34D" opacity="0.5" style="animation:pmDot2 2.5s ease-in-out infinite alternate 1.3s;"/>

        </svg>
    </div>

    <div style="margin-top:.6rem; animation:pmFadeUp .7s ease .3s both;">
        <h3 style="font-size:1.25rem; font-weight:800; color:#1c1917; margin:0 0 .5rem; letter-spacing:-.02em;">
            Belum Ada Procurement
        </h3>
        <p style="font-size:.87rem; color:#78716c; line-height:1.75; margin:0 0 1.2rem; max-width:320px;">
            Panda kecil ini sedang menunggu procurement pertamamu!<br>
            Yuk buat pengajuan sekarang 🚀
        </p>

        @if(auth()->check() && auth()->user()->hasRole(['requester', 'super_admin']))
            <a href="{{ route('filament.admin.resources.procurements.create') }}"
               style="display:inline-flex; align-items:center; gap:.5rem;
                      padding:.7rem 1.8rem;
                      background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);
                      color:#fff; border-radius:9999px; border:none;
                      font-weight:700; font-size:.9rem; text-decoration:none;
                      box-shadow:0 4px 20px rgba(234,88,12,.35);
                      transition:transform .22s, box-shadow .22s;
                      animation:pmBtnPop .6s cubic-bezier(.34,1.56,.64,1) .6s both;"
               onmouseover="this.style.transform='translateY(-3px) scale(1.03)';this.style.boxShadow='0 8px 30px rgba(234,88,12,.5)'"
               onmouseout="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(234,88,12,.35)'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.8" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5"  y1="12" x2="19" y2="12"/>
                </svg>
                Buat Procurement Pertama
            </a>
        @else
            <div style="display:inline-flex; align-items:center; gap:.4rem;
                        padding:.5rem 1.3rem; background:#fff7ed;
                        border:1.5px solid #fed7aa; border-radius:9999px;
                        font-size:.82rem; font-weight:600; color:#c2410c;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Tidak ada data procurement saat ini
            </div>
        @endif
    </div>

</div>

<style>
@keyframes pmEntrance {
    from { opacity:0; transform:scale(.6) translateY(20px); }
    to   { opacity:1; transform:scale(1)  translateY(0); }
}
@keyframes pmFadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes pmBtnPop {
    from { opacity:0; transform:scale(.8); }
    to   { opacity:1; transform:scale(1); }
}

@keyframes pmPart1 {
    0%,100% { transform:translate(0,0); }
    33%      { transform:translate(-8px,-12px); }
    66%      { transform:translate(6px,-7px); }
}
@keyframes pmPart2 {
    0%,100% { transform:translate(0,0); }
    33%      { transform:translate(8px,-10px); }
    66%      { transform:translate(-5px,-8px); }
}

@keyframes pmGlowPulse {
    0%,100% { transform:scale(.88); opacity:.55; }
    50%      { transform:scale(1.08); opacity:1; }
}

@keyframes pmRing1 {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.04); }
}
@keyframes pmRing2 {
    0%,100% { transform:scale(1);    opacity:.6; }
    50%      { transform:scale(1.06); opacity:.4; }
}

@keyframes pmPlaneOrbit {
    0%   { transform:rotate(0deg)   translateY(-108px) rotate(0deg); }
    25%  { transform:rotate(90deg)  translateY(-108px) rotate(-90deg); }
    50%  { transform:rotate(180deg) translateY(-108px) rotate(-180deg); }
    75%  { transform:rotate(270deg) translateY(-108px) rotate(-270deg); }
    100% { transform:rotate(360deg) translateY(-108px) rotate(-360deg); }
}
@keyframes pmPlaneTilt {
    0%   { transform:rotate(90deg); }
    25%  { transform:rotate(180deg); }
    50%  { transform:rotate(270deg); }
    75%  { transform:rotate(360deg); }
    100% { transform:rotate(450deg); }
}

@keyframes pmPlaneOrbit2 {
    0%   { transform:rotate(180deg) translateY(-108px) rotate(0deg); }
    25%  { transform:rotate(270deg) translateY(-108px) rotate(-90deg); }
    50%  { transform:rotate(360deg) translateY(-108px) rotate(-180deg); }
    75%  { transform:rotate(450deg) translateY(-108px) rotate(-270deg); }
    100% { transform:rotate(540deg) translateY(-108px) rotate(-360deg); }
}
@keyframes pmPlaneTilt2 {
    0%   { transform:rotate(90deg); }
    25%  { transform:rotate(180deg); }
    50%  { transform:rotate(270deg); }
    75%  { transform:rotate(360deg); }
    100% { transform:rotate(450deg); }
}

@keyframes pmTrailOrbit {
    0%   { transform:rotate(0deg)   translateY(-108px) rotate(0deg); }
    100% { transform:rotate(360deg) translateY(-108px) rotate(-360deg); }
}

@keyframes pmPandaBob {
    0%,100% { transform:translateY(0); }
    50%      { transform:translateY(-10px); }
}

@keyframes pmHandWave {
    0%,100% { transform:rotate(0deg); }
    25%      { transform:rotate(20deg); }
    75%      { transform:rotate(-15deg); }
}

@keyframes pmHandHold {
    0%,100% { transform:translateY(0); }
    50%      { transform:translateY(-10px); }
}

@keyframes pmCrown {
    0%,100% { transform:translateY(0)    rotate(0deg); }
    50%      { transform:translateY(-10px) rotate(-5deg); }
}

@keyframes pmSweat {
    0%,100% { opacity:.8; transform:translateY(0); }
    50%      { opacity:.3; transform:translateY(4px); }
}

@keyframes pmStarTwinkle {
    from { opacity:.3; }
    to   { opacity:1; }
}
@keyframes pmStarSpin {
    0%,100% { transform:scale(1)   rotate(0deg);  opacity:.6; }
    50%      { transform:scale(1.4) rotate(180deg); opacity:1; }
}
@keyframes pmDot1 {
    from { opacity:.3; transform:translateY(0); }
    to   { opacity:.9; transform:translateY(-7px); }
}
@keyframes pmDot2 {
    from { opacity:.3; transform:translateX(0); }
    to   { opacity:.9; transform:translateX(7px); }
}
</style>