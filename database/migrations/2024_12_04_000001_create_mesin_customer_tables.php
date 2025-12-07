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
        // Create or update mesin_customer table
        if (!Schema::hasTable('mesin_customer')) {
            Schema::create('mesin_customer', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_member');
                $table->unsignedBigInteger('id_ongkir')->nullable();
                $table->string('kode_mesin')->unique();
                $table->string('nama_mesin');
                $table->string('closing_type')->nullable();
                $table->decimal('biaya_service', 15, 2)->nullable();
                $table->timestamps();

                $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
                $table->foreign('id_ongkir')->references('id_ongkir')->on('ongkos_kirim')->onDelete('set null');
            });
        } else {
            // Update existing table if needed
            Schema::table('mesin_customer', function (Blueprint $table) {
                // Check and add columns if they don't exist
                if (!Schema::hasColumn('mesin_customer', 'nama_mesin')) {
                    $table->string('nama_mesin')->after('kode_mesin');
                }
                if (!Schema::hasColumn('mesin_customer', 'closing_type')) {
                    $table->string('closing_type')->nullable()->after('nama_mesin');
                }
                if (!Schema::hasColumn('mesin_customer', 'biaya_service')) {
                    $table->decimal('biaya_service', 15, 2)->nullable()->after('closing_type');
                }
            });
        }

        // Create pivot table mesin_customer_produk
        if (!Schema::hasTable('mesin_customer_produk')) {
            Schema::create('mesin_customer_produk', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_mesin_customer');
                $table->unsignedBigInteger('id_produk');
                $table->integer('jumlah')->default(1);
                $table->decimal('biaya_service', 15, 2)->default(0);
                $table->string('closing_type')->default('jual_putus');
                $table->timestamps();

                $table->foreign('id_mesin_customer')->references('id')->on('mesin_customer')->onDelete('cascade');
                $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
                
                $table->unique(['id_mesin_customer', 'id_produk']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesin_customer_produk');
        Schema::dropIfExists('mesin_customer');
    }
};
