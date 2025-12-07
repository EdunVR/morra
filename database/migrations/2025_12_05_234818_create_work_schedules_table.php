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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_id')->constrained('recruitments')->onDelete('cascade');
            $table->time('clock_in')->default('08:00:00');
            $table->time('clock_out')->default('17:00:00');
            $table->timestamps();
            
            // Unique constraint untuk memastikan satu karyawan hanya punya satu jadwal
            $table->unique('recruitment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
