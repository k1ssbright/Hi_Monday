@extends('office.layout.office')
@php
    $standalone = $standalone ?? false;
    $isEdit = isset($deliveryNote);
    $revision = $revision ?? null;
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').' Delivery Note | Hi Monday')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb & Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">{{ $isEdit ? 'แก้ไขใบส่งของ' : 'สร้างใบส่งของใหม่' }}</span>
        <div class="fs-13 text-muted">
            <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <a href="{{ route('office.delivery-notes.index') }}"
                class="text-muted text-decoration-none">ใบส่งของ (DLV)</a> <i class="ri-arrow-right-s-line"></i> <span
                class="fw-semibold text-dark">{{ $isEdit ? 'แก้ไข '.$deliveryNote->displayNo() : 'สร้างเอกสารใหม่' }}</span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">
            {{ $isEdit ? 'แก้ไขใบส่งของ '.$deliveryNote->displayNo() : 'สร้างใบส่งของใหม่ (Delivery Note)' }}
        </h4>
    </div>

    @include('office.settings.partials.flash')

    @if ($standalone || $isEdit)
        @include('office.partials.document-standalone-form-wrap', [
            'formAction' => $isEdit ? route('office.delivery-notes.update', $deliveryNote) : route('office.delivery-notes.store.standalone'),
            'formMethod' => $isEdit ? 'put' : 'post',
            'showStandaloneAlert' => ! $isEdit,
            'cancelUrl' => $isEdit ? route('office.delivery-notes.show', $deliveryNote) : route('office.delivery-notes.index'),
            'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกใบส่งของ',
            'showPaymentTerms' => false,
            'footerTitle' => 'ข้อมูลการส่งสินค้าและหมายเหตุ',
            'footerFieldsView' => 'office.partials.document-issue-date-note-fields',
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
        @php($revision = $salesOrder->currentRevision)
        <div class="alert bg-secondary-subtle text-secondary border-0 d-flex align-items-center gap-2 mb-3 py-2">
            <i class="ri-truck-line fs-18"></i>
            <span>สร้างใบส่งของอ้างอิงจาก Sales Order <strong>{{ $salesOrder->displayNo() }}</strong></span>
        </div>

        <form method="post" action="{{ route('office.sales-orders.delivery-notes.store', $salesOrder) }}" class="document-form">
            @csrf
            <div class="emp-card-main mb-4">
                {{-- ส่วนที่ 1: ข้อมูลลูกค้าและเอกสารอ้างอิง --}}
                <h6 class="section-header-title">
    <i class="ri-file-list-3-line"></i>
    <span>ข้อมูลลูกค้าและคำสั่งขายอ้างอิง</span>
</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-6 border-end">
                        <div class="contact-card h-100 mb-0">
                            <div class="contact-card-header">
                                <span class="contact-number"><i class="ri-user-smile-line me-1 text-primary"></i> ข้อมูลลูกค้า</span>
                            </div>
                            <p class="mb-2 fs-13"><strong>ชื่อลูกค้า:</strong> {{ $salesOrder->partner?->partner_name_th ?: ($revision?->customer_name ?: '-') }}</p>
                            <p class="mb-0 fs-13"><strong>เลขประจำตัวผู้เสียภาษี:</strong> {{ $salesOrder->partner?->tax_number ?: ($revision?->customer_tax_no ?: '-') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="contact-card h-100 mb-0">
                            <div class="contact-card-header">
                                <span class="contact-number"><i class="ri-file-text-line me-1 text-info"></i> ข้อมูล Sales Order</span>
                            </div>
                            <p class="mb-2 fs-13"><strong>เลขที่ SO:</strong> <span class="badge bg-light text-dark border">{{ $salesOrder->displayNo() }}</span></p>
                            <p class="mb-0 fs-13"><strong>ยอดรวมทั้งสิ้น:</strong> <span class="fw-bold text-primary">{{ number_format((float) ($revision?->total_amount ?? 0), 2) }}</span> {{ $revision?->currency ?: 'THB' }}</p>
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 2: รายการสินค้าและบริการ --}}
                <h6 class="section-header-title mt-5">
    <i class="ri-shopping-cart-2-line"></i>
    <span>รายการสินค้าและบริการที่จะส่งมอบ (จาก Sales Order)</span>
</h6>
                <div class="mb-4">
                    @include('office.partials.document-lines-and-totals', [
                        'revision' => $revision,
                        'variant' => 'standard',
                    ])
                </div>

                {{-- ส่วนที่ 3: ข้อมูลการส่งของและหมายเหตุ --}}
                <h6 class="section-header-title mt-5">
    <i class="ri-truck-line"></i>
    <span>ข้อมูลการส่งสินค้าและหมายเหตุ</span>
</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fs-13 fw-semibold">วันที่ออกใบส่งของ <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fs-13 fw-semibold">อ้างอิงใบวางบิล / Billing Note (ถ้ามี)</label>
                        <select name="billing_note_id" class="form-select select2-form-select" data-placeholder="— ไม่ระบุ —">
                            <option value="">— ไม่ระบุ —</option>
                            @foreach ($salesOrder->billingNotes as $bn)
                                <option value="{{ $bn->id }}" @selected(old('billing_note_id', $billingNote?->id) == $bn->id)>{{ $bn->displayNo() }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">เลือกใบวางบิลที่เกี่ยวข้องกับรายการส่งมอบนี้ (ถ้ามี)</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fs-13 fw-semibold">หมายเหตุ (Note)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="ระบุรายละเอียดการจัดส่ง สถานที่ หรือหมายเหตุเพิ่มเติม">{{ old('note') }}</textarea>
                    </div>
                </div>

                {{-- Action Bar --}}
                @include('office.partials.document-form-actionbar', [
                    'cancelUrl' => route('office.sales-orders.show', $salesOrder),
                    'submitLabel' => 'บันทึกใบส่งของ',
                    'left' => 'อ้างอิงจาก SO: <strong>' . e($salesOrder->displayNo()) . '</strong>',
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

