<?php

namespace App\Services;

use App\Models\Sales\BillingNotePaymentTerm;
use App\Models\Sales\BillingNoteRevision;
use App\Models\Sales\QuotationPaymentTerm;
use App\Models\Sales\QuotationRevision;
use App\Models\Sales\SalesOrderPaymentTerm;
use App\Models\Sales\SalesOrderRevision;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\Relation;

class PaymentTermsService
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public function normalize(array $rows, string $mode, float $documentTotal): array
    {
        $mode = $mode === 'baht' ? 'baht' : 'percent';
        $normalized = [];
        $installment = 1;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $condition = trim((string) ($row['condition'] ?? ''));
            $percent = (float) ($row['percent_amount'] ?? 0);
            $baht = (float) ($row['baht_amount'] ?? 0);
            $dueDays = (int) ($row['due_days'] ?? 0);

            if ($condition === '' && $percent <= 0 && $baht <= 0) {
                continue;
            }

            if ($mode === 'percent') {
                $baht = round($documentTotal * ($percent / 100), 2);
            } else {
                $percent = $documentTotal > 0
                    ? round(($baht / $documentTotal) * 100, 4)
                    : 0.0;
            }

            $normalized[] = [
                'installment_no' => (int) ($row['installment_no'] ?? $installment),
                'condition' => $condition !== '' ? $condition : 'งวดที่ '.$installment,
                'due_days' => max(0, $dueDays),
                'due_date' => $row['due_date'] ?? null,
                'percent_amount' => round($percent, 4),
                'baht_amount' => round($baht, 2),
            ];
            $installment++;
        }

        if ($normalized === []) {
            $normalized[] = [
                'installment_no' => 1,
                'condition' => 'ชำระเต็มจำนวน',
                'due_days' => 0,
                'due_date' => null,
                'percent_amount' => 100,
                'baht_amount' => round($documentTotal, 2),
            ];
        }

        $sumPercent = array_sum(array_column($normalized, 'percent_amount'));
        if ($mode === 'percent' && abs($sumPercent - 100) > 0.05) {
            throw ValidationException::withMessages([
                'payment' => 'ผลรวมงวดชำระต้องเท่ากับ 100% (ตอนนี้ '.round($sumPercent, 2).'%)',
            ]);
        }

        if ($mode === 'baht') {
            $sumBaht = array_sum(array_column($normalized, 'baht_amount'));
            if (abs($sumBaht - $documentTotal) > 0.05) {
                throw ValidationException::withMessages([
                    'payment' => 'ผลรวมยอดงวดชำระต้องเท่ากับยอดเอกสาร',
                ]);
            }
        }

        return $normalized;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function syncQuotationTerms(QuotationRevision $revision, array $rows, string $mode): void
    {
        $revision->payment_unit_mode = $mode === 'baht' ? 'baht' : 'percent';
        $revision->save();

        $this->softDeletePaymentTerms($revision->paymentTerms());
        foreach ($this->normalize($rows, $revision->payment_unit_mode, (float) $revision->total_amount) as $row) {
            QuotationPaymentTerm::create([
                'quotation_revision_id' => $revision->id,
                ...$row,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $rows
     */
    public function syncSalesOrderTerms(SalesOrderRevision $revision, ?array $rows = null, ?string $mode = null): void
    {
        if ($rows === null) {
            return;
        }

        $revision->payment_unit_mode = ($mode ?? $revision->payment_unit_mode) === 'baht' ? 'baht' : 'percent';
        $revision->save();

        $normalized = $this->normalize($rows, $revision->payment_unit_mode, (float) $revision->total_amount);
        $this->softDeletePaymentTerms($revision->paymentTerms());

        foreach ($normalized as $index => $row) {
            SalesOrderPaymentTerm::create([
                'sales_order_revision_id' => $revision->id,
                'quotation_payment_term_id' => $rows[$index]['quotation_payment_term_id'] ?? null,
                'installment_no' => $row['installment_no'],
                'condition' => $row['condition'],
                'due_days' => $row['due_days'],
                'due_date' => $row['due_date'],
                'percent_amount' => $row['percent_amount'],
                'baht_amount' => $row['baht_amount'],
            ]);
        }
    }

    public function copyQuotationToSalesOrder(QuotationRevision $from, SalesOrderRevision $to): void
    {
        $to->payment_unit_mode = $from->payment_unit_mode ?: 'percent';
        $to->save();

        $from->loadMissing('paymentTerms');
        $this->softDeletePaymentTerms($to->paymentTerms());

        if ($from->paymentTerms->isEmpty()) {
            $this->syncSalesOrderTerms($to, [[
                'installment_no' => 1,
                'condition' => 'ชำระเต็มจำนวน',
                'due_days' => 0,
                'percent_amount' => 100,
                'baht_amount' => (float) $to->total_amount,
            ]], $to->payment_unit_mode);

            return;
        }

        foreach ($from->paymentTerms as $term) {
            $percent = (float) $term->percent_amount;
            $baht = $to->payment_unit_mode === 'percent'
                ? round(((float) $to->total_amount) * ($percent / 100), 2)
                : (float) $term->baht_amount;

            SalesOrderPaymentTerm::create([
                'sales_order_revision_id' => $to->id,
                'quotation_payment_term_id' => $term->id,
                'installment_no' => $term->installment_no,
                'condition' => $term->condition,
                'due_days' => $term->due_days,
                'due_date' => $term->due_date,
                'percent_amount' => $percent,
                'baht_amount' => $baht,
            ]);
        }
    }

    public function copySalesOrderToBillingNote(SalesOrderRevision $from, BillingNoteRevision $to, ?SalesOrderPaymentTerm $selected = null): void
    {
        $to->payment_unit_mode = $from->payment_unit_mode ?: 'percent';

        if ($selected) {
            $to->installment_no = $selected->installment_no;
            $to->billing_percent = $selected->percent_amount;
            $to->sales_order_payment_term_id = $selected->id;
            $to->subtotal = round(((float) $from->subtotal) * ((float) $selected->percent_amount / 100), 2);
            $to->discount_amount = round(((float) $from->discount_amount) * ((float) $selected->percent_amount / 100), 2);
            $to->vat_amount = round(((float) $from->vat_amount) * ((float) $selected->percent_amount / 100), 2);
            $to->total_amount = (float) $selected->baht_amount;
            $to->save();

            $this->softDeletePaymentTerms($to->paymentTerms());
            BillingNotePaymentTerm::create([
                'billing_note_revision_id' => $to->id,
                'sales_order_payment_term_id' => $selected->id,
                'installment_no' => 1,
                'condition' => $selected->condition,
                'due_days' => $selected->due_days,
                'due_date' => $selected->due_date,
                'percent_amount' => 100,
                'baht_amount' => $selected->baht_amount,
            ]);

            return;
        }

        $to->save();
        $from->loadMissing('paymentTerms');
        $this->softDeletePaymentTerms($to->paymentTerms());

        foreach ($from->paymentTerms as $term) {
            BillingNotePaymentTerm::create([
                'billing_note_revision_id' => $to->id,
                'sales_order_payment_term_id' => $term->id,
                'installment_no' => $term->installment_no,
                'condition' => $term->condition,
                'due_days' => $term->due_days,
                'due_date' => $term->due_date,
                'percent_amount' => $term->percent_amount,
                'baht_amount' => $term->baht_amount,
            ]);
        }
    }

    /**
     * @return Collection<int, SalesOrderPaymentTerm>
     */
    public function termsForSalesOrderRevision(SalesOrderRevision $revision): Collection
    {
        return $revision->paymentTerms()->orderBy('installment_no')->get();
    }

    /**
     * Soft-delete related payment terms one-by-one so deleted_by / events are recorded.
     * Query builder delete() would hard-delete even on SoftDeletes models.
     */
    private function softDeletePaymentTerms(Relation $relation): void
    {
        $relation->get()->each->delete();
    }
}
