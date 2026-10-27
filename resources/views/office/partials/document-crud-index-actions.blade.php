@props([
    'revision' => null,
    'editRoute' => null,
    'destroyRoute' => null,
    'editableStatuses' => ['draft'],
    'rejectStatuses' => ['rejected'],
])

@if ($revision && $editRoute && (in_array($revision->status, $editableStatuses, true) || in_array($revision->status, $rejectStatuses, true)))
    <a href="{{ $editRoute }}" class="action-btn-circle" title="แก้ไข">
        <i class="ri-pencil-line text-info"></i>
    </a>
@endif
@if ($revision && $destroyRoute && $revision->status === 'draft')
    <form action="{{ $destroyRoute }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบเอกสารนี้?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="action-btn-circle" title="ลบ">
            <i class="ri-delete-bin-line text-danger"></i>
        </button>
    </form>
@endif
