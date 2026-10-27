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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('businesses_id')->constrained('businesses');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->comment('เชื่อมโยงกับ users (ดึงข้อมูลผู้ใช้)');
            $table->foreignUuid('reports_to')->nullable()->constrained('employees')->comment('เชื่อมโยงกับ employees (ผู้บังคับบัญชี)');
            $table->string('employee_no')->nullable()->comment('รหัสพนักงาน');
            $table->boolean('status')->default(true)->comment('สถานะการทำงาน 1 = ปกติ, 0 = ลาออก/พ้นสภาพ');
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
        Schema::dropIfExists('employees');
    }
};
