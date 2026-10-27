<div class="row g-3 mb-0">
    <div class="col-sm-4">
        <label class="form-label">วันที่ขอ <span class="text-danger">*</span></label>
        <input type="date" name="request_date" class="form-control" value="{{ old('request_date', $revision?->request_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-sm-4">
        <label class="form-label">วันที่ต้องการ</label>
        <input type="date" name="required_date" class="form-control" value="{{ old('required_date', $revision?->required_date?->format('Y-m-d')) }}">
    </div>
    <div class="col-sm-4">
        <label class="form-label">หมายเหตุ</label>
        <input type="text" name="note" class="form-control" value="{{ old('note', $revision?->note) }}">
    </div>
</div>
