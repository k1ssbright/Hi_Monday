@php
    $terms = $terms ?? collect();
    $mode = $mode ?? 'percent';
@endphp
@if ($terms->isNotEmpty())
<div class="card">
    <div class="card-body">
        <h6 class="mb-3">เงื่อนไขการชำระเงิน
            <span class="badge bg-secondary-subtle text-secondary">{{ $mode === 'baht' ? 'บาท' : '%' }}</span>
        </h6>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>งวด</th>
                        <th>เงื่อนไข</th>
                        <th class="text-end">ครบกำหนด (วัน)</th>
                        <th class="text-end">%</th>
                        <th class="text-end">บาท</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($terms as $term)
                    <tr>
                        <td>{{ $term->installment_no }}</td>
                        <td>{{ $term->condition }}</td>
                        <td class="text-end">{{ $term->due_days }}</td>
                        <td class="text-end">{{ number_format((float) $term->percent_amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $term->baht_amount, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
