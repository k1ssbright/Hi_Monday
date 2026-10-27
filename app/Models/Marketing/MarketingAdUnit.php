<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingAdUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'marketing_ad_id',
        'business_id',
        'name',
        'platform',
        'objective',
        'external_id',
        'budget',
        'spend',
        'status',
        'start_date',
        'end_date',
        'meta',
        'notes',
        'is_active',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'spend' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'meta' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function marketingAd(): BelongsTo
    {
        return $this->belongsTo(MarketingAd::class, 'marketing_ad_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'business_id');
    }
}
