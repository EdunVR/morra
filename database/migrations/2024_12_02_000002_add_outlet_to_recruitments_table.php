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
        Schema::table('recruitments', function (Blueprint $table) {
            // Only add column if it doesn't exist
            if (!Schema::hasColumn('recruitments', 'outlet_id')) {
                $table->unsignedBigInteger('outlet_id')->nullable()->after('id');
            }
        });
        
        // Only add foreign key if outlets table exists
        if (Schema::hasTable('outlets')) {
            Schema::table('recruitments', function (Blueprint $table) {
                $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
    }
};
