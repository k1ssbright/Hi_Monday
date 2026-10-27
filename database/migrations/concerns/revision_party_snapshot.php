<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * คอลัมน์ snapshot ลูกค้า/ผู้ขาย + VAT breakdown บน revision เอกสารขาย
 * (เรียกหลังคอลัมน์ totals / payment_unit_mode ในคำสั่ง create table เดียวกัน)
 */
function migration_add_revision_party_snapshot(Blueprint $table): void
{
    $table->string('customer_contact_name')->nullable();
    $table->string('customer_contact_phone')->nullable();
    $table->string('customer_email')->nullable();
    $table->tinyInteger('customer_type')->nullable()->comment('1=บริษัท, 2=ห้างหุ้นส่วน, 3=บุคคลธรรมดา');
    $table->string('customer_tax_no', 13)->nullable();
    $table->string('customer_name')->nullable();
    $table->tinyInteger('customer_status')->nullable()->comment('0=สำนักงานใหญ่, 1=สาขา');
    $table->string('customer_branch_no')->nullable();
    $table->string('customer_branch_name')->nullable();
    $table->string('customer_address')->nullable();
    $table->foreignId('customer_province_id')->nullable()->constrained('provinces');
    $table->foreignId('customer_amphure_id')->nullable()->constrained('amphures');
    $table->foreignId('customer_tambon_id')->nullable()->constrained('tambons');
    $table->string('customer_zip_code', 10)->nullable();
    $table->enum('vat_type', ['none', 'inclusive', 'exclusive'])->default('exclusive');
    $table->decimal('none_vat_amount', 15, 2)->default(0);
    $table->decimal('taxable_amount', 15, 2)->default(0);
    $table->decimal('after_discount_amount', 15, 2)->default(0);
    $table->string('seller_name')->nullable();
    $table->string('seller_phone')->nullable();
    $table->string('seller_email')->nullable();
}

function migration_add_actor_soft_delete(Blueprint $table): void
{
    $table->softDeletes();
    $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();
}
