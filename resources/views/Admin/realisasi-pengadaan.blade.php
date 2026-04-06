<div>{{-- ROOT ELEMENT TUNGGAL untuk Livewire --}}

<style>
/* ─── Scoped Reset — HANYA berlaku di dalam .r-wrap ─── */
.r-wrap *, .r-wrap *::before, .r-wrap *::after {
    box-sizing: border-box;
}

/* ════════════════════════════════════════════════════════
   DESIGN TOKENS — Light Mode (default)
   ════════════════════════════════════════════════════════ */
:root,
.fi-body:not(.dark) {
    /* Amber palette */
    --a50:#fffbeb; --a100:#fef3c7; --a200:#fde68a; --a300:#fcd34d;
    --a400:#fbbf24; --a500:#f59e0b; --a600:#d97706; --a700:#b45309;
    --a800:#92400e; --a900:#78350f;

    /* Neutral ink */
    --ink:#18181b; --ink2:#3f3f46; --ink3:#71717a; --ink4:#a1a1aa; --ink5:#e4e4e7;

    /* Surface */
    --white:#ffffff; --surface:#fafaf9;

    /* Semantic green / red / blue */
    --g50:#f0fdf4; --g200:#bbf7d0; --g600:#16a34a;
    --r600:#dc2626;
    --b50:#eff6ff; --b200:#bfdbfe; --b700:#1d4ed8;

    /* Component tokens */
    --bg-card:         var(--white);
    --bg-surface:      var(--surface);
    --bg-cat-row:      linear-gradient(90deg,var(--a50),#fffdf7);
    --bg-purchased:    #f0fdf4;
    --bg-pending:      var(--a50);
    --bg-no-proc:      var(--surface);
    --border-card:     var(--a100);
    --border-table:    var(--a100);
    --border-row:      #f4f4f5;
    --border-cat:      var(--a200);
    --border-cat-bot:  var(--a100);
    --text-cat:        var(--a800);
    --bg-hdr:          var(--white);

    /* Stat card accent bg */
    --s1-accent:#fef3c7;
    --s2-accent:#d1fae5;
    --s3-accent:#dbeafe;
    --s4-accent:#ffe4e6;

    /* Scrollbar */
    --sb-track: var(--a50);
    --sb-thumb: var(--a300);
    --sb-thumb-h: var(--a500);

    /* Dropdown */
    --dd-bg: var(--white);
    --dd-border: var(--a200);

    /* Input */
    --input-bg: var(--white);
    --input-border: var(--a200);
    --input-color: var(--ink);

    /* Modal overlay */
    --overlay-bg: rgba(24,24,27,.5);

    /* Toast */
    --toast-bg: var(--a800);

    /* Struk modal */
    --struk-item-bg: var(--surface);

    /* Breakdwon panel row hover */
    --breakdown-row-hover: var(--a50);

    --sh-xs:0 1px 2px rgba(0,0,0,.04);
    --sh-sm:0 1px 4px rgba(0,0,0,.06),0 2px 8px rgba(0,0,0,.04);
    --sh-md:0 4px 16px rgba(0,0,0,.08),0 1px 4px rgba(0,0,0,.04);
    --sh-lg:0 12px 40px rgba(0,0,0,.1),0 4px 12px rgba(0,0,0,.05);
    --sh-amber:0 8px 32px rgba(245,158,11,.2);
    --sh-green:0 4px 16px rgba(22,163,74,.15);
}

/* ════════════════════════════════════════════════════════
   DESIGN TOKENS — Dark Mode
   ════════════════════════════════════════════════════════ */
@media (prefers-color-scheme: dark) {
    :root {
        --a50:#2d1f00; --a100:#3d2c00; --a200:#5a4000; --a300:#7a5800;
        --a400:#9c7213; --a500:#c49121; --a600:#e0a830; --a700:#f0bf50;
        --a800:#fcd97a; --a900:#fdedb0;

        --ink:#f4f4f5; --ink2:#d4d4d8; --ink3:#a1a1aa; --ink4:#71717a; --ink5:#3f3f46;
        --white:#1c1c1e; --surface:#141414;

        --g50:#052e16; --g200:#14532d; --g600:#4ade80;
        --r600:#f87171;
        --b50:#0c1a2e; --b200:#1e3a5f; --b700:#60a5fa;

        --bg-card:         #1c1c1e;
        --bg-surface:      #141414;
        --bg-cat-row:      linear-gradient(90deg,#2d2000,#1c1500);
        --bg-purchased:    #052e16;
        --bg-pending:      #2d1f00;
        --bg-no-proc:      #141414;
        --border-card:     #3d2c00;
        --border-table:    #2a2a2e;
        --border-row:      #27272a;
        --border-cat:      #5a4000;
        --border-cat-bot:  #3d2c00;
        --text-cat:        #fcd97a;
        --bg-hdr:          #1c1c1e;

        --s1-accent:#3d2c00;
        --s2-accent:#052e16;
        --s3-accent:#0c1a2e;
        --s4-accent:#2d0a0f;

        --sb-track: #2d1f00;
        --sb-thumb: #7a5800;
        --sb-thumb-h: #c49121;

        --dd-bg: #1c1c1e;
        --dd-border: #3d2c00;

        --input-bg: #141414;
        --input-border: #3d2c00;
        --input-color: #f4f4f5;

        --overlay-bg: rgba(0,0,0,.7);
        --toast-bg: #3d2c00;
        --struk-item-bg: #141414;
        --breakdown-row-hover: #2d1f00;

        --sh-xs:0 1px 2px rgba(0,0,0,.3);
        --sh-sm:0 1px 4px rgba(0,0,0,.4),0 2px 8px rgba(0,0,0,.3);
        --sh-md:0 4px 16px rgba(0,0,0,.5),0 1px 4px rgba(0,0,0,.3);
        --sh-lg:0 12px 40px rgba(0,0,0,.6),0 4px 12px rgba(0,0,0,.4);
        --sh-amber:0 8px 32px rgba(156,114,19,.4);
        --sh-green:0 4px 16px rgba(74,222,128,.2);
    }
}

/* Filament dark class override */
.dark, .dark .r-wrap, .fi-body.dark {
    --a50:#2d1f00; --a100:#3d2c00; --a200:#5a4000; --a300:#7a5800;
    --a400:#9c7213; --a500:#c49121; --a600:#e0a830; --a700:#f0bf50;
    --a800:#fcd97a; --a900:#fdedb0;

    --ink:#f4f4f5; --ink2:#d4d4d8; --ink3:#a1a1aa; --ink4:#71717a; --ink5:#3f3f46;
    --white:#1c1c1e; --surface:#141414;

    --g50:#052e16; --g200:#14532d; --g600:#4ade80;
    --r600:#f87171;
    --b50:#0c1a2e; --b200:#1e3a5f; --b700:#60a5fa;

    --bg-card:         #1c1c1e;
    --bg-surface:      #141414;
    --bg-cat-row:      linear-gradient(90deg,#2d2000,#1c1500);
    --bg-purchased:    #052e16;
    --bg-pending:      #2d1f00;
    --bg-no-proc:      #141414;
    --border-card:     #3d2c00;
    --border-table:    #2a2a2e;
    --border-row:      #27272a;
    --border-cat:      #5a4000;
    --border-cat-bot:  #3d2c00;
    --text-cat:        #fcd97a;
    --bg-hdr:          #1c1c1e;

    --s1-accent:#3d2c00;
    --s2-accent:#052e16;
    --s3-accent:#0c1a2e;
    --s4-accent:#2d0a0f;

    --sb-track: #2d1f00;
    --sb-thumb: #7a5800;
    --sb-thumb-h: #c49121;

    --dd-bg: #1c1c1e;
    --dd-border: #3d2c00;

    --input-bg: #141414;
    --input-border: #3d2c00;
    --input-color: #f4f4f5;

    --overlay-bg: rgba(0,0,0,.7);
    --toast-bg: #3d2c00;
    --struk-item-bg: #141414;
    --breakdown-row-hover: #2d1f00;

    --sh-xs:0 1px 2px rgba(0,0,0,.3);
    --sh-sm:0 1px 4px rgba(0,0,0,.4),0 2px 8px rgba(0,0,0,.3);
    --sh-md:0 4px 16px rgba(0,0,0,.5),0 1px 4px rgba(0,0,0,.3);
    --sh-lg:0 12px 40px rgba(0,0,0,.6),0 4px 12px rgba(0,0,0,.4);
    --sh-amber:0 8px 32px rgba(156,114,19,.4);
    --sh-green:0 4px 16px rgba(74,222,128,.2);
}

/* ─── Filament overrides ─────────────────────────────── */
.fi-main-ctn,.fi-main,.fi-page,.fi-page-content,.fi-simple-page,.fi-body,
.fi-wi-stats-overview,.fi-section,.fi-section-content,.fi-section-content-ctn {
    overflow-x:auto!important; overflow-y:auto!important; min-width:0!important;
}
.r-wrap,.r-wrap>*{ min-width:0!important; }

/* ─── Scrollbar ──────────────────────────────────────── */
.table-shell::-webkit-scrollbar{ height:5px; }
.table-shell::-webkit-scrollbar-track{ background:var(--sb-track); border-radius:99px; }
.table-shell::-webkit-scrollbar-thumb{ background:var(--sb-thumb); border-radius:99px; }
.table-shell::-webkit-scrollbar-thumb:hover{ background:var(--sb-thumb-h); }

/* ─── Root wrapper ───────────────────────────────────── */
.r-wrap {
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-size: 13px;
    color: var(--ink);
    min-width: 0;
    width: 100%;
    -webkit-font-smoothing: antialiased;
}

/* ─── Toast / Loading ────────────────────────────────── */
.toast-loading {
    display:flex; align-items:center; gap:10px;
    padding:10px 18px; border-radius:40px;
    background:var(--toast-bg); color:#fff;
    font-size:12px; font-weight:500;
    box-shadow:var(--sh-lg);
    animation:toastSlide var(--d-slow) var(--ease-spring) both;
}
@keyframes toastSlide{
    from{opacity:0;transform:translateY(16px) scale(.94);}
    to{opacity:1;transform:translateY(0) scale(1);}
}
.spinner {
    width:14px; height:14px; border-radius:50%;
    border:2px solid rgba(255,255,255,.3); border-top-color:#fff;
    animation:spin .7s linear infinite; flex-shrink:0;
}
@keyframes spin{ to{transform:rotate(360deg);} }

/* ─── Animation easing & duration ───────────────────── */
:root {
    --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-2xl:24px;
    --ease-spring:cubic-bezier(.34,1.56,.64,1);
    --ease-out:cubic-bezier(0,0,.2,1);
    --ease-io:cubic-bezier(.4,0,.2,1);
    --d-fast:120ms; --d-base:200ms; --d-slow:320ms;
}

/* ─── Stat Grid ──────────────────────────────────────── */
.stat-grid {
    display:grid; grid-template-columns:repeat(4,1fr);
    gap:12px; margin-bottom:20px;
}
@media(max-width:900px){ .stat-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:520px){ .stat-grid{ grid-template-columns:1fr; } }

.stat-card {
    background:var(--bg-card); border-radius:var(--r-lg);
    padding:18px; border:1px solid var(--border-card); box-shadow:var(--sh-sm);
    cursor:default; will-change:transform;
    animation:cardRise var(--d-slow) var(--ease-out) both;
    transition:box-shadow var(--d-base) var(--ease-io),
               border-color var(--d-base) var(--ease-io),
               transform var(--d-base) var(--ease-spring);
}
.stat-card:nth-child(1){animation-delay:0ms;}
.stat-card:nth-child(2){animation-delay:55ms;}
.stat-card:nth-child(3){animation-delay:110ms;}
.stat-card:nth-child(4){animation-delay:165ms;}
@keyframes cardRise{
    from{opacity:0;transform:translateY(14px);}
    to{opacity:1;transform:translateY(0);}
}
.stat-card:hover{ box-shadow:var(--sh-amber); border-color:var(--a200); transform:translateY(-3px); }

.stat-accent {
    width:36px; height:36px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; margin-bottom:12px;
    transition:transform var(--d-base) var(--ease-spring);
}
.stat-card:hover .stat-accent{ transform:scale(1.1) rotate(-4deg); }
.s1 .stat-accent{ background:var(--s1-accent); }
.s2 .stat-accent{ background:var(--s2-accent); }
.s3 .stat-accent{ background:var(--s3-accent); }
.s4 .stat-accent{ background:var(--s4-accent); }

.stat-label {
    font-size:11px; font-weight:600;
    color:var(--ink4); margin-bottom:4px;
}
.stat-value {
    font-size:18px; font-weight:600;
    color:var(--ink); line-height:1.25;
}
.stat-value span { font-size:12px; color:var(--ink4); font-weight:400; }
.stat-sub { font-size:11px; color:var(--ink4); margin-top:4px; }

/* ─── Toolbar ────────────────────────────────────────── */
.toolbar {
    display:flex; align-items:center; justify-content:flex-end;
    margin-bottom:14px; flex-wrap:wrap; gap:8px;
}

.tb-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 13px; background:var(--bg-card);
    border:1px solid var(--a200); border-radius:var(--r-sm);
    color:var(--a700); font-size:12px; font-weight:500;
    font-family:inherit; cursor:pointer; white-space:nowrap;
    box-shadow:var(--sh-xs);
    transition:background var(--d-fast) var(--ease-io),
               border-color var(--d-fast) var(--ease-io),
               box-shadow var(--d-fast) var(--ease-io),
               color var(--d-fast) var(--ease-io),
               transform var(--d-base) var(--ease-spring);
}
.tb-btn:hover{ background:var(--a50); border-color:var(--a400); box-shadow:var(--sh-md); transform:translateY(-2px); color:var(--a800); }
.tb-btn:active{ transform:translateY(0) scale(.97); }

/* ─── Tooltip ────────────────────────────────────────── */
[data-tooltip]{ position:relative; }
[data-tooltip]::before {
    content:attr(data-tooltip);
    position:absolute; bottom:calc(100% + 10px);
    left:50%; transform:translateX(-50%) translateY(6px);
    padding:5px 11px; background:var(--ink); color:var(--white);
    font-size:11px; font-weight:400;
    border-radius:7px; white-space:nowrap;
    opacity:0; visibility:hidden; pointer-events:none;
    box-shadow:var(--sh-md);
    transition:opacity var(--d-fast) var(--ease-io),
               transform var(--d-fast) var(--ease-io),
               visibility var(--d-fast) var(--ease-io);
    z-index:9999;
}
[data-tooltip]:hover::before{ opacity:1; visibility:visible; transform:translateX(-50%) translateY(0); }
[data-tooltip].tooltip-right::before{ left:auto; right:0; transform:translateY(6px); }
[data-tooltip].tooltip-right:hover::before{ transform:translateY(0); }
[data-tooltip].tooltip-left::before{ left:0; right:auto; transform:translateY(6px); }
[data-tooltip].tooltip-left:hover::before{ transform:translateY(0); }
.toolbar>div:last-child [data-tooltip]::before{ left:auto; right:0; transform:translateY(6px); }
.toolbar>div:last-child [data-tooltip]:hover::before{ transform:translateY(0); }
.toolbar>div:nth-last-child(2) [data-tooltip]::before{ left:auto; right:0; transform:translateY(6px); }
.toolbar>div:nth-last-child(2) [data-tooltip]:hover::before{ transform:translateY(0); }

/* ─── Dropdown Panels ────────────────────────────────── */
.dropdown-panel {
    position:absolute; top:calc(100% + 8px); right:0;
    background:var(--dd-bg); border:1px solid var(--dd-border);
    border-radius:var(--r-md); padding:5px;
    box-shadow:var(--sh-lg); z-index:9999; min-width:170px;
    transform-origin:top right;
    animation:dropIn var(--d-base) var(--ease-spring);
}
.dropdown-panel-wide {
    position:absolute; top:calc(100% + 8px); right:0;
    background:var(--dd-bg); border:1px solid var(--dd-border);
    border-radius:var(--r-lg); padding:20px;
    box-shadow:var(--sh-lg); z-index:9999; min-width:520px;
    transform-origin:top right;
    animation:dropIn var(--d-base) var(--ease-spring);
}
@keyframes dropIn{
    from{opacity:0;transform:scale(.93) translateY(-8px);}
    to{opacity:1;transform:scale(1) translateY(0);}
}
[x-cloak]{ display:none!important; }

/* ─── Company Option ─────────────────────────────────── */
.company-option {
    display:flex; align-items:center; gap:8px;
    padding:8px 11px; border-radius:8px;
    font-size:12px; font-weight:400; color:var(--ink2);
    cursor:pointer; border:none; background:transparent;
    width:100%; text-align:left; font-family:inherit;
    transition:background var(--d-fast) var(--ease-io),
               padding-left var(--d-fast) var(--ease-io),
               color var(--d-fast) var(--ease-io);
}
.company-option:hover{ background:var(--a50); padding-left:15px; }
.company-option.active{ background:var(--a100); color:var(--a800); font-weight:600; }
.company-option .check{
    margin-left:auto; color:var(--a500);
    opacity:0; transform:scale(0);
    transition:opacity var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.company-option.active .check{ opacity:1; transform:scale(1); }

/* ─── Filter Controls ────────────────────────────────── */
.filter-dropdown-title {
    font-size:11px; font-weight:600;
    color:var(--ink3); margin-bottom:14px;
    padding-bottom:10px; border-bottom:1px solid var(--border-card);
}
.filter-row{ display:flex; flex-wrap:wrap; align-items:flex-end; gap:10px; }
.filter-field label {
    font-size:11px; font-weight:500; color:var(--ink3);
    display:block; margin-bottom:5px;
}
.filter-field select,
.filter-field input[type="date"] {
    padding:7px 10px; border:1px solid var(--input-border);
    border-radius:var(--r-sm); font-size:12px; color:var(--input-color);
    background:var(--input-bg); font-family:inherit; outline:none; cursor:pointer;
    transition:border-color var(--d-fast),box-shadow var(--d-fast);
}
.filter-field select:focus,
.filter-field input[type="date"]:focus {
    border-color:var(--a500); box-shadow:0 0 0 3px rgba(245,158,11,.12);
}
.filter-divider{ width:1px; height:36px; background:var(--border-card); align-self:flex-end; }
.btn-reset {
    padding:7px 14px; background:var(--a50);
    border:1px solid var(--a200); color:var(--a700);
    border-radius:var(--r-sm); font-size:12px; font-weight:500;
    cursor:pointer; font-family:inherit;
    transition:background var(--d-fast),border-color var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.btn-reset:hover{ background:var(--a100); border-color:var(--a400); transform:translateY(-1px); }
.btn-reset:active{ transform:scale(.97); }

/* ─── Dropdown Menu Buttons ──────────────────────────── */
.dd-menu-btn {
    display:flex; align-items:center; gap:10px;
    width:100%; padding:9px 11px; border-radius:9px;
    border:none; background:transparent;
    font-family:inherit; font-size:12px; font-weight:500;
    color:var(--ink2); cursor:pointer;
    transition:background var(--d-fast) var(--ease-io),
               padding-left var(--d-fast) var(--ease-io);
}
.dd-menu-btn:hover{ background:var(--a50); padding-left:15px; }
.dd-menu-icon {
    width:28px; height:28px; border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    font-size:14px; flex-shrink:0;
}

/* ─── Table Shell ────────────────────────────────────── */
.table-shell {
    background:var(--bg-card); border-radius:var(--r-lg);
    border:1px solid var(--border-card); box-shadow:var(--sh-sm);
    overflow-x:auto!important; overflow-y:visible!important;
    -webkit-overflow-scrolling:touch!important;
    display:block!important; width:100%!important;
    animation:cardRise var(--d-slow) var(--ease-out) .25s both;
}
table.rt { width:100%; border-collapse:collapse; font-size:12.5px; min-width:1160px; }
table.rt thead tr.hdr th {
    background:var(--bg-hdr); padding:10px 14px;
    font-size:11px; font-weight:600; color:var(--ink3);
    border-bottom:1px solid var(--border-table);
    position:sticky; top:0; z-index:10; white-space:nowrap;
}

/* ─── Category Row ───────────────────────────────────── */
tr.cat-row td {
    padding:10px 14px;
    background:var(--bg-cat-row);
    border-top:1px solid var(--border-cat); border-bottom:1px solid var(--border-cat-bot);
    color:var(--text-cat); font-weight:600; font-size:12px;
}
.cat-name-wrap{ display:flex; align-items:center; gap:8px; }
.cat-dot {
    width:7px; height:7px; border-radius:50%; background:var(--a400); flex-shrink:0;
    animation:catPulse 2.4s ease-in-out infinite;
}
@keyframes catPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.85);}}
.cat-pill {
    background:var(--a100); border:1px solid var(--a200);
    color:var(--a700); padding:2px 8px; border-radius:99px;
    font-size:10px; font-weight:500;
}
.mini-bar{ height:3px; background:var(--a100); border-radius:99px; margin-top:5px; max-width:160px; overflow:hidden; }
.mini-bar-fill {
    height:100%; border-radius:99px;
    background:linear-gradient(90deg,var(--a400),var(--a600));
    transition:width .8s var(--ease-out);
}

/* ─── Data Rows ──────────────────────────────────────── */
tr.dr td {
    padding:9px 14px; border-bottom:1px solid var(--border-row);
    vertical-align:middle; color:var(--ink2);
    transition:background var(--d-fast) var(--ease-io);
}
tr.dr:hover td{ background:var(--a50)!important; }
tr.dr:hover .iname{ color:var(--a800); }
tr.dr.purchased td{ background:var(--bg-purchased); }
tr.dr.pending td{ background:var(--bg-pending); }
tr.dr.no-proc td{ background:var(--bg-no-proc); }

.iname{ font-weight:600; font-size:13px; color:var(--ink); line-height:1.4; transition:color var(--d-fast) var(--ease-io); }
.ispec{ font-size:11px; color:var(--ink4); margin-top:2px; }
.vtag{ font-size:11px; color:var(--ink4); margin-left:6px; }
.no-col{ text-align:center; color:var(--ink5); font-size:11px; }
.company-label-text{ font-size:12px; font-weight:500; color:var(--ink)!important; }

/* ─── Status Badge ───────────────────────────────────── */
.sbadge {
    display:inline-flex; flex-direction:column; align-items:center;
    width:100%; padding:6px 10px; border-radius:var(--r-sm);
    font-size:11px; font-weight:500; line-height:1.4;
    border:1px solid transparent;
    transition:transform var(--d-base) var(--ease-spring),box-shadow var(--d-base) var(--ease-io);
}
.sbadge:hover{ transform:scale(1.02); box-shadow:var(--sh-xs); }
.sbadge-done{ background:var(--g50); color:#166534; border-color:var(--g200); }
.sbadge-wait{ background:var(--a50); color:var(--a700); border-color:var(--a200); }
.sbadge-no{ background:var(--bg-surface); color:var(--ink4); border-color:var(--ink5); }
.sbadge-sub{ font-size:10px; font-weight:400; opacity:.65; margin-top:2px; }

/* Dark mode: sbadge-done text fix */
@media (prefers-color-scheme: dark) {
    .sbadge-done{ color:#4ade80; }
}
.dark .sbadge-done{ color:#4ade80; }

.selisih-over  { color:var(--r600); font-weight:600; font-size:12px; }
.selisih-under { color:var(--g600); font-weight:600; font-size:12px; }
.selisih-even  { color:var(--ink4); font-size:12px; }

.rqty-badge {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:28px; padding:2px 8px;
    background:var(--g50); border:1px solid var(--g200);
    border-radius:99px; font-size:13px; font-weight:600; color:var(--g600);
    transition:transform var(--d-base) var(--ease-spring);
}
tr.dr:hover .rqty-badge{ transform:scale(1.08); }

/* ─── Struk Button ───────────────────────────────────── */
.struk-btn {
    display:inline-flex; align-items:center; gap:4px;
    font-size:11px; font-weight:500; color:var(--g600);
    margin-top:5px; padding:4px 9px;
    background:var(--g50); border-radius:7px; border:1px solid var(--g200);
    cursor:pointer; font-family:inherit;
    transition:background var(--d-fast),transform var(--d-base) var(--ease-spring),box-shadow var(--d-fast);
}
.struk-btn:hover{ background:var(--g200); transform:translateY(-2px); box-shadow:var(--sh-green); }
.struk-btn:active{ transform:scale(.96); }

/* ─── Download Overlay ───────────────────────────────── */
.dl-overlay {
    position:fixed; inset:0; background:var(--overlay-bg);
    z-index:1000; display:flex; align-items:center; justify-content:center;
    backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
    opacity:0; visibility:hidden;
    transition:opacity var(--d-slow) var(--ease-io),visibility var(--d-slow) var(--ease-io);
}
.dl-overlay:not(.hidden){ opacity:1; visibility:visible; }
.dl-overlay.hidden{ pointer-events:none; }
.dl-modal {
    background:var(--bg-card); border-radius:var(--r-2xl); padding:28px;
    width:480px; max-width:calc(100vw - 32px); box-shadow:var(--sh-lg);
    border:1px solid var(--border-card);
    animation:modalIn var(--d-slow) var(--ease-spring);
}
@keyframes modalIn{
    from{opacity:0;transform:scale(.92) translateY(-20px);}
    to{opacity:1;transform:scale(1) translateY(0);}
}
.dl-modal-header{ display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.dl-modal-title{ font-size:15px; font-weight:600; color:var(--ink); display:flex; align-items:center; gap:8px; }
.dl-close-btn {
    background:none; border:none; cursor:pointer;
    color:var(--ink4); padding:5px; border-radius:8px; font-size:16px; line-height:1;
    transition:background var(--d-fast),color var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.dl-close-btn:hover{ background:var(--a100); color:var(--a700); transform:rotate(90deg); }
.dl-format-row{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:20px; }
.dl-format-card {
    border:1px solid var(--border-card); border-radius:var(--r-md); padding:12px;
    cursor:pointer; background:var(--bg-card); display:flex; align-items:center; gap:10px;
    transition:border-color var(--d-fast),background var(--d-fast),
               transform var(--d-base) var(--ease-spring),box-shadow var(--d-fast);
}
.dl-format-card:hover{ border-color:var(--a300); background:var(--a50); transform:translateY(-2px); }
.dl-format-card.selected{ border-color:var(--a500); background:var(--a50); box-shadow:0 4px 16px rgba(245,158,11,.2); }
.dl-format-icon{ width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.pdf-icon{ background:var(--s4-accent); }
.excel-icon{ background:var(--s2-accent); }
.dl-format-name{ font-size:12px; font-weight:600; color:var(--ink); }
.dl-format-desc{ font-size:10px; color:var(--ink4); margin-top:2px; }
.dl-date-section{ margin-bottom:20px; }
.dl-section-label{ font-size:11px; font-weight:500; color:var(--ink4); margin-bottom:10px; display:flex; align-items:center; gap:6px; }
.dl-date-row{ display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; }
.dl-date-field label{ font-size:11px; font-weight:500; color:var(--ink3); display:block; margin-bottom:4px; }
.dl-date-field input[type="date"] {
    width:100%; padding:8px 10px; border:1px solid var(--input-border);
    border-radius:var(--r-sm); font-size:12px; color:var(--input-color);
    background:var(--input-bg); font-family:inherit; outline:none;
    transition:border-color var(--d-fast),box-shadow var(--d-fast);
}
.dl-date-field input[type="date"]:focus{ border-color:var(--a500); box-shadow:0 0 0 3px rgba(245,158,11,.12); }
.dl-date-sep{ color:var(--ink4); font-size:12px; padding-top:20px; text-align:center; }
.dl-quick-ranges{ display:flex; gap:6px; flex-wrap:wrap; margin-top:10px; }
.dl-quick-btn {
    padding:4px 11px; border:1px solid var(--a200); border-radius:99px;
    font-size:11px; font-weight:500; color:var(--a700);
    background:var(--bg-card); cursor:pointer; font-family:inherit;
    transition:background var(--d-fast),border-color var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.dl-quick-btn:hover{ background:var(--a100); border-color:var(--a400); transform:translateY(-1px); }
.dl-action-row{ display:flex; gap:8px; justify-content:flex-end; padding-top:16px; border-top:1px solid var(--border-card); }
.dl-cancel-btn {
    padding:9px 18px; background:var(--bg-card); border:1px solid var(--a200);
    color:var(--ink3); border-radius:var(--r-sm); font-size:12px; font-weight:500;
    cursor:pointer; font-family:inherit;
    transition:background var(--d-fast),border-color var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.dl-cancel-btn:hover{ background:var(--a50); border-color:var(--a400); transform:translateY(-1px); }
.dl-confirm-btn {
    padding:9px 20px; background:linear-gradient(135deg,var(--a500),var(--a700));
    border:none; color:#fff; border-radius:var(--r-sm);
    font-size:12px; font-weight:600; cursor:pointer; font-family:inherit;
    display:flex; align-items:center; gap:6px;
    box-shadow:0 2px 8px rgba(180,83,9,.25); text-decoration:none;
    transition:transform var(--d-base) var(--ease-spring),box-shadow var(--d-fast),opacity var(--d-fast);
}
.dl-confirm-btn:hover:not(:disabled){ transform:translateY(-2px); box-shadow:0 6px 20px rgba(180,83,9,.35); }
.dl-confirm-btn:active{ transform:scale(.97); }
.dl-confirm-btn:disabled{ opacity:.45; cursor:not-allowed; }

/* ─── Struk Modal ────────────────────────────────────── */
.struk-overlay {
    position:fixed; inset:0; background:var(--overlay-bg);
    z-index:2000; display:flex; align-items:center; justify-content:center;
    backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
}
.struk-modal {
    background:var(--bg-card); border-radius:var(--r-2xl); padding:28px;
    width:520px; max-width:calc(100vw - 32px); max-height:80vh; overflow-y:auto;
    box-shadow:var(--sh-lg); border:1px solid var(--border-card);
}
.struk-modal-header {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid var(--border-card);
}
.struk-modal-title{ font-size:14px; font-weight:600; color:var(--ink); }
.struk-modal-close {
    background:none; border:none; cursor:pointer; color:var(--ink4);
    font-size:16px; padding:4px 8px; border-radius:7px; font-family:inherit;
    transition:background var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.struk-modal-close:hover{ background:var(--a100); transform:rotate(90deg); }
.struk-item {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 14px; border-radius:var(--r-md);
    border:1px solid var(--border-card); background:var(--struk-item-bg);
    margin-bottom:8px; gap:12px;
    transition:background var(--d-fast),transform var(--d-base) var(--ease-spring),
               border-color var(--d-fast),box-shadow var(--d-fast);
}
.struk-item:hover{ background:var(--a50); transform:translateX(5px); border-color:var(--a200); box-shadow:var(--sh-xs); }
.struk-item-left{ display:flex; align-items:center; gap:10px; }
.struk-num {
    width:30px; height:30px; border-radius:9px; background:var(--a100);
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:600; color:var(--a700); flex-shrink:0;
}
.struk-proc-num{ font-size:12px; font-weight:600; color:var(--ink); }
.struk-meta{ font-size:11px; color:var(--ink4); }
.struk-amount{ font-size:12px; font-weight:600; color:var(--g600); }
.struk-view-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 12px; background:var(--g50); border:1px solid var(--g200);
    border-radius:8px; font-size:11px; font-weight:500; color:var(--g600);
    text-decoration:none; font-family:inherit;
    transition:background var(--d-fast),transform var(--d-base) var(--ease-spring);
}
.struk-view-btn:hover{ background:var(--g200); transform:translateY(-1px); }

/* ─── Grand Total Bar ────────────────────────────────── */
.grand-bar {
    margin-top:12px;
    background:linear-gradient(135deg,#92400e,#78350f);
    border-radius:var(--r-lg); padding:18px 24px;
    display:flex; align-items:center; flex-wrap:wrap; gap:12px;
    animation:cardRise var(--d-slow) var(--ease-out) .35s both;
}
.grand-divider{ width:1px; height:36px; background:rgba(255,255,255,.15); flex-shrink:0; }
.grand-progress{ height:3px; background:rgba(255,255,255,.2); border-radius:99px; margin-top:6px; overflow:hidden; }
.grand-progress-fill {
    height:100%; border-radius:99px;
    background:linear-gradient(90deg,#fbbf24,#6ee7b7);
    transition:width .8s var(--ease-out);
}

/* ─── Responsive ─────────────────────────────────────── */
@media(max-width:768px){
    .dl-format-row{ grid-template-columns:1fr 1fr; }
    .dl-date-row{ grid-template-columns:1fr; }
    .dl-date-sep{ display:none; }
    .dropdown-panel-wide{ min-width:calc(100vw - 32px); right:-16px; }
    .grand-bar{ padding:14px 18px; }
    .toolbar{ justify-content:flex-start; }
}
@media(max-width:480px){
    .dl-format-row{ grid-template-columns:1fr; }
    .filter-row{ flex-direction:column; align-items:stretch; }
    .filter-divider{ display:none; }
}

/* ─── Dark mode: date input color-scheme ─────────────── */
@media (prefers-color-scheme: dark) {
    input[type="date"] { color-scheme: dark; }
    select { color-scheme: dark; }
}
.dark input[type="date"], .dark select { color-scheme: dark; }
</style>

<div style="display:block;overflow-x:auto;overflow-y:visible;width:100%;min-width:0;-webkit-overflow-scrolling:touch;box-sizing:border-box;">
<div class="r-wrap" wire:poll.30s wire:loading.class="opacity-50" wire:target="getTableData">

    {{-- Toast loading --}}
    <div wire:loading wire:target="getTableData"
         style="position:fixed;bottom:24px;right:24px;z-index:9999;pointer-events:none;">
        <div class="toast-loading">
            <span class="spinner"></span>
            Memperbarui data…
        </div>
    </div>

@php
    $tableData=$this->getTableData();
    $grandEstimasi=0;$grandEstimasiRequested=0;$grandRealisasi=0;$grandSudah=0;$grandBelum=0;$totalItems=0;
    $realPerCompany=[];
    foreach($tableData as $items){
        foreach($items as $item){
            $grandEstimasi+=$item->total_estimasi;
            // Hanya hitung estimasi item yang sudah di-request untuk grand bar
            if($item->has_procurement || $item->is_done){
                $grandEstimasiRequested += $item->estimated_price * ($item->total_qty > 0 ? $item->total_qty : 1);
            }
            $grandRealisasi+=$item->realisasi;
            $totalItems++;
            if($item->is_done){ $grandSudah++; $key=$item->company_label??'—'; $realPerCompany[$key]=($realPerCompany[$key]??0)+$item->realisasi; }
            else $grandBelum++;
        }
    }
    $pct=$grandEstimasiRequested>0?round($grandRealisasi/$grandEstimasiRequested*100,2):0;
    $selisihG=$grandRealisasi-$grandEstimasiRequested;
@endphp

{{-- ── STAT CARDS ── --}}
<div class="stat-grid">
    <div class="stat-card s1">
        <div class="stat-accent">💰</div>
        <div class="stat-label">Estimasi Harga</div>
        <div class="stat-value">Rp{{ number_format($grandEstimasi,0,',','.') }}</div>
        <div class="stat-sub">Seluruh anggaran master beban</div>
    </div>
    <div class="stat-card s2">
        <div class="stat-accent">✅</div>
        <div class="stat-label">Total Realisasi</div>
        <div class="stat-value">Rp{{ number_format($grandRealisasi,0,',','.') }}</div>
        <div class="stat-sub">
            @if($selisihG>0)<span style="color:var(--r600);font-weight:600;">▲ Over Rp{{ number_format($selisihG,0,',','.') }}</span>
            @elseif($selisihG<0)<span style="color:var(--g600);font-weight:600;">▼ Hemat Rp{{ number_format(abs($selisihG),0,',','.') }}</span>
            @else Sesuai estimasi @endif
        </div>
    </div>
    <div class="stat-card s3">
        <div class="stat-accent">📦</div>
        <div class="stat-label">Item Terealisasi</div>
        <div class="stat-value">{{ $grandSudah }} <span>item</span></div>
        <div class="stat-sub">dari {{ $totalItems }} total item</div>
    </div>
    <div class="stat-card s4">
        <div class="stat-accent">⏳</div>
        <div class="stat-label">Belum Terealisasi</div>
        <div class="stat-value">{{ $grandBelum }} <span>item</span></div>
        <div class="stat-sub">Struk belum diunggah</div>
    </div>
</div>

{{-- ── BREAKDOWN PER PERUSAHAAN ── --}}
@if($filterCompany==='all' && count($realPerCompany) > 0)
@php
    $totalReal=array_sum($realPerCompany);
    $coChunks=array_chunk(array_map(null,array_keys($realPerCompany),array_values($realPerCompany)),2);
@endphp
<div style="margin-top:10px;margin-bottom:20px;background:var(--bg-card);border-radius:var(--r-lg);border:1px solid var(--border-cat);overflow:hidden;box-shadow:var(--sh-sm);animation:cardRise var(--d-slow) var(--ease-out) .15s both;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;background:linear-gradient(135deg,#92400e,#b45309);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:28px;height:28px;border-radius:9px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:14px;">🏢</div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#fcd34d;">Realisasi Per Perusahaan</div>
                <div style="font-size:11px;color:#fde68a;opacity:.8;margin-top:1px;">{{ count($realPerCompany) }} perusahaan</div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:10px;font-weight:500;color:#fcd34d;opacity:.8;">Total</div>
            <div style="font-size:14px;font-weight:600;color:#fff;">Rp{{ number_format($totalReal,0,',','.') }}</div>
        </div>
    </div>
    @foreach($coChunks as $pair)
    <div style="display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid var(--border-card);">
        @php [$label0,$amount0]=$pair[0];$pct0=$totalReal>0?round($amount0/$totalReal*100,1):0; @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 20px;border-right:1px solid var(--border-card);background:var(--bg-card);transition:background var(--d-fast);"
             onmouseenter="this.style.background='var(--breakdown-row-hover)'" onmouseleave="this.style.background='var(--bg-card)'">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:6px;height:6px;border-radius:50%;background:var(--a500);flex-shrink:0;"></div>
                <span style="font-size:12px;font-weight:500;color:var(--ink);">{{ $label0 }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:10px;font-weight:500;color:var(--a700);background:var(--a100);padding:2px 8px;border-radius:99px;">{{ $pct0 }}%</span>
                <span style="font-size:13px;font-weight:600;color:var(--g600);">Rp{{ number_format($amount0,0,',','.') }}</span>
            </div>
        </div>
        @if(isset($pair[1]))
            @php [$label1,$amount1]=$pair[1];$pct1=$totalReal>0?round($amount1/$totalReal*100,1):0; @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 20px;background:var(--bg-card);transition:background var(--d-fast);"
                 onmouseenter="this.style.background='var(--breakdown-row-hover)'" onmouseleave="this.style.background='var(--bg-card)'">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:6px;height:6px;border-radius:50%;background:var(--a500);flex-shrink:0;"></div>
                    <span style="font-size:12px;font-weight:500;color:var(--ink);">{{ $label1 }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:10px;font-weight:500;color:var(--a700);background:var(--a100);padding:2px 8px;border-radius:99px;">{{ $pct1 }}%</span>
                    <span style="font-size:13px;font-weight:600;color:var(--g600);">Rp{{ number_format($amount1,0,',','.') }}</span>
                </div>
            </div>
        @else
            <div style="background:var(--bg-surface);"></div>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- ── TOOLBAR ── --}}
<div class="toolbar">

    @php
        $availableCompanies=$this->getActiveCompanies();
        $companyOptions=['all'=>'Semua'];
        foreach($availableCompanies as $co){ $companyOptions[(string)$co->id]=$co->name; }
        $currentLabel=$companyOptions[$filterCompany]??'Semua';
    @endphp

    {{-- Filter Perusahaan --}}
    <div style="position:relative;" x-data="{ openC: false }" @click.outside="openC=false">
        <button class="tb-btn" @click.stop="openC=!openC" type="button"
                data-tooltip="Filter berdasarkan perusahaan">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11M8 10v11M16 10v11M12 10v11"/>
            </svg>
            {{ $currentLabel }}
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                 :style="openC?'transform:rotate(180deg);transition:transform .2s':'transition:transform .2s'">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
        <div class="dropdown-panel" x-show="openC" x-cloak @click.stop>
            @foreach($companyOptions as $val=>$lbl)
            <button class="company-option {{ $filterCompany===(string)$val?'active':'' }}"
                    wire:click="setFilterCompany('{{ $val }}')"
                    @click="openC=false" type="button">
                {{ $lbl }}
                <svg class="check" xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Filter Tanggal --}}
    <div style="position:relative;" x-data="{ openF: false }" @click.outside="openF=false">
        <button class="tb-btn tooltip-right" @click.stop="openF=!openF" type="button"
                data-tooltip="Filter berdasarkan periode">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Filter Tanggal
            @if($filterDateFrom&&$filterDateTo)
                <span style="padding:1px 7px;border-radius:99px;font-size:10px;background:var(--a100);color:var(--a700);">
                    {{ \Carbon\Carbon::parse($filterDateFrom)->format('d/m') }} — {{ \Carbon\Carbon::parse($filterDateTo)->format('d/m/Y') }}
                </span>
            @elseif($filterMonth&&$filterYear)
                <span style="padding:1px 7px;border-radius:99px;font-size:10px;background:var(--a100);color:var(--a700);">
                    {{ ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'][$filterMonth]??'' }} {{ $filterYear }}
                </span>
            @endif
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                 :style="openF?'transform:rotate(180deg);transition:transform .2s':'transition:transform .2s'">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
        <div class="dropdown-panel-wide" x-show="openF" x-cloak @click.stop>
            <div class="filter-dropdown-title">Filter Periode Procurement</div>
            <div class="filter-row">
                <div class="filter-field">
                    <label>Bulan</label>
                    <select wire:model.live="filterMonth">
                        <option value="">Semua</option>
                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $v=>$l)
                            <option value="{{ $v }}" {{ $filterMonth===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label>Tahun</label>
                    <select wire:model.live="filterYear">
                        <option value="">Semua</option>
                        @foreach(range(now()->year,now()->year-3) as $y)
                            <option value="{{ $y }}" {{ $filterYear==(string)$y?'selected':'' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-divider"></div>
                <div class="filter-field">
                    <label>Dari Tanggal</label>
                    <input type="date" wire:model.live="filterDateFrom" value="{{ $filterDateFrom }}">
                </div>
                <div style="padding-bottom:8px;color:var(--ink4);font-size:12px;">—</div>
                <div class="filter-field">
                    <label>Sampai Tanggal</label>
                    <input type="date" wire:model.live="filterDateTo" value="{{ $filterDateTo }}">
                </div>
                <div class="filter-divider"></div>
                <button class="btn-reset" type="button" wire:click="resetFilter" @click="openF=false">Reset</button>
            </div>
        </div>
    </div>

    {{-- Unduh --}}
    <div style="position:relative;" x-data="{ openD: false }" @click.outside="openD=false">
        <button class="tb-btn tooltip-right" @click.stop="openD=!openD" type="button"
                data-tooltip="Unduh laporan dalam berbagai format">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Unduh
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                 :style="openD?'transform:rotate(180deg);transition:transform .2s':'transition:transform .2s'">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
        <div class="dropdown-panel" style="min-width:185px;" x-show="openD" x-cloak @click.stop>
            <button class="dd-menu-btn" wire:click="openDownloadModal('pdf')" @click="openD=false" type="button">
                <span class="dd-menu-icon" style="background:var(--s4-accent);">📄</span> Unduh PDF
            </button>
            <button class="dd-menu-btn" wire:click="openDownloadModal('excel')" @click="openD=false" type="button">
                <span class="dd-menu-icon" style="background:var(--s2-accent);">📊</span> Unduh Excel
            </button>
            <button class="dd-menu-btn" wire:click="openDownloadModal('excel-realisasi')" @click="openD=false" type="button">
                <span class="dd-menu-icon" style="background:var(--g50);">✅</span> Unduh Realisasi Saja
            </button>
        </div>
    </div>
</div>

{{-- ── DOWNLOAD MODAL ── --}}
<div class="dl-overlay {{ $showDownloadModal?'':'hidden' }}">
    <div class="dl-modal">
        <div class="dl-modal-header">
            <div class="dl-modal-title">
                {{ $downloadType==='pdf'?'📄':'📊' }} Unduh Realisasi Pengadaan
            </div>
            <button class="dl-close-btn" wire:click="closeDownloadModal" type="button">✕</button>
        </div>
        <div class="dl-format-row">
            <div class="dl-format-card {{ $downloadType==='pdf'?'selected':'' }}" wire:click="$set('downloadType','pdf')" style="cursor:pointer;">
                <div class="dl-format-icon pdf-icon">📄</div>
                <div><div class="dl-format-name">PDF</div><div class="dl-format-desc">Siap cetak, landscape A4</div></div>
            </div>
            <div class="dl-format-card {{ $downloadType==='excel'?'selected':'' }}" wire:click="$set('downloadType','excel')" style="cursor:pointer;">
                <div class="dl-format-icon excel-icon">📊</div>
                <div><div class="dl-format-name">Excel</div><div class="dl-format-desc">Data tabel spreadsheet</div></div>
            </div>
            <div class="dl-format-card {{ $downloadType==='excel-realisasi'?'selected':'' }}" wire:click="$set('downloadType','excel-realisasi')" style="cursor:pointer;">
                <div class="dl-format-icon" style="background:var(--g50);">✅</div>
                <div><div class="dl-format-name">Realisasi</div><div class="dl-format-desc">Hanya terealisasi</div></div>
            </div>
        </div>
        <div class="dl-date-section">
            <div class="dl-section-label">Pilih Rentang Tanggal</div>
            <div class="dl-date-row">
                <div class="dl-date-field">
                    <label>Dari Tanggal</label>
                    <input type="date" wire:model.live="downloadDateFrom">
                </div>
                <div class="dl-date-sep">→</div>
                <div class="dl-date-field">
                    <label>Sampai Tanggal</label>
                    <input type="date" wire:model.live="downloadDateTo">
                </div>
            </div>
            <div class="dl-quick-ranges">
                <button class="dl-quick-btn" type="button"
                    wire:click="$set('downloadDateFrom','{{ now()->startOfMonth()->format('Y-m-d') }}')"
                    wire:then="$set('downloadDateTo','{{ now()->format('Y-m-d') }}')">Bulan Ini</button>
                <button class="dl-quick-btn" type="button"
                    wire:click="$set('downloadDateFrom','{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}')"
                    wire:then="$set('downloadDateTo','{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')">Bulan Lalu</button>
                <button class="dl-quick-btn" type="button"
                    wire:click="$set('downloadDateFrom','{{ now()->startOfYear()->format('Y-m-d') }}')"
                    wire:then="$set('downloadDateTo','{{ now()->format('Y-m-d') }}')">Tahun Ini</button>
            </div>
            @if($downloadDateFrom&&$downloadDateTo)
            <div style="margin-top:10px;padding:9px 13px;background:var(--a50);border-radius:var(--r-sm);border:1px solid var(--a200);font-size:12px;color:var(--a700);font-weight:500;">
                {{ \Carbon\Carbon::parse($downloadDateFrom)->translatedFormat('d F Y') }}
                s/d
                {{ \Carbon\Carbon::parse($downloadDateTo)->translatedFormat('d F Y') }}
            </div>
            @endif
        </div>
        <div class="dl-action-row">
            <button class="dl-cancel-btn" wire:click="closeDownloadModal" type="button">Batal</button>
            @if($downloadDateFrom&&$downloadDateTo)
                @if($downloadType==='excel-realisasi')
                    <a class="dl-confirm-btn"
                       href="{{ route('realisasi.download.terealisasi',['dateFrom'=>$downloadDateFrom,'dateTo'=>$downloadDateTo,'company'=>$filterCompany]) }}"
                       wire:click="closeDownloadModal" style="text-decoration:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Unduh Excel Realisasi
                    </a>
                @else
                    <a class="dl-confirm-btn"
                       href="{{ route('realisasi.download',['type'=>$downloadType,'dateFrom'=>$downloadDateFrom,'dateTo'=>$downloadDateTo,'company'=>$filterCompany]) }}"
                       wire:click="closeDownloadModal" style="text-decoration:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Unduh {{ $downloadType==='pdf'?'PDF':'Excel' }}
                    </a>
                @endif
            @else
                <button class="dl-confirm-btn" disabled type="button">Pilih tanggal dulu</button>
            @endif
        </div>
    </div>
</div>

{{-- ── TABEL ── --}}
<div class="table-shell">
<table class="rt">
    <thead>
        <tr class="hdr">
            <th style="text-align:center;width:36px;">#</th>
            <th style="text-align:center;min-width:100px;">Perusahaan</th>
            <th style="text-align:left;min-width:200px;">Nama Item</th>
            <th style="text-align:right;min-width:130px;">Est. Harga / Satuan</th>
            <th style="text-align:center;min-width:55px;">Unit</th>
            <th style="text-align:center;min-width:55px;">Est. QTY</th>
            <th style="text-align:right;min-width:150px;">Total Estimasi</th>
            <th style="text-align:center;min-width:70px;">Real. QTY</th>
            <th style="text-align:right;min-width:150px;">Realisasi</th>
            <th style="text-align:right;min-width:120px;">Selisih</th>
            <th style="text-align:center;min-width:180px;">Status Realisasi</th>
        </tr>
    </thead>
    <tbody>
    @php $no=0; @endphp
    @forelse($tableData as $categoryName=>$items)
        @php
            $cEst=$items->sum('total_estimasi');$cReal=$items->sum('realisasi');
            $cDone=$items->where('is_done',true)->count();$cAll=$items->count();
            $cPct=$cAll>0?round($cDone/$cAll*100):0;
            $cClr=$cPct===100?'#10b981':($cPct>0?'#d97706':'#f43f5e');
            $cHasProc = $items->where('has_procurement', true)->count() > 0 || $cDone > 0;
            $cEstProc = $items->filter(function($i){ return $i->has_procurement || $i->is_done; })
                              ->sum('total_estimasi');
            $cSelisih = $cReal - $cEstProc;
        @endphp

        {{-- ════ CATEGORY ROW — diperbaiki agar sejajar dengan 11 kolom header ════ --}}
        <tr class="cat-row">
            {{-- Kolom 1: # --}}
            <td style="text-align:center;">
                <span style="color:var(--a400);font-size:14px;">◆</span>
            </td>
            {{-- Kolom 2–5: Perusahaan + Nama Item + Est. Harga/Satuan + Unit (colspan=4) --}}
            <td colspan="4">
                <div class="cat-name-wrap">
                    <div class="cat-dot"></div>
                    <span style="font-size:12px;">{{ $categoryName }}</span>
                    <span class="cat-pill">{{ $cAll }} item</span>
                </div>
                <div class="mini-bar"><div class="mini-bar-fill" style="width:{{ $cPct }}%;"></div></div>
            </td>
            {{-- Kolom 6: Est. QTY — tampilkan progress item --}}
            <td style="text-align:center;">
                <span style="font-size:11px;color:{{ $cClr }};font-weight:600;">{{ $cDone }}/{{ $cAll }}</span>
            </td>
            {{-- Kolom 7: Total Estimasi — hanya tampil & hanya jumlahkan item yang sudah diajukan/disetujui --}}
            <td style="text-align:right;font-size:13px;">
                @if($cHasProc)
                    Rp{{ number_format($cEstProc,0,',','.') }}
                @else —@endif
            </td>
            {{-- Kolom 8: Real. QTY — kosong di level kategori --}}
            <td style="text-align:center;color:var(--ink5);">—</td>
            {{-- Kolom 9: Realisasi --}}
            <td style="text-align:right;color:#10b981;font-weight:600;font-size:13px;">
                {{ $cReal>0?'Rp'.number_format($cReal,0,',','.'):'—' }}
            </td>
            {{-- Kolom 10: Selisih --}}
            <td style="text-align:right;font-size:12px;">
                @if($cReal>0)
                    @if($cSelisih>0)<span style="color:var(--r600);font-weight:600;">▲ +Rp{{ number_format($cSelisih,0,',','.') }}</span>
                    @elseif($cSelisih<0)<span style="color:var(--g600);font-weight:600;">▼ Rp{{ number_format($cSelisih,0,',','.') }}</span>
                    @else<span style="color:var(--ink4);">Rp0</span>@endif
                @else<span style="color:var(--ink5);">—</span>@endif
            </td>
            {{-- Kolom 11: Status Realisasi — tampilkan persentase kategori --}}
            <td style="text-align:center;">
                <span style="font-weight:600;font-size:12px;color:{{ $cClr }};">{{ $cPct }}%</span>
            </td>
        </tr>
        {{-- ════ END CATEGORY ROW ════ --}}

        @foreach($items as $item)
        @php
            $no++;
            if($item->is_done){ $rowClass='purchased';$badgeClass='sbadge-done';$badgeLabel='✅ Sudah Terealisasi';$badgeSub=$item->struk_date?'Struk: '.\Carbon\Carbon::parse($item->struk_date)->format('d/m/Y'):'Struk telah diunggah'; }
            elseif($item->has_procurement){ $rowClass='pending';$badgeClass='sbadge-wait';$badgeLabel='⏳ Belum Terealisasi';$badgeSub='Disetujui, struk belum diunggah'; }
            else{ $rowClass='no-proc';$badgeClass='sbadge-no';$badgeLabel='— Belum Diajukan';$badgeSub='Belum ada procurement'; }
        @endphp
        <tr class="dr {{ $rowClass }}">
            <td class="no-col">{{ $no }}</td>
            <td style="text-align:center;">
                @if($item->is_done||$item->has_procurement)
                    <span class="company-label-text">{{ $item->company_label }}</span>
                @else
                    <span style="color:var(--ink4);font-size:13px;">—</span>
                @endif
            </td>
            <td>
                <div class="iname">{{ $item->item_name }}</div>
                @if($item->specification)<div class="ispec">{{ $item->specification }}</div>@endif
                @if($item->vendor)<span class="vtag">🏪 {{ $item->vendor }}</span>@endif
            </td>
            <td style="text-align:right;font-size:13px;">Rp{{ number_format($item->estimated_price,0,',','.') }}</td>
            <td style="text-align:center;color:var(--ink3);font-size:13px;">{{ $item->unit??'—' }}</td>
            <td style="text-align:center;font-weight:500;font-size:13px;">
                {{ ($item->show_est_qty&&$item->est_qty>0)?$item->est_qty:'—' }}
            </td>
            <td style="text-align:right;font-size:13px;">
                @if($item->is_done || $item->has_procurement)
                    @php $procTotal = $item->estimated_price * ($item->total_qty > 0 ? $item->total_qty : 1); @endphp
                    Rp{{ number_format($procTotal,0,',','.') }}
                @else —@endif
            </td>
            <td style="text-align:center;font-size:13px;">
                @if($item->is_done&&$item->realisasi_qty>0)
                    <span class="rqty-badge">{{ $item->realisasi_qty }}</span>
                    @if($item->total_qty>0&&$item->realisasi_qty!=$item->total_qty)
                        <div style="font-size:10px;margin-top:3px;color:var(--r600);">
                            {{ $item->realisasi_qty>$item->total_qty?'▲ lebih':'▼ kurang' }}
                        </div>
                    @endif
                @else —@endif
            </td>
            <td style="text-align:right;font-size:13px;">
                @if($item->is_done)
                    <span style="color:var(--g600);font-weight:600;">Rp{{ number_format($item->realisasi,0,',','.') }}</span>
                @else —@endif
            </td>
            <td style="text-align:right;">
                @if($item->is_done&&$item->selisih!==null)
                    @if($item->selisih>0)
                        <span class="selisih-over">▲ +Rp{{ number_format($item->selisih,0,',','.') }}</span>
                        <div style="font-size:10px;color:var(--r600);opacity:.7;">Over budget</div>
                    @elseif($item->selisih<0)
                        <span class="selisih-under">▼ Rp{{ number_format(abs($item->selisih),0,',','.') }}</span>
                        <div style="font-size:10px;color:var(--g600);opacity:.7;">Under budget</div>
                    @else
                        <span class="selisih-even">Rp0</span>
                    @endif
                @else —@endif
            </td>
            <td style="text-align:center;padding:6px 8px;">
                <div class="sbadge {{ $badgeClass }}">
                    {{ $badgeLabel }}<span class="sbadge-sub">{{ $badgeSub }}</span>
                </div>
                @if($item->struk_list&&$item->struk_list->isNotEmpty())
                    <button class="struk-btn" onclick="openStrukModal({{ $no }})" type="button">
                        🧾 Lihat Struk ({{ $item->struk_list->count() }})
                    </button>
                    <div id="struk-data-{{ $no }}" style="display:none;" data-item="{{ $item->item_name }}">
                        @foreach($item->struk_list as $struk)
                        @php
                            $strukcUrl=$struk->url??'';
                            $parsedStruk=[];
                            if($strukcUrl&&filter_var($strukcUrl,FILTER_VALIDATE_URL)){
                                $parsedStruk=parse_url($strukcUrl);
                                $currentHost=request()->getSchemeAndHttpHost();
                                $strukcPath=$parsedStruk['path']??'';
                                $strukcQuery=isset($parsedStruk['query'])?'?'.$parsedStruk['query']:'';
                                $strukcUrl=$currentHost.$strukcPath.$strukcQuery;
                            }
                        @endphp
                        <div class="struk-entry"
                             data-no="{{ $struk->no }}"
                             data-url="{{ $strukcUrl }}"
                             data-date="{{ $struk->date?\Carbon\Carbon::parse($struk->date)->format('d/m/Y H:i'):'-' }}"
                             data-amount="{{ $struk->amount>0?'Rp'.number_format($struk->amount,0,',','.'):'-' }}"
                             data-proc="{{ $struk->proc_number }}"
                             data-qty="{{ $struk->qty }}"
                             data-filename="{{ basename($parsedStruk['path']??$struk->url??'struk') }}">
                        </div>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
        @endforeach

    @empty
        <tr>
            <td colspan="11" style="padding:64px;text-align:center;">
                <div style="font-size:36px;opacity:.2;margin-bottom:14px;">📋</div>
                <div style="font-size:13px;font-weight:500;color:var(--ink2);">Belum ada master data beban</div>
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- ── GRAND TOTAL BAR ── --}}
@if($tableData->isNotEmpty())
<div class="grand-bar">
    <div style="display:flex;align-items:center;gap:9px;margin-right:8px;">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:16px;">📊</div>
        <div>
            <div style="font-size:10px;font-weight:600;color:#fcd34d;">Grand Total</div>
            <div style="font-size:11px;color:#fde68a;margin-top:1px;">{{ $totalItems }} item · {{ $grandSudah }} terealisasi</div>
        </div>
    </div>
    <div class="grand-divider"></div>
    <div style="flex:1;min-width:160px;">
        <div style="font-size:10px;font-weight:500;color:#fcd34d;">Total Estimasi</div>
        <div style="font-size:15px;font-weight:600;color:#fff;margin-top:2px;">Rp{{ number_format($grandEstimasiRequested,0,',','.') }}</div>
    </div>
    <div class="grand-divider"></div>
    <div style="flex:1;min-width:160px;">
        <div style="font-size:10px;font-weight:500;color:#fcd34d;">Total Realisasi</div>
        <div style="font-size:15px;font-weight:600;color:#6ee7b7;margin-top:2px;">Rp{{ number_format($grandRealisasi,0,',','.') }}</div>
    </div>
    <div class="grand-divider"></div>
    <div style="flex:1;min-width:140px;">
        <div style="font-size:10px;font-weight:500;color:#fcd34d;">Selisih</div>
        <div style="font-size:15px;font-weight:600;margin-top:2px;">
            @if($selisihG>0)<span style="color:#fca5a5;">▲ +Rp{{ number_format($selisihG,0,',','.') }}</span>
            @elseif($selisihG<0)<span style="color:#6ee7b7;">▼ Rp{{ number_format(abs($selisihG),0,',','.') }}</span>
            @else<span style="color:#fde68a;">Rp0</span>@endif
        </div>
        <div style="font-size:10px;color:#fde68a;margin-top:2px;opacity:.7;">
            @if($selisihG>0) Over budget
            @elseif($selisihG<0) Hemat / Under budget
            @else Sesuai estimasi @endif
        </div>
    </div>
    <div class="grand-divider"></div>
    <div style="min-width:100px;text-align:center;">
        <div style="font-size:10px;font-weight:500;color:#fcd34d;">Realisasi</div>
        <div style="font-size:22px;font-weight:600;color:#fff;line-height:1;margin-top:2px;">{{ $pct }}<span style="font-size:13px;font-weight:400;">%</span></div>
        <div class="grand-progress">
            <div class="grand-progress-fill" style="width:{{ min($pct,100) }}%;"></div>
        </div>
    </div>
</div>
@endif

</div>{{-- /r-wrap --}}

{{-- ── STRUK MODAL ── --}}
<div id="struk-modal-overlay" class="struk-overlay" style="display:none;"
     onclick="if(event.target===this)closeStrukModal()">
    <div class="struk-modal">
        <div class="struk-modal-header">
            <div class="struk-modal-title">🧾 Daftar Struk — <span id="struk-modal-item-name" style="color:var(--a700);"></span></div>
            <button class="struk-modal-close" onclick="closeStrukModal()" type="button">✕</button>
        </div>
        <div id="struk-modal-body"></div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    'use strict';

    function normalizeStrukUrl(url) {
        if (!url || url === 'null' || url === '') return '';
        try {
            var p = new URL(url);
            p.host = window.location.host;
            p.protocol = window.location.protocol;
            return p.toString();
        } catch(e){ return url; }
    }

    window.downloadStruk = function(url, filename) {
        var btn  = event.currentTarget;
        var orig = btn.innerHTML;
        var spinSt = 'display:inline-block;width:12px;height:12px;border:2px solid rgba(29,78,216,.3);border-radius:50%;border-top-color:#1d4ed8;animation:__spin .7s linear infinite;margin-right:5px;vertical-align:middle;';
        btn.innerHTML = '<span style="'+spinSt+'"></span>Mengunduh…';
        btn.style.opacity = '.65'; btn.style.pointerEvents = 'none';

        var norm = normalizeStrukUrl(url);
        fetch(norm, { method:'GET', credentials:'same-origin' })
            .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.blob(); })
            .then(function(blob){
                var bUrl = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = bUrl; a.download = filename || 'struk';
                document.body.appendChild(a); a.click();
                document.body.removeChild(a); URL.revokeObjectURL(bUrl);
                btn.innerHTML = '✅ Selesai';
                setTimeout(function(){ btn.innerHTML=orig; btn.style.opacity=''; btn.style.pointerEvents=''; }, 1500);
            })
            .catch(function(){
                btn.innerHTML='❌ Gagal'; btn.style.opacity=''; btn.style.pointerEvents='';
                window.open(norm,'_blank');
                setTimeout(function(){ btn.innerHTML=orig; }, 2000);
            });
    };

    window.openStrukModal = function(rowNo) {
        var dataEl = document.getElementById('struk-data-' + rowNo);
        if (!dataEl) return;
        document.getElementById('struk-modal-item-name').textContent = dataEl.getAttribute('data-item');
        var entries = dataEl.querySelectorAll('.struk-entry');
        var html = '';
        entries.forEach(function(e, i){
            var url = normalizeStrukUrl(e.getAttribute('data-url'));
            var fn  = e.getAttribute('data-filename') || 'struk';
            var has = url && url !== 'null' && url !== '';
            var btns = has
                ? '<div style="display:flex;gap:6px;flex-shrink:0;">'
                    + '<a href="'+url+'" target="_blank" class="struk-view-btn" rel="noopener">🔍 Buka</a>'
                    + '<button onclick="downloadStruk(\''+url.replace(/'/g,"\\'")+'\',\''+fn.replace(/'/g,"\\'")+'\''+')" class="struk-view-btn" style="background:var(--b50);border-color:var(--b200);color:var(--b700);cursor:pointer;font-family:inherit;">⬇️ Unduh</button>'
                  + '</div>'
                : '<span style="font-size:11px;color:var(--ink5);">Tidak ada file</span>';
            html += '<div class="struk-item" style="animation:__siIn .28s var(--ease-spring) '+(i*.06)+'s both;">'
                  +   '<div class="struk-item-left">'
                  +     '<div class="struk-num">'+e.getAttribute('data-no')+'</div>'
                  +     '<div><div class="struk-proc-num">📄 '+e.getAttribute('data-proc')+'</div>'
                  +     '<div class="struk-meta">📦 Qty: '+e.getAttribute('data-qty')+' · 🕐 '+e.getAttribute('data-date')+'</div>'
                  +     '<div class="struk-amount">'+e.getAttribute('data-amount')+'</div></div>'
                  +   '</div>'+btns
                  + '</div>';
        });
        document.getElementById('struk-modal-body').innerHTML = html;
        var ov = document.getElementById('struk-modal-overlay');
        ov.style.display = 'flex';
        ov.style.animation = '__ovIn .22s var(--ease-out)';
        document.body.style.overflow = 'hidden';
    };

    window.closeStrukModal = function() {
        var ov = document.getElementById('struk-modal-overlay');
        ov.style.animation = '__ovOut .18s var(--ease-io) forwards';
        setTimeout(function(){
            ov.style.display='none'; ov.style.animation='';
            document.body.style.overflow='';
        }, 190);
    };

    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeStrukModal(); });

    if (!document.getElementById('__rp_kf__')) {
        var s = document.createElement('style');
        s.id = '__rp_kf__';
        s.textContent = '@keyframes __ovIn{from{opacity:0}to{opacity:1}}'
                      + '@keyframes __ovOut{from{opacity:1}to{opacity:0}}'
                      + '@keyframes __siIn{from{opacity:0;transform:translateX(-8px)}to{opacity:1;transform:none}}'
                      + '@keyframes __spin{to{transform:rotate(360deg)}}';
        document.head.appendChild(s);
    }

    function adjustTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(function(el){
            var rect   = el.getBoundingClientRect();
            var tipW   = (el.getAttribute('data-tooltip')||'').length * 6 + 24;
            var center = rect.left + rect.width / 2;
            el.classList.remove('tooltip-left','tooltip-right');
            if ((center + tipW/2) > window.innerWidth - 8) el.classList.add('tooltip-right');
            else if ((center - tipW/2) < 8)                el.classList.add('tooltip-left');
        });
    }

    function fixScroll() {
        document.querySelectorAll('.table-shell').forEach(function(t){
            t.style.setProperty('overflow-x','auto','important');
            t.style.setProperty('display','block','important');
            var el=t.parentElement, d=0;
            while(el && el!==document.body && d<20){
                var cs=window.getComputedStyle(el);
                if(cs.overflowX==='hidden'||cs.overflow==='hidden'){
                    el.style.setProperty('overflow-x','auto','important');
                    el.style.setProperty('min-width','0','important');
                }
                el=el.parentElement; d++;
            }
        });
    }

    function init(){ adjustTooltips(); fixScroll(); }

    document.readyState==='loading'
        ? document.addEventListener('DOMContentLoaded',init)
        : init();

    ['livewire:navigated','livewire:update','livewire:load'].forEach(function(ev){
        document.addEventListener(ev,function(){ setTimeout(init,50); });
    });

    var _rzTimer;
    window.addEventListener('resize',function(){ clearTimeout(_rzTimer); _rzTimer=setTimeout(init,120); },{ passive:true });

    setTimeout(init, 280);
    setTimeout(init, 900);
})();
</script>
@endpush

</div>{{-- /outer wrapper --}}

</div>{{-- /ROOT ELEMENT TUNGGAL --}}