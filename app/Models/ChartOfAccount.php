<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';
    
    protected $fillable = [
        'outlet_id',
        'code',
        'name',
        'type',
        'category',
        'description',
        'balance',
        'parent_id',
        'level',
        'status',
        'is_system_account'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_system_account' => 'boolean',
    ];

    // Relasi ke outlet
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    // Relasi ke akun induk
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // Relasi ke akun anak
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function scopeSearchAccounts($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Scope untuk filter berdasarkan outlet
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    // Scope untuk filter berdasarkan tipe
    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type', $type);
        }
        return $query;
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    // Scope untuk akun parent saja (level 1)
    public function scopeParentAccounts($query)
    {
        return $query->where('level', 1)->orWhereNull('parent_id');
    }

    // Method untuk mendapatkan nama tipe dalam bahasa Indonesia
    public function getTypeNameAttribute(): string
    {
        $types = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban',
            'otherrevenue' => 'Pendapatan Lain',
            'otherexpense' => 'Beban Lain'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Method untuk mendapatkan class badge berdasarkan tipe
    public function getTypeBadgeClassAttribute(): string
    {
        $classes = [
            'asset' => 'bg-blue-100 text-blue-800',
            'liability' => 'bg-red-100 text-red-800',
            'equity' => 'bg-purple-100 text-purple-800',
            'revenue' => 'bg-green-100 text-green-800',
            'expense' => 'bg-orange-100 text-orange-800',
            'otherrevenue' => 'bg-green-100 text-green-800',
            'otherexpense' => 'bg-orange-100 text-orange-800'
        ];

        return $classes[$this->type] ?? 'bg-gray-100 text-gray-800';
    }

    public static function generateAccountCode($outletId, $parentId = null, $type = 'asset', $category = ''): string
    {
        if ($parentId) {
            // Generate kode untuk child account (level 2+) dengan grouping kategori
            $parent = self::find($parentId);
            if (!$parent) {
                throw new \Exception('Akun induk tidak ditemukan');
            }
            
            if ($category) {
                // Cari child terakhir dengan kategori yang sama di parent yang sama
                $lastChild = self::where('parent_id', $parentId)
                    ->where('category', $category)
                    ->orderBy('code', 'desc')
                    ->first();
                
                if ($lastChild) {
                    // Extract number dari kode child (format: 1000.01, 1000.02)
                    $parts = explode('.', $lastChild->code);
                    if (count($parts) > 1) {
                        $lastNumber = intval($parts[1]);
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1;
                    }
                } else {
                    $newNumber = 1;
                }
            } else {
                // Jika tidak ada kategori, gunakan logic sequential biasa
                $lastChild = self::where('parent_id', $parentId)
                    ->where(function($query) {
                        $query->whereNull('category')->orWhere('category', '');
                    })
                    ->orderBy('code', 'desc')
                    ->first();
                
                if ($lastChild) {
                    $parts = explode('.', $lastChild->code);
                    if (count($parts) > 1) {
                        $lastNumber = intval($parts[1]);
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1;
                    }
                } else {
                    $newNumber = 1;
                }
            }
            
            return $parent->code . '.' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        } else {
            // Generate kode untuk parent account (level 1)
            if ($category) {
                // Cari base code berdasarkan kategori yang sama
                $baseAccount = self::where('outlet_id', $outletId)
                    ->whereNull('parent_id')
                    ->where('type', $type)
                    ->where('category', $category)
                    ->orderBy('code', 'desc')
                    ->first();
                
                if ($baseAccount) {
                    // Extract base code dan increment 2 digit terakhir
                    $baseCode = intval(substr($baseAccount->code, 0, 2) . '00');
                    $lastTwoDigits = intval(substr($baseAccount->code, -2));
                    
                    $newLastTwoDigits = $lastTwoDigits + 1;
                    
                    if ($newLastTwoDigits > 99) {
                        $newNumber = self::findNextAvailableBaseCode($outletId, $type, $category);
                    } else {
                        $newNumber = $baseCode + $newLastTwoDigits;
                    }
                } else {
                    $newNumber = self::findNextAvailableBaseCode($outletId, $type, $category);
                }
            } else {
                $lastParent = self::where('outlet_id', $outletId)
                    ->whereNull('parent_id')
                    ->where('type', $type)
                    ->orderBy('code', 'desc')
                    ->first();
                
                if ($lastParent) {
                    $lastCode = $lastParent->code;
                    $lastCode = explode('.', $lastCode)[0]; // Hapus bagian decimal jika ada
                    $lastNumber = intval($lastCode);
                    
                    // Cari next available number (bisa +1 atau +100 tergantung gap)
                    $newNumber = self::findNextAvailableNumber($outletId, $type, $lastNumber);
                } else {
                    $newNumber = self::getTypeStartNumber($type);
                }
            }
            
            // Validasi range
            $typeRange = self::getTypeRange($type);
            if ($newNumber > $typeRange['max']) {
                $newNumber = self::findAvailableGap($outletId, $type, $typeRange['min'], $typeRange['max']);
            }
            
            return str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
    }

    private static function findNextAvailableNumber($outletId, $type, $lastNumber): int
{
    // Ambil semua kode yang sudah ada (tanpa peduli kategori)
    $existingCodes = self::where('outlet_id', $outletId)
        ->whereNull('parent_id')
        ->where('type', $type)
        ->orderBy('code')
        ->pluck('code')
        ->map(function($code) {
            return intval(explode('.', $code)[0]);
        })
        ->toArray();
    
    // Coba increment +100 dulu (untuk maintain pattern)
    $candidate1 = $lastNumber + 100;
    if (!in_array($candidate1, $existingCodes)) {
        return $candidate1;
    }
    
    // Jika +100 sudah digunakan, coba +1
    $candidate2 = $lastNumber + 1;
    if (!in_array($candidate2, $existingCodes)) {
        return $candidate2;
    }
    
    // Jika kedua-duanya sudah digunakan, cari gap yang available
    for ($i = $lastNumber + 1; $i <= $lastNumber + 1000; $i++) {
        if (!in_array($i, $existingCodes)) {
            return $i;
        }
    }
    
    // Fallback: return last number + 100
    return $lastNumber + 100;
}

// Method findAvailableGap juga perlu diupdate
private static function findAvailableGap($outletId, $type, $min, $max): int
{
    $existingCodes = self::where('outlet_id', $outletId)
        ->whereNull('parent_id')
        ->where('type', $type)
        ->orderBy('code')
        ->pluck('code')
        ->map(function($code) {
            return intval(explode('.', $code)[0]);
        })
        ->toArray();
    
    // Cari gap yang tersedia (sequential, tidak harus kelipatan 100)
    for ($i = $min; $i <= $max; $i++) {
        if (!in_array($i, $existingCodes)) {
            return $i;
        }
    }
    
    return $max;
}

    private static function findNextAvailableBaseCode($outletId, $type, $category): int
{
    // Ambil semua kode dengan type yang sama (tanpa peduli kategori)
    $allExistingCodes = self::where('outlet_id', $outletId)
        ->whereNull('parent_id')
        ->where('type', $type)
        ->orderBy('code')
        ->pluck('code')
        ->map(function($code) {
            return intval(explode('.', $code)[0]);
        })
        ->toArray();
    
    // Ambil kode dengan kategori yang sama
    $categoryCodes = self::where('outlet_id', $outletId)
        ->whereNull('parent_id')
        ->where('type', $type)
        ->where('category', $category)
        ->orderBy('code')
        ->pluck('code')
        ->map(function($code) {
            return intval(explode('.', $code)[0]);
        })
        ->toArray();
    
    // Jika belum ada akun dengan kategori ini
    if (empty($categoryCodes)) {
        // Cari base code yang belum digunakan sama sekali
        $typeStart = self::getTypeStartNumber($type);
        
        for ($base = $typeStart; $base <= $typeStart + 9900; $base += 100) {
            // Cek apakah base code ini available (tidak ada yang menggunakan range ini)
            $rangeUsed = false;
            foreach ($allExistingCodes as $existingCode) {
                if ($existingCode >= $base && $existingCode < $base + 100) {
                    $rangeUsed = true;
                    break;
                }
            }
            
            if (!$rangeUsed) {
                return $base;
            }
        }
        
        // Jika semua range penuh, cari angka yang available
        for ($i = $typeStart; $i <= $typeStart + 9999; $i++) {
            if (!in_array($i, $allExistingCodes)) {
                return $i;
            }
        }
        
        return $typeStart;
    }
    
    // Jika sudah ada akun dengan kategori ini, lanjutkan dari base code terakhir
    $lastCategoryCode = end($categoryCodes);
    $lastBaseCode = intval(substr(str_pad($lastCategoryCode, 4, '0', STR_PAD_LEFT), 0, 2) . '00');
    
    // Cek apakah masih ada slot di base code terakhir
    $usedSlotsInBase = array_filter($categoryCodes, function($code) use ($lastBaseCode) {
        return $code >= $lastBaseCode && $code < $lastBaseCode + 100;
    });
    
    if (count($usedSlotsInBase) < 100) {
        // Cari slot kosong di base code ini
        for ($i = 0; $i <= 99; $i++) {
            $candidate = $lastBaseCode + $i;
            if (!in_array($candidate, $categoryCodes) && !in_array($candidate, $allExistingCodes)) {
                return $candidate;
            }
        }
    }
    
    // Jika base code penuh, cari base code baru yang available
    $typeRange = self::getTypeRange($type);
    for ($base = $typeRange['min']; $base <= $typeRange['max']; $base += 100) {
        $rangeUsed = false;
        foreach ($allExistingCodes as $existingCode) {
            if ($existingCode >= $base && $existingCode < $base + 100) {
                $rangeUsed = true;
                break;
            }
        }
        
        if (!$rangeUsed) {
            return $base;
        }
    }
    
    // Fallback: cari angka sequential yang available
    for ($i = $typeRange['min']; $i <= $typeRange['max']; $i++) {
        if (!in_array($i, $allExistingCodes)) {
            return $i;
        }
    }
    
    return $typeRange['max'];
}

    private static function getTypeRange($type): array
    {
        $ranges = [
            'asset' => ['min' => 1000, 'max' => 1999],
            'liability' => ['min' => 2000, 'max' => 2999],
            'equity' => ['min' => 3000, 'max' => 3999],
            'revenue' => ['min' => 4000, 'max' => 4999],
            'expense' => ['min' => 5000, 'max' => 5999],
            'otherrevenue' => ['min' => 6000, 'max' => 6999],
            'otherexpense' => ['min' => 7000, 'max' => 7999],
        ];
        return $ranges[$type] ?? ['min' => 9000, 'max' => 9999];
    }

    private static function getTypeStartNumber($type): int
    {
        $startNumbers = [
            'asset' => 1000,      // 1000, 1100, 1200, ...
            'liability' => 2000,  // 2000, 2100, 2200, ...
            'equity' => 3000,     // 3000, 3100, 3200, ...
            'revenue' => 4000,    // 4000, 4100, 4200, ...
            'expense' => 5000,    // 5000, 5100, 5200, ...
            'otherrevenue' => 6000,    // 6000, 6100, 6200, ...
            'otherexpense' => 7000,    // 7000, 7100, 7200, ...
        ];
        return $startNumbers[$type] ?? 9000;
    }

    private static function getTypePrefix($type): string
    {
        $prefixes = [
            'asset' => '1',
            'liability' => '2', 
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5',
            'otherrevenue' => '6',
            'otherexpense' => '7',
        ];
        return $prefixes[$type] ?? '9';
    }

    // Method untuk mengecek apakah akun memiliki transaksi
    public function hasTransactions(): bool
    {
        // Anda perlu menyesuaikan dengan model JournalEntry atau Transaction yang sesuai
        return false; // Temporary
    }

    public function updateParentBalance(): void
    {
        if ($this->parent_id) {
            $parent = $this->parent;
            if ($parent) {
                $totalChildrenBalance = $parent->children()->sum('balance');
                $parent->balance = $totalChildrenBalance;
                $parent->save();
                
                // Recursive update ke parent yang lebih tinggi
                $parent->updateParentBalance();
            }
        }
    }

    // Method untuk mendapatkan saldo yang sudah termasuk children (readonly)
    // public function getAccumulatedBalanceAttribute(): float
    // {
    //     if ($this->children()->exists()) {
    //         return $this->children()->sum('balance');
    //     }
    //     return $this->balance;
    // }

    // Method untuk update saldo dengan auto-update parent
    public function updateBalance($amount): bool
    {
        $this->balance += $amount;
        $saved = $this->save();
        
        if ($saved) {
            $this->updateParentBalance();
        }
        
        return $saved;
    }

    // Override save method untuk auto-update parent balance
    public function save(array $options = [])
    {
        $saved = parent::save($options);
        
        if ($saved && $this->parent_id) {
            $this->updateParentBalance();
        }
        
        return $saved;
    }

    // Scope untuk menghitung statistik dengan saldo akumulasi
    public function scopeWithAccumulatedBalance($query)
    {
        return $query->selectRaw('
            chart_of_accounts.*,
            CASE 
                WHEN EXISTS (SELECT 1 FROM chart_of_accounts AS children WHERE children.parent_id = chart_of_accounts.id)
                THEN (SELECT SUM(balance) FROM chart_of_accounts AS children WHERE children.parent_id = chart_of_accounts.id)
                ELSE chart_of_accounts.balance
            END as accumulated_balance
        ');
    }

    public function getAccumulatedBalanceAttribute(): float
    {
        // Jika akun memiliki children, hitung saldo akumulasi
        if ($this->children && $this->children->count() > 0) {
            return $this->calculateAccumulatedBalanceWithChildren($this);
        }
        
        // Jika akun tanpa children, gunakan saldo dari jurnal
        return $this->calculateBalanceFromJournals();
    }

    /**
     * Calculate balance from journal entries
     */
    private function calculateBalanceFromJournals(): float
    {
        $balance = JournalEntryDetail::whereHas('journalEntry', function($query) {
                $query->where('outlet_id', $this->outlet_id)
                    ->where('status', 'posted');
            })
            ->where('account_id', $this->id)
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance');

        return floatval($balance ?? 0);
    }

    /**
     * Calculate accumulated balance including children recursively
     */
    private function calculateAccumulatedBalanceWithChildren($account): float
    {
        $balance = $this->calculateBalanceFromJournalsForAccount($account->id);
        
        if ($account->children && $account->children->count() > 0) {
            foreach ($account->children as $child) {
                $balance += $this->calculateAccumulatedBalanceWithChildren($child);
            }
        }
        
        return $balance;
    }

    /**
     * Calculate balance from journals for specific account
     */
    private function calculateBalanceFromJournalsForAccount($accountId): float
    {
        $balance = JournalEntryDetail::whereHas('journalEntry', function($query) {
                $query->where('outlet_id', $this->outlet_id)
                    ->where('status', 'posted');
            })
            ->where('account_id', $accountId)
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance');

        return floatval($balance ?? 0);
    }
}