<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontra_bon', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_kontra_bon', 50)->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('id_supplier');
            $table->unsignedBigInteger('id_outlet');
            $table->decimal('total', 15,2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->foreign('id_supplier')->references('id_supplier')->on('supplier')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontra_bon');
    }
};
