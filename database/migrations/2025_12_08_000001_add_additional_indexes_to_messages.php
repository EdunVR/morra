<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add additional indexes for optimizing common query patterns:
     * - created_at for ordering messages
     * - mode + created_at for filtering by mode with ordering
     * - sender_id + mode for chatbot message queries
     * - outlet_id for outlet-specific queries
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Index for ordering messages by creation time (used in pagination)
            $table->index('created_at', 'messages_created_at_index');
            
            // Composite index for mode filtering with ordering
            $table->index(['mode', 'created_at'], 'messages_mode_created_at_index');
            
            // Index for sender queries in specific mode (useful for chatbot queries)
            $table->index(['sender_id', 'mode'], 'messages_sender_mode_index');
            
            // Index for outlet-specific queries
            $table->index('outlet_id', 'messages_outlet_id_index');
            
            // Composite index for unread messages by receiver and mode
            $table->index(['receiver_id', 'is_read', 'mode'], 'messages_receiver_read_mode_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_created_at_index');
            $table->dropIndex('messages_mode_created_at_index');
            $table->dropIndex('messages_sender_mode_index');
            $table->dropIndex('messages_outlet_id_index');
            $table->dropIndex('messages_receiver_read_mode_index');
        });
    }
};
