<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingArtwork extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'marketing_ad_id',
        'business_id',
        'file_name',
        'original_name',
        'type',
        'create_by',
    ];

    public function marketingAd(): BelongsTo
    {
        return $this->belongsTo(MarketingAd::class, 'marketing_ad_id');
    }
}
