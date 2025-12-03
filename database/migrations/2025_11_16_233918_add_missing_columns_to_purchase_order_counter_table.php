<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_counter', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->string('prefix')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_counter', function (Blueprint $table) {

        });
    }
};
