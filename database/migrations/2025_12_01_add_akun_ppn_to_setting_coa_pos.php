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
        Schema::table('setting_coa_pos', function (Blueprint $table) {
            $table->string('akun_ppn', 20)->nullable()->after('akun_persediaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_coa_pos', function (Blueprint $table) {
            $table->dropColumn('akun_ppn');
        });
    }
};
