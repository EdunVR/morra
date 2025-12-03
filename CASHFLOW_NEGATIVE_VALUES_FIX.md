# ✅ Fix Tampilan Nilai Negatif & Verifikasi Penyusutan - Laporan Arus Kas

## Masalah yang Diperbaiki

### 1. **Nilai Negatif Tidak Ditampilkan dengan Benar**

-   Pembayaran kas (expense) seharusnya negatif (cash outflow)
-   Tapi ditampilkan sebagai positif
-   Seharusnya ditampilkan dengan tanda kurung: `(Rp 50.000.000)`

### 2. **Verifikasi: Penyusutan Tidak Mempengaruhi Pembelian Aset Tetap**

-   Penyusutan = non-cash expense
-   Pembelian aset tetap = cash outflow
-   Keduanya harus terpisah dan tidak saling mempengaruhi

---

## Solusi yang Diterapkan

### 1. **Fix Backend Calculation**

**File:** `app/Http/Controllers/CashFlowController.php`

#### A. **Remove Double Negation for Expenses**

**BEFORE (SALAH):**

```php
// Line 133
'amount' => -$cashPayments,  // ❌ Double negation!

// Line 144
$total = $cashReceipts - $cashPayments;  // ❌ Wrong formula!
```

**Penjelasan Masalah:**

1. `$cashPayments` dihitung dengan `SUM(credit - debit)` untuk expense
2. Expense di debit, jadi `credit - debit` = **negatif** (benar!)
3. Tapi kemudian di-negate lagi: `-$cashPayments` = **positif** (salah!)
4. Total: `$cashReceipts - $cashPayments` = salah karena $cashPayments sudah negatif

**AFTER (BENAR):**

```php
// Line 133 - Keep negative (cash outflow)
'amount' => $cashPayments,  // ✅ Keep negative!

// Line 144 - Add instead of subtract (because $cashPayments is negative)
$total = $cashReceipts + $cashPayments;  // ✅ Correct formula!
```

**Logic:**

```
Revenue (cash inflow):  credit - debit = +100jt (positive)
Expense (cash outflow): credit - debit = -50jt (negative)
Total: 100jt + (-50jt) = 50jt ✅
```

---

### 2. **Fix Frontend Display - Parentheses for Negative Values**

**File:** `resources/views/admin/finance/cashflow/index.blade.php`

**BEFORE:**

```javascript
formatCurrency(amount) {
    const absAmount = Math.abs(amount);
    const sign = amount >= 0 ? '' : '-';
    return sign + new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(absAmount);
}
// Output: -Rp 50.000.000 ❌
```

**AFTER:**

```javascript
formatCurrency(amount) {
    const absAmount = Math.abs(amount);
    const formatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(absAmount);

    // Show negative values in parentheses (accounting format)
    return amount < 0 ? `(${formatted})` : formatted;
}
// Output: (Rp 50.000.000) ✅
```

**Accounting Format:**

-   Positive: `Rp 100.000.000`
-   Negative: `(Rp 50.000.000)` ← dengan tanda kurung

---

### 3. **Verifikasi: Penyusutan TIDAK Mempengaruhi Pembelian Aset Tetap**

**File:** `app/Http/Controllers/CashFlowController.php` - Method: `calculateInvestingCashFlow()`

**Pembelian Aset Tetap (Line 393-394):**

```php
// Get fixed asset purchases (cash outflows)
$assetPurchases = FixedAsset::where('outlet_id', $outletId)
    ->whereBetween('acquisition_date', [$startDate, $endDate])
    ->when($bookId, function($query) use ($bookId) {
        $query->where('book_id', $bookId);
    })
    ->sum('acquisition_cost');  // ✅ Langsung dari tabel fixed_assets
```

**Penyusutan (Metode Tidak Langsung - Line 167-177):**

```php
// Depreciation (Penyusutan) - Add back non-cash expense
$depreciation = \App\Models\FixedAssetDepreciation::whereHas('fixedAsset', function($q) use ($outletId) {
        $q->where('outlet_id', $outletId);
    })
    ->whereBetween('depreciation_date', [$startDate, $endDate])
    ->where('status', 'posted')
    ->sum('amount');  // ✅ Dari tabel fixed_asset_depreciations
```

**Kesimpulan:**

-   ✅ **Pembelian Aset Tetap** = dari tabel `fixed_assets` (cash outflow)
-   ✅ **Penyusutan** = dari tabel `fixed_asset_depreciations` (non-cash, hanya di metode tidak langsung)
-   ✅ **Keduanya TERPISAH** dan tidak saling mempengaruhi
-   ✅ **Penyusutan TIDAK mengurangi pembelian aset tetap** di arus kas

**Alasan:**

1. **Pembelian aset** = transaksi kas (keluar uang)
2. **Penyusutan** = alokasi biaya (tidak keluar uang)
3. Di **metode langsung**: hanya tampil pembelian aset (cash outflow)
4. Di **metode tidak langsung**: penyusutan di-add back ke laba bersih (karena non-cash)

---

## Contoh Tampilan

### Before (Salah):

```
A. Arus Kas dari Aktivitas Operasi
  Penerimaan Kas dari Pelanggan
    Pendapatan                     Rp 100.000.000  ✅

  Pembayaran Kas kepada Pemasok dan Karyawan
    Gaji & Tunjangan               Rp 50.000.000   ❌ (Seharusnya negatif!)
    Beban Operasional              Rp 30.000.000   ❌ (Seharusnya negatif!)

Kas Bersih Operasi                 Rp 20.000.000   ❌ (Salah!)

B. Arus Kas dari Aktivitas Investasi
  Pembelian Aset Tetap             Rp 25.000.000   ❌ (Seharusnya negatif!)
```

### After (Benar):

```
A. Arus Kas dari Aktivitas Operasi
  Penerimaan Kas dari Pelanggan
    Pendapatan                     Rp 100.000.000  ✅

  Pembayaran Kas kepada Pemasok dan Karyawan
    Gaji & Tunjangan               (Rp 50.000.000) ✅ (Negatif dengan kurung!)
    Beban Operasional              (Rp 30.000.000) ✅ (Negatif dengan kurung!)

Kas Bersih Operasi                 Rp 20.000.000   ✅ (Benar!)

B. Arus Kas dari Aktivitas Investasi
  Pembelian Aset Tetap             (Rp 25.000.000) ✅ (Negatif dengan kurung!)
```

---

## Testing Guide

### 1. **Test Nilai Negatif dengan Kurung:**

```bash
✓ Clear cache: php artisan route:clear; php artisan config:clear; php artisan view:clear
✓ Hard refresh browser (Ctrl+F5)
✓ Buka Laporan Arus Kas → Metode Langsung
✓ Check "Pembayaran Kas kepada Pemasok dan Karyawan"
✓ Verify: Nilai ditampilkan dengan kurung: (Rp xxx)
✓ Verify: Warna merah untuk nilai negatif
✓ Check "Pembelian Aset Tetap" di Aktivitas Investasi
✓ Verify: Nilai ditampilkan dengan kurung: (Rp xxx)
```

### 2. **Test Perhitungan Benar:**

```bash
✓ Hitung manual:
  - Pendapatan: +100jt
  - Gaji: -50jt
  - Beban: -30jt
  - Total: 100 - 50 - 30 = 20jt ✅

✓ Verify di laporan:
  - Kas Bersih Operasi = 20jt ✅
```

### 3. **Test Penyusutan Tidak Mempengaruhi Pembelian:**

```bash
✓ Buat fixed asset baru dengan penyusutan
✓ Check Laporan Arus Kas - Metode Langsung:
  - Pembelian Aset Tetap = nilai acquisition_cost ✅
  - Penyusutan TIDAK muncul (karena non-cash) ✅

✓ Check Laporan Arus Kas - Metode Tidak Langsung:
  - Laba Bersih (sudah dikurangi penyusutan)
  - Penyesuaian: Penyusutan di-add back ✅
  - Pembelian Aset Tetap tetap sama ✅
```

---

## Files Modified

1. **app/Http/Controllers/CashFlowController.php**

    - Line 133: Remove double negation for expenses
    - Line 144: Fix total calculation (add instead of subtract)

2. **resources/views/admin/finance/cashflow/index.blade.php**
    - `formatCurrency()`: Show negative values in parentheses

---

## Summary

✅ **Nilai negatif ditampilkan dengan kurung** (accounting format)
✅ **Perhitungan total benar** (revenue + expenses)
✅ **Penyusutan TIDAK mempengaruhi pembelian aset tetap**
✅ **Pembelian aset = cash outflow** (dari tabel fixed_assets)
✅ **Penyusutan = non-cash** (hanya di metode tidak langsung)

### Key Points:

1. **Accounting Format:**

    - Positive: `Rp 100.000.000`
    - Negative: `(Rp 50.000.000)`

2. **Cash Flow Logic:**

    - Revenue (inflow): positive
    - Expense (outflow): negative
    - Total: revenue + expense (expense sudah negatif)

3. **Fixed Assets:**
    - Purchase: cash outflow (dari tabel fixed_assets)
    - Depreciation: non-cash (dari tabel depreciations, hanya di indirect method)
    - Keduanya terpisah dan tidak saling mempengaruhi

Laporan Arus Kas sekarang menampilkan nilai negatif dengan benar dan perhitungan akurat!
