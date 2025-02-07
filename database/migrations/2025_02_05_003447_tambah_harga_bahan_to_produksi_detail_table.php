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
        Schema::table('produksi_detail', function (Blueprint $table) {
            $table->integer('harga_beli')->after('jumlah')->nullable();
            $table->string('tanggal_harga')->after('harga_beli')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi_detail', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
            $table->dropColumn('tanggal_harga');
        });
    }
};
