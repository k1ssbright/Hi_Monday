<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Product/Service items — consolidated final schema from oneClick3.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_service_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->nullable(); // รหัสระบบ generate เอง (เช่น P-00001)
            $table->uuid('legacy_product_id')->nullable()->index(); // เก็บ UUID ของ product เก่า (ถ้ามีย้ายข้อมูล)
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignId('product_service_category_id')->nullable()->constrained('product_service_categories');
            $table->enum('kind', ['product', 'service'])->default('product');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('default_price', 12, 2)->nullable();
            $table->decimal('buy_price', 12, 2)->nullable(); // ราคาทุน/ซื้อ
            $table->string('image')->nullable();
            $table->boolean('track_inventory')->default(false);
            $table->boolean('is_active')->default(true);
            // active = ใช้งานปกติ, inactive = ปิดชั่วคราว; archive ใช้ is_active=false
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'kind', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_service_items');
    }
};
