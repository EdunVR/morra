<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $primaryKey = 'id_image';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}