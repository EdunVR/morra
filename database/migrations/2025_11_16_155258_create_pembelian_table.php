<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->bigIncrements('id_pembelian');
            $table->unsignedBigInteger('id_outlet');
            $table->unsignedBigInteger('id_supplier');
            $table->unsignedBigInteger('id_user');
            $table->date('tanggal');
            $table->integer('total_item')->default(0);
            $table->decimal('total_harga', 15,2)->default(0);
            $table->decimal('diskon', 15,2)->default(0);
            $table->decimal('bayar', 15,2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_supplier')->references('id_supplier')->on('supplier')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
