<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $table = 'piutang';
    protected $primaryKey = 'id_piutang';
    
    protected $fillable = [
        'id_penjualan',
        'tanggal_tempo',
        'nama',
        'piutang',
        'id_member',
        'id_outlet',
        'jumlah_piutang',
        'jumlah_dibayar',
        'sisa_piutang',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'jumlah_piutang' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
        'tanggal_tempo' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }
}
