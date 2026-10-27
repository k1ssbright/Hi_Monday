<?php

namespace App\Services;

use App\Models\Sales\SalesDocumentAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class DocumentAttachmentService
{
    /**
     * @param  array<int, UploadedFile>|null  $files
     * @return list<SalesDocumentAttachment>
     */
    public function storeMany(
        Model $document,
        string $businessId,
        ?string $createBy,
        string $kind,
        ?array $files,
    ): array {
        if (! $files) {
            return [];
        }

        $saved = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }
            $saved[] = $this->storeOne($document, $businessId, $createBy, $kind, $file);
        }

        return $saved;
    }

    public function storeOne(
        Model $document,
        string $businessId,
        ?string $createBy,
        string $kind,
        UploadedFile $file,
    ): SalesDocumentAttachment {
        $fileName = uploadFileAddTime($file);

        return SalesDocumentAttachment::create([
            'business_id' => $businessId,
            'document_type' => $document->getMorphClass(),
            'document_id' => $document->getKey(),
            'kind' => $kind,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize() ?: null,
            'mime_type' => $file->getClientMimeType(),
            'create_by' => $createBy,
        ]);
    }

    /**
     * @param  list<SalesDocumentAttachment>  $attachments
     */
    public function copyTo(Model $target, string $businessId, ?string $createBy, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            SalesDocumentAttachment::create([
                'business_id' => $businessId,
                'document_type' => $target->getMorphClass(),
                'document_id' => $target->getKey(),
                'kind' => $attachment->kind,
                'file_name' => $attachment->file_name,
                'original_name' => $attachment->original_name,
                'file_size' => $attachment->file_size,
                'mime_type' => $attachment->mime_type,
                'create_by' => $createBy,
            ]);
        }
    }
}
