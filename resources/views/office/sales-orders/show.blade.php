@extends('office.layout.office')
@section('title', ($salesOrder->displayNo() ?: 'Sales Order Record').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@include('office.partials.sales-journey-styles')
@endsection

@section('content')
@php
    $revision = $salesOrder->currentRevision;
    $relatedItems = [];
    if ($salesOrder->quotation) {
        $relatedItems[] = [
            'url' => route('office.quotations.show', $salesOrder->quotation),
            'label' => 'Quotation: '.$salesOrder->quotation->displayNo(),
            'badge' => 'QT',
            'badge_class' => 'bg-info-subtle text-info',
        ];
    }
    if ($salesOrder->opportunity) {
        $relatedItems[] = [
            'url' => route('office.opportunities.show', $salesOrder->opportunity),
            'label' => 'Opportunity: '.$salesOrder->opportunity->opportunity_no,
            'badge' => 'OPP',
            'badge_class' => 'bg-secondary-subtle text-secondary',
        ];
    }
    foreach ($salesOrder->billingNotes as $bn) {
        $relatedItems[] = [
            'url' => route('office.billing-notes.show', $bn),
            'label' => $bn->displayNo(),
            'badge' => 'BN',
            'badge_class' => 'bg-info-subtle text-info',
        ];
    }
    foreach ($salesOrder->deliveryNotes as $dlv) {
        $relatedItems[] = [
            'url' => route('office.delivery-notes.show', $dlv),
            'label' => $dlv->displayNo(),
            'badge' => 'DLV',
            'badge_class' => 'bg-secondary-subtle text-secondary',
        ];
    }
    foreach ($salesOrder->taxInvoices as $ti) {
        $relatedItems[] = [
            'url' => route('office.tax-invoices.show', $ti),
            'label' => $ti->displayNo(),
            'badge' => 'TI',
            'badge_class' => 'bg-success-subtle text-success',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Sales Orders',
        'parentUrl' => route('office.sales-orders.index'),
        'title' => $salesOrder->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่สั่ง', 'value' => $revision?->order_date?->format('d/m/Y')],
            ['label' => 'เลขที่ PO ลูกค้า', 'value' => $revision?->customer_po_no],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-3'],
            ['label' => 'ยืนยันโดย', 'value' => $revision?->confirmedBy?->displayName(), 'class' => 'col-sm-4'],
        ]"
        :show-payment-terms="true"
        :payment-terms="$revision?->paymentTerms ?? collect()"
        :payment-mode="$revision?->payment_unit_mode ?? 'percent'"
        :attachments="$revision?->attachments ?? collect()"
    >
        @if ($relatedItems !== [])
            <x-slot:related>
                @include('office.partials.document-related-links', ['items' => $relatedItems])
            </x-slot:related>
        @endif

        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$salesOrder->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.sales-orders.index')"
            >
                <x-slot:actions>
                    @if ($revision && $revision->status === 'draft')
                        <a href="{{ route('office.sales-orders.edit', $salesOrder) }}" class="btn btn-soft-info">
                            <i class="ri-pencil-line"></i> แก้ไข
                        </a>
                        <form method="post" action="{{ route('office.sales-orders.confirm', $salesOrder) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"><i class="ri-checkbox-circle-line"></i> ยืนยัน Sales Order</button>
                        </form>
                        <form method="post" action="{{ route('office.sales-orders.destroy', $salesOrder) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบ Sales Order นี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i> ลบ</button>
                        </form>
                        <form method="post" action="{{ route('office.sales-orders.cancel', $salesOrder) }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิก Sales Order นี้?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
                        </form>
                    @endif
                    @if ($revision && $revision->status === 'confirmed')
                        <form method="post" action="{{ route('office.sales-orders.amend', $salesOrder) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning"><i class="ri-file-edit-line"></i> แก้ไข (Amend)</button>
                        </form>
                        <form method="post" action="{{ route('office.sales-orders.cancel', $salesOrder) }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิก Sales Order นี้?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
                        </form>
                    @endif
                    @if ($salesOrder->canCreateBillingNote())
                        <a href="{{ route('office.sales-orders.billing-notes.create', $salesOrder) }}" class="btn btn-primary">
                            <i class="ri-bill-line"></i> สร้าง Billing Note
                        </a>
                    @endif
                    @if ($salesOrder->canCreateDeliveryNote())
                        <a href="{{ route('office.sales-orders.delivery-notes.create', $salesOrder) }}" class="btn btn-soft-primary">
                            <i class="ri-truck-line"></i> สร้าง Delivery Note
                        </a>
                    @endif
                    @if ($salesOrder->canCreateTaxInvoice())
                        <a href="{{ route('office.sales-orders.tax-invoices.create', $salesOrder) }}" class="btn btn-soft-success">
                            <i class="ri-file-paper-2-line"></i> สร้าง Tax Invoice
                        </a>
                    @endif
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection
