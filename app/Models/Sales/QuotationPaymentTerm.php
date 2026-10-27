<?php

namespace App\Models\Sales;

use App\Models\Concerns\SoftDeletesWithActor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationPaymentTerm extends Model
{
    use SoftDeletesWithActor;

    protected $fillable = [
        'deleted_by',
        'quotation_revision_id',
        'installment_no',
        'condition',
        'due_days',
        'due_date',
        'percent_amount',
        'baht_amount',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'percent_amount' => 'decimal:4',
            'baht_amount' => 'decimal:2',
        ];
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(QuotationRevision::class, 'quotation_revision_id');
    }
}
