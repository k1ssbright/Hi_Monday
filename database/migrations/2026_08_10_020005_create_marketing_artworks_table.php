<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ไฟล์ Artwork ของแคมเปญ — แนบได้ไม่จำกัด แต่ละไฟล์ระบุประเภท/แพลตฟอร์ม
     */
    public function up(): void
    {
        Schema::create('marketing_artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_ad_id')->constrained('marketing_ads');
            $table->foreignUuid('business_id')->nullable()->constrained('businesses');
            $table->string('file_name');
            $table->string('original_name')->nullable();
            $table->string('type')->default('other'); // facebook / instagram / tiktok / line / youtube / other
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_artworks');
    }
};
