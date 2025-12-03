<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id'); // tanpa foreign constraint
            $table->string('code', 50);
            $table->string('name');
            $table->enum('type', ['general', 'cash', 'bank', 'sales', 'purchase', 'inventory', 'payroll']);
            $table->text('description')->nullable();
            $table->enum('currency', ['IDR', 'USD', 'EUR', 'SGD'])->default('IDR');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->integer('total_entries')->default(0);
            $table->enum('status', ['active', 'inactive', 'draft', 'closed'])->default('draft');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['outlet_id', 'code']);
            $table->index(['outlet_id', 'type']);
            $table->index(['outlet_id', 'status']);
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_books');
    }
};
