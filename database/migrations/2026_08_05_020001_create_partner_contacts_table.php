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
        Schema::create('partner_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->constrained('partners');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->comment('เชื่อมโยงกับ users (ดึงข้อมูลผู้ใช้)');
            $table->string('email')->comment('อีเมลผู้ติดต่อ');
            $table->enum('prefix', ['นาย', 'นาง', 'นางสาว', 'Mr.', 'Mrs.', 'Ms.'])->comment('คำนำหน้าชื่อ');
            $table->string('other_prefix')->nullable()->comment('คำนำหน้าชื่ออื่นๆ');
            $table->string('firstname')->comment('ชื่อ');
            $table->string('surname')->comment('นามสกุล');
            $table->string('phone')->nullable()->comment('เบอร์โทรศัพท์');
            $table->string('address')->nullable()->comment('ที่อยู่');
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('amphure_id')->nullable()->constrained('amphures');
            $table->foreignId('tambon_id')->nullable()->constrained('tambons');
            $table->string('profile_image')->default('user.jpg');
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
        Schema::dropIfExists('partner_contacts');
    }
};
