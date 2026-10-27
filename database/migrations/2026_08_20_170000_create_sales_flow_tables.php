<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->string('opportunity_no')->nullable();
            $table->string('name');
            $table->string('stage', 32)->default('discovery');
            $table->unsignedTinyInteger('probability')->default(10);
            $table->date('expected_close_date')->nullable();
            $table->char('currency', 3)->default('THB');
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->foreignUuid('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignUuid('partner_branch_id')->nullable()->constrained('partner_branches')->nullOnDelete();
            $table->foreignUuid('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->foreignUuid('owner_employee_branch_id')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->foreignUuid('converted_from_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('lead_channel_id')->nullable()->constrained('lead_channels')->nullOnDelete();
            $table->foreignId('marketing_ad_id')->nullable()->constrained('marketing_ads')->nullOnDelete();
            $table->foreignId('marketing_ad_unit_id')->nullable()->constrained('marketing_ad_units')->nullOnDelete();
            $table->foreignId('lead_closed_reason_id')->nullable()->constrained('lead_closed_reasons')->nullOnDelete();
            $table->text('lost_note')->nullable();
            $table->dateTime('won_at')->nullable();
            $table->dateTime('lost_at')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->index(['business_id', 'stage']);
            $table->index(['business_id', 'owner_employee_branch_id']);
            $table->unique(['business_id', 'opportunity_no']);
        });

        Schema::create('opportunity_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->foreignId('product_service_item_id')->nullable()->constrained('product_service_items')->nullOnDelete();
            $table->text('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price_estimate', 15, 2)->default(0);
            $table->decimal('line_total_estimate', 15, 2)->default(0);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_lines');
        Schema::dropIfExists('opportunities');
    }
};
