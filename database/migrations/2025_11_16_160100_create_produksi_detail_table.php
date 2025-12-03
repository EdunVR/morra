<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_produksi');
            $table->unsignedBigInteger('id_bahan');
            $table->decimal('jumlah', 10,2);
            $table->decimal('harga', 15,2)->default(0);
            $table->decimal('subtotal', 15,2)->default(0);
            $table->timestamps();
            $table->foreign('id_produksi')->references('id_produksi')->on('produksi')->onDelete('cascade');
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_detail');
    }
};
