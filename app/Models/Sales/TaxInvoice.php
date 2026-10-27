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

class TaxInvoice extends Model
{
    use CascadesSoftDeletes, DisplaysDocumentRevision, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'revisions',
    ];

    protected string $documentNumberAttribute = 'tax_invoice_no';

    protected $fillable = [
        'business_id',
        'business_branch_id',
        'opportunity_id',
        'sales_order_id',
        'sales_order_revision_id',
        'billing_note_id',
        'billing_note_revision_id',
        'delivery_note_id',
        'delivery_note_revision_id',
        'partner_id',
        'partner_branch_id',
        'partner_contact_id',
        'tax_invoice_no',
        'current_revision_id',
        'create_by',
        'deleted_by',
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function salesOrderRevision(): BelongsTo
    {
        return $this->belongsTo(SalesOrderRevision::class);
    }

    public function billingNote(): BelongsTo
    {
        return $this->belongsTo(BillingNote::class);
    }

    public function billingNoteRevision(): BelongsTo
    {
        return $this->belongsTo(BillingNoteRevision::class);
    }

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function deliveryNoteRevision(): BelongsTo
    {
        return $this->belongsTo(DeliveryNoteRevision::class);
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
        return $this->belongsTo(TaxInvoiceRevision::class, 'current_revision_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(TaxInvoiceRevision::class)->orderBy('revision_no');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(TaxInvoiceLine::class, 'tax_invoice_revision_id', 'current_revision_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class)->orderByDesc('created_at');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class)->orderByDesc('created_at');
    }

    public function debitNotes(): HasMany
    {
        return $this->hasMany(DebitNote::class)->orderByDesc('created_at');
    }

    public function canCreateReceipt(): bool
    {
        return $this->currentRevision?->status === 'issued';
    }

    public function canCreateCreditNote(): bool
    {
        return $this->currentRevision?->status === 'issued';
    }

    public function canCreateDebitNote(): bool
    {
        return $this->currentRevision?->status === 'issued';
    }
}
