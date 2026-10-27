@extends('office.layout.office')
@section('title', ($debitNote->displayNo() ?: 'Debit Note Record').' | Hi Monday')

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
    $revision = $debitNote->currentRevision;
    $relatedItems = [];
    if ($debitNote->taxInvoice) {
        $relatedItems[] = [
            'url' => route('office.tax-invoices.show', $debitNote->taxInvoice),
            'label' => 'Tax Invoice: '.$debitNote->taxInvoice->displayNo(),
            'badge' => 'TI',
            'badge_class' => 'bg-success-subtle text-success',
        ];
    }
    if ($debitNote->salesOrder) {
        $relatedItems[] = [
            'url' => route('office.sales-orders.show', $debitNote->salesOrder),
            'label' => 'Sales Order: '.$debitNote->salesOrder->displayNo(),
            'badge' => 'SO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Debit Notes',
        'parentUrl' => route('office.debit-notes.index'),
        'title' => $debitNote->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่ออก', 'value' => $revision?->issue_date?->format('d/m/Y'), 'class' => 'col-sm-3'],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-2'],
            ['label' => 'สาเหตุการเพิ่มหนี้', 'value' => $revision?->reason ?: '-', 'class' => 'col-sm-7'],
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
                :document-no="$debitNote->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.debit-notes.index')"
            >
                <x-slot:actions>
                    <button type="button" onclick="window.print()" class="btn btn-primary">
                        <i class="ri-printer-line me-1"></i> พิมพ์ใบเพิ่มหนี้
                    </button>
                    @include('office.partials.document-issued-show-actions', [
                        'revision' => $revision,
                        'editRoute' => route('office.debit-notes.edit', $debitNote),
                        'destroyRoute' => route('office.debit-notes.destroy', $debitNote),
                        'issueRoute' => route('office.debit-notes.issue', $debitNote),
                        'cancelRoute' => route('office.debit-notes.cancel', $debitNote),
                    ])
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection
