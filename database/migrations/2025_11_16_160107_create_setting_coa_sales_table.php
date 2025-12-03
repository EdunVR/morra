<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_coa_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->unsignedBigInteger('accounting_book_id')->nullable();
            $table->string('akun_piutang_usaha', 50)->nullable();
            $table->string('akun_pendapatan_penjualan', 50)->nullable();
            $table->string('akun_kas', 50)->nullable();
            $table->string('akun_bank', 50)->nullable();
            $table->string('akun_hpp', 50)->nullable();
            $table->string('akun_persediaan', 50)->nullable();
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
            $table->foreign('accounting_book_id')->references('id')->on('accounting_books')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_coa_sales');
    }
};
