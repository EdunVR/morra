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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->enum('category', ['land', 'building', 'vehicle', 'equipment', 'furniture', 'computer']);
            $table->string('location', 255)->nullable();

            // Acquisition Information
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 15, 2);
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->integer('useful_life'); // in years

            // Depreciation Configuration
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'double_declining', 'units_of_production'])->default('straight_line');

            // Account Mapping
            $table->unsignedBigInteger('asset_account_id');
            $table->unsignedBigInteger('depreciation_expense_account_id');
            $table->unsignedBigInteger('accumulated_depreciation_account_id');
            $table->unsignedBigInteger('payment_account_id');

            // Current Status
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('book_value', 15, 2);
            $table->enum('status', ['active', 'inactive', 'sold', 'disposed'])->default('active');

            // Disposal Information
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 15, 2)->nullable();
            $table->text('disposal_notes')->nullable();

            // Metadata
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['outlet_id', 'status']);
            $table->index('category');
            $table->index('acquisition_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
