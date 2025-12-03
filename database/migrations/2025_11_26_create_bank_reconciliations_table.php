<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedBigInteger('account_id'); // COA ID (Chart of Account)
            $table->date('reconciliation_date');
            $table->string('period_month', 7); // Format: YYYY-MM
            $table->decimal('bank_statement_balance', 15, 2)->default(0);
            $table->decimal('book_balance', 15, 2)->default(0);
            $table->decimal('adjusted_balance', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->enum('status', ['draft', 'completed', 'approved'])->default('draft');
            $table->text('notes')->nullable();
            $table->string('reconciled_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            
            $table->index(['outlet_id', 'reconciliation_date']);
            $table->index('status');
        });

        Schema::create('bank_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reconciliation_id');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->date('transaction_date');
            $table->string('transaction_number')->nullable();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['debit', 'credit']);
            $table->enum('status', ['unreconciled', 'reconciled', 'pending'])->default('unreconciled');
            $table->enum('category', ['deposit_in_transit', 'outstanding_check', 'bank_charge', 'bank_interest', 'error', 'other'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('reconciliation_id')->references('id')->on('bank_reconciliations')->onDelete('cascade');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            
            $table->index('reconciliation_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_items');
        Schema::dropIfExists('bank_reconciliations');
    }
};
