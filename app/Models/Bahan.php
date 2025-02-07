<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';
    protected $primaryKey = 'id_bahan';
    protected $guarded = [];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function hargaBahan()
    {
        return $this->hasMany(BahanDetail::class, 'id_bahan', 'id_bahan');
    }
}
