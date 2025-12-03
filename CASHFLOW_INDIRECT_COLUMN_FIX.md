# Fix: Column 'depreciation_amount' Not Found - Indirect Method

## Error yang Terjadi

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'depreciation_amount' in 'field list'
SQL: select sum(`depreciation_amount`) as aggregate from `fixed_asset_depreciations`
```

## Penyebab

Kode menggunakan nama kolom yang salah: `depreciation_amount`

Nama kolom yang benar di tabel `fixed_asset_depreciations` adalah: `amount`

## Struktur Tabel Actual

Dari migration `2025_11_16_155741_create_fixed_asset_depreciations_table.php`:

```php
Schema::create('fixed_asset_depreciations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('fixed_asset_id');
    $table->integer('period');
    $table->date('depreciation_date');

    // Depreciation Values
    $table->decimal('amount', 15, 2);  // ← Ini kolom yang benar
    $table->decimal('accumulated_depreciation', 15, 2);
    $table->decimal('book_value', 15, 2);

    $table->unsignedBigInteger('journal_entry_id')->nullable();
    $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
    // ...
});
```

## Solusi

### Before (Salah):

```php
$depreciation = \App\Models\FixedAssetDepreciation::whereHas('fixedAsset', function($q) use ($outletId) {
        $q->where('outlet_id', $outletId);
    })
    ->whereBetween('depreciation_date', [$startDate, $endDate])
    ->sum('depreciation_amount'); // ← Kolom tidak ada
```

### After (Benar):

```php
$depreciation = \App\Models\FixedAssetDepreciation::whereHas('fixedAsset', function($q) use ($outletId) {
        $q->where('outlet_id', $outletId);
    })
    ->whereBetween('depreciation_date', [$startDate, $endDate])
    ->where('status', 'posted') // ← Tambahan: hanya yang sudah diposting
    ->sum('amount'); // ← Kolom yang benar
```

## Perbaikan Tambahan

Menambahkan filter `where('status', 'posted')` untuk memastikan hanya penyusutan yang sudah diposting yang dihitung.

## File yang Dimodifikasi

**app/Http/Controllers/CashFlowController.php**

-   Method: `calculateOperatingCashFlowIndirect()`
-   Line: ~169
-   Change: `depreciation_amount` → `amount`
-   Added: `where('status', 'posted')`

## Testing

### Test Case 1: Metode Tidak Langsung Tanpa Data Penyusutan

```
Input:
- Outlet: 1
- Periode: 2025-10-31 to 2025-11-23
- Method: indirect
- No depreciation data

Expected:
- No error
- Depreciation adjustment = 0
- Laba bersih + adjustments = Kas operasi

Result: ✅ PASS
```

### Test Case 2: Metode Tidak Langsung Dengan Data Penyusutan

```
Input:
- Outlet: 1
- Periode: dengan data penyusutan
- Method: indirect
- Has posted depreciation

Expected:
- No error
- Depreciation adjustment > 0
- Shown in adjustments list

Result: ✅ PASS (after fix)
```

### Test Case 3: Switch Between Methods

```
Input:
- Switch from direct to indirect
- Switch from indirect to direct

Expected:
- No error on both switches
- Data loads correctly

Result: ✅ PASS
```

## Verification Query

Untuk verify data penyusutan:

```sql
SELECT
    fad.id,
    fa.name as asset_name,
    fad.depreciation_date,
    fad.amount,  -- Kolom yang benar
    fad.status
FROM fixed_asset_depreciations fad
JOIN fixed_assets fa ON fad.fixed_asset_id = fa.id
WHERE fa.outlet_id = 1
  AND fad.depreciation_date BETWEEN '2025-10-31' AND '2025-11-23'
  AND fad.status = 'posted';
```

## Status

✅ **FIXED** - Column name corrected from `depreciation_amount` to `amount`

## Related

-   Table: `fixed_asset_depreciations`
-   Migration: `2025_11_16_155741_create_fixed_asset_depreciations_table.php`
-   Model: `App\Models\FixedAssetDepreciation`
-   Controller: `App\Http\Controllers\CashFlowController`
-   Method: `calculateOperatingCashFlowIndirect()`

## Notes

-   Tidak ada perubahan database
-   Hanya menyesuaikan nama kolom di query
-   Menambahkan filter status untuk data yang valid
-   Metode indirect sekarang berfungsi dengan benar
