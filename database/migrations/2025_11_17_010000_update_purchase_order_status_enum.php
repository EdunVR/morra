<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status menjadi string untuk menampung nilai yang lebih panjang
        DB::statement("ALTER TABLE `purchase_order` MODIFY `status` VARCHAR(50) DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM jika rollback
        DB::statement("ALTER TABLE `purchase_order` MODIFY `status` ENUM('draft', 'diproses', 'dikirim', 'diterima', 'dibatalkan', 'selesai') DEFAULT 'draft'");
    }
};
