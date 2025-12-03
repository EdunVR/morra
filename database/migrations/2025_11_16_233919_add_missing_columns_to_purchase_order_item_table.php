<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_item', function (Blueprint $table) {
            $table->string('tipe_item')->nullable();
            $table->unsignedBigInteger('id_bahan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_item', function (Blueprint $table) {

        });
    }
};
