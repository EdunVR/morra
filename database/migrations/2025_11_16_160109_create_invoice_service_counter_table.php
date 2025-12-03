<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_service_counter', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prefix', 10);
            $table->integer('last_number')->default(0);
            $table->integer('year');
            $table->integer('month');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_service_counter');
    }
};
