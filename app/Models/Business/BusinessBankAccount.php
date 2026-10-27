<?php

namespace App\Models\Business;

use App\Models\Bank\Bank;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BusinessBankAccount extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'business_id',
        'bank_id',
        'account_name',
        'account_no',
        'active_status',
        'default_status',
        'is_salary',
    ];

    /**
     * Get the business that owns the bank account.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Get the bank associated with the account.
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
