@php
    /** @var array<string, mixed> $journey */
    $journey = $journey ?? [];
    $upstream = $journey['upstream'] ?? [];
    $lanes = $journey['lanes'] ?? [];
    $billingRatio = $journey['billing_ratio'] ?? [];
    $activePanel = $journey['active_panel'] ?? null;
    $installmentCount = (int) ($journey['installment_count'] ?? 0);
@endphp

{{-- Port structure from stitch_sales_timeline_widget_card/code.html --}}
<div class="sj-timeline d-print-none" data-purpose="sales-workflow-container">
<article class="sj-card" data-purpose="sales-pipeline-widget">

{{-- Header --}}
<header class="sj-header">
    <div class="d-flex align-items-center gap-3">
        <div class="sj-icon" data-purpose="module-badge" aria-hidden="true">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="6" x2="6" y1="3" y2="15"></line>
                <circle cx="18" cy="6" r="3"></circle>
                <circle cx="6" cy="18" r="3"></circle>
                <path d="M18 9a9 9 0 0 1-9 9"></path>
                <path d="M14 15l4 4m0-4l-4 4" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>
        <div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h1 class="sj-title">ไทม์ไลน์งานขาย &amp; งวดชำระเงิน (Sales &amp; Billing Flow)</h1>
                @if ($installmentCount > 0)
                    <span class="sj-pill"><i></i>{{ $installmentCount }} งวดชำระ (Multi-Installment)</span>
                @endif
            </div>
            @if (! empty($journey['subtitle']))
                <p class="sj-sub">
                    {!! preg_replace('/฿[\d,]+\.\d{2}/u', '<strong class="sj-code">$0</strong>', e($journey['subtitle'])) !!}
                </p>
            @endif
        </div>
    </div>
</header>

{{-- Upstream --}}
<section class="sj-up" data-purpose="linear-upstream-section">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="sj-up-label"><i></i><span>กระบวนการต้นน้ำก่อนแตกงวด (Pre-Sales &amp; Order Confirmation)</span></div>
        @if (! empty($journey['upstream_complete']))
            <span class="sj-confirmed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                อนุมัติใบสั่งขายเรียบร้อย (SO Confirmed)
            </span>
        @endif
    </div>

    <div class="overflow-auto pb-2">
        <div class="sj-stepper">
            <div class="sj-stepper-line {{ ! empty($journey['upstream_complete']) ? 'is-on' : '' }}" aria-hidden="true"></div>
            <ol class="sj-steps">
                @foreach ($upstream as $index => $node)
                    @php
                        $status = $node['status'] ?? '';
                        $isCurrent = (bool) ($node['is_current'] ?? false);
                        $isHub = (bool) ($node['is_hub'] ?? false);
                        $exists = (bool) ($node['exists'] ?? false);
                        $cls = ['sj-step-a'];
                        if ($status === 'done') $cls[] = 'is-done';
                        // SO ไฮไลต์เฉพาะตอนอยู่หน้า SO
                        if ($isCurrent || ($status === 'active' && ! $isHub)) $cls[] = 'is-now';
                        if (! $exists) $cls[] = 'is-off';
                        $tag = ($node['url'] ?? null) ? 'a' : 'div';
                        $code = $isHub ? ($node['code'].' (หลัก)') : $node['code'];
                        $showCheck = in_array($status, ['done', 'active'], true) || $isCurrent || $exists;
                    @endphp
                    <li>
                        <{{ $tag }} @if ($node['url'] ?? null) href="{{ $node['url'] }}" @endif class="{{ implode(' ', $cls) }}">
                            <div class="sj-dot">
                                @if ($showCheck)
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                @else
                                    <span class="fw-bold" style="font-size:11px;">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="mt-2">
                                @if ($isHub && $isCurrent)
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="sj-k">{{ $index + 1 }}. {{ $code }}</span>
                                        <span style="width:6px;height:6px;border-radius:999px;background:#2563eb;display:inline-block;"></span>
                                    </div>
                                @else
                                    <span class="sj-k">{{ $index + 1 }}. {{ $code }}</span>
                                @endif
                                <span class="sj-l">{{ $node['label'] }}</span>
                                @if ($node['displayNo'] ?? null)
                                    <span class="sj-no sj-code">{{ $node['displayNo'] }}</span>
                                @elseif (! $exists)
                                    <span class="sj-no text-muted">ยังไม่มี</span>
                                @endif
                            </div>
                        </{{ $tag }}>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

{{-- Lanes --}}
<section class="sj-lanes" data-purpose="multi-installment-lanes-section">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="sj-lanes-h2">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 3v12a3 3 0 003 3h12" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M18 9a9 9 0 01-9 9" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    แตกงวดการเรียกเก็บและส่งมอบ (Split Billing &amp; Delivery Lanes)
                </h2>
                @if ($installmentCount > 0)
                    <span class="sj-lanes-hint">{{ $installmentCount }} งวดชำระจากใบสั่งขาย SO เดียวกัน</span>
                @endif
            </div>
            <p class="sj-lanes-p">แต่ละงวดมีเอกสารรอบการวางบิลและส่งมอบแยกจากกันอย่างอิสระ</p>
        </div>
        <div class="sj-leg">
            <span><b class="g"></b> ชำระครบ</span>
            <span><b class="b"></b> ปัจจุบัน</span>
            <span class="muted"><b class="s"></b> รอดำเนินการ</span>
        </div>
    </div>

    @if ($lanes === [])
        <div class="alert alert-light border mb-0">ยังไม่มีงวดชำระในไทม์ไลน์นี้ — เมื่อมี Sales Order และเงื่อนไขการชำระ จะแสดงเลนงวดที่นี่</div>
    @else
        <div class="overflow-auto">
            <div class="sj-branch">
                <div class="sj-stack">
                    @foreach ($lanes as $laneIndex => $lane)
                        @php
                            $st = $lane['status'] ?? 'upcoming';
                            $vis = (($lane['is_active'] ?? false) && $st !== 'paid') ? 'active' : $st;
                            $laneCls = match ($vis) { 'paid' => 'is-paid', 'active' => 'is-active', default => 'is-upcoming' };
                            $joinCls = match ($vis) { 'paid' => 'is-paid', 'active' => 'is-active', default => 'is-upcoming' };
                            $steps = $lane['steps'] ?? [];
                            $cols = count($steps) >= 4 ? 'c4' : 'c3';
                            $pct = rtrim(rtrim(number_format((float) ($lane['percent'] ?? 0), 2), '0'), '.');
                            $stepKeys = array_keys($steps);
                            $isFirst = $laneIndex === 0;
                            $isLast = $laneIndex === array_key_last($lanes);
                        @endphp
                        <div class="sj-lane-row">
                            <div class="sj-join {{ $joinCls }}" aria-hidden="true">
                                @unless ($isFirst)
                                    <span class="sj-join-trunk is-top"></span>
                                @endunless
                                @unless ($isLast)
                                    <span class="sj-join-trunk is-bot"></span>
                                @endunless
                                <span class="sj-join-node"></span>
                                <span class="sj-join-arm"></span>
                            </div>

                            <div class="sj-lane {{ $laneCls }}">
                            <div class="sj-rail"></div>
                            @if ($vis === 'active')
                                <span class="sj-tag"><span class="ping"></span>งวดปัจจุบัน (Active Milestone)</span>
                            @endif

                            <div class="row g-3 align-items-center">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="sj-num">{{ $lane['installment_no'] }}</span>
                                        <h3 class="sj-ht">งวดที่ {{ $lane['installment_no'] }}: {{ $lane['label'] }}</h3>
                                    </div>
                                    <div class="mt-1 d-flex align-items-baseline gap-2">
                                        <span class="sj-amt sj-code">฿{{ number_format((float) ($lane['amount'] ?? 0), 2) }}</span>
                                        <span class="sj-pct">({{ $pct }}%)</span>
                                    </div>
                                    <div>
                                        <span class="sj-chip {{ $vis === 'active' ? 'act' : ($st === 'paid' ? 'paid' : 'wait') }}">
                                            @if ($st === 'paid')
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                            @elseif ($vis === 'active')
                                                <span style="width:8px;height:8px;border-radius:999px;background:#2563eb;display:inline-block;animation:sj-fade 1.5s ease-in-out infinite;"></span>
                                            @else
                                                <span style="width:6px;height:6px;border-radius:999px;background:#94a3b8;display:inline-block;"></span>
                                            @endif
                                            {{ $lane['status_label'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-9 ps-lg-2">
                                    <div class="sj-flow">
                                        <div class="sj-track"><i style="width: {{ (float) ($lane['progress'] ?? 0) }}%"></i></div>
                                        <div class="sj-grid {{ $cols }}">
                                            @foreach ($steps as $key => $step)
                                                @php
                                                    $state = $step['state'] ?? 'pending';
                                                    if ($step['is_current'] ?? false) $state = 'active';
                                                    if ($vis === 'upcoming' && $state !== 'done') $state = 'pending';
                                                    $ndCls = ['sj-nd'];
                                                    if ($state === 'done') $ndCls[] = 'is-done';
                                                    if ($state === 'active') $ndCls[] = 'is-focus';
                                                    if ($state === 'pending') $ndCls[] = 'is-pend';
                                                    if ($key === end($stepKeys) && $state === 'done') $ndCls[] = 'is-last';
                                                    $tag = ($step['url'] ?? null) ? 'a' : 'div';
                                                    $n = array_search($key, $stepKeys, true);
                                                    $n = $n === false ? 1 : $n + 1;
                                                @endphp
                                                <{{ $tag }} @if ($step['url'] ?? null) href="{{ $step['url'] }}" @endif class="{{ implode(' ', $ndCls) }}">
                                                    <div class="sj-ball">
                                                        @if ($state === 'done')
                                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                        @elseif ($state === 'active')
                                                            <span style="width:10px;height:10px;border-radius:999px;background:#fff;display:inline-block;"></span>
                                                        @elseif ($vis === 'upcoming')
                                                            <span style="font-size:10px;font-weight:700;">{{ $n }}</span>
                                                        @else
                                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1 text-center">
                                                        <span class="sj-nl">{{ $step['label'] }}</span>
                                                        @if ($step['display_no'] ?? null)
                                                            <span class="sj-nn sj-code">{{ $step['display_no'] }}</span>
                                                        @else
                                                            <span class="sj-nn sj-code">{{ $step['code'] ?? '—' }}</span>
                                                        @endif
                                                    </div>
                                                </{{ $tag }}>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>{{-- /.sj-lane --}}
                        </div>{{-- /.sj-lane-row --}}
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>

{{-- Footer --}}
<footer class="sj-foot" data-purpose="current-stage-details">
    @if ($billingRatio !== [])
        <div class="mb-4 pb-4 border-bottom">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2">
                <span class="sj-ratio-t">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    สัดส่วนมูลค่าโครงการรวม (Project Billing Ratio Breakdown)
                </span>
                <div class="sj-ratio-leg">
                    @foreach ($billingRatio as $seg)
                        @php
                            $ss = $seg['status'] ?? 'upcoming';
                            $word = match ($ss) { 'paid' => 'รับชำระแล้ว', 'active' => 'อยู่ระหว่างวางบิล', default => 'รอส่งมอบ' };
                            $color = match ($ss) { 'paid' => 'text-success', 'active' => 'text-primary', default => 'text-muted' };
                            $dot = match ($ss) { 'paid' => 'bg-success', 'active' => 'bg-primary', default => 'bg-secondary' };
                            $pct = rtrim(rtrim(number_format((float) $seg['percent'], 2), '0'), '.');
                            $k = number_format((float) $seg['amount'] / 1000, 0);
                        @endphp
                        <span class="{{ $color }} d-inline-flex align-items-center gap-1">
                            <span class="d-inline-block rounded-circle {{ $dot }}" style="width:8px;height:8px;opacity:{{ $ss === 'upcoming' ? '.5' : '1' }};"></span>
                            {{ $word }} {{ $pct }}% (฿{{ $k }}k)
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="sj-bar">
                @foreach ($billingRatio as $seg)
                    <span class="{{ match($seg['status'] ?? '') { 'paid' => 'p', 'active' => 'a', default => 'u' } }}" style="width: {{ max(0, min(100, (float) $seg['width'])) }}%" title="งวด {{ $seg['installment_no'] }}: {{ $seg['label'] }}"></span>
                @endforeach
            </div>
        </div>
    @endif

    @if ($activePanel)
        <div class="sj-panel">
            <div class="row g-4 align-items-center">
                <div class="col-lg-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="sj-panel-ic" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark" style="font-size:.875rem;">{{ $activePanel['title'] }}</span>
                                @if ($activePanel['doc_no'] ?? null)
                                    <span class="sj-code px-2 py-0 rounded" style="font-size:.75rem;background:#dbeafe;color:#1e40af;font-weight:700;">{{ $activePanel['doc_no'] }}</span>
                                @endif
                            </div>
                            <p class="sj-panel-due"><i></i>{{ $activePanel['status_text'] ?? '' }}</p>
                            @if (! empty($activePanel['credit_notes']) || ! empty($activePanel['debit_notes']))
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @foreach ($activePanel['credit_notes'] ?? [] as $cn)
                                        <a href="{{ $cn['url'] }}" class="badge bg-warning-subtle text-warning text-decoration-none">CN {{ $cn['display_no'] }}</a>
                                    @endforeach
                                    @foreach ($activePanel['debit_notes'] ?? [] as $dn)
                                        <a href="{{ $dn['url'] }}" class="badge bg-info-subtle text-info text-decoration-none">DBN {{ $dn['display_no'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sj-mid">
                        <div class="row g-3">
                            <div class="col-6">
                                <span class="lab">ยอดเรียกเก็บงวดนี้</span>
                                <div class="val sj-code">
                                    @if ($activePanel['amount'] !== null)
                                        ฿{{ number_format((float) $activePanel['amount'], 2) }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <span class="lab">วันครบกำหนดชำระ</span>
                                <div class="{{ ($activePanel['due_date_display'] ?? null) ? 'due' : 'text-muted' }}">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    {{ $activePanel['due_date_display'] ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                        @foreach ($activePanel['actions'] ?? [] as $action)
                            @php
                                $btn = match ($action['tone'] ?? 'secondary') {
                                    'primary' => 'p',
                                    'success' => 's',
                                    default => 'g',
                                };
                            @endphp
                            <a href="{{ $action['url'] }}" class="sj-btn {{ $btn }}">
                                @if ($btn === 's')
                                    <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                @elseif ($btn === 'p')
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                @else
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                @endif
                                <span>{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</footer>

</article>
</div>
