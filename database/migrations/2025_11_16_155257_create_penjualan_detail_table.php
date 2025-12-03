<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->bigIncrements('id_penjualan_detail');
            $table->unsignedBigInteger('id_penjualan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->unsignedBigInteger('id_hpp')->nullable();
            $table->decimal('hpp', 15,2)->default(0);
            $table->decimal('harga_jual', 15,2)->default(0);
            $table->decimal('jumlah', 10,2)->default(0);
            $table->decimal('diskon', 15,2)->default(0);
            $table->decimal('subtotal', 15,2)->default(0);
            $table->timestamps();
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};
