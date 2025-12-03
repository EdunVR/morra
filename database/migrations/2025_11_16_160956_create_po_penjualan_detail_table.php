<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_penjualan_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_po');
            $table->unsignedBigInteger('id_produk');
            $table->decimal('qty', 10,2);
            $table->decimal('harga', 15,2);
            $table->decimal('subtotal', 15,2);
            $table->timestamps();
            $table->foreign('id_po')->references('id_po')->on('po_penjualan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_penjualan_detail');
    }
};
