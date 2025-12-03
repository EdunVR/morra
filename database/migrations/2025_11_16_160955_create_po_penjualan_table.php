<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_penjualan', function (Blueprint $table) {
            $table->bigIncrements('id_po');
            $table->string('no_po', 50)->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('id_member')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->decimal('total', 15,2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('set null');
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_penjualan');
    }
};
