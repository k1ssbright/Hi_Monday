@props([
    'revision' => null,
    'editRoute' => null,
    'destroyRoute' => null,
    'issueRoute' => null,
    'cancelRoute' => null,
    'issueLabel' => 'ออกเอกสาร',
])

@if ($revision && $editRoute && $revision->status === 'draft')
    <a href="{{ $editRoute }}" class="btn btn-soft-info">
        <i class="ri-pencil-line"></i> แก้ไข
    </a>
@endif
@if ($revision && $issueRoute && $revision->status === 'draft')
    <form method="post" action="{{ $issueRoute }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success"><i class="ri-checkbox-circle-line"></i> {{ $issueLabel }}</button>
    </form>
@endif
@if ($revision && $destroyRoute && $revision->status === 'draft')
    <form method="post" action="{{ $destroyRoute }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบเอกสารนี้?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i> ลบ</button>
    </form>
@endif
@if ($revision && $cancelRoute && in_array($revision->status, ['draft', 'issued'], true))
    <form method="post" action="{{ $cancelRoute }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิกเอกสารนี้?');">
        @csrf
        <button type="submit" class="btn btn-soft-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
    </form>
@endif
