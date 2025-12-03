<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartLog extends Model
{
    use HasFactory;

    protected $table = 'sparepart_logs';
    protected $primaryKey = 'id_log';
    
    protected $fillable = [
        'id_sparepart',
        'id_user',
        'tipe_perubahan',
        'nilai_lama',
        'nilai_baru',
        'selisih',
        'keterangan'
    ];

    protected $casts = [
        'nilai_lama' => 'integer',
        'nilai_baru' => 'integer',
        'selisih' => 'integer'
    ];

    // Relationship ke sparepart
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'id_sparepart', 'id_sparepart');
    }

    // Relationship ke user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user', 'id');
    }

    // Scope untuk filter tipe perubahan
    public function scopeStok($query)
    {
        return $query->where('tipe_perubahan', 'stok');
    }

    public function scopeHarga($query)
    {
        return $query->where('tipe_perubahan', 'harga');
    }
}