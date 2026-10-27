<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingAdLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'marketing_ad_id',
        'user_id',
        'action',
        'changes',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function marketingAd(): BelongsTo
    {
        return $this->belongsTo(MarketingAd::class, 'marketing_ad_id');
    }
}
