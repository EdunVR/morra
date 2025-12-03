<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('id_produk');
            $table->unsignedBigInteger('id_outlet');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('kode_produk', 50);
            $table->string('nama_produk');
            $table->string('merk', 100)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->decimal('diskon', 15,2)->default('0');
            $table->decimal('harga_jual', 15,2)->default('0');
            $table->unsignedBigInteger('id_satuan')->nullable();
            $table->string('tipe_produk', 50)->default('barang_dagang');
            $table->boolean('track_inventory')->default(true);
            $table->string('metode_hpp', 50)->default('average');
            $table->string('jenis_paket', 50)->nullable();
            $table->unsignedBigInteger('keberangkatan_template_id')->nullable();
            $table->integer('stok_minimum')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('set null');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
