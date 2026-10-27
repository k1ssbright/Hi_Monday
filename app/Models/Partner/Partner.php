<?php

namespace App\Models\Partner;

use App\Models\Business\Business;
use App\Models\Concerns\CascadesSoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    protected $fillable = [
        'business_id',
        'partner_type',
        'partner_tax_no',
        'partner_vat_status',
        'partner_code',
        'partner_name_th',
        'partner_en_status',
        'partner_name_en',
        'create_by',
    ];

    protected array $cascadeSoftDeletes = [
        'contacts',
        'channels',
        'branches',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PartnerContact::class, 'partner_id');
    }

    public function channels(): HasMany
    {
        return $this->hasMany(PartnerChannel::class, 'partner_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(PartnerBranch::class, 'partner_id');
    }

    public function entityTypeLabel(): string
    {
        return match ((int) $this->partner_type) {
            1 => 'บริษัท',
            2 => 'ห้างหุ้นส่วน',
            3 => 'บุคคลธรรมดา',
            default => '',
        };
    }

    public function hqBranch(): ?PartnerBranch
    {
        return $this->branches->firstWhere('partner_status', 0)
            ?? $this->branches->first();
    }

    public function role(): string
    {
        $branch = $this->hqBranch();

        if (! $branch) {
            return 'customer';
        }

        return PartnerBranch::roleFromFlags((bool) $branch->is_customer, (bool) $branch->is_vendor);
    }

    public function roleLabel(): string
    {
        return PartnerBranch::labelForRole($this->role());
    }

    public function isActive(): bool
    {
        if ($this->branches->isEmpty()) {
            return true;
        }

        return $this->branches->contains(fn (PartnerBranch $branch): bool => (bool) $branch->active_status);
    }
}
