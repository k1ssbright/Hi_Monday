@php
    $revision = $revision ?? null;
    $fmt = fn ($value) => number_format((float) $value, 2);
    $fullTotals = $revision && array_key_exists('none_vat_amount', $revision->getAttributes());
    $showVatBreakdown = $fullTotals && ($revision->vat_type ?? 'exclusive') !== 'none';
    $afterDiscount = $fullTotals
        ? (float) $revision->after_discount_amount
        : (float) ($revision?->subtotal ?? 0) - (float) ($revision?->discount_amount ?? 0);
@endphp
@if ($revision)
<div class="doc-totals-summary ms-auto">
    @if ($fullTotals && method_exists($revision, 'vatTypeLabel'))
        <div class="doc-totals-row">
            <div class="doc-totals-label">ประเภท VAT</div>
            <div class="doc-totals-value">
                <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $revision->vatTypeLabel() }}">
            </div>
        </div>
    @endif
    <div class="doc-totals-row">
        <div class="doc-totals-label">รวมเป็นเงิน</div>
        <div class="doc-totals-value">
            <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($revision->subtotal) }}">
        </div>
    </div>
    <div class="doc-totals-row">
        <div class="doc-totals-label">ส่วนลดรวม</div>
        <div class="doc-totals-value">
            <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($revision->discount_amount) }}">
        </div>
    </div>
    <div class="doc-totals-row border-bottom pb-2 mb-2">
        <div class="doc-totals-label">ราคาหลังหักส่วนลด</div>
        <div class="doc-totals-value">
            <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($afterDiscount) }}">
        </div>
    </div>
    @if ($showVatBreakdown)
        <div class="doc-totals-row doc-form-vat">
            <div class="doc-totals-label">มูลค่าที่ไม่มี/ยกเว้นภาษี</div>
            <div class="doc-totals-value">
                <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($revision->none_vat_amount) }}">
            </div>
        </div>
        <div class="doc-totals-row doc-form-vat">
            <div class="doc-totals-label">มูลค่าที่คำนวณภาษี</div>
            <div class="doc-totals-value">
                <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($revision->taxable_amount) }}">
            </div>
        </div>
    @endif
    <div class="doc-totals-row @if($showVatBreakdown) doc-form-vat border-bottom pb-2 mb-2 @endif">
        <div class="doc-totals-label">ภาษีมูลค่าเพิ่ม</div>
        <div class="doc-totals-value">
            <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($revision->vat_amount) }}">
        </div>
    </div>
    <div class="doc-totals-row doc-totals-grand">
        <div class="doc-totals-label">จำนวนเงินรวมทั้งสิ้น</div>
        <div class="doc-totals-value">
            <input type="text" class="form-control text-end doc-totals-input" readonly value="{{ $fmt($revision->total_amount) }}">
        </div>
    </div>
</div>
@endif
