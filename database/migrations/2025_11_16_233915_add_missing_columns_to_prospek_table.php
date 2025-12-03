<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            $table->date('tanggal')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('jenis')->nullable();
            $table->string('provinsi_id')->nullable();
            $table->string('kabupaten_id')->nullable();
            $table->string('kecamatan_id')->nullable();
            $table->string('desa_id')->nullable();
            $table->string('pemilik_manager')->nullable();
            $table->string('kapasitas_produksi')->nullable();
            $table->string('sistem_produksi')->nullable();
            $table->string('bahan_bakar')->nullable();
            $table->string('informasi_perusahaan')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('recruitment_id')->nullable();
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->string('current_status')->nullable();
            $table->string('menggunakan_boiler')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {

        });
    }
};
