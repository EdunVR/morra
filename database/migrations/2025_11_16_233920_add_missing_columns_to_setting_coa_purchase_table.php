<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_coa_purchase', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->string('akun_hutang_sementara')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('setting_coa_purchase', function (Blueprint $table) {

        });
    }
};
