<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'journal_entries';
    
    protected $fillable = [
        'book_id',
        'outlet_id',
        'transaction_number',
        'transaction_date',
        'description',
        'status',
        'total_debit',
        'total_credit',
        'notes',
        'reference_type',
        'reference_number',
        'posted_at'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime'
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(AccountingBook::class, 'book_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function journalEntryDetails(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id');
    }

    // Method untuk generate transaction number
    public static function generateTransactionNumber($bookId): string
    {
        $book = AccountingBook::find($bookId);
        $prefix = 'JNL';
        $bookCode = $book ? substr($book->code, 0, 3) : '000';
        
        $lastEntry = self::where('book_id', $bookId)
            ->orderBy('transaction_number', 'desc')
            ->first();
        
        if ($lastEntry) {
            $lastNumber = intval(substr($lastEntry->transaction_number, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . $bookCode . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Method untuk post journal entry
    public function post(): bool
    {
        if ($this->status === 'draft' && $this->total_debit === $this->total_credit) {
            $this->status = 'posted';
            $this->posted_at = now();
            return $this->save();
        }
        return false;
    }

    // Method untuk void journal entry
    public function void(): bool
    {
        $this->status = 'void';
        return $this->save();
    }

    public function scopeByReference($query, $referenceType, $referenceId)
    {
        $referenceNumber = 'REF-' . str_pad($referenceId, 6, '0', STR_PAD_LEFT);
        return $query->where('reference_type', $referenceType)
                    ->where('reference_number', $referenceNumber);
    }

    /**
     * Scope untuk outlet tertentu
     */
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }
}