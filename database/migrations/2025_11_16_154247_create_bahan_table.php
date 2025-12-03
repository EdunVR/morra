<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan', function (Blueprint $table) {
            $table->bigIncrements('id_bahan');
            $table->unsignedBigInteger('id_outlet');
            $table->string('kode_bahan', 50);
            $table->string('nama_bahan');
            $table->unsignedBigInteger('id_satuan')->nullable();
            $table->decimal('harga_beli', 15,2)->default('0');
            $table->decimal('stok', 15,2)->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan');
    }
};
