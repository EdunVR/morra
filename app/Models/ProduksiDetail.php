<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProduksiDetail extends Model
{
    use HasFactory;

    protected $table = 'produksi_detail';
    protected $primaryKey = 'id_produksi_detail';
    protected $guarded = [];

    public function bahan()
    {
        return $this->hasOne(Bahan::class, 'id_bahan', 'id_bahan');
    }
}
