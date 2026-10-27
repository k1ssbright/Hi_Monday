<?php

namespace App\Models\Purchasing;

use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Concerns\HasDocumentPartySnapshot;
use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisitionRevision extends Model
{
    use CascadesSoftDeletes, HasDocumentPartySnapshot, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'lines',
    ];

    public const STATUSES = [
        'draft',
        'submitted',
        'approved',
        'rejected',
        'cancelled',
    ];

    protected $fillable = [
        'purchase_requisition_id',
        'revision_no',
        'supersedes_revision_id',
        'status',
        'request_date',
        'required_date',
        'currency',
        'subtotal',
        'discount_amount',
        'vat_amount',
        'total_amount',
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
        'note',
        'rejected_note',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejected_at',
        'create_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'required_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'none_vat_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'after_discount_amount' => 'decimal:2',
            'customer_type' => 'integer',
            'customer_status' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_revision_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'approved_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionLine::class)->orderBy('id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class)->orderByDesc('created_at');
    }
}
