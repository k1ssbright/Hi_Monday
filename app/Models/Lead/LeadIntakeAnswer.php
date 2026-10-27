<?php

namespace App\Models\Lead;

use App\Models\ProductService\ProductServiceQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadIntakeAnswer extends Model
{
    protected $fillable = [
        'lead_id', 'product_service_question_id', 'label', 'value',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProductServiceQuestion::class, 'product_service_question_id');
    }
}
