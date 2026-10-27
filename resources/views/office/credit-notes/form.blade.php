@extends('office.layout.office')
@php
    $standalone = $standalone ?? false;
    $isEdit = isset($creditNote);
    $revision = $revision ?? null;
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').' ใบลดหนี้ (Credit Note) | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
<style>
    .form-section-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
    }
    .section-badge-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .section-badge-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .section-badge-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .form-label-custom {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
        display: block;
    }
    .btn-submit-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        height: 48px;
        padding: 0 28px;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    }
    .btn-back-white {
        background-color: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        height: 48px;
        padding: 0 24px;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Bar -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="ri-file-reduce-line text-primary fs-22"></i>
                {{ $isEdit ? 'แก้ไขใบลดหนี้ (Credit Note)' : 'สร้างใบลดหนี้ใหม่ (Credit Note)' }}
            </h4>
        </div>
        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
            <nav aria-label="breadcrumb" class="d-inline-block">
                <ol class="breadcrumb mb-0 bg-transparent py-0 px-0 fs-13">
                    <li class="breadcrumb-item"><a href="{{ route('office.credit-notes.index') }}" class="text-decoration-none text-muted">ใบลดหนี้</a></li>
                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">{{ $isEdit ? 'แก้ไขเอกสาร' : 'สร้างเอกสารใหม่' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @include('office.settings.partials.flash')

    @if ($standalone || $isEdit)
        @include('office.partials.document-standalone-form-wrap', [
            'formAction' => $isEdit ? route('office.credit-notes.update', $creditNote) : route('office.credit-notes.store.standalone'),
            'formMethod' => $isEdit ? 'put' : 'post',
            'showStandaloneAlert' => ! $isEdit,
            'cancelUrl' => $isEdit ? route('office.credit-notes.show', $creditNote) : route('office.credit-notes.index'),
            'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึก Credit Note',
            'showPaymentTerms' => false,
            'headerFieldsView' => 'office.credit-notes.partials.header-fields',
            'documentNo' => $isEdit ? $creditNote->displayNo() : null,
            'seedLines' => $seedLines ?? [],
            'discountType' => old('discount_type', $totalsDefaults['discount_type'] ?? 'percent'),
            'customerParty' => $customerParty ?? [],
            'sellerParty' => $sellerParty ?? [],
            'provinces' => $provinces ?? collect(),
            'customerAmphures' => $customerAmphures ?? collect(),
            'customerTambons' => $customerTambons ?? collect(),
            'totalsDefaults' => $totalsDefaults ?? [],
            'partner' => $partner ?? null,
            'revision' => $revision,
        ])
    @else
        @php($revision = $taxInvoice->currentRevision)
        <!-- Reference Alert Banner -->
        <div class="alert border border-primary-subtle bg-primary-subtle rounded-3 p-3 d-flex align-items-center gap-3 mb-4 shadow-none">
            <div class="rounded-2 bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                <i class="ri-file-reduce-line fs-18"></i>
            </div>
            <div class="fs-13 text-dark">
                <strong class="text-primary me-1">สร้างใบลดหนี้ (Credit Note) อ้างอิงจาก Tax Invoice:</strong>
                <span>เลขที่เอกสาร <strong>{{ $taxInvoice->displayNo() }}</strong> | ลูกค้า: <strong>{{ $taxInvoice->partner?->partner_name_th ?: 'ไม่ระบุ' }}</strong></span>
            </div>
        </div>

        <form method="post" action="{{ route('office.tax-invoices.credit-notes.store', $taxInvoice) }}" class="document-form">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <!-- Section 1: ข้อมูลอ้างอิงและลูกค้า -->
                    <div class="form-section-card">
                        <div class="section-badge-header">
                            <div class="section-badge-icon">
                                <i class="ri-user-shared-line"></i>
                            </div>
                            <h2 class="section-badge-title">ข้อมูลลูกค้าและเอกสารอ้างอิง</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">ลูกค้า / องค์กร</label>
                                <div class="form-control bg-light border-0 fw-semibold text-dark fs-14 py-2.5">
                                    <i class="ri-building-line me-1 text-primary"></i> {{ $taxInvoice->partner?->partner_name_th ?: '-' }}
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">Tax Invoice อ้างอิง</label>
                                <div class="form-control bg-light border-0 fw-semibold text-dark fs-14 py-2.5">
                                    <i class="ri-file-paper-2-line me-1 text-primary"></i> {{ $taxInvoice->displayNo() }}
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">ยอดรวม Tax Invoice</label>
                                <div class="form-control bg-light border-0 fw-bold text-primary fs-15 py-2.5">
                                    ฿{{ number_format((float) ($revision?->total_amount ?? 0), 2) }} {{ $revision?->currency ?: 'THB' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: รายการสินค้า/บริการที่ลดหนี้ -->
                    <div class="form-section-card">
                        <div class="section-badge-header">
                            <div class="section-badge-icon">
                                <i class="ri-shopping-cart-2-line"></i>
                            </div>
                            <h2 class="section-badge-title">รายการสินค้า / บริการที่ปรับลด</h2>
                        </div>

                        @include('office.partials.document-lines-and-totals', [
                            'revision' => $revision,
                            'variant' => 'simple',
                        ])
                    </div>

                    <!-- Section 3: รายละเอียดใบลดหนี้และวันที่ -->
                    <div class="form-section-card">
                        <div class="section-badge-header">
                            <div class="section-badge-icon">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <h2 class="section-badge-title">รายละเอียดเอกสารใบลดหนี้</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">วันที่ออกเอกสาร <span class="text-danger">*</span></label>
                                <input type="date" name="issue_date" class="form-control rounded-3 py-2" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-8 col-12">
                                <label class="form-label-custom">สาเหตุการออกใบลดหนี้ (Reason / Remark)</label>
                                <input type="text" name="reason" class="form-control rounded-3 py-2" value="{{ old('reason') }}" placeholder="ระบุสาเหตุ เช่น คืนสินค้า, สินค้าชำรุด, คำนวณราคาผิดพลาด...">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">หมายเหตุเพิ่มเติม</label>
                                <textarea name="note" class="form-control rounded-3" rows="3" placeholder="กรอกหมายเหตุ หรือข้อความเพิ่มเติมสำหรับใบลดหนี้...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <button type="submit" class="btn-submit-blue flex-grow-1">
                            <i class="ri-check-line fs-18"></i>
                            บันทึกใบลดหนี้ (Save Credit Note)
                        </button>
                        <a href="{{ route('office.tax-invoices.show', $taxInvoice) }}" class="btn-back-white">
                            ยกเลิก
                        </a>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

@if ($standalone || $isEdit)
@section('script')
@include('office.partials.document-form-scripts')
@endsection
@endif
