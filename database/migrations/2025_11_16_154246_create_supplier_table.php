<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->bigIncrements('id_supplier');
            $table->string('nama');
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('bank', 100)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('atas_nama')->nullable();
            $table->timestamps();
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
