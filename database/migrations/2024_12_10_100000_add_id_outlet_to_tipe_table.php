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
        Schema::table('tipe', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outlet')->nullable()->after('id_tipe');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->index('id_outlet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe', function (Blueprint $table) {
            $table->dropForeign(['id_outlet']);
            $table->dropIndex(['id_outlet']);
            $table->dropColumn('id_outlet');
        });
    }
};