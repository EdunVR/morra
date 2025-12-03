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
    
    protected $fillable = [
        'id_penjualan',
        'tanggal_tempo',
        'tanggal_jatuh_tempo',
        'nama',
        'piutang',
        'jumlah_piutang',
        'jumlah_dibayar',
        'sisa_piutang',
        'id_member',
        'id_outlet',
        'status',
    ];

    protected $casts = [
        'tanggal_tempo' => 'datetime',
        'tanggal_jatuh_tempo' => 'date',
        'piutang' => 'decimal:2',
        'jumlah_piutang' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    /**
     * Get journal entries related to this piutang
     */
    public function journalEntries()
    {
        return JournalEntry::where('reference_type', 'piutang')
            ->where('reference_number', 'LIKE', '%' . str_pad($this->id_piutang, 6, '0', STR_PAD_LEFT) . '%')
            ->orWhere(function($query) {
                if ($this->id_penjualan) {
                    $query->where('reference_type', 'penjualan')
                          ->where('reference_number', 'LIKE', '%' . str_pad($this->id_penjualan, 6, '0', STR_PAD_LEFT) . '%');
                }
            });
    }

    /**
     * Scope untuk filter berdasarkan outlet
     */
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId) {
            return $query->where('id_outlet', $outletId);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Check if piutang is overdue
     */
    public function isOverdue()
    {
        if ($this->status === 'lunas') {
            return false;
        }
        
        if ($this->tanggal_jatuh_tempo) {
            return now()->gt($this->tanggal_jatuh_tempo);
        }
        
        return false;
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->tanggal_jatuh_tempo);
    }
}
