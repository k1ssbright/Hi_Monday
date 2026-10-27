<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('document_type', 64);
            $table->uuid('document_id');
            $table->string('kind', 32)->default('extra');
            $table->string('file_name');
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->index(['document_type', 'document_id'], 'sda_doc_idx');
            $table->index(['business_id', 'kind'], 'sda_biz_kind_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_document_attachments');
    }
};
