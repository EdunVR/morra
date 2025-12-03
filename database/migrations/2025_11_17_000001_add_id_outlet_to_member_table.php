<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member', function (Blueprint $table) {
            if (!Schema::hasColumn('member', 'id_outlet')) {
                $table->unsignedBigInteger('id_outlet')->nullable()->after('id_member');
                $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            if (Schema::hasColumn('member', 'id_outlet')) {
                $table->dropForeign(['id_outlet']);
                $table->dropColumn('id_outlet');
            }
        });
    }
};
