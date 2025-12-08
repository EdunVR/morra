<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk_tipe', function (Blueprint $table) {
            $table->decimal('diskon', 5, 2)->default(0)->after('id_tipe')->comment('Diskon dalam persen (0-100)');
            $table->decimal('harga_jual', 15, 2)->nullable()->after('diskon')->comment('Harga jual khusus untuk tipe ini');
        });
    }

    public function down(): void
    {
        Schema::table('produk_tipe', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'harga_jual']);
        });
    }
};
