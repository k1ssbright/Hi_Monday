<div class="row g-3 mb-0">
    <div class="col-sm-4">
        <label class="form-label">วันที่สั่ง <span class="text-danger">*</span></label>
        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $revision?->order_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-sm-4">
        <label class="form-label">เลขอ้างอิงผู้ขาย</label>
        <input type="text" name="vendor_reference_no" class="form-control" value="{{ old('vendor_reference_no', $revision?->vendor_reference_no) }}">
    </div>
</div>
