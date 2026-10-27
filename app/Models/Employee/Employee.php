<?php

namespace App\Models\Employee;

use App\Models\Business\Business;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use CascadesSoftDeletes, HasFactory, HasUuids, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'employeeBranches',
    ];

    protected $fillable = [
        'businesses_id',
        'user_id',
        'reports_to',
        'employee_no',
        'status',
        'create_by_employee_branch',
        'create_by_user',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'positions' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'boolean',
        ];
    }

    /**
     * Get the user associated with the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the business where the employee works.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'businesses_id');
    }

    /**
     * Get the manager/supervisor of the employee.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reports_to');
    }

    public function employeeBranches(): HasMany
    {
        return $this->hasMany(EmployeeBranch::class, 'employee_id');
    }
}
