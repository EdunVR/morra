<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPengiriman extends Model
{
    use HasFactory;

    protected $table = 'permintaan_pengiriman';
    protected $primaryKey = 'id_permintaan';
    protected $fillable = [
        'id_outlet_asal',
        'id_outlet_tujuan',
        'id_produk',
        'id_bahan',
        'id_inventori',
        'jumlah',
        'status',
    ];

    public function outletAsal()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet_asal');
    }

    public function outletTujuan()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet_tujuan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'id_inventori');
    }
}
