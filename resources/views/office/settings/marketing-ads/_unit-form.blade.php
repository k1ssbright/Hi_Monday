<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ชื่อหน่วยโฆษณา</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $unit->name ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">แพลตฟอร์ม</label>
        <select name="platform" class="form-select">
            <option value="">—</option>
            @foreach ($platforms as $p)
                <option value="{{ $p->name }}" @selected(old('platform', $unit->platform ?? '') === $p->name)>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">วัตถุประสงค์</label>
        <select name="objective" class="form-select">
            <option value="">—</option>
            @foreach ($objectives as $o)
                <option value="{{ $o->name }}" @selected(old('objective', $unit->objective ?? '') === $o->name)>{{ $o->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">External ID</label>
        <input type="text" name="external_id" class="form-control" value="{{ old('external_id', $unit->external_id ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">งบ</label>
        <input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $unit->budget ?? 0) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">ใช้ไป</label>
        <input type="number" step="0.01" name="spend" class="form-control" value="{{ old('spend', $unit->spend ?? 0) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">สถานะ</label>
        <select name="status" class="form-select">
            @foreach (['draft','active','paused','ended'] as $st)
                <option value="{{ $st }}" @selected(old('status', $unit->status ?? 'draft') === $st)>{{ $st }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">เริ่ม</label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', isset($unit) ? $unit->start_date?->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">สิ้นสุด</label>
        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', isset($unit) ? $unit->end_date?->format('Y-m-d') : '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $unit->notes ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Meta (JSON เฉพาะแพลตฟอร์ม)</label>
        <textarea name="meta_json" class="form-control" rows="3" placeholder='{"placement":"feed"}'>{{ old('meta_json', isset($unit) && $unit->meta ? json_encode($unit->meta, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : '') }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="unit_active_{{ $unit->id ?? 'new' }}" @checked(old('is_active', $unit->is_active ?? true))>
            <label class="form-check-label" for="unit_active_{{ $unit->id ?? 'new' }}">ใช้งาน</label>
        </div>
    </div>
</div>
