<?php

namespace App\Models\Employee;

use App\Models\Business\BusinessBranch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeBranch extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'business_branch_id',
        'employee_position',
        'department',
        'is_salary',
    ];

    /**
     * Get the employee associated with the branch mapping.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the business branch associated with the employee mapping.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }

    public function displayName(): string
    {
        $user = $this->employee?->user;
        if ($user) {
            return trim($user->firstname.' '.$user->surname);
        }

        return $this->employee?->employee_no ?: substr((string) $this->id, 0, 8);
    }
}
