<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_coa_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id')->unique();
            
            // Expense Accounts (Debit saat approve)
            $table->unsignedBigInteger('salary_expense_account_id')->nullable();
            $table->unsignedBigInteger('overtime_expense_account_id')->nullable();
            $table->unsignedBigInteger('bonus_expense_account_id')->nullable();
            $table->unsignedBigInteger('allowance_expense_account_id')->nullable();
            
            // Liability Accounts (Credit saat approve)
            $table->unsignedBigInteger('tax_payable_account_id')->nullable();
            $table->unsignedBigInteger('salary_payable_account_id')->nullable();
            
            // Asset Accounts
            $table->unsignedBigInteger('loan_receivable_account_id')->nullable(); // Debit saat approve (potongan pinjaman)
            $table->unsignedBigInteger('cash_account_id')->nullable(); // Credit saat pay
            
            $table->timestamps();

            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('salary_expense_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('overtime_expense_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('bonus_expense_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('allowance_expense_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('tax_payable_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('loan_receivable_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('salary_payable_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('cash_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_coa_settings');
    }
};
