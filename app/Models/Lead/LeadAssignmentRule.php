<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use App\Models\Business\BusinessBranch;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadAssignmentRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id', 'priority', 'name', 'is_active', 'lead_channel_id', 'marketing_ad_id',
        'province_id', 'assign_business_branch_id', 'assign_employee_branch_id', 'create_by',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LeadChannel::class, 'lead_channel_id');
    }

    public function assignBranch(): BelongsTo
    {
        return $this->belongsTo(BusinessBranch::class, 'assign_business_branch_id');
    }

    public function assignEmployee(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'assign_employee_branch_id');
    }
}
