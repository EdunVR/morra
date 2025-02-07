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
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->integer('id_hpp')->after('id_produk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropColumn('id_hpp');
        });
    }
};
