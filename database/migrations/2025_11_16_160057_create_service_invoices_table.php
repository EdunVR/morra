<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->bigIncrements('id_service_invoice');
            $table->string('no_invoice', 50)->unique();
            $table->date('tanggal');
            $table->date('tanggal_mulai_service')->nullable();
            $table->date('tanggal_selesai_service')->nullable();
            $table->unsignedBigInteger('id_member')->nullable();
            $table->unsignedBigInteger('id_mesin_customer')->nullable();
            $table->string('jenis_service', 100)->nullable();
            $table->integer('jumlah_teknisi')->default(0);
            $table->decimal('jumlah_jam', 10,2)->default(0);
            $table->decimal('biaya_teknisi', 15,2)->default(0);
            $table->boolean('is_garansi')->default(0);
            $table->decimal('diskon', 15,2)->default(0);
            $table->decimal('total_sebelum_diskon', 15,2)->default(0);
            $table->decimal('total', 15,2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->datetime('due_date')->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_service_berikutnya')->nullable();
            $table->unsignedBigInteger('id_invoice_sebelumnya')->nullable();
            $table->integer('service_lanjutan_ke')->default(0);
            $table->string('jenis_pembayaran', 50)->nullable();
            $table->string('penerima')->nullable();
            $table->datetime('tanggal_pembayaran')->nullable();
            $table->text('catatan_pembayaran')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->text('keterangan_service')->nullable();
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoices');
    }
};
