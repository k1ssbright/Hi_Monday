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
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('bank_id')->constrained('banks');
            $table->string('account_name');
            $table->string('account_no');
            $table->boolean('active_status')->default(true)->comment('1 = ใช้งาน , 0 = ไม่ใช้งาน');
            $table->boolean('default_status')->default(false)->comment('1 = ค่าเริ่มต้น , 0 = ตัวเลือกสำรอง');
            $table->boolean('is_salary')->default(false)->comment('1 = ใช้งาน , 0 = ไม่ใช้งาน');
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
        Schema::dropIfExists('user_bank_accounts');
    }
};
