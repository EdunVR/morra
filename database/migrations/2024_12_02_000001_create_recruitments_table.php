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
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('department')->nullable();
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->json('jobdesk')->nullable();
            $table->string('fingerprint_id')->nullable();
            $table->boolean('is_registered_fingerprint')->default(false);
            $table->decimal('salary', 15, 2)->nullable();
            $table->decimal('hourly_rate', 15, 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('join_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
