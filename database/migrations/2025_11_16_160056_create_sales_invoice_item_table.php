<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoice_item', function (Blueprint $table) {
            $table->bigIncrements('id_sales_invoice_item');
            $table->unsignedBigInteger('id_sales_invoice');
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->string('deskripsi')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('kuantitas', 10,2)->default(0);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga', 15,2)->default(0);
            $table->decimal('subtotal', 15,2)->default(0);
            $table->string('tipe', 50)->nullable();
            $table->decimal('diskon', 15,2)->default(0);
            $table->decimal('harga_normal', 15,2)->default(0);
            $table->timestamps();
            $table->foreign('id_sales_invoice')->references('id_sales_invoice')->on('sales_invoice')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_item');
    }
};
