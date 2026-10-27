<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('business_branch_id')->nullable()->constrained('business_branches');
            $table->string('registration_no')->nullable();
            $table->date('registration_date')->nullable();
            $table->unsignedInteger('fiscal_year')->nullable();
            $table->string('status', 32)->default('draft');
            $table->foreignId('lead_channel_id')->nullable()->constrained('lead_channels')->nullOnDelete();
            $table->foreignId('marketing_ad_id')->nullable()->constrained('marketing_ads')->nullOnDelete();
            $table->foreignId('marketing_ad_unit_id')->nullable()->constrained('marketing_ad_units')->nullOnDelete();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('form_id')->nullable();
            $table->string('external_id')->nullable();
            $table->foreignId('lead_project_type_id')->nullable()->constrained('lead_project_types')->nullOnDelete();
            $table->foreignId('product_service_category_id')->nullable()->constrained('product_service_categories')->nullOnDelete();
            $table->foreignId('product_service_item_id')->nullable()->constrained('product_service_items')->nullOnDelete();
            $table->string('organization_name')->nullable();
            $table->tinyInteger('organization_type')->nullable();
            $table->string('tax_no', 13)->nullable();
            $table->string('address')->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('amphure_id')->nullable()->constrained('amphures')->nullOnDelete();
            $table->foreignId('tambon_id')->nullable()->constrained('tambons')->nullOnDelete();
            $table->string('project_name')->nullable();
            $table->text('project_scope')->nullable();
            $table->unsignedTinyInteger('quality_demand')->nullable();
            $table->unsignedTinyInteger('quality_competition')->nullable();
            $table->unsignedTinyInteger('quality_authority')->nullable();
            $table->unsignedTinyInteger('quality_budget_fit')->nullable();
            $table->decimal('quality_score', 3, 2)->nullable();
            $table->string('tier', 4)->nullable();
            $table->boolean('is_high_focus')->default(false);
            $table->date('nurture_review_at')->nullable();
            $table->foreignId('lead_closed_reason_id')->nullable()->constrained('lead_closed_reasons')->nullOnDelete();
            $table->text('closed_note')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->uuid('duplicate_of_lead_id')->nullable();
            $table->foreignUuid('matched_partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->uuid('reopened_from_lead_id')->nullable();
            $table->foreignUuid('transferred_to_business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->uuid('transferred_to_lead_id')->nullable();
            $table->dateTime('converted_at')->nullable();
            $table->uuid('converted_opportunity_id')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->foreignUuid('assigned_to')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->dateTime('first_activity_at')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'assigned_to']);
            $table->unique(['business_id', 'registration_no']);
            $table->index('lead_channel_id');
            $table->index('marketing_ad_id');
            $table->index('duplicate_of_lead_id');
            $table->index('external_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('duplicate_of_lead_id')->references('id')->on('leads')->nullOnDelete();
            $table->foreign('reopened_from_lead_id')->references('id')->on('leads')->nullOnDelete();
            $table->foreign('transferred_to_lead_id')->references('id')->on('leads')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
