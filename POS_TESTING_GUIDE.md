# Testing Guide - Point of Sales (POS)

## 🧪 Panduan Testing Lengkap

### Pre-requisites

-   ✅ Migration sudah dijalankan
-   ✅ Seeder permission sudah dijalankan
-   ✅ User memiliki permission POS
-   ✅ Ada produk dengan stok > 0
-   ✅ Ada customer di database
-   ✅ Setting COA sudah dikonfigurasi

## 1️⃣ Test Setup & Configuration

### Test 1.1: Migration

```bash
php artisan migrate
```

**Expected**: Migration berhasil tanpa error

### Test 1.2: Seeder Permission

```bash
php artisan db:seed --class=PosPermissionSeeder
```

**Expected**: 5 permission POS terbuat

### Test 1.3: Check Database Tables

```sql
SHOW TABLES LIKE 'pos_%';
SHOW TABLES LIKE 'setting_coa_pos';
```

**Expected**: 3 tabel terbuat (pos_sales, pos_sale_items, setting_coa_pos)

### Test 1.4: Menu Sidebar

1. Login sebagai user dengan permission POS
2. Buka sidebar Penjualan
3. **Expected**: Menu "Point of Sales" muncul

## 2️⃣ Test Setting COA

### Test 2.1: Akses Setting COA

1. Akses `/penjualan/pos/coa-settings`
2. **Expected**: Halaman setting terbuka

### Test 2.2: Simpan Setting COA

1. Pilih Buku Akuntansi
2. Isi semua akun:
    - Akun Kas: 1101
    - Akun Bank: 1102
    - Akun Piutang Usaha: 1103
    - Akun Pendapatan Penjualan: 4101
    - Akun HPP: 5101
    - Akun Persediaan: 1201
3. Klik Simpan
4. **Expected**: Alert "Setting COA POS berhasil disimpan"

### Test 2.3: Validasi Setting Tersimpan

```sql
SELECT * FROM setting_coa_pos WHERE id_outlet = 1;
```

**Expected**: Data setting muncul

## 3️⃣ Test POS Interface

### Test 3.1: Akses POS

1. Akses `/penjualan/pos`
2. **Expected**: Interface POS terbuka dengan grid produk

### Test 3.2: Load Products

1. Buka console browser (F12)
2. Check network tab
3. **Expected**: Request ke `/penjualan/pos/products` berhasil

### Test 3.3: Load Customers

1. Check network tab
2. **Expected**: Request ke `/penjualan/pos/customers` berhasil

### Test 3.4: Filter Kategori

1. Klik chip kategori "Barang"
2. **Expected**: Hanya produk kategori Barang yang muncul

### Test 3.5: Search Produk

1. Ketik nama produk di search box
2. Tekan Enter
3. **Expected**: Produk yang dicari muncul di grid

## 4️⃣ Test Transaksi Cash

### Test 4.1: Tambah Produk ke Keranjang

1. Klik produk dari grid
2. **Expected**: Produk masuk ke keranjang dengan qty 1

### Test 4.2: Ubah Qty

1. Klik tombol + pada item di keranjang
2. **Expected**: Qty bertambah, subtotal update

### Test 4.3: Hapus Item

1. Klik tombol trash pada item
2. **Expected**: Item hilang dari keranjang

### Test 4.4: Diskon Nominal

1. Masukkan diskon nominal: 10000
2. **Expected**: Total berkurang Rp 10.000

### Test 4.5: Diskon Persen

1. Masukkan diskon persen: 10
2. **Expected**: Total berkurang 10% dari subtotal

### Test 4.6: PPN

1. Centang "PPN 10%"
2. **Expected**: Total bertambah 10%

### Test 4.7: Transaksi Cash Berhasil

1. Pilih produk (contoh: Briket 25kg, qty 2)
2. Subtotal: Rp 160.000
3. Pilih metode: Cash
4. Masukkan bayar: Rp 200.000
5. Klik "Bayar & Cetak"
6. **Expected**:
    - Alert "Transaksi berhasil disimpan"
    - Keranjang kosong
    - Produk reload

### Test 4.8: Validasi Database

```sql
-- Check pos_sales
SELECT * FROM pos_sales ORDER BY id DESC LIMIT 1;

-- Check pos_sale_items
SELECT * FROM pos_sale_items WHERE pos_sale_id = [last_id];

-- Check penjualan
SELECT * FROM penjualan ORDER BY id_penjualan DESC LIMIT 1;

-- Check penjualan_detail
SELECT * FROM penjualan_detail WHERE id_penjualan = [last_id];

-- Check stok berkurang
SELECT stok FROM produk WHERE id_produk = [product_id];
```

**Expected**: Semua data tersimpan dengan benar

### Test 4.9: Validasi Jurnal

```sql
SELECT * FROM journal_entries
WHERE reference_type = 'pos'
ORDER BY id DESC LIMIT 1;

SELECT * FROM journal_entry_details
WHERE journal_entry_id = [last_id];
```

**Expected**: Jurnal terbuat dengan benar:

-   Debit: Kas
-   Credit: Pendapatan Penjualan
-   Debit: HPP
-   Credit: Persediaan

## 5️⃣ Test Transaksi Transfer

### Test 5.1: Transaksi Transfer

1. Pilih produk
2. Pilih metode: Transfer
3. Masukkan bayar
4. Klik "Bayar & Cetak"
5. **Expected**: Transaksi berhasil

### Test 5.2: Validasi Jurnal Transfer

```sql
SELECT jed.*, coa.name
FROM journal_entry_details jed
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE journal_entry_id = [last_id];
```

**Expected**: Debit ke akun Bank (bukan Kas)

## 6️⃣ Test Transaksi Bon (Piutang)

### Test 6.1: Transaksi Bon

1. Pilih produk
2. Pilih customer
3. Centang "Bon (Piutang)"
4. Klik "Bayar & Cetak"
5. **Expected**: Transaksi berhasil

### Test 6.2: Validasi Piutang

```sql
SELECT * FROM piutang
WHERE id_penjualan = [last_penjualan_id];
```

**Expected**:

-   Piutang terbuat
-   Status: belum_lunas
-   Total piutang = total transaksi
-   Jatuh tempo = tanggal + 30 hari

### Test 6.3: Validasi Jurnal Bon

```sql
SELECT jed.*, coa.name
FROM journal_entry_details jed
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE journal_entry_id = [last_id];
```

**Expected**:

-   Debit: Piutang Usaha
-   Credit: Pendapatan Penjualan

## 7️⃣ Test Hold Order

### Test 7.1: Hold Order

1. Tambah produk ke keranjang
2. Klik "Tahan"
3. **Expected**: Alert "Order ditahan", keranjang kosong

### Test 7.2: Ambil Hold Order

1. Klik "Ambil Tahanan"
2. Pilih order yang ditahan
3. Klik "Ambil"
4. **Expected**: Order kembali ke keranjang

### Test 7.3: Hapus Hold Order

1. Klik "Ambil Tahanan"
2. Klik "Hapus" pada order
3. **Expected**: Order hilang dari daftar

## 8️⃣ Test Riwayat Transaksi

### Test 8.1: Akses Riwayat

1. Akses `/penjualan/pos/history`
2. **Expected**: DataTable muncul dengan data transaksi

### Test 8.2: Filter Outlet

1. Pilih outlet dari dropdown
2. **Expected**: Data filter sesuai outlet

### Test 8.3: Filter Status

1. Pilih status "Lunas"
2. **Expected**: Hanya transaksi lunas yang muncul

### Test 8.4: Filter Tanggal

1. Pilih tanggal mulai dan akhir
2. **Expected**: Data filter sesuai range tanggal

### Test 8.5: View Detail

1. Klik tombol "Detail" pada transaksi
2. **Expected**: Modal detail muncul dengan info lengkap

### Test 8.6: Print Struk

1. Klik tombol "Print" pada transaksi
2. **Expected**: Window baru terbuka dengan struk, auto print

## 9️⃣ Test Multi Outlet

### Test 9.1: Ganti Outlet

1. Pilih outlet berbeda dari dropdown
2. **Expected**: Produk reload sesuai outlet

### Test 9.2: Stok Per Outlet

1. Pilih outlet A
2. Cek stok produk X
3. Pilih outlet B
4. Cek stok produk X
5. **Expected**: Stok berbeda per outlet

### Test 9.3: Setting COA Per Outlet

1. Akses setting COA
2. Pilih outlet berbeda
3. **Expected**: Setting berbeda per outlet

## 🔟 Test Error Handling

### Test 10.1: Stok Habis

1. Pilih produk dengan stok 0
2. **Expected**: Produk tidak muncul di grid

### Test 10.2: Qty Melebihi Stok

1. Tambah produk dengan stok 5
2. Coba ubah qty jadi 10
3. **Expected**: Alert "Qty melebihi stok"

### Test 10.3: Bayar Kurang

1. Total: Rp 100.000
2. Bayar: Rp 50.000
3. Klik "Bayar & Cetak"
4. **Expected**: Tombol disabled atau alert error

### Test 10.4: Keranjang Kosong

1. Keranjang kosong
2. Klik "Bayar & Cetak"
3. **Expected**: Alert "Keranjang masih kosong"

### Test 10.5: Setting COA Belum Lengkap

1. Hapus setting COA
2. Lakukan transaksi
3. **Expected**: Transaksi tersimpan, tapi jurnal tidak dibuat

## 1️⃣1️⃣ Test Performance

### Test 11.1: Load Time

1. Akses POS
2. Check network tab
3. **Expected**: Halaman load < 2 detik

### Test 11.2: Transaksi Speed

1. Lakukan transaksi
2. Check response time
3. **Expected**: Response < 1 detik

### Test 11.3: DataTable Load

1. Akses riwayat dengan 1000+ data
2. **Expected**: DataTable load dengan pagination

## 1️⃣2️⃣ Test Security

### Test 12.1: Permission Check

1. Login sebagai user tanpa permission POS
2. Akses `/penjualan/pos`
3. **Expected**: Redirect atau 403 Forbidden

### Test 12.2: CSRF Token

1. Coba submit transaksi tanpa CSRF token
2. **Expected**: Error 419

### Test 12.3: SQL Injection

1. Coba input SQL di search box
2. **Expected**: Tidak ada error, query aman

## ✅ Checklist Testing

-   [ ] Setup & Configuration (4 tests)
-   [ ] Setting COA (3 tests)
-   [ ] POS Interface (5 tests)
-   [ ] Transaksi Cash (9 tests)
-   [ ] Transaksi Transfer (2 tests)
-   [ ] Transaksi Bon (3 tests)
-   [ ] Hold Order (3 tests)
-   [ ] Riwayat Transaksi (6 tests)
-   [ ] Multi Outlet (3 tests)
-   [ ] Error Handling (5 tests)
-   [ ] Performance (3 tests)
-   [ ] Security (3 tests)

**Total Tests**: 49 test cases

## 📊 Test Report Template

```
=== POS Testing Report ===
Date: [Date]
Tester: [Name]
Environment: [Dev/Staging/Production]

Setup & Configuration: ✅ PASS / ❌ FAIL
Setting COA: ✅ PASS / ❌ FAIL
POS Interface: ✅ PASS / ❌ FAIL
Transaksi Cash: ✅ PASS / ❌ FAIL
Transaksi Transfer: ✅ PASS / ❌ FAIL
Transaksi Bon: ✅ PASS / ❌ FAIL
Hold Order: ✅ PASS / ❌ FAIL
Riwayat Transaksi: ✅ PASS / ❌ FAIL
Multi Outlet: ✅ PASS / ❌ FAIL
Error Handling: ✅ PASS / ❌ FAIL
Performance: ✅ PASS / ❌ FAIL
Security: ✅ PASS / ❌ FAIL

Overall Status: ✅ PASS / ❌ FAIL
Notes: [Any issues or observations]
```

---

**Happy Testing! 🧪**
