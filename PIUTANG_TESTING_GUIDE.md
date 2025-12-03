# Panduan Testing Halaman Piutang

## 🎯 Tujuan Testing

Memastikan halaman Piutang berfungsi dengan baik dan terintegrasi dengan modul lain.

## 📋 Pre-requisites

1. Server Laravel sudah running
2. Database sudah terisi dengan data:
    - Outlets
    - Members (customers)
    - Penjualan (sales invoices)
    - Piutang
    - Journal Entries (opsional)

## 🧪 Test Cases

### 1. Test Akses Halaman

**Langkah:**

1. Login ke aplikasi
2. Buka sidebar menu "Finance & Accounting"
3. Klik "Piutang dari Customer"

**Expected Result:**

-   Halaman piutang terbuka
-   URL: `/finance/piutang`
-   Summary cards muncul dengan data
-   Tabel piutang muncul (jika ada data)

---

### 2. Test Summary Cards

**Langkah:**

1. Perhatikan 4 summary cards di bagian atas

**Expected Result:**

-   Card 1: Total Piutang (warna biru)
-   Card 2: Sudah Dibayar (warna hijau)
-   Card 3: Sisa Piutang (warna orange)
-   Card 4: Jatuh Tempo (warna merah, menampilkan jumlah)

**Validasi:**

-   Total Piutang = Jumlah semua piutang
-   Sudah Dibayar = Total yang sudah dibayar
-   Sisa Piutang = Total Piutang - Sudah Dibayar
-   Jatuh Tempo = Jumlah piutang yang melewati tanggal jatuh tempo

---

### 3. Test Filter Outlet

**Langkah:**

1. Klik dropdown "Outlet"
2. Pilih outlet berbeda
3. Tunggu data reload

**Expected Result:**

-   Data piutang berubah sesuai outlet yang dipilih
-   Summary cards update
-   Tabel menampilkan piutang dari outlet tersebut

---

### 4. Test Filter Status

**Langkah:**

1. Pilih "Belum Lunas" di dropdown Status
2. Tunggu data reload
3. Pilih "Lunas"
4. Pilih "Semua Status"

**Expected Result:**

-   Filter "Belum Lunas": Hanya tampil piutang belum lunas
-   Filter "Lunas": Hanya tampil piutang lunas
-   Filter "Semua Status": Tampil semua piutang

---

### 5. Test Filter Tanggal

**Langkah:**

1. Ubah "Tanggal Mulai" ke tanggal tertentu
2. Ubah "Tanggal Akhir" ke tanggal tertentu
3. Tunggu data reload

**Expected Result:**

-   Hanya piutang dalam range tanggal yang ditampilkan
-   Summary cards update sesuai filter

---

### 6. Test Search Customer

**Langkah:**

1. Ketik nama customer di search box
2. Tunggu 500ms (debounce)
3. Data akan auto-reload

**Expected Result:**

-   Hanya piutang dengan nama customer yang match yang ditampilkan
-   Search case-insensitive
-   Partial match works (e.g., "john" matches "John Doe")

---

### 7. Test Tabel Piutang

**Langkah:**

1. Perhatikan kolom-kolom tabel

**Expected Result:**
Tabel menampilkan:

-   No Invoice (format: INV-XXXXXX)
-   Tanggal (format: DD MMM YYYY)
-   Customer (nama customer)
-   Outlet (nama outlet)
-   Jumlah Piutang (format: Rp X.XXX)
-   Dibayar (format: Rp X.XXX, warna hijau)
-   Sisa (format: Rp X.XXX, warna orange jika > 0)
-   Jatuh Tempo (format: DD MMM YYYY)
-   Status (badge: Lunas/Belum Lunas/Jatuh Tempo)
-   Aksi (tombol Detail)

---

### 8. Test Status Badge

**Langkah:**

1. Perhatikan kolom Status di tabel

**Expected Result:**

-   Badge "Lunas" (hijau) untuk status lunas
-   Badge "Belum Lunas" (orange) untuk belum lunas & belum jatuh tempo
-   Badge "Jatuh Tempo" (merah) untuk belum lunas & sudah lewat jatuh tempo

---

### 9. Test Overdue Indicator

**Langkah:**

1. Cari piutang yang sudah jatuh tempo
2. Perhatikan kolom "Jatuh Tempo"

**Expected Result:**

-   Tanggal jatuh tempo berwarna merah
-   Muncul text "Terlambat X hari" di bawah tanggal
-   X = jumlah hari sejak jatuh tempo

---

### 10. Test Modal Detail - Buka/Tutup

**Langkah:**

1. Klik tombol "Detail" pada salah satu piutang
2. Modal muncul
3. Klik tombol "Tutup" atau klik di luar modal

**Expected Result:**

-   Modal muncul dengan smooth transition
-   Background blur/overlay
-   Modal tertutup saat klik "Tutup" atau klik backdrop

---

### 11. Test Modal Detail - Informasi Piutang

**Langkah:**

1. Buka modal detail
2. Perhatikan section "Informasi Piutang"

**Expected Result:**
Menampilkan:

-   Customer (nama, telepon, alamat)
-   Outlet
-   Tanggal transaksi
-   Tanggal jatuh tempo (merah jika overdue)
-   Jumlah Piutang
-   Sudah Dibayar (hijau)
-   Sisa Piutang (orange)
-   Status badge

---

### 12. Test Modal Detail - Transaksi Penjualan

**Langkah:**

1. Buka modal detail
2. Perhatikan section "Detail Transaksi Penjualan"

**Expected Result:**

-   No Invoice ditampilkan
-   Tabel items dengan kolom:
    -   Produk
    -   Qty
    -   Harga
    -   Diskon
    -   Subtotal
-   Footer menampilkan Total

**Validasi:**

-   Total = Sum of all subtotals
-   Subtotal = (Harga × Qty) - Diskon

---

### 13. Test Modal Detail - Jurnal Terkait

**Langkah:**

1. Buka modal detail
2. Perhatikan section "Jurnal Terkait"

**Expected Result:**

-   Jika ada jurnal: Tampil list jurnal
-   Jika tidak ada: Tampil "Tidak ada jurnal terkait"

**Untuk setiap jurnal:**

-   Transaction Number
-   Tanggal
-   Status badge (posted/draft)
-   Description
-   Tabel detail akun:
    -   Kode & Nama Akun
    -   Keterangan
    -   Debit
    -   Kredit
-   Footer: Total Debit & Total Kredit

**Validasi:**

-   Total Debit = Total Kredit (balanced)

---

### 14. Test Refresh Button

**Langkah:**

1. Klik tombol "Refresh"
2. Perhatikan loading state

**Expected Result:**

-   Icon refresh berputar (animate-spin)
-   Data reload dari server
-   Notification "Data berhasil dimuat ulang"

---

### 15. Test Loading State

**Langkah:**

1. Reload halaman atau ubah filter
2. Perhatikan loading indicator

**Expected Result:**

-   Spinner muncul saat loading
-   Text "Memuat data piutang..."
-   Tabel/konten tersembunyi saat loading

---

### 16. Test Empty State

**Langkah:**

1. Set filter yang tidak menghasilkan data
   (e.g., tanggal di masa depan)

**Expected Result:**

-   Icon receipt abu-abu
-   Text "Tidak ada data piutang"
-   Text "Belum ada piutang yang tercatat untuk filter yang dipilih."

---

### 17. Test Responsive Design

**Langkah:**

1. Resize browser window
2. Test di mobile view (< 768px)
3. Test di tablet view (768px - 1024px)
4. Test di desktop view (> 1024px)

**Expected Result:**

-   Summary cards stack di mobile
-   Filter stack di mobile
-   Tabel scrollable horizontal di mobile
-   Modal responsive di semua ukuran

---

### 18. Test Currency Formatting

**Langkah:**

1. Perhatikan semua angka currency

**Expected Result:**

-   Format: Rp X.XXX (tanpa desimal)
-   Separator ribuan: titik (.)
-   Contoh: Rp 1.500.000

---

### 19. Test Date Formatting

**Langkah:**

1. Perhatikan semua tanggal

**Expected Result:**

-   Format: DD MMM YYYY
-   Bahasa Indonesia
-   Contoh: 24 Nov 2025

---

### 20. Test API Endpoints

**Menggunakan Browser DevTools atau Postman:**

**Test 1: Get Piutang Data**

```
GET /finance/piutang/data?outlet_id=1&status=all&start_date=2025-01-01&end_date=2025-12-31
```

Expected: JSON dengan data piutang dan summary

**Test 2: Get Piutang Detail**

```
GET /finance/piutang/1/detail
```

Expected: JSON dengan detail piutang, penjualan, dan jurnal

---

## 🐛 Common Issues & Solutions

### Issue 1: Halaman tidak muncul

**Solution:**

-   Clear cache: `php artisan cache:clear`
-   Clear route: `php artisan route:clear`
-   Clear view: `php artisan view:clear`

### Issue 2: Data tidak muncul

**Solution:**

-   Cek apakah ada data piutang di database
-   Cek filter outlet (pastikan outlet memiliki piutang)
-   Cek console browser untuk error

### Issue 3: Modal tidak muncul

**Solution:**

-   Cek console browser untuk JavaScript error
-   Pastikan Alpine.js loaded
-   Cek network tab untuk API call

### Issue 4: Jurnal tidak muncul di modal

**Solution:**

-   Ini normal jika belum ada jurnal terkait
-   Jurnal harus memiliki reference_type dan reference_number yang sesuai

---

## ✅ Checklist Testing

-   [ ] Halaman dapat diakses
-   [ ] Summary cards menampilkan data benar
-   [ ] Filter outlet berfungsi
-   [ ] Filter status berfungsi
-   [ ] Filter tanggal berfungsi
-   [ ] Search customer berfungsi
-   [ ] Tabel menampilkan data lengkap
-   [ ] Status badge sesuai kondisi
-   [ ] Overdue indicator muncul
-   [ ] Modal detail dapat dibuka/ditutup
-   [ ] Informasi piutang lengkap di modal
-   [ ] Detail transaksi penjualan muncul
-   [ ] Jurnal terkait muncul (jika ada)
-   [ ] Refresh button berfungsi
-   [ ] Loading state muncul
-   [ ] Empty state muncul saat tidak ada data
-   [ ] Responsive di semua device
-   [ ] Currency format benar
-   [ ] Date format benar
-   [ ] API endpoints return data benar

---

## 📊 Test Data Recommendation

Untuk testing optimal, pastikan database memiliki:

1. Minimal 2 outlet
2. Minimal 5 customer (member)
3. Minimal 10 piutang dengan variasi:
    - Status lunas
    - Status belum lunas
    - Beberapa yang sudah jatuh tempo
    - Beberapa yang belum jatuh tempo
4. Beberapa transaksi penjualan terkait
5. Beberapa jurnal terkait (opsional)

---

## 🎉 Testing Complete

Jika semua test case di atas passed, halaman Piutang siap untuk production!
