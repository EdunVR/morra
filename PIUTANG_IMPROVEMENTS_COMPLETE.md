# Piutang Improvements - Complete

## Overview

Tiga perbaikan penting untuk halaman Piutang agar lebih user-friendly dan akurat.

---

## Fix #1: Date Range Filter (Inclusive)

### Problem

Filter tanggal tidak include tanggal mulai dan tanggal akhir. User harus:

-   Kurangi 1 hari pada tanggal mulai
-   Tambah 1 hari pada tanggal akhir

Contoh: Untuk melihat piutang tanggal 1-31 Desember, user harus input:

-   Tanggal Mulai: 30 November ❌
-   Tanggal Akhir: 1 Januari ❌

### Root Cause

`whereBetween()` di MySQL bersifat inclusive, tapi ada masalah dengan timezone atau format datetime.

### Solution

Gunakan `whereDate()` dengan operator `>=` dan `<=` untuk memastikan inclusive:

**Before:**

```php
$query->whereBetween('si.tanggal', [$startDate, $endDate]);
$posQuery->whereBetween('ps.tanggal', [$startDate, $endDate]);
```

**After:**

```php
$query->whereDate('si.tanggal', '>=', $startDate)
      ->whereDate('si.tanggal', '<=', $endDate);

$posQuery->whereDate('ps.tanggal', '>=', $startDate)
         ->whereDate('ps.tanggal', '<=', $endDate);
```

### Benefits

-   ✅ Tanggal mulai dan akhir included
-   ✅ User tidak perlu adjust tanggal
-   ✅ Lebih intuitif
-   ✅ Konsisten dengan ekspektasi user

### SQL Generated

```sql
-- Before
WHERE tanggal BETWEEN '2025-12-01' AND '2025-12-31'

-- After
WHERE DATE(tanggal) >= '2025-12-01'
  AND DATE(tanggal) <= '2025-12-31'
```

### Testing

**Input:**

-   Tanggal Mulai: 1 Desember 2025
-   Tanggal Akhir: 31 Desember 2025

**Expected:**

-   ✅ Piutang tanggal 1 Desember muncul
-   ✅ Piutang tanggal 31 Desember muncul
-   ✅ Piutang tanggal 15 Desember muncul
-   ❌ Piutang tanggal 30 November tidak muncul
-   ❌ Piutang tanggal 1 Januari tidak muncul

---

## Fix #2: POS Nota in Modal

### Problem

Saat klik nomor invoice POS, nota dibuka di tab baru. Tidak konsisten dengan invoice yang dibuka di modal.

### Solution

Update fungsi `showInvoicePreview()` untuk membuka POS nota di modal yang sama dengan invoice.

**Before:**

```javascript
showInvoicePreview(piutang) {
  if (piutang.source === 'pos') {
    // Open POS nota in new tab
    const url = this.routes.posNotaPrint.replace(':id', piutang.id_piutang) + '?type=besar';
    window.open(url, '_blank');  // ❌ New tab
  } else {
    this.showInvoicePDF(piutang.id_piutang, piutang.id_penjualan);
  }
}
```

**After:**

```javascript
showInvoicePreview(piutang) {
  if (piutang.source === 'pos') {
    // Show POS nota in modal
    const url = this.routes.posNotaPrint.replace(':id', piutang.id_piutang) + '?type=besar';
    this.printPdfUrl = url;
    this.showPrintModal = true;  // ✅ Modal
  } else {
    this.showInvoicePDF(piutang.id_piutang, piutang.id_penjualan);
  }
}
```

### Benefits

-   ✅ Konsisten dengan invoice
-   ✅ User tidak perlu switch tab
-   ✅ Preview langsung di modal
-   ✅ Bisa tutup dengan mudah

### Modal Reuse

Modal "Invoice Penjualan" yang sudah ada digunakan untuk:

-   Invoice PDF (existing)
-   POS Nota (new)

**Modal Features:**

-   Header: "Invoice Penjualan"
-   Body: Iframe dengan preview
-   Footer: Tombol "Tutup"

### User Flow

```
1. User klik nomor invoice POS
   ↓
2. Modal "Invoice Penjualan" muncul
   ↓
3. Nota POS tampil di iframe
   ↓
4. User bisa lihat detail
   ↓
5. User klik "Tutup" atau backdrop
   ↓
6. Kembali ke list piutang
```

---

## Fix #3: Show Lunas Piutang (History)

### Problem

Piutang yang sudah lunas tidak ditampilkan di tabel. User tidak bisa melihat history pembayaran.

### Root Cause

Query POS filter `status = 'menunggu'`:

```php
->where('ps.status', 'menunggu')  // ❌ Exclude lunas
```

### Solution

Hapus filter status agar semua piutang (lunas & belum lunas) ditampilkan:

**Before:**

```php
$posQuery = DB::table('pos_sales as ps')
    // ...
    ->where('ps.is_bon', true)
    ->where('ps.status', 'menunggu');  // ❌ Only menunggu
```

**After:**

```php
$posQuery = DB::table('pos_sales as ps')
    // ...
    ->where('ps.is_bon', true);
    // Removed status filter to show all (menunggu & lunas)  ✅
```

### Benefits

-   ✅ History piutang tersimpan
-   ✅ User bisa tracking pembayaran
-   ✅ Audit trail lengkap
-   ✅ Laporan lebih akurat

### Filter Status Still Works

User masih bisa filter by status menggunakan dropdown:

```php
if ($status === 'belum_lunas') {
    $posQuery->where('pt.status', 'belum_lunas');
} elseif ($status === 'lunas') {
    $posQuery->where('pt.status', 'lunas');
}
```

### Display

**Piutang Lunas:**

-   Badge: "Lunas" (hijau)
-   Tombol Bayar: Hidden
-   Sisa Piutang: Rp 0

**Piutang Belum Lunas:**

-   Badge: "Belum Lunas" (orange) atau "Jatuh Tempo" (merah)
-   Tombol Bayar: Visible
-   Sisa Piutang: > Rp 0

---

## Files Modified

### 1. `app/Http/Controllers/FinanceAccountantController.php`

**Method:** `getPiutangData()`

**Changes:**

1. Changed `whereBetween()` to `whereDate()` with `>=` and `<=`
2. Removed `->where('ps.status', 'menunggu')` filter
3. Now shows all POS piutang (lunas & belum lunas)

### 2. `resources/views/admin/finance/piutang/index.blade.php`

**Function:** `showInvoicePreview()`

**Changes:**

1. Changed POS nota from `window.open()` to modal
2. Reuse existing print modal
3. Set `printPdfUrl` and `showPrintModal = true`

---

## Testing Checklist

### Date Range Filter

-   [ ] Set tanggal mulai: 1 Des 2025
-   [ ] Set tanggal akhir: 31 Des 2025
-   [ ] Piutang tanggal 1 Des muncul ✅
-   [ ] Piutang tanggal 31 Des muncul ✅
-   [ ] Piutang tanggal 30 Nov tidak muncul ✅
-   [ ] Piutang tanggal 1 Jan tidak muncul ✅

### POS Nota Modal

-   [ ] Klik nomor invoice POS
-   [ ] Modal muncul (bukan tab baru) ✅
-   [ ] Nota POS tampil di iframe ✅
-   [ ] Klik "Tutup" → Modal tutup ✅
-   [ ] Klik backdrop → Modal tutup ✅

### Lunas History

-   [ ] Filter status: "Semua Status"
-   [ ] Piutang lunas muncul ✅
-   [ ] Badge "Lunas" tampil ✅
-   [ ] Tombol bayar hidden ✅
-   [ ] Sisa piutang = Rp 0 ✅

### Filter Status

-   [ ] Filter: "Belum Lunas" → Hanya belum lunas
-   [ ] Filter: "Lunas" → Hanya lunas
-   [ ] Filter: "Semua Status" → Semua muncul

---

## SQL Comparison

### Before (Exclusive)

```sql
-- Invoice
WHERE tanggal BETWEEN '2025-12-01' AND '2025-12-31'

-- POS
WHERE is_bon = 1
  AND status = 'menunggu'
  AND tanggal BETWEEN '2025-12-01' AND '2025-12-31'
```

**Issues:**

-   Date might exclude boundaries
-   Only shows 'menunggu' status
-   No history for lunas

### After (Inclusive)

```sql
-- Invoice
WHERE DATE(tanggal) >= '2025-12-01'
  AND DATE(tanggal) <= '2025-12-31'

-- POS
WHERE is_bon = 1
  AND DATE(tanggal) >= '2025-12-01'
  AND DATE(tanggal) <= '2025-12-31'
```

**Benefits:**

-   ✅ Date boundaries included
-   ✅ Shows all status (filtered by dropdown)
-   ✅ Complete history

---

## User Experience Improvements

### Before

1. **Date Filter:**

    - User: "Kenapa piutang tanggal 1 Des tidak muncul?"
    - User harus adjust tanggal ❌

2. **POS Nota:**

    - Klik → Tab baru
    - User harus switch tab ❌
    - Tidak konsisten dengan invoice ❌

3. **Lunas History:**
    - Piutang lunas hilang
    - Tidak ada history ❌
    - Tidak bisa tracking ❌

### After

1. **Date Filter:**

    - Input tanggal 1-31 Des
    - Semua piutang di range muncul ✅
    - Intuitif ✅

2. **POS Nota:**

    - Klik → Modal muncul
    - Preview langsung ✅
    - Konsisten dengan invoice ✅

3. **Lunas History:**
    - Piutang lunas tetap tampil
    - History lengkap ✅
    - Bisa tracking pembayaran ✅

---

## Cache Cleared

```bash
php artisan config:clear
php artisan view:clear
```

## Status

✅ **COMPLETE** - All three improvements implemented

**Changes:**

1. ✅ Date range filter now inclusive
2. ✅ POS nota opens in modal
3. ✅ Lunas piutang shown in history

**Ready for Testing:**

-   Test date range with exact dates
-   Test POS nota modal
-   Test filter status with lunas
-   Verify history is complete

---

**Document Version:** 1.0  
**Last Updated:** December 1, 2025  
**Author:** Development Team
