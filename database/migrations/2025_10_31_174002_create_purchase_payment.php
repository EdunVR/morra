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
        Schema::create('purchase_payment', function (Blueprint $table) {
            $table->id('id_purchase_payment');
            $table->unsignedBigInteger('id_purchase_invoice')->nullable();
            $table->date('tanggal_bayar');
            $table->string('metode_bayar', 50);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('kode_bank', 20)->nullable();
            $table->string('no_referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'diproses', 'selesai', 'dibatalkan', 'gagal'])->default('draft');
            $table->string('bukti_bayar')->nullable();
            $table->timestamps();

            // Index untuk performa query
            $table->index('id_purchase_invoice');
            $table->index('tanggal_bayar');
            $table->index('status');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payment');
    }
};
