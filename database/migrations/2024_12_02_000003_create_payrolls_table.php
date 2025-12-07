<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('recruitment_id');
            $table->string('period'); // Format: YYYY-MM
            $table->date('payment_date');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_pay', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('allowance', 15, 2)->default(0);
            $table->decimal('deduction', 15, 2)->default(0);
            $table->decimal('late_penalty', 15, 2)->default(0);
            $table->decimal('absent_penalty', 15, 2)->default(0);
            $table->decimal('loan_deduction', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['recruitment_id', 'period']);
        });
        
        // Add foreign keys only if referenced tables exist
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasTable('outlets')) {
                $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('set null');
            }
            if (Schema::hasTable('recruitments')) {
                $table->foreign('recruitment_id')->references('id')->on('recruitments')->onDelete('cascade');
            }
            if (Schema::hasTable('users')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
