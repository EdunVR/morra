<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key dulu
        Schema::table('sales_invoice', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        // Ubah kolom menjadi nullable
        DB::statement("ALTER TABLE `sales_invoice` MODIFY `id_user` BIGINT UNSIGNED NULL");
        
        // Tambahkan foreign key lagi
        Schema::table('sales_invoice', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign key
        Schema::table('sales_invoice', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        // Kembalikan ke NOT NULL
        DB::statement("ALTER TABLE `sales_invoice` MODIFY `id_user` BIGINT UNSIGNED NOT NULL");
        
        // Tambahkan foreign key lagi
        Schema::table('sales_invoice', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
