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
        Schema::create('harga_bahan', function (Blueprint $table) {
            $table->increments('id'); // ID unik untuk tabel ini
            $table->unsignedInteger('id_bahan');
            $table->integer('harga_beli'); // Diskon untuk produk dalam tipe
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_bahan');
    }
};
