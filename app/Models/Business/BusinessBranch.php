<?php

namespace App\Models\Business;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BusinessBranch extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'employeeBranches',
    ];

    protected $fillable = [
        'business_id',
        'business_vat_file',
        'business_status',
        'business_branch_no',
        'business_branch_code',
        'business_branch_name_th',
        'business_en_status',
        'business_address_en',
        'business_branch_name_en',
        'business_address',
        'province_id',
        'amphure_id',
        'tambon_id',
        'business_tel',
        'business_phone',
        'business_fax',
        'business_website',
        'business_logo',
        'business_stamp',
        'business_account_finance_year',
        'business_business_finance_year',
        'business_payment_endOfMonth',
        'business_payment_date',
        'business_payment_day',
        'business_salary_endOfMonth',
        'business_salary_date',
        'sales_target_amount',
    ];

    /**
     * Get the business that owns the branch.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Get the province of the branch.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }

    /**
     * Get the amphure of the branch.
     */
    public function amphure(): BelongsTo
    {
        return $this->belongsTo(Amphures::class, 'amphure_id');
    }

    /**
     * Get the tambon of the branch.
     */
    public function tambon(): BelongsTo
    {
        return $this->belongsTo(Tambons::class, 'tambon_id');
    }

    public function employeeBranches(): HasMany
    {
        return $this->hasMany(EmployeeBranch::class, 'business_branch_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $branch): void {
            if (! empty($branch->business_branch_code)) {
                return;
            }

            $branch->business_branch_code = self::generateUniqueBranchCode(
                (string) $branch->business_id,
                $branch->business_branch_no
            );
        });
    }

    private static function generateUniqueBranchCode(string $businessId, ?string $branchNo = null): string
    {
        $businessPart = strtoupper(substr(str_replace('-', '', $businessId), 0, 6));

        $branchNoDigits = preg_replace('/\D+/', '', (string) ($branchNo ?? ''));
        if ($branchNoDigits !== '') {
            $branchPart = str_pad(substr($branchNoDigits, -3), 3, '0', STR_PAD_LEFT);
        } else {
            $nextSequence = self::withTrashed()
                ->where('business_id', $businessId)
                ->count() + 1;
            $branchPart = str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
        }

        $baseCode = $businessPart.'-B'.$branchPart;
        $candidate = $baseCode;
        $suffix = 2;

        while (self::withTrashed()->where('business_branch_code', $candidate)->exists()) {
            $candidate = $baseCode.'-'.$suffix;
            $suffix++;
        }

        return Str::upper($candidate);
    }
}
