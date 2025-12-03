<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rab_template', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_template', 'outlet_id')) {
                $table->unsignedBigInteger('outlet_id')->nullable()->after('id_rab');
                $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('set null');
            }
            if (!Schema::hasColumn('rab_template', 'book_id')) {
                $table->unsignedBigInteger('book_id')->nullable()->after('outlet_id');
                $table->foreign('book_id')->references('id')->on('accounting_books')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rab_template', function (Blueprint $table) {
            if (Schema::hasColumn('rab_template', 'outlet_id')) {
                $table->dropForeign(['outlet_id']);
                $table->dropColumn('outlet_id');
            }
            if (Schema::hasColumn('rab_template', 'book_id')) {
                $table->dropForeign(['book_id']);
                $table->dropColumn('book_id');
            }
        });
    }
};
