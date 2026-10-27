@php
    $lines = $lines ?? ($revision?->lines ?? collect());
    $variant = $variant ?? 'standard';
    $fmt = fn ($value) => number_format((float) $value, 2);
    $splitDescription = function (?string $text): array {
        $text = (string) $text;
        if (! str_contains($text, "\n")) {
            return ['name' => $text, 'detail' => null];
        }
        $parts = explode("\n", $text, 2);

        return ['name' => $parts[0], 'detail' => $parts[1] ?? null];
    };
@endphp
<div class="doc-table-wrapper table-responsive">
    <table class="invoice-table table table-borderless table-nowrap align-middle mb-0 document-lines-table">
        <thead class="align-middle">
            <tr class="text-center">
                @if ($variant === 'standard')
                    <th style="width:50px;">#</th>
                @endif
                <th class="text-start">สินค้าและบริการ</th>
                <th style="width:110px;">จำนวน</th>
                @if ($variant === 'standard')
                    <th style="width:110px;">หน่วย</th>
                @endif
                <th style="width:130px;" class="text-end">ราคา/หน่วย</th>
                @if ($variant === 'standard')
                    <th style="width:120px;" class="text-end">ส่วนลด</th>
                    <th class="doc-vat-col text-end" style="width:100px;">VAT (%)</th>
                @endif
                <th style="width:140px;" class="text-end">ราคารวม</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($lines as $idx => $line)
            @php($split = $splitDescription($line->description ?? ''))
            <tr>
                @if ($variant === 'standard')
                    <th scope="row" class="doc-line-no text-center">
                        <span class="badge bg-light text-secondary border px-2 py-1">{{ $idx + 1 }}</span>
                    </th>
                @endif
                <td class="text-start">
                    <div class="fw-semibold text-dark">{{ $split['name'] }}</div>
                    @if ($split['detail'])
                        <div class="doc-line-description-detail text-muted fs-12">{{ $split['detail'] }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $fmt($line->quantity ?? 0) }}</td>
                @if ($variant === 'standard')
                    <td class="text-center">{{ $line->unit ?: '-' }}</td>
                @endif
                <td class="text-end">{{ $fmt($line->unit_price ?? 0) }}</td>
                @if ($variant === 'standard')
                    <td class="text-end">{{ $fmt($line->discount_amount ?? 0) }}</td>
                    <td class="text-end doc-vat-col">{{ $fmt($line->vat_percent ?? 0) }}</td>
                @endif
                <td class="text-end fw-semibold text-dark">{{ $fmt($line->line_total ?? 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
