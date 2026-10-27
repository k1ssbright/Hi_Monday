@extends('office.layout.office')
@section('title', 'รายการ Lead | Hi Monday')

@section('style')
    @include('office.partials.document-index-hero-styles')
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="hero-banner-card">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8 col-md-7">
                <div class="hero-badge">
                    <i class="ri-user-search-line text-warning"></i>
                    LEADS MANAGEMENT
                </div>
                <h2 class="fw-bolder mb-2 text-white fs-26">จัดการ Lead</h2>
                <p class="mb-0 text-white-50 fs-14" style="max-width: 600px;">
                    รับและติดตาม Lead จากทุกช่องทาง คัดกรองคุณภาพ และแปลงเป็น Opportunity ในระบบ Hi Monday
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-end mt-4 mt-md-0">
                <a href="{{ route('office.leads.create') }}" class="btn-create-glow">
                    <i class="ri-add-circle-fill fs-20"></i>
                    <span>สร้าง Lead ใหม่</span>
                </a>
            </div>
        </div>
    </div>

    @include('office.settings.partials.flash')

    @php
        $totalCount = $leads->total();
        $wonCount = $leads->filter(fn ($item) => in_array($item->status, ['qualified', 'converted'], true))->count();
        $pipelineCount = $leads->filter(fn ($item) => in_array($item->status, ['new', 'contacting', 'nurturing'], true))->count();
        $draftCount = $leads->filter(fn ($item) => ($item->status ?? '') === 'draft')->count();
    @endphp
    <div class="row g-3.5 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">รายการทั้งหมด</span>
                        <h3 class="fw-bolder text-dark mb-1 fs-26 mt-1">{{ number_format($totalCount) }} <span class="fs-13 fw-normal text-muted">รายการ</span></h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Total Records</span>
                    </div>
                    <div class="stat-icon-wrap primary"><i class="ri-user-search-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">ผ่านเกณฑ์ / แปลงแล้ว</span>
                        <h3 class="fw-bolder text-success mb-1 fs-26 mt-1">{{ number_format($wonCount) }} <span class="fs-13 fw-normal text-muted">รายการ</span></h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Qualified / Converted</span>
                    </div>
                    <div class="stat-icon-wrap success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">กำลังติดตาม</span>
                        <h3 class="fw-bolder text-warning mb-1 fs-26 mt-1">{{ number_format($pipelineCount) }} <span class="fs-13 fw-normal text-muted">รายการ</span></h3>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">In Progress</span>
                    </div>
                    <div class="stat-icon-wrap warning"><i class="ri-phone-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">ร่างเอกสาร</span>
                        <h3 class="fw-bolder text-primary mb-1 fs-26 mt-1">{{ number_format($draftCount) }} <span class="fs-13 fw-normal text-muted">รายการ</span></h3>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Draft Status</span>
                    </div>
                    <div class="stat-icon-wrap info"><i class="ri-edit-box-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-card-wow mb-4">
        <div class="toolbar-container">
            <form method="get" action="{{ route('office.leads.index') }}">
                <div class="row g-2.5 align-items-center">
                    <div class="col-lg-5 col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3" style="border: 1.5px solid #e2e8f0; border-right: none;">
                                <i class="ri-search-2-line fs-16 text-primary"></i>
                            </span>
                            <input type="text" name="q" class="form-control search-input-wow border-start-0 rounded-end-3 ps-1" value="{{ request('q') }}" placeholder="ค้นหารหัส Lead, ชื่อผู้ติดต่อ, อีเมล หรือองค์กร...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <select name="status" class="form-select select-wow" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะ Lead --</option>
                            @foreach (\App\Models\Lead\Lead::STATUS_LABELS as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" @selected(request('status') === (string) $statusKey)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-2 col-12 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn-filter-wow">
                            <i class="ri-filter-3-line fs-15"></i>
                            <span>กรองข้อมูล</span>
                        </button>
                        @if (request('q') || request('status'))
                            <a href="{{ route('office.leads.index') }}" class="btn btn-light border rounded-3 px-3 d-inline-flex align-items-center gap-1 fs-13 text-secondary" style="height: 44px;">
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
                        <th style="min-width: 160px;" class="ps-4">รหัส Lead</th>
                        <th style="min-width: 220px;">ผู้ติดต่อ</th>
                        <th style="min-width: 220px;">องค์กร / โครงการ</th>
                        <th style="min-width: 180px;">สาขา / ช่องทาง</th>
                        <th style="min-width: 150px;">ผู้รับผิดชอบ</th>
                        <th style="min-width: 140px;" class="text-center">สถานะ</th>
                        <th style="min-width: 130px;">วันที่ลงทะเบียน</th>
                        <th style="min-width: 120px;" class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="fs-13">
                @forelse ($leads as $row)
                    @php
                        $contactName = $row->primaryContact?->displayName();
                        $phone = $row->primaryContact?->phone;
                        $email = $row->primaryContact?->email;
                        $leadNo = $row->registration_no ?: 'LEAD-'.str_pad((string) $row->id, 5, '0', STR_PAD_LEFT);
                    @endphp
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <a href="{{ route('office.leads.show', $row) }}" class="doc-pill-tag">
                                <i class="ri-user-search-line text-primary fs-15"></i>
                                <span>{{ $leadNo }}</span>
                            </a>
                        </td>
                        <td>
                            <div class="cust-info-box">
                                <div class="cust-avatar-box"><i class="ri-user-3-line"></i></div>
                                <div class="cust-text-wrap">
                                    <div class="cust-name-title">{{ $contactName ?: 'ไม่ระบุผู้ติดต่อ' }}</div>
                                    <div class="cust-sub-desc">{{ $email ?: ($phone ?: 'ผู้ติดต่อหลัก') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark fs-13">{{ $row->organization_name ?: '-' }}</div>
                            @if ($row->project_name)
                                <div class="cust-sub-desc"><i class="ri-folder-2-line me-1"></i>{{ $row->project_name }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark fs-13">{{ $row->branch?->business_branch_name_th ?? 'สำนักงานใหญ่' }}</div>
                            <div class="cust-sub-desc">{{ $row->channel?->name ?? '-' }}</div>
                        </td>
                        <td class="text-nowrap">{{ $row->assignedTo?->displayName() ?? '-' }}</td>
                        <td class="text-center text-nowrap">
                            @include('office.partials.document-status-pill', [
                                'status' => $row->status,
                                'label' => $row->statusLabel(),
                            ])
                        </td>
                        <td class="text-nowrap">
                            <span class="text-secondary fs-13 d-inline-flex align-items-center gap-1">
                                <i class="ri-calendar-line text-primary"></i>
                                <span>{{ $row->registration_date?->format('d/m/Y') ?? $row->created_at?->format('d/m/Y') ?? '-' }}</span>
                            </span>
                        </td>
                        <td class="text-center pe-4 text-nowrap">
                            <div class="d-flex justify-content-center align-items-center gap-1.5">
                                <a href="{{ route('office.leads.show', $row) }}" class="action-btn-circle" title="ดูรายละเอียด">
                                    <i class="ri-eye-line text-primary"></i>
                                </a>
                                @unless ($row->isLocked())
                                    <a href="{{ route('office.leads.edit', $row) }}" class="action-btn-circle" title="แก้ไข">
                                        <i class="ri-pencil-line text-info"></i>
                                    </a>
                                    <form action="{{ route('office.leads.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูล Lead นี้ใช่หรือไม่?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn-circle" title="ลบ">
                                            <i class="ri-delete-bin-line text-danger"></i>
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 bg-white">
                            <div class="py-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                    <i class="ri-user-search-line fs-30"></i>
                                </div>
                                <h5 class="fw-bold text-dark fs-16 mb-1">ยังไม่มีข้อมูล Lead</h5>
                                <p class="fs-13 text-muted mb-3" style="max-width: 400px; margin: 0 auto;">คุณยังไม่ได้สร้าง Lead ในระบบ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</p>
                                <a href="{{ route('office.leads.create') }}" class="btn-create-glow" style="display: inline-flex; border: 1px solid #2563eb; color: #2563eb;">
                                    <i class="ri-add-line fs-18"></i> เริ่มสร้าง Lead รายการแรก
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
            <div class="card-footer bg-white border-top p-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted fs-13">
                    แสดง {{ $leads->firstItem() }} ถึง {{ $leads->lastItem() }} จากทั้งหมด <strong class="text-dark">{{ $leads->total() }}</strong> รายการ
                </div>
                <div>{{ $leads->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
