<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ช่องทางติดต่อระดับ Partner (เบอร์กลาง/อีเมลกลาง/Line/เว็บ)
     * เดิมใน oneClick3 ชื่อ contact_channels อ้างอิง contact → ปรับเป็น partners
     */
    public function up(): void
    {
        Schema::create('partner_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('partner_id')->constrained('partners');
            $table->string('channel_type', 20); // phone | email | line | website
            $table->string('value');
            $table->string('label')->nullable();
            $table->integer('sort')->default(0);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_channels');
    }
};
