<?php

namespace App\Models\Marketing;

use App\Models\Business\Business;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingAd extends Model
{
    use CascadesSoftDeletes, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'units',
        'logs',
        'artworks',
    ];

    protected $fillable = [
        'business_id',
        'name',
        'product_service_item_id',
        'budget',
        'status',
        'start_date',
        'end_date',
        'notes',
        'is_active',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function productServiceItem(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_service_item_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(MarketingAdUnit::class, 'marketing_ad_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MarketingAdLog::class, 'marketing_ad_id');
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(MarketingArtwork::class, 'marketing_ad_id');
    }
}
