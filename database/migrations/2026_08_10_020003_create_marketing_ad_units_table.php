<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Ad" ที่ยิงจริงภายใต้แคมเปญ (marketing_ads)
 *
 * แต่ละแพลตฟอร์มมีโครงสร้างต่างกันและเปลี่ยนบ่อย → ใช้ hybrid schema:
 *  - คอลัมน์จริง = ฟิลด์กลางที่ต้อง query / รวมยอด / คุมงบ
 *  - คอลัมน์ json `meta` = ฟิลด์เฉพาะแพลตฟอร์มที่เปลี่ยนบ่อย
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_ad_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_ad_id')->constrained('marketing_ads');
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('name');
            $table->string('platform')->nullable();
            $table->string('objective')->nullable();
            $table->string('external_id')->nullable();
            $table->decimal('budget', 12, 2)->default(0);
            $table->decimal('spend', 12, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'paused', 'ended'])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('meta')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'is_active']);
            $table->index('marketing_ad_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_ad_units');
    }
};
