# User Guide - Modul Aktiva Tetap

## Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Cara Menambah Aset Baru](#cara-menambah-aset-baru)
3. [Cara Menghitung dan Posting Penyusutan](#cara-menghitung-dan-posting-penyusutan)
4. [Cara Melepas Aset](#cara-melepas-aset)
5. [Cara Membaca Laporan](#cara-membaca-laporan)
6. [Tips dan Best Practices](#tips-dan-best-practices)
7. [Troubleshooting](#troubleshooting)

---

## Pengenalan

Modul Aktiva Tetap adalah fitur dalam sistem ERP yang membantu Anda mengelola aset tetap perusahaan secara terintegrasi dengan sistem akuntansi. Setiap transaksi aktiva tetap (perolehan, penyusutan, pelepasan) akan otomatis mencatat jurnal akuntansi yang sesuai.

### Fitur Utama

-   **Pencatatan Perolehan Aset**: Mencatat pembelian aset baru dengan jurnal otomatis
-   **Perhitungan Penyusutan**: Mendukung berbagai metode penyusutan (garis lurus, saldo menurun, dll)
-   **Pelepasan Aset**: Mencatat penjualan atau penghapusan aset dengan perhitungan gain/loss
-   **Laporan Terintegrasi**: Laporan nilai buku, akumulasi penyusutan, dan referensi jurnal
-   **Multi-Outlet**: Pengelolaan aset per cabang/outlet

### Konsep Dasar

-   **Nilai Perolehan (Acquisition Cost)**: Harga beli aset ditambah biaya-biaya terkait
-   **Nilai Residu (Salvage Value)**: Perkiraan nilai aset di akhir masa manfaat
-   **Masa Manfaat (Useful Life)**: Estimasi berapa tahun aset dapat digunakan
-   **Nilai Buku (Book Value)**: Nilai perolehan dikurangi akumulasi penyusutan
-   **Akumulasi Penyusutan**: Total penyusutan yang telah dicatat sejak perolehan

---

## Cara Menambah Aset Baru

### Langkah 1: Persiapan Data

Sebelum menambah aset baru, pastikan Anda memiliki informasi berikut:

1. **Informasi Aset**

    - Kode aset (akan di-generate otomatis atau bisa diisi manual)
    - Nama aset
    - Kategori (Tanah, Gedung, Kendaraan, Peralatan, Furniture, Komputer)
    - Lokasi fisik aset

2. **Informasi Keuangan**

    - Tanggal perolehan
    - Nilai perolehan (harga beli + biaya terkait)
    - Nilai residu (estimasi nilai akhir)
    - Masa manfaat (dalam tahun)
    - Metode penyusutan yang akan digunakan

3. **Akun-Akun Terkait**
    - Akun Aset Tetap (tipe: Asset)
    - Akun Beban Penyusutan (tipe: Expense)
    - Akun Akumulasi Penyusutan (tipe: Asset - Contra Asset)
    - Akun Pembayaran (Kas/Bank/Hutang)

### Langkah 2: Membuka Form Tambah Aset

1. Login ke sistem ERP
2. Navigasi ke menu **Finance** → **Aktiva Tetap**
3. Klik tombol **"+ Tambah Aset"** di pojok kanan atas

### Langkah 3: Mengisi Form

#### Tab Informasi Dasar

1. **Outlet**: Pilih outlet/cabang tempat aset berada
2. **Kode Aset**:
    - Klik tombol "Generate" untuk membuat kode otomatis (format: AST-YYYYMM-XXX)
    - Atau isi manual sesuai kebijakan perusahaan
3. **Nama Aset**: Masukkan nama deskriptif aset (contoh: "Mobil Toyota Avanza 2024")
4. **Kategori**: Pilih kategori yang sesuai
5. **Lokasi**: Masukkan lokasi fisik aset (contoh: "Kantor Pusat - Lantai 2")
6. **Deskripsi**: Tambahkan informasi detail (opsional)

#### Tab Informasi Keuangan

1. **Tanggal Perolehan**: Pilih tanggal pembelian/perolehan aset
2. **Nilai Perolehan**: Masukkan total biaya perolehan (harga beli + biaya instalasi + biaya lainnya)
3. **Nilai Residu**: Masukkan estimasi nilai aset di akhir masa manfaat
    - Untuk tanah, biasanya nilai residu = nilai perolehan (tidak disusutkan)
    - Untuk aset lain, biasanya 5-10% dari nilai perolehan
4. **Masa Manfaat**: Masukkan estimasi umur ekonomis dalam tahun
    - Gedung: 20-30 tahun
    - Kendaraan: 5-8 tahun
    - Peralatan: 5-10 tahun
    - Komputer: 3-5 tahun
5. **Metode Penyusutan**: Pilih metode yang sesuai
    - **Garis Lurus (Straight Line)**: Penyusutan sama setiap periode - paling umum digunakan
    - **Saldo Menurun (Declining Balance)**: Penyusutan lebih besar di awal
    - **Saldo Menurun Ganda (Double Declining)**: Penyusutan sangat besar di awal

#### Tab Akun Akuntansi

1. **Akun Aset**: Pilih akun untuk mencatat nilai aset (contoh: "1301 - Gedung")
2. **Akun Beban Penyusutan**: Pilih akun untuk mencatat beban penyusutan (contoh: "6101 - Beban Penyusutan Gedung")
3. **Akun Akumulasi Penyusutan**: Pilih akun kontra aset (contoh: "1302 - Akumulasi Penyusutan Gedung")
4. **Akun Pembayaran**: Pilih akun sumber dana (contoh: "1101 - Kas" atau "2101 - Hutang Usaha")

> **Catatan Penting**: Pastikan akun yang dipilih memiliki tipe yang sesuai. Sistem akan memvalidasi tipe akun secara otomatis.

### Langkah 4: Menyimpan dan Verifikasi

1. Klik tombol **"Simpan"**
2. Sistem akan:

    - Memvalidasi semua input
    - Membuat record aset baru
    - Membuat jurnal perolehan otomatis:
        ```
        Debit: Akun Aset = Nilai Perolehan
        Credit: Akun Pembayaran = Nilai Perolehan
        ```
    - Memposting jurnal secara otomatis
    - Memperbarui saldo akun terkait

3. Jika berhasil, akan muncul notifikasi sukses dan aset baru akan muncul di daftar
4. Klik pada aset untuk melihat detail dan jurnal yang telah dibuat

### Contoh Kasus: Pembelian Kendaraan

**Skenario**: Perusahaan membeli mobil operasional seharga Rp 250.000.000 secara tunai pada 15 November 2024.

**Data Input**:

-   Kode: AST-202411-001
-   Nama: Mobil Toyota Avanza 2024
-   Kategori: Kendaraan
-   Tanggal Perolehan: 15 November 2024
-   Nilai Perolehan: Rp 250.000.000
-   Nilai Residu: Rp 25.000.000 (10%)
-   Masa Manfaat: 8 tahun
-   Metode: Garis Lurus
-   Akun Aset: 1401 - Kendaraan
-   Akun Beban Penyusutan: 6102 - Beban Penyusutan Kendaraan
-   Akun Akumulasi Penyusutan: 1402 - Akumulasi Penyusutan Kendaraan
-   Akun Pembayaran: 1101 - Kas

**Jurnal yang Dibuat**:

```
Tanggal: 15 November 2024
No. Transaksi: FA-ACQ-AST-202411-001

Debit: 1401 - Kendaraan = Rp 250.000.000
Credit: 1101 - Kas = Rp 250.000.000
```

**Penyusutan Bulanan**: Rp 2.343.750 per bulan

```
(250.000.000 - 25.000.000) / 8 tahun / 12 bulan = Rp 2.343.750
```

---

## Cara Menghitung dan Posting Penyusutan

### Metode 1: Perhitungan Manual per Aset

#### Langkah 1: Buka Detail Aset

1. Dari daftar aktiva tetap, klik pada aset yang ingin dihitung penyusutannya
2. Scroll ke bagian **"Riwayat Penyusutan"**

#### Langkah 2: Hitung Penyusutan

1. Klik tombol **"Hitung Penyusutan"**
2. Pilih periode (bulan dan tahun)
3. Sistem akan menghitung nilai penyusutan berdasarkan metode yang dipilih
4. Status penyusutan akan menjadi **"Draft"**

#### Langkah 3: Review dan Post

1. Review nilai penyusutan yang dihitung
2. Jika sudah sesuai, klik tombol **"Post"** pada baris penyusutan
3. Sistem akan:
    - Membuat jurnal penyusutan
    - Memposting jurnal otomatis
    - Mengupdate nilai buku aset
    - Mengupdate akumulasi penyusutan
    - Mengubah status menjadi **"Posted"**

### Metode 2: Batch Processing (Untuk Semua Aset)

#### Langkah 1: Buka Menu Batch Penyusutan

1. Dari halaman Aktiva Tetap, klik tombol **"Batch Penyusutan"**
2. Akan muncul modal dialog

#### Langkah 2: Pilih Periode

1. Pilih **Bulan** (contoh: November)
2. Pilih **Tahun** (contoh: 2024)
3. Pilih **Outlet** (atau pilih "Semua Outlet")

#### Langkah 3: Pilih Mode Proses

**Mode 1: Hitung Saja**

-   Centang: "Hanya Hitung (Draft)"
-   Sistem akan menghitung penyusutan untuk semua aset aktif
-   Status akan menjadi "Draft"
-   Anda bisa review satu per satu sebelum posting

**Mode 2: Hitung dan Post Otomatis**

-   Centang: "Hitung dan Post Otomatis"
-   Sistem akan menghitung dan langsung memposting semua penyusutan
-   Jurnal akan dibuat otomatis untuk semua aset
-   Proses lebih cepat untuk closing bulanan

#### Langkah 4: Jalankan Proses

1. Klik tombol **"Proses"**
2. Sistem akan menampilkan progress bar
3. Tunggu hingga proses selesai
4. Akan muncul summary:
    - Jumlah aset yang diproses
    - Total nilai penyusutan
    - Jumlah jurnal yang dibuat
    - Daftar error (jika ada)

### Contoh Jurnal Penyusutan

Untuk aset mobil di contoh sebelumnya, jurnal penyusutan bulan Desember 2024:

```
Tanggal: 31 Desember 2024
No. Transaksi: FA-DEP-AST-202411-001-1

Debit: 6102 - Beban Penyusutan Kendaraan = Rp 2.343.750
Credit: 1402 - Akumulasi Penyusutan Kendaraan = Rp 2.343.750
```

### Reverse Penyusutan (Pembatalan)

Jika terjadi kesalahan dan perlu membatalkan penyusutan yang sudah diposting:

1. Buka detail aset
2. Di riwayat penyusutan, cari entry yang ingin dibatalkan
3. Klik tombol **"Reverse"**
4. Konfirmasi pembatalan
5. Sistem akan:
    - Membuat jurnal pembalik (debit-credit dibalik)
    - Mengupdate status menjadi "Reversed"
    - Mengembalikan nilai buku aset
    - Mengurangi akumulasi penyusutan

> **Peringatan**: Reverse penyusutan hanya untuk koreksi kesalahan. Gunakan dengan hati-hati dan pastikan sudah mendapat approval.

---

## Cara Melepas Aset

### Kapan Melepas Aset?

Aset perlu dilepas (disposal) ketika:

-   Dijual ke pihak lain
-   Ditukar dengan aset lain (trade-in)
-   Rusak dan tidak dapat digunakan lagi
-   Hilang atau dicuri
-   Dihibahkan

### Langkah 1: Persiapan

Sebelum melepas aset, pastikan:

1. Semua penyusutan sampai bulan pelepasan sudah dicatat
2. Nilai buku aset sudah akurat
3. Anda memiliki informasi:
    - Tanggal pelepasan
    - Nilai pelepasan (harga jual atau nilai penggantian)
    - Catatan/alasan pelepasan

### Langkah 2: Buka Form Pelepasan

1. Dari daftar aktiva tetap, cari aset yang akan dilepas
2. Klik tombol **"Actions"** → **"Lepas Aset"**
3. Akan muncul modal form pelepasan

### Langkah 3: Isi Data Pelepasan

1. **Tanggal Pelepasan**: Pilih tanggal transaksi pelepasan
2. **Nilai Pelepasan**: Masukkan nilai jual atau nilai penggantian
    - Untuk penjualan: masukkan harga jual
    - Untuk penghapusan: masukkan 0
    - Untuk asuransi: masukkan nilai klaim
3. **Catatan**: Jelaskan alasan dan detail pelepasan

### Langkah 4: Review Gain/Loss

Sistem akan otomatis menghitung dan menampilkan:

**Rumus**:

```
Gain/Loss = Nilai Pelepasan - Nilai Buku
```

**Contoh**:

-   Nilai Buku saat ini: Rp 200.000.000
-   Nilai Pelepasan: Rp 220.000.000
-   **Gain (Keuntungan)**: Rp 20.000.000

Atau:

-   Nilai Buku saat ini: Rp 200.000.000
-   Nilai Pelepasan: Rp 180.000.000
-   **Loss (Kerugian)**: Rp 20.000.000

### Langkah 5: Konfirmasi dan Simpan

1. Review semua informasi
2. Klik tombol **"Lepas Aset"**
3. Sistem akan:
    - Membuat jurnal pelepasan
    - Mencatat gain atau loss
    - Mengupdate status aset menjadi "Sold" atau "Disposed"
    - Memposting jurnal otomatis

### Contoh Jurnal Pelepasan

**Kasus 1: Penjualan dengan Keuntungan**

Mobil dijual Rp 220.000.000, nilai buku Rp 200.000.000, akumulasi penyusutan Rp 50.000.000

```
Tanggal: 15 November 2024
No. Transaksi: FA-DSP-AST-202411-001

Debit: 1101 - Kas = Rp 220.000.000
Debit: 1402 - Akumulasi Penyusutan Kendaraan = Rp 50.000.000
Credit: 1401 - Kendaraan = Rp 250.000.000
Credit: 8101 - Keuntungan Pelepasan Aset = Rp 20.000.000
```

**Kasus 2: Penjualan dengan Kerugian**

Mobil dijual Rp 180.000.000, nilai buku Rp 200.000.000, akumulasi penyusutan Rp 50.000.000

```
Tanggal: 15 November 2024
No. Transaksi: FA-DSP-AST-202411-001

Debit: 1101 - Kas = Rp 180.000.000
Debit: 1402 - Akumulasi Penyusutan Kendaraan = Rp 50.000.000
Debit: 9101 - Kerugian Pelepasan Aset = Rp 20.000.000
Credit: 1401 - Kendaraan = Rp 250.000.000
```

**Kasus 3: Penghapusan (Rusak Total)**

Aset rusak total, tidak ada nilai pelepasan

```
Tanggal: 15 November 2024
No. Transaksi: FA-DSP-AST-202411-001

Debit: 1402 - Akumulasi Penyusutan Kendaraan = Rp 50.000.000
Debit: 9101 - Kerugian Pelepasan Aset = Rp 200.000.000
Credit: 1401 - Kendaraan = Rp 250.000.000
```

---

## Cara Membaca Laporan

### 1. Dashboard Aktiva Tetap

Dashboard menampilkan ringkasan keseluruhan:

#### Statistik Utama

-   **Total Aset**: Jumlah semua aset yang tercatat
-   **Aset Aktif**: Jumlah aset yang masih digunakan
-   **Total Nilai Perolehan**: Total biaya perolehan semua aset
-   **Total Penyusutan**: Total akumulasi penyusutan
-   **Total Nilai Buku**: Nilai buku saat ini (Perolehan - Penyusutan)
-   **Tingkat Penyusutan**: Persentase penyusutan terhadap nilai perolehan

#### Grafik Distribusi Aset

-   Menampilkan distribusi aset berdasarkan kategori
-   Membantu melihat komposisi aset perusahaan
-   Klik pada segmen untuk melihat detail

#### Grafik Trend Nilai Aset

-   Menampilkan perbandingan nilai perolehan vs nilai buku dari waktu ke waktu
-   Membantu melihat trend penyusutan
-   Berguna untuk perencanaan penggantian aset

### 2. Daftar Aktiva Tetap

Tabel daftar menampilkan semua aset dengan kolom:

-   **Kode**: Kode unik aset
-   **Nama**: Nama aset
-   **Kategori**: Kategori aset
-   **Tanggal Perolehan**: Kapan aset diperoleh
-   **Nilai Perolehan**: Biaya perolehan awal
-   **Akumulasi Penyusutan**: Total penyusutan sampai saat ini
-   **Nilai Buku**: Nilai saat ini (Perolehan - Akumulasi)
-   **Status**: Active, Inactive, Sold, Disposed
-   **Actions**: Tombol untuk edit, detail, lepas, dll

#### Filter dan Pencarian

Gunakan filter untuk mempersempit data:

-   **Outlet**: Filter berdasarkan cabang
-   **Kategori**: Filter berdasarkan jenis aset
-   **Status**: Filter berdasarkan status
-   **Search**: Cari berdasarkan kode atau nama

### 3. Detail Aset

Halaman detail menampilkan informasi lengkap:

#### Informasi Umum

-   Data master aset
-   Informasi keuangan
-   Akun-akun terkait
-   Status dan lokasi

#### Riwayat Penyusutan

Tabel menampilkan:

-   **Periode**: Periode penyusutan (1, 2, 3, ...)
-   **Tanggal**: Tanggal penyusutan
-   **Nilai Penyusutan**: Nilai penyusutan periode ini
-   **Akumulasi**: Total penyusutan sampai periode ini
-   **Nilai Buku**: Nilai buku setelah penyusutan
-   **Status**: Draft, Posted, Reversed
-   **No. Jurnal**: Link ke jurnal terkait
-   **Actions**: Tombol Post atau Reverse

#### Jurnal Terkait

Daftar semua jurnal yang terkait dengan aset:

-   Jurnal perolehan
-   Jurnal penyusutan
-   Jurnal pelepasan (jika ada)

Klik pada nomor jurnal untuk melihat detail jurnal lengkap.

### 4. Laporan Riwayat Penyusutan

Laporan ini menampilkan semua transaksi penyusutan:

#### Filter Laporan

-   **Periode**: Pilih bulan dan tahun
-   **Aset**: Pilih aset tertentu atau semua
-   **Status**: Draft, Posted, Reversed
-   **Outlet**: Filter berdasarkan cabang

#### Kolom Laporan

-   Kode dan nama aset
-   Periode penyusutan
-   Tanggal
-   Nilai penyusutan
-   Akumulasi penyusutan
-   Nilai buku
-   Status
-   Nomor jurnal

#### Export Laporan

Klik tombol **"Export"** untuk download laporan dalam format:

-   **Excel**: Untuk analisis lebih lanjut
-   **PDF**: Untuk print atau arsip

### 5. Interpretasi Laporan

#### Nilai Buku vs Nilai Pasar

-   Nilai buku adalah nilai akuntansi (perolehan - penyusutan)
-   Nilai pasar bisa berbeda dengan nilai buku
-   Gunakan nilai buku untuk laporan keuangan
-   Gunakan nilai pasar untuk keputusan jual/beli

#### Tingkat Penyusutan

```
Tingkat Penyusutan = (Akumulasi Penyusutan / Nilai Perolehan) × 100%
```

-   0-25%: Aset masih baru
-   25-50%: Aset setengah umur
-   50-75%: Aset sudah tua, pertimbangkan penggantian
-   75-100%: Aset mendekati akhir masa manfaat

#### Analisis Umur Aset

Lihat distribusi aset berdasarkan umur untuk:

-   Perencanaan penggantian aset
-   Budgeting pembelian aset baru
-   Evaluasi kebijakan penyusutan

---

## Tips dan Best Practices

### 1. Penamaan Aset

Gunakan konvensi penamaan yang konsisten:

```
[Kategori] [Merk/Tipe] [Tahun] [Lokasi]
```

Contoh:

-   "Kendaraan Toyota Avanza 2024 Jakarta"
-   "Komputer Dell Latitude 5420 Kantor Pusat"
-   "Gedung Kantor 3 Lantai Surabaya"

### 2. Kode Aset

Gunakan sistem kode yang terstruktur:

```
AST-[YYYYMM]-[XXX]
```

Atau buat sistem sendiri:

```
[Kategori]-[Outlet]-[Sequence]
```

Contoh:

-   VHC-JKT-001 (Vehicle - Jakarta - 001)
-   BLD-SBY-001 (Building - Surabaya - 001)

### 3. Dokumentasi

Simpan dokumen pendukung:

-   Faktur pembelian
-   Bukti pembayaran
-   Sertifikat/BPKB
-   Foto aset
-   Kontrak pemeliharaan

Upload atau link dokumen di field "Deskripsi" atau sistem DMS perusahaan.

### 4. Review Berkala

Lakukan review rutin:

-   **Bulanan**: Pastikan penyusutan sudah dicatat
-   **Triwulanan**: Review kondisi fisik aset
-   **Tahunan**: Evaluasi nilai residu dan masa manfaat

### 5. Pemisahan Tugas

Implementasikan segregation of duties:

-   **Input**: Staff admin mencatat perolehan aset
-   **Review**: Supervisor mereview data
-   **Approval**: Manager menyetujui
-   **Posting**: Akuntan memposting penyusutan

### 6. Backup Data

Pastikan data aset di-backup secara berkala:

-   Export data ke Excel setiap bulan
-   Simpan di lokasi yang aman
-   Dokumentasikan perubahan penting

### 7. Rekonsiliasi

Lakukan rekonsiliasi berkala:

-   Nilai buku di sistem vs laporan keuangan
-   Daftar aset di sistem vs fisik di lapangan
-   Akumulasi penyusutan vs jurnal

### 8. Pelatihan

Pastikan semua user memahami:

-   Cara menggunakan sistem
-   Konsep akuntansi aktiva tetap
-   Kebijakan perusahaan terkait aset

---

## Troubleshooting

### Masalah 1: Tidak Bisa Menyimpan Aset Baru

**Gejala**: Muncul error saat menyimpan aset

**Penyebab dan Solusi**:

1. **"Akun aset harus memiliki tipe 'asset'"**

    - Pastikan akun yang dipilih memiliki tipe yang benar
    - Cek di master Chart of Accounts
    - Hubungi admin untuk membuat akun baru jika perlu

2. **"Nilai residu tidak boleh lebih besar dari nilai perolehan"**

    - Periksa kembali nilai yang diinput
    - Nilai residu harus lebih kecil dari nilai perolehan

3. **"Kode aset sudah digunakan"**
    - Gunakan kode yang berbeda
    - Atau klik "Generate" untuk kode otomatis

### Masalah 2: Penyusutan Tidak Bisa Diposting

**Gejala**: Tombol "Post" tidak berfungsi atau muncul error

**Penyebab dan Solusi**:

1. **"Penyusutan sudah diposting sebelumnya"**

    - Cek status penyusutan
    - Jika sudah "Posted", tidak bisa diposting lagi
    - Jika perlu koreksi, gunakan "Reverse"

2. **"Gagal membuat jurnal"**
    - Cek koneksi database
    - Pastikan akun-akun masih aktif
    - Hubungi IT support jika masalah berlanjut

### Masalah 3: Tidak Bisa Menghapus Aset

**Gejala**: Muncul error saat menghapus aset

**Penyebab dan Solusi**:

1. **"Tidak dapat menghapus aset yang sudah memiliki jurnal terposting"**

    - Aset yang sudah memiliki jurnal tidak bisa dihapus
    - Gunakan fitur "Nonaktifkan" untuk menonaktifkan aset
    - Atau gunakan "Lepas Aset" jika aset sudah tidak digunakan

2. **"Aset memiliki riwayat penyusutan"**
    - Hapus atau reverse semua penyusutan terlebih dahulu
    - Atau gunakan fitur nonaktifkan

### Masalah 4: Nilai Penyusutan Tidak Sesuai

**Gejala**: Nilai penyusutan yang dihitung tidak sesuai ekspektasi

**Penyebab dan Solusi**:

1. **Metode penyusutan salah**

    - Periksa metode yang dipilih
    - Ganti metode jika perlu (sebelum ada penyusutan posted)

2. **Nilai residu atau masa manfaat salah**

    - Periksa kembali data aset
    - Update jika perlu (sebelum ada penyusutan posted)

3. **Perhitungan declining balance**
    - Metode ini menghasilkan nilai berbeda setiap periode
    - Nilai penyusutan akan menurun seiring waktu
    - Ini adalah perilaku normal

### Masalah 5: Gain/Loss Pelepasan Tidak Sesuai

**Gejala**: Nilai gain/loss saat pelepasan tidak sesuai

**Penyebab dan Solusi**:

1. **Penyusutan belum lengkap**

    - Pastikan penyusutan sampai bulan pelepasan sudah dicatat
    - Hitung dan post penyusutan yang kurang

2. **Nilai buku tidak akurat**
    - Cek riwayat penyusutan
    - Pastikan semua penyusutan sudah posted
    - Lakukan rekonsiliasi

### Masalah 6: Laporan Tidak Muncul

**Gejala**: Laporan kosong atau tidak ada data

**Penyebab dan Solusi**:

1. **Filter terlalu ketat**

    - Reset filter
    - Coba dengan filter yang lebih luas

2. **Tidak ada data untuk periode tersebut**

    - Pilih periode yang berbeda
    - Pastikan ada aset yang tercatat

3. **Masalah permission**
    - Pastikan user memiliki akses ke outlet yang dipilih
    - Hubungi admin untuk setting permission

### Kontak Support

Jika masalah tidak terselesaikan:

1. **IT Support**: Untuk masalah teknis sistem
2. **Finance Team**: Untuk pertanyaan akuntansi
3. **Admin**: Untuk masalah akses dan permission

Sertakan informasi berikut saat menghubungi support:

-   Screenshot error
-   Langkah-langkah yang sudah dilakukan
-   Data aset terkait (kode, nama)
-   User ID dan outlet

---

## Lampiran

### A. Daftar Kategori Aset dan Masa Manfaat Umum

| Kategori  | Masa Manfaat     | Metode Umum                     |
| --------- | ---------------- | ------------------------------- |
| Tanah     | Tidak disusutkan | -                               |
| Gedung    | 20-30 tahun      | Garis Lurus                     |
| Kendaraan | 5-8 tahun        | Garis Lurus / Declining Balance |
| Peralatan | 5-10 tahun       | Garis Lurus                     |
| Furniture | 5-8 tahun        | Garis Lurus                     |
| Komputer  | 3-5 tahun        | Garis Lurus / Double Declining  |

### B. Contoh Chart of Accounts untuk Aktiva Tetap

```
1300 - AKTIVA TETAP
  1301 - Tanah
  1302 - Akumulasi Penyusutan Tanah (tidak digunakan)
  1311 - Gedung
  1312 - Akumulasi Penyusutan Gedung
  1321 - Kendaraan
  1322 - Akumulasi Penyusutan Kendaraan
  1331 - Peralatan
  1332 - Akumulasi Penyusutan Peralatan
  1341 - Furniture & Fixtures
  1342 - Akumulasi Penyusutan Furniture
  1351 - Komputer & Elektronik
  1352 - Akumulasi Penyusutan Komputer

6100 - BEBAN PENYUSUTAN
  6101 - Beban Penyusutan Gedung
  6102 - Beban Penyusutan Kendaraan
  6103 - Beban Penyusutan Peralatan
  6104 - Beban Penyusutan Furniture
  6105 - Beban Penyusutan Komputer

8100 - PENDAPATAN LAIN-LAIN
  8101 - Keuntungan Pelepasan Aset

9100 - BEBAN LAIN-LAIN
  9101 - Kerugian Pelepasan Aset
```

### C. Checklist Closing Bulanan

-   [ ] Hitung penyusutan untuk semua aset aktif
-   [ ] Review dan post semua penyusutan draft
-   [ ] Cek ada aset baru yang perlu dicatat
-   [ ] Cek ada aset yang perlu dilepas
-   [ ] Rekonsiliasi nilai buku dengan general ledger
-   [ ] Review laporan penyusutan
-   [ ] Export dan backup data
-   [ ] Dokumentasi perubahan penting

### D. Referensi Standar Akuntansi

Modul ini mengikuti prinsip akuntansi yang berlaku umum:

-   PSAK 16: Aset Tetap
-   PSAK 48: Penurunan Nilai Aset
-   Peraturan perpajakan terkait penyusutan fiskal

Konsultasikan dengan akuntan atau auditor untuk kebijakan spesifik perusahaan Anda.

---

**Versi**: 1.0  
**Tanggal**: November 2024  
**Penyusun**: Tim Development ERP

Untuk pertanyaan atau saran perbaikan dokumentasi, hubungi tim development.
