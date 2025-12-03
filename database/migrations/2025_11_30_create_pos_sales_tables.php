<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel pos_sales
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->dateTime('tanggal');
            $table->unsignedBigInteger('id_outlet');
            $table->unsignedBigInteger('id_member')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 15, 2)->default(0);
            $table->decimal('total_diskon', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('jenis_pembayaran', ['cash', 'transfer', 'qris'])->default('cash');
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('status', ['lunas', 'menunggu'])->default('lunas');
            $table->text('catatan')->nullable();
            $table->boolean('is_bon')->default(false);
            $table->unsignedBigInteger('id_penjualan')->nullable();
            $table->timestamps();

            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('set null');
            
            $table->index('tanggal');
            $table->index('id_outlet');
            $table->index('status');
        });

        // Tabel pos_sale_items
        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_sale_id');
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->string('nama_produk');
            $table->string('sku', 50)->nullable();
            $table->decimal('kuantitas', 10, 2);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->enum('tipe', ['produk', 'jasa'])->default('produk');
            $table->timestamps();

            $table->foreign('pos_sale_id')->references('id')->on('pos_sales')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('set null');
            
            $table->index('pos_sale_id');
        });

        // Tabel setting_coa_pos
        Schema::create('setting_coa_pos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_outlet')->unique();
            $table->unsignedBigInteger('accounting_book_id')->nullable();
            $table->string('akun_kas', 20)->nullable();
            $table->string('akun_bank', 20)->nullable();
            $table->string('akun_piutang_usaha', 20)->nullable();
            $table->string('akun_pendapatan_penjualan', 20)->nullable();
            $table->string('akun_hpp', 20)->nullable();
            $table->string('akun_persediaan', 20)->nullable();
            $table->timestamps();

            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('accounting_book_id')->references('id')->on('accounting_books')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
        Schema::dropIfExists('setting_coa_pos');
    }
};
