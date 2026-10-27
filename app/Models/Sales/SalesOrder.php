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

class SalesOrder extends Model
{
    use CascadesSoftDeletes, DisplaysDocumentRevision, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'revisions',
    ];

    protected string $documentNumberAttribute = 'sales_order_no';

    protected $fillable = [
        'business_id',
        'business_branch_id',
        'opportunity_id',
        'quotation_id',
        'quotation_revision_id',
        'partner_id',
        'partner_branch_id',
        'partner_contact_id',
        'sales_order_no',
        'current_revision_id',
        'create_by',
        'deleted_by',
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function quotationRevision(): BelongsTo
    {
        return $this->belongsTo(QuotationRevision::class);
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
        return $this->belongsTo(SalesOrderRevision::class, 'current_revision_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(SalesOrderRevision::class)->orderBy('revision_no');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesOrderLine::class, 'sales_order_revision_id', 'current_revision_id');
    }

    public function billingNotes(): HasMany
    {
        return $this->hasMany(BillingNote::class)->orderByDesc('created_at');
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class)->orderByDesc('created_at');
    }

    public function taxInvoices(): HasMany
    {
        return $this->hasMany(TaxInvoice::class)->orderByDesc('created_at');
    }

    public function canCreateBillingNote(): bool
    {
        return $this->currentRevision?->status === 'confirmed';
    }

    public function canCreateDeliveryNote(): bool
    {
        return $this->currentRevision?->status === 'confirmed';
    }

    public function canCreateTaxInvoice(): bool
    {
        return $this->currentRevision?->status === 'confirmed';
    }
}
