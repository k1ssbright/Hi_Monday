<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require_once __DIR__.'/concerns/revision_party_snapshot.php';

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->foreignUuid('business_branch_id')->nullable()->constrained('business_branches')->nullOnDelete();
            $table->foreignUuid('opportunity_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignUuid('sales_order_revision_id')->nullable()->constrained('sales_order_revisions')->nullOnDelete();
            $table->foreignUuid('billing_note_id')->nullable()->constrained('billing_notes')->nullOnDelete();
            $table->foreignUuid('billing_note_revision_id')->nullable()->constrained('billing_note_revisions')->nullOnDelete();
            $table->foreignUuid('delivery_note_id')->nullable()->constrained('delivery_notes')->nullOnDelete();
            $table->foreignUuid('delivery_note_revision_id')->nullable()->constrained('delivery_note_revisions')->nullOnDelete();
            $table->foreignUuid('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignUuid('partner_branch_id')->nullable()->constrained('partner_branches')->nullOnDelete();
            $table->foreignUuid('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->string('tax_invoice_no')->nullable();
            $table->uuid('current_revision_id')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->index(['business_id']);
            $table->unique(['business_id', 'tax_invoice_no']);
        });

        Schema::create('tax_invoice_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tax_invoice_id')->constrained('tax_invoices')->cascadeOnDelete();
            $table->unsignedInteger('revision_no')->default(1);
            $table->uuid('supersedes_revision_id')->nullable();
            $table->string('status', 32)->default('draft');
            $table->date('issue_date')->nullable();
            $table->char('currency', 3)->default('THB');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            migration_add_revision_party_snapshot($table);
            $table->text('note')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('employee_branches')->nullOnDelete();

            $table->unique(['tax_invoice_id', 'revision_no']);
            $table->index(['tax_invoice_id', 'status']);
        });

        Schema::table('tax_invoice_revisions', function (Blueprint $table) {
            $table->foreign('supersedes_revision_id')->references('id')->on('tax_invoice_revisions')->nullOnDelete();
        });

        Schema::table('tax_invoices', function (Blueprint $table) {
            $table->foreign('current_revision_id')->references('id')->on('tax_invoice_revisions')->nullOnDelete();
        });

        Schema::create('tax_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tax_invoice_revision_id')->constrained('tax_invoice_revisions')->cascadeOnDelete();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoice_lines');
        Schema::dropIfExists('tax_invoice_revisions');
        Schema::dropIfExists('tax_invoices');
    }
};
