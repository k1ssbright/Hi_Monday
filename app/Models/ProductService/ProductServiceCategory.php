<?php

namespace App\Models\ProductService;

use App\Models\Business\Business;
use App\Models\Concerns\CascadesSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductServiceCategory extends Model
{
    use CascadesSoftDeletes, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'items',
        'questions',
    ];

    protected $fillable = [
        'business_id',
        'kind',
        'code',
        'name',
        'description',
        'is_active',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductServiceItem::class, 'product_service_category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProductServiceQuestion::class, 'product_service_category_id');
    }
}
