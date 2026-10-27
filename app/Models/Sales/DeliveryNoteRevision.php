<?php

namespace App\Models\Sales;

use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Concerns\HasDocumentPartySnapshot;
use App\Models\Concerns\SoftDeletesWithActor;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryNoteRevision extends Model
{
    use CascadesSoftDeletes, HasDocumentPartySnapshot, HasUuids, SoftDeletesWithActor;

    protected array $cascadeSoftDeletes = [
        'lines',
    ];

    public const STATUSES = [
        'draft',
        'issued',
        'voided',
    ];

    protected $fillable = [
        'delivery_note_id',
        'revision_no',
        'supersedes_revision_id',
        'status',
        'issue_date',
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
        'issued_at',
        'voided_at',
        'create_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'none_vat_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'after_discount_amount' => 'decimal:2',
            'customer_type' => 'integer',
            'customer_status' => 'integer',
            'issued_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_revision_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(DeliveryNoteLine::class)->orderBy('id');
    }
}
