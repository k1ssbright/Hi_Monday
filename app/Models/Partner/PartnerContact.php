<?php

namespace App\Models\Partner;

use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerContact extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    public const PREFIXES = ['นาย', 'นาง', 'นางสาว', 'Mr.', 'Mrs.', 'Ms.', 'อื่นๆ'];

    protected $fillable = [
        'partner_id',
        'user_id',
        'email',
        'prefix',
        'other_prefix',
        'firstname',
        'surname',
        'phone',
        'line_id',
        'address',
        'province_id',
        'amphure_id',
        'tambon_id',
        'profile_image',
        'active_status',
        'create_by',
    ];

    protected array $cascadeSoftDeletes = [
        'contactBranches',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contactBranches(): HasMany
    {
        return $this->hasMany(PartnerContactBranch::class, 'partner_contact_id');
    }

    public function displayName(): string
    {
        $prefix = $this->prefix === 'อื่นๆ'
            ? ($this->other_prefix ?: '')
            : ($this->prefix ?: '');

        return trim(collect([$prefix, $this->firstname, $this->surname])->filter()->implode(' '));
    }
}
