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
        Schema::create('hpp_produk', function (Blueprint $table) {
            $table->increments('id_hpp'); // ID unik untuk tabel ini
            $table->unsignedInteger('id_produk');
            $table->integer('hpp'); // Diskon untuk produk dalam tipe
            $table->integer('stok');
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_produk');
    }
};
