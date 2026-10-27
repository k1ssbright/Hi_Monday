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
        Schema::create('partner_branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->constrained('partners');
            $table->string('partner_vat_file')->nullable()->comment('เอกสาร ภ.พ.20');

            $table->tinyInteger('partner_status')->comment('สถานะ 0 = สำนักงาน , 1 = สาขา');
            $table->string('partner_branch_no')->nullable()->comment('รหัสสาขา');
            $table->string('partner_branch_name_th')->nullable()->comment('ชื่อสาขา');

            $table->tinyInteger('partner_en_status')->nullable()->comment('0 = ไม่ใช้ข้อมูลภาษาอังกฤษ , 1 = ใช้ข้อมูลภาษาอังกฤษ');
            $table->string('partner_address_en')->nullable()->comment('ที่อยู่ ภาษาอังกฤษ');
            $table->string('partner_branch_name_en')->nullable()->comment('ชื่อสาขา ภาษาอังกฤษ');

            $table->string('partner_address')->nullable()->comment('ที่อยู่');
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('amphure_id')->nullable()->constrained('amphures');
            $table->foreignId('tambon_id')->nullable()->constrained('tambons');
            $table->string('partner_tel', 255)->nullable()->comment('เบอร์โทรสำนักงาน');
            $table->string('partner_phone', 255)->nullable()->comment('เบอร์โทรมือถือ');
            $table->string('partner_fax')->nullable()->comment('โทรสาร (Fax)');
            $table->string('partner_website')->nullable()->comment('เว็บไซต์');

            $table->string('partner_logo')->nullable()->comment('โลโก้บริษัท');
            $table->string('partner_stamp')->nullable()->comment('ตรายางอิเล็กทรอนิกส์');

            $table->boolean('is_vendor')->default(true)->comment('สถานะผู้ขาย 1 = ใช้งาน, 0 = ไม่ใช้งาน');
            $table->boolean('is_customer')->default(true)->comment('สถานะผู้ซื้อ 1 = ใช้งาน, 0 = ไม่ใช้งาน');
            $table->boolean('active_status')->default(true)->comment('สถานะการใช้งาน 1 = ใช้งาน, 0 = ไม่ใช้งาน');

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
        Schema::dropIfExists('partner_branches');
    }
};
