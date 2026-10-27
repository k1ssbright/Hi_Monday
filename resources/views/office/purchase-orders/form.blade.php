@extends('office.layout.office')
@php
    $standalone = $standalone ?? false;
    $isEdit = isset($purchaseOrder);
    $revision = $revision ?? (isset($purchaseRequisition) ? $purchaseRequisition->currentRevision : null);
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').'ใบสั่งซื้อ (PO) | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', [
        'section' => 'งานจัดซื้อ',
        'parent' => 'ใบสั่งซื้อ (PO)',
        'parentUrl' => route('office.purchase-orders.index'),
        'title' => $isEdit ? 'แก้ไข '.$purchaseOrder->displayNo() : 'สร้างใบสั่งซื้อ',
    ])
    @include('office.settings.partials.flash')

    @if ($standalone || $isEdit)
        @include('office.partials.document-standalone-form-wrap', [
            'formAction' => $isEdit
                ? route('office.purchase-orders.update', $purchaseOrder)
                : route('office.purchase-orders.store.standalone'),
            'formMethod' => $isEdit ? 'put' : 'post',
            'showStandaloneAlert' => ! $isEdit,
            'cancelUrl' => $isEdit
                ? route('office.purchase-orders.show', $purchaseOrder)
                : route('office.purchase-orders.index'),
            'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกใบสั่งซื้อ',
            'showPaymentTerms' => false,
            'headerFieldsView' => 'office.purchase-orders.partials.standalone-header-fields',
            'seedLines' => $seedLines ?? [],
            'discountType' => old('discount_type', $totalsDefaults['discount_type'] ?? 'percent'),
            'customerParty' => $customerParty ?? [],
            'sellerParty' => $sellerParty ?? [],
            'provinces' => $provinces ?? collect(),
            'customerAmphures' => $customerAmphures ?? collect(),
            'customerTambons' => $customerTambons ?? collect(),
            'totalsDefaults' => $totalsDefaults ?? [],
            'partner' => $partner ?? null,
            'counterpartyTitle' => 'ข้อมูลผู้ขาย',
            'issuerTitle' => 'ข้อมูลผู้ซื้อ',
            'revision' => $revision,
        ])
    @else
        <div class="alert bg-primary-subtle text-primary border-0 d-flex align-items-center gap-2 mb-3 py-2">
            <i class="ri-file-text-line fs-18"></i>
            <span>จากใบขอซื้อ <strong>{{ $purchaseRequisition->displayNo() }}</strong></span>
        </div>

        <form method="post" action="{{ route('office.purchase-requisitions.purchase-orders.store', $purchaseRequisition) }}" class="document-form">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card">
                        <div class="card-body border-bottom border-bottom-dashed p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fs-13"><strong>ผู้ขาย:</strong> {{ $purchaseRequisition->partner?->partner_name_th ?: ($revision?->customer_name ?: '-') }}</p>
                                    <p class="mb-0 fs-13"><strong>ยอดรวม PR:</strong> {{ $revision ? number_format((float) $revision->total_amount, 2) : '-' }} {{ $revision?->currency ?: 'THB' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">วันที่สั่ง</label>
                                        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">เลขอ้างอิงผู้ขาย</label>
                                        <input type="text" name="vendor_reference_no" class="form-control" value="{{ old('vendor_reference_no') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4 border-bottom border-bottom-dashed">
                            @include('office.partials.document-lines-and-totals', [
                                'revision' => $revision,
                                'variant' => 'standard',
                            ])
                        </div>
                    </div>
                    @include('office.partials.document-form-actionbar', [
                        'cancelUrl' => route('office.purchase-requisitions.show', $purchaseRequisition),
                        'submitLabel' => 'บันทึกใบสั่งซื้อ',
                    ])
                </div>
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
