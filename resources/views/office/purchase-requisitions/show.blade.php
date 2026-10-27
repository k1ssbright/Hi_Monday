@extends('office.layout.office')
@section('title', ($purchaseRequisition->displayNo() ?: 'ใบขอซื้อ').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@endsection

@section('content')
@php
    $revision = $purchaseRequisition->currentRevision;
    $relatedItems = [];
    foreach ($purchaseRequisition->purchaseOrders as $po) {
        $relatedItems[] = [
            'url' => route('office.purchase-orders.show', $po),
            'label' => $po->displayNo(),
            'badge' => $po->currentRevision?->status ?: 'PO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานจัดซื้อ',
        'parent' => 'ใบขอซื้อ (PR)',
        'parentUrl' => route('office.purchase-requisitions.index'),
        'title' => $purchaseRequisition->displayNo(),
    ])
    @include('office.settings.partials.flash')

    @if ($revision && $revision->status === 'submitted')
        <div class="row justify-content-center mb-3">
            <div class="col-12 col-xl-10">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">อนุมัติใบขอซื้อ</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('office.purchase-requisitions.approve', $purchaseRequisition) }}" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="ri-checkbox-circle-line"></i> อนุมัติ
                            </button>
                        </form>
                        <form method="post" action="{{ route('office.purchase-requisitions.reject', $purchaseRequisition) }}" class="row g-2 align-items-end mt-3">
                            @csrf
                            <div class="col-md-8">
                                <label class="form-label">เหตุผลปฏิเสธ</label>
                                <input type="text" name="rejected_note" class="form-control" placeholder="ระบุเหตุผล" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-danger w-100">ปฏิเสธ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-document-show-layout
        :revision="$revision"
        counterparty-title="ข้อมูลผู้ขาย"
        issuer-title="ข้อมูลผู้ซื้อ"
        :meta-fields="[
            ['label' => 'วันที่ขอ', 'value' => $revision?->request_date?->format('d/m/Y')],
            ['label' => 'วันที่ต้องการ', 'value' => $revision?->required_date?->format('d/m/Y')],
            ['label' => 'หมายเหตุ', 'value' => $revision?->note],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-3'],
            ['label' => 'ส่งโดย', 'value' => $revision?->submittedBy?->displayName(), 'class' => 'col-sm-4'],
            ['label' => 'อนุมัติโดย', 'value' => $revision?->approvedBy?->displayName(), 'class' => 'col-sm-4'],
        ]"
    >
        @if ($relatedItems !== [])
            <x-slot:related>
                @include('office.partials.document-related-links', ['items' => $relatedItems])
            </x-slot:related>
        @endif

        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$purchaseRequisition->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.purchase-requisitions.index')"
            >
                <x-slot:actions>
                    @if ($revision && in_array($revision->status, ['draft', 'rejected'], true))
                        <a href="{{ route('office.purchase-requisitions.edit', $purchaseRequisition) }}" class="btn btn-soft-info">
                            <i class="ri-pencil-line"></i> แก้ไข
                        </a>
                    @endif
                    @if ($revision && $revision->status === 'draft')
                        <form method="post" action="{{ route('office.purchase-requisitions.submit', $purchaseRequisition) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"><i class="ri-send-plane-line"></i> ส่งอนุมัติ</button>
                        </form>
                        <form method="post" action="{{ route('office.purchase-requisitions.destroy', $purchaseRequisition) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบใบขอซื้อนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i> ลบ</button>
                        </form>
                    @endif
                    @if ($revision && in_array($revision->status, ['draft', 'submitted', 'approved'], true) && $purchaseRequisition->purchaseOrders->isEmpty())
                        <form method="post" action="{{ route('office.purchase-requisitions.cancel', $purchaseRequisition) }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิกใบขอซื้อนี้?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
                        </form>
                    @endif
                    @if ($purchaseRequisition->canCreatePurchaseOrder())
                        <a href="{{ route('office.purchase-requisitions.purchase-orders.create', $purchaseRequisition) }}" class="btn btn-primary">
                            <i class="ri-shopping-cart-2-line"></i> สร้างใบสั่งซื้อ (PO)
                        </a>
                    @endif
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
</div>
@endsection
