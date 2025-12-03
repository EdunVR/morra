<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_price', function (Blueprint $table) {
            // Ubah id_member menjadi nullable karena sekarang menggunakan customer_type + customer_id
            if (Schema::hasColumn('customer_price', 'id_member')) {
                $table->unsignedBigInteger('id_member')->nullable()->change();
            }
            
            // Ubah id_produk menjadi nullable juga karena sekarang menggunakan pivot table
            if (Schema::hasColumn('customer_price', 'id_produk')) {
                $table->unsignedBigInteger('id_produk')->nullable()->change();
            }
            
            // Ubah harga_khusus menjadi nullable
            if (Schema::hasColumn('customer_price', 'harga_khusus')) {
                $table->decimal('harga_khusus', 15, 2)->nullable()->change();
            }
            
            // Ubah primary key jika masih menggunakan 'id'
            if (Schema::hasColumn('customer_price', 'id') && !Schema::hasColumn('customer_price', 'id_customer_price')) {
                $table->renameColumn('id', 'id_customer_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_price', function (Blueprint $table) {
            // Kembalikan ke NOT NULL jika rollback
            if (Schema::hasColumn('customer_price', 'id_member')) {
                $table->unsignedBigInteger('id_member')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('customer_price', 'id_produk')) {
                $table->unsignedBigInteger('id_produk')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('customer_price', 'harga_khusus')) {
                $table->decimal('harga_khusus', 15, 2)->nullable(false)->change();
            }
            
            if (Schema::hasColumn('customer_price', 'id_customer_price')) {
                $table->renameColumn('id_customer_price', 'id');
            }
        });
    }
};
