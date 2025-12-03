<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    use HasFactory;

    protected $table = 'hutang';
    protected $primaryKey = 'id_hutang';

    protected $fillable = [
        'tanggal_tempo',
        'id_supplier',
        'id_outlet',
        'id_purchase_invoice',
        'id_pembelian',
        'nama',
        'hutang',
        'jumlah_hutang',
        'jumlah_dibayar',
        'sisa_hutang',
        'tanggal_jatuh_tempo',
        'status'
    ];

    protected $casts = [
        'tanggal_tempo' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'hutang' => 'decimal:2',
        'jumlah_hutang' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_hutang' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'id_purchase_invoice');
    }
}