@php $row = $row ?? null; @endphp

<div class="mb-3">
    <label class="form-label" for="channel_name_{{ $row->id ?? 'new' }}">
        ชื่อช่องทาง <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="name"
           id="channel_name_{{ $row->id ?? 'new' }}"
           class="form-control"
           value="{{ old('name', $row->name ?? '') }}"
           placeholder="เช่น Facebook Ads, LINE OA, เว็บไซต์, ลูกค้าแนะนำ"
           required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="channel_kind_{{ $row->id ?? 'new' }}">
            กลุ่มช่องทาง <span class="text-danger">*</span>
        </label>
        <select name="kind" id="channel_kind_{{ $row->id ?? 'new' }}" class="form-select" required>
            @php
                $kindOptions = [
                    'inbound'  => 'Inbound (ลูกค้าติดต่อเข้ามาเอง)',
                    'paid'     => 'Paid Ads (โฆษณาแบบเสียเงิน)',
                    'owned'    => 'Owned Media (ช่องทางของธุรกิจ)',
                    'outbound' => 'Outbound (ทีมงานติดต่อออกไป)',
                    'partner'  => 'Partner (พันธมิตร / แนะนำต่อ)',
                    'offline'  => 'Offline (หน้าร้าน / งานอีเวนต์)',
                ];
                $selectedKind = old('kind', $row->kind ?? 'inbound');
            @endphp
            @foreach ($kindOptions as $val => $label)
                <option value="{{ $val }}" @selected($selectedKind === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label" for="channel_code_{{ $row->id ?? 'new' }}">
            รหัสอ้างอิง (Code)
        </label>
        <input type="text"
               name="code"
               id="channel_code_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('code', $row->code ?? '') }}"
               placeholder="เช่น fb_ads, line_oa">
        <div class="form-text text-muted">ภาษาอังกฤษและตัวเลข (ไม่จำเป็นต้องระบุ)</div>
    </div>
</div>

<div class="row align-items-center">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="channel_sort_{{ $row->id ?? 'new' }}">
            ลำดับการแสดงผล
        </label>
        <input type="number"
               name="sort"
               id="channel_sort_{{ $row->id ?? 'new' }}"
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
                   id="channel_active_{{ $row->id ?? 'new' }}"
                   @checked(old('is_active', $row->is_active ?? true))>
            <label class="form-check-label ms-1" for="channel_active_{{ $row->id ?? 'new' }}">
                เปิดใช้งาน
            </label>
        </div>
    </div>
</div>
