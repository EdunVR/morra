# Quick Test - Perbaikan Halaman Piutang

## 🎯 3 Fitur Baru yang Harus Ditest

### ✅ Test 1: Nomor Invoice dari Database

**Waktu:** 30 detik

1. Buka halaman piutang: `/finance/piutang`
2. Lihat kolom "No Invoice"
3. **Cek:** Apakah menampilkan format dari database?
    - ✅ Contoh: "Invoice 003//INV/XI/2025"
    - ❌ Bukan: "INV-000003"

**Expected Result:**

-   Nomor invoice sesuai dengan kolom `nama` di tabel `piutang`
-   Format bervariasi sesuai data di database

---

### ✅ Test 2: Klik Invoice → Lihat PDF

**Waktu:** 1 menit

1. Di tabel piutang, klik nomor invoice (warna biru)
2. **Cek:** Modal muncul?
3. **Cek:** PDF invoice muncul di iframe?
4. **Cek:** PDF bisa di-scroll?
5. Klik "Tutup" atau klik di luar modal
6. **Cek:** Modal tertutup?

**Expected Result:**

-   Modal muncul dengan smooth animation
-   PDF invoice pembelian tampil di iframe (stream, bukan download)
-   PDF bisa di-scroll dan di-zoom
-   Modal bisa ditutup dengan 2 cara (button atau backdrop)

**Troubleshooting:**

-   Jika PDF tidak muncul: Cek apakah `id_penjualan` ada di data piutang
-   Jika error: Cek console browser untuk error message

---

### ✅ Test 3: Tombol Bayar → Auto-open Payment Modal

**Waktu:** 2 menit

1. Di kolom "Aksi", klik tombol "Bayar" (hijau dengan icon credit card)
2. **Cek:** Redirect ke halaman Purchase Order?
3. **Cek:** URL contains `?po_id=X&open_payment=1`?
4. Tunggu 1-2 detik
5. **Cek:** Modal "Riwayat Pembayaran" terbuka otomatis?
6. **Cek:** Modal menampilkan data PO yang benar?
7. **Cek:** URL parameters hilang (clean URL)?

**Expected Result:**

-   Redirect ke halaman PO
-   Modal pembayaran terbuka otomatis setelah 1.5 detik
-   Data PO yang ditampilkan sesuai dengan piutang yang diklik
-   URL bersih setelah modal terbuka (no parameters)

**Troubleshooting:**

-   Jika modal tidak terbuka: Tunggu lebih lama (data PO mungkin load lambat)
-   Jika PO tidak ditemukan: Cek apakah `id_penjualan` valid
-   Jika error: Cek console browser

---

## 🚨 Error Scenarios to Test

### Error 1: Invoice Tidak Ada

1. Cari piutang yang tidak punya `id_penjualan` (NULL)
2. Klik nomor invoice
3. **Expected:** Notification error "Invoice tidak tersedia untuk piutang ini"

### Error 2: PO Tidak Ditemukan

1. Manual akses URL: `/pembelian/purchase-order?po_id=99999&open_payment=1`
2. **Expected:** Toast message "Purchase Order tidak ditemukan"

---

## ✅ Quick Checklist

Centang jika test berhasil:

-   [ ] Nomor invoice dari database (bukan format INV-XXXXXX)
-   [ ] Nomor invoice clickable (hover effect)
-   [ ] Modal PDF muncul saat klik invoice
-   [ ] PDF tampil di iframe (stream)
-   [ ] Modal PDF bisa ditutup
-   [ ] Tombol "Bayar" ada di kolom Aksi (warna hijau)
-   [ ] Klik "Bayar" redirect ke PO
-   [ ] URL contains parameters (po_id, open_payment)
-   [ ] Modal pembayaran terbuka otomatis
-   [ ] Data PO sesuai dengan piutang
-   [ ] URL parameters hilang setelah modal terbuka
-   [ ] Error handling untuk invoice tidak ada
-   [ ] Error handling untuk PO tidak ditemukan

---

## 🎯 Success Criteria

**PASS jika:**

-   ✅ Semua 3 fitur utama berfungsi
-   ✅ Minimal 11 dari 13 checklist items passed
-   ✅ Tidak ada JavaScript error di console
-   ✅ UI/UX smooth dan responsive

**FAIL jika:**

-   ❌ Nomor invoice masih format INV-XXXXXX
-   ❌ Modal PDF tidak muncul
-   ❌ Tombol "Bayar" tidak redirect
-   ❌ Modal pembayaran tidak auto-open
-   ❌ Ada JavaScript error di console

---

## 🔧 Quick Fixes

### Issue: Nomor invoice masih INV-XXXXXX

**Fix:** Clear cache

```bash
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Modal PDF tidak muncul

**Fix:** Cek console browser untuk error, pastikan route ada

```bash
php artisan route:list --name=piutang
```

### Issue: Modal pembayaran tidak auto-open

**Fix:**

1. Cek apakah data PO sudah load (tunggu lebih lama)
2. Cek console untuk error
3. Cek apakah `po_id` valid

---

## 📊 Test Data Requirements

Untuk testing optimal, pastikan ada:

1. Minimal 1 piutang dengan `id_penjualan` valid
2. Minimal 1 piutang dengan `id_penjualan` NULL (untuk test error)
3. Purchase Order yang sesuai dengan `id_penjualan`
4. Kolom `nama` di tabel piutang terisi

---

## ⏱️ Total Testing Time

-   Test 1: 30 seconds
-   Test 2: 1 minute
-   Test 3: 2 minutes
-   Error scenarios: 1 minute

**Total: ~5 minutes**

---

## 🎉 Done!

Jika semua test passed, fitur siap untuk production! 🚀
