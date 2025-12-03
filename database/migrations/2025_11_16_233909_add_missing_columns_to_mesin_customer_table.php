<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesin_customer', function (Blueprint $table) {
            $table->string('closing_type')->nullable();
            $table->unsignedBigInteger('id_ongkir')->nullable();
            $table->string('biaya_service')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mesin_customer', function (Blueprint $table) {

        });
    }
};
