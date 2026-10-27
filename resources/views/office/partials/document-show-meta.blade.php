@php
    $fields = $fields ?? [];
@endphp
@if ($fields !== [])
<div class="row g-3 mb-0">
    @foreach ($fields as $field)
        <div class="{{ $field['class'] ?? 'col-sm-4' }}">
            <label class="form-label text-muted fs-13 fw-medium mb-1">{{ $field['label'] }}</label>
            @if (! empty($field['html']))
                <div class="form-control bg-light border-0 fw-semibold text-dark rounded-3 h-auto py-2">{!! $field['html'] !!}</div>
            @else
                <input type="text" class="form-control bg-light border-0 fw-semibold text-dark rounded-3 py-2 fs-13" readonly value="{{ $field['value'] ?? '-' }}">
            @endif
            @if (! empty($field['hint']))
                <div class="form-text text-muted fs-12 mt-1">{{ $field['hint'] }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

