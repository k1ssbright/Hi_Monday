<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadGoal extends Model
{
    protected $fillable = [
        'business_id', 'fiscal_year', 'revenue_target', 'avg_deal_value',
        'lead_to_opportunity_rate', 'opportunity_win_rate', 'create_by',
    ];

    protected function casts(): array
    {
        return [
            'revenue_target' => 'float',
            'avg_deal_value' => 'float',
            'lead_to_opportunity_rate' => 'float',
            'opportunity_win_rate' => 'float',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function requiredLeads(): ?int
    {
        $avg = (float) $this->avg_deal_value;
        $conv = ((float) $this->lead_to_opportunity_rate / 100) * ((float) $this->opportunity_win_rate / 100);
        if ($avg <= 0 || $conv <= 0) {
            return null;
        }

        return (int) ceil(((float) $this->revenue_target) / $avg / $conv);
    }
}
