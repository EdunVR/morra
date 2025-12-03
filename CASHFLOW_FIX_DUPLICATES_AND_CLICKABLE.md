# ✅ Fix Duplikasi & Pembelian Aset Tetap Clickable - Laporan Arus Kas

## Masalah yang Ditemukan

### 1. **Duplikasi di Metode Langsung**

Di **Laporan Arus Kas - Metode Langsung**, terjadi **duplikasi nilai** pada:

-   **Pendapatan** (Revenue accounts)
-   **Gaji & Tunjangan Karyawan** (Salary & Employee Benefits)

### 2. **Pembelian Aset Tetap Tidak Clickable**

Di **B. Arus Kas dari Aktivitas Investasi**, item "Pembelian Aset Tetap" tidak bisa diklik untuk melihat detail.

---

## Solusi yang Diterapkan

### 1. **Fix Double Counting di buildAccountHierarchy()**

**File:** `app/Http/Controllers/CashFlowController.php`

**Root Cause:**

```php
// BEFORE (SALAH):
$totalAmount = $amount + $childrenTotal;
```

**Masalah:**

-   Parent account punya `$amount` sendiri (misal: Pendapatan = 100jt)
-   Children accounts juga punya amount (misal: Pendapatan Penjualan = 80jt, Pendapatan Jasa = 20jt)
-   Total dihitung: `$amount + $childrenTotal` = 100jt + (80jt + 20jt) = **200jt** ❌ (DOUBLE!)

**Solusi:**

```php
// AFTER (BENAR):
// FIX DOUBLE COUNTING:
// If account has children: use ONLY children total (avoid double counting)
// If no children: use account's own amount
$totalAmount = count($childrenData) > 0 ? $childrenTotal : $amount;
```

---

### 2. **Pembelian Aset Tetap Clickable**

#### A. **Backend: Ensure account_id Always Set**

**Perubahan di `calculateInvestingCashFlow()`:**

```php
if ($assetPurchases > 0) {
    $items[] = [
        'account_id' => $fixedAssetAccount ? $fixedAssetAccount->id : 'fixed_asset_purchase', // ✅ Always set
        'name' => 'Pembelian Aset Tetap',
        // ...
    ];
}
```

#### B. **Backend: New Endpoint for Fixed Asset Purchases**

**New Method:** `getFixedAssetPurchases()`

-   Get fixed assets purchased in period
-   Format as transactions
-   Return with summary

#### C. **Route: Add New Endpoint**

```php
Route::get('cashflow/fixed-asset-purchases',
    [CashFlowController::class, 'getFixedAssetPurchases'])
    ->name('cashflow.fixed-asset-purchases');
```

#### D. **Frontend: Detect & Use Special Endpoint**

```javascript
async showAccountTransactions(accountId, accountCode, accountName) {
    // Check if this is "Pembelian Aset Tetap" - use special endpoint
    let url;
    if (accountName && accountName.toLowerCase().includes('pembelian aset tetap')) {
        url = `{{ route('finance.cashflow.fixed-asset-purchases') }}?${params}`;
    } else {
        url = `{{ route('finance.cashflow.account-details', '') }}/${accountId}?${params}`;
    }
    // ...
}
```

---

## Testing Guide

### 1. **Test Duplikasi Fixed:**

```bash
✓ Clear cache: php artisan route:clear; php artisan config:clear; php artisan view:clear
✓ Hard refresh browser (Ctrl+F5)
✓ Buka Laporan Arus Kas → Metode: Langsung
✓ Verify: Pendapatan tidak double
✓ Verify: Gaji & Tunjangan tidak double
```

### 2. **Test Pembelian Aset Tetap Clickable:**

```bash
✓ Scroll ke "B. Arus Kas dari Aktivitas Investasi"
✓ Lihat "Pembelian Aset Tetap" (text biru, clickable)
✓ Klik → Modal terbuka
✓ Verify: List fixed assets yang dibeli
✓ Verify: Detail lengkap ditampilkan
```

---

## Files Modified

1. **app/Http/Controllers/CashFlowController.php**

    - `buildAccountHierarchy()` - Fix double counting
    - `calculateInvestingCashFlow()` - Ensure account_id
    - `getFixedAssetPurchases()` - NEW endpoint

2. **routes/web.php**

    - Added route: `cashflow/fixed-asset-purchases`

3. **resources/views/admin/finance/cashflow/index.blade.php**
    - Modified: `showAccountTransactions()` - Detect & use special endpoint

---

## Summary

✅ **Fixed duplikasi Pendapatan**
✅ **Fixed duplikasi Gaji & Tunjangan**
✅ **Pembelian Aset Tetap CLICKABLE**
✅ **Modal detail fixed assets**
✅ **Calculations accurate**
