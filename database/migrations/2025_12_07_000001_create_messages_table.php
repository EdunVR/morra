<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('mode', ['superadmin', 'chatbot'])->default('superadmin');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['sender_id', 'receiver_id', 'mode']);
            $table->index(['receiver_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
