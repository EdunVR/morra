<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_penjualan_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_po_penjualan')->nullable();
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->decimal('jumlah', 15, 2)->nullable();
            $table->string('diskon')->nullable();
            $table->string('hpp')->nullable();
            $table->unsignedBigInteger('id_hpp')->nullable();
            $table->string('tipe_item')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('po_penjualan_detail', function (Blueprint $table) {

        });
    }
};
