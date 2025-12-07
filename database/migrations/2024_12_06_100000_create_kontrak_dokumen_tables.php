<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Kontrak Kerja
        Schema::create('kontrak_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_id'); // Using recruitment_id instead of employee_id
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->string('nomor_kontrak')->unique();
            $table->string('jenis_kontrak'); // PKWT, PKWTT, Freelance, Magang
            $table->string('jabatan');
            $table->string('unit_kerja');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('durasi_bulan')->nullable();
            $table->decimal('gaji_pokok', 15, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['aktif', 'habis', 'diperpanjang', 'dibatalkan'])->default('aktif');
            $table->unsignedBigInteger('perpanjangan_dari')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            if (Schema::hasTable('recruitments')) {
                $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
            }
        });

        // Tabel Perpanjangan Kontrak
        Schema::create('perpanjangan_kontrak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_lama_id');
            $table->unsignedBigInteger('kontrak_baru_id');
            $table->date('tanggal_perpanjangan');
            $table->text('alasan')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            
            $table->foreign('kontrak_lama_id')->references('id')->on('kontrak_kerja')->onDelete('cascade');
            $table->foreign('kontrak_baru_id')->references('id')->on('kontrak_kerja')->onDelete('cascade');
        });

        // Tabel Surat Peringatan
        Schema::create('surat_peringatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_id'); // Using recruitment_id
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->string('nomor_sp')->unique();
            $table->enum('jenis_sp', ['SP1', 'SP2', 'SP3']);
            $table->date('tanggal_sp');
            $table->date('tanggal_berlaku');
            $table->date('tanggal_berakhir')->nullable();
            $table->text('alasan');
            $table->text('catatan')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
            
            if (Schema::hasTable('recruitments')) {
                $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
            }
        });

        // Tabel Dokumen HR
        Schema::create('dokumen_hr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_id')->nullable(); // Using recruitment_id
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->string('nomor_dokumen')->unique();
            $table->string('jenis_dokumen'); // SK Jabatan, Surat Tugas, SK Lainnya, dll
            $table->string('judul_dokumen');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berlaku')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->boolean('memiliki_masa_berlaku')->default(false);
            $table->string('file_path')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['aktif', 'habis', 'dibatalkan'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
            
            if (Schema::hasTable('recruitments')) {
                $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpanjangan_kontrak');
        Schema::dropIfExists('surat_peringatan');
        Schema::dropIfExists('dokumen_hr');
        Schema::dropIfExists('kontrak_kerja');
    }
};
