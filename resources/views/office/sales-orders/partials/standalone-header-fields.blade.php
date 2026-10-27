<div class="row g-3 mb-2">
    <div class="col-md-4 col-sm-6">
        <label class="form-label fs-13 fw-semibold">วันที่สั่งขาย (Order Date) <span class="text-danger">*</span></label>
        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $revision?->order_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4 col-sm-6">
        <label class="form-label fs-13 fw-semibold">เลขที่ PO ลูกค้า (Customer PO No.)</label>
        <input type="text" name="customer_po_no" class="form-control" value="{{ old('customer_po_no', $revision?->customer_po_no) }}" placeholder="เช่น PO-20260901">
        <div class="form-text">ระบุเลขที่ใบสั่งซื้อของลูกค้า (ถ้ามี)</div>
    </div>
</div>
