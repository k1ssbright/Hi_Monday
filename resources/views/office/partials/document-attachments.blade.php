@php
    $embedded = $embedded ?? false;
@endphp
@if (($attachments ?? collect())->isNotEmpty())
<div @class(['card mb-3' => !$embedded, 'mb-0' => $embedded])>
    <div @class(['card-body' => !$embedded, 'p-0' => $embedded])>
        <h6 class="mb-3">เอกสารแนบ</h6>
        <ul class="list-group list-group-flush border rounded">
            @foreach ($attachments as $attachment)
                <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <span class="badge bg-{{ $attachment->kind === 'po' ? 'success' : 'secondary' }}-subtle text-{{ $attachment->kind === 'po' ? 'success' : 'secondary' }} me-1">
                            {{ $attachment->kind === 'po' ? 'PO' : 'เพิ่มเติม' }}
                        </span>
                        <a href="{{ $attachment->publicUrl() }}" target="_blank" rel="noopener" class="link-primary">
                            {{ $attachment->original_name ?: $attachment->file_name }}
                        </a>
                    </div>
                    @if ($attachment->file_size)
                        <span class="text-muted small">{{ number_format($attachment->file_size / 1024, 1) }} KB</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
