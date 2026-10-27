<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadTierSetting extends Model
{
    public const DEFAULTS = [
        'factors' => [
            'quality_demand' => ['enabled' => true, 'weight' => 1],
            'quality_competition' => ['enabled' => true, 'weight' => 1],
            'quality_authority' => ['enabled' => true, 'weight' => 1],
            'quality_budget_fit' => ['enabled' => true, 'weight' => 1],
            'timeline' => ['enabled' => false, 'weight' => 1],
        ],
        'min_factors_required' => 1,
        't1_min' => 3.5,
        't2_min' => 2.5,
        't1_pct' => 75,
        't2_pct' => 50,
        't3_pct' => 25,
    ];

    protected $fillable = [
        'business_id', 'factors', 'min_factors_required', 't1_min', 't2_min', 't1_pct', 't2_pct', 't3_pct', 'create_by',
    ];

    protected function casts(): array
    {
        return [
            'factors' => 'array',
            't1_min' => 'float',
            't2_min' => 'float',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public static function forBusiness(string $businessId): array
    {
        $row = static::query()->where('business_id', $businessId)->first();
        if (! $row) {
            return self::DEFAULTS;
        }

        return [
            'factors' => $row->factors ?: self::DEFAULTS['factors'],
            'min_factors_required' => (int) $row->min_factors_required,
            't1_min' => (float) $row->t1_min,
            't2_min' => (float) $row->t2_min,
            't1_pct' => (int) $row->t1_pct,
            't2_pct' => (int) $row->t2_pct,
            't3_pct' => (int) $row->t3_pct,
        ];
    }
}
