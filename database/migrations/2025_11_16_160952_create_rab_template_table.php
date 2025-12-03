<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rab_template', function (Blueprint $table) {
            $table->bigIncrements('id_rab');
            $table->string('nama_template');
            $table->text('deskripsi')->nullable();
            $table->decimal('total_biaya', 15,2)->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_template');
    }
};
