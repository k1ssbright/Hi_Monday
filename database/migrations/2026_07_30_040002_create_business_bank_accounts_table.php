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
        Schema::create('business_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('bank_id')->constrained('banks');            
            $table->string('account_name');
            $table->string('account_no');
            $table->boolean('active_status')->default(true)->comment('1 = ใช้งาน , 0 = ไม่ใช้งาน');
            $table->boolean('default_status')->default(false)->comment('1 = ค่าเริ่มต้น , 0 = ตัวเลือกสำรอง');
            $table->boolean('is_salary')->default(false)->comment('1 = ใช้งาน , 0 = ไม่ใช้งาน');
            $table->foreignUuid('create_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_bank_accounts');
    }
};
