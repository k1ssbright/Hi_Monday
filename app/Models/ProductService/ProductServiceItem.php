<?php

namespace App\Models\ProductService;

use App\Models\Business\Business;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Marketing\MarketingAd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductServiceItem extends Model
{
    use CascadesSoftDeletes, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'marketingAds',
    ];

    protected $fillable = [
        'product_id',
        'legacy_product_id',
        'business_id',
        'product_service_category_id',
        'kind',
        'code',
        'name',
        'brand',
        'manufacturer',
        'model',
        'description',
        'unit',
        'default_price',
        'buy_price',
        'image',
        'track_inventory',
        'is_active',
        'status',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
            'buy_price' => 'decimal:2',
            'track_inventory' => 'boolean',
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

    public function marketingAds(): HasMany
    {
        return $this->hasMany(MarketingAd::class, 'product_service_item_id');
    }
}
