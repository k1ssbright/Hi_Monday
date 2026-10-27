<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require_once __DIR__.'/concerns/revision_party_snapshot.php';

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->foreignUuid('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignUuid('partner_branch_id')->nullable()->constrained('partner_branches')->nullOnDelete();
            $table->foreignUuid('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->string('purchase_requisition_no')->nullable();
            $table->uuid('current_revision_id')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->index(['business_id']);
            $table->unique(['business_id', 'purchase_requisition_no']);
        });

        Schema::create('purchase_requisition_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
            $table->unsignedInteger('revision_no')->default(1);
            $table->uuid('supersedes_revision_id')->nullable();
            $table->string('status', 32)->default('draft');
            $table->date('request_date')->nullable();
            $table->date('required_date')->nullable();
            $table->char('currency', 3)->default('THB');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            migration_add_revision_party_snapshot($table);
            $table->text('note')->nullable();
            $table->text('rejected_note')->nullable();
            $table->foreignUuid('submitted_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->dateTime('submitted_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->unique(['purchase_requisition_id', 'revision_no'], 'pr_rev_unique');
            $table->index(['purchase_requisition_id', 'status'], 'pr_rev_status_idx');
        });

        Schema::table('purchase_requisition_revisions', function (Blueprint $table) {
            $table->foreign('supersedes_revision_id')->references('id')->on('purchase_requisition_revisions')->nullOnDelete();
        });

        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->foreign('current_revision_id')->references('id')->on('purchase_requisition_revisions')->nullOnDelete();
        });

        Schema::create('purchase_requisition_lines', function (Blueprint $table) {
            $table->id();
            $table->uuid('purchase_requisition_revision_id');
            $table->foreignId('product_service_item_id')->nullable()->constrained('product_service_items')->nullOnDelete();
            $table->text('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(7);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();
        });

        Schema::table('purchase_requisition_lines', function (Blueprint $table) {
            $table->foreign('purchase_requisition_revision_id', 'pr_lines_revision_fk')
                ->references('id')->on('purchase_requisition_revisions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_lines');
        Schema::dropIfExists('purchase_requisition_revisions');
        Schema::dropIfExists('purchase_requisitions');
    }
};
