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
        Schema::create('setting', function (Blueprint $table) {
            $table->increments('id_setting');
            $table->string('nama_perusahaan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->tinyInteger('tipe_nota')->default(0);
            $table->string('path_logo')->nullable();
            $table->string('path_kartu_member')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
