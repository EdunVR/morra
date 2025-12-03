<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontra_bon_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_kontra_bon');
            $table->unsignedBigInteger('id_bahan');
            $table->decimal('qty', 10,2);
            $table->decimal('harga', 15,2);
            $table->decimal('subtotal', 15,2);
            $table->timestamps();
            $table->foreign('id_kontra_bon')->references('id')->on('kontra_bon')->onDelete('cascade');
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontra_bon_detail');
    }
};
