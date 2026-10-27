@extends('office.layout.office')
@php
    $standalone = $standalone ?? false;
    $isEdit = isset($billingNote);
    $revision = $revision ?? null;
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').' Billing Note | Hi Monday')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb & Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">{{ $isEdit ? 'แก้ไขใบวางบิล' : 'สร้างใบวางบิลใหม่' }}</span>
        <div class="fs-13 text-muted">
            <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <a href="{{ route('office.billing-notes.index') }}"
                class="text-muted text-decoration-none">ใบวางบิล (BN)</a> <i class="ri-arrow-right-s-line"></i> <span
                class="fw-semibold text-dark">{{ $isEdit ? 'แก้ไข '.$billingNote->displayNo() : 'สร้างเอกสารใหม่' }}</span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">
            {{ $isEdit ? 'แก้ไขใบวางบิล '.$billingNote->displayNo() : 'สร้างใบวางบิลใหม่ (Billing Note)' }}
        </h4>
    </div>

    @include('office.settings.partials.flash')

    @if ($standalone || $isEdit)
        @include('office.partials.document-standalone-form-wrap', [
            'formAction' => $isEdit
                ? route('office.billing-notes.update', $billingNote)
                : route('office.billing-notes.store.standalone'),
            'formMethod' => $isEdit ? 'put' : 'post',
            'showStandaloneAlert' => ! $isEdit,
            'cancelUrl' => $isEdit
                ? route('office.billing-notes.show', $billingNote)
                : route('office.billing-notes.index'),
            'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกใบวางบิล',
            'showPaymentTerms' => false,
            'footerTitle' => 'ข้อมูลการออกเอกสารและหมายเหตุ',
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
        <div class="alert bg-primary-subtle text-primary border-0 d-flex align-items-center gap-2 mb-3 py-2">
            <i class="ri-bill-line fs-18"></i>
            <span>สร้างใบวางบิลอ้างอิงจาก Sales Order <strong>{{ $salesOrder->displayNo() }}</strong></span>
        </div>

        <form method="post" action="{{ route('office.sales-orders.billing-notes.store', $salesOrder) }}" class="document-form">
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
    <span>รายการสินค้าและบริการ (จาก Sales Order)</span>
</h6>
                <div class="mb-4">
                    @include('office.partials.document-lines-and-totals', [
                        'revision' => $revision,
                        'variant' => 'standard',
                    ])
                </div>

                {{-- ส่วนที่ 3: เงื่อนไขการชำระเงิน --}}
                @if (($revision?->paymentTerms ?? collect())->isNotEmpty())
                    <h6 class="section-header-title mt-5">
    <i class="ri-bank-card-line"></i>
    <span>เงื่อนไขการชำระเงินที่กำหนดไว้ (Payment Terms)</span>
</h6>
                    <div class="mb-4">
                        @include('office.partials.payment-terms-table', [
                            'terms' => $revision?->paymentTerms ?? collect(),
                            'mode' => $revision?->payment_unit_mode ?? 'percent',
                        ])
                    </div>
                @endif

                {{-- ส่วนที่ 4: ข้อมูลการวางบิลและหมายเหตุ --}}
                <h6 class="section-header-title mt-5">
    <i class="ri-bill-line"></i>
    <span>ข้อมูลการวางบิลและหมายเหตุ</span>
</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fs-13 fw-semibold">วันที่ออกใบวางบิล <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fs-13 fw-semibold">เลือกงวดชำระที่ต้องการวางบิล (จาก SO)</label>
                        <select name="sales_order_payment_term_id" class="form-select select2-form-select" data-placeholder="เลือกงวดชำระเงิน">
                            <option value="">ทั้งใบ (100%)</option>
                            @foreach ($revision?->paymentTerms ?? [] as $term)
                                <option value="{{ $term->id }}" @selected((string) old('sales_order_payment_term_id') === (string) $term->id)>
                                    งวดที่ {{ $term->installment_no }} · {{ number_format((float) $term->percent_amount, 2) }}%
                                    · {{ number_format((float) $term->baht_amount, 2) }} บาท
                                    — {{ \Illuminate\Support\Str::limit($term->condition, 40) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">เลือกงวดเพื่อวางบิลบางส่วนตาม payment terms หรือเลือกทั้งใบหากต้องการวางบิลยอดเต็ม</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fs-13 fw-semibold">หมายเหตุ (Note)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="ระบุเงื่อนไขการรับเงิน หรือหมายเหตุเพิ่มเติม">{{ old('note') }}</textarea>
                    </div>
                </div>

                {{-- Action Bar --}}
                @include('office.partials.document-form-actionbar', [
                    'cancelUrl' => route('office.sales-orders.show', $salesOrder),
                    'submitLabel' => 'บันทึกใบวางบิล',
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

