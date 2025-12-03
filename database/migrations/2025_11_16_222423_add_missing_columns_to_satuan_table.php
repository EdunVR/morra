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
        Schema::table('satuan', function (Blueprint $table) {
            $table->string('kode_satuan', 50)->unique()->after('id_satuan');
            $table->string('simbol', 10)->nullable()->after('nama_satuan');
            $table->text('deskripsi')->nullable()->after('simbol');
            $table->boolean('is_active')->default(true)->after('deskripsi');
            $table->decimal('nilai_konversi', 10, 6)->nullable()->after('is_active');
            $table->unsignedBigInteger('satuan_utama_id')->nullable()->after('nilai_konversi');
            
            $table->foreign('satuan_utama_id')->references('id_satuan')->on('satuan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('satuan', function (Blueprint $table) {
            $table->dropForeign(['satuan_utama_id']);
            $table->dropColumn([
                'kode_satuan',
                'simbol',
                'deskripsi',
                'is_active',
                'nilai_konversi',
                'satuan_utama_id'
            ]);
        });
    }
};
