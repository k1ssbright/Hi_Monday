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

$(document).ready(function() {
    if (window.$ && $.fn.select2) {
        $('.select2-form-select').select2({
            width: '100%',
            minimumResultsForSearch: 10
        });

        $('#customer_type, #customer_status').on('select2:select', function() {
            this.dispatchEvent(new Event('change'));
        });
    }
});
</script>
@include('partials.thailand-address-autocomplete-script')
