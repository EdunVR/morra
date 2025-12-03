<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_pengiriman', function (Blueprint $table) {
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->unsignedBigInteger('id_bahan')->nullable();
            $table->unsignedBigInteger('id_inventori')->nullable();
            $table->decimal('jumlah', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_pengiriman', function (Blueprint $table) {

        });
    }
};
