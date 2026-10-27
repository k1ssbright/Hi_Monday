<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Bank\UserBankAccount;
use App\Models\Business\Business;
use App\Models\Business\BusinessUser;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Employee\Employee;
use App\Models\Partner\PartnerContact;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use CascadesSoftDeletes, HasFactory, HasRoles, HasUuids, Notifiable, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'businessUsers',
        'userBankAccounts',
        'employees',
        'partnerContacts',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'prefix',
        'other_prefix',
        'firstname',
        'surname',
        'email',
        'password',
        'phone',
        'active_status',
        'signature',
        'birthday',
        'address',
        'province_id',
        'amphure_id',
        'tambon_id',
        'tax_no',
        'profile_image',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the employee profile associated with the user.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'user_id');
    }

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class, 'user_id');
    }

    public function userBankAccounts(): HasMany
    {
        return $this->hasMany(UserBankAccount::class, 'user_id');
    }

    public function partnerContacts(): HasMany
    {
        return $this->hasMany(PartnerContact::class, 'user_id');
    }

    /**
     * Businesses this user belongs to (via business_users).
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_users', 'user_id', 'business_id')
            ->withTimestamps();
    }
}
