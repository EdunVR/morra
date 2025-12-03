# Piutang Testing Checklist

## Pre-requisites

✅ Server Laravel sudah running
✅ Database sudah terkoneksi
✅ Ada data piutang di database
✅ Browser sudah dibuka

## Test 1: Akses Halaman Piutang

**Steps:**

1. Buka browser
2. Navigate ke: `http://localhost/finance/piutang`
3. Login jika diperlukan

**Expected Result:**

-   ✅ Halaman piutang terbuka tanpa error
-   ✅ Tabel piutang menampilkan data
-   ✅ Summary cards menampilkan statistik
-   ✅ Filter berfungsi (outlet, status, tanggal)

**Check Console:**

-   ✅ Tidak ada error JavaScript
-   ✅ Network tab menunjukkan request ke route name (bukan hardcoded URL)

---

## Test 2: Klik Invoice untuk Print PDF

**Steps:**

1. Di tabel piutang, cari baris dengan data
2. Klik pada **nomor invoice** (kolom pertama, warna biru)
3. Tunggu modal terbuka

**Expected Result:**

-   ✅ Modal "Invoice Penjualan" terbuka
-   ✅ PDF invoice ditampilkan di iframe
-   ✅ PDF menampilkan detail invoice penjualan yang benar
-   ✅ Tombol "Tutup" berfungsi

**Check Console:**

1. Buka Developer Tools (F12)
2. Tab Network
3. Cari request ke: `finance/piutang/get-sales-invoice-id/{id}`
4. Response harus:

```json
{
    "success": true,
    "sales_invoice_id": 123,
    "penjualan_id": 456
}
```

5. Cari request ke: `penjualan/invoice/{id}/print`
6. Response harus menampilkan PDF

**Troubleshooting:**

-   ❌ Modal tidak muncul → Check console untuk error
-   ❌ PDF tidak muncul → Check apakah sales_invoice_id valid
-   ❌ Error 404 → Check route name di routes/web.php

---

## Test 3: Tombol Bayar - Redirect ke Invoice

**Steps:**

1. Di tabel piutang, cari piutang dengan status "Belum Lunas"
2. Klik tombol **"Bayar"** (hijau, icon credit card)
3. Tunggu redirect

**Expected Result:**

-   ✅ Redirect ke halaman invoice penjualan
-   ✅ Modal "Konfirmasi Pelunasan Invoice" otomatis terbuka
-   ✅ Data invoice sudah terisi di modal
-   ✅ Form pembayaran siap diisi
-   ✅ URL di address bar berubah (tanpa parameter ?invoice_id=X&open_payment=1)

**Check Console:**

1. Sebelum klik "Bayar", buka Developer Tools
2. Tab Network
3. Klik tombol "Bayar"
4. Cari request ke: `finance/piutang/get-sales-invoice-id/{id}`
5. Response harus berisi sales_invoice_id
6. Browser akan redirect ke: `penjualan/invoice?invoice_id=X&open_payment=1`
7. Setelah 1.5 detik, modal pembayaran terbuka
8. URL berubah menjadi: `penjualan/invoice` (parameter dihapus)

**Troubleshooting:**

-   ❌ Tidak redirect → Check console untuk error
-   ❌ Modal tidak terbuka → Check setTimeout di invoice index
-   ❌ Data tidak terisi → Check invoice_id di URL parameter

---

## Test 4: Verifikasi Route Name Usage

**Steps:**

1. Buka halaman piutang
2. Buka Developer Tools (F12)
3. Tab Network
4. Clear network log
5. Lakukan berbagai aksi (klik invoice, filter, refresh)

**Expected Result:**
Semua request menggunakan URL dari route name:

-   ✅ `/finance/outlets` → finance.outlets.data
-   ✅ `/finance/piutang/data` → finance.piutang.data
-   ✅ `/finance/piutang/{id}/detail` → finance.piutang.detail
-   ✅ `/finance/piutang/get-sales-invoice-id/{id}` → finance.piutang.get-sales-invoice-id
-   ✅ `/penjualan/invoice/{id}/print` → penjualan.invoice.print

**Check:**

-   ❌ Jika ada URL hardcoded (contoh: `/finance/piutang/data` tanpa route name)
-   ✅ Semua URL harus generated dari Laravel route()

---

## Test 5: Modal Print PDF - Full Flow

**Steps:**

1. Klik nomor invoice
2. Modal terbuka dengan PDF
3. Scroll PDF untuk melihat detail
4. Klik tombol "Tutup"
5. Modal tertutup

**Expected Result:**

-   ✅ PDF loading smooth tanpa error
-   ✅ PDF menampilkan data yang benar
-   ✅ Modal dapat ditutup dengan tombol atau klik outside
-   ✅ Setelah tutup, tabel piutang masih terlihat

---

## Test 6: Redirect + Auto-Open Modal - Full Flow

**Steps:**

1. Klik tombol "Bayar" pada piutang belum lunas
2. Tunggu redirect ke halaman invoice
3. Modal pembayaran otomatis terbuka
4. Isi form pembayaran:
    - Jumlah pembayaran
    - Tanggal pembayaran
    - Metode pembayaran
    - Upload bukti transfer (optional)
    - Catatan (optional)
5. Klik "Proses Pembayaran"

**Expected Result:**

-   ✅ Redirect berhasil
-   ✅ Modal terbuka otomatis dalam 1.5 detik
-   ✅ Form terisi dengan data invoice
-   ✅ Validasi form bekerja
-   ✅ Pembayaran berhasil diproses
-   ✅ Notifikasi sukses muncul
-   ✅ Data piutang terupdate

---

## Test 7: Error Handling

**Test 7.1: Invoice Tidak Ditemukan**

1. Buka console
2. Jalankan: `piutangManagement().showInvoicePDF(999, 999)`
3. Expected: Notifikasi error "Invoice tidak ditemukan"

**Test 7.2: Network Error**

1. Matikan koneksi internet
2. Klik nomor invoice
3. Expected: Notifikasi error "Gagal memuat invoice"

**Test 7.3: Invalid Data**

1. Klik invoice dengan id_penjualan = NULL
2. Expected: Notifikasi error "Invoice tidak tersedia untuk piutang ini"

---

## Test 8: Performance Check

**Steps:**

1. Buka halaman piutang dengan banyak data (>100 records)
2. Klik filter outlet
3. Klik filter status
4. Ubah tanggal
5. Ketik di search box

**Expected Result:**

-   ✅ Loading tidak lebih dari 2 detik
-   ✅ UI tetap responsive
-   ✅ Tidak ada lag saat scroll
-   ✅ Filter bekerja dengan cepat

---

## Test 9: Mobile Responsive

**Steps:**

1. Buka Developer Tools
2. Toggle device toolbar (Ctrl+Shift+M)
3. Pilih device: iPhone 12 Pro
4. Test semua fitur

**Expected Result:**

-   ✅ Tabel responsive (horizontal scroll jika perlu)
-   ✅ Modal fit di layar mobile
-   ✅ Tombol mudah diklik
-   ✅ PDF viewer bekerja di mobile

---

## Test 10: Browser Compatibility

Test di berbagai browser:

-   ✅ Chrome (latest)
-   ✅ Firefox (latest)
-   ✅ Edge (latest)
-   ✅ Safari (latest)

**Expected Result:**

-   ✅ Semua fitur bekerja di semua browser
-   ✅ Tidak ada error JavaScript
-   ✅ UI konsisten

---

## Checklist Summary

### Functionality

-   [ ] Halaman piutang terbuka tanpa error
-   [ ] Tabel menampilkan data dengan benar
-   [ ] Filter bekerja (outlet, status, tanggal, search)
-   [ ] Klik invoice → modal print PDF terbuka
-   [ ] PDF ditampilkan dengan benar
-   [ ] Tombol bayar → redirect ke invoice
-   [ ] Modal pembayaran auto-open
-   [ ] Form pembayaran bekerja
-   [ ] Pembayaran berhasil diproses
-   [ ] Data piutang terupdate setelah bayar

### Technical

-   [ ] Semua fetch menggunakan route name
-   [ ] Tidak ada hardcoded URL
-   [ ] Console tidak ada error
-   [ ] Network request menggunakan route yang benar
-   [ ] Response API sesuai format
-   [ ] Error handling bekerja

### UI/UX

-   [ ] Loading state ditampilkan
-   [ ] Notifikasi muncul dengan benar
-   [ ] Modal dapat ditutup
-   [ ] Responsive di mobile
-   [ ] Smooth animation
-   [ ] Consistent styling

### Performance

-   [ ] Loading cepat (<2 detik)
-   [ ] Tidak ada lag
-   [ ] Memory usage normal
-   [ ] No memory leaks

---

## Quick Debug Commands

### Check Routes

```bash
php artisan route:list --name=finance.piutang
php artisan route:list --name=penjualan.invoice
```

### Check Database

```sql
-- Check piutang data
SELECT * FROM piutang LIMIT 10;

-- Check sales_invoice mapping
SELECT p.id_piutang, p.nama, p.id_penjualan, si.id_sales_invoice
FROM piutang p
LEFT JOIN sales_invoice si ON p.id_penjualan = si.id_penjualan
LIMIT 10;
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

---

## Common Issues & Solutions

### Issue 1: Modal tidak muncul

**Solution:**

-   Check console untuk error JavaScript
-   Verify `showPrintModal` variable
-   Check Alpine.js loaded

### Issue 2: PDF tidak muncul

**Solution:**

-   Check sales_invoice_id valid
-   Verify route `penjualan.invoice.print` exists
-   Check PDF generation di controller

### Issue 3: Redirect tidak bekerja

**Solution:**

-   Check `window.location.href` syntax
-   Verify route `penjualan.invoice.index` exists
-   Check URL parameter format

### Issue 4: Modal pembayaran tidak auto-open

**Solution:**

-   Check setTimeout di invoice index
-   Verify `openPaymentModal` function exists
-   Check URL parameter `open_payment=1`

---

## Success Criteria

✅ Semua test passed
✅ Tidak ada error di console
✅ Semua fitur bekerja sesuai spesifikasi
✅ Performance baik
✅ UI/UX smooth
✅ Mobile responsive
✅ Browser compatible

## Status: READY FOR TESTING

Tanggal: 2025-11-24
