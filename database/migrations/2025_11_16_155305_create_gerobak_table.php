<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gerobak', function (Blueprint $table) {
            $table->bigIncrements('id_gerobak');
            $table->string('kode_gerobak', 50)->unique();
            $table->string('nama_gerobak');
            $table->unsignedBigInteger('id_outlet');
            $table->string('lokasi')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gerobak');
    }
};
