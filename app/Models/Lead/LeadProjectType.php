<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use App\Models\ProductService\ProductServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadProjectType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id', 'code', 'name', 'product_service_category_id', 'sort', 'is_active', 'create_by',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
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
