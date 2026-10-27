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
        Schema::create('employee_branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained('employees');
            $table->foreignUuid('business_branch_id')->constrained('business_branches');
            $table->string('employee_position')->nullable()->comment('ตำแหน่งหลัก');
            $table->string('department')->nullable()->comment('แผนก');
            $table->boolean('is_salary')->default(false)->comment('สถานะการจ่ายเงินเดือน');
            $table->foreignUuid('create_by_employee_branch')->nullable()->constrained('employee_branches');
            $table->foreignUuid('create_by_user')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('create_by_employee_branch')->references('id')->on('employee_branches');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreign('create_by_employee_branch')->references('id')->on('employee_branches');
        });

        Schema::table('business_users', function (Blueprint $table) {
            $table->foreign('create_by_employee_branch')->references('id')->on('employee_branches');
        });

        Schema::table('business_branches', function (Blueprint $table) {
            $table->foreign('create_by_employee_branch')->references('id')->on('employee_branches');
        });

        Schema::table('business_bank_accounts', function (Blueprint $table) {
            $table->foreign('create_by')->references('id')->on('employee_branches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_bank_accounts', function (Blueprint $table) {
            $table->dropForeign(['create_by']);
        });
        Schema::table('business_branches', function (Blueprint $table) {
            $table->dropForeign(['create_by_employee_branch']);
        });
        Schema::table('business_users', function (Blueprint $table) {
            $table->dropForeign(['create_by_employee_branch']);
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['create_by_employee_branch']);
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['create_by_employee_branch']);
        });
        Schema::dropIfExists('employee_branches');
    }
};
