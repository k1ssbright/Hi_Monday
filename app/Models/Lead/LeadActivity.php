<?php

namespace App\Models\Lead;

use App\Models\Business\Business;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadActivity extends Model
{
    use HasUuids, SoftDeletes;

    public const TYPES = ['call', 'chat', 'meeting', 'email', 'note', 'task'];

    public const TYPE_LABELS = [
        'call' => 'โทร',
        'chat' => 'แชท',
        'meeting' => 'นัดหมาย',
        'email' => 'อีเมล',
        'note' => 'โน้ต',
        'task' => 'งานติดตาม',
    ];

    public const CONTACT_TYPES = ['call', 'chat', 'meeting', 'email'];

    protected $fillable = [
        'business_id', 'lead_id', 'lead_contact_id', 'type', 'direction',
        'occurred_at', 'due_at', 'done_at', 'summary', 'outcome',
        'next_action', 'next_action_at', 'performed_by', 'create_by',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'due_at' => 'datetime',
            'done_at' => 'datetime',
            'next_action_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(LeadContact::class, 'lead_contact_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'performed_by');
    }
}
