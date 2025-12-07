<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chart_of_accounts';
    
    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'balance',
        'outlet_id',
        'parent_id',
        'level',
        'description',
        'status',
        'is_system_account',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_system_account' => 'boolean',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function journalEntryDetails()
    {
        return $this->hasMany(JournalEntryDetail::class, 'account_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'all') {
            return $query->where('outlet_id', $outletId);
        }
        return $query;
    }

    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type', $type);
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeParentAccounts($query)
    {
        return $query->whereNull('parent_id');
    }
}
