@extends('office.layout.office')
@php
    $standalone = $standalone ?? false;
    $isEdit = isset($salesOrder);
    $revision = $revision ?? null;
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').' Sales Order | Hi Monday')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb & Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">{{ $isEdit ? 'แก้ไขใบสั่งขาย' : 'สร้างใบสั่งขายใหม่' }}</span>
        <div class="fs-13 text-muted">
            <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <a href="{{ route('office.sales-orders.index') }}"
                class="text-muted text-decoration-none">ใบสั่งขาย (SO)</a> <i class="ri-arrow-right-s-line"></i> <span
                class="fw-semibold text-dark">{{ $isEdit ? 'แก้ไข '.$salesOrder->displayNo() : 'สร้างเอกสารใหม่' }}</span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">
            {{ $isEdit ? 'แก้ไขใบสั่งขาย '.$salesOrder->displayNo() : 'สร้างใบสั่งขายใหม่ (Sales Order)' }}
        </h4>
    </div>

    @include('office.settings.partials.flash')

    @if ($standalone || $isEdit)
        @include('office.partials.document-standalone-form-wrap', [
            'formAction' => $isEdit
                ? route('office.sales-orders.update', $salesOrder)
                : route('office.sales-orders.store.standalone'),
            'formMethod' => $isEdit ? 'put' : 'post',
            'showStandaloneAlert' => ! $isEdit,
            'cancelUrl' => $isEdit
                ? route('office.sales-orders.show', $salesOrder)
                : route('office.sales-orders.index'),
            'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกใบสั่งขาย',
            'showPaymentTerms' => true,
            'headerTitle' => 'ข้อมูลคำสั่งขายและวันที่',
            'headerFieldsView' => 'office.sales-orders.partials.standalone-header-fields',
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
            'paymentRows' => $paymentRows ?? null,
            'paymentMode' => $paymentMode ?? null,
        ])
    @else
        @php($revision = $quotation->currentRevision)
        <div class="alert bg-primary-subtle text-primary border-0 d-flex align-items-center gap-2 mb-3 py-2">
            <i class="ri-file-text-line fs-18"></i>
            <span>สร้างใบสั่งขายอ้างอิงจากใบเสนอราคา <strong>{{ $quotation->displayNo() }}</strong></span>
        </div>

        <form method="post" action="{{ route('office.quotations.sales-orders.store', $quotation) }}" class="document-form">
            @csrf
            <div class="emp-card-main mb-4">
                {{-- ส่วนที่ 1: ข้อมูลคำสั่งขายและวันที่ --}}
                <h6 class="section-header-title">
    <i class="ri-file-list-3-line"></i>
    <span>ข้อมูลคำสั่งขายและวันที่</span>
</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-6 border-end">
                        <div class="contact-card h-100 mb-0">
                            <div class="contact-card-header">
                                <span class="contact-number"><i class="ri-building-line me-1 text-primary"></i> ข้อมูลอ้างอิง</span>
                            </div>
                            <p class="mb-2 fs-13"><strong>ลูกค้า:</strong> {{ $quotation->partner?->partner_name_th ?: ($revision?->customer_name ?: '-') }}</p>
                            <p class="mb-0 fs-13"><strong>Opportunity:</strong> {{ $quotation->opportunity?->opportunity_no ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold">วันที่สั่งขาย <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fs-13 fw-semibold">เลขที่ PO ลูกค้า</label>
                            <input type="text" name="customer_po_no" class="form-control" value="{{ old('customer_po_no') }}" placeholder="เช่น PO-20260901">
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 2: รายการสินค้าและบริการ --}}
                <h6 class="section-header-title mt-5">
    <i class="ri-shopping-cart-2-line"></i>
    <span>รายการสินค้าและบริการ (จากใบเสนอราคา)</span>
</h6>
                <div class="mb-4">
                    @include('office.partials.document-lines-and-totals', [
                        'revision' => $revision,
                        'variant' => 'standard',
                    ])
                </div>

                {{-- Action Bar --}}
                @include('office.partials.document-form-actionbar', [
                    'cancelUrl' => route('office.quotations.show', $quotation),
                    'submitLabel' => 'บันทึกใบสั่งขาย',
                    'left' => 'อ้างอิงจาก: <strong>' . e($quotation->displayNo()) . '</strong>',
                ])
            </div>
        </form>
    @endif
</div>
@endsection

@section('style')
@include('office.partials.document-form-styles')
@endsection

@section('script')
@include('office.partials.document-form-scripts')
@endsection

