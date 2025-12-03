<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_price_produk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_customer_price');
            $table->unsignedBigInteger('id_produk');
            $table->decimal('harga_khusus', 15, 2);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_customer_price')
                  ->references('id_customer_price')
                  ->on('customer_price')
                  ->onDelete('cascade');
                  
            $table->foreign('id_produk')
                  ->references('id_produk')
                  ->on('produk')
                  ->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['id_customer_price', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_price_produk');
    }
};
