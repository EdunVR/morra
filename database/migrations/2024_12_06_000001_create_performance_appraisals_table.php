<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_appraisals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedBigInteger('recruitment_id');
            $table->string('employee_name');
            $table->string('period'); // Format: YYYY-MM or YYYY-Q1, etc
            $table->date('appraisal_date');
            
            // Performance Parameters (1-100 scale)
            $table->integer('discipline_score')->default(0);
            $table->integer('teamwork_score')->default(0);
            $table->integer('work_result_score')->default(0);
            $table->integer('initiative_score')->default(0);
            $table->integer('kpi_score')->default(0);
            
            // Calculated
            $table->decimal('total_score', 5, 2)->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->string('grade')->nullable(); // A, B, C, D, E
            
            // Notes
            $table->text('evaluator_notes')->nullable();
            $table->text('employee_notes')->nullable();
            $table->text('improvement_plan')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'final'])->default('draft');
            
            // Evaluator
            $table->unsignedBigInteger('evaluator_id');
            $table->timestamp('evaluated_at')->nullable();
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
            $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['outlet_id', 'period']);
            $table->index(['recruitment_id', 'period']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_appraisals');
    }
};
