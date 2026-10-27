@props([
    'documentNo' => '-',
    'status' => null,
    'revisionNo' => null,
    'backUrl' => '#',
])

<div class="doc-actionbar">
    <div class="text-muted fs-13 d-flex flex-wrap align-items-center gap-2">
        <span>เลขที่:</span>
        <span class="fw-semibold text-body">{{ $documentNo }}</span>
        @if ($revisionNo)
            <span class="badge bg-info-subtle text-info">R{{ $revisionNo }}</span>
        @endif
        @if ($status)
            <span class="badge bg-primary-subtle text-primary">{{ $status }}</span>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        @isset($actions)
            {{ $actions }}
        @endisset
        <a href="{{ $backUrl }}" class="btn-emp btn-emp-light"><i class="ri-arrow-left-line"></i> กลับ</a>
    </div>
</div>
