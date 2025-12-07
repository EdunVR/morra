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
        // Create spareparts table
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id('id_sparepart');
            $table->string('kode_sparepart', 50)->unique();
            $table->string('nama_sparepart', 255);
            $table->string('merk', 100)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->string('satuan', 50);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('kode_sparepart');
            $table->index('nama_sparepart');
            $table->index('is_active');
        });

        // Create sparepart_logs table
        Schema::create('sparepart_logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_sparepart');
            $table->unsignedBigInteger('id_user');
            $table->enum('tipe_perubahan', ['stok', 'harga']);
            $table->integer('nilai_lama')->default(0);
            $table->integer('nilai_baru')->default(0);
            $table->integer('selisih')->default(0);
            $table->string('keterangan', 255);
            $table->timestamps();
            
            $table->foreign('id_sparepart')->references('id_sparepart')->on('spareparts')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('id_sparepart');
            $table->index('tipe_perubahan');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_logs');
        Schema::dropIfExists('spareparts');
    }
};
