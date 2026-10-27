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
        Schema::create('partner_contact_branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_contact_id')->constrained('partner_contacts');
            $table->foreignUuid('partner_branch_id')->constrained('partner_branches');
            $table->string('partner_contact_position')->nullable()->comment('ตำแหน่งหลัก');
            $table->json('partner_contact_positions')->nullable()->comment('ตำแหน่งทั้งหมด (รองรับหลายตำแหน่ง)');
            $table->string('department')->nullable()->comment('แผนก');
            $table->boolean('status')->default(true)->comment('สถานะการทำงาน 1 = ปกติ, 0 = ลาออก/พ้นสภาพ');
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
        Schema::dropIfExists('partner_contact_branches');
    }
};
