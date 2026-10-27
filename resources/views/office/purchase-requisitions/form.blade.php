@extends('office.layout.office')
@php
    $isEdit = isset($purchaseRequisition);
    $revision = $revision ?? null;
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').'ใบขอซื้อ (PR) | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', [
        'section' => 'งานจัดซื้อ',
        'parent' => 'ใบขอซื้อ (PR)',
        'parentUrl' => route('office.purchase-requisitions.index'),
        'title' => $isEdit ? 'แก้ไข '.$purchaseRequisition->displayNo() : 'สร้างใบขอซื้อ',
    ])
    @include('office.settings.partials.flash')

    @include('office.partials.document-standalone-form-wrap', [
        'formAction' => $isEdit
            ? route('office.purchase-requisitions.update', $purchaseRequisition)
            : route('office.purchase-requisitions.store.standalone'),
        'formMethod' => $isEdit ? 'put' : 'post',
        'showStandaloneAlert' => ! $isEdit,
        'cancelUrl' => $isEdit
            ? route('office.purchase-requisitions.show', $purchaseRequisition)
            : route('office.purchase-requisitions.index'),
        'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกใบขอซื้อ',
        'showPaymentTerms' => false,
        'headerFieldsView' => 'office.purchase-requisitions.partials.header-fields',
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
</div>
@endsection

@section('style')
@include('office.partials.document-form-styles')
@endsection

@section('script')
@include('office.partials.document-form-scripts')
@endsection
