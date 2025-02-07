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
        Schema::create('produksi_detail', function (Blueprint $table) {
            $table->increments('id_produksi_detail');
            $table->integer('id_produksi')->unsigned();
            $table->integer('id_bahan')->unsigned();
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_detail');
    }
};
