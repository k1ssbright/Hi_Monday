<?php

namespace App\Models\Lead;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Business\Business;
use App\Models\Business\BusinessBranch;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Employee\EmployeeBranch;
use App\Models\Marketing\MarketingAd;
use App\Models\Marketing\MarketingAdUnit;
use App\Models\Partner\Partner;
use App\Models\ProductService\ProductServiceCategory;
use App\Models\ProductService\ProductServiceItem;
use App\Models\Sales\Opportunity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'contacts',
        'activities',
    ];

    public const STATUSES = [
        'draft', 'new', 'contacting', 'nurturing', 'qualified',
        'converted', 'unqualified', 'duplicate', 'archived',
    ];

    public const OPEN_STATUSES = ['draft', 'new', 'contacting', 'nurturing', 'qualified'];

    public const STATUS_LABELS = [
        'draft' => 'ร่าง',
        'new' => 'ใหม่',
        'contacting' => 'กำลังติดต่อ',
        'nurturing' => 'ดูแลต่อ',
        'qualified' => 'ผ่านเกณฑ์',
        'converted' => 'แปลงแล้ว',
        'unqualified' => 'ปิด / ไม่ผ่าน',
        'duplicate' => 'ซ้ำ',
        'archived' => 'คลัง',
    ];

    public const QUALITY_FACTORS = [
        'quality_demand' => 'ความต้องการ',
        'quality_competition' => 'ระดับการแข่งขัน',
        'quality_authority' => 'อำนาจตัดสินใจ',
        'quality_budget_fit' => 'งบประมาณตรงราคา',
    ];

    protected $fillable = [
        'business_id', 'business_branch_id', 'registration_no', 'registration_date', 'fiscal_year',
        'status', 'lead_channel_id', 'marketing_ad_id', 'marketing_ad_unit_id',
        'utm_source', 'utm_medium', 'utm_campaign', 'form_id', 'external_id',
        'lead_project_type_id', 'product_service_category_id', 'product_service_item_id',
        'organization_name', 'organization_type', 'tax_no', 'address',
        'province_id', 'amphure_id', 'tambon_id', 'project_name', 'project_scope',
        'quality_demand', 'quality_competition', 'quality_authority', 'quality_budget_fit',
        'quality_score', 'tier', 'is_high_focus', 'nurture_review_at',
        'lead_closed_reason_id', 'closed_note', 'closed_at',
        'duplicate_of_lead_id', 'matched_partner_id', 'reopened_from_lead_id',
        'transferred_to_business_id', 'transferred_to_lead_id',
        'converted_at', 'converted_opportunity_id',
        'created_by', 'assigned_to', 'first_activity_at', 'create_by',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'nurture_review_at' => 'date',
            'closed_at' => 'datetime',
            'converted_at' => 'datetime',
            'first_activity_at' => 'datetime',
            'is_high_focus' => 'boolean',
            'quality_score' => 'float',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LeadChannel::class, 'lead_channel_id');
    }

    public function marketingAd(): BelongsTo
    {
        return $this->belongsTo(MarketingAd::class, 'marketing_ad_id');
    }

    public function adUnit(): BelongsTo
    {
        return $this->belongsTo(MarketingAdUnit::class, 'marketing_ad_unit_id');
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(LeadProjectType::class, 'lead_project_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductServiceCategory::class, 'product_service_category_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_service_item_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }

    public function amphure(): BelongsTo
    {
        return $this->belongsTo(Amphures::class, 'amphure_id');
    }

    public function tambon(): BelongsTo
    {
        return $this->belongsTo(Tambons::class, 'tambon_id');
    }

    public function closedReason(): BelongsTo
    {
        return $this->belongsTo(LeadClosedReason::class, 'lead_closed_reason_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'created_by');
    }

    public function matchedPartner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'matched_partner_id');
    }

    public function convertedOpportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class, 'converted_opportunity_id');
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_lead_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(LeadContact::class);
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(LeadContact::class)->where('is_primary', true);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('occurred_at');
    }

    public function inboundTouches(): HasMany
    {
        return $this->hasMany(LeadInboundTouch::class)->orderBy('contacted_at');
    }

    public function intakeAnswers(): HasMany
    {
        return $this->hasMany(LeadIntakeAnswer::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class)->latest('id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(LeadStatusLog::class)->latest('id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['converted', 'archived'], true);
    }

    public function recomputeTier(): void
    {
        $config = LeadTierSetting::forBusiness((string) $this->business_id);
        $raw = [
            'quality_demand' => $this->quality_demand,
            'quality_competition' => $this->quality_competition,
            'quality_authority' => $this->quality_authority,
            'quality_budget_fit' => $this->quality_budget_fit,
        ];

        $weightedSum = 0.0;
        $weightTotal = 0.0;
        $answered = 0;
        foreach (($config['factors'] ?? []) as $key => $factor) {
            if (empty($factor['enabled']) || $key === 'timeline') {
                continue;
            }
            $score = $raw[$key] ?? null;
            if ($score === null || (int) $score <= 0) {
                continue;
            }
            $weight = (float) ($factor['weight'] ?? 1);
            if ($weight <= 0) {
                continue;
            }
            $weightedSum += ((float) $score) * $weight;
            $weightTotal += $weight;
            $answered++;
        }

        $minRequired = (int) ($config['min_factors_required'] ?? 1);
        if ($answered < max(1, $minRequired) || $weightTotal <= 0) {
            $this->quality_score = null;
            $this->tier = null;

            return;
        }

        $avg = $weightedSum / $weightTotal;
        $this->quality_score = round($avg, 2);
        if ($avg >= (float) ($config['t1_min'] ?? 3.5)) {
            $this->tier = 'T1';
        } elseif ($avg >= (float) ($config['t2_min'] ?? 2.5)) {
            $this->tier = 'T2';
        } else {
            $this->tier = 'T3';
        }
    }
}
