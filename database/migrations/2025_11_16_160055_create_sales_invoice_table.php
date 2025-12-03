<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoice', function (Blueprint $table) {
            $table->bigIncrements('id_sales_invoice');
            $table->string('no_invoice', 50)->unique();
            $table->datetime('tanggal');
            $table->unsignedBigInteger('id_member')->nullable();
            $table->unsignedBigInteger('id_prospek')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->unsignedBigInteger('id_customer_price')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_penjualan')->nullable();
            $table->decimal('total', 15,2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->date('due_date')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('jenis_pembayaran', 50)->nullable();
            $table->string('penerima')->nullable();
            $table->datetime('tanggal_pembayaran')->nullable();
            $table->text('catatan_pembayaran')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->decimal('jumlah_transfer', 15,2)->nullable();
            $table->decimal('total_diskon', 15,2)->default(0);
            $table->decimal('subtotal', 15,2)->default(0);
            $table->unsignedBigInteger('id_ongkir')->nullable();
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice');
    }
};
