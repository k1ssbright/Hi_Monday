<?php

namespace App\Models\Sales;

use App\Models\Business\BusinessBranch;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\Employee\EmployeeBranch;
use App\Models\Lead\Lead;
use App\Models\Lead\LeadChannel;
use App\Models\Marketing\MarketingAd;
use App\Models\Marketing\MarketingAdUnit;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerBranch;
use App\Models\Partner\PartnerContact;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opportunity extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'lines',
    ];

    public const STAGES = [
        'discovery',
        'needs_analysis',
        'proposal',
        'negotiation',
        'closed_won',
        'closed_lost',
    ];

    public const STAGE_LABELS = [
        'discovery' => 'Discovery',
        'needs_analysis' => 'Needs Analysis',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'closed_won' => 'Closed Won',
        'closed_lost' => 'Closed Lost',
    ];

    protected $fillable = [
        'business_id',
        'business_branch_id',
        'opportunity_no',
        'name',
        'stage',
        'probability',
        'expected_close_date',
        'currency',
        'estimated_value',
        'partner_id',
        'partner_branch_id',
        'partner_contact_id',
        'owner_employee_branch_id',
        'converted_from_lead_id',
        'lead_channel_id',
        'marketing_ad_id',
        'marketing_ad_unit_id',
        'lead_closed_reason_id',
        'lost_note',
        'won_at',
        'lost_at',
        'create_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'expected_close_date' => 'date',
            'estimated_value' => 'decimal:2',
            'won_at' => 'datetime',
            'lost_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(BusinessBranch::class, 'business_branch_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function partnerBranch(): BelongsTo
    {
        return $this->belongsTo(PartnerBranch::class, 'partner_branch_id');
    }

    public function partnerContact(): BelongsTo
    {
        return $this->belongsTo(PartnerContact::class, 'partner_contact_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'owner_employee_branch_id');
    }

    public function convertedFromLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'converted_from_lead_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LeadChannel::class, 'lead_channel_id');
    }

    public function marketingAd(): BelongsTo
    {
        return $this->belongsTo(MarketingAd::class, 'marketing_ad_id');
    }

    public function marketingAdUnit(): BelongsTo
    {
        return $this->belongsTo(MarketingAdUnit::class, 'marketing_ad_unit_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OpportunityLine::class)->orderBy('id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class)->orderByDesc('created_at');
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class)->orderByDesc('created_at');
    }

    public function stageLabel(): string
    {
        return self::STAGE_LABELS[$this->stage] ?? $this->stage;
    }
}

