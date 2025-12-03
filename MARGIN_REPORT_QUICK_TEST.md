# Quick Test Guide - Laporan Margin

## 🚀 Quick Start

### 1. Akses Halaman

```
URL: http://localhost/admin/penjualan/laporan-margin
atau
Menu: Penjualan & Pemasaran → Laporan Margin
```

### 2. Verifikasi Tampilan Awal

✅ Halaman terbuka tanpa error
✅ Summary cards menampilkan angka 0 (jika belum ada data)
✅ Filter outlet, tanggal mulai, tanggal akhir terisi
✅ Default date range: 7 hari terakhir
✅ Table kosong atau menampilkan data

### 3. Test Filter

#### Filter by Outlet

1. Pilih outlet dari dropdown
2. Data otomatis reload
3. Summary cards update
4. Table menampilkan data outlet tersebut

#### Filter by Date Range

1. Ubah tanggal mulai
2. Ubah tanggal akhir
3. Data otomatis reload
4. Verifikasi data sesuai periode

#### Search Product

1. Ketik nama produk di search box
2. Table filter secara real-time
3. Summary cards update sesuai hasil filter

### 4. Verifikasi Data Display

#### Check Table Columns

-   ✅ No urut
-   ✅ Source badge (Invoice/POS)
-   ✅ Tanggal transaksi
-   ✅ Outlet
-   ✅ Nama Produk
-   ✅ Qty
-   ✅ HPP (format rupiah)
-   ✅ Harga Jual (format rupiah)
-   ✅ Subtotal (format rupiah)
-   ✅ Profit (format rupiah, warna hijau/merah)
-   ✅ Margin % (badge dengan warna sesuai level)
-   ✅ Payment Type (badge Cash/QRIS/BON)

#### Check Summary Cards

-   ✅ Total Items (jumlah baris)
-   ✅ Total HPP (sum of HPP × Qty)
-   ✅ Total Penjualan (sum of Subtotal)
-   ✅ Total Profit (sum of Profit)
-   ✅ Avg Margin (rata-rata margin %)

### 5. Test Margin Color Coding

Verifikasi badge margin memiliki warna yang benar:

-   **Hijau**: Margin ≥ 30%
-   **Biru**: Margin 15-29%
-   **Orange**: Margin 5-14%
-   **Merah**: Margin < 5%

### 6. Test Export PDF

1. Click button "Export PDF"
2. Modal terbuka
3. PDF loading
4. PDF tampil di iframe
5. Verifikasi isi PDF:
    - ✅ Header dengan judul
    - ✅ Info outlet dan periode
    - ✅ Summary boxes
    - ✅ Table lengkap dengan semua data
    - ✅ Color-coded badges
    - ✅ Footer dengan timestamp
6. Click "Tutup" untuk close modal

### 7. Test Refresh

1. Click button "Refresh"
2. Icon refresh berputar
3. Data reload
4. Notification "Data berhasil dimuat ulang"

## 🧪 Test Scenarios

### Scenario 1: Empty Data

```
1. Pilih outlet yang tidak ada transaksi
2. Atau pilih date range yang tidak ada data
3. Verifikasi:
   - Summary cards = 0
   - Table menampilkan empty state
   - Message: "Tidak ada data margin"
```

### Scenario 2: Mixed Data (Invoice + POS)

```
1. Pilih outlet yang ada transaksi Invoice dan POS
2. Pilih date range yang mencakup kedua jenis transaksi
3. Verifikasi:
   - Table menampilkan kedua source
   - Badge "Invoice" dan "POS" muncul
   - Perhitungan margin benar untuk kedua source
```

### Scenario 3: High Margin Products

```
1. Cari produk dengan margin tinggi (>30%)
2. Verifikasi:
   - Badge margin berwarna hijau
   - Profit positif dan besar
   - Perhitungan benar
```

### Scenario 4: Low/Negative Margin

```
1. Cari produk dengan margin rendah atau negatif
2. Verifikasi:
   - Badge margin berwarna merah
   - Profit negatif (jika ada)
   - Warna profit merah
```

### Scenario 5: Different Payment Types

```
1. Filter data dengan berbagai jenis pembayaran
2. Verifikasi badge payment type:
   - Cash = hijau
   - QRIS = biru
   - BON = orange
```

## 🔍 Manual Calculation Check

Pilih satu baris data dan verifikasi manual:

```
Contoh Data:
- Produk: Produk A
- Qty: 5
- HPP: Rp 10,000
- Harga Jual: Rp 15,000
- Subtotal: Rp 75,000

Perhitungan:
1. Total HPP = 10,000 × 5 = 50,000
2. Profit = 75,000 - 50,000 = 25,000
3. Margin % = (25,000 / 75,000) × 100 = 33.33%

Verifikasi di table:
✅ Profit = Rp 25,000 (hijau)
✅ Margin = 33.33% (badge hijau)
```

## 🐛 Common Issues & Solutions

### Issue 1: Data tidak muncul

**Solution:**

-   Check apakah ada transaksi di date range
-   Check permission `sales.invoice.view`
-   Check console browser untuk error
-   Check `storage/logs/laravel.log`

### Issue 2: HPP = 0

**Solution:**

-   Check apakah produk memiliki HPP
-   Untuk POS, check method `calculateHppBarangDagang()`
-   Update HPP produk di master data

### Issue 3: Margin calculation salah

**Solution:**

-   Verifikasi formula: (Profit / Subtotal) × 100
-   Check apakah subtotal > 0
-   Check apakah HPP sudah benar

### Issue 4: PDF tidak muncul

**Solution:**

-   Check DomPDF installed: `composer require barryvdh/laravel-dompdf`
-   Check route `penjualan.margin.export-pdf`
-   Check browser console untuk error
-   Try direct URL: `/admin/penjualan/laporan-margin/export-pdf?start_date=...&end_date=...`

### Issue 5: Summary tidak update

**Solution:**

-   Check method `calculateSummary()` di Alpine.js
-   Check apakah `filteredData` ter-update
-   Refresh page

## ✅ Success Criteria

Implementasi dianggap berhasil jika:

1. ✅ Halaman terbuka tanpa error
2. ✅ Data Invoice dan POS muncul
3. ✅ Filter berfungsi dengan baik
4. ✅ Summary cards menghitung dengan benar
5. ✅ Margin % dihitung dengan benar
6. ✅ Color coding sesuai dengan level margin
7. ✅ Export PDF berfungsi
8. ✅ PDF menampilkan data lengkap
9. ✅ Responsive di berbagai ukuran layar
10. ✅ No console errors

## 📊 Sample Test Data

Untuk testing yang lebih baik, pastikan ada data:

1. **Invoice dengan margin tinggi** (>30%)

    - Produk premium dengan markup besar

2. **Invoice dengan margin rendah** (<10%)

    - Produk dengan harga kompetitif

3. **POS dengan berbagai payment type**

    - Cash
    - QRIS
    - BON

4. **Transaksi dari berbagai outlet**

    - Minimal 2-3 outlet berbeda

5. **Transaksi dalam berbagai periode**
    - Hari ini
    - Minggu ini
    - Bulan ini

## 🎯 Performance Check

-   ✅ Page load < 2 detik
-   ✅ Filter response < 1 detik
-   ✅ Search real-time smooth
-   ✅ PDF generation < 5 detik
-   ✅ No memory issues dengan 1000+ records

---

**Happy Testing!** 🚀

Jika menemukan bug atau issue, catat:

1. Steps to reproduce
2. Expected behavior
3. Actual behavior
4. Screenshot/error message
5. Browser & version
