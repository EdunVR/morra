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
        Schema::create('invoice_payment_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sales_invoice');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('jenis_pembayaran', ['cash', 'transfer']);
            $table->string('nama_bank')->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->string('bukti_pembayaran')->nullable()->comment('Compressed payment proof image');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
                  
            $table->index('id_sales_invoice');
            $table->index('tanggal_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payment_history');
    }
};
