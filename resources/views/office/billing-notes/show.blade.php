@extends('office.layout.office')
@section('title', ($billingNote->displayNo() ?: 'Billing Note Record').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@include('office.partials.sales-journey-styles')
@endsection

@section('content')
@php
    $revision = $billingNote->currentRevision;
    $installmentLabel = $revision?->installment_no
        ? 'งวดที่ '.$revision->installment_no.($revision->billing_percent ? ' ('.number_format((float) $revision->billing_percent, 2).'%)' : '')
        : 'ทั้งใบ';
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Billing Notes',
        'parentUrl' => route('office.billing-notes.index'),
        'title' => $billingNote->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่ออก', 'value' => $revision?->issue_date?->format('d/m/Y')],
            ['label' => 'งวดที่วางบิล', 'value' => $installmentLabel],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-3'],
            ['label' => 'Sales Order', 'value' => $billingNote->salesOrder?->displayNo() ?: '-', 'class' => 'col-sm-4'],
        ]"
        :show-payment-terms="true"
        :payment-terms="$revision?->paymentTerms ?? collect()"
        :payment-mode="$revision?->payment_unit_mode ?? 'percent'"
    >
        @if ($billingNote->salesOrder)
            <x-slot:related>
                @include('office.partials.document-related-links', [
                    'items' => [[
                        'url' => route('office.sales-orders.show', $billingNote->salesOrder),
                        'label' => 'Sales Order: '.$billingNote->salesOrder->displayNo(),
                        'badge' => 'SO',
                        'badge_class' => 'bg-primary-subtle text-primary',
                    ]],
                ])
            </x-slot:related>
        @endif
        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$billingNote->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.billing-notes.index')"
            >
                <x-slot:actions>
                    @include('office.partials.document-issued-show-actions', [
                        'revision' => $revision,
                        'editRoute' => route('office.billing-notes.edit', $billingNote),
                        'destroyRoute' => route('office.billing-notes.destroy', $billingNote),
                        'issueRoute' => route('office.billing-notes.issue', $billingNote),
                        'cancelRoute' => route('office.billing-notes.cancel', $billingNote),
                    ])
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection
