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
        Schema::table('attendances', function (Blueprint $table) {
            // Add early_minutes column after late_minutes
            $table->integer('early_minutes')->default(0)->after('late_minutes');
            
            // Add hours_worked column after overtime_hours (if not exists)
            if (!Schema::hasColumn('attendances', 'hours_worked')) {
                $table->decimal('hours_worked', 5, 2)->default(0)->after('overtime_hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['early_minutes', 'hours_worked']);
        });
    }
};
