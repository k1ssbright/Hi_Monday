<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use App\Models\Partner\PartnerContact;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadContact extends Model
{
    use HasUuids, SoftDeletes;

    public const PREFIXES = ['นาย', 'นาง', 'นางสาว', 'Mr.', 'Mrs.', 'Ms.'];

    protected $fillable = [
        'business_id', 'lead_id', 'prefix', 'other_prefix', 'firstname', 'surname',
        'position', 'phone', 'email', 'line_id', 'address', 'is_primary',
        'converted_to_partner_contact_id', 'create_by',
    ];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function convertedContact(): BelongsTo
    {
        return $this->belongsTo(PartnerContact::class, 'converted_to_partner_contact_id');
    }

    public function displayName(): string
    {
        return trim(($this->prefix ? $this->prefix.' ' : '').$this->firstname.' '.($this->surname ?? ''));
    }

    public function hasReachableChannel(): bool
    {
        return filled($this->phone) || filled($this->email) || filled($this->line_id);
    }
}
