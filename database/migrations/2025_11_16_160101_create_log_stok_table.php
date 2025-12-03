<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_stok', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->unsignedBigInteger('id_bahan')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->string('jenis_transaksi', 50);
            $table->string('referensi', 100)->nullable();
            $table->decimal('jumlah', 10,2);
            $table->decimal('stok_sebelum', 10,2);
            $table->decimal('stok_sesudah', 10,2);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('set null');
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan')->onDelete('set null');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_stok');
    }
};
