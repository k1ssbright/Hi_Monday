<div class="row g-3">
    <div class="col-md-3 col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">
            วันที่ออก <span class="text-danger">*</span>
        </label>
        <input type="date" name="issue_date" class="form-control bg-light border-0 rounded-3 py-2 fs-13 fw-semibold text-dark" value="{{ old('issue_date', $revision?->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-2 col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">สกุลเงิน</label>
        <input type="text" name="currency" class="form-control rounded-3 py-2 fs-13 fw-semibold text-dark" value="{{ old('currency', $revision?->currency ?: 'THB') }}" placeholder="THB" maxlength="10">
    </div>
    <div class="col-md-7 col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">สาเหตุการเพิ่มหนี้</label>
        <input type="text" name="reason" class="form-control bg-light border-0 rounded-3 py-2 fs-13 text-dark" value="{{ old('reason', $revision?->reason) }}" placeholder="ระบุสาเหตุการเพิ่มหนี้ (ถ้ามี)...">
    </div>
    <div class="col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">หมายเหตุ</label>
        <input type="text" name="note" class="form-control bg-light border-0 rounded-3 py-2 fs-13 text-dark" value="{{ old('note', $revision?->note) }}" placeholder="กรอกหมายเหตุ หรือข้อความเพิ่มเติม...">
    </div>
</div>
