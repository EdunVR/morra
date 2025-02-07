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
        Schema::table('pembelian_detail', function (Blueprint $table) {
            // Tambahkan kolom id_bahan
            $table->Integer('id_bahan')->after('id_pembelian')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
    

            // Hapus kolom id_bahan
            $table->dropColumn('id_bahan');
        });
    }
};
