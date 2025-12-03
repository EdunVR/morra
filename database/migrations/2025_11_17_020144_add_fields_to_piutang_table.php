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
        Schema::table('piutang', function (Blueprint $table) {
            $table->dateTime('tanggal_tempo')->nullable()->after('id_penjualan');
            $table->string('nama')->nullable()->after('tanggal_tempo');
            $table->decimal('piutang', 15, 2)->default(0)->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piutang', function (Blueprint $table) {
            $table->dropColumn('tanggal_tempo');
            $table->dropColumn('nama');
            $table->dropColumn('piutang');
        });
    }
};
