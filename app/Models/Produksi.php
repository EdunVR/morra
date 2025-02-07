<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProduksiDetail;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';
    protected $primaryKey = 'id_produksi';
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function detail()
{
    return $this->hasMany(ProduksiDetail::class, 'id_produksi', 'id_produksi');
}
}
