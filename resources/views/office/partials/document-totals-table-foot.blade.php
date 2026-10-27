@php
    $totalsDefaults = $totalsDefaults ?? [];
    $whtPercent = old('wht_percent', $totalsDefaults['wht_percent'] ?? '');
    $whtActive = filled($whtPercent);
@endphp
<tr class="border-top border-top-dashed">
    <th colspan="4">
        <button type="button" class="btn btn-sm btn-soft-secondary fw-medium" id="qt-add-line-foot">
            <i class="ri-add-fill me-1 align-bottom"></i> เพิ่มรายการ
        </button>
    </th>
    <th class="qt-form-vat-row"></th>
    <th colspan="2"></th>
</tr>
<tr>
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2">รวมเป็นเงิน</th>
    <td>
        <input type="text" class="form-control form-control-sm text-end bg-light border-0" readonly
            id="totals-before-discount" value="0.00">
    </td>
</tr>
<tr>
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2">ส่วนลดรวม</th>
    <td>
        <input type="text" class="form-control form-control-sm text-end bg-light border-0" readonly
            id="totals-discount" value="0.00">
    </td>
</tr>
<tr>
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2 border-bottom border-dark">ราคาหลังหักส่วนลด</th>
    <td class="border-bottom border-dark">
        <input type="text" class="form-control form-control-sm text-end bg-light border-0" readonly
            id="totals-after-discount" value="0.00">
    </td>
</tr>
<tr class="qt-form-vat-row">
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2">มูลค่าที่ไม่มี/ยกเว้นภาษี</th>
    <td>
        <input type="text" class="form-control form-control-sm text-end bg-light border-0" readonly
            id="totals-none-vat" value="0.00">
    </td>
</tr>
<tr class="qt-form-vat-row">
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2">มูลค่าที่คำนวณภาษี</th>
    <td>
        <input type="text" class="form-control form-control-sm text-end bg-light border-0" readonly
            id="totals-taxable" value="0.00">
    </td>
</tr>
<tr class="qt-form-vat-row">
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2 border-bottom border-dark">ภาษีมูลค่าเพิ่ม</th>
    <td class="border-bottom border-dark">
        <input type="text" class="form-control form-control-sm text-end bg-light border-0" readonly
            id="totals-vat" value="0.00">
    </td>
</tr>
<tr>
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end text-muted fs-13 py-2 border-bottom border-dark">หัก ณ ที่จ่าย</th>
    <td class="border-bottom border-dark">
        <div class="input-group input-group-sm">
            <select name="wht_percent" id="wht_percent" class="form-select">
                <option value="" @selected(!$whtActive)>ไม่มี</option>
                <option value="0.5" @selected((string) $whtPercent === '0.5')>0.5%</option>
                <option value="0.75" @selected((string) $whtPercent === '0.75')>0.75%</option>
                <option value="1" @selected((string) $whtPercent === '1')>1%</option>
                <option value="2" @selected((string) $whtPercent === '2')>2%</option>
                <option value="3" @selected((string) $whtPercent === '3')>3%</option>
                <option value="5" @selected((string) $whtPercent === '5')>5%</option>
                <option value="10" @selected((string) $whtPercent === '10')>10%</option>
                <option value="15" @selected((string) $whtPercent === '15')>15%</option>
            </select>
            <input type="text" class="form-control text-end bg-light border-0" readonly
                id="totals-wht" value="0.00">
        </div>
        <input type="hidden" name="wht_status" id="wht_status" value="{{ $whtActive ? '1' : '0' }}">
    </td>
</tr>
<tr>
    <th colspan="4"></th>
    <th class="qt-form-vat-row"></th>
    <th class="text-end fw-semibold fs-13 py-2" style="border-bottom: 3px double !important">จำนวนเงินรวมทั้งสิ้น</th>
    <td style="border-bottom: 3px double !important">
        <input type="text" class="form-control form-control-sm text-end bg-light border-0 fw-bold" readonly
            id="totals-grand" value="0.00">
    </td>
</tr>
