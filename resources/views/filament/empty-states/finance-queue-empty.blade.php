{{--
    resources/views/filament/empty-states/finance-queue-empty.blade.php
    Digunakan di: DashboardFinanceStaffTable, ManagerProcurementTableWidget
    Warna: Amber latar + centang hijau
--}}
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2rem 1rem 2.5rem; text-align:center;">

    {{-- SVG Ilustrasi --}}
    <div style="animation: fqFadeUp .7s ease both;">
        <svg viewBox="-20 -20 280 240" xmlns="http://www.w3.org/2000/svg"
             style="width:200px; height:auto; overflow:visible; filter:drop-shadow(0 8px 20px rgba(245,158,11,.2));">

            {{-- ── Lingkaran latar — AMBER ── --}}
            <circle cx="120" cy="110" r="100" fill="#FFFBEB"
                style="transform-origin:120px 110px; animation:fqPulse1 3s ease-in-out infinite;"/>
            <circle cx="120" cy="110" r="76" fill="#FEF3C7" opacity="0.75"
                style="transform-origin:120px 110px; animation:fqPulse2 3s ease-in-out infinite .5s;"/>
            <circle cx="120" cy="110" r="54" fill="#FDE68A" opacity="0.5"
                style="transform-origin:120px 110px; animation:fqBounce 2.4s ease-in-out infinite;"/>

            {{-- ── Dokumen melayang KIRI — border amber, centang hijau ── --}}
            <g style="animation:fqFloatL 3.3s ease-in-out infinite; transform-origin:30px 55px;">
                <rect x="0" y="25" width="52" height="64" rx="6" fill="#ffffff" stroke="#FCD34D" stroke-width="2"/>
                <rect x="9"  y="39" width="34" height="3.5" rx="2" fill="#FDE68A"/>
                <rect x="9"  y="49" width="26" height="3.5" rx="2" fill="#FDE68A"/>
                <rect x="9"  y="59" width="30" height="3.5" rx="2" fill="#FDE68A"/>
                {{-- centang hijau --}}
                <circle cx="26" cy="75" r="9" fill="#86EFAC" opacity="0.35"/>
                <path d="M20 75 L25 80 L33 68" stroke="#16A34A" stroke-width="2" fill="none"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </g>

            {{-- ── Dokumen melayang KANAN — border amber, centang hijau ── --}}
            <g style="animation:fqFloatR 3.8s ease-in-out infinite .6s; transform-origin:210px 60px;">
                <rect x="185" y="30" width="50" height="62" rx="6" fill="#ffffff" stroke="#FCD34D" stroke-width="2"/>
                <rect x="193" y="44" width="34" height="3.5" rx="2" fill="#FDE68A"/>
                <rect x="193" y="54" width="26" height="3.5" rx="2" fill="#FDE68A"/>
                <rect x="193" y="64" width="30" height="3.5" rx="2" fill="#FDE68A"/>
                {{-- centang hijau --}}
                <circle cx="210" cy="79" r="9" fill="#86EFAC" opacity="0.35"/>
                <path d="M204 79 L209 84 L217 72" stroke="#16A34A" stroke-width="2" fill="none"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </g>

            {{-- ── Centang besar di tengah — HIJAU ── --}}
            <path d="M88 110 L106 128 L152 82"
                  stroke="#16A34A" stroke-width="8" fill="none"
                  stroke-linecap="round" stroke-linejoin="round"
                  style="stroke-dasharray:100; stroke-dashoffset:100;
                         animation:fqDraw .75s cubic-bezier(.22,1,.36,1) .3s forwards;"/>

            {{-- ── Titik dekoratif — AMBER ── --}}
            <circle cx="-5"  cy="55"  r="5"   fill="#FBBF24" opacity="0.7"
                style="animation:fqDot1 2.2s ease-in-out infinite alternate;"/>
            <circle cx="248" cy="65"  r="4"   fill="#FBBF24" opacity="0.65"
                style="animation:fqDot2 2.6s ease-in-out infinite alternate .4s;"/>
            <circle cx="-8"  cy="150" r="4"   fill="#FCD34D" opacity="0.7"
                style="animation:fqDot1 2.8s ease-in-out infinite alternate .8s;"/>
            <circle cx="250" cy="155" r="5"   fill="#FCD34D" opacity="0.6"
                style="animation:fqDot2 2.4s ease-in-out infinite alternate 1.1s;"/>

            {{-- ── Bintang kecil — AMBER ── --}}
            <rect x="50" y="5" width="7" height="7" rx="2" fill="#F59E0B" opacity="0.65"
                style="transform-origin:53px 8px; animation:fqStar 3s ease-in-out infinite;"/>
            <rect x="178" y="148" width="6" height="6" rx="2" fill="#F59E0B" opacity="0.55"
                style="transform-origin:181px 151px; animation:fqStar 3.5s ease-in-out infinite .8s;"/>

        </svg>
    </div>

    {{-- Teks --}}
    <div style="margin-top:1rem; animation:fqFadeUp .65s ease .2s both;">
        <h3 style="font-size:1.1rem; font-weight:700; color:#92400E; margin:0 0 .45rem; letter-spacing:-.01em;">
            🎉 Semua Sudah Beres!
        </h3>
        <p style="font-size:.83rem; color:#78716c; line-height:1.7; margin:0;">
            Tidak ada procurement yang perlu ditangani saat ini.<br>
            Semua sudah direview dan diproses.
        </p>
    </div>

    {{-- Badge — AMBER border, teks amber, icon centang hijau --}}
    <div style="margin-top:.9rem; display:inline-flex; align-items:center; gap:.38rem;
                padding:.42rem 1.1rem; background:#FFFBEB; border:1.5px solid #FCD34D;
                border-radius:9999px; font-size:.76rem; font-weight:600; color:#92400E;
                box-shadow:0 2px 10px rgba(245,158,11,.18);
                animation:fqFadeUp .65s ease .35s both;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16A34A"
             stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Work queue kosong
    </div>

</div>

<style>
@keyframes fqFadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes fqPulse1 {
    0%,100% { transform:scale(1);    opacity:1;   }
    50%      { transform:scale(1.05); opacity:.78; }
}
@keyframes fqPulse2 {
    0%,100% { transform:scale(1);    opacity:.75; }
    50%      { transform:scale(1.07); opacity:.5;  }
}
@keyframes fqBounce {
    0%,100% { transform:translateY(0);    }
    50%      { transform:translateY(-8px); }
}
@keyframes fqDraw {
    to { stroke-dashoffset:0; }
}
@keyframes fqFloatL {
    0%,100% { transform:translate(0,0)      rotate(-2deg); }
    50%      { transform:translate(-5px,-8px) rotate(2.5deg); }
}
@keyframes fqFloatR {
    0%,100% { transform:translate(0,0)     rotate(3deg);  }
    50%      { transform:translate(5px,-7px) rotate(-2deg); }
}
@keyframes fqDot1 {
    from { opacity:.3; transform:translateY(0);    }
    to   { opacity:.9; transform:translateY(-5px); }
}
@keyframes fqDot2 {
    from { opacity:.3; transform:translateX(0);   }
    to   { opacity:.9; transform:translateX(5px); }
}
@keyframes fqStar {
    0%,100% { transform:scale(1)   rotate(0deg);  opacity:.5; }
    50%      { transform:scale(1.5) rotate(45deg); opacity:1;  }
}
</style>