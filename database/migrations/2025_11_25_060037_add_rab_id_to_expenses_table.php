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
            if (!Schema::hasColumn('expenses', 'rab_id')) {
                $table->unsignedBigInteger('rab_id')->nullable()->after('outlet_id');
                $table->foreign('rab_id')->references('id_rab')->on('rab_template')->onDelete('set null');
                $table->index('rab_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['rab_id']);
            $table->dropIndex(['rab_id']);
            $table->dropColumn('rab_id');
        });
    }
};
