# Panduan Testing Point of Sales (POS) - Updated

## Perbaikan yang Telah Dilakukan

### 1. ✅ Error Kolom 'stok' - FIXED

**Masalah:** Query mencoba menggunakan kolom `stok` yang tidak ada di tabel `produk`
**Solusi:**

-   Controller sudah diperbaiki untuk menggunakan accessor `getStokAttribute()` dari model Produk
-   Stok dihitung dari relasi `hppProduk` menggunakan metode FIFO
-   Filter produk menggunakan `->filter()` setelah `->get()` untuk menghindari query langsung ke kolom stok

### 2. ✅ Halaman Setting COA POS - READY

**Lokasi:** `/pos/coa-settings`
**Fitur:**

-   Pilih outlet
-   Pilih buku akuntansi
-   Konfigurasi akun:
    -   Akun Kas (untuk pembayaran cash)
    -   Akun Bank (untuk transfer/QRIS)
    -   Akun Piutang Usaha (untuk bon)
    -   Akun Pendapatan Penjualan
    -   Akun HPP (opsional)
    -   Akun Persediaan (opsional)

### 3. ✅ Gambar Produk & Barcode - IMPLEMENTED

**Fitur:**

-   Gambar produk ditampilkan di card produk
-   Jika tidak ada gambar, tampil placeholder dengan icon
-   Barcode otomatis di-generate menggunakan JsBarcode library
-   Barcode menggunakan format CODE128
-   Barcode dapat di-scan untuk quick add produk

### 4. ✅ Customer Search dengan Piutang - IMPLEMENTED

**Fitur:**

-   Search customer dengan autocomplete
-   Tampil nama, telepon, dan total piutang
-   Piutang ditampilkan dengan warna:
    -   Hijau: Tidak ada piutang
    -   Merah: Ada piutang
-   Pilih "Pelanggan Umum" untuk transaksi tanpa customer

### 5. ✅ Tombol Lunas - IMPLEMENTED

**Fitur:**

-   Tombol "💰 Lunas" untuk auto-fill jumlah bayar sesuai total
-   Otomatis hitung kembalian
-   Posisi di samping input jumlah bayar

## Cara Testing

### A. Persiapan

1. **Clear Cache**

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

2. **Pastikan Tabel Ada**

```bash
php artisan migrate
```

3. **Seed Data (jika perlu)**

```bash
php artisan db:seed --class=PosPermissionSeeder
```

### B. Testing Setting COA POS

1. Buka `/pos/coa-settings`
2. Pilih outlet
3. Pilih buku akuntansi
4. Pilih akun-akun yang diperlukan
5. Klik "Simpan Setting"
6. Pastikan muncul alert "Setting COA POS berhasil disimpan"

### C. Testing Halaman POS

1. **Buka Halaman POS**

    - URL: `/pos`
    - Pastikan tampil daftar produk dengan gambar dan barcode

2. **Test Gambar Produk**

    - Produk dengan gambar: tampil gambar
    - Produk tanpa gambar: tampil placeholder dengan icon
    - Hover pada gambar untuk melihat efek

3. **Test Barcode**

    - Setiap produk memiliki barcode di bawah gambar
    - Barcode dapat di-scan menggunakan barcode scanner
    - Atau ketik SKU di input "Scan Barcode" dan tekan Enter

4. **Test Customer Search**

    - Klik input "Customer"
    - Ketik nama atau telepon customer
    - Pilih customer dari dropdown
    - Lihat informasi piutang customer
    - Atau pilih "Pelanggan Umum"

5. **Test Tambah Produk ke Keranjang**

    - Klik card produk untuk menambah ke keranjang
    - Atau ketik SKU/nama di search dan tekan Enter
    - Atau scan barcode
    - Pastikan qty dan subtotal benar

6. **Test Tombol Lunas**

    - Tambah produk ke keranjang
    - Lihat total bayar
    - Klik tombol "💰 Lunas"
    - Pastikan jumlah bayar otomatis terisi sesuai total
    - Kembalian harus 0

7. **Test Pembayaran Cash**

    - Pilih metode "Cash"
    - Klik tombol "Lunas" atau input manual
    - Lihat kembalian
    - Klik "Bayar & Cetak"
    - Pastikan transaksi berhasil

8. **Test Pembayaran Transfer/QRIS**

    - Pilih metode "Transfer" atau "QRIS"
    - Klik tombol "Lunas"
    - Klik "Bayar & Cetak"
    - Pastikan transaksi berhasil

9. **Test Bon (Piutang)**

    - Centang checkbox "Bon (Piutang)"
    - Pilih customer (wajib untuk bon)
    - Input pembayaran akan hilang
    - Klik "Bayar & Cetak"
    - Pastikan piutang tercatat

10. **Test Diskon**

    - Input diskon nominal (Rp)
    - Atau input diskon persen (%)
    - Lihat total berubah
    - Pastikan perhitungan benar

11. **Test PPN 10%**

    - Centang "PPN 10%"
    - Lihat total bertambah 10%
    - Pastikan perhitungan benar

12. **Test Hold Order**
    - Tambah produk ke keranjang
    - Klik "Tahan"
    - Keranjang kosong
    - Klik "Ambil Tahanan"
    - Pilih order yang ditahan
    - Klik "Ambil"
    - Keranjang terisi kembali

### D. Testing Integrasi

1. **Cek Stok Berkurang**

    - Catat stok awal produk
    - Lakukan transaksi
    - Cek stok akhir
    - Pastikan stok berkurang sesuai qty

2. **Cek Jurnal Otomatis**

    - Lakukan transaksi
    - Buka menu Jurnal
    - Cari jurnal dengan keterangan "POS [no_transaksi]"
    - Pastikan jurnal tercatat dengan benar:
        - Cash: Kas (D) vs Pendapatan (K)
        - Transfer: Bank (D) vs Pendapatan (K)
        - Bon: Piutang (D) vs Pendapatan (K)
        - HPP: HPP (D) vs Persediaan (K)

3. **Cek Piutang**

    - Lakukan transaksi bon
    - Buka menu Piutang
    - Pastikan piutang tercatat
    - Cek total piutang customer

4. **Cek History**
    - Buka `/pos/history`
    - Lihat daftar transaksi
    - Filter by outlet, status, tanggal
    - Klik "Detail" untuk lihat detail
    - Klik "Print" untuk cetak struk

## Troubleshooting

### Error: Column 'stok' not found

**Solusi:** Sudah diperbaiki di controller. Clear cache dan reload.

### Gambar tidak muncul

**Solusi:**

1. Pastikan tabel `product_images` ada
2. Pastikan ada data gambar di tabel
3. Pastikan path gambar benar
4. Jalankan: `php artisan storage:link`

### Barcode tidak muncul

**Solusi:**

1. Pastikan library JsBarcode sudah di-load
2. Cek console browser untuk error
3. Pastikan SKU produk valid

### Customer search tidak muncul

**Solusi:**

1. Pastikan ada data customer di tabel `member`
2. Cek endpoint `/pos/customers` di browser
3. Cek console untuk error

### Tombol Lunas tidak berfungsi

**Solusi:**

1. Clear cache browser
2. Reload halaman
3. Cek console untuk error

### Setting COA tidak tersimpan

**Solusi:**

1. Pastikan semua field required terisi
2. Pastikan akun yang dipilih ada di outlet yang dipilih
3. Cek log error di `storage/logs/laravel.log`

## Checklist Testing

-   [ ] Setting COA POS berhasil disimpan
-   [ ] Gambar produk tampil dengan benar
-   [ ] Barcode tampil di setiap produk
-   [ ] Customer search berfungsi
-   [ ] Piutang customer tampil
-   [ ] Tombol Lunas berfungsi
-   [ ] Pembayaran Cash berhasil
-   [ ] Pembayaran Transfer berhasil
-   [ ] Pembayaran QRIS berhasil
-   [ ] Bon (Piutang) berhasil
-   [ ] Diskon nominal berfungsi
-   [ ] Diskon persen berfungsi
-   [ ] PPN 10% berfungsi
-   [ ] Hold order berfungsi
-   [ ] Resume order berfungsi
-   [ ] Stok berkurang setelah transaksi
-   [ ] Jurnal otomatis tercatat
-   [ ] Piutang tercatat (untuk bon)
-   [ ] History transaksi tampil
-   [ ] Print struk berfungsi

## Catatan Penting

1. **Stok menggunakan FIFO**: Stok dihitung dari tabel `hpp_produk` dengan metode First In First Out
2. **Gambar produk**: Menggunakan tabel `product_images` dengan field `is_primary` untuk gambar utama
3. **Barcode**: Menggunakan library JsBarcode dengan format CODE128
4. **Customer search**: Real-time search dengan autocomplete
5. **Tombol Lunas**: Auto-fill jumlah bayar sesuai total untuk mempercepat transaksi
6. **Setting COA**: Wajib diisi sebelum transaksi agar jurnal otomatis berfungsi

## File yang Diperbaiki

1. `app/Http/Controllers/PosController.php` - Fix query stok
2. `resources/views/admin/penjualan/pos/index.blade.php` - Fix routes, gambar, barcode, customer search, tombol lunas
3. `resources/views/admin/penjualan/pos/coa-settings.blade.php` - Fix routes
4. `database/migrations/2025_12_01_000001_create_product_images_table.php` - Migration untuk tabel gambar

## Next Steps

1. Test semua fitur sesuai checklist
2. Jika ada error, cek log di `storage/logs/laravel.log`
3. Jika ada bug, laporkan dengan detail error dan langkah reproduksi
4. Jika perlu tambahan fitur, diskusikan terlebih dahulu
