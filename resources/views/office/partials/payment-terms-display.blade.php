@php
    $terms = $terms ?? collect();
    $mode = $mode ?? 'percent';
    $isPercent = $mode === 'percent';
@endphp
<div class="mb-0" id="payment-terms-card">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h6 class="mb-0">เงื่อนไขการชำระเงิน (Payment Terms)</h6>
            <p class="text-muted fs-12 mb-0">หน่วย: {{ $isPercent ? '%' : 'บาท' }}</p>
        </div>
    </div>
    @if ($terms->isEmpty())
        <p class="text-muted fs-13 mb-0">ชำระเต็มจำนวน 100%</p>
    @else
        <div class="table-responsive-emp border rounded-3 overflow-hidden">
            <table class="table-emp-custom mb-0" id="payment-terms-table">
                <thead>
                    <tr>
                        <th style="width:70px;">งวด</th>
                        <th>เงื่อนไข</th>
                        <th style="width:120px;">ครบกำหนด (วัน)</th>
                        <th style="width:120px;" @if(!$isPercent) class="d-none" @endif>%</th>
                        <th style="width:140px;" @if($isPercent) class="d-none" @endif>บาท</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($terms as $term)
                    <tr>
                        <td>
                            <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $term->installment_no }}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $term->condition }}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm bg-light text-end" readonly value="{{ $term->due_days }}">
                        </td>
                        <td @if(!$isPercent) class="d-none" @endif>
                            <input type="text" class="form-control form-control-sm bg-light text-end" readonly value="{{ number_format((float) $term->percent_amount, 2) }}">
                        </td>
                        <td @if($isPercent) class="d-none" @endif>
                            <input type="text" class="form-control form-control-sm bg-light text-end" readonly value="{{ number_format((float) $term->baht_amount, 2) }}">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
