@extends('office.layout.office')
@section('title', ($receipt->displayNo() ?: 'Receipt Record').' | Hi Monday')

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
    $revision = $receipt->currentRevision;
    $relatedItems = [];
    if ($receipt->taxInvoice) {
        $relatedItems[] = [
            'url' => route('office.tax-invoices.show', $receipt->taxInvoice),
            'label' => 'Tax Invoice: '.$receipt->taxInvoice->displayNo(),
            'badge' => 'TI',
            'badge_class' => 'bg-success-subtle text-success',
        ];
    }
    if ($receipt->salesOrder) {
        $relatedItems[] = [
            'url' => route('office.sales-orders.show', $receipt->salesOrder),
            'label' => 'Sales Order: '.$receipt->salesOrder->displayNo(),
            'badge' => 'SO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }

    $paymentMethodLabel = match($revision?->payment_method ?? '') {
        'cash' => 'เงินสด (Cash)',
        'cheque' => 'เช็คธนาคาร (Cheque)',
        'credit_card' => 'บัตรเครดิต (Credit Card)',
        default => 'โอนเงินผ่านธนาคาร (Bank Transfer)',
    };
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Receipts',
        'parentUrl' => route('office.receipts.index'),
        'title' => $receipt->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่ออก', 'value' => $revision?->issue_date?->format('d/m/Y')],
            ['label' => 'ช่องทางชำระเงิน', 'value' => $paymentMethodLabel, 'class' => 'col-sm-4'],
            ['label' => 'เลขที่อ้างอิง/เช็ค', 'value' => $revision?->payment_ref ?: '-', 'class' => 'col-sm-4'],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-2'],
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
                :document-no="$receipt->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.receipts.index')"
            >
                <x-slot:actions>
                    <button type="button" onclick="window.print()" class="btn btn-primary">
                        <i class="ri-printer-line me-1"></i> พิมพ์ใบเสร็จ
                    </button>
                    @include('office.partials.document-issued-show-actions', [
                        'revision' => $revision,
                        'editRoute' => route('office.receipts.edit', $receipt),
                        'destroyRoute' => route('office.receipts.destroy', $receipt),
                        'issueRoute' => route('office.receipts.issue', $receipt),
                        'cancelRoute' => route('office.receipts.cancel', $receipt),
                    ])
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection

