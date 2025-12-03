<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSale extends Model
{
    use HasFactory;

    protected $table = 'pos_sales';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'id_outlet',
        'id_member',
        'id_user',
        'subtotal',
        'diskon_persen',
        'diskon_nominal',
        'total_diskon',
        'ppn',
        'total',
        'jenis_pembayaran',
        'jumlah_bayar',
        'kembalian',
        'status',
        'catatan',
        'is_bon',
        'id_penjualan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'diskon_nominal' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'ppn' => 'decimal:2',
        'total' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'is_bon' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(PosSaleItem::class, 'pos_sale_id');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan', 'id_penjualan');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'pos');
    }

    /**
     * Generate nomor transaksi POS
     */
    public static function generateTransactionNumber($outletId)
    {
        $date = now();
        $prefix = 'POS';
        $month = $date->format('m');
        $year = $date->format('Y');
        
        // Get last number for this month
        $lastSale = static::where('id_outlet', $outletId)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastSale ? (intval(substr($lastSale->no_transaksi, 0, 4)) + 1) : 1;
        
        return sprintf('%04d/POS/%s/%s', $sequence, $month, $year);
    }

    /**
     * Scope untuk filter outlet
     */
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'all') {
            return $query->where('id_outlet', $outletId);
        }
        return $query;
    }

    /**
     * Scope untuk filter tanggal (inclusive)
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereDate('tanggal', '>=', $startDate)
                        ->whereDate('tanggal', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope untuk filter status
     */
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }
}
