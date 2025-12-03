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
        Schema::create('fixed_asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fixed_asset_id');

            // Period Information
            $table->integer('period'); // sequential period number
            $table->date('depreciation_date');

            // Depreciation Values
            $table->decimal('amount', 15, 2);
            $table->decimal('accumulated_depreciation', 15, 2);
            $table->decimal('book_value', 15, 2);

            // Journal Integration
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');

            // Metadata
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('fixed_asset_id')->references('id')->on('fixed_assets')->onDelete('cascade');

            // Indexes
            $table->unique(['fixed_asset_id', 'period']);
            $table->index(['depreciation_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_depreciations');
    }
};
