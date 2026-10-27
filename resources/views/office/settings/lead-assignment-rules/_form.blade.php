@php $row = $row ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="rule_name_{{ $row->id ?? 'new' }}">
            ชื่อกฎการมอบหมาย <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="name"
               id="rule_name_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('name', $row->name ?? '') }}"
               placeholder="เช่น กฎรับ Lead กทม. จาก Facebook Ads"
               required>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="rule_priority_{{ $row->id ?? 'new' }}">
            ลำดับความสำคัญ (Priority)
        </label>
        <input type="number"
               name="priority"
               id="rule_priority_{{ $row->id ?? 'new' }}"
               class="form-control"
               value="{{ old('priority', $row->priority ?? 100) }}"
               min="1">
        <div class="form-text text-muted">ตัวเลขน้อยกว่าจะมีผลก่อน</div>
    </div>

    <div class="col-12 mt-3 pt-2 border-top">
        <h6 class="fs-13 text-muted text-uppercase fw-semibold mb-1">เงื่อนไขการคัดกรอง</h6>
        <p class="text-muted fs-12 mb-2">กำหนดเงื่อนไขที่ตรงกับ Lead หากไม่ระบุจะถือว่าใช้ได้กับทุกเงื่อนไข</p>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="rule_channel_{{ $row->id ?? 'new' }}">
            ช่องทางรับ Lead
        </label>
        <select name="lead_channel_id" id="rule_channel_{{ $row->id ?? 'new' }}" class="form-select select2-init" data-placeholder="ทุกช่องทาง">
            <option value="">ทุกช่องทาง</option>
            @foreach ($channels as $c)
                <option value="{{ $c->id }}" @selected((string) old('lead_channel_id', $row->lead_channel_id ?? '') === (string) $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="rule_ad_{{ $row->id ?? 'new' }}">
            แคมเปญโฆษณา
        </label>
        <select name="marketing_ad_id" id="rule_ad_{{ $row->id ?? 'new' }}" class="form-select select2-init" data-placeholder="ทุกแคมเปญ">
            <option value="">ทุกแคมเปญ</option>
            @foreach ($ads as $ad)
                <option value="{{ $ad->id }}" @selected((string) old('marketing_ad_id', $row->marketing_ad_id ?? '') === (string) $ad->id)>
                    {{ $ad->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="rule_province_{{ $row->id ?? 'new' }}">
            จังหวัด
        </label>
        <select name="province_id" id="rule_province_{{ $row->id ?? 'new' }}" class="form-select select2-init" data-placeholder="ทุกจังหวัด">
            <option value="">ทุกจังหวัด</option>
            @foreach ($provinces as $p)
                <option value="{{ $p->id }}" @selected((string) old('province_id', $row->province_id ?? '') === (string) $p->id)>
                    {{ $p->name_th }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 mt-3 pt-2 border-top">
        <h6 class="fs-13 text-muted text-uppercase fw-semibold mb-1">การมอบหมายปลายทาง</h6>
        <p class="text-muted fs-12 mb-2">กำหนดผู้รับผิดชอบหรือสาขาที่ต้องการส่งต่อ Lead ไปให้</p>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="rule_branch_{{ $row->id ?? 'new' }}">
            สาขาปลายทาง
        </label>
        <select name="assign_business_branch_id" id="rule_branch_{{ $row->id ?? 'new' }}" class="form-select select2-init" data-placeholder="ไม่บังคับสาขา">
            <option value="">ไม่บังคับสาขา</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}" @selected((string) old('assign_business_branch_id', $row->assign_business_branch_id ?? '') === (string) $b->id)>
                    {{ $b->business_branch_name_th }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="rule_employee_{{ $row->id ?? 'new' }}">
            ผู้รับผิดชอบ Lead
        </label>
        <select name="assign_employee_branch_id" id="rule_employee_{{ $row->id ?? 'new' }}" class="form-select select2-init" data-placeholder="คิวส่วนกลางของสาขา">
            <option value="">คิวส่วนกลางของสาขา</option>
            @foreach ($employees as $eb)
                <option value="{{ $eb->id }}" @selected((string) old('assign_employee_branch_id', $row->assign_employee_branch_id ?? '') === (string) $eb->id)>
                    {{ $eb->displayName() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 mt-2">
        <label class="form-label d-block">สถานะการใช้งาน</label>
        <div class="form-check form-switch form-switch-md">
            <input class="form-check-input"
                   type="checkbox"
                   role="switch"
                   name="is_active"
                   value="1"
                   id="rule_active_{{ $row->id ?? 'new' }}"
                   @checked(old('is_active', $row->is_active ?? true))>
            <label class="form-check-label ms-1" for="rule_active_{{ $row->id ?? 'new' }}">
                เปิดใช้งานกฎนี้
            </label>
        </div>
    </div>
</div>
