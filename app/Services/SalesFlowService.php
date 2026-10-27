<?php

namespace App\Services;

use App\Models\Sales\BillingNote;
use App\Models\Sales\CreditNote;
use App\Models\Sales\DebitNote;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\Opportunity;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationRevision;
use App\Models\Sales\Receipt;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderLine;
use App\Models\Sales\SalesOrderRevision;
use App\Models\Sales\TaxInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesFlowService
{
    public function __construct(
        private PaymentTermsService $paymentTerms,
        private DocumentPartyService $partyInfo,
    ) {}

    public function allocateOpportunityNo(Opportunity $opportunity): void
    {
        $this->allocateDocumentNo($opportunity, 'OP', 'opportunity_no');
    }

    public function allocateQuotationNo(Quotation $quotation): void
    {
        $this->allocateDocumentNo($quotation, 'QT', 'quotation_no');
    }

    public function allocateSalesOrderNo(SalesOrder $salesOrder): void
    {
        $this->allocateDocumentNo($salesOrder, 'SO', 'sales_order_no');
    }

    /**
     * @param  array{order_date?: string|null, customer_po_no?: string|null, status?: string, create_by?: string|null, confirmed_by?: string|null}  $options
     */
    public function createSalesOrderFromQuotation(Quotation $quotation, QuotationRevision $quotationRevision, array $options = []): SalesOrder
    {
        $status = $options['status'] ?? 'draft';
        $createBy = $options['create_by'] ?? null;

        if (blank($quotation->partner_id)) {
            $this->partyInfo->linkPartnerFromRevisionSnapshot($quotation, $quotationRevision, $createBy);
            $quotation->refresh();
        }

        $salesOrder = new SalesOrder([
            'business_id' => $quotation->business_id,
            'business_branch_id' => $quotation->business_branch_id,
            'opportunity_id' => $quotation->opportunity_id,
            'quotation_id' => $quotation->id,
            'quotation_revision_id' => $quotationRevision->id,
            'partner_id' => $quotation->partner_id,
            'partner_branch_id' => $quotation->partner_branch_id,
            'partner_contact_id' => $quotation->partner_contact_id,
            'create_by' => $createBy,
        ]);
        $this->allocateSalesOrderNo($salesOrder);
        $salesOrder->save();

        $revision = new SalesOrderRevision([
            'sales_order_id' => $salesOrder->id,
            'revision_no' => 1,
            'status' => $status,
            'order_date' => $options['order_date'] ?? now()->toDateString(),
            'customer_po_no' => $options['customer_po_no'] ?? $quotationRevision->customer_po_no,
            'currency' => $quotationRevision->currency,
            'subtotal' => $quotationRevision->subtotal,
            'discount_amount' => $quotationRevision->discount_amount,
            'vat_amount' => $quotationRevision->vat_amount,
            'total_amount' => $quotationRevision->total_amount,
            'payment_unit_mode' => $quotationRevision->payment_unit_mode ?: 'percent',
            'create_by' => $createBy,
        ]);

        if ($status === 'confirmed') {
            $revision->confirmed_at = now();
            $revision->confirmed_by = $options['confirmed_by'] ?? $createBy;
        }

        $revision->save();

        $salesOrder->current_revision_id = $revision->id;
        $salesOrder->save();

        $quotationRevision->loadMissing('lines');
        foreach ($quotationRevision->lines as $line) {
            SalesOrderLine::create([
                'sales_order_revision_id' => $revision->id,
                'quotation_line_id' => $line->id,
                'product_service_item_id' => $line->product_service_item_id,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit' => $line->unit,
                'unit_price' => $line->unit_price,
                'discount_amount' => $line->discount_amount,
                'vat_percent' => $line->vat_percent,
                'line_total' => $line->line_total,
            ]);
        }

        $this->paymentTerms->copyQuotationToSalesOrder($quotationRevision, $revision);

        if ($status === 'confirmed') {
            $opportunity = $quotation->opportunity;
            if ($opportunity && $opportunity->stage !== 'closed_won') {
                $opportunity->stage = 'closed_won';
                $opportunity->probability = 100;
                $opportunity->won_at = now();
                $opportunity->estimated_value = $revision->total_amount;
                $opportunity->save();
            }
        }

        return $salesOrder->fresh(['currentRevision']);
    }

    public function allocateBillingNoteNo(BillingNote $billingNote): void
    {
        $this->allocateDocumentNo($billingNote, 'BN', 'billing_note_no');
    }

    public function allocateDeliveryNoteNo(DeliveryNote $deliveryNote): void
    {
        $this->allocateDocumentNo($deliveryNote, 'DLV', 'delivery_note_no');
    }

    public function allocateTaxInvoiceNo(TaxInvoice $taxInvoice): void
    {
        $this->allocateDocumentNo($taxInvoice, 'TI', 'tax_invoice_no');
    }

    public function allocateReceiptNo(Receipt $receipt): void
    {
        $this->allocateDocumentNo($receipt, 'RCT', 'receipt_no');
    }

    public function allocateCreditNoteNo(CreditNote $creditNote): void
    {
        $this->allocateDocumentNo($creditNote, 'CN', 'credit_note_no');
    }

    public function allocateDebitNoteNo(DebitNote $debitNote): void
    {
        $this->allocateDocumentNo($debitNote, 'DBN', 'debit_note_no');
    }

    public function allocateDocumentNo(Model $doc, string $prefix, string $column): void
    {
        if (filled($doc->{$column})) {
            return;
        }

        $year = now()->year;
        $fullPrefix = $prefix.'-'.$year.'-';
        $query = $doc->newQuery();

        if (in_array(SoftDeletes::class, class_uses_recursive($doc), true)) {
            $query->withTrashed();
        }

        $last = $query
            ->where('business_id', $doc->business_id)
            ->where($column, 'like', $fullPrefix.'%')
            ->orderByDesc($column)
            ->value($column);

        $seq = 1;
        if (is_string($last) && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        $doc->{$column} = $fullPrefix.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
