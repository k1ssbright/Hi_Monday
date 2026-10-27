<?php

namespace App\Models\ProductService;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductServiceQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'product_service_category_id',
        'purpose',
        'label',
        'input_type',
        'options',
        'is_required',
        'sort_order',
        'is_active',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductServiceCategory::class, 'product_service_category_id');
    }
}
