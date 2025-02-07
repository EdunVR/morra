<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'id_member';
    protected $guarded = [];
    protected $fillable = ['nama', 'telepon', 'alamat', 'id_tipe'];

    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe', 'id_tipe');
    }
}
