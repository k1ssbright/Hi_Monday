<?php

namespace App\Models\Bank;

use App\Models\Business\BusinessBankAccount;
use App\Models\Concerns\CascadesSoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'userBankAccounts',
        'businessBankAccounts',
    ];

    protected $fillable = [
        'name_th',
        'name_en',
        'logo',
    ];

    public function userBankAccounts(): HasMany
    {
        return $this->hasMany(UserBankAccount::class, 'bank_id');
    }

    public function businessBankAccounts(): HasMany
    {
        return $this->hasMany(BusinessBankAccount::class, 'bank_id');
    }
}
