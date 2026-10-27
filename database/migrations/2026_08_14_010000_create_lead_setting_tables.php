<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('code')->nullable();
            $table->string('name');
            $table->enum('kind', ['paid', 'owned', 'inbound', 'partner', 'outbound', 'offline'])->default('inbound');
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'is_active']);
        });

        Schema::create('lead_closed_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('applies_to', ['lead', 'opportunity', 'both'])->default('lead');
            $table->boolean('allows_reopen')->default(false);
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_project_types', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('code')->nullable();
            $table->string('name');
            $table->foreignId('product_service_category_id')->nullable()->constrained('product_service_categories')->nullOnDelete();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_tier_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->unique()->constrained('businesses');
            $table->json('factors');
            $table->unsignedInteger('min_factors_required')->default(1);
            $table->decimal('t1_min', 4, 2)->default(3.50);
            $table->decimal('t2_min', 4, 2)->default(2.50);
            $table->unsignedInteger('t1_pct')->default(75);
            $table->unsignedInteger('t2_pct')->default(50);
            $table->unsignedInteger('t3_pct')->default(25);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
        });

        Schema::create('lead_sla_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->unique()->constrained('businesses');
            $table->unsignedInteger('first_touch_minutes')->default(60);
            $table->unsignedInteger('qualify_within_days')->default(7);
            $table->unsignedInteger('nurture_review_days')->default(30);
            $table->boolean('business_hours_only')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
        });

        Schema::create('lead_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->unsignedInteger('fiscal_year');
            $table->decimal('revenue_target', 15, 2)->default(0);
            $table->decimal('avg_deal_value', 15, 2)->default(0);
            $table->decimal('lead_to_opportunity_rate', 5, 2)->default(0);
            $table->decimal('opportunity_win_rate', 5, 2)->default(0);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->unique(['business_id', 'fiscal_year']);
        });

        Schema::create('lead_assignment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->integer('priority')->default(100);
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->foreignId('lead_channel_id')->nullable()->constrained('lead_channels')->nullOnDelete();
            $table->foreignId('marketing_ad_id')->nullable()->constrained('marketing_ads')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignUuid('assign_business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->foreignUuid('assign_employee_branch_id')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assignment_rules');
        Schema::dropIfExists('lead_goals');
        Schema::dropIfExists('lead_sla_settings');
        Schema::dropIfExists('lead_tier_settings');
        Schema::dropIfExists('lead_project_types');
        Schema::dropIfExists('lead_closed_reasons');
        Schema::dropIfExists('lead_channels');
    }
};
