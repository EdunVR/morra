<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rab_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_rab');
            $table->string('item');
            $table->text('deskripsi')->nullable();
            $table->decimal('qty', 10,2);
            $table->string('satuan', 50);
            $table->decimal('harga', 15,2);
            $table->decimal('subtotal', 15,2);
            $table->timestamps();
            $table->foreign('id_rab')->references('id_rab')->on('rab_template')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_detail');
    }
};
