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
        Schema::create('hutang', function (Blueprint $table) {
            $table->increments('id_hutang');
            $table->integer('id_supplier');
            $table->string('nama'); 
            $table->integer('hutang'); // Jumlah hutang
            $table->enum('status', ['belum_lunas', 'lunas']); // Status hutang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang');
    }
};
