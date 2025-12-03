<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piutang', function (Blueprint $table) {
            $table->bigIncrements('id_piutang');
            $table->unsignedBigInteger('id_penjualan');
            $table->unsignedBigInteger('id_member')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->decimal('jumlah_piutang', 15,2);
            $table->decimal('jumlah_dibayar', 15,2)->default(0);
            $table->decimal('sisa_piutang', 15,2);
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->timestamps();
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('cascade');
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piutang');
    }
};
