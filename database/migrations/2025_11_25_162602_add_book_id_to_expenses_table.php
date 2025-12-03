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
            if (!Schema::hasColumn('expenses', 'book_id')) {
                $table->unsignedBigInteger('book_id')->nullable()->after('outlet_id');
                $table->foreign('book_id')->references('id')->on('accounting_books')->onDelete('set null');
                $table->index('book_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'book_id')) {
                $table->dropForeign(['book_id']);
                $table->dropIndex(['book_id']);
                $table->dropColumn('book_id');
            }
        });
    }
};
