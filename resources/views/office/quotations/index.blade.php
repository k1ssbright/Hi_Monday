@extends('office.layout.office')
@section('title', 'จัดการใบเสนอราคา (Quotations) | Hi Monday')

@section('style')
    @include('office.partials.document-index-hero-styles')
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="hero-banner-card">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8 col-md-7">
                <div class="hero-badge">
                    <i class="ri-file-text-line text-warning"></i>
                    QUOTATIONS MANAGEMENT
                </div>
                <h2 class="fw-bolder mb-2 text-white fs-26">จัดการใบเสนอราคา (Quotations)</h2>
                <p class="mb-0 text-white-50 fs-14" style="max-width: 600px;">
                    สร้างและติดตามใบเสนอราคา ตรวจสอบสถานะการตอบรับ และเชื่อมต่อไปยังใบสั่งขายในระบบ Hi Monday
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-end mt-4 mt-md-0">
                <a href="{{ route('office.quotations.create') }}" class="btn-create-glow">
                    <i class="ri-add-circle-fill fs-20"></i>
                    <span>สร้างใบเสนอราคาใหม่</span>
                </a>
            </div>
        </div>
    </div>

    @include('office.settings.partials.flash')

    @php
        $totalCount = $quotations->total();
        $draftCount = $quotations->filter(fn ($item) => ($item->currentRevision?->status ?? '') === 'draft')->count();
        $doneCount = $quotations->filter(fn ($item) => in_array($item->currentRevision?->status ?? '', ['accepted', 'approved', 'sent'], true))->count();
        $sumAmount = $quotations->sum(fn ($item) => (float) ($item->currentRevision?->total_amount ?? 0));
    @endphp
    <div class="row g-3.5 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">รายการทั้งหมด</span>
                        <h3 class="fw-bolder text-dark mb-1 fs-26 mt-1">{{ number_format($totalCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Total Records</span>
                    </div>
                    <div class="stat-icon-wrap primary"><i class="ri-file-copy-2-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">ส่งแล้ว / ตอบรับ</span>
                        <h3 class="fw-bolder text-success mb-1 fs-26 mt-1">{{ number_format($doneCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Sent / Accepted</span>
                    </div>
                    <div class="stat-icon-wrap success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">ร่างเอกสาร</span>
                        <h3 class="fw-bolder text-warning mb-1 fs-26 mt-1">{{ number_format($draftCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Draft Status</span>
                    </div>
                    <div class="stat-icon-wrap warning"><i class="ri-edit-box-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase">มูลค่ารวมทั้งสิ้น</span>
                        <h3 class="fw-bolder text-primary mb-1 fs-22 mt-1">฿{{ number_format($sumAmount, 2) }}</h3>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Grand Total</span>
                    </div>
                    <div class="stat-icon-wrap info"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-card-wow mb-4">
        <div class="toolbar-container">
            <form method="get" action="{{ route('office.quotations.index') }}">
                <div class="row g-2.5 align-items-center">
                    <div class="col-lg-5 col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3" style="border: 1.5px solid #e2e8f0; border-right: none;">
                                <i class="ri-search-2-line fs-16 text-primary"></i>
                            </span>
                            <input type="text" name="q" class="form-control search-input-wow border-start-0 rounded-end-3 ps-1" value="{{ request('q') }}" placeholder="ค้นหาเลขที่ QT, ชื่อลูกค้า, หรือ Opportunity...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <select name="status" class="form-select select-wow" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะเอกสาร --</option>
                            <option value="draft" @selected(request('status') === 'draft')>ร่างเอกสาร (Draft)</option>
                            <option value="pending_approval" @selected(request('status') === 'pending_approval')>รออนุมัติ</option>
                            <option value="approved" @selected(request('status') === 'approved')>อนุมัติแล้ว</option>
                            <option value="sent" @selected(request('status') === 'sent')>ส่งแล้ว</option>
                            <option value="accepted" @selected(request('status') === 'accepted')>ตอบรับ</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>ปฏิเสธ</option>
                            <option value="expired" @selected(request('status') === 'expired')>หมดอายุ</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ยกเลิก</option>
                            <option value="superseded" @selected(request('status') === 'superseded')>มีฉบับใหม่แทน</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-2 col-12 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn-filter-wow">
                            <i class="ri-filter-3-line fs-15"></i>
                            <span>กรองข้อมูล</span>
                        </button>
                        @if (request('q') || request('status'))
                            <a href="{{ route('office.quotations.index') }}" class="btn btn-light border rounded-3 px-3 d-inline-flex align-items-center gap-1 fs-13 text-secondary" style="height: 44px;">
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
                        <th style="min-width: 180px;" class="ps-4">เลขที่เอกสาร</th>
                        <th style="min-width: 240px;">ลูกค้า / องค์กร</th>
                        <th style="min-width: 150px;">Opportunity</th>
                        <th style="min-width: 150px;" class="text-center">สถานะ</th>
                        <th style="min-width: 150px;" class="text-end">ยอดเงินรวม</th>
                        <th style="min-width: 130px;">วันที่ออก</th>
                        <th style="min-width: 120px;" class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="fs-13">
                @forelse ($quotations as $quotation)
                    @php
                        $revision = $quotation->currentRevision;
                        $customerName = $quotation->partner?->partner_name_th ?: ($revision?->customer_name ?: 'ไม่ระบุชื่อลูกค้า');
                    @endphp
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <a href="{{ route('office.quotations.show', $quotation) }}" class="doc-pill-tag">
                                <i class="ri-file-text-line text-primary fs-15"></i>
                                <span>{{ $quotation->displayNo() }}</span>
                            </a>
                        </td>
                        <td>
                            <div class="cust-info-box">
                                <div class="cust-avatar-box"><i class="ri-building-line"></i></div>
                                <div class="cust-text-wrap">
                                    <div class="cust-name-title">{{ $customerName }}</div>
                                    <div class="cust-sub-desc">{{ $quotation->partner?->partner_tax_no ? 'Tax ID: '.$quotation->partner->partner_tax_no : 'องค์กรลูกค้า' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            @if ($quotation->opportunity)
                                <a href="{{ route('office.opportunities.show', $quotation->opportunity) }}" class="badge bg-light text-secondary border font-monospace text-decoration-none fs-12 px-2.5 py-1.5 rounded-2">
                                    <i class="ri-focus-3-line me-1 text-primary"></i>{{ $quotation->opportunity->opportunity_no }}
                                </a>
                            @else
                                <span class="text-muted fs-13">-</span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            @include('office.partials.document-status-pill', ['status' => $revision?->status])
                        </td>
                        <td class="text-end fw-bolder text-dark font-monospace text-nowrap fs-14">
                            {{ $revision ? number_format((float) $revision->total_amount, 2) : '0.00' }} <span class="fs-11 fw-normal text-muted">บาท</span>
                        </td>
                        <td class="text-nowrap">
                            <span class="text-secondary fs-13 d-inline-flex align-items-center gap-1">
                                <i class="ri-calendar-line text-primary"></i>
                                <span>{{ $revision?->issue_date?->format('d/m/Y') ?: '-' }}</span>
                            </span>
                        </td>
                        <td class="text-center pe-4 text-nowrap">
                            <div class="d-flex justify-content-center align-items-center gap-1.5">
                                <a href="{{ route('office.quotations.show', $quotation) }}" class="action-btn-circle" title="ดูรายละเอียด">
                                    <i class="ri-eye-line text-primary"></i>
                                </a>
                                @include('office.partials.document-crud-index-actions', [
                                    'revision' => $revision,
                                    'editRoute' => route('office.quotations.edit', $quotation),
                                    'destroyRoute' => route('office.quotations.destroy', $quotation),
                                ])
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 bg-white">
                            <div class="py-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                    <i class="ri-inbox-archive-line fs-30"></i>
                                </div>
                                <h5 class="fw-bold text-dark fs-16 mb-1">ยังไม่มีรายการใบเสนอราคา</h5>
                                <p class="fs-13 text-muted mb-3" style="max-width: 400px; margin: 0 auto;">คุณยังไม่ได้สร้างใบเสนอราคาในระบบ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</p>
                                <a href="{{ route('office.quotations.create') }}" class="btn-create-glow" style="display: inline-flex; border: 1px solid #2563eb; color: #2563eb;">
                                    <i class="ri-add-line fs-18"></i> เริ่มสร้างใบเสนอราคาฉบับแรก
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($quotations->hasPages())
            <div class="card-footer bg-white border-top p-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted fs-13">
                    แสดง {{ $quotations->firstItem() }} ถึง {{ $quotations->lastItem() }} จากทั้งหมด <strong class="text-dark">{{ $quotations->total() }}</strong> รายการ
                </div>
                <div>{{ $quotations->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
