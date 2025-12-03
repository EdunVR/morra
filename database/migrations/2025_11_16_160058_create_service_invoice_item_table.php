<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_invoice_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_service_invoice');
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->string('deskripsi')->nullable();
            $table->decimal('kuantitas', 10,2)->default(0);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga', 15,2)->default(0);
            $table->decimal('subtotal', 15,2)->default(0);
            $table->timestamps();
            $table->foreign('id_service_invoice')->references('id_service_invoice')->on('service_invoices')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoice_item');
    }
};
