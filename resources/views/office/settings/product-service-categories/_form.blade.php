<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ประเภท</label>
        <select name="kind" class="form-select" required>
            <option value="product" @selected(old('kind', $row->kind ?? 'product') === 'product')>สินค้า</option>
            <option value="service" @selected(old('kind', $row->kind ?? '') === 'service')>บริการ</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">รหัส</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $row->code ?? '') }}" placeholder="เช่น CAT-01">
    </div>
    <div class="col-12">
        <label class="form-label">ชื่อ</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $row->name ?? '') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">คำอธิบาย</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $row->description ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_{{ $row->id ?? 'new' }}" @checked(old('is_active', $row->is_active ?? true))>
            <label class="form-check-label" for="is_active_{{ $row->id ?? 'new' }}">ใช้งาน</label>
        </div>
    </div>
</div>
