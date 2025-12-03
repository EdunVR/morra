<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventori', function (Blueprint $table) {
            $table->bigIncrements('id_inventori');
            $table->string('kode_inventori', 50);
            $table->string('nama_barang');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->string('penanggung_jawab')->nullable();
            $table->integer('stok')->default(0);
            $table->string('lokasi_penyimpanan')->nullable();
            $table->string('status', 50)->default('baik');
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('set null');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventori');
    }
};
