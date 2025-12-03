<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_penjualan', function (Blueprint $table) {
            $table->decimal('total_item', 15, 2)->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->string('diskon')->nullable();
            $table->string('ongkir')->nullable();
            $table->string('bayar')->nullable();
            $table->string('diterima')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('tanggal_tempo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('po_penjualan', function (Blueprint $table) {

        });
    }
};
