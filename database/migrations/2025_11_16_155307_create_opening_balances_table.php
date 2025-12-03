<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_balances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('outlet_id');
            $table->integer('period_year');
            $table->integer('period_month');
            $table->decimal('debit', 15,2)->default(0);
            $table->decimal('credit', 15,2)->default(0);
            $table->decimal('balance', 15,2)->default(0);
            $table->boolean('is_posted')->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_balances');
    }
};
