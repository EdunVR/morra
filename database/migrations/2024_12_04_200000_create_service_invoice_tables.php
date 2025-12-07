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
        // Create service_invoices table
        if (!Schema::hasTable('service_invoices')) {
            Schema::create('service_invoices', function (Blueprint $table) {
                $table->id('id_service_invoice');
                $table->string('no_invoice', 50)->unique();
                $table->dateTime('tanggal');
                $table->date('tanggal_mulai_service');
                $table->date('tanggal_selesai_service');
                $table->date('tanggal_service_berikutnya')->nullable();
                $table->unsignedBigInteger('id_member');
                $table->unsignedBigInteger('id_mesin_customer');
                $table->unsignedBigInteger('id_user');
                $table->boolean('is_garansi')->default(false);
                $table->decimal('diskon', 15, 2)->default(0);
                $table->decimal('total_sebelum_diskon', 15, 2)->default(0);
                $table->decimal('total_setelah_diskon', 15, 2)->default(0);
                $table->string('jenis_service', 50);
                $table->text('keterangan_service')->nullable();
                $table->integer('jumlah_teknisi')->default(0);
                $table->decimal('jumlah_jam', 8, 2)->default(0);
                $table->decimal('biaya_teknisi', 15, 2)->default(0);
                $table->enum('status', ['menunggu', 'lunas', 'gagal'])->default('menunggu');
                $table->date('due_date')->nullable();
                $table->timestamps();
                
                $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
                $table->foreign('id_mesin_customer')->references('id')->on('mesin_customer')->onDelete('cascade');
                $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Create service_invoice_items table
        if (!Schema::hasTable('service_invoice_items')) {
            Schema::create('service_invoice_items', function (Blueprint $table) {
                $table->id('id_service_invoice_item');
                $table->unsignedBigInteger('id_service_invoice');
                $table->unsignedBigInteger('id_produk')->nullable();
                $table->unsignedBigInteger('id_sparepart')->nullable();
                $table->string('deskripsi');
                $table->text('keterangan')->nullable();
                $table->decimal('kuantitas', 10, 2);
                $table->string('satuan', 50)->nullable();
                $table->decimal('harga', 15, 2);
                $table->decimal('diskon', 15, 2)->default(0);
                $table->decimal('harga_setelah_diskon', 15, 2);
                $table->decimal('subtotal', 15, 2);
                $table->enum('tipe', ['produk', 'ongkir', 'teknisi', 'sparepart', 'lainnya']);
                $table->boolean('is_sparepart')->default(false);
                $table->string('jenis_kendaraan', 50)->nullable();
                $table->string('kode_sparepart', 50)->nullable();
                $table->timestamps();
                
                $table->foreign('id_service_invoice')->references('id_service_invoice')->on('service_invoices')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_invoice_items');
        Schema::dropIfExists('service_invoices');
    }
};
