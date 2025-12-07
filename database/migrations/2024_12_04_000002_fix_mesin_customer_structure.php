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
        // Drop unnecessary columns from mesin_customer
        $columnsToCheck = ['merk', 'tipe', 'no_seri', 'tahun_pembuatan', 'tanggal_beli', 'lokasi', 'status', 'catatan'];
        
        foreach ($columnsToCheck as $column) {
            if (Schema::hasColumn('mesin_customer', $column)) {
                Schema::table('mesin_customer', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        // Change biaya_service to decimal if it's varchar
        DB::statement('ALTER TABLE mesin_customer MODIFY biaya_service DECIMAL(15,2) NULL');
        
        // Try to add unique constraint on kode_mesin (ignore if already exists)
        try {
            DB::statement('ALTER TABLE mesin_customer ADD UNIQUE KEY `mesin_customer_kode_mesin_unique` (`kode_mesin`)');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        // Try to add foreign keys (ignore if already exist)
        try {
            DB::statement('ALTER TABLE mesin_customer ADD CONSTRAINT `mesin_customer_id_member_foreign` FOREIGN KEY (`id_member`) REFERENCES `member` (`id_member`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        try {
            DB::statement('ALTER TABLE mesin_customer ADD CONSTRAINT `mesin_customer_id_ongkir_foreign` FOREIGN KEY (`id_ongkir`) REFERENCES `ongkos_kirim` (`id_ongkir`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        // Try to add foreign keys for mesin_customer_produk
        try {
            DB::statement('ALTER TABLE mesin_customer_produk ADD CONSTRAINT `mesin_customer_produk_id_mesin_customer_foreign` FOREIGN KEY (`id_mesin_customer`) REFERENCES `mesin_customer` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        try {
            DB::statement('ALTER TABLE mesin_customer_produk ADD CONSTRAINT `mesin_customer_produk_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        // Try to add unique constraint
        try {
            DB::statement('ALTER TABLE mesin_customer_produk ADD UNIQUE KEY `mesin_customer_produk_id_mesin_customer_id_produk_unique` (`id_mesin_customer`, `id_produk`)');
        } catch (\Exception $e) {
            // Ignore if already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the columns if needed
        Schema::table('mesin_customer', function (Blueprint $table) {
            $table->string('merk', 100)->nullable();
            $table->string('tipe', 100)->nullable();
            $table->string('no_seri', 100)->nullable();
            $table->integer('tahun_pembuatan')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('status', 50)->nullable();
            $table->text('catatan')->nullable();
        });
    }
};
