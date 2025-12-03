<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('outlet_id');
            $table->string('bank_name');
            $table->string('account_number', 50);
            $table->string('account_holder');
            $table->string('branch', 100)->nullable();
            $table->enum('account_type', ['checking', 'savings', 'other'])->default('checking');
            $table->string('currency', 3)->default('IDR');
            $table->boolean('is_active')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_bank_accounts');
    }
};
