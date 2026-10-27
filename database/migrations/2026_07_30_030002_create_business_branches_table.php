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
        Schema::create('business_branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('business_vat_file')->nullable()->comment('เอกสาร ภ.พ.20');

            $table->tinyInteger('business_status')->comment('สถานะ 0 = สำนักงาน , 1 = สาขา');
            $table->string('business_branch_no')->nullable()->comment('รหัสสาขา');
            $table->string('business_branch_name_th')->nullable()->comment('ชื่อสาขา');

            $table->tinyInteger('business_en_status')->nullable()->comment('0 = ไม่ใช้ข้อมูลภาษาอังกฤษ , 1 = ใช้ข้อมูลภาษาอังกฤษ');
            $table->string('business_address_en')->nullable()->comment('ที่อยู่ ภาษาอังกฤษ');
            $table->string('business_branch_name_en')->nullable()->comment('ชื่อสาขา ภาษาอังกฤษ');

            $table->string('business_address')->nullable()->comment('ที่อยู่');
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('amphure_id')->nullable()->constrained('amphures');
            $table->foreignId('tambon_id')->nullable()->constrained('tambons');
            $table->string('business_tel', 255)->nullable()->comment('เบอร์โทรสำนักงาน');
            $table->string('business_phone', 255)->nullable()->comment('เบอร์โทรมือถือ');
            $table->string('business_fax')->nullable()->comment('โทรสาร (Fax)');
            $table->string('business_website')->nullable()->comment('เว็บไซต์');

            $table->string('business_logo')->nullable()->comment('โลโก้บริษัท');
            $table->string('business_stamp')->nullable()->comment('ตรายางอิเล็กทรอนิกส์');

            $table->tinyInteger('business_account_finance_year')->comment('Accounting Financial Year (เดือน)');
            $table->tinyInteger('business_business_finance_year')->comment('Business Financial Year (เดือน)');

            $table->boolean('business_payment_endOfMonth')->default(false)->comment('จ่ายทุกวันที่สิ้นเดือน');
            $table->string('business_payment_date')->nullable()->comment('จ่ายทุกวันที่(1-31) หากเดือนไหนมีไม่ถึงวันที่เลือกจะจ่ายวันสุดท้ายของเดือนนั้น');
            $table->string('business_payment_day')->nullable()->comment('จ่ายทุกวัน(monday, tuesday, wednesday, thursday, friday, saturday, sunday)');

            $table->boolean('business_salary_endOfMonth')->default(false)->comment('จ่ายทุกวันที่สิ้นเดือน');
            $table->string('business_salary_date')->nullable()->comment('จ่ายทุกวันที่(1-31) หากเดือนไหนมีไม่ถึงวันที่เลือกจะจ่ายวันสุดท้ายของเดือนนั้น');

            $table->decimal('sales_target_amount', 14, 2)->default(1000000);

            $table->foreignUuid('create_by_employee_branch')->nullable();
            $table->foreignUuid('create_by_user')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_branches');
    }
};
