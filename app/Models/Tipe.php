<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tipe extends Model
{
    use HasFactory;

    protected $table = 'tipe';
    protected $primaryKey = 'id_tipe';
    protected $guarded = [];

    public function produkTipe()
    {
        return $this->hasMany(ProdukTipe::class, 'id_tipe');
    }
}
