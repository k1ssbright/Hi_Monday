<?php

namespace App\Models\Sales;

use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityLine extends Model
{
    use SoftDeletesWithActor;

    protected $fillable = [
        'business_id',
        'opportunity_id',
        'product_service_item_id',
        'description',
        'quantity',
        'unit',
        'unit_price_estimate',
        'line_total_estimate',
        'create_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price_estimate' => 'decimal:2',
            'line_total_estimate' => 'decimal:2',
        ];
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_service_item_id');
    }
}

