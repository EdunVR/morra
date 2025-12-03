<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ongkos_kirim', function (Blueprint $table) {
            $table->bigIncrements('id_ongkir');
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->string('nama_tujuan');
            $table->decimal('biaya', 15,2);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ongkos_kirim');
    }
};
