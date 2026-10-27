@php
    $revision = $revision ?? null;
    $lines = $lines ?? null;
    $variant = $variant ?? 'standard';
@endphp
@include('office.partials.document-lines-readonly', [
    'revision' => $revision,
    'lines' => $lines,
    'variant' => $variant,
])
<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mt-4 pt-1">
    <div></div>
    @include('office.partials.document-totals-summary-display', ['revision' => $revision])
</div>
