@php
    $optionsText = is_array($row?->options ?? null) ? implode("\n", $row->options) : '';
@endphp
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">หมวด</label>
        <select name="product_service_category_id" class="form-select" required>
            <option value="">เลือกหมวด</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('product_service_category_id', $row->product_service_category_id ?? '') === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">วัตถุประสงค์</label>
        <select name="purpose" class="form-select">
            <option value="intake" @selected(old('purpose', $row->purpose ?? 'intake') === 'intake')>Intake</option>
            <option value="quality" @selected(old('purpose', $row->purpose ?? '') === 'quality')>Quality</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">ชนิดช่องกรอก</label>
        <select name="input_type" class="form-select">
            @foreach (['text','number','select','checkbox','textarea','date'] as $type)
                <option value="{{ $type }}" @selected(old('input_type', $row->input_type ?? 'text') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">ข้อความคำถาม</label>
        <input type="text" name="label" class="form-control" value="{{ old('label', $row->label ?? '') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">ตัวเลือก (บรรทัดละ 1 ค่า — ใช้กับ select/checkbox)</label>
        <textarea name="options_text" class="form-control" rows="3">{{ old('options_text', $optionsText) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">ลำดับ</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $row->sort_order ?? 0) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end gap-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_required" value="1" id="req_{{ $row->id ?? 'new' }}" @checked(old('is_required', $row->is_required ?? false))>
            <label class="form-check-label" for="req_{{ $row->id ?? 'new' }}">บังคับตอบ</label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act_{{ $row->id ?? 'new' }}" @checked(old('is_active', $row->is_active ?? true))>
            <label class="form-check-label" for="act_{{ $row->id ?? 'new' }}">ใช้งาน</label>
        </div>
    </div>
</div>
