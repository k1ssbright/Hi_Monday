<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadChannel extends Model
{
    use SoftDeletes;

    public const KINDS = ['paid', 'owned', 'inbound', 'partner', 'outbound', 'offline'];

    protected $fillable = [
        'business_id', 'code', 'name', 'kind', 'sort', 'is_active', 'create_by',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
