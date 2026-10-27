@extends('office.layout.office')
@section('title', ($creditNote->displayNo() ?: 'Credit Note Record').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@include('office.partials.sales-journey-styles')
<style>
    @media print {
        #page-topbar, .app-menu, .footer, .doc-actionbar, .page-title-box, .btn, #back-to-top {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }
        .page-content {
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $revision = $creditNote->currentRevision;
    $relatedItems = [];
    if ($creditNote->taxInvoice) {
        $relatedItems[] = [
            'url' => route('office.tax-invoices.show', $creditNote->taxInvoice),
            'label' => 'Tax Invoice: '.$creditNote->taxInvoice->displayNo(),
            'badge' => 'TI',
            'badge_class' => 'bg-success-subtle text-success',
        ];
    }
    if ($creditNote->salesOrder) {
        $relatedItems[] = [
            'url' => route('office.sales-orders.show', $creditNote->salesOrder),
            'label' => 'Sales Order: '.$creditNote->salesOrder->displayNo(),
            'badge' => 'SO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Credit Notes',
        'parentUrl' => route('office.credit-notes.index'),
        'title' => $creditNote->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่ออก', 'value' => $revision?->issue_date?->format('d/m/Y'), 'class' => 'col-sm-3'],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-2'],
            ['label' => 'สาเหตุการลดหนี้', 'value' => $revision?->reason ?: '-', 'class' => 'col-sm-7'],
            ['label' => 'หมายเหตุ', 'value' => $revision?->note ?: '-', 'class' => 'col-sm-12'],
        ]"
    >
        @if ($relatedItems !== [])
            <x-slot:related>
                @include('office.partials.document-related-links', ['items' => $relatedItems])
            </x-slot:related>
        @endif

        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$creditNote->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.credit-notes.index')"
            >
                <x-slot:actions>
                    <button type="button" onclick="window.print()" class="btn btn-primary">
                        <i class="ri-printer-line me-1"></i> พิมพ์ใบลดหนี้
                    </button>
                    @include('office.partials.document-issued-show-actions', [
                        'revision' => $revision,
                        'editRoute' => route('office.credit-notes.edit', $creditNote),
                        'destroyRoute' => route('office.credit-notes.destroy', $creditNote),
                        'issueRoute' => route('office.credit-notes.issue', $creditNote),
                        'cancelRoute' => route('office.credit-notes.cancel', $creditNote),
                    ])
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection
