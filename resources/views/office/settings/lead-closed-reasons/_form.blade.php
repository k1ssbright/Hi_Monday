@php $row = $row ?? null; @endphp

<div class="mb-3">
    <label class="form-label" for="reason_name_{{ $row->id ?? 'new' }}">
        ชื่อเหตุผล <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="name"
           id="reason_name_{{ $row->id ?? 'new' }}"
           class="form-control"
           value="{{ old('name', $row->name ?? '') }}"
           placeholder="เช่น งบประมาณไม่เพียงพอ, เลือกเจ้าอื่น, ติดต่อไม่ได้"
           required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="reason_applies_{{ $row->id ?? 'new' }}">
            ใช้กับ <span class="text-danger">*</span>
        </label>
        <select name="applies_to" id="reason_applies_{{ $row->id ?? 'new' }}" class="form-select" required>
            <option value="lead" @selected(old('applies_to', $row->applies_to ?? 'lead') === 'lead')>Lead</option>
            <option value="opportunity" @selected(old('applies_to', $row->applies_to ?? 'lead') === 'opportunity')>Opportunity</option>
            <option value="both" @selected(old('applies_to', $row->applies_to ?? 'lead') === 'both')>Lead + Opportunity</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label" for="reason_code_{{ $row->id ?? 'new' }}">
            รหัสอ้างอิง (Code)
        </label>
        <input type="text"
               name="code"
               id="reason_code_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('code', $row->code ?? '') }}"
               placeholder="เช่น no_budget, competitor">
        <div class="form-text text-muted">ภาษาอังกฤษและตัวเลข (ไม่จำเป็นต้องระบุ)</div>
    </div>
</div>

<div class="row align-items-start">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="reason_sort_{{ $row->id ?? 'new' }}">
            ลำดับการแสดงผล
        </label>
        <input type="number"
               name="sort"
               id="reason_sort_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('sort', $row->sort ?? 0) }}"
               min="0">
        <div class="form-text text-muted">ลำดับน้อยจะแสดงก่อนในตัวเลือก</div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label d-block">การตั้งค่าเพิ่มเติม</label>
        <div class="d-flex flex-column gap-2 mt-2">
            <div class="form-check form-switch form-switch-md">
                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       name="allows_reopen"
                       value="1"
                       id="allows_reopen_{{ $row->id ?? 'new' }}"
                       @checked(old('allows_reopen', $row->allows_reopen ?? false))>
                <label class="form-check-label ms-1" for="allows_reopen_{{ $row->id ?? 'new' }}">
                    อนุญาตให้เปิดใหม่ได้ (Reopen)
                </label>
            </div>
            <div class="form-check form-switch form-switch-md">
                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       name="is_active"
                       value="1"
                       id="reason_active_{{ $row->id ?? 'new' }}"
                       @checked(old('is_active', $row->is_active ?? true))>
                <label class="form-check-label ms-1" for="reason_active_{{ $row->id ?? 'new' }}">
                    เปิดใช้งาน
                </label>
            </div>
        </div>
    </div>
</div>
