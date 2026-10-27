<?php

namespace App\Models\Sales;

use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationLine extends Model
{
    use SoftDeletesWithActor;

    protected $fillable = [
        'deleted_by',
        'quotation_revision_id',
        'opportunity_line_id',
        'product_service_item_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'vat_percent',
        'line_total',
        'cost_reference',
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
            'cost_reference' => 'decimal:2',
        ];
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(QuotationRevision::class, 'quotation_revision_id');
    }

    public function opportunityLine(): BelongsTo
    {
        return $this->belongsTo(OpportunityLine::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_service_item_id');
    }
}
