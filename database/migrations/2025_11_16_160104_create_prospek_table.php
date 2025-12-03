<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospek', function (Blueprint $table) {
            $table->bigIncrements('id_prospek');
            $table->string('nama');
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('perusahaan')->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->string('sumber', 100)->nullable();
            $table->string('status', 50)->default('new');
            $table->decimal('nilai_estimasi', 15,2)->nullable();
            $table->date('tanggal_follow_up')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospek');
    }
};
