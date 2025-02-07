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
        Schema::create('piutang', function (Blueprint $table) {
            $table->increments('id_piutang');
            $table->integer('id_member');
            $table->string('nama'); 
            $table->integer('piutang'); // Jumlah hutang
            $table->enum('status', ['belum_lunas', 'lunas']); // Status hutang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang');
    }
};
