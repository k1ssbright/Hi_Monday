@props([
    'journey' => [],
    'detailTabId' => 'tab-doc-detail',
    'journeyTabId' => 'tab-sales-journey',
])

<ul class="nav nav-tabs nav-tabs-custom mb-3 d-print-none" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#{{ $detailTabId }}" role="tab">
            <i class="ri-file-text-line me-1"></i> รายละเอียดเอกสาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#{{ $journeyTabId }}" role="tab">
            <i class="ri-git-branch-line me-1"></i> ไทม์ไลน์งานขาย
        </a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="{{ $detailTabId }}" role="tabpanel">
        {{ $slot }}
    </div>
    <div class="tab-pane" id="{{ $journeyTabId }}" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                @include('office.partials.sales-journey-timeline', ['journey' => $journey])
            </div>
        </div>
    </div>
</div>
