@extends('office.layout.office')
@section('title', 'จัดการใบขอซื้อ (PR) | Hi Monday')

@section('style')
    @include('office.partials.document-index-hero-styles')
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="hero-banner-card" style="background: #0f766e; --hero-shadow: rgba(15,118,110,.22); --hero-btn: #0f766e;">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8 col-md-7">
                <div class="hero-badge">
                    <i class="ri-file-list-2-line text-warning"></i>
                    PURCHASE REQUISITIONS MANAGEMENT
                </div>
                <h2 class="fw-bolder mb-2 text-white fs-26">จัดการใบขอซื้อ (Purchase Requisitions)</h2>
                <p class="mb-0 text-white-50 fs-14" style="max-width: 600px;">
                    สร้างและอนุมัติใบขอซื้อ ติดตามสถานะเอกสาร และเชื่อมต่อไปยังใบสั่งซื้อในระบบ Hi Monday
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-end mt-4 mt-md-0">
                <a href="{{ route('office.purchase-requisitions.create') }}" class="btn-create-glow">
                    <i class="ri-add-circle-fill fs-20"></i>
                    <span>สร้างใบขอซื้อใหม่</span>
                </a>
            </div>
        </div>
    </div>

    @include('office.settings.partials.flash')

    @php
        $totalCount = $purchaseRequisitions->total();
        $draftCount = $purchaseRequisitions->filter(fn ($item) => ($item->currentRevision?->status ?? '') === 'draft')->count();
        $doneCount = $purchaseRequisitions->filter(fn ($item) => in_array($item->currentRevision?->status ?? '', ['approved', 'submitted'], true))->count();
        $sumAmount = $purchaseRequisitions->sum(fn ($item) => (float) ($item->currentRevision?->total_amount ?? 0));
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
                        <span class="text-secondary fs-12 fw-bold text-uppercase">ส่งอนุมัติ / อนุมัติแล้ว</span>
                        <h3 class="fw-bolder text-success mb-1 fs-26 mt-1">{{ number_format($doneCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Submitted / Approved</span>
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
            <form method="get" action="{{ route('office.purchase-requisitions.index') }}">
                <div class="row g-2.5 align-items-center">
                    <div class="col-lg-5 col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3" style="border: 1.5px solid #e2e8f0; border-right: none;">
                                <i class="ri-search-2-line fs-16 text-primary"></i>
                            </span>
                            <input type="text" name="q" class="form-control search-input-wow border-start-0 rounded-end-3 ps-1" value="{{ request('q') }}" placeholder="ค้นหาเลขที่ PR หรือชื่อผู้ขาย...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <select name="status" class="form-select select-wow" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะเอกสาร --</option>
                            <option value="draft" @selected(request('status') === 'draft')>ร่างเอกสาร (Draft)</option>
                            <option value="submitted" @selected(request('status') === 'submitted')>ส่งอนุมัติแล้ว</option>
                            <option value="approved" @selected(request('status') === 'approved')>อนุมัติแล้ว</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>ปฏิเสธ</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ยกเลิก</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-2 col-12 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn-filter-wow">
                            <i class="ri-filter-3-line fs-15"></i>
                            <span>กรองข้อมูล</span>
                        </button>
                        @if (request('q') || request('status'))
                            <a href="{{ route('office.purchase-requisitions.index') }}" class="btn btn-light border rounded-3 px-3 d-inline-flex align-items-center gap-1 fs-13 text-secondary" style="height: 44px;">
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
                        <th style="min-width: 240px;">ผู้ขาย / องค์กร</th>
                        <th style="min-width: 150px;" class="text-center">สถานะ</th>
                        <th style="min-width: 150px;" class="text-end">ยอดเงินรวม</th>
                        <th style="min-width: 130px;">วันที่ขอ</th>
                        <th style="min-width: 130px;">วันที่ต้องการ</th>
                        <th style="min-width: 120px;" class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="fs-13">
                @forelse ($purchaseRequisitions as $purchaseRequisition)
                    @php
                        $revision = $purchaseRequisition->currentRevision;
                        $sellerName = $purchaseRequisition->partner?->partner_name_th ?: ($revision?->customer_name ?: 'ไม่ระบุชื่อผู้ขาย');
                    @endphp
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <a href="{{ route('office.purchase-requisitions.show', $purchaseRequisition) }}" class="doc-pill-tag">
                                <i class="ri-file-list-2-line text-primary fs-15"></i>
                                <span>{{ $purchaseRequisition->displayNo() }}</span>
                            </a>
                        </td>
                        <td>
                            <div class="cust-info-box">
                                <div class="cust-avatar-box"><i class="ri-store-2-line"></i></div>
                                <div class="cust-text-wrap">
                                    <div class="cust-name-title">{{ $sellerName }}</div>
                                    <div class="cust-sub-desc">{{ $purchaseRequisition->partner?->partner_tax_no ? 'Tax ID: '.$purchaseRequisition->partner->partner_tax_no : 'ผู้ขาย / คู่ค้า' }}</div>
                                </div>
                            </div>
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
                                <span>{{ $revision?->request_date?->format('d/m/Y') ?: '-' }}</span>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <span class="text-secondary fs-13 d-inline-flex align-items-center gap-1">
                                <i class="ri-calendar-check-line text-primary"></i>
                                <span>{{ $revision?->required_date?->format('d/m/Y') ?: '-' }}</span>
                            </span>
                        </td>
                        <td class="text-center pe-4 text-nowrap">
                            <div class="d-flex justify-content-center align-items-center gap-1.5">
                                <a href="{{ route('office.purchase-requisitions.show', $purchaseRequisition) }}" class="action-btn-circle" title="ดูรายละเอียด">
                                    <i class="ri-eye-line text-primary"></i>
                                </a>
                                @include('office.partials.document-crud-index-actions', [
                                    'revision' => $revision,
                                    'editRoute' => route('office.purchase-requisitions.edit', $purchaseRequisition),
                                    'destroyRoute' => route('office.purchase-requisitions.destroy', $purchaseRequisition),
                                    'editableStatuses' => ['draft', 'rejected'],
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
                                <h5 class="fw-bold text-dark fs-16 mb-1">ยังไม่มีรายการใบขอซื้อ</h5>
                                <p class="fs-13 text-muted mb-3" style="max-width: 400px; margin: 0 auto;">คุณยังไม่ได้สร้างใบขอซื้อในระบบ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</p>
                                <a href="{{ route('office.purchase-requisitions.create') }}" class="btn-create-glow" style="display: inline-flex; border: 1px solid #2563eb; color: #2563eb;">
                                    <i class="ri-add-line fs-18"></i> เริ่มสร้างใบขอซื้อฉบับแรก
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($purchaseRequisitions->hasPages())
            <div class="card-footer bg-white border-top p-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted fs-13">
                    แสดง {{ $purchaseRequisitions->firstItem() }} ถึง {{ $purchaseRequisitions->lastItem() }} จากทั้งหมด <strong class="text-dark">{{ $purchaseRequisitions->total() }}</strong> รายการ
                </div>
                <div>{{ $purchaseRequisitions->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
