<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_invoice_item', function (Blueprint $table) {
            $table->id('id_purchase_invoice_item');
            $table->unsignedBigInteger('id_purchase_invoice')->nullable();
            $table->unsignedBigInteger('id_purchase_order_item')->nullable();
            $table->string('deskripsi', 255);
            $table->decimal('kuantitas', 10, 2);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('pajak', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            // Index untuk mempercepat query relasional
            $table->index('id_purchase_invoice');
            $table->index('id_purchase_order_item');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_item');
    }
};
