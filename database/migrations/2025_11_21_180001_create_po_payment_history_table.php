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
        Schema::create('po_payment_history', function (Blueprint $table) {
            $table->id('id_payment');
            $table->unsignedBigInteger('id_purchase_order');
            $table->date('tanggal_pembayaran');
            $table->decimal('jumlah_pembayaran', 15, 2);
            $table->enum('jenis_pembayaran', ['cash', 'transfer']);
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->string('penerima', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_purchase_order')
                  ->references('id_purchase_order')
                  ->on('purchase_order')
                  ->onDelete('cascade');

            // Indexes for performance
            $table->index('id_purchase_order');
            $table->index('tanggal_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_payment_history');
    }
};
