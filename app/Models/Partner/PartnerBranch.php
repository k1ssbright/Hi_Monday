<?php

namespace App\Models\Partner;

use App\Models\Concerns\CascadesSoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class PartnerBranch extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'partner_vat_file',
        'partner_status',
        'partner_branch_no',
        'partner_branch_name_th',
        'partner_en_status',
        'partner_address_en',
        'partner_branch_name_en',
        'partner_address',
        'province_id',
        'amphure_id',
        'tambon_id',
        'partner_tel',
        'partner_phone',
        'partner_fax',
        'partner_website',
        'partner_logo',
        'partner_stamp',
        'is_vendor',
        'is_customer',
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

    public function contactBranches(): HasMany
    {
        return $this->hasMany(PartnerContactBranch::class, 'partner_branch_id');
    }

    protected function casts(): array
    {
        return [
            'is_vendor' => 'boolean',
            'is_customer' => 'boolean',
            'active_status' => 'boolean',
        ];
    }

    public static function roleFromFlags(bool $isCustomer, bool $isVendor): string
    {
        if ($isCustomer && $isVendor) {
            return 'both';
        }

        if ($isVendor && ! $isCustomer) {
            return 'vendor';
        }

        return 'customer';
    }

    public static function roleFlags(string $role): array
    {
        return match ($role) {
            'vendor' => ['is_customer' => false, 'is_vendor' => true],
            'both' => ['is_customer' => true, 'is_vendor' => true],
            default => ['is_customer' => true, 'is_vendor' => false],
        };
    }

    public static function labelForRole(string $role): string
    {
        return match ($role) {
            'vendor' => 'ผู้ขาย',
            'both' => 'ลูกค้า & ผู้ขาย',
            default => 'ลูกค้า',
        };
    }

    public function role(): string
    {
        return self::roleFromFlags((bool) $this->is_customer, (bool) $this->is_vendor);
    }

    public function roleLabel(): string
    {
        return self::labelForRole($this->role());
    }

    public function roleBadgeClass(): string
    {
        return match ($this->role()) {
            'vendor' => 'bg-warning-subtle text-warning',
            'both' => 'bg-primary-subtle text-primary',
            default => 'bg-info-subtle text-info',
        };
    }

    public function roleIconClass(): string
    {
        return match ($this->role()) {
            'vendor' => 'text-warning',
            'both' => 'text-primary',
            default => 'text-info',
        };
    }

    public function assignedContacts(): Collection
    {
        return $this->contactBranches
            ->map(fn (PartnerContactBranch $pivot) => $pivot->contact)
            ->filter()
            ->values();
    }
}
