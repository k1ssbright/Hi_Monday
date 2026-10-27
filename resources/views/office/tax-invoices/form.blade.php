@extends('office.layout.office')

@php
    $standalone = $standalone ?? false;
    $isEdit = isset($taxInvoice);
    $revision = $revision ?? null;
@endphp

@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').' ใบกำกับภาษี (Tax Invoice) | Hi Monday')

@section('style')
<style>
    :root {
        --ti-primary: #2563eb;
        --ti-primary-dark: #1d4ed8;
        --ti-dark: #0f172a;
        --ti-border: #e2e8f0;
    }

    .form-section-card {
        background-color: #ffffff;
        border: 1px solid var(--ti-border);
        border-radius: 14px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        transition: all 0.2s ease;
    }

    .form-section-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
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
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
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
        color: var(--ti-dark);
        margin: 0;
    }

    .form-label-custom {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
        display: block;
    }

    .required-star {
        color: #ef4444;
        font-weight: 600;
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
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        text-decoration: none;
    }

    .btn-submit-blue:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        transform: translateY(-1px);
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
        transition: all 0.2s ease;
    }

    .btn-back-white:hover {
        background-color: #f8fafc;
        color: var(--ti-dark);
        border-color: #94a3b8;
    }
</style>
@if ($standalone || $isEdit)
    @include('office.partials.document-form-styles')
@endif
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Bar -->
    <div class="row align-items-center mb-3">
        <div class="col-sm-6">
            <h4 class="fw-bold mb-0 text-dark">
                <i class="ri-file-text-fill me-2 text-primary"></i>
                {{ $isEdit ? 'แก้ไขใบกำกับภาษี (Tax Invoice)' : 'สร้างใบกำกับภาษีใหม่ (Tax Invoice)' }}
            </h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb" class="d-inline-block">
                <ol class="breadcrumb mb-0 bg-transparent py-0 px-0 fs-13">
                    <li class="breadcrumb-item"><a href="{{ route('office.tax-invoices.index') }}" class="text-decoration-none text-muted">ใบกำกับภาษี</a></li>
                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">{{ $isEdit ? 'แก้ไขเอกสาร' : 'สร้างเอกสารใหม่' }}</li>
                </ol>
            </nav>
        </div>
    </div>


    @include('office.settings.partials.flash')

    @if ($standalone || $isEdit)
        @include('office.partials.document-standalone-form-wrap', [
            'formAction' => $isEdit ? route('office.tax-invoices.update', $taxInvoice) : route('office.tax-invoices.store.standalone'),
            'formMethod' => $isEdit ? 'put' : 'post',
            'showStandaloneAlert' => ! $isEdit,
            'cancelUrl' => $isEdit ? route('office.tax-invoices.show', $taxInvoice) : route('office.tax-invoices.index'),
            'submitLabel' => $isEdit ? 'บันทึกการแก้ไขใบกำกับภาษี' : 'บันทึกใบกำกับภาษี',
            'showPaymentTerms' => false,
            'headerFieldsView' => 'office.tax-invoices.partials.header-fields',
            'documentNo' => $isEdit ? $taxInvoice->displayNo() : null,
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
        <!-- Sales Order Reference Banner Card -->
        <div class="alert bg-primary-subtle text-primary border border-primary-subtle rounded-4 p-3 d-flex align-items-center gap-3 mb-4">
            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                <i class="ri-file-paper-2-line fs-20"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-14">สร้างใบกำกับภาษีอ้างอิงจาก Sales Order</div>
                <div class="fs-13 text-secondary">
                    เลขที่ Sales Order: <strong class="text-primary">{{ $salesOrder->displayNo() }}</strong> | ลูกค้า: <strong>{{ $salesOrder->partner?->partner_name_th ?: 'ไม่ระบุ' }}</strong>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('office.sales-orders.tax-invoices.store', $salesOrder) }}" class="document-form">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <!-- Section Card 1: ข้อมูลลูกค้า & มูลค่ารวม -->
                    <div class="form-section-card">
                        <div class="section-badge-header">
                            <div class="section-badge-icon">
                                <i class="ri-user-shared-line"></i>
                            </div>
                            <h2 class="section-badge-title">ข้อมูลลูกค้าและเอกสารอ้างอิง</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label-custom">ลูกค้า / องค์กร</label>
                                <div class="form-control bg-light border-0 fw-semibold text-dark fs-14 py-2.5">
                                    <i class="ri-building-line me-1 text-primary"></i> {{ $salesOrder->partner?->partner_name_th ?: '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label-custom">ยอดรวม Sales Order</label>
                                <div class="form-control bg-light border-0 fw-bold text-primary fs-15 py-2.5">
                                    ฿{{ number_format((float) ($revision?->total_amount ?? 0), 2) }} {{ $revision?->currency ?: 'THB' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Card 2: รายการสินค้าและสรุปยอด -->
                    <div class="form-section-card">
                        <div class="section-badge-header">
                            <div class="section-badge-icon">
                                <i class="ri-shopping-cart-2-line"></i>
                            </div>
                            <h2 class="section-badge-title">รายการสินค้า / บริการ</h2>
                        </div>

                        @include('office.partials.document-lines-and-totals', [
                            'revision' => $revision,
                            'variant' => 'standard',
                        ])
                    </div>

                    <!-- Section Card 3: รายละเอียดวันที่และเอกสารเกี่ยวเนื่อง -->
                    <div class="form-section-card">
                        <div class="section-badge-header">
                            <div class="section-badge-icon">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <h2 class="section-badge-title">เงื่อนไขและวันที่ออกเอกสาร</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">วันที่ออกเอกสาร <span class="required-star">*</span></label>
                                <input type="date" name="issue_date" class="form-control rounded-3 py-2" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">Billing Note อ้างอิง (ถ้ามี)</label>
                                <select name="billing_note_id" class="form-select rounded-3 py-2">
                                    <option value="">— ไม่ระบุ —</option>
                                    @foreach ($salesOrder->billingNotes as $bn)
                                        <option value="{{ $bn->id }}" @selected(old('billing_note_id', $billingNote?->id) == $bn->id)>{{ $bn->displayNo() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label-custom">Delivery Note อ้างอิง (ถ้ามี)</label>
                                <select name="delivery_note_id" class="form-select rounded-3 py-2">
                                    <option value="">— ไม่ระบุ —</option>
                                    @foreach ($salesOrder->deliveryNotes as $dlv)
                                        <option value="{{ $dlv->id }}" @selected(old('delivery_note_id', $deliveryNote?->id) == $dlv->id)>{{ $dlv->displayNo() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">หมายเหตุเพิ่มเติม</label>
                                <textarea name="note" class="form-control rounded-3" rows="3" placeholder="กรอกหมายเหตุ หรือข้อความเพิ่มเติมสำหรับใบกำกับภาษี...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <button type="submit" class="btn-submit-blue flex-grow-1">
                            <i class="ri-check-line fs-18"></i>
                            บันทึกใบกำกับภาษี
                        </button>
                        <a href="{{ route('office.sales-orders.show', $salesOrder) }}" class="btn-back-white">
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
