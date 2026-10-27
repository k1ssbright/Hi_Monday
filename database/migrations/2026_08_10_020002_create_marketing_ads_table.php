<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Final schema from oneClick3 (platform/ads_id moved to marketing_ad_units).
     */
    public function up(): void
    {
        Schema::create('marketing_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('name');                       // ชื่อแคมเปญ/โฆษณา
            $table->foreignId('product_service_item_id')->nullable()->constrained('product_service_items');
            $table->decimal('budget', 12, 2)->nullable(); // งบประมาณ
            $table->enum('status', ['active', 'paused', 'ended'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);  // archive flag
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_ads');
    }
};
