<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_bank_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->integer('account_holder_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('sort_order')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('company_bank_accounts', function (Blueprint $table) {

        });
    }
};
