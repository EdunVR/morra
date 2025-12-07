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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedBigInteger('recruitment_id');
            $table->string('employee_name'); // Denormalized for performance
            $table->string('fingerprint_id')->nullable();
            $table->date('date');
            
            // Time fields
            $table->time('clock_in')->nullable();
            $table->time('break_out')->nullable();
            $table->time('break_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->time('overtime_in')->nullable();
            $table->time('overtime_out')->nullable();
            
            // Calculated fields
            $table->enum('status', ['present', 'late', 'absent', 'leave', 'sick', 'permission'])->default('present');
            $table->decimal('work_hours', 5, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->integer('late_minutes')->default(0);
            
            // Additional info
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['outlet_id', 'date']);
            $table->index(['recruitment_id', 'date']);
            $table->index('fingerprint_id');
            $table->index('date');
            $table->index('status');
            
            // Unique constraint: one attendance per employee per day
            $table->unique(['recruitment_id', 'date']);
            
            // Foreign keys
            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
