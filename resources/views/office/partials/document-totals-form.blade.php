@php
    $totalsDefaults = $totalsDefaults ?? [];
    $vatType = old('vat_type', $totalsDefaults['vat_type'] ?? 'exclusive');
    $whtStatus = (bool) old('wht_status', $totalsDefaults['wht_status'] ?? false);
    $whtPercent = old('wht_percent', $totalsDefaults['wht_percent'] ?? '');
    $sidebar = $sidebar ?? false;
@endphp
<div class="{{ $sidebar ? '' : 'card mb-3' }}" id="document-totals-card">
    <div class="{{ $sidebar ? '' : 'card-body' }}">
        @if ($sidebar)
            <h6 class="d-flex align-items-center gap-2 mb-3">
                <i class="ri-calculator-line text-primary"></i> สรุปยอดเงิน
            </h6>
        @else
            <h6 class="mb-3">สรุปยอดเงิน</h6>
        @endif

        <div class="row g-2 mb-3">
            <div class="col-12">
                <label class="form-label fs-13">ประเภท VAT</label>
                <select name="vat_type" id="vat_type" class="form-select form-select-sm">
                    <option value="none" @selected($vatType === 'none')>ไม่มี VAT</option>
                    <option value="inclusive" @selected($vatType === 'inclusive')>รวม VAT</option>
                    <option value="exclusive" @selected($vatType === 'exclusive')>ไม่รวม VAT</option>
                </select>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between rounded border px-3 py-2">
                    <label class="form-check-label fs-13 mb-0" for="wht_status">หัก ณ ที่จ่าย (WHT)</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="wht_status" id="wht_status" value="1"
                            @checked($whtStatus)>
                    </div>
                </div>
            </div>
            <div class="col-12" id="wht-percent-wrap" @if(!$whtStatus) style="display:none" @endif>
                <label class="form-label fs-13">WHT %</label>
                <input type="number" name="wht_percent" id="wht_percent" class="form-control form-control-sm" min="0" max="100" step="0.01"
                    value="{{ $whtPercent }}">
            </div>
        </div>

        <div class="bg-light rounded-3 p-3">
            <div class="d-flex justify-content-between fs-13 mb-1">
                <span class="text-muted">ก่อนหักส่วนลด</span>
                <span id="totals-before-discount">0.00</span>
            </div>
            <div class="d-flex justify-content-between fs-13 mb-1">
                <span class="text-muted">ส่วนลด</span>
                <span class="text-danger" id="totals-discount">0.00</span>
            </div>
            <div class="d-flex justify-content-between fs-13 mb-2 pb-2 border-bottom">
                <span class="text-muted">หลังหักส่วนลด</span>
                <span id="totals-after-discount">0.00</span>
            </div>
            <div class="d-flex justify-content-between fs-12 mb-1">
                <span class="text-muted">ไม่มี VAT</span>
                <span id="totals-none-vat">0.00</span>
            </div>
            <div class="d-flex justify-content-between fs-12 mb-1">
                <span class="text-muted">มี VAT (ก่อน VAT)</span>
                <span id="totals-taxable">0.00</span>
            </div>
            <div class="d-flex justify-content-between fs-12 mb-1">
                <span class="text-muted">VAT</span>
                <span id="totals-vat">0.00</span>
            </div>
            <div class="d-flex justify-content-between fs-12 mb-2 pb-2 border-bottom">
                <span class="text-muted">หัก ณ ที่จ่าย</span>
                <span class="text-danger" id="totals-wht">0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">ยอดสุทธิชำระ</span>
                <span class="fw-bold fs-18 text-success" id="totals-grand">0.00</span>
            </div>
        </div>
    </div>
</div>
