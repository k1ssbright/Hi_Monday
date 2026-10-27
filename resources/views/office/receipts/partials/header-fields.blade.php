<div class="row g-3">
    <div class="col-md-3 col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">
            วันที่ออก <span class="text-danger">*</span>
        </label>
        <input type="date" name="issue_date" class="form-control bg-light border-0 rounded-3 py-2 fs-13 fw-semibold text-dark" value="{{ old('issue_date', $revision?->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4 col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">ช่องทางการชำระเงิน <span class="text-danger">*</span></label>
        <select name="payment_method" class="form-select bg-light border-0 rounded-3 py-2 fs-13 fw-semibold text-dark">
            <option value="bank_transfer" @selected(old('payment_method', $revision?->payment_method ?? 'bank_transfer') === 'bank_transfer')>โอนเงินผ่านธนาคาร (Bank Transfer)</option>
            <option value="cash" @selected(old('payment_method', $revision?->payment_method ?? '') === 'cash')>เงินสด (Cash)</option>
            <option value="cheque" @selected(old('payment_method', $revision?->payment_method ?? '') === 'cheque')>เช็คธนาคาร (Cheque)</option>
            <option value="credit_card" @selected(old('payment_method', $revision?->payment_method ?? '') === 'credit_card')>บัตรเครดิต (Credit Card)</option>
        </select>
    </div>
    <div class="col-md-5 col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">เลขที่อ้างอิงการโอน / เช็ค (Ref No.)</label>
        <input type="text" name="payment_ref" class="form-control bg-light border-0 rounded-3 py-2 fs-13 text-dark" value="{{ old('payment_ref', $revision?->payment_ref) }}" placeholder="เช่น KBANK-123456 / เลขที่เช็ค">
    </div>
    <div class="col-12">
        <label class="form-label fs-13 fw-semibold text-dark mb-1.5">หมายเหตุ</label>
        <input type="text" name="note" class="form-control bg-light border-0 rounded-3 py-2 fs-13 text-dark" value="{{ old('note', $revision?->note) }}" placeholder="กรอกหมายเหตุ หรือข้อความเพิ่มเติม...">
    </div>
</div>
