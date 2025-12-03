<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->bigIncrements('id_produksi');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_outlet');
            $table->date('tanggal_produksi');
            $table->decimal('jumlah_produksi', 10,2);
            $table->decimal('biaya_produksi', 15,2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
