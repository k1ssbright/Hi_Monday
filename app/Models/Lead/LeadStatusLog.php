<?php

namespace App\Models\Lead;

use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lead_id', 'from_status', 'to_status', 'lead_closed_reason_id', 'note', 'changed_by', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(LeadClosedReason::class, 'lead_closed_reason_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'changed_by');
    }
}
