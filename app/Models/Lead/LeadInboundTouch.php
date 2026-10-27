<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadInboundTouch extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lead_id', 'lead_channel_id', 'marketing_ad_id', 'marketing_ad_unit_id',
        'contacted_at', 'payload', 'create_by', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
            'created_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LeadChannel::class, 'lead_channel_id');
    }
}
