<?php

namespace App\Services;

use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderLine;
use App\Models\Purchasing\PurchaseOrderRevision;
use App\Models\Purchasing\PurchaseRequisition;
use App\Models\Purchasing\PurchaseRequisitionRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseFlowService
{
    public function __construct(
        private DocumentPartyService $partyInfo,
    ) {}

    public function allocatePurchaseRequisitionNo(PurchaseRequisition $purchaseRequisition): void
    {
        $this->allocateDocumentNo($purchaseRequisition, 'PR', 'purchase_requisition_no');
    }

    public function allocatePurchaseOrderNo(PurchaseOrder $purchaseOrder): void
    {
        $this->allocateDocumentNo($purchaseOrder, 'PO', 'purchase_order_no');
    }

    /**
     * @param  array{order_date?: string|null, vendor_reference_no?: string|null, status?: string, create_by?: string|null, confirmed_by?: string|null}  $options
     */
    public function createPurchaseOrderFromRequisition(
        PurchaseRequisition $purchaseRequisition,
        PurchaseRequisitionRevision $requisitionRevision,
        array $options = [],
    ): PurchaseOrder {
        $status = $options['status'] ?? 'draft';
        $createBy = $options['create_by'] ?? null;

        if (blank($purchaseRequisition->partner_id)) {
            $this->partyInfo->linkPartnerFromRevisionSnapshot($purchaseRequisition, $requisitionRevision, $createBy);
            $purchaseRequisition->refresh();
        }

        $purchaseOrder = new PurchaseOrder([
            'business_id' => $purchaseRequisition->business_id,
            'business_branch_id' => $purchaseRequisition->business_branch_id,
            'purchase_requisition_id' => $purchaseRequisition->id,
            'purchase_requisition_revision_id' => $requisitionRevision->id,
            'partner_id' => $purchaseRequisition->partner_id,
            'partner_branch_id' => $purchaseRequisition->partner_branch_id,
            'partner_contact_id' => $purchaseRequisition->partner_contact_id,
            'create_by' => $createBy,
        ]);
        $this->allocatePurchaseOrderNo($purchaseOrder);
        $purchaseOrder->save();

        $revision = new PurchaseOrderRevision(array_merge([
            'purchase_order_id' => $purchaseOrder->id,
            'revision_no' => 1,
            'status' => $status,
            'order_date' => $options['order_date'] ?? now()->toDateString(),
            'vendor_reference_no' => $options['vendor_reference_no'] ?? null,
            'currency' => $requisitionRevision->currency,
            'subtotal' => $requisitionRevision->subtotal,
            'discount_amount' => $requisitionRevision->discount_amount,
            'vat_amount' => $requisitionRevision->vat_amount,
            'total_amount' => $requisitionRevision->total_amount,
            'create_by' => $createBy,
        ], $this->partySnapshotFromRevision($requisitionRevision)));

        if ($status === 'confirmed') {
            $revision->confirmed_at = now();
            $revision->confirmed_by = $options['confirmed_by'] ?? $createBy;
        }

        $revision->save();

        $purchaseOrder->current_revision_id = $revision->id;
        $purchaseOrder->save();

        $requisitionRevision->loadMissing('lines');
        foreach ($requisitionRevision->lines as $line) {
            PurchaseOrderLine::create([
                'purchase_order_revision_id' => $revision->id,
                'purchase_requisition_line_id' => $line->id,
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

        return $purchaseOrder->fresh(['currentRevision']);
    }

    /**
     * @return array<string, mixed>
     */
    public function partySnapshotFromRevision(object $revision): array
    {
        return collect([
            'customer_contact_name',
            'customer_contact_phone',
            'customer_email',
            'customer_type',
            'customer_tax_no',
            'customer_name',
            'customer_status',
            'customer_branch_no',
            'customer_branch_name',
            'customer_address',
            'customer_province_id',
            'customer_amphure_id',
            'customer_tambon_id',
            'customer_zip_code',
            'vat_type',
            'none_vat_amount',
            'taxable_amount',
            'after_discount_amount',
            'seller_name',
            'seller_phone',
            'seller_email',
        ])->mapWithKeys(fn (string $key) => [$key => $revision->{$key} ?? null])->all();
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
