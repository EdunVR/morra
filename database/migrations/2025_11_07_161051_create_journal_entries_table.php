<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id'); // tanpa foreign constraint
            $table->unsignedBigInteger('outlet_id'); // tanpa foreign constraint
            $table->string('transaction_number', 100);
            $table->date('transaction_date');
            $table->text('description');
            $table->enum('status', ['draft', 'posted', 'void'])->default('draft');
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('reference_type')->nullable(); // invoice, payment, etc
            $table->string('reference_number')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['book_id', 'transaction_number']);
            $table->index(['outlet_id', 'transaction_date']);
            $table->index(['book_id', 'status']);
            $table->index('reference_type');
            $table->index('reference_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
};
