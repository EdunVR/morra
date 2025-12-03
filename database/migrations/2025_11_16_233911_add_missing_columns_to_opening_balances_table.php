<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opening_balances', function (Blueprint $table) {
            $table->string('book_id')->nullable();
            $table->date('effective_date')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('opening_balances', function (Blueprint $table) {

        });
    }
};
