# RAB Quick Start Guide

## 🚀 Mulai Cepat (5 Menit)

### 1. Jalankan Migration

```bash
php artisan migrate
```

### 2. Clear Cache

```bash
php artisan route:clear
```

### 3. Akses Halaman

1. Login ke ERP
2. Klik menu "Keuangan (F&A)"
3. Klik "Manajemen RAB"

### 4. Tambah RAB Pertama

1. Klik tombol "Tambah RAB"
2. Isi form:
    - **Tanggal**: Pilih tanggal hari ini
    - **Nama Template**: "RAB Testing"
    - **Deskripsi**: "RAB untuk testing"
    - **Komponen**: Klik "Tambah Komponen", isi "Komponen A"
    - **Budget Total**: 5000000
    - **Nilai Disetujui**: 4500000
    - **Status**: Draft
    - **Produk Terkait**: Tidak
3. Klik "Simpan"

### 5. Verifikasi

-   Data muncul di tabel
-   Bisa edit, lihat detail, dan hapus
-   Filter & search berfungsi

## ✅ Selesai!

RAB sudah siap digunakan. Untuk panduan lengkap, lihat:

-   `RAB_INTEGRATION_COMPLETE.md` - Dokumentasi lengkap
-   `RAB_TESTING_GUIDE.md` - Panduan testing
-   `RAB_API_REFERENCE.md` - API reference

## 🆘 Troubleshooting

### Error: Route not defined

```bash
php artisan route:clear
php artisan config:clear
```

### Error: Column not found

```bash
php artisan migrate
```

### Data tidak muncul

1. Buka Developer Tools (F12)
2. Cek Console untuk error
3. Cek Network tab untuk API response

## 📞 Need Help?

Lihat dokumentasi lengkap di folder project atau hubungi tim development.
