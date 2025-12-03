# Quick Start Guide - Point of Sales (POS)

## 🚀 Setup Cepat (5 Menit)

### 1. Jalankan Migration & Seeder

```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder permission
php artisan db:seed --class=PosPermissionSeeder
```

### 2. Berikan Permission ke User

Tambahkan permission berikut ke user Anda (via menu User Management):

-   ✅ `POS`
-   ✅ `POS Create`
-   ✅ `POS View`
-   ✅ `POS History`
-   ✅ `POS Settings`

### 3. Setting COA (Wajib!)

1. Login ke sistem
2. Buka menu **Penjualan > Point of Sales**
3. Klik link **Setting COA** atau akses: `/penjualan/pos/coa-settings`
4. Isi konfigurasi:
    - **Buku Akuntansi**: Pilih buku yang aktif
    - **Akun Kas**: Contoh: `1101 - Kas`
    - **Akun Bank**: Contoh: `1102 - Bank`
    - **Akun Piutang Usaha**: Contoh: `1103 - Piutang Usaha`
    - **Akun Pendapatan Penjualan**: Contoh: `4101 - Pendapatan Penjualan`
    - **Akun HPP**: Contoh: `5101 - HPP` (opsional)
    - **Akun Persediaan**: Contoh: `1201 - Persediaan` (opsional)
5. Klik **Simpan Setting**

### 4. Mulai Transaksi!

1. Buka menu **Penjualan > Point of Sales**
2. Pilih produk dari grid
3. Atur qty dan diskon jika perlu
4. Pilih metode pembayaran
5. Masukkan jumlah bayar
6. Klik **Bayar & Cetak**

## 📋 Contoh Transaksi

### Transaksi Cash

```
1. Pilih produk: Briket Kayu 25kg (Rp 80.000)
2. Qty: 2
3. Subtotal: Rp 160.000
4. Diskon: 5% = Rp 8.000
5. Total: Rp 152.000
6. Metode: Cash
7. Bayar: Rp 200.000
8. Kembali: Rp 48.000
9. Klik "Bayar & Cetak"
```

**Jurnal yang terbuat:**

```
Debit: Kas Rp 152.000
Credit: Pendapatan Penjualan Rp 152.000

Debit: HPP Rp 120.000 (contoh)
Credit: Persediaan Rp 120.000
```

### Transaksi Bon (Piutang)

```
1. Pilih produk: Briket Kayu 10kg (Rp 35.000)
2. Qty: 10
3. Subtotal: Rp 350.000
4. Pilih customer: CV Nusantara
5. Centang "Bon (Piutang)"
6. Klik "Bayar & Cetak"
```

**Jurnal yang terbuat:**

```
Debit: Piutang Usaha Rp 350.000
Credit: Pendapatan Penjualan Rp 350.000

Debit: HPP Rp 250.000 (contoh)
Credit: Persediaan Rp 250.000
```

**Piutang:**

-   Jatuh tempo: 30 hari dari tanggal transaksi
-   Status: Belum Lunas
-   Dapat dilihat di menu Finance > Piutang

## 🎯 Fitur Utama

### 1. Search & Scan

-   **Search**: Ketik nama/SKU produk, tekan Enter
-   **Scan Barcode**: Scan barcode produk, otomatis masuk keranjang

### 2. Diskon

-   **Diskon Nominal**: Masukkan nilai rupiah
-   **Diskon Persen**: Masukkan persen (0-100)
-   Bisa kombinasi keduanya

### 3. PPN

-   Centang "PPN 10%" untuk menambah pajak 10%
-   PPN dihitung dari subtotal setelah diskon

### 4. Hold Order

-   Klik **Tahan** untuk simpan order sementara
-   Klik **Ambil Tahanan** untuk melanjutkan order
-   Berguna saat melayani multiple customer

### 5. Multi Outlet

-   Pilih outlet dari dropdown
-   Produk dan stok otomatis berubah sesuai outlet
-   Setting COA per outlet

## 📊 Melihat Riwayat

1. Akses `/penjualan/pos/history`
2. Filter berdasarkan:
    - Outlet
    - Status (Lunas/Menunggu)
    - Tanggal
3. Klik **Detail** untuk melihat detail transaksi
4. Klik **Print** untuk cetak ulang struk

## ⚠️ Troubleshooting Cepat

### Jurnal tidak terbuat?

✅ Pastikan setting COA sudah dikonfigurasi lengkap

### Produk tidak muncul?

✅ Pastikan produk memiliki stok > 0 untuk outlet yang dipilih

### Error saat transaksi?

✅ Cek console browser (F12) untuk error detail
✅ Cek `storage/logs/laravel.log`

### Menu POS tidak muncul?

✅ Pastikan user memiliki permission `POS`

## 📞 Support

Jika ada masalah, cek dokumentasi lengkap di `POS_IMPLEMENTATION_COMPLETE.md`

---

**Happy Selling! 🎉**
