@include('office.partials.document-vat-type-row', [
    'totalsDefaults' => $totalsDefaults ?? [],
])
<div class="doc-table-wrapper table-responsive">
    <table class="invoice-table table table-borderless table-nowrap mb-0" id="doc-lines-table">
        <thead class="align-middle">
            <tr class="text-center">
                <th style="width:50px;">#</th>
                <th class="text-start">สินค้าและบริการ <span class="text-danger">*</span></th>
                <th style="width:110px;">จำนวน</th>
                <th style="width:110px;">หน่วย</th>
                <th style="width:130px;">ราคา/หน่วย</th>
                <th style="width:130px;">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <span>ส่วนลด</span>
                        <select name="discount_type" id="discount_type" class="form-select form-select-sm border-0 bg-white shadow-sm pe-0" style="width:auto; min-width:76px; font-weight:600;">
                            <option value="baht" @selected($discountType === 'baht')>(บาท)</option>
                            <option value="percent" @selected($discountType === 'percent')>(%)</option>
                        </select>
                    </div>
                </th>
                <th class="doc-vat-col" style="width:100px;">VAT (%)</th>
                <th style="width:140px;">ราคารวม</th>
                <th style="width:65px;"></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($seedLines as $idx => $line)
            <tr>
                <th scope="row" class="doc-line-no text-center">
                    <span class="badge bg-light text-secondary border px-2 py-1">{{ $idx + 1 }}</span>
                </th>
                <td class="text-start">
                    <input type="hidden" name="line[{{ $idx }}][product_service_item_id]" value="{{ $line['product_service_item_id'] ?? '' }}">
                    <div class="mb-2">
                        <input type="text" name="line[{{ $idx }}][description]" class="form-control"
                            value="{{ $line['description'] ?? '' }}" placeholder="ชื่อสินค้าหรือบริการ" required>
                    </div>
                    <textarea name="line[{{ $idx }}][detail]" class="form-control doc-line-detail" rows="2" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)">{{ $line['detail'] ?? '' }}</textarea>
                </td>
                <td><input type="number" min="0.01" step="0.01" name="line[{{ $idx }}][qty]" class="form-control text-center doc-line-input" value="{{ $line['qty'] ?? 1 }}" required></td>
                <td><input type="text" name="line[{{ $idx }}][unit]" class="form-control text-center" value="{{ $line['unit'] ?? 'หน่วย' }}"></td>
                <td><input type="number" min="0" step="0.01" name="line[{{ $idx }}][unit_price]" class="form-control text-end doc-line-input" value="{{ $line['unit_price'] ?? 0 }}" required></td>
                <td><input type="number" min="0" step="0.01" name="line[{{ $idx }}][discount]" class="form-control text-end doc-line-input doc-line-discount" value="{{ $line['discount'] ?? 0 }}"></td>
                <td class="doc-vat-col"><input type="number" min="0" max="100" step="0.01" name="line[{{ $idx }}][vat_percent]" class="form-control text-end doc-line-input" value="{{ $line['vat_percent'] ?? 7 }}"></td>
                <td><input type="text" class="form-control bg-light border-0 text-end doc-line-total fw-semibold" readonly value="0.00"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-soft-danger rounded-pill px-2 doc-remove-line" title="ลบรายการ">
                        <i class="ri-delete-bin-5-line align-middle"></i>
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mt-4 pt-1">
    <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-semibold" id="doc-add-line">
        <i class="ri-add-line fs-14 align-middle me-1"></i> เพิ่มรายการ
    </button>
    @include('office.partials.document-totals-summary')
</div>
