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
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'realisasi_id')) {
                $table->unsignedBigInteger('realisasi_id')->nullable()->after('rab_id');
                $table->foreign('realisasi_id')->references('id')->on('rab_realisasi_history')->onDelete('set null');
                $table->index('realisasi_id');
            }
            
            if (!Schema::hasColumn('expenses', 'is_auto_generated')) {
                $table->boolean('is_auto_generated')->default(false)->after('realisasi_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'realisasi_id')) {
                $table->dropForeign(['realisasi_id']);
                $table->dropIndex(['realisasi_id']);
                $table->dropColumn('realisasi_id');
            }
            
            if (Schema::hasColumn('expenses', 'is_auto_generated')) {
                $table->dropColumn('is_auto_generated');
            }
        });
    }
};
