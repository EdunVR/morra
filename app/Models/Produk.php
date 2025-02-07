<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];

    public function produkTipe()
    {
        return $this->hasMany(ProdukTipe::class, 'id_produk');
    }

    public function tipe()
    {
        return $this->hasOne(ProdukTipe::class, 'id_produk', 'id_produk');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function hppProduk()
    {
        return $this->hasMany(HppProduk::class, 'id_produk', 'id_produk');
    }
}
