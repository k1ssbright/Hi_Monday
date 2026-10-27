@php
    $items = $items ?? [];
@endphp
@if ($items !== [])
<div>
    <h6 class="mb-3">เอกสารที่เกี่ยวข้อง</h6>
    <div class="row g-2">
        @foreach ($items as $item)
            <div class="col-md-6">
                <div class="border rounded p-2 d-flex align-items-center justify-content-between gap-2">
                    <a href="{{ $item['url'] }}" class="link-primary fw-semibold">{{ $item['label'] }}</a>
                    @if (! empty($item['badge']))
                        <span class="badge {{ $item['badge_class'] ?? 'bg-secondary-subtle text-secondary' }}">{{ $item['badge'] }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
