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
        Schema::table('sales_invoice', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_invoice', 'total_dibayar')) {
                $table->decimal('total_dibayar', 15, 2)->default(0)->after('total')->comment('Total amount paid so far');
            }
            if (!Schema::hasColumn('sales_invoice', 'sisa_tagihan')) {
                $table->decimal('sisa_tagihan', 15, 2)->default(0)->after('total_dibayar')->comment('Remaining balance');
            }
        });
        
        // Update existing invoices - set sisa_tagihan = total for unpaid invoices
        DB::statement('UPDATE sales_invoice SET sisa_tagihan = total WHERE sisa_tagihan = 0 AND status != "lunas"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice', function (Blueprint $table) {
            $table->dropColumn(['total_dibayar', 'sisa_tagihan']);
        });
    }
};
