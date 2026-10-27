<?php

namespace App\Models\Sales;

use App\Models\Business\BusinessBranch;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Concerns\DisplaysDocumentRevision;
use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerBranch;
use App\Models\Partner\PartnerContact;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use CascadesSoftDeletes, DisplaysDocumentRevision, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'revisions',
    ];

    protected string $documentNumberAttribute = 'quotation_no';

    protected $fillable = [
        'business_id',
        'business_branch_id',
        'opportunity_id',
        'partner_id',
        'partner_branch_id',
        'partner_contact_id',
        'quotation_no',
        'current_revision_id',
        'create_by',
        'deleted_by',
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
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

    public function currentRevision(): BelongsTo
    {
        return $this->belongsTo(QuotationRevision::class, 'current_revision_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(QuotationRevision::class)->orderBy('revision_no');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuotationLine::class, 'quotation_revision_id', 'current_revision_id');
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class)->orderByDesc('created_at');
    }

    public function canCreateSalesOrder(): bool
    {
        return $this->currentRevision?->status === 'accepted'
            && ! $this->salesOrders()->exists();
    }
}
