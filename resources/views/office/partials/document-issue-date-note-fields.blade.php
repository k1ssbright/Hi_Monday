<div class="pt-1">
    <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
        <div class="avatar-xs rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:34px; height:34px;">
            <i class="ri-calendar-event-line fs-18"></i>
        </div>
        <h6 class="fw-bold text-dark fs-14 mb-0">เงื่อนไขวันที่และหมายเหตุเอกสาร</h6>
    </div>

    <div class="row g-3 align-items-center">
        <div class="col-md-3 col-12">
            <label class="form-label fs-13 fw-semibold text-dark mb-1.5">
                วันที่ออกเอกสาร <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted" style="height: 42px;"><i class="ri-calendar-line"></i></span>
                <input type="date" name="issue_date" class="form-control border-start-0 rounded-end-3 fs-13" style="height: 42px;" value="{{ old('issue_date', $revision?->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
            </div>
        </div>
        <div class="col-md-2 col-12">
            <label class="form-label fs-13 fw-semibold text-dark mb-1.5">สกุลเงิน</label>
            <input type="text" name="currency" class="form-control rounded-3 fs-13 fw-semibold" style="height: 42px;" value="{{ old('currency', $revision?->currency ?: 'THB') }}" placeholder="THB" maxlength="10">
        </div>
        <div class="col-md-7 col-12">
            <label class="form-label fs-13 fw-semibold text-dark mb-1.5">หมายเหตุเพิ่มเติม</label>
            <input type="text" name="note" class="form-control rounded-3 fs-13" style="height: 42px;" value="{{ old('note', $revision?->note) }}" placeholder="กรอกหมายเหตุ หรือข้อความเพิ่มเติมในเอกสาร...">
        </div>
    </div>
</div>
