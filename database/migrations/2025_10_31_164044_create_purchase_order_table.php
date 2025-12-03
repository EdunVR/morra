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
        // Tabel purchase_order
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id('id_purchase_order');
            $table->string('no_po', 50)->unique();
            $table->dateTime('tanggal');
            $table->unsignedBigInteger('id_supplier')->nullable();
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_diskon', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'diproses', 'dikirim', 'diterima', 'dibatalkan', 'selesai'])->default('draft');
            $table->date('due_date')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('metode_pengiriman', 100)->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->text('catatan_status')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'status']);
            $table->index('id_supplier');
            $table->index('id_outlet');
            $table->index('id_user');
        });

        // Tabel purchase_order_item
        Schema::create('purchase_order_item', function (Blueprint $table) {
            $table->id('id_purchase_order_item');
            $table->unsignedBigInteger('id_purchase_order')->nullable();
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->string('deskripsi', 255);
            $table->text('keterangan')->nullable();
            $table->decimal('kuantitas', 10, 2);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->index('id_purchase_order');
            $table->index('id_produk');
        });

        // Tabel purchase_order_counter
        Schema::create('purchase_order_counter', function (Blueprint $table) {
            $table->id('id_counter');
            $table->integer('last_number')->default(0);
            $table->integer('year')->default(2024);
            $table->timestamps();
        });

        // Tabel setting_coa_purchase
        Schema::create('setting_coa_purchase', function (Blueprint $table) {
            $table->id('id_setting');
            $table->unsignedBigInteger('accounting_book_id')->nullable();
            $table->string('akun_hutang_usaha', 20)->nullable();
            $table->string('akun_persediaan', 20)->nullable();
            $table->string('akun_pembelian', 20)->nullable();
            $table->string('akun_kas', 20)->nullable();
            $table->string('akun_bank', 20)->nullable();
            $table->string('akun_ppn_masukan', 20)->nullable();
            $table->timestamps();

            $table->index('accounting_book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
        Schema::dropIfExists('purchase_order_item');
        Schema::dropIfExists('purchase_order_counter');
        Schema::dropIfExists('setting_coa_purchase');
    }
};
