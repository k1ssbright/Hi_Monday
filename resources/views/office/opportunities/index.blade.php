@extends('office.layout.office')
@section('title', 'รายการ Opportunity | Hi Monday')

@section('style')
    @include('office.partials.document-index-hero-styles')
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="hero-banner-card">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8 col-md-7">
                <div class="hero-badge">
                    <i class="ri-focus-3-line text-warning"></i>
                    OPPORTUNITIES MANAGEMENT
                </div>
                <h2 class="fw-bolder mb-2 text-white fs-26">จัดการ Opportunity</h2>
                <p class="mb-0 text-white-50 fs-14" style="max-width: 600px;">
                    ติดตามดีลที่ผ่านการคัดกรองจาก Lead บริหาร stage การขาย และเชื่อมต่อไปยังใบเสนอราคาในระบบ Hi Monday
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-end mt-4 mt-md-0">
                <a href="{{ route('office.opportunities.create') }}" class="btn-create-glow">
                    <i class="ri-add-circle-fill fs-20"></i>
                    <span>สร้าง Opportunity ใหม่</span>
                </a>
            </div>
        </div>
    </div>

    @include('office.settings.partials.flash')

    @php
        $totalCount = $opportunities->total();
        $wonCount = $opportunities->filter(fn ($item) => ($item->stage ?? '') === 'closed_won')->count();
        $openCount = $opportunities->filter(fn ($item) => ! in_array($item->stage ?? '', ['closed_won', 'closed_lost'], true))->count();
        $sumAmount = $opportunities->sum(fn ($item) => (float) ($item->estimated_value ?? 0));
    @endphp
    <div class="row g-3.5 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">รายการทั้งหมด</span>
                        <h3 class="fw-bolder text-dark mb-1 fs-26 mt-1">{{ number_format($totalCount) }} <span class="fs-13 fw-normal text-muted">ดีล</span></h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Total Records</span>
                    </div>
                    <div class="stat-icon-wrap primary"><i class="ri-briefcase-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">ปิดการขายได้</span>
                        <h3 class="fw-bolder text-success mb-1 fs-26 mt-1">{{ number_format($wonCount) }} <span class="fs-13 fw-normal text-muted">ดีล</span></h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Closed Won</span>
                    </div>
                    <div class="stat-icon-wrap success"><i class="ri-trophy-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">กำลังดำเนินการ</span>
                        <h3 class="fw-bolder text-warning mb-1 fs-26 mt-1">{{ number_format($openCount) }} <span class="fs-13 fw-normal text-muted">ดีล</span></h3>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Open Pipeline</span>
                    </div>
                    <div class="stat-icon-wrap warning"><i class="ri-flow-chart"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">มูลค่าประมาณการ</span>
                        <h3 class="fw-bolder text-primary mb-1 fs-22 mt-1">฿{{ number_format($sumAmount, 2) }}</h3>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Estimated Value</span>
                    </div>
                    <div class="stat-icon-wrap info"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-card-wow mb-4">
        <div class="toolbar-container">
            <form method="get" action="{{ route('office.opportunities.index') }}">
                <div class="row g-2.5 align-items-center">
                    <div class="col-lg-5 col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3" style="border: 1.5px solid #e2e8f0; border-right: none;">
                                <i class="ri-search-2-line fs-16 text-primary"></i>
                            </span>
                            <input type="text" name="q" class="form-control search-input-wow border-start-0 rounded-end-3 ps-1" value="{{ request('q') }}" placeholder="ค้นหาเลขที่ดีล, ชื่อดีล หรือชื่อลูกค้า...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <select name="stage" class="form-select select-wow" onchange="this.form.submit()">
                            <option value="">-- ทุก Stage --</option>
                            @foreach (\App\Models\Sales\Opportunity::STAGE_LABELS as $stageKey => $stageLabel)
                                <option value="{{ $stageKey }}" @selected(request('stage') === (string) $stageKey)>{{ $stageLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-2 col-12 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn-filter-wow">
                            <i class="ri-filter-3-line fs-15"></i>
                            <span>กรองข้อมูล</span>
                        </button>
                        @if (request('q') || request('stage'))
                            <a href="{{ route('office.opportunities.index') }}" class="btn btn-light border rounded-3 px-3 d-inline-flex align-items-center gap-1 fs-13 text-secondary" style="height: 44px;">
                                <i class="ri-refresh-line"></i> ล้าง
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-wow align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 160px;" class="ps-4">เลขที่ดีล</th>
                        <th style="min-width: 240px;">ชื่อดีล / ลูกค้า</th>
                        <th style="min-width: 150px;" class="text-center">Stage</th>
                        <th style="min-width: 150px;" class="text-end">มูลค่าประมาณ</th>
                        <th style="min-width: 140px;">วันที่คาดว่าจะปิด</th>
                        <th style="min-width: 150px;">ผู้รับผิดชอบ</th>
                        <th style="min-width: 120px;" class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="fs-13">
                @forelse ($opportunities as $op)
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <a href="{{ route('office.opportunities.show', $op) }}" class="doc-pill-tag">
                                <i class="ri-focus-3-line text-primary fs-15"></i>
                                <span>{{ $op->opportunity_no }}</span>
                            </a>
                        </td>
                        <td>
                            <div class="cust-info-box">
                                <div class="cust-avatar-box"><i class="ri-briefcase-line"></i></div>
                                <div class="cust-text-wrap">
                                    <div class="cust-name-title">{{ $op->name ?: 'ไม่ระบุชื่อดีล' }}</div>
                                    <div class="cust-sub-desc">{{ $op->partner?->partner_name_th ?: 'ยังไม่ผูกลูกค้า' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center text-nowrap">
                            @include('office.partials.document-status-pill', [
                                'status' => $op->stage,
                                'label' => $op->stageLabel(),
                            ])
                        </td>
                        <td class="text-end fw-bolder text-dark font-monospace text-nowrap fs-14">
                            {{ number_format((float) $op->estimated_value, 2) }} <span class="fs-11 fw-normal text-muted">บาท</span>
                        </td>
                        <td class="text-nowrap">
                            <span class="text-secondary fs-13 d-inline-flex align-items-center gap-1">
                                <i class="ri-calendar-check-line text-primary"></i>
                                <span>{{ $op->expected_close_date?->format('d/m/Y') ?: '-' }}</span>
                            </span>
                        </td>
                        <td class="text-nowrap">{{ $op->owner?->displayName() ?: '-' }}</td>
                        <td class="text-center pe-4 text-nowrap">
                            <div class="d-flex justify-content-center align-items-center gap-1.5">
                                <a href="{{ route('office.opportunities.show', $op) }}" class="action-btn-circle" title="ดูรายละเอียด">
                                    <i class="ri-eye-line text-primary"></i>
                                </a>
                                <a href="{{ route('office.opportunities.edit', $op) }}" class="action-btn-circle" title="แก้ไข">
                                    <i class="ri-pencil-line text-info"></i>
                                </a>
                                <form action="{{ route('office.opportunities.destroy', $op) }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูล Opportunity นี้ใช่หรือไม่?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-circle" title="ลบ">
                                        <i class="ri-delete-bin-line text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 bg-white">
                            <div class="py-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                    <i class="ri-briefcase-line fs-30"></i>
                                </div>
                                <h5 class="fw-bold text-dark fs-16 mb-1">ยังไม่มีข้อมูล Opportunity</h5>
                                <p class="fs-13 text-muted mb-3" style="max-width: 400px; margin: 0 auto;">คุณยังไม่ได้สร้าง Opportunity ในระบบ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</p>
                                <a href="{{ route('office.opportunities.create') }}" class="btn-create-glow" style="display: inline-flex; border: 1px solid #2563eb; color: #2563eb;">
                                    <i class="ri-add-line fs-18"></i> เริ่มสร้าง Opportunity รายการแรก
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($opportunities->hasPages())
            <div class="card-footer bg-white border-top p-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted fs-13">
                    แสดง {{ $opportunities->firstItem() }} ถึง {{ $opportunities->lastItem() }} จากทั้งหมด <strong class="text-dark">{{ $opportunities->total() }}</strong> รายการ
                </div>
                <div>{{ $opportunities->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
