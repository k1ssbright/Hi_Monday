<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->enum('prefix', ['นาย', 'นาง', 'นางสาว', 'Mr.', 'Mrs.', 'Ms.'])->nullable();
            $table->string('other_prefix')->nullable();
            $table->string('firstname');
            $table->string('surname')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('line_id')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->foreignUuid('converted_to_partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['lead_id', 'is_primary']);
            $table->index(['business_id', 'phone']);
            $table->index(['business_id', 'email']);
        });

        Schema::create('lead_inbound_touches', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('lead_channel_id')->nullable()->constrained('lead_channels')->nullOnDelete();
            $table->foreignId('marketing_ad_id')->nullable()->constrained('marketing_ads')->nullOnDelete();
            $table->foreignId('marketing_ad_unit_id')->nullable()->constrained('marketing_ad_units')->nullOnDelete();
            $table->dateTime('contacted_at');
            $table->json('payload')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
            $table->index(['lead_id', 'contacted_at']);
        });

        Schema::create('lead_intake_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('product_service_question_id')->nullable()->constrained('product_service_questions')->nullOnDelete();
            $table->string('label');
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('lead_contact_id')->nullable()->constrained('lead_contacts')->nullOnDelete();
            $table->enum('type', ['call', 'chat', 'meeting', 'email', 'note', 'task']);
            $table->enum('direction', ['inbound', 'outbound'])->nullable();
            $table->dateTime('occurred_at');
            $table->dateTime('due_at')->nullable();
            $table->dateTime('done_at')->nullable();
            $table->text('summary');
            $table->string('outcome')->nullable();
            $table->text('next_action')->nullable();
            $table->dateTime('next_action_at')->nullable();
            $table->foreignUuid('performed_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['lead_id', 'occurred_at']);
            $table->index('due_at');
        });

        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('from_assigned_to')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->foreignUuid('to_assigned_to')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->foreignUuid('from_business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->foreignUuid('to_business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->enum('reason', ['created', 'rule', 'manual', 'transfer', 'reopen']);
            $table->string('note')->nullable();
            $table->foreignId('lead_assignment_rule_id')->nullable()->constrained('lead_assignment_rules')->nullOnDelete();
            $table->foreignUuid('changed_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('lead_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->foreignId('lead_closed_reason_id')->nullable()->constrained('lead_closed_reasons')->nullOnDelete();
            $table->text('note')->nullable();
            $table->foreignUuid('changed_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_status_logs');
        Schema::dropIfExists('lead_assignments');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('lead_intake_answers');
        Schema::dropIfExists('lead_inbound_touches');
        Schema::dropIfExists('lead_contacts');
    }
};
