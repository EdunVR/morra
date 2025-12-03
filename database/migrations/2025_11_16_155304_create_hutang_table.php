<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hutang', function (Blueprint $table) {
            $table->bigIncrements('id_hutang');
            $table->unsignedBigInteger('id_pembelian')->nullable();
            $table->unsignedBigInteger('id_supplier');
            $table->unsignedBigInteger('id_outlet');
            $table->decimal('jumlah_hutang', 15,2);
            $table->decimal('jumlah_dibayar', 15,2)->default(0);
            $table->decimal('sisa_hutang', 15,2);
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->timestamps();
            $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->onDelete('set null');
            $table->foreign('id_supplier')->references('id_supplier')->on('supplier')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang');
    }
};
