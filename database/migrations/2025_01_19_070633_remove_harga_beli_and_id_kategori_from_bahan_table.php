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
        Schema::table('bahan', function (Blueprint $table) {
            $table->dropColumn('harga_beli');   // Menghapus kolom harga_beli
            $table->dropColumn('id_kategori'); // Menghapus kolom id_kategori
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan', function (Blueprint $table) {
            $table->integer('harga_beli')->nullable();    // Menambahkan kembali kolom harga_beli
            $table->unsignedBigInteger('id_kategori')->nullable(); // Menambahkan kembali kolom id_kategori
        });
    }
};
