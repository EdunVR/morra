<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel utama produksi
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_code')->unique();
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedBigInteger('product_id');
            $table->string('production_line', 50);
            $table->integer('target_quantity');
            $table->integer('realized_quantity')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'approved', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            $table->decimal('max_reject_rate', 5, 2)->default(3.00);
            $table->decimal('min_efficiency', 5, 2)->default(85.00);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('outlet_id')->references('id_outlet')->on('outlets')->onDelete('cascade');
            $table->foreign('product_id')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['outlet_id', 'status']);
            $table->index('production_code');
        });

        // Tabel material yang dibutuhkan untuk produksi
        Schema::create('production_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id');
            $table->unsignedBigInteger('material_id'); // bisa dari tabel bahan atau produk
            $table->string('material_type', 20); // 'bahan' atau 'produk'
            $table->decimal('quantity_required', 15, 2);
            $table->decimal('quantity_used', 15, 2)->default(0);
            $table->string('unit', 20);
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            
            $table->index('production_id');
        });

        // Tabel log realisasi produksi
        Schema::create('production_realizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id');
            $table->integer('quantity_produced');
            $table->integer('quantity_rejected')->default(0);
            $table->date('realization_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('production_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_realizations');
        Schema::dropIfExists('production_materials');
        Schema::dropIfExists('productions');
    }
};
