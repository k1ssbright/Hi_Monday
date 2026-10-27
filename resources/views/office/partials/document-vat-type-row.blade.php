@php
    $totalsDefaults = $totalsDefaults ?? [];
    $vatType = old('vat_type', $totalsDefaults['vat_type'] ?? 'exclusive');
@endphp
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div class="d-flex align-items-center gap-2">
        <label for="vat_type" class="form-label mb-0 fs-13 fw-semibold text-muted">
            <i class="ri-percent-line me-1 text-primary"></i>ประเภทภาษี (VAT):
        </label>
        <select name="vat_type" id="vat_type" class="form-select form-select-sm" style="width: auto; min-width: 160px;">
            <option value="none" @selected($vatType === 'none')>ไม่มี VAT (0%)</option>
            <option value="exclusive" @selected($vatType === 'exclusive')>ราคาไม่รวม VAT (7%)</option>
            <option value="inclusive" @selected($vatType === 'inclusive')>ราคารวม VAT แล้ว (7%)</option>
        </select>
    </div>
</div>
