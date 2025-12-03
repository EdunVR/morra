<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->bigIncrements('id_outlet');
            $table->string('kode_outlet', 50)->unique();
            $table->string('nama_outlet');
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
