<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_price', function (Blueprint $table) {
            $table->string('customer_type')->nullable();
            $table->string('customer_id')->nullable();
            $table->unsignedBigInteger('id_ongkir')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_price', function (Blueprint $table) {

        });
    }
};
