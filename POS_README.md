# 📦 Point of Sales (POS) - MORRA ERP

> Sistem Point of Sales terintegrasi penuh dengan modul ERP lainnya

## 📚 Dokumentasi

### 🚀 Quick Start

Baca: **[POS_QUICK_START.md](POS_QUICK_START.md)**

-   Setup dalam 5 menit
-   Contoh transaksi
-   Troubleshooting cepat

### 📖 Dokumentasi Lengkap

Baca: **[POS_IMPLEMENTATION_COMPLETE.md](POS_IMPLEMENTATION_COMPLETE.md)**

-   Fitur lengkap
-   Struktur database
-   Flow transaksi
-   Integrasi sistem

### 🧪 Testing Guide

Baca: **[POS_TESTING_GUIDE.md](POS_TESTING_GUIDE.md)**

-   49 test cases
-   Testing checklist
-   Test report template

### 📋 Summary

Baca: **[POS_SUMMARY.md](POS_SUMMARY.md)**

-   Files yang dibuat
-   Tech stack
-   Status implementasi

## ⚡ Quick Links

### Setup

```bash
# 1. Migration
php artisan migrate

# 2. Seeder
php artisan db:seed --class=PosPermissionSeeder
```

### URL Akses

-   **POS**: `/penjualan/pos`
-   **Riwayat**: `/penjualan/pos/history`
-   **Setting COA**: `/penjualan/pos/coa-settings`

### Permission Required

-   `POS` - Akses menu
-   `POS Create` - Buat transaksi
-   `POS View` - Lihat transaksi
-   `POS History` - Lihat riwayat
-   `POS Settings` - Setting COA

## 🎯 Fitur Utama

### ✅ Transaksi

-   Grid produk dengan kategori
-   Search & scan barcode
-   Diskon nominal & persen
-   PPN 10%
-   Multi metode pembayaran (Cash/Transfer/QRIS)
-   Bon/Piutang
-   Hold order

### ✅ Integrasi

-   Produk & Stok
-   Customer (Member)
-   Penjualan & Detail
-   Piutang
-   Jurnal Otomatis
-   Multi Outlet

### ✅ Laporan

-   Riwayat transaksi
-   Filter outlet, status, tanggal
-   Detail transaksi
-   Print struk thermal

## 📊 Database

### Tables

1. `pos_sales` - Header transaksi
2. `pos_sale_items` - Detail item
3. `setting_coa_pos` - Setting COA

### Relationships

```
pos_sales
├── pos_sale_items (1:N)
├── outlet (N:1)
├── member (N:1)
├── user (N:1)
├── penjualan (1:1)
└── piutang (1:1)
```

## 🔄 Flow Transaksi

### Cash/Transfer

```
Input → Validasi → Create PosSale → Create Items
→ Create Penjualan → Kurangi Stok → Create Journal
→ Success
```

### Bon (Piutang)

```
Input → Validasi → Create PosSale → Create Items
→ Create Penjualan → Kurangi Stok → Create Piutang
→ Create Journal → Success
```

## 🎨 Tech Stack

-   **Backend**: Laravel 11, PHP 8.2+
-   **Frontend**: Alpine.js, Tailwind CSS
-   **Database**: MySQL
-   **DataTable**: Yajra DataTables
-   **PDF**: DomPDF

## 📝 Files Created

### Backend (7 files)

```
app/Models/PosSale.php
app/Models/PosSaleItem.php
app/Models/SettingCOAPos.php
app/Http/Controllers/PosController.php
database/migrations/2025_11_30_create_pos_sales_tables.php
database/seeders/PosPermissionSeeder.php
routes/web.php (updated)
```

### Frontend (5 files)

```
resources/views/admin/penjualan/pos/index.blade.php
resources/views/admin/penjualan/pos/history.blade.php
resources/views/admin/penjualan/pos/print.blade.php
resources/views/admin/penjualan/pos/coa-settings.blade.php
resources/views/partials/sidebar/sales.blade.php (updated)
```

### Documentation (5 files)

```
POS_README.md (this file)
POS_QUICK_START.md
POS_IMPLEMENTATION_COMPLETE.md
POS_TESTING_GUIDE.md
POS_SUMMARY.md
```

**Total**: 17 files

## ⚠️ Important Notes

1. **Setting COA Wajib**: Konfigurasi COA sebelum transaksi pertama
2. **Permission**: User harus punya permission `POS`
3. **Stok**: Stok otomatis berkurang saat transaksi
4. **Jurnal**: Otomatis jika setting COA lengkap
5. **Piutang**: Otomatis untuk transaksi bon (jatuh tempo 30 hari)

## 🐛 Troubleshooting

### Jurnal tidak terbuat?

✅ Cek setting COA sudah lengkap

### Produk tidak muncul?

✅ Cek stok > 0 untuk outlet yang dipilih

### Menu tidak muncul?

✅ Cek permission user

### Error saat transaksi?

✅ Cek console browser (F12)
✅ Cek `storage/logs/laravel.log`

## 📞 Support

Untuk pertanyaan atau issue:

1. Cek dokumentasi lengkap
2. Cek testing guide
3. Cek log error
4. Contact developer

## ✅ Status

**Version**: 1.0.0  
**Status**: ✅ Production Ready  
**Last Update**: 30 November 2025

---

## 🎉 Ready to Use!

Fitur POS sudah lengkap dan siap digunakan. Selamat berjualan! 🚀

---

**Developed with ❤️ for MORRA ERP**
