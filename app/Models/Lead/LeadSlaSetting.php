<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadSlaSetting extends Model
{
    protected $fillable = [
        'business_id', 'first_touch_minutes', 'qualify_within_days', 'nurture_review_days', 'business_hours_only', 'create_by',
    ];

    protected function casts(): array
    {
        return ['business_hours_only' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public static function forBusiness(string $businessId): self
    {
        return static::query()->firstOrNew(
            ['business_id' => $businessId],
            [
                'first_touch_minutes' => 60,
                'qualify_within_days' => 7,
                'nurture_review_days' => 30,
                'business_hours_only' => true,
            ]
        );
    }
}
