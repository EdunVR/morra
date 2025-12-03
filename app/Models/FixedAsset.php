<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedAsset extends Model
{
    protected $fillable = [
        'outlet_id',
        'book_id',
        'code',
        'name',
        'category',
        'location',
        'acquisition_date',
        'acquisition_cost',
        'salvage_value',
        'useful_life',
        'depreciation_method',
        'asset_account_id',
        'depreciation_expense_account_id',
        'accumulated_depreciation_account_id',
        'payment_account_id',
        'accumulated_depreciation',
        'book_value',
        'status',
        'disposal_date',
        'disposal_value',
        'disposal_notes',
        'description',
        'created_by'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'disposal_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'disposal_value' => 'decimal:2',
    ];

    // Relationships
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(AccountingBook::class, 'book_id');
    }

    public function assetAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'asset_account_id');
    }

    public function depreciationExpenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function accumulatedDepreciationAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account_id');
    }

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(FixedAssetDepreciation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Methods
    public function calculateMonthlyDepreciation(): float
    {
        if ($this->book_value <= $this->salvage_value) {
            return 0;
        }

        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;

        switch ($this->depreciation_method) {
            case 'straight_line':
                $monthlyDepreciation = $depreciableAmount / $this->useful_life / 12;
                break;

            case 'declining_balance':
                $rate = 1.5 / $this->useful_life;
                $monthlyDepreciation = $this->book_value * $rate / 12;
                break;

            case 'double_declining':
                $rate = 2 / $this->useful_life;
                $monthlyDepreciation = $this->book_value * $rate / 12;
                break;

            default:
                $monthlyDepreciation = $depreciableAmount / $this->useful_life / 12;
        }

        // Ensure depreciation doesn't exceed depreciable amount
        $remainingDepreciable = $this->book_value - $this->salvage_value;
        return min($monthlyDepreciation, $remainingDepreciable);
    }

    public function calculateRemainingLife(): float
    {
        if ($this->accumulated_depreciation == 0) {
            return $this->useful_life;
        }

        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;
        $depreciationRate = $this->accumulated_depreciation / $depreciableAmount;
        
        return max(0, $this->useful_life * (1 - $depreciationRate));
    }

    public function canBeDeleted(): bool
    {
        // Cannot delete if has posted journal entries
        $hasPostedDepreciation = $this->depreciations()
            ->where('status', 'posted')
            ->exists();

        return !$hasPostedDepreciation;
    }

    public function updateBookValue(): void
    {
        $this->book_value = $this->acquisition_cost - $this->accumulated_depreciation;
        $this->save();
    }

    public static function generateCode($outletId): string
    {
        $date = now()->format('Ym');
        $prefix = "AST-{$date}-";
        
        $lastAsset = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}