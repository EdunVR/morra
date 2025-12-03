# Panduan Testing Laporan Laba Rugi

## 1. Persiapan Testing

### A. Cek Data Master Akun

Jalankan query berikut untuk memastikan akun sudah lengkap:

```sql
-- Cek akun revenue
SELECT * FROM chart_of_accounts WHERE type = 'revenue' AND status = 'active';

-- Cek akun expense
SELECT * FROM chart_of_accounts WHERE type = 'expense' AND status = 'active';
```

### B. Cek Data Jurnal

Gunakan file `check_profit_loss_data.sql` untuk mengecek:

-   Jurnal dari penjualan
-   Jurnal dari pembelian
-   Jurnal penyusutan
-   Balance jurnal (debit = credit)

## 2. Testing Tampilan Tabel

### Checklist:

-   [ ] Kolom Kode: lebar 120px, rata kiri
-   [ ] Kolom Nama Akun: flexible, min 250px
-   [ ] Kolom Jumlah: lebar 180px, rata kanan
-   [ ] Angka currency tidak terpotong
-   [ ] Nama akun panjang ter-truncate
-   [ ] Child accounts ter-indent dengan benar
-   [ ] Expand/collapse button berfungsi

## 3. Testing Data & Perhitungan

### A. Test dengan Data Real

1. Buka halaman Laporan Laba Rugi
2. Pilih outlet
3. Pilih periode (misal: bulan ini)
4. Klik "Refresh" atau "Muat Data"

### B. Verifikasi Perhitungan

-   Total Pendapatan = Revenue + Other Revenue
-   Total Beban = Expense + Other Expense
-   Laba/Rugi Bersih = Total Pendapatan - Total Beban

## 4. Testing Fitur

### A. Detail Transaksi

-   Klik nama akun
-   Modal detail transaksi muncul
-   Data transaksi tampil lengkap

### B. Export & Print

-   Export XLSX: file terdownload
-   Export PDF: file terdownload
-   Print: preview print muncul

## 5. Troubleshooting

### Masalah: Data tidak muncul

**Solusi:**

1. Cek apakah ada jurnal yang sudah di-post
2. Cek filter periode
3. Cek filter outlet

### Masalah: Amount tidak sesuai

**Solusi:**

1. Cek balance jurnal (debit = credit)
2. Cek mapping akun
3. Cek status jurnal (harus 'posted')
