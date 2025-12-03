<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_pengiriman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_permintaan', 50)->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('id_outlet_asal');
            $table->unsignedBigInteger('id_outlet_tujuan');
            $table->string('status', 50)->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->foreign('id_outlet_asal')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('id_outlet_tujuan')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_pengiriman');
    }
};
