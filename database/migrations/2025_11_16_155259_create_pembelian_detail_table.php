<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->bigIncrements('id_pembelian_detail');
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('id_bahan');
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->decimal('harga_beli', 15,2)->default(0);
            $table->decimal('jumlah', 10,2)->default(0);
            $table->decimal('subtotal', 15,2)->default(0);
            $table->timestamps();
            $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->onDelete('cascade');
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_detail');
    }
};
