<?php

namespace App\Services;

class DocumentTotalsService
{
    public const VAT_TYPES = [
        'none' => 'ไม่มี VAT',
        'inclusive' => 'รวม VAT',
        'exclusive' => 'ไม่รวม VAT',
    ];

    public function vatTypeLabel(?string $type): string
    {
        return self::VAT_TYPES[$type ?? ''] ?? '-';
    }

    public function mapVatTypeForHelper(?string $vatType): string
    {
        return match ($vatType) {
            'none' => '0',
            'exclusive' => '1',
            'inclusive' => '2',
            default => '1',
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<string, float>
     */
    public function calculate(
        array $lines,
        string $vatType = 'exclusive',
        bool $whtStatus = false,
        ?float $whtPercent = null,
        string $discountType = 'percent',
    ): array {
        $productAmounts = [];
        $productPrices = [];
        $discountAmounts = [];
        $vatAmounts = [];

        foreach ($lines as $line) {
            $qty = (float) ($line['qty'] ?? 1);
            if ($qty <= 0) {
                $qty = 1;
            }
            $productAmounts[] = $qty;
            $productPrices[] = (float) ($line['unit_price'] ?? 0);
            if ($discountType === 'baht') {
                $discountAmounts[] = (float) ($line['discount_amount'] ?? $line['discount'] ?? 0);
            } else {
                $discountAmounts[] = (float) ($line['discount_percent'] ?? $line['discount'] ?? 0);
            }
            $vatAmounts[] = array_key_exists('vat_percent', $line) && $line['vat_percent'] !== null && $line['vat_percent'] !== ''
                ? (float) $line['vat_percent']
                : 7.0;
        }

        $whtPercentValue = $whtStatus && $whtPercent !== null ? $whtPercent : null;
        $helperDiscountType = $discountType === 'baht' ? '1' : '2';

        return CalculateLineItemMoneyTotals(
            $productAmounts,
            $productPrices,
            $discountAmounts,
            $vatAmounts,
            $this->mapVatTypeForHelper($vatType),
            $helperDiscountType,
            $whtPercentValue,
            null,
            100,
            0,
            'before_vat',
        );
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array{discount_amount: float, vat_amount: float, line_total: float}
     */
    public function calculateLine(array $line, string $vatType, string $discountType = 'percent'): array
    {
        $qty = (float) ($line['qty'] ?? 1);
        if ($qty <= 0) {
            $qty = 1;
        }
        $unitPrice = (float) ($line['unit_price'] ?? 0);
        $vatPercent = array_key_exists('vat_percent', $line) && $line['vat_percent'] !== null && $line['vat_percent'] !== ''
            ? (float) $line['vat_percent']
            : 7.0;

        $lineGross = RoundMoney($qty * $unitPrice);
        if ($discountType === 'baht') {
            $discountAmount = RoundMoney((float) ($line['discount_amount'] ?? $line['discount'] ?? 0));
        } else {
            $discountPercent = (float) ($line['discount_percent'] ?? $line['discount'] ?? 0);
            $discountAmount = RoundMoney($lineGross * ($discountPercent / 100));
        }
        $afterDiscount = RoundMoney($lineGross - $discountAmount);

        $helperVatType = $this->mapVatTypeForHelper($vatType);
        $vatAmount = 0.0;
        $lineTotal = $afterDiscount;

        if ($helperVatType === '0' || $vatPercent == 0) {
            $lineTotal = $afterDiscount;
        } elseif ($helperVatType === '1') {
            $vatAmount = RoundMoney($afterDiscount * ($vatPercent / 100));
            $lineTotal = RoundMoney($afterDiscount + $vatAmount);
        } else {
            $base = RoundMoney($afterDiscount * (100 / ($vatPercent + 100)));
            $vatAmount = RoundMoney($afterDiscount - $base);
            $lineTotal = $afterDiscount;
        }

        return [
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'line_total' => $lineTotal,
        ];
    }

    /**
     * @param  array<string, float>  $totals
     * @return array<string, mixed>
     */
    public function mapToRevisionTotals(array $totals): array
    {
        return [
            'subtotal' => $totals['before_discount'],
            'discount_amount' => $totals['discount'],
            'after_discount_amount' => $totals['after_discount'],
            'none_vat_amount' => $totals['none_vat'],
            'taxable_amount' => $totals['is_vat'],
            'vat_amount' => $totals['total_vat_price'],
            'wht_amount' => $totals['wht_price'],
            'total_amount' => $totals['total_price'],
        ];
    }
}
