<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify status enum to include 'draft'
        DB::statement("ALTER TABLE sales_invoice MODIFY COLUMN status ENUM('draft', 'menunggu', 'dibayar_sebagian', 'lunas', 'gagal') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE sales_invoice MODIFY COLUMN status ENUM('menunggu', 'dibayar_sebagian', 'lunas', 'gagal') DEFAULT 'menunggu'");
    }
};
