<?php

namespace App\Models\Sales;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Concerns\SoftDeletesWithActor;
use App\Services\DocumentTotalsService;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class QuotationRevision extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'lines',
        'paymentTerms',
        'attachments',
    ];

    public const STATUSES = [
        'draft',
        'pending_approval',
        'approved',
        'sent',
        'accepted',
        'rejected',
        'expired',
        'cancelled',
        'superseded',
    ];

    protected $fillable = [
        'quotation_id',
        'revision_no',
        'supersedes_revision_id',
        'status',
        'issue_date',
        'valid_until',
        'currency',
        'subtotal',
        'discount_amount',
        'vat_amount',
        'total_amount',
        'payment_unit_mode',
        'customer_contact_name',
        'customer_contact_phone',
        'customer_email',
        'customer_type',
        'customer_tax_no',
        'customer_name',
        'customer_status',
        'customer_branch_no',
        'customer_branch_name',
        'customer_address',
        'customer_province_id',
        'customer_amphure_id',
        'customer_tambon_id',
        'customer_zip_code',
        'vat_type',
        'none_vat_amount',
        'taxable_amount',
        'after_discount_amount',
        'seller_name',
        'seller_phone',
        'seller_email',
        'approval_note',
        'accepted_note',
        'customer_po_no',
        'rejected_note',
        'approved_by',
        'approved_at',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'create_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'none_vat_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'after_discount_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'customer_type' => 'integer',
            'customer_status' => 'integer',
        ];
    }

    public function vatTypeLabel(): string
    {
        return DocumentTotalsService::VAT_TYPES[$this->vat_type ?? ''] ?? '-';
    }

    public function customerProvince(): BelongsTo
    {
        return $this->belongsTo(Provinces::class, 'customer_province_id');
    }

    public function customerAmphure(): BelongsTo
    {
        return $this->belongsTo(Amphures::class, 'customer_amphure_id');
    }

    public function customerTambon(): BelongsTo
    {
        return $this->belongsTo(Tambons::class, 'customer_tambon_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_revision_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'approved_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuotationLine::class)->orderBy('id');
    }

    public function paymentTerms(): HasMany
    {
        return $this->hasMany(QuotationPaymentTerm::class)->orderBy('installment_no');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(SalesDocumentAttachment::class, 'document');
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class)->orderByDesc('created_at');
    }
}
