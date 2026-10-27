<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');

            $table->tinyInteger('partner_type')->comment('1 = บริษัท , 2 = ห้างหุ้นส่วน , 3 = บุคคลธรรมดา');
            $table->string('partner_tax_no', 13)->nullable()->comment('เลขประจำตัวผู้เสียภาษี');
            $table->tinyInteger('partner_vat_status')->comment('สถานะจดภาษีมูลค่าเพิ่ม 1 = จดภาษีมูลค่าเพิ่ม , 0 = ยังไม่เข้าระบบภาษีมูลค่าเพิ่ม ');
            
            $table->string('partner_code')->nullable()->comment('รหัสธุรกิจ');
            $table->string('partner_name_th')->nullable()->comment('ชื่อธุรกิจ');

            $table->tinyInteger('partner_en_status')->nullable()->comment('0 = ไม่ใช้ข้อมูลภาษาอังกฤษ , 1 = ใช้ข้อมูลภาษาอังกฤษ');
            $table->string('partner_name_en')->nullable()->comment('ชื่อธุรกิจ ภาษาอังกฤษ');

            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
