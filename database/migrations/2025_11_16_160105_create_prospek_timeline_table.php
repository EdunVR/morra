<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospek_timeline', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_prospek');
            $table->datetime('tanggal');
            $table->string('aktivitas');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
            $table->foreign('id_prospek')->references('id_prospek')->on('prospek')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospek_timeline');
    }
};
