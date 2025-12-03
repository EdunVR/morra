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
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->decimal('total_dibayar', 15, 2)->default(0)->after('total');
            $table->decimal('sisa_pembayaran', 15, 2)->nullable()->after('total_dibayar');
        });

        // Update existing records: sisa_pembayaran = total - total_dibayar
        DB::statement('UPDATE purchase_order SET sisa_pembayaran = total - total_dibayar');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn(['total_dibayar', 'sisa_pembayaran']);
        });
    }
};
