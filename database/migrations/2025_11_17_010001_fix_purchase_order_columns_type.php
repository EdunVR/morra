<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom yang salah tipe datanya dari integer ke string
        DB::statement("ALTER TABLE `purchase_order` MODIFY `no_vendor_bill` VARCHAR(100) NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `no_quotation` VARCHAR(100) NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `no_referensi_payment` VARCHAR(100) NULL");
        
        // Ubah kolom tanggal dari string ke date/datetime
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_vendor_bill` DATE NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_payment` DATE NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_dibayar` DATE NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_penerimaan` DATE NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_quotation` DATE NULL");
    }

    public function down(): void
    {
        // Kembalikan ke tipe asli jika rollback
        DB::statement("ALTER TABLE `purchase_order` MODIFY `no_vendor_bill` INT NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `no_quotation` INT NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `no_referensi_payment` INT NULL");
        
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_vendor_bill` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_payment` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_dibayar` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_penerimaan` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `purchase_order` MODIFY `tanggal_quotation` VARCHAR(255) NULL");
    }
};
