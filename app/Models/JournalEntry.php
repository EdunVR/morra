<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';
    
    protected $fillable = [
        'book_id',
        'reference_number',
        'description',
        'transaction_date',
        'outlet_id',
        'source_type',
        'source_id',
        'transaction_number',
        'reference_type',
        'status',
        'total_debit',
        'total_credit',
        'posted_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function book()
    {
        return $this->belongsTo(AccountingBook::class, 'book_id');
    }

    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id');
    }

    // Alias for details relationship (for compatibility)
    public function journalEntryDetails()
    {
        return $this->details();
    }
}
