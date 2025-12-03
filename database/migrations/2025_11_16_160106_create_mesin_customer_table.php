<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesin_customer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_member');
            $table->string('kode_mesin', 50)->unique();
            $table->string('nama_mesin');
            $table->string('merk', 100)->nullable();
            $table->string('tipe', 100)->nullable();
            $table->string('no_seri', 100)->nullable();
            $table->integer('tahun_pembuatan')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('status', 50)->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesin_customer');
    }
};
