@php
    $revision = $revision ?? null;
@endphp
@if ($revision && method_exists($revision, 'vatTypeLabel'))
<div class="row mb-2">
    <div class="col-lg-4 col-sm-6">
        <div class="row align-items-center g-2">
            <div class="col-lg-4">
                <label class="form-label mb-0 fs-13">ประเภท VAT</label>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control bg-light" readonly value="{{ $revision->vatTypeLabel() }}">
            </div>
        </div>
    </div>
</div>
@endif
