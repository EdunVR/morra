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
        Schema::create('produk_tipe', function (Blueprint $table) {
            $table->increments('id'); // ID unik untuk tabel ini
            $table->unsignedInteger('id_produk'); // ID produk dari tabel produk
            $table->unsignedInteger('id_tipe'); // ID tipe dari tabel tipe
            $table->tinyInteger('diskon')->default(0); // Diskon untuk produk dalam tipe
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->foreign('id_tipe')->references('id_tipe')->on('tipe')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_tipe');
    }
};
