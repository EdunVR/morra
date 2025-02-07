<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piutang extends Model
{
    use HasFactory;

    protected $table = 'piutang';
    protected $primaryKey = 'id_piutang';
    protected $guarded = [];
}
