<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';
    
    protected $fillable = [
        'reference_number',
        'description',
        'transaction_date',
        'outlet_id',
        'source_type',
        'source_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id');
    }
}
