<?php

namespace App\Models\Sales;

use App\Models\Concerns\SoftDeletesWithActor;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SalesDocumentAttachment extends Model
{
    use SoftDeletesWithActor;

    public const KIND_PO = 'po';

    public const KIND_EXTRA = 'extra';

    protected $fillable = [
        'business_id',
        'document_type',
        'document_id',
        'kind',
        'file_name',
        'original_name',
        'file_size',
        'mime_type',
        'create_by',
        'deleted_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'create_by');
    }

    public function publicUrl(): string
    {
        return asset('upload_file/'.$this->file_name);
    }

    public function existsOnDisk(): bool
    {
        return Storage::disk('public_uploads')->exists($this->file_name);
    }
}
