<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ประวัติการแก้ไขแคมเปญ — ต่ออายุ, ปรับงบ, เปลี่ยนสถานะ ฯลฯ
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_ad_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_ad_id')->constrained('marketing_ads');
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('action'); // created / updated / archived / restored / duplicated
            $table->json('changes')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index('marketing_ad_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_ad_logs');
    }
};
