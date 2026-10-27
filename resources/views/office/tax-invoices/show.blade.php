@extends('office.layout.office')
@section('title', ($taxInvoice->displayNo() ?: 'Tax Invoice Record').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@include('office.partials.sales-journey-styles')
@endsection

@section('content')
@php
    $revision = $taxInvoice->currentRevision;
    $relatedItems = [];
    if ($taxInvoice->salesOrder) {
        $relatedItems[] = [
            'url' => route('office.sales-orders.show', $taxInvoice->salesOrder),
            'label' => 'Sales Order: '.$taxInvoice->salesOrder->displayNo(),
            'badge' => 'SO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }
    if ($taxInvoice->billingNote) {
        $relatedItems[] = [
            'url' => route('office.billing-notes.show', $taxInvoice->billingNote),
            'label' => 'Billing Note: '.$taxInvoice->billingNote->displayNo(),
            'badge' => 'BN',
            'badge_class' => 'bg-info-subtle text-info',
        ];
    }
    if ($taxInvoice->deliveryNote) {
        $relatedItems[] = [
            'url' => route('office.delivery-notes.show', $taxInvoice->deliveryNote),
            'label' => 'Delivery Note: '.$taxInvoice->deliveryNote->displayNo(),
            'badge' => 'DLV',
            'badge_class' => 'bg-secondary-subtle text-secondary',
        ];
    }
    foreach ($taxInvoice->receipts as $rct) {
        $relatedItems[] = [
            'url' => route('office.receipts.show', $rct),
            'label' => $rct->displayNo(),
            'badge' => 'RCT',
            'badge_class' => 'bg-success-subtle text-success',
        ];
    }
    foreach ($taxInvoice->creditNotes as $cn) {
        $relatedItems[] = [
            'url' => route('office.credit-notes.show', $cn),
            'label' => $cn->displayNo(),
            'badge' => 'CN',
            'badge_class' => 'bg-warning-subtle text-warning',
        ];
    }
    foreach ($taxInvoice->debitNotes as $dbn) {
        $relatedItems[] = [
            'url' => route('office.debit-notes.show', $dbn),
            'label' => $dbn->displayNo(),
            'badge' => 'DBN',
            'badge_class' => 'bg-danger-subtle text-danger',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Tax Invoices',
        'parentUrl' => route('office.tax-invoices.index'),
        'title' => $taxInvoice->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่ออก', 'value' => $revision?->issue_date?->format('d/m/Y')],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-3'],
            ['label' => 'หมายเหตุ', 'value' => $revision?->note, 'class' => 'col-sm-8'],
        ]"
    >
        @if ($relatedItems !== [])
            <x-slot:related>
                @include('office.partials.document-related-links', ['items' => $relatedItems])
            </x-slot:related>
        @endif

        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$taxInvoice->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.tax-invoices.index')"
            >
                <x-slot:actions>
                    @include('office.partials.document-issued-show-actions', [
                        'revision' => $revision,
                        'editRoute' => route('office.tax-invoices.edit', $taxInvoice),
                        'destroyRoute' => route('office.tax-invoices.destroy', $taxInvoice),
                        'issueRoute' => route('office.tax-invoices.issue', $taxInvoice),
                        'cancelRoute' => route('office.tax-invoices.cancel', $taxInvoice),
                    ])
                    @if ($taxInvoice->canCreateReceipt())
                        <a href="{{ route('office.tax-invoices.receipts.create', $taxInvoice) }}" class="btn btn-primary">
                            <i class="ri-cash-line"></i> สร้าง Receipt
                        </a>
                    @endif
                    @if ($taxInvoice->canCreateCreditNote())
                        <a href="{{ route('office.tax-invoices.credit-notes.create', $taxInvoice) }}" class="btn btn-soft-warning">
                            <i class="ri-file-reduce-line"></i> สร้าง Credit Note
                        </a>
                    @endif
                    @if ($taxInvoice->canCreateDebitNote())
                        <a href="{{ route('office.tax-invoices.debit-notes.create', $taxInvoice) }}" class="btn btn-soft-danger">
                            <i class="ri-file-add-line"></i> สร้าง Debit Note
                        </a>
                    @endif
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection
