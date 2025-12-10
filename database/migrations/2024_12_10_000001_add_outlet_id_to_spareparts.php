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
        Schema::table('spareparts', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->after('id_sparepart')->nullable();
            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->index('outlet_id');
        });

        // Update existing spareparts to have default outlet (outlet pertama)
        $defaultOutletId = DB::table('outlets')->orderBy('id_outlet')->value('id_outlet');
        if ($defaultOutletId) {
            DB::table('spareparts')->whereNull('outlet_id')->update(['outlet_id' => $defaultOutletId]);
        }

        // Make outlet_id required after setting default values
        Schema::table('spareparts', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropIndex(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
    }
};
