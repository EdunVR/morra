<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing table
        Schema::dropIfExists('setting');
        
        // Recreate with correct structure
        Schema::create('setting', function (Blueprint $table) {
            $table->integer('id_setting', false, true)->length(10)->autoIncrement();
            $table->string('nama_perusahaan', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 255)->nullable();
            $table->tinyInteger('tipe_nota')->default(0);
            $table->smallInteger('diskon')->default(0);
            $table->string('path_logo', 255)->nullable();
            $table->string('path_kartu_member', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        // Restore old structure
        Schema::dropIfExists('setting');
        
        Schema::create('setting', function (Blueprint $table) {
            $table->bigIncrements('id_setting');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
