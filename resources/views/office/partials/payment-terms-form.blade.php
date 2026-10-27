@php
    $paymentMode = old('payment_unit_mode', $paymentMode ?? 'percent');
    $paymentRows = old('payment', $paymentRows ?? [
        ['condition' => 'ชำระเต็มจำนวน', 'due_days' => 0, 'percent_amount' => 100, 'baht_amount' => 0],
    ]);
    $embedded = $embedded ?? false;
@endphp
<div @class(['card mb-3' => !$embedded, 'mb-0' => $embedded]) id="payment-terms-card">
    <div @class(['card-body' => !$embedded, 'p-0' => $embedded])>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <span class="text-muted fs-12">ไม่บังคับ — หากไม่ระบุ ระบบจะตั้งค่าเป็น "ชำระเต็มจำนวน 100%" ให้โดยอัตโนมัติ</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fs-13 fw-semibold text-muted">โหมด:</label>
                    <select name="payment_unit_mode" id="payment_unit_mode" class="form-select form-select-sm" style="width: auto; min-width: 90px; font-weight: 600;">
                        <option value="percent" @selected($paymentMode === 'percent')>% (ร้อยละ)</option>
                        <option value="baht" @selected($paymentMode === 'baht')>บาท</option>
                    </select>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" id="payment-add-row">
                    <i class="ri-add-line fs-14 align-middle me-1"></i> เพิ่มงวด
                </button>
            </div>
        </div>
        <div class="doc-table-wrapper table-responsive">
            <table class="table align-middle mb-0" id="payment-terms-table">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px;" class="text-center">งวดที่</th>
                        <th>เงื่อนไขการชำระเงิน</th>
                        <th style="width:140px;" class="text-center">ครบกำหนด (วัน)</th>
                        <th style="width:140px;" class="payment-col-percent text-end">%</th>
                        <th style="width:160px;" class="payment-col-baht text-end">จำนวนเงิน (บาท)</th>
                        <th style="width:65px;" class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($paymentRows as $idx => $row)
                    <tr>
                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm text-center fw-semibold" name="payment[{{ $idx }}][installment_no]" value="{{ $row['installment_no'] ?? ($idx + 1) }}" min="1">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="payment[{{ $idx }}][condition]" value="{{ $row['condition'] ?? '' }}" placeholder="เช่น 30% มัดจำเมื่อเริ่มงาน">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm text-center" name="payment[{{ $idx }}][due_days]" value="{{ $row['due_days'] ?? 0 }}" min="0">
                        </td>
                        <td class="payment-col-percent">
                            <input type="number" class="form-control form-control-sm text-end payment-percent" name="payment[{{ $idx }}][percent_amount]" value="{{ $row['percent_amount'] ?? 0 }}" min="0" max="100" step="0.0001">
                        </td>
                        <td class="payment-col-baht">
                            <input type="number" class="form-control form-control-sm text-end payment-baht" name="payment[{{ $idx }}][baht_amount]" value="{{ $row['baht_amount'] ?? 0 }}" min="0" step="0.01">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-soft-danger rounded-pill px-2 payment-remove-row" title="ลบงวด"><i class="ri-delete-bin-5-line align-middle"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-muted fs-12 mb-0 mt-2">
            <i class="ri-information-line me-1 text-info"></i>
            คำแนะนำ: หากเลือกโหมด % ผลรวมของทุกงวดควรเท่ากับ 100% หรือหากเลือกโหมดบาท ผลรวมควรเท่ากับยอดเงินสุทธิ
        </p>
    </div>
</div>
<script>
(function () {
    const table = document.getElementById('payment-terms-table');
    if (!table) return;
    const tbody = table.querySelector('tbody');
    const modeSelect = document.getElementById('payment_unit_mode');

    function reindex() {
        [...tbody.querySelectorAll('tr')].forEach((tr, idx) => {
            tr.querySelectorAll('input').forEach((input) => {
                input.name = input.name.replace(/payment\[\d+]/, 'payment[' + idx + ']');
            });
            const no = tr.querySelector('input[name*="[installment_no]"]');
            if (no && !no.value) no.value = idx + 1;
        });
    }

    function toggleMode() {
        const isPercent = modeSelect.value === 'percent';
        document.querySelectorAll('.payment-col-percent').forEach((el) => el.style.display = isPercent ? '' : 'none');
        document.querySelectorAll('.payment-col-baht').forEach((el) => el.style.display = isPercent ? 'none' : '');
    }

    document.getElementById('payment-add-row')?.addEventListener('click', () => {
        const idx = tbody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center"><input type="number" class="form-control form-control-sm text-center fw-semibold" name="payment[${idx}][installment_no]" value="${idx + 1}" min="1"></td>
            <td><input type="text" class="form-control form-control-sm" name="payment[${idx}][condition]" placeholder="เช่น ชำระส่วนที่เหลือเมื่อส่งมอบงาน"></td>
            <td><input type="number" class="form-control form-control-sm text-center" name="payment[${idx}][due_days]" value="0" min="0"></td>
            <td class="payment-col-percent"><input type="number" class="form-control form-control-sm text-end payment-percent" name="payment[${idx}][percent_amount]" value="0" min="0" max="100" step="0.0001"></td>
            <td class="payment-col-baht"><input type="number" class="form-control form-control-sm text-end payment-baht" name="payment[${idx}][baht_amount]" value="0" min="0" step="0.01"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-soft-danger rounded-pill px-2 payment-remove-row" title="ลบงวด"><i class="ri-delete-bin-5-line align-middle"></i></button></td>
        `;
        tbody.appendChild(tr);
        toggleMode();
    });

    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.payment-remove-row');
        if (!btn) return;
        btn.closest('tr').remove();
        reindex();
    });

    modeSelect?.addEventListener('change', toggleMode);
    toggleMode();
})();
</script>
