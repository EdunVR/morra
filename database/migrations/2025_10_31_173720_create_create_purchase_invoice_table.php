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
        Schema::create('purchase_invoice', function (Blueprint $table) {
            $table->id('id_purchase_invoice');
            $table->string('no_invoice', 50)->unique();
            $table->unsignedBigInteger('id_purchase_order')->nullable();
            $table->date('tanggal_invoice');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_pajak', 15, 2)->default(0);
            $table->decimal('total_diskon', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'diproses', 'dibayar', 'jatuh_tempo', 'dibatalkan'])->default('draft');
            $table->string('metode_pembayaran', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->index('id_purchase_order');
            $table->index('tanggal_invoice');
            $table->index('status');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice');
    }
};
