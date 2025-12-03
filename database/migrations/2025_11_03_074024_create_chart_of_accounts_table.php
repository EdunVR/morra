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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();

            // Hilangkan foreign constraint tapi tetap simpan kolom relasi
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('category')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->integer('level')->default(1);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_system_account')->default(false);
            $table->timestamps();

            // Tetap gunakan index agar query cepat
            $table->index(['outlet_id', 'type']);
            $table->index(['outlet_id', 'status']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
