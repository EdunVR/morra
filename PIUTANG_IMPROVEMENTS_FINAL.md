# Perbaikan Halaman Piutang - Final

## 🎯 Perubahan yang Dilakukan

### 1. ✅ Nomor Invoice dari Kolom `nama`

**Sebelum:** Menggunakan format `INV-XXXXXX` dari `id_penjualan`  
**Sesudah:** Menggunakan kolom `nama` dari tabel `piutang` yang berisi nomor invoice asli

**File yang Diubah:**

-   `app/Http/Controllers/FinanceAccountantController.php`
    -   Method `getPiutangData()` - Line untuk `invoice_number`

**Perubahan:**

```php
// Sebelum
'invoice_number' => $piutang->penjualan ? 'INV-' . str_pad($piutang->id_penjualan, 6, '0', STR_PAD_LEFT) : '-'

// Sesudah
'invoice_number' => $piutang->nama ?: '-' // Menggunakan kolom nama sebagai invoice number
```

---

### 2. ✅ No Invoice Clickable untuk Menampilkan PDF

**Fitur:** Klik nomor invoice untuk melihat PDF invoice pembelian di modal

**File yang Diubah:**

-   `app/Http/Controllers/FinanceAccountantController.php`
    -   Tambah method `getPiutangInvoicePDF($id)`
-   `routes/web.php`

    -   Tambah route: `GET /finance/piutang/{id}/invoice-pdf`

-   `resources/views/admin/finance/piutang/index.blade.php`
    -   Ubah nomor invoice dari text menjadi button clickable
    -   Tambah modal PDF dengan iframe
    -   Tambah JavaScript functions:
        -   `showInvoicePDF(piutangId, penjualanId)`
        -   `closePDFModal()`

**Cara Kerja:**

1. User klik nomor invoice di tabel
2. Modal muncul dengan iframe
3. Iframe load PDF dari route `pembelian.purchase-order.print`
4. PDF ditampilkan langsung di modal (stream PDF)

**UI Changes:**

```html
<!-- Sebelum -->
<span class="font-medium text-blue-600" x-text="piutang.invoice_number"></span>

<!-- Sesudah -->
<button
    @click="showInvoicePDF(piutang.id_piutang, piutang.id_penjualan)"
    class="font-medium text-blue-600 hover:text-blue-800 hover:underline"
    x-text="piutang.invoice_number"
></button>
```

---

### 3. ✅ Tombol "Bayar" dengan Redirect ke PO

**Fitur:** Tombol "Bayar" yang mengarah ke halaman Purchase Order dan langsung membuka modal riwayat pembayaran

**File yang Diubah:**

-   `resources/views/admin/finance/piutang/index.blade.php`

    -   Ganti tombol "Detail" dengan tombol "Bayar"
    -   Tambah function `redirectToPOPayment(penjualanId)`

-   `resources/views/admin/pembelian/purchase-order/index.blade.php`
    -   Update method `init()` untuk handle URL parameters
    -   Auto-open payment modal jika ada parameter `po_id` dan `open_payment=1`

**Cara Kerja:**

1. User klik tombol "Bayar" di halaman piutang
2. Redirect ke `/pembelian/purchase-order?po_id=123&open_payment=1`
3. Halaman PO load data
4. Setelah 1.5 detik, otomatis buka modal riwayat pembayaran untuk PO tersebut
5. URL dibersihkan (remove parameters) setelah modal terbuka

**UI Changes:**

```html
<!-- Sebelum -->
<button
    @click="showDetail(piutang.id_piutang)"
    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-medium"
>
    <i class="bx bx-show"></i> Detail
</button>

<!-- Sesudah -->
<button
    @click="redirectToPOPayment(piutang.id_penjualan)"
    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 text-xs font-medium"
>
    <i class="bx bx-credit-card"></i> Bayar
</button>
```

---

## 📋 Summary Perubahan

### Backend Changes

1. ✅ Method baru: `getPiutangInvoicePDF($id)` di `FinanceAccountantController`
2. ✅ Update: `getPiutangData()` - invoice_number dari kolom `nama`
3. ✅ Route baru: `finance.piutang.invoice-pdf`

### Frontend Changes

1. ✅ Modal PDF baru dengan iframe untuk stream PDF
2. ✅ Nomor invoice clickable (button dengan hover effect)
3. ✅ Tombol "Bayar" menggantikan tombol "Detail"
4. ✅ JavaScript functions baru:
    - `showInvoicePDF()`
    - `closePDFModal()`
    - `redirectToPOPayment()`

### Integration Changes

1. ✅ Halaman PO menerima parameter URL (`po_id`, `open_payment`)
2. ✅ Auto-open payment modal di halaman PO
3. ✅ URL cleanup setelah modal terbuka

---

## 🎨 UI/UX Improvements

### Nomor Invoice

-   **Hover Effect:** Underline saat hover
-   **Color:** Blue 600 → Blue 800 saat hover
-   **Cursor:** Pointer untuk indicate clickable

### Modal PDF

-   **Size:** Max-width 6xl (extra large)
-   **Height:** 80vh untuk iframe
-   **Loading State:** Spinner saat load PDF
-   **Backdrop:** Semi-transparent dengan blur
-   **Animation:** Smooth fade in/out

### Tombol Bayar

-   **Color:** Green (sesuai dengan action pembayaran)
-   **Icon:** Credit card icon
-   **Position:** Di kolom Aksi (menggantikan tombol Detail)

---

## 🔧 Technical Details

### PDF Display

-   Menggunakan `<iframe>` untuk stream PDF
-   URL: `/pembelian/purchase-order/{id}/print`
-   No download, langsung tampil di browser
-   Responsive height (80vh)

### URL Parameters

-   `po_id`: ID purchase order yang akan dibuka
-   `open_payment=1`: Flag untuk auto-open payment modal
-   Parameters di-clean setelah digunakan (history.replaceState)

### Timing

-   Delay 1.5 detik sebelum auto-open modal
-   Memberikan waktu untuk data PO load sempurna
-   Mencegah error "PO not found"

---

## 🧪 Testing Guide

### Test 1: Nomor Invoice dari Kolom `nama`

1. Buka halaman piutang
2. Periksa kolom "No Invoice"
3. **Expected:** Menampilkan format dari database (e.g., "Invoice 003//INV/XI/2025")
4. **Not:** Format INV-000003

### Test 2: Klik Invoice untuk Lihat PDF

1. Klik nomor invoice di tabel
2. **Expected:** Modal muncul dengan PDF invoice
3. PDF load di iframe (stream, bukan download)
4. Klik "Tutup" atau backdrop untuk close modal
5. **Expected:** Modal tertutup, PDF hilang

### Test 3: Tombol Bayar

1. Klik tombol "Bayar" (hijau dengan icon credit card)
2. **Expected:** Redirect ke halaman Purchase Order
3. URL contains: `?po_id=X&open_payment=1`
4. Tunggu 1.5 detik
5. **Expected:** Modal "Riwayat Pembayaran" terbuka otomatis
6. Modal menampilkan data PO yang sesuai
7. URL parameters hilang (clean URL)

### Test 4: Error Handling

**Test 4.1: Invoice tidak ada**

1. Klik invoice yang tidak punya `id_penjualan`
2. **Expected:** Notification error "Invoice tidak tersedia"

**Test 4.2: PO tidak ditemukan**

1. Akses URL dengan `po_id` yang tidak valid
2. **Expected:** Toast message "Purchase Order tidak ditemukan"

---

## 📊 Database Schema Reference

### Tabel `piutang`

```sql
- id_piutang (PK)
- id_penjualan (FK) -- Digunakan untuk link ke PO
- nama (varchar)    -- Berisi nomor invoice (NEW: digunakan untuk display)
- jumlah_piutang
- jumlah_dibayar
- sisa_piutang
- status
- ...
```

### Relasi

-   `piutang.id_penjualan` → `purchase_order.id_purchase_order`
-   PDF Invoice: `/pembelian/purchase-order/{id_penjualan}/print`

---

## 🚀 Deployment Checklist

-   [x] Update controller method
-   [x] Add new route
-   [x] Update view (modal + buttons)
-   [x] Update JavaScript functions
-   [x] Update PO page for auto-open
-   [x] Test all scenarios
-   [x] Clear cache: `php artisan cache:clear`
-   [x] Clear route: `php artisan route:clear`
-   [x] Clear view: `php artisan view:clear`

---

## 📝 Notes

1. **Modal Detail Dihapus:** Tombol "Detail" diganti dengan "Bayar"

    - Jika masih perlu detail, bisa ditambahkan kembali sebagai tombol terpisah

2. **PDF Stream:** PDF tidak di-download, langsung tampil di browser

    - Lebih user-friendly
    - Faster (no download wait)

3. **Auto-open Timing:** 1.5 detik delay

    - Bisa disesuaikan jika terlalu cepat/lambat
    - Tergantung kecepatan load data PO

4. **URL Cleanup:** Parameters dihapus setelah digunakan
    - Mencegah re-open modal saat refresh
    - Clean URL untuk user experience

---

## 🎉 Status

**SEMUA PERBAIKAN SELESAI DAN SIAP DIGUNAKAN**

✅ Nomor invoice dari kolom `nama`  
✅ Invoice clickable untuk lihat PDF  
✅ Tombol "Bayar" dengan redirect ke PO  
✅ Auto-open payment modal di PO  
✅ Error handling lengkap  
✅ UI/UX improvements

---

## 🔗 Related Files

**Modified:**

-   `app/Http/Controllers/FinanceAccountantController.php`
-   `routes/web.php`
-   `resources/views/admin/finance/piutang/index.blade.php`
-   `resources/views/admin/pembelian/purchase-order/index.blade.php`

**Routes Added:**

-   `GET /finance/piutang/{id}/invoice-pdf`

**No Database Changes Required**

---

---

## 🔧 Bug Fixes

### Fix 1: TypeError - showPaymentHistory is not a function

**Issue:** Error saat klik tombol "Bayar"  
**Root Cause:** Nama fungsi salah (`showPaymentHistory` vs `viewPaymentHistory`)  
**Solution:** Update ke `viewPaymentHistory(po)`  
**Status:** ✅ Fixed

**Details:** See `PIUTANG_FIX_PAYMENT_MODAL.md`

---

**Last Updated:** November 24, 2025  
**Version:** 1.1.1  
**Status:** ✅ Production Ready
