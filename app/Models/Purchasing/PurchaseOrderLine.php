<?php

namespace App\Models\Purchasing;

use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderLine extends Model
{
    use SoftDeletesWithActor;

    protected $fillable = [
        'deleted_by',
        'purchase_order_revision_id',
        'purchase_requisition_line_id',
        'product_service_item_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_amount',
        'vat_percent',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'vat_percent' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderRevision::class, 'purchase_order_revision_id');
    }

    public function purchaseRequisitionLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionLine::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_service_item_id');
    }
}
