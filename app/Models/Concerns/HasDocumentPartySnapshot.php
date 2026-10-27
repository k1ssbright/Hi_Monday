<?php

namespace App\Models\Concerns;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Services\DocumentTotalsService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasDocumentPartySnapshot
{
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
}
