<?php

namespace App\Models\Lead;

use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lead_id', 'from_assigned_to', 'to_assigned_to',
        'from_business_branch_id', 'to_business_branch_id',
        'reason', 'note', 'lead_assignment_rule_id', 'changed_by', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function toAssignee(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'to_assigned_to');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'changed_by');
    }
}
