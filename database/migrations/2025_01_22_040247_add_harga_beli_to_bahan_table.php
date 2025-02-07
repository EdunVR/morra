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
            $table->integer('harga_beli')->after('stok')->nullable();
            // `harga_beli` adalah kolom bertipe decimal (jumlah maksimal 15 digit, 2 desimal)
            // `after('nama_bahan')` untuk menempatkan kolom setelah kolom `nama_bahan`.
            // `nullable()` agar kolom dapat berisi NULL.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });
    }
};
