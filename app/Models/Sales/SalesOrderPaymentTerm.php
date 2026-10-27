<?php

namespace App\Models\Sales;

use App\Models\Concerns\SoftDeletesWithActor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderPaymentTerm extends Model
{
    use SoftDeletesWithActor;

    protected $fillable = [
        'deleted_by',
        'sales_order_revision_id',
        'quotation_payment_term_id',
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
        return $this->belongsTo(SalesOrderRevision::class, 'sales_order_revision_id');
    }

    public function quotationPaymentTerm(): BelongsTo
    {
        return $this->belongsTo(QuotationPaymentTerm::class);
    }
}
