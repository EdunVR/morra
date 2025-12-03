<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_price', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->decimal('harga_khusus', 15,2);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_price');
    }
};
