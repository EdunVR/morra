<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HppProduk extends Model
{
    use HasFactory;

    protected $table = 'hpp_produk';
    protected $primaryKey = 'id_hpp';
    protected $guarded = [];


    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
