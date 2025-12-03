<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('harga_bahan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_bahan');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('stok', 15, 2);
            $table->timestamps();
            
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_bahan');
    }
};
