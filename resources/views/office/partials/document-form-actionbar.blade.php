@php
    $cancelUrl = $cancelUrl ?? '#';
    $submitLabel = $submitLabel ?? 'บันทึก';
    $left = $left ?? null;
    $documentNo = $documentNo ?? null;
    $revisionNo = $revisionNo ?? null;
    $status = $status ?? null;
@endphp
<div class="doc-actionbar">
    @if ($documentNo)
        <div class="d-flex flex-wrap align-items-center gap-2.5 fs-14">
            <span class="text-secondary fw-medium">เลขที่:</span>
            <span class="fw-bold text-dark fs-15 me-1">{{ $documentNo }}</span>
            @if ($revisionNo)
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-2 px-2 py-1 fs-12 fw-semibold">R{{ $revisionNo }}</span>
            @endif
            @if ($status)
                @php
                    $statusLower = strtolower($status);
                    $statusBadge = match($statusLower) {
                        'issued' => 'bg-success-subtle text-success border-success-subtle',
                        'voided', 'cancelled' => 'bg-danger-subtle text-danger border-danger-subtle',
                        'draft' => 'bg-warning-subtle text-warning border-warning-subtle',
                        default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                    };
                @endphp
                <span class="badge {{ $statusBadge }} border px-2.5 py-1 rounded-pill fs-12 fw-semibold text-uppercase">{{ $status }}</span>
            @endif
        </div>
    @elseif ($left)
        <div class="text-muted fs-13 d-flex align-items-center gap-2">
            <span class="badge bg-light text-muted border px-3 py-2 fs-12 rounded-pill">
                <i class="ri-file-info-line me-1 text-primary"></i> {!! $left !!}
            </span>
        </div>
    @else
        <div></div>
    @endif
    <div class="d-flex gap-3 flex-wrap align-items-center">
        @if (isset($cancelUrl))
            <a href="{{ $cancelUrl }}" class="btn btn-premium-light">ยกเลิก</a>
        @endif
        @if (isset($submitLabel))
            <button class="btn btn-premium-primary" type="submit">
                <i class="ri-save-3-line align-middle"></i>
                <span>{{ $submitLabel }}</span>
            </button>
        @endif
    </div>
</div>
