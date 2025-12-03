<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reg_regencies', function (Blueprint $table) {
            $table->string('id', 4)->primary();
            $table->string('province_id', 2);
            $table->string('name');
            $table->foreign('province_id')->references('id')->on('reg_provinces')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_regencies');
    }
};
