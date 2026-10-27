@extends('office.layout.office')
@section('title', ($deliveryNote->displayNo() ?: 'Delivery Note Record').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@include('office.partials.sales-journey-styles')
@endsection

@section('content')
@php
    $revision = $deliveryNote->currentRevision;
    $relatedItems = [];
    if ($deliveryNote->salesOrder) {
        $relatedItems[] = [
            'url' => route('office.sales-orders.show', $deliveryNote->salesOrder),
            'label' => 'Sales Order: '.$deliveryNote->salesOrder->displayNo(),
            'badge' => 'SO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }
    if ($deliveryNote->billingNote) {
        $relatedItems[] = [
            'url' => route('office.billing-notes.show', $deliveryNote->billingNote),
            'label' => 'Billing Note: '.$deliveryNote->billingNote->displayNo(),
            'badge' => 'BN',
            'badge_class' => 'bg-info-subtle text-info',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Delivery Notes',
        'parentUrl' => route('office.delivery-notes.index'),
        'title' => $deliveryNote->displayNo(),
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
                :document-no="$deliveryNote->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.delivery-notes.index')"
            >
                <x-slot:actions>
                    @include('office.partials.document-issued-show-actions', [
                        'revision' => $revision,
                        'editRoute' => route('office.delivery-notes.edit', $deliveryNote),
                        'destroyRoute' => route('office.delivery-notes.destroy', $deliveryNote),
                        'issueRoute' => route('office.delivery-notes.issue', $deliveryNote),
                        'cancelRoute' => route('office.delivery-notes.cancel', $deliveryNote),
                    ])
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection
