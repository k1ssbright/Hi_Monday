@extends('office.layout.office')
@php
    $opportunity = $opportunity ?? null;
    $quotation = $quotation ?? null;
    $isEdit = isset($quotation);
@endphp
@section('title', ($isEdit ? 'แก้ไข' : 'สร้าง').' Quotation | Hi Monday')

@section('style')
    @include('office.partials.document-form-styles')
@endsection

@section('content')
@php
    $partner = $partner ?? $opportunity?->partner;
    $seedLines = old('line');
    $formAction = $isEdit
        ? route('office.quotations.update', $quotation)
        : ($opportunity
            ? route('office.opportunities.quotations.store', $opportunity)
            : route('office.quotations.store.standalone'));
    $cancelUrl = $isEdit
        ? route('office.quotations.show', $quotation)
        : ($opportunity ? route('office.opportunities.show', $opportunity) : route('office.quotations.index'));
    $discountType = old('discount_type', $totalsDefaults['discount_type'] ?? 'percent');

    $splitLineText = function (?string $text): array {
        $text = (string) $text;
        if (! str_contains($text, "\n")) {
            return ['name' => $text, 'detail' => ''];
        }
        $parts = explode("\n", $text, 2);

        return ['name' => $parts[0], 'detail' => $parts[1] ?? ''];
    };

    if (! is_array($seedLines)) {
        $seedLines = $opportunity
            ? $opportunity->lines->map(function ($line) use ($splitLineText) {
                $split = $splitLineText($line->description);

                return [
                    'opportunity_line_id' => $line->id,
                    'product_service_item_id' => $line->product_service_item_id,
                    'description' => $split['name'],
                    'detail' => $split['detail'],
                    'qty' => $line->quantity,
                    'unit' => $line->unit ?: 'หน่วย',
                    'unit_price' => $line->unit_price_estimate,
                    'discount' => 0,
                    'vat_percent' => 7,
                ];
            })->values()->all()
            : [];
    } else {
        $seedLines = collect($seedLines)->map(function ($line) use ($splitLineText) {
            if (! isset($line['detail']) && isset($line['description'])) {
                $split = $splitLineText($line['description']);
                $line['description'] = $split['name'];
                $line['detail'] = $split['detail'];
            }
            $line['discount'] = $line['discount'] ?? $line['discount_percent'] ?? $line['discount_amount'] ?? 0;

            return $line;
        })->all();
    }

    if ($seedLines === []) {
        $seedLines = [[
            'opportunity_line_id' => null,
            'product_service_item_id' => null,
            'description' => '',
            'detail' => '',
            'qty' => 1,
            'unit' => 'หน่วย',
            'unit_price' => 0,
            'discount' => 0,
            'vat_percent' => 7,
        ]];
    }
@endphp
<div class="container-fluid">
    {{-- Breadcrumb & Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">{{ $isEdit ? 'แก้ไขใบเสนอราคา' : 'สร้างใบเสนอราคาใหม่' }}</span>
        <div class="fs-13 text-muted">
            <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <a href="{{ route('office.quotations.index') }}"
                class="text-muted text-decoration-none">ใบเสนอราคา (QT)</a> <i class="ri-arrow-right-s-line"></i> <span
                class="fw-semibold text-dark">{{ $isEdit ? 'แก้ไข '.$quotation->displayNo() : 'สร้างเอกสารใหม่' }}</span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">
            {{ $isEdit ? 'แก้ไขใบเสนอราคา '.$quotation->displayNo() : 'สร้างใบเสนอราคาใหม่ (Quotation)' }}
        </h4>
    </div>

    @include('office.settings.partials.flash')

    @if ($opportunity)
        <div class="alert bg-primary-subtle text-primary border-0 d-flex align-items-center gap-2 mb-3 py-2">
            <i class="ri-link fs-18"></i>
            <span>อ้างอิง Opportunity <strong>{{ $opportunity->opportunity_no }}</strong> · {{ $opportunity->name }}</span>
        </div>
    @elseif (! $isEdit)
        <div class="alert bg-info-subtle text-info border-0 d-flex align-items-center gap-2 mb-3 py-2">
            <i class="ri-edit-line fs-18"></i>
            <span>กรอกข้อมูลลูกค้าในฟอร์มด้านล่างได้เลย · ไม่ต้องเลือกลูกค้าในระบบหรือสร้าง Opportunity ก่อน</span>
        </div>
    @endif

    <form method="post" action="{{ $formAction }}" id="quotation-form" class="document-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif
        @if (! $opportunity && $partner?->id)
            <input type="hidden" name="partner_id" value="{{ $partner->id }}">
        @endif

        <div class="emp-card-main mb-4">
            {{-- ส่วนที่ 1: ข้อมูลเอกสารและวันที่ --}}
            <div class="section-title">
                <div class="section-icon"><i class="ri-file-text-line"></i></div>
                <span>ข้อมูลเอกสารและวันที่</span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label fs-13 fw-semibold">วันที่ออกเอกสาร <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', $issueDate) }}" required>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="form-label fs-13 fw-semibold">วันยืนราคา (Valid Until)</label>
                    <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $validUntil) }}">
                    <div class="form-text">ไม่บังคับ</div>
                </div>
            </div>

            {{-- ส่วนที่ 2: ข้อมูลคู่ค้าและผู้ออกเอกสาร --}}
            <div class="section-title mt-5">
                <div class="section-icon bg-success-subtle text-success"><i class="ri-building-4-line"></i></div>
                <span>ข้อมูลคู่ค้าและผู้ออกเอกสาร</span>
            </div>
            <div class="mb-4">
                @include('office.partials.document-party-form', [
                    'customerParty' => $customerParty ?? [],
                    'sellerParty' => $sellerParty ?? [],
                    'provinces' => $provinces ?? collect(),
                    'customerAmphures' => $customerAmphures ?? collect(),
                    'customerTambons' => $customerTambons ?? collect(),
                    'counterpartyTitle' => 'ข้อมูลลูกค้า',
                    'issuerTitle' => 'ข้อมูลผู้ขาย',
                ])
            </div>

            {{-- ส่วนที่ 3: รายการสินค้าและบริการ --}}
            <div class="section-title mt-5">
                <div class="section-icon bg-warning-subtle text-warning"><i class="ri-shopping-cart-2-line"></i></div>
                <span>รายการสินค้าและบริการ</span>
            </div>
            <div class="mb-4">
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
                                    <input type="hidden" name="line[{{ $idx }}][opportunity_line_id]" value="{{ $line['opportunity_line_id'] ?? '' }}">
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
            </div>

            {{-- ส่วนที่ 4: เงื่อนไขการชำระเงิน --}}
            <div class="section-title mt-5">
                <div class="section-icon bg-info-subtle text-info"><i class="ri-bank-card-line"></i></div>
                <span>เงื่อนไขการชำระเงิน (Payment Terms)</span>
            </div>
            <div class="mb-4">
                @include('office.partials.payment-terms-form', [
                    'embedded' => true,
                    'paymentRows' => $isEdit ? $revision->paymentTerms->map(fn ($term) => [
                        'condition' => $term->condition,
                        'due_days' => $term->due_days,
                        'percent_amount' => (float) $term->percent_amount,
                        'baht_amount' => (float) $term->baht_amount,
                    ])->all() : null,
                    'paymentMode' => $isEdit ? ($revision->payment_unit_mode ?? 'percent') : null,
                ])
            </div>

            {{-- Action Bar --}}
            @php
                $statusText = $isEdit ? 'เลขที่: <strong>' . e($quotation->displayNo()) . '</strong>' : 'เลขที่: ร่างยังไม่ออกเลข';
            @endphp
            @include('office.partials.document-form-actionbar', [
                'cancelUrl' => $cancelUrl,
                'submitLabel' => $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกใบเสนอราคา',
                'left' => $statusText,
            ])
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
(function () {
    const tbody = document.querySelector('#doc-lines-table tbody');
    if (!tbody) return;

    function roundMoney(n) {
        return Math.round((Number(n) + Number.EPSILON) * 100) / 100;
    }

    function fmt(n) {
        return n.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function setTotal(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = fmt(val);
    }

    function discountType() {
        return document.getElementById('discount_type')?.value || 'percent';
    }

    function setVatVisibility() {
        const showVat = (document.getElementById('vat_type')?.value || 'exclusive') !== 'none';
        document.querySelectorAll('.doc-form-vat').forEach((el) => {
            el.style.display = showVat ? '' : 'none';
        });
        document.querySelectorAll('.doc-vat-col').forEach((el) => {
            el.style.display = showVat ? '' : 'none';
        });
    }

    function reindexLineNumbers() {
        tbody.querySelectorAll('.doc-line-no').forEach((el, idx) => {
            el.innerHTML = `<span class="badge bg-light text-secondary border px-2 py-1">${idx + 1}</span>`;
        });
    }

    function reindex() {
        [...tbody.querySelectorAll('tr')].forEach((tr, idx) => {
            tr.querySelectorAll('input, textarea').forEach((input) => {
                input.name = input.name.replace(/line\[\d+]/, 'line[' + idx + ']');
            });
        });
        reindexLineNumbers();
        recalcTotals();
    }

    function mapVatType(type) {
        if (type === 'none') return '0';
        if (type === 'exclusive') return '1';
        return '2';
    }

    function lineDiscountAmount(lineGross, discountValue) {
        const value = parseFloat(discountValue || '0');
        if (discountType() === 'baht') {
            return roundMoney(value);
        }
        return roundMoney(lineGross * (value / 100));
    }

    function recalcTotals() {
        const vatType = document.getElementById('vat_type')?.value || 'exclusive';
        const vatTypeCode = mapVatType(vatType);

        let beforeDiscount = 0, discount = 0, noneVat = 0, isVat = 0, totalVat = 0;

        tbody.querySelectorAll('tr').forEach((tr) => {
            const qty = parseFloat(tr.querySelector('[name*="[qty]"]')?.value || '0');
            const price = parseFloat(tr.querySelector('[name*="[unit_price]"]')?.value || '0');
            const discountValue = tr.querySelector('[name*="[discount]"]')?.value || '0';
            const vatPct = parseFloat(tr.querySelector('[name*="[vat_percent]"]')?.value || '7');

            const lineGross = roundMoney(qty * price);
            const lineDiscount = lineDiscountAmount(lineGross, discountValue);
            const afterDiscount = roundMoney(lineGross - lineDiscount);

            const lineTotalInput = tr.querySelector('.doc-line-total');
            if (lineTotalInput) lineTotalInput.value = fmt(afterDiscount);

            beforeDiscount = roundMoney(beforeDiscount + lineGross);
            discount = roundMoney(discount + lineDiscount);

            if (vatTypeCode === '0' || vatPct === 0) {
                noneVat = roundMoney(noneVat + afterDiscount);
            } else if (vatTypeCode === '1') {
                isVat = roundMoney(isVat + afterDiscount);
                totalVat = roundMoney(totalVat + afterDiscount * (vatPct / 100));
            } else {
                const base = roundMoney(afterDiscount * (100 / (vatPct + 100)));
                const vatPart = roundMoney(afterDiscount - base);
                isVat = roundMoney(isVat + base);
                totalVat = roundMoney(totalVat + vatPart);
            }
        });

        const afterDiscountTotal = roundMoney(beforeDiscount - discount);
        const grandTotal = roundMoney(noneVat + isVat + totalVat);

        setTotal('totals-before-discount', beforeDiscount);
        setTotal('totals-discount', discount);
        setTotal('totals-after-discount', afterDiscountTotal);
        setTotal('totals-none-vat', noneVat);
        setTotal('totals-taxable', isVat);
        setTotal('totals-vat', totalVat);
        setTotal('totals-grand', grandTotal);
    }

    function addLineRow() {
        const idx = tbody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <th scope="row" class="doc-line-no text-center">
                <span class="badge bg-light text-secondary border px-2 py-1">${idx + 1}</span>
            </th>
            <td class="text-start">
                <input type="hidden" name="line[${idx}][opportunity_line_id]" value="">
                <input type="hidden" name="line[${idx}][product_service_item_id]" value="">
                <div class="mb-2">
                    <input type="text" name="line[${idx}][description]" class="form-control" placeholder="ชื่อสินค้าหรือบริการ" required>
                </div>
                <textarea name="line[${idx}][detail]" class="form-control doc-line-detail" rows="2" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
            </td>
            <td><input type="number" min="0.01" step="0.01" name="line[${idx}][qty]" class="form-control text-center doc-line-input" value="1" required></td>
            <td><input type="text" name="line[${idx}][unit]" class="form-control text-center" value="หน่วย"></td>
            <td><input type="number" min="0" step="0.01" name="line[${idx}][unit_price]" class="form-control text-end doc-line-input" value="0" required></td>
            <td><input type="number" min="0" step="0.01" name="line[${idx}][discount]" class="form-control text-end doc-line-input doc-line-discount" value="0"></td>
            <td class="doc-vat-col"><input type="number" min="0" max="100" step="0.01" name="line[${idx}][vat_percent]" class="form-control text-end doc-line-input" value="7"></td>
            <td><input type="text" class="form-control bg-light border-0 text-end doc-line-total fw-semibold" readonly value="0.00"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-soft-danger rounded-pill px-2 doc-remove-line" title="ลบรายการ">
                    <i class="ri-delete-bin-5-line align-middle"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        setVatVisibility();
        recalcTotals();
    }

    document.getElementById('doc-add-line')?.addEventListener('click', addLineRow);

    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.doc-remove-line');
        if (!btn) return;
        if (tbody.querySelectorAll('tr').length <= 1) return;
        btn.closest('tr').remove();
        reindex();
    });

    tbody.addEventListener('input', (e) => {
        if (e.target.classList.contains('doc-line-input') || e.target.closest('#doc-lines-table')) {
            recalcTotals();
        }
    });

    document.getElementById('vat_type')?.addEventListener('change', () => {
        setVatVisibility();
        recalcTotals();
    });
    document.getElementById('discount_type')?.addEventListener('change', recalcTotals);

    const customerType = document.getElementById('customer_type');
    const customerStatus = document.getElementById('customer_status');
    const branchSection = document.getElementById('customer-branch-section');
    const branchDetailFields = document.getElementById('customer-branch-detail-fields');

    function syncBranchFields() {
        const isPerson = customerType?.value === '3';
        const isHeadOffice = customerStatus?.value === '0';

        if (branchSection) {
            branchSection.style.display = isPerson ? 'none' : '';
        }

        if (branchDetailFields) {
            branchDetailFields.style.display = (!isPerson && !isHeadOffice) ? '' : 'none';
        }

        if (isPerson && branchSection) {
            branchSection.querySelectorAll('input').forEach((el) => { el.value = ''; });
            if (customerStatus) customerStatus.value = '0';
        }

        if (isHeadOffice && branchDetailFields) {
            branchDetailFields.querySelectorAll('input').forEach((el) => { el.value = ''; });
        }
    }

    customerType?.addEventListener('change', syncBranchFields);
    customerStatus?.addEventListener('change', syncBranchFields);
    syncBranchFields();
    setVatVisibility();
    reindexLineNumbers();
    recalcTotals();
})();
</script>
@include('partials.thailand-address-autocomplete-script')
@endsection
