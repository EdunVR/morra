# Margin Report - Relation Fix

## Issue

```
Error: Call to undefined method App\Models\PenjualanDetail::penjualan()
```

## Root Cause

Model `PenjualanDetail` tidak memiliki relasi `penjualan()` yang dibutuhkan oleh `MarginReportController` untuk mengakses data penjualan (outlet, member, dll).

## Solution

### File Modified: `app/Models/PenjualanDetail.php`

Added missing relation:

```php
public function penjualan()
{
    return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
}
```

## Relations Verified

### PenjualanDetail Model

-   ✅ `penjualan()` - belongsTo Penjualan (ADDED)
-   ✅ `produk()` - hasOne Produk
-   ✅ `outlet()` - belongsTo Outlet

### Penjualan Model

-   ✅ `outlet()` - belongsTo Outlet
-   ✅ `member()` - hasOne Member
-   ✅ `user()` - hasOne User
-   ✅ `details()` - hasMany PenjualanDetail
-   ✅ `piutang()` - hasOne Piutang

### PosSaleItem Model

-   ✅ `posSale()` - belongsTo PosSale
-   ✅ `produk()` - belongsTo Produk

### PosSale Model

-   ✅ `outlet()` - belongsTo Outlet
-   ✅ `member()` - belongsTo Member
-   ✅ `user()` - belongsTo User
-   ✅ `items()` - hasMany PosSaleItem
-   ✅ `penjualan()` - belongsTo Penjualan
-   ✅ `piutang()` - hasOne Piutang

## Usage in MarginReportController

```php
// Now this works correctly:
$invoiceDetails = PenjualanDetail::with(['produk', 'penjualan.outlet', 'penjualan.member'])
    ->whereHas('penjualan', function($q) use ($outletId, $startDate, $endDate) {
        // Filter logic
    })
    ->get();

// Access related data:
$detail->penjualan->outlet->nama_outlet
$detail->penjualan->member->nama
$detail->penjualan->created_at
```

## Testing

After fix, test:

1. ✅ Access `/admin/penjualan/laporan-margin`
2. ✅ Page loads without error
3. ✅ Data displays correctly
4. ✅ Outlet names show correctly
5. ✅ Dates show correctly
6. ✅ Export PDF works

## Status

✅ **FIXED** - Relation added successfully

---

**Date:** December 1, 2024
