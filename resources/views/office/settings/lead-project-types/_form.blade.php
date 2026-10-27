@php $row = $row ?? null; @endphp

<div class="mb-3">
    <label class="form-label" for="project_type_name_{{ $row->id ?? 'new' }}">
        ชื่อประเภทงาน <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="name"
           id="project_type_name_{{ $row->id ?? 'new' }}"
           class="form-control"
           value="{{ old('name', $row->name ?? '') }}"
           placeholder="เช่น งานติดตั้งระบบ, งานออกแบบ, บริการซ่อมบำรุง"
           required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="project_type_category_{{ $row->id ?? 'new' }}">
            หมวดสินค้าแนะนำ
        </label>
        <select name="product_service_category_id" id="project_type_category_{{ $row->id ?? 'new' }}" class="form-select">
            <option value="">— ไม่ระบุ —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('product_service_category_id', $row->product_service_category_id ?? '') === (string) $cat->id)>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text text-muted">เชื่อมโยงหมวดสินค้าเพื่อช่วยแนะนำรายการขาย</div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label" for="project_type_code_{{ $row->id ?? 'new' }}">
            รหัสอ้างอิง (Code)
        </label>
        <input type="text"
               name="code"
               id="project_type_code_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('code', $row->code ?? '') }}"
               placeholder="เช่น install, design, service">
        <div class="form-text text-muted">ภาษาอังกฤษและตัวเลข (ไม่จำเป็นต้องระบุ)</div>
    </div>
</div>

<div class="row align-items-center">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="project_type_sort_{{ $row->id ?? 'new' }}">
            ลำดับการแสดงผล
        </label>
        <input type="number"
               name="sort"
               id="project_type_sort_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('sort', $row->sort ?? 0) }}"
               min="0">
        <div class="form-text text-muted">ลำดับน้อยจะแสดงก่อนในตัวเลือก</div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label d-block">สถานะการใช้งาน</label>
        <div class="form-check form-switch form-switch-md mt-2">
            <input class="form-check-input"
                   type="checkbox"
                   role="switch"
                   name="is_active"
                   value="1"
                   id="project_type_active_{{ $row->id ?? 'new' }}"
                   @checked(old('is_active', $row->is_active ?? true))>
            <label class="form-check-label ms-1" for="project_type_active_{{ $row->id ?? 'new' }}">
                เปิดใช้งาน
            </label>
        </div>
    </div>
</div>
