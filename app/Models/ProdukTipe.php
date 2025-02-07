<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukTipe extends Model
{
    protected $table = 'produk_tipe';
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe');
    }
}
