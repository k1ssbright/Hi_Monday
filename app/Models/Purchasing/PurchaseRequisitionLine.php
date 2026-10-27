<?php

namespace App\Models\Purchasing;

use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionLine extends Model
{
    use SoftDeletesWithActor;

    protected $fillable = [
        'deleted_by',
        'purchase_requisition_revision_id',
        'product_service_item_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'vat_percent',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'vat_percent' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionRevision::class, 'purchase_requisition_revision_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_service_item_id');
    }
}
