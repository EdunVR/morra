<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->string('tanggal_dibayar')->nullable();
            $table->string('tanggal_payment')->nullable();
            $table->string('metode_payment')->nullable();
            $table->integer('no_referensi_payment')->nullable();
            $table->text('catatan_payment')->nullable();
            $table->string('tanggal_vendor_bill')->nullable();
            $table->integer('no_vendor_bill')->nullable();
            $table->text('catatan_vendor_bill')->nullable();
            $table->string('tanggal_penerimaan')->nullable();
            $table->string('penerima_barang')->nullable();
            $table->text('catatan_penerimaan')->nullable();
            $table->string('tanggal_quotation')->nullable();
            $table->integer('no_quotation')->nullable();
            $table->text('catatan_quotation')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {

        });
    }
};
