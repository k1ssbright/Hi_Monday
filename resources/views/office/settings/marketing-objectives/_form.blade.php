<div class="row g-3">
    <div class="col-12">
        <label class="form-label">ชื่อ</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $row->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">ลำดับ</label>
        <input type="number" name="sort" class="form-control" value="{{ old('sort', $row->sort ?? 0) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="obj_{{ $row->id ?? 'new' }}" @checked(old('is_active', $row->is_active ?? true))>
            <label class="form-check-label" for="obj_{{ $row->id ?? 'new' }}">ใช้งาน</label>
        </div>
    </div>
</div>
