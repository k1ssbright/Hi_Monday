<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerChannel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'partner_id',
        'channel_type',
        'value',
        'label',
        'sort',
        'create_by',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
