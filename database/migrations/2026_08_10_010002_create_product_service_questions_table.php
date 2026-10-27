<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_service_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignId('product_service_category_id')->constrained('product_service_categories');
            $table->enum('purpose', ['intake', 'quality'])->default('intake');
            $table->string('label');
            $table->enum('input_type', ['text', 'number', 'select', 'checkbox', 'textarea', 'date'])->default('text');
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['product_service_category_id', 'purpose', 'is_active'], 'ps_questions_cat_purpose_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_service_questions');
    }
};
