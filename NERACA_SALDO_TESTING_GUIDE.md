# Panduan Testing Neraca Saldo

## 🚀 Cara Mengakses

1. Login ke sistem ERP
2. Buka menu **Finance** di sidebar
3. Klik **Neraca Saldo**
4. Atau akses langsung: `http://your-domain/finance/neraca-saldo`

## ✅ Checklist Testing

### 1. Tampilan Halaman ✓

-   [ ] Halaman terbuka tanpa error
-   [ ] Header dengan judul "Neraca Saldo" tampil
-   [ ] 3 Summary cards tampil (Total Debit, Total Kredit, Selisih)
-   [ ] Filter outlet, buku, tanggal mulai, dan tanggal akhir tampil
-   [ ] Tabel dengan 7 kolom tampil
-   [ ] Tombol Export PDF dan Excel tampil di header

### 2. Filter Data ✓

-   [ ] **Filter Outlet**:
    -   Pilih outlet berbeda
    -   Data berubah sesuai outlet yang dipilih
-   [ ] **Filter Buku**:
    -   Pilih buku berbeda
    -   Data berubah sesuai buku yang dipilih
-   [ ] **Filter Tanggal**:
    -   Ubah tanggal mulai
    -   Ubah tanggal akhir
    -   Data berubah sesuai periode yang dipilih

### 3. Tampilan Data ✓

-   [ ] Kode akun tampil dengan format yang benar
-   [ ] Nama akun tampil lengkap
-   [ ] Badge tipe akun tampil dengan warna yang sesuai:
    -   Aset: Biru
    -   Kewajiban: Merah
    -   Ekuitas: Ungu
    -   Pendapatan: Hijau
    -   Beban: Orange
-   [ ] Saldo Awal tampil dengan format currency
-   [ ] Debit tampil dengan warna hijau
-   [ ] Kredit tampil dengan warna merah
-   [ ] Saldo Akhir tampil dengan format currency
-   [ ] Baris total di footer tampil dengan bold

### 4. Summary Cards ✓

-   [ ] **Total Debit**: Angka sesuai dengan total kolom debit
-   [ ] **Total Kredit**: Angka sesuai dengan total kolom kredit
-   [ ] **Selisih**: Angka = Total Debit - Total Kredit
-   [ ] **Status Seimbang**:
    -   Jika selisih < Rp 1, tampil "✓ Seimbang" dengan warna hijau
    -   Jika selisih >= Rp 1, tampil "⚠ Tidak Seimbang" dengan warna merah

### 5. Detail Transaksi (Modal) ✓

-   [ ] Klik pada baris akun
-   [ ] Modal popup muncul
-   [ ] Header modal menampilkan kode dan nama akun
-   [ ] 4 Summary cards tampil:
    -   Total Transaksi
    -   Total Debit
    -   Total Kredit
    -   Saldo
-   [ ] Tabel transaksi tampil dengan kolom:
    -   Tanggal
    -   No. Transaksi
    -   Deskripsi
    -   Buku
    -   Debit
    -   Kredit
-   [ ] Data transaksi sesuai dengan akun yang dipilih
-   [ ] Tombol "Tutup" berfungsi untuk menutup modal

### 6. Export PDF ✓

-   [ ] Klik tombol "PDF" di header
-   [ ] PDF terbuka di tab baru (tidak langsung download)
-   [ ] Header PDF menampilkan:
    -   Nama perusahaan
    -   Judul "NERACA SALDO (TRIAL BALANCE)"
    -   Periode
-   [ ] Info section menampilkan:
    -   Outlet
    -   Buku
    -   Tanggal Cetak
-   [ ] Tabel data lengkap dengan styling
-   [ ] Badge tipe akun tampil dengan warna
-   [ ] Baris total tampil dengan bold
-   [ ] Summary box tampil di bawah tabel
-   [ ] Footer tampil dengan informasi cetak

### 7. Export Excel ✓

-   [ ] Klik tombol "Excel" di header
-   [ ] File Excel terdownload otomatis
-   [ ] Buka file Excel
-   [ ] Sheet bernama "Neraca Saldo"
-   [ ] Header menampilkan:
    -   Judul "NERACA SALDO (TRIAL BALANCE)"
    -   Outlet
    -   Buku
    -   Periode
    -   Tanggal Cetak
-   [ ] Kolom header dengan background biru
-   [ ] Data lengkap dengan formatting angka
-   [ ] Baris total dengan background abu-abu dan bold
-   [ ] Section ringkasan di bawah tabel
-   [ ] Column width optimal (tidak terlalu sempit/lebar)

### 8. Validasi Data ✓

-   [ ] **Akurasi Saldo Awal**:
    -   Bandingkan dengan data jurnal sebelum periode
    -   Pastikan sesuai
-   [ ] **Akurasi Debit/Kredit**:
    -   Bandingkan dengan data jurnal dalam periode
    -   Pastikan sesuai
-   [ ] **Akurasi Saldo Akhir**:
    -   Rumus: Saldo Akhir = Saldo Awal + Debit - Kredit
    -   Pastikan perhitungan benar
-   [ ] **Total Seimbang**:
    -   Untuk sistem double-entry yang benar, Total Debit harus = Total Kredit
    -   Jika ada selisih, periksa transaksi yang tidak balance

### 9. Edge Cases ✓

-   [ ] **Tidak Ada Data**:
    -   Pilih periode tanpa transaksi
    -   Pesan "Tidak ada data" tampil
-   [ ] **Akun Tanpa Transaksi**:
    -   Akun yang tidak memiliki transaksi tidak tampil di list
-   [ ] **Saldo Negatif**:
    -   Saldo negatif tampil dengan warna merah
-   [ ] **Angka Besar**:
    -   Test dengan angka > 1 miliar
    -   Format currency tetap rapi

### 10. Performance ✓

-   [ ] Loading data < 3 detik untuk 100 akun
-   [ ] Filter response < 2 detik
-   [ ] Modal detail transaksi < 2 detik
-   [ ] Export PDF < 5 detik
-   [ ] Export Excel < 5 detik

## 🐛 Common Issues & Solutions

### Issue 1: Data Tidak Muncul

**Solusi**:

-   Pastikan ada transaksi jurnal yang sudah di-post
-   Periksa filter outlet dan periode
-   Cek console browser untuk error JavaScript

### Issue 2: Total Tidak Seimbang

**Solusi**:

-   Periksa transaksi jurnal yang tidak balance
-   Pastikan setiap jurnal memiliki total debit = total kredit
-   Jalankan validasi jurnal

### Issue 3: Export Gagal

**Solusi**:

-   Periksa log Laravel: `storage/logs/laravel.log`
-   Pastikan library DomPDF dan Maatwebsite Excel terinstall
-   Cek permission folder storage

### Issue 4: Modal Tidak Muncul

**Solusi**:

-   Periksa console browser untuk error JavaScript
-   Pastikan Alpine.js loaded
-   Clear browser cache

### Issue 5: Format Angka Salah

**Solusi**:

-   Periksa locale setting di browser
-   Pastikan fungsi `formatCurrency` bekerja
-   Test dengan browser berbeda

## 📊 Sample Test Data

### Contoh Transaksi untuk Testing:

```sql
-- Transaksi 1: Penerimaan Kas dari Modal
Debit: Kas (1-1000) = Rp 10.000.000
Kredit: Modal (3-1000) = Rp 10.000.000

-- Transaksi 2: Pembelian Peralatan
Debit: Peralatan (1-2000) = Rp 5.000.000
Kredit: Kas (1-1000) = Rp 5.000.000

-- Transaksi 3: Penjualan
Debit: Kas (1-1000) = Rp 3.000.000
Kredit: Pendapatan (4-1000) = Rp 3.000.000

-- Transaksi 4: Beban Gaji
Debit: Beban Gaji (5-1000) = Rp 2.000.000
Kredit: Kas (1-1000) = Rp 2.000.000
```

### Expected Result:

```
Kode    | Nama Akun      | Debit       | Kredit      | Saldo Akhir
--------|----------------|-------------|-------------|-------------
1-1000  | Kas            | 13.000.000  | 7.000.000   | 6.000.000
1-2000  | Peralatan      | 5.000.000   | 0           | 5.000.000
3-1000  | Modal          | 0           | 10.000.000  | -10.000.000
4-1000  | Pendapatan     | 0           | 3.000.000   | -3.000.000
5-1000  | Beban Gaji     | 2.000.000   | 0           | 2.000.000
--------|----------------|-------------|-------------|-------------
TOTAL   |                | 20.000.000  | 20.000.000  | 0
```

Status: ✓ Seimbang

## 📝 Test Report Template

```
LAPORAN TESTING NERACA SALDO
Tanggal: [DD/MM/YYYY]
Tester: [Nama]
Environment: [Development/Staging/Production]

1. Tampilan Halaman: [PASS/FAIL]
   Notes: ___________

2. Filter Data: [PASS/FAIL]
   Notes: ___________

3. Tampilan Data: [PASS/FAIL]
   Notes: ___________

4. Summary Cards: [PASS/FAIL]
   Notes: ___________

5. Detail Transaksi: [PASS/FAIL]
   Notes: ___________

6. Export PDF: [PASS/FAIL]
   Notes: ___________

7. Export Excel: [PASS/FAIL]
   Notes: ___________

8. Validasi Data: [PASS/FAIL]
   Notes: ___________

9. Edge Cases: [PASS/FAIL]
   Notes: ___________

10. Performance: [PASS/FAIL]
    Notes: ___________

OVERALL RESULT: [PASS/FAIL]
BUGS FOUND: [Jumlah]
CRITICAL ISSUES: [Jumlah]
```

## 🎯 Success Criteria

Testing dianggap berhasil jika:

1. ✅ Semua checklist terpenuhi
2. ✅ Tidak ada error di console browser
3. ✅ Tidak ada error di log Laravel
4. ✅ Data akurat dan sesuai dengan jurnal
5. ✅ Export berfungsi dengan baik
6. ✅ Performance memenuhi standar
7. ✅ UI/UX responsif dan user-friendly

## 📞 Support

Jika menemukan bug atau issue:

1. Screenshot error
2. Copy error message dari console/log
3. Catat langkah-langkah untuk reproduce
4. Laporkan ke tim development

---

**Happy Testing! 🚀**
