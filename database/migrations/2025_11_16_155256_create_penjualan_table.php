<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->bigIncrements('id_penjualan');
            $table->unsignedBigInteger('id_outlet');
            $table->unsignedBigInteger('id_member')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_gerobak')->nullable();
            $table->integer('total_item')->default(0);
            $table->decimal('total_harga', 15,2)->default(0);
            $table->decimal('diskon', 15,2)->default(0);
            $table->decimal('bayar', 15,2)->default(0);
            $table->decimal('diterima', 15,2)->default(0);
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
