<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadClosedReason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id', 'name', 'code', 'applies_to', 'allows_reopen', 'sort', 'is_active', 'create_by',
    ];

    protected function casts(): array
    {
        return [
            'allows_reopen' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
