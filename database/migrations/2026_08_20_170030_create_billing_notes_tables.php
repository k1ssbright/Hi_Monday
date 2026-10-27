<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require_once __DIR__.'/concerns/revision_party_snapshot.php';

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->foreignUuid('opportunity_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignUuid('sales_order_revision_id')->nullable()->constrained('sales_order_revisions')->nullOnDelete();
            $table->foreignUuid('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignUuid('partner_branch_id')->nullable()->constrained('partner_branches')->nullOnDelete();
            $table->foreignUuid('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->string('billing_note_no')->nullable();
            $table->uuid('current_revision_id')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->index(['business_id']);
            $table->unique(['business_id', 'billing_note_no']);
        });

        Schema::create('billing_note_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('billing_note_id')->constrained('billing_notes')->cascadeOnDelete();
            $table->unsignedInteger('revision_no')->default(1);
            $table->uuid('supersedes_revision_id')->nullable();
            $table->string('status', 32)->default('draft');
            $table->date('issue_date')->nullable();
            $table->char('currency', 3)->default('THB');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('payment_unit_mode', 10)->default('percent');
            $table->unsignedInteger('installment_no')->nullable();
            $table->decimal('billing_percent', 12, 4)->nullable();
            $table->foreignId('sales_order_payment_term_id')->nullable()->constrained('sales_order_payment_terms')->nullOnDelete();
            migration_add_revision_party_snapshot($table);
            $table->text('note')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->unique(['billing_note_id', 'revision_no']);
            $table->index(['billing_note_id', 'status']);
        });

        Schema::table('billing_note_revisions', function (Blueprint $table) {
            $table->foreign('supersedes_revision_id')->references('id')->on('billing_note_revisions')->nullOnDelete();
        });

        Schema::table('billing_notes', function (Blueprint $table) {
            $table->foreign('current_revision_id')->references('id')->on('billing_note_revisions')->nullOnDelete();
        });

        Schema::create('billing_note_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('billing_note_revision_id')->constrained('billing_note_revisions')->cascadeOnDelete();
            $table->foreignId('sales_order_line_id')->nullable()->constrained('sales_order_lines')->nullOnDelete();
            $table->foreignId('product_service_item_id')->nullable()->constrained('product_service_items')->nullOnDelete();
            $table->text('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(7);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();
        });

        Schema::create('billing_note_payment_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('billing_note_revision_id')->constrained('billing_note_revisions')->cascadeOnDelete();
            $table->foreignId('sales_order_payment_term_id')->nullable()->constrained('sales_order_payment_terms')->nullOnDelete();
            $table->unsignedTinyInteger('installment_no')->default(1);
            $table->text('condition')->nullable();
            $table->unsignedInteger('due_days')->default(0);
            $table->date('due_date')->nullable();
            $table->decimal('percent_amount', 12, 4)->default(0);
            $table->decimal('baht_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->index(['billing_note_revision_id', 'installment_no'], 'bn_pay_terms_rev_inst_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_note_payment_terms');
        Schema::dropIfExists('billing_note_lines');
        Schema::dropIfExists('billing_note_revisions');
        Schema::dropIfExists('billing_notes');
    }
};
