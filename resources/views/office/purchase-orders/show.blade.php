@extends('office.layout.office')
@section('title', ($purchaseOrder->displayNo() ?: 'ใบสั่งซื้อ').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@endsection

@section('content')
@php
    $revision = $purchaseOrder->currentRevision;
    $relatedItems = [];
    if ($purchaseOrder->purchaseRequisition) {
        $relatedItems[] = [
            'url' => route('office.purchase-requisitions.show', $purchaseOrder->purchaseRequisition),
            'label' => 'PR: '.$purchaseOrder->purchaseRequisition->displayNo(),
            'badge' => 'PR',
            'badge_class' => 'bg-info-subtle text-info',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานจัดซื้อ',
        'parent' => 'ใบสั่งซื้อ (PO)',
        'parentUrl' => route('office.purchase-orders.index'),
        'title' => $purchaseOrder->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-layout
        :revision="$revision"
        counterparty-title="ข้อมูลผู้ขาย"
        issuer-title="ข้อมูลผู้ซื้อ"
        :meta-fields="[
            ['label' => 'วันที่สั่ง', 'value' => $revision?->order_date?->format('d/m/Y')],
            ['label' => 'เลขอ้างอิงผู้ขาย', 'value' => $revision?->vendor_reference_no],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-3'],
            ['label' => 'ยืนยันโดย', 'value' => $revision?->confirmedBy?->displayName(), 'class' => 'col-sm-4'],
        ]"
    >
        @if ($relatedItems !== [])
            <x-slot:related>
                @include('office.partials.document-related-links', ['items' => $relatedItems])
            </x-slot:related>
        @endif

        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$purchaseOrder->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.purchase-orders.index')"
            >
                <x-slot:actions>
                    @if ($revision && $revision->status === 'draft')
                        <a href="{{ route('office.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-soft-info">
                            <i class="ri-pencil-line"></i> แก้ไข
                        </a>
                        <form method="post" action="{{ route('office.purchase-orders.confirm', $purchaseOrder) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"><i class="ri-checkbox-circle-line"></i> ยืนยันใบสั่งซื้อ</button>
                        </form>
                        <form method="post" action="{{ route('office.purchase-orders.destroy', $purchaseOrder) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบใบสั่งซื้อนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i> ลบ</button>
                        </form>
                        <form method="post" action="{{ route('office.purchase-orders.cancel', $purchaseOrder) }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิกใบสั่งซื้อนี้?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
                        </form>
                    @endif
                    @if ($revision && $revision->status === 'confirmed')
                        <form method="post" action="{{ route('office.purchase-orders.amend', $purchaseOrder) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning"><i class="ri-file-edit-line"></i> แก้ไข (Amend)</button>
                        </form>
                        <form method="post" action="{{ route('office.purchase-orders.cancel', $purchaseOrder) }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิกใบสั่งซื้อนี้?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
                        </form>
                    @endif
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
</div>
@endsection
