<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerContactBranch extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'partner_contact_id',
        'partner_branch_id',
        'partner_contact_position',
        'partner_contact_positions',
        'department',
        'status',
        'create_by',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(PartnerContact::class, 'partner_contact_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(PartnerBranch::class, 'partner_branch_id');
    }
}
