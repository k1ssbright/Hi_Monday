<style>
/* Tabs */
.nav-tabs-custom { border-bottom: 1px solid #e2e8f0; }
.nav-tabs-custom .nav-link {
    border: 0; color: #64748b; font-weight: 600; font-size: .875rem;
    padding: .75rem 1rem; border-bottom: 2px solid transparent;
}
.nav-tabs-custom .nav-link.active { color: #2563eb; background: transparent; border-bottom-color: #2563eb; }
.nav-tabs-custom .nav-link:hover { color: #1d4ed8; }

/* Port of stitch_sales_timeline_widget_card/code.html */
.sj-timeline { font-size: .8125rem; color: #1e293b; -webkit-font-smoothing: antialiased; }
.sj-timeline .font-code,
.sj-timeline .sj-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.sj-timeline .sj-card {
    background: #fff; border: 1px solid rgba(226,232,240,.9); border-radius: 1rem;
    overflow: hidden; box-shadow: 0 20px 25px -5px rgba(148,163,184,.28), 0 8px 10px -6px rgba(148,163,184,.2);
}
.sj-timeline .sj-header {
    padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;
    background: linear-gradient(90deg, #fff, #fff, rgba(248,250,252,.6));
}
.sj-timeline .sj-icon {
    width: 2.75rem; height: 2.75rem; border-radius: .75rem; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center; color: #fff;
    background: linear-gradient(135deg, #2563eb, #4f46e5, #4338ca);
    box-shadow: 0 4px 6px -1px rgba(59,130,246,.25);
}
.sj-timeline .sj-icon svg { width: 1.5rem; height: 1.5rem; }
.sj-timeline .sj-title {
    margin: 0; font-size: 1.125rem; line-height: 1.35; font-weight: 700; color: #0f172a; letter-spacing: -.015em;
}
.sj-timeline .sj-pill {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .125rem .625rem; border-radius: 999px; font-size: .75rem; font-weight: 600;
    background: #eef2ff; color: #4338ca; border: 1px solid rgba(199,210,254,.7);
}
.sj-timeline .sj-pill i { width: .375rem; height: .375rem; border-radius: 999px; background: #4f46e5; display: inline-block; }
.sj-timeline .sj-sub { margin: .125rem 0 0; font-size: .75rem; color: #64748b; font-weight: 400; }
.sj-timeline .sj-sub strong { color: #1e293b; font-weight: 600; font-size: 12px; }

.sj-timeline .sj-up {
    padding: 1.5rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(180deg, rgba(248,250,252,.4), #fff);
}
@media (min-width: 768px) { .sj-timeline .sj-up { padding-left: 2rem; padding-right: 2rem; } }
.sj-timeline .sj-up-label {
    display: flex; align-items: center; gap: .5rem;
    font-size: .75rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #64748b;
}
.sj-timeline .sj-up-label i { width: .5rem; height: .5rem; border-radius: .125rem; background: #2563eb; display: inline-block; }
.sj-timeline .sj-confirmed {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .75rem; font-weight: 500; color: #047857; background: #ecfdf5;
    border: 1px solid rgba(167,243,208,.8); padding: .125rem .625rem; border-radius: 999px;
}
.sj-timeline .sj-stepper { position: relative; min-width: 760px; padding: .75rem 0; }
.sj-timeline .sj-stepper-line {
    position: absolute; top: 32px; left: 10%; right: 10%; height: .25rem; border-radius: 999px;
    background: #cbd5e1; z-index: 0; pointer-events: none;
}
.sj-timeline .sj-stepper-line.is-on { background: #2563eb; }
.sj-timeline .sj-steps {
    position: relative; z-index: 1; display: grid; grid-template-columns: repeat(4, minmax(0,1fr));
    list-style: none; margin: 0; padding: 0; align-items: start; text-align: center;
}
.sj-timeline .sj-step-a {
    display: flex; flex-direction: column; align-items: center; text-decoration: none !important; color: inherit;
    cursor: pointer;
}
.sj-timeline .sj-step-a.is-off { pointer-events: none; opacity: .55; }
.sj-timeline .sj-dot {
    width: 2.5rem; height: 2.5rem; border-radius: 999px; border: 2px solid #cbd5e1; background: #fff;
    color: #94a3b8; display: inline-flex; align-items: center; justify-content: center;
    box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: box-shadow .15s ease;
}
.sj-timeline .sj-step-a:hover .sj-dot { box-shadow: 0 0 0 4px #dbeafe; }
.sj-timeline .sj-step-a.is-done .sj-dot { border-color: #2563eb; color: #2563eb; }
.sj-timeline .sj-step-a.is-now .sj-dot {
    background: #2563eb; border-color: #1d4ed8; color: #fff;
    box-shadow: 0 4px 6px -1px rgba(37,99,235,.3), 0 0 0 2px #dbeafe;
}
.sj-timeline .sj-step-a.is-now:hover .sj-dot { box-shadow: 0 4px 6px -1px rgba(37,99,235,.3), 0 0 0 4px #bfdbfe; }
.sj-timeline .sj-k {
    display: block; margin-top: .625rem; font-size: .75rem; font-weight: 700; letter-spacing: .025em; color: #172554;
}
.sj-timeline .sj-step-a.is-now .sj-k { font-weight: 800; color: #1e3a8a; text-transform: uppercase; }
.sj-timeline .sj-l { display: block; font-size: 11px; color: #64748b; font-weight: 500; }
.sj-timeline .sj-step-a.is-now .sj-l { color: #334155; font-weight: 600; }
.sj-timeline .sj-no {
    display: inline-block; margin-top: .125rem; font-size: 11px; color: #475569; background: #f1f5f9;
    padding: .125rem .375rem; border-radius: .25rem;
}
.sj-timeline .sj-step-a.is-now .sj-no {
    color: #1d4ed8; background: #eff6ff; border: 1px solid #bfdbfe; font-weight: 700;
}

.sj-timeline .sj-lanes { padding: 1.5rem; background: rgba(248,250,252,.5); }
@media (min-width: 768px) { .sj-timeline .sj-lanes { padding: 2rem; } }
.sj-timeline .sj-lanes-h2 {
    margin: 0; font-size: .875rem; font-weight: 700; color: #0f172a; letter-spacing: -.01em;
    display: inline-flex; align-items: center; gap: .375rem;
}
.sj-timeline .sj-lanes-h2 svg { width: 1rem; height: 1rem; color: #2563eb; }
.sj-timeline .sj-lanes-hint { font-size: 11px; color: #64748b; font-weight: 400; margin-left: .25rem; }
.sj-timeline .sj-lanes-p { margin: .125rem 0 0; font-size: .75rem; color: #64748b; }
.sj-timeline .sj-leg { display: flex; align-items: center; gap: .75rem; font-size: .75rem; }
.sj-timeline .sj-leg span { display: inline-flex; align-items: center; gap: .375rem; color: #475569; }
.sj-timeline .sj-leg span.muted { color: #94a3b8; }
.sj-timeline .sj-leg b {
    width: .625rem; height: .625rem; border-radius: 999px; display: inline-block;
}
.sj-timeline .sj-leg .g { background: #10b981; }
.sj-timeline .sj-leg .b { background: #2563eb; }
.sj-timeline .sj-leg .s { background: #cbd5e1; }

.sj-timeline .sj-branch { min-width: 880px; }
.sj-timeline .sj-stack { display: flex; flex-direction: column; gap: 1rem; position: relative; }
.sj-timeline .sj-lane-row {
    display: flex;
    align-items: stretch;
    gap: 0;
}
.sj-timeline .sj-join {
    width: 3.5rem;
    flex-shrink: 0;
    position: relative;
    pointer-events: none;
    user-select: none;
}
.sj-timeline .sj-join-trunk {
    position: absolute;
    left: 9px;
    width: 3px;
    background: #2563eb;
    border-radius: 999px;
}
.sj-timeline .sj-lane-row:not(:first-child) .sj-join-trunk.is-top {
    top: 0;
    height: 50%;
}
.sj-timeline .sj-lane-row:not(:last-child) .sj-join-trunk.is-bot {
    top: 50%;
    height: 50%;
}
.sj-timeline .sj-join-node {
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateY(-50%);
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: #2563eb;
    z-index: 2;
    box-shadow: 0 0 0 3px rgba(219, 234, 254, .9);
}
.sj-timeline .sj-join-arm {
    position: absolute;
    left: 14px;
    right: 0;
    top: 50%;
    height: 2.5px;
    transform: translateY(-50%);
    border-radius: 999px;
    background: #cbd5e1;
    z-index: 1;
}
.sj-timeline .sj-join.is-paid .sj-join-arm { background: #10b981; height: 2.5px; }
.sj-timeline .sj-join.is-active .sj-join-arm { background: #2563eb; height: 3px; }
.sj-timeline .sj-join.is-upcoming .sj-join-arm {
    background: transparent;
    border-top: 2px dashed #cbd5e1;
    height: 0;
}
.sj-timeline .sj-lane-row .sj-lane { flex: 1; min-width: 0; }

.sj-timeline .sj-lane {
    position: relative; overflow: hidden; background: #fff; border: 1px solid rgba(226,232,240,.95);
    border-radius: .75rem; padding: 1rem; box-shadow: 0 1px 2px rgba(15,23,42,.04);
    transition: box-shadow .15s ease, opacity .15s ease;
}
.sj-timeline .sj-lane:hover { box-shadow: 0 4px 6px -1px rgba(15,23,42,.08); }
.sj-timeline .sj-rail {
    position: absolute; left: 0; top: 0; bottom: 0; width: .375rem;
}
.sj-timeline .sj-lane.is-paid { border-color: rgba(167,243,208,.9); }
.sj-timeline .sj-lane.is-paid .sj-rail { background: #10b981; }
.sj-timeline .sj-lane.is-active {
    background: rgba(239,246,255,.4); border: 2px solid #3b82f6;
    box-shadow: 0 4px 6px -1px rgba(59,130,246,.1), 0 0 0 4px rgba(219,234,254,.5);
}
.sj-timeline .sj-lane.is-active .sj-rail { background: #2563eb; }
.sj-timeline .sj-lane.is-upcoming {
    border-style: dashed; border-color: #cbd5e1; opacity: .75;
}
.sj-timeline .sj-lane.is-upcoming:hover { opacity: 1; }
.sj-timeline .sj-tag {
    position: absolute; top: .5rem; right: .75rem; z-index: 2;
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .125rem .5rem; border-radius: .25rem; font-size: 10px; font-weight: 800;
    letter-spacing: .04em; text-transform: uppercase; background: #2563eb; color: #fff;
    box-shadow: 0 1px 2px rgba(37,99,235,.2);
}
.sj-timeline .sj-tag .ping {
    width: .375rem; height: .375rem; border-radius: 999px; background: #fff;
    animation: sj-ping 1.4s cubic-bezier(0,0,.2,1) infinite;
}
.sj-timeline .sj-num {
    width: 1.25rem; height: 1.25rem; border-radius: 999px; display: inline-flex;
    align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; flex-shrink: 0;
}
.sj-timeline .sj-lane.is-paid .sj-num { background: #d1fae5; color: #047857; }
.sj-timeline .sj-lane.is-active .sj-num { background: #2563eb; color: #fff; }
.sj-timeline .sj-lane.is-upcoming .sj-num { background: #e2e8f0; color: #475569; }
.sj-timeline .sj-ht { margin: 0; font-size: .75rem; font-weight: 700; color: #0f172a; }
.sj-timeline .sj-lane.is-active .sj-ht { color: #172554; }
.sj-timeline .sj-lane.is-upcoming .sj-ht { color: #334155; }
.sj-timeline .sj-amt { font-size: .75rem; font-weight: 700; color: #1e293b; }
.sj-timeline .sj-lane.is-active .sj-amt { font-size: .875rem; font-weight: 900; color: #1e3a8a; }
.sj-timeline .sj-pct { font-size: 11px; color: #64748b; font-weight: 500; }
.sj-timeline .sj-lane.is-active .sj-pct { color: #1d4ed8; font-weight: 600; }
.sj-timeline .sj-chip {
    display: inline-flex; align-items: center; gap: .25rem; margin-top: .375rem;
    padding: .125rem .5rem; border-radius: .25rem; font-size: 11px; font-weight: 600; border: 1px solid transparent;
}
.sj-timeline .sj-chip.paid { color: #047857; background: #ecfdf5; border-color: rgba(167,243,208,.8); }
.sj-timeline .sj-chip.act { color: #1e40af; background: rgba(219,234,254,.7); border-color: rgba(147,197,253,.7); }
.sj-timeline .sj-chip.wait { color: #64748b; background: #f1f5f9; font-weight: 500; }

.sj-timeline .sj-flow { position: relative; padding: .25rem 0; }
.sj-timeline .sj-track {
    position: absolute; top: 17px; left: 2rem; right: 2rem; height: .25rem; border-radius: 999px;
    background: #e2e8f0; z-index: 0; overflow: hidden;
}
.sj-timeline .sj-track > i { display: block; height: 100%; border-radius: 999px; background: #2563eb; }
.sj-timeline .sj-lane.is-paid .sj-track { background: #10b981; }
.sj-timeline .sj-lane.is-paid .sj-track > i { width: 100% !important; background: #10b981; }
.sj-timeline .sj-lane.is-upcoming .sj-track {
    height: 0; border-top: 1px dashed #cbd5e1; background: transparent; top: 17px;
}
.sj-timeline .sj-lane.is-upcoming .sj-track > i { display: none; }
.sj-timeline .sj-grid {
    position: relative; z-index: 1; display: grid; gap: .5rem; text-align: center;
}
.sj-timeline .sj-grid.c3 { grid-template-columns: repeat(3, minmax(0,1fr)); }
.sj-timeline .sj-grid.c4 { grid-template-columns: repeat(4, minmax(0,1fr)); }
.sj-timeline .sj-nd {
    display: flex; flex-direction: column; align-items: center; text-decoration: none !important; color: inherit;
}
.sj-timeline .sj-nd.is-pend { opacity: .75; }
.sj-timeline .sj-nd.is-focus { position: relative; top: -.25rem; }
.sj-timeline .sj-ball {
    width: 2rem; height: 2rem; border-radius: 999px; border: 2px solid #cbd5e1; background: #fff;
    color: #94a3b8; display: inline-flex; align-items: center; justify-content: center;
}
.sj-timeline .sj-lane.is-paid .sj-nd.is-done .sj-ball {
    background: #10b981; border-color: #10b981; color: #fff; box-shadow: 0 1px 2px rgba(16,185,129,.25);
}
.sj-timeline .sj-lane.is-paid .sj-nd.is-done:last-child .sj-ball,
.sj-timeline .sj-lane.is-paid .sj-nd.is-done.is-last .sj-ball { box-shadow: 0 0 0 2px #a7f3d0; }
.sj-timeline .sj-lane.is-active .sj-nd.is-done .sj-ball {
    background: #fff; border-color: #2563eb; color: #2563eb; box-shadow: 0 1px 2px rgba(15,23,42,.05);
}
.sj-timeline .sj-nd.is-focus .sj-ball {
    width: 2.25rem; height: 2.25rem; background: #1d4ed8; border-color: #1d4ed8; color: #fff;
    box-shadow: 0 4px 6px -1px rgba(37,99,235,.4); animation: sj-pulse 2.2s cubic-bezier(.45,0,.55,1) infinite;
}
.sj-timeline .sj-nd.is-pend .sj-ball { border-color: #cbd5e1; color: #94a3b8; }
.sj-timeline .sj-lane.is-upcoming .sj-ball { border-width: 1px; }
.sj-timeline .sj-nl {
    display: block; margin-top: .25rem; font-size: 11px; font-weight: 700; color: #1e293b;
}
.sj-timeline .sj-nd.is-focus .sj-nl {
    font-weight: 800; color: #172554; text-transform: uppercase; letter-spacing: -.01em;
}
.sj-timeline .sj-nd.is-pend .sj-nl { font-weight: 500; color: #475569; }
.sj-timeline .sj-lane.is-upcoming .sj-nl { color: #64748b; font-weight: 400; }
.sj-timeline .sj-nn {
    display: inline-block; margin-top: .125rem; font-size: 10px; padding: 0 .25rem; border-radius: .25rem;
}
.sj-timeline .sj-lane.is-paid .sj-nd.is-done .sj-nn {
    color: #065f46; background: #ecfdf5; font-weight: 500;
}
.sj-timeline .sj-lane.is-active .sj-nd.is-done .sj-nn {
    color: #334155; background: #f1f5f9; font-weight: 500;
}
.sj-timeline .sj-nd.is-focus .sj-nn {
    color: #1e40af; background: #dbeafe; font-weight: 700; border: 1px solid #bfdbfe;
    padding: .125rem .375rem; box-shadow: 0 1px 2px rgba(37,99,235,.1);
}
.sj-timeline .sj-nd.is-pend .sj-nn { color: #94a3b8; background: transparent; padding: 0; }

.sj-timeline .sj-foot {
    background: #fff; border-top: 1px solid rgba(226,232,240,.9);
    padding: 1.25rem 1.25rem 1.5rem;
}
@media (min-width: 768px) { .sj-timeline .sj-foot { padding: 1.5rem 2rem; } }
.sj-timeline .sj-ratio-t { font-size: .75rem; font-weight: 600; color: #1e293b; display: inline-flex; align-items: center; gap: .375rem; }
.sj-timeline .sj-ratio-t svg { width: 1rem; height: 1rem; color: #64748b; }
.sj-timeline .sj-ratio-leg { display: flex; flex-wrap: wrap; gap: .75rem; font-size: 11px; font-weight: 500; }
.sj-timeline .sj-bar {
    width: 100%; height: .625rem; background: #f1f5f9; border-radius: 999px; overflow: hidden; display: flex;
}
.sj-timeline .sj-bar > span { display: block; height: 100%; }
.sj-timeline .sj-bar .p { background: #10b981; }
.sj-timeline .sj-bar .a { background: #2563eb; animation: sj-fade 1.8s ease-in-out infinite; }
.sj-timeline .sj-bar .u { background: #cbd5e1; }

.sj-timeline .sj-panel {
    background: rgba(248,250,252,.8); border: 1px solid rgba(226,232,240,.9);
    border-radius: .75rem; padding: 1rem;
}
@media (min-width: 768px) { .sj-timeline .sj-panel { padding: 1.25rem; } }
.sj-timeline .sj-panel-ic {
    padding: .75rem; background: rgba(219,234,254,.7); color: #1d4ed8; border-radius: .75rem;
    box-shadow: 0 1px 2px rgba(37,99,235,.08); flex-shrink: 0;
}
.sj-timeline .sj-panel-ic svg { width: 1.5rem; height: 1.5rem; }
.sj-timeline .sj-panel-due {
    margin: .25rem 0 0; font-size: .75rem; color: #64748b;
    display: flex; align-items: center; gap: .375rem;
}
.sj-timeline .sj-panel-due i {
    width: .5rem; height: .5rem; border-radius: 999px; background: #f59e0b;
    display: inline-block; animation: sj-fade 1.5s ease-in-out infinite;
}
.sj-timeline .sj-mid {
    border-top: 1px solid rgba(226,232,240,.7); border-bottom: 1px solid rgba(226,232,240,.7);
    padding: .75rem 0;
}
@media (min-width: 992px) {
    .sj-timeline .sj-mid {
        border-top: 0; border-bottom: 0; border-left: 1px solid rgba(226,232,240,.7);
        border-right: 1px solid rgba(226,232,240,.7); padding: 0 1.25rem;
    }
}
.sj-timeline .sj-mid .lab { display: block; font-size: 11px; font-weight: 500; color: #64748b; }
.sj-timeline .sj-mid .val { font-size: 1.125rem; font-weight: 700; color: #0f172a; letter-spacing: -.02em; }
.sj-timeline .sj-mid .due { font-size: .875rem; font-weight: 700; color: #dc2626; display: inline-flex; align-items: center; gap: .25rem; }
.sj-timeline .sj-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5rem .75rem; border-radius: .5rem; font-size: .75rem; font-weight: 600;
    text-decoration: none !important; border: 1px solid transparent; transition: background .15s ease, color .15s ease;
}
.sj-timeline .sj-btn svg { width: 1rem; height: 1rem; }
.sj-timeline .sj-btn.g {
    color: #334155; background: #fff; border-color: #e2e8f0; box-shadow: 0 1px 2px rgba(15,23,42,.04);
}
.sj-timeline .sj-btn.g:hover { background: #f1f5f9; color: #0f172a; }
.sj-timeline .sj-btn.p { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
.sj-timeline .sj-btn.p:hover { background: #dbeafe; }
.sj-timeline .sj-btn.s {
    color: #fff; background: #059669; box-shadow: 0 1px 2px rgba(5,150,105,.25);
}
.sj-timeline .sj-btn.s:hover { background: #047857; color: #fff; }

@keyframes sj-pulse {
    0% { transform: scale(.95); box-shadow: 0 0 0 0 rgba(37,99,235,.45); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(37,99,235,0); }
    100% { transform: scale(.95); box-shadow: 0 0 0 0 rgba(37,99,235,0); }
}
@keyframes sj-fade { 0%,100% { opacity: 1; } 50% { opacity: .7; } }
@keyframes sj-ping { 75%,100% { transform: scale(1.8); opacity: 0; } }

@media (max-width: 991.98px) {
    .sj-timeline .sj-join { width: 1.75rem; }
    .sj-timeline .sj-branch { min-width: 720px; }
}
</style>
