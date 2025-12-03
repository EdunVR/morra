# Implementasi Neraca Saldo (Trial Balance)

## 📋 Ringkasan

Fitur Neraca Saldo telah berhasil diimplementasikan untuk modul Finance ERP baru. Neraca Saldo menampilkan ringkasan saldo debit dan kredit dari semua akun dalam periode tertentu.

## ✅ Fitur yang Diimplementasikan

### 1. **Halaman Neraca Saldo**

-   **Path**: `resources/views/admin/finance/neraca-saldo/index.blade.php`
-   **Route**: `/finance/neraca-saldo`
-   **Fitur**:
    -   Filter berdasarkan Outlet, Buku, Tanggal Mulai, dan Tanggal Akhir
    -   Tampilan tabel dengan kolom: Kode Akun, Nama Akun, Tipe, Saldo Awal, Debit, Kredit, Saldo Akhir
    -   Summary cards menampilkan Total Debit, Total Kredit, dan Selisih
    -   Indikator status seimbang/tidak seimbang
    -   Setiap baris akun dapat diklik untuk melihat detail transaksi

### 2. **Detail Transaksi Akun (Modal)**

-   Modal popup menampilkan detail transaksi untuk akun yang dipilih
-   Informasi yang ditampilkan:
    -   Total Transaksi
    -   Total Debit
    -   Total Kredit
    -   Saldo
    -   Daftar transaksi dengan tanggal, nomor transaksi, deskripsi, buku, debit, dan kredit

### 3. **Export ke PDF**

-   **Path**: `resources/views/admin/finance/neraca-saldo/pdf.blade.php`
-   **Route**: `/finance/trial-balance/export/pdf`
-   **Fitur**:
    -   Header dengan nama perusahaan dan periode
    -   Informasi outlet dan buku
    -   Tabel neraca saldo dengan styling profesional
    -   Summary box dengan total debit, kredit, selisih, dan status
    -   Footer dengan tanggal cetak

### 4. **Export ke Excel**

-   **Class**: `app/Exports/TrialBalanceExport.php`
-   **Route**: `/finance/trial-balance/export/excel`
-   **Fitur**:
    -   Header dengan informasi lengkap
    -   Tabel data dengan formatting angka
    -   Baris total dengan styling bold
    -   Section ringkasan di bawah tabel
    -   Column width yang optimal

## 🔧 Backend Implementation

### Controller Methods (FinanceAccountantController.php)

#### 1. `trialBalanceData(Request $request): JsonResponse`

Mengambil data neraca saldo berdasarkan filter:

-   `outlet_id`: Filter berdasarkan outlet
-   `book_id`: Filter berdasarkan buku akuntansi
-   `start_date`: Tanggal mulai periode
-   `end_date`: Tanggal akhir periode

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "1-1000",
            "name": "Kas",
            "type": "asset",
            "level": 1,
            "opening_balance": 1000000,
            "debit": 500000,
            "credit": 200000,
            "ending_balance": 1300000,
            "normal_balance": "debit"
        }
    ],
    "summary": {
        "total_debit": 500000,
        "total_credit": 200000,
        "difference": 300000,
        "is_balanced": true
    }
}
```

#### 2. `calculateTrialBalanceForAccount($accountId, $outletId, $startDate, $endDate, $bookId)`

Menghitung saldo awal, debit, kredit, dan saldo akhir untuk akun tertentu.

#### 3. `exportTrialBalancePDF(Request $request)`

Export neraca saldo ke format PDF.

#### 4. `exportTrialBalanceXLSX(Request $request)`

Export neraca saldo ke format Excel.

## 🛣️ Routes

```php
// Data API
Route::get('trial-balance/data', [FinanceAccountantController::class, 'trialBalanceData'])
    ->name('trial-balance.data');

// Export
Route::get('trial-balance/export/pdf', [FinanceAccountantController::class, 'exportTrialBalancePDF'])
    ->name('trial-balance.export.pdf');
Route::get('trial-balance/export/excel', [FinanceAccountantController::class, 'exportTrialBalanceXLSX'])
    ->name('trial-balance.export.excel');

// View
Route::view('/neraca-saldo', 'admin.finance.neraca-saldo.index')
    ->name('neraca-saldo.index');
```

## 📊 Integrasi dengan Modul Lain

### 1. **Jurnal Umum**

-   Setiap akun di neraca saldo dapat diklik untuk melihat detail transaksi
-   Detail transaksi menggunakan endpoint yang sama dengan Buku Besar: `/finance/general-ledger/account-details`

### 2. **Buku Akuntansi**

-   Filter buku akuntansi tersedia untuk melihat neraca saldo per buku

### 3. **Outlet**

-   Filter outlet tersedia untuk melihat neraca saldo per outlet

## 🎨 UI/UX Features

### Design Pattern

Mengikuti pola yang sama dengan modul finance lainnya:

-   Gradient header dengan icon
-   Card-based summary
-   Modern table dengan hover effects
-   Modal untuk detail transaksi
-   Export buttons di header

### Color Coding

-   **Debit**: Hijau (`text-green-600`)
-   **Kredit**: Merah (`text-red-600`)
-   **Seimbang**: Hijau dengan checkmark
-   **Tidak Seimbang**: Merah dengan warning icon

### Account Type Badges

-   **Aset**: Biru (`bg-blue-100 text-blue-800`)
-   **Kewajiban**: Merah (`bg-red-100 text-red-800`)
-   **Ekuitas**: Ungu (`bg-purple-100 text-purple-800`)
-   **Pendapatan**: Hijau (`bg-green-100 text-green-800`)
-   **Beban**: Orange (`bg-orange-100 text-orange-800`)

## 🧪 Testing Guide

### 1. **Akses Halaman**

```
URL: http://your-domain/finance/neraca-saldo
```

### 2. **Test Filter**

-   Pilih outlet berbeda
-   Pilih buku berbeda
-   Ubah tanggal mulai dan akhir
-   Pastikan data berubah sesuai filter

### 3. **Test Detail Transaksi**

-   Klik pada baris akun
-   Modal harus muncul dengan detail transaksi
-   Pastikan data transaksi sesuai dengan akun yang dipilih

### 4. **Test Export PDF**

-   Klik tombol "PDF" di header
-   PDF harus terbuka di tab baru
-   Pastikan data dan formatting sesuai

### 5. **Test Export Excel**

-   Klik tombol "Excel" di header
-   File Excel harus terdownload
-   Buka file dan pastikan data lengkap dengan formatting

### 6. **Test Validasi Seimbang**

-   Pastikan total debit = total kredit (atau selisih sangat kecil < 0.01)
-   Status "Seimbang" harus muncul jika balanced
-   Status "Tidak Seimbang" muncul jika ada selisih

## 📝 Catatan Penting

### 1. **Perhitungan Saldo**

-   Saldo Awal: Akumulasi transaksi sebelum tanggal mulai
-   Debit/Kredit: Total transaksi dalam periode
-   Saldo Akhir: Saldo Awal + Debit - Kredit

### 2. **Normal Balance**

-   Aset & Beban: Normal balance di Debit
-   Kewajiban, Ekuitas, & Pendapatan: Normal balance di Kredit

### 3. **Status Posted**

-   Hanya transaksi dengan status "posted" yang dihitung
-   Draft atau pending tidak termasuk dalam perhitungan

### 4. **Precision**

-   Selisih < 0.01 dianggap seimbang (untuk mengatasi floating point precision)

## 🔗 Menu Navigation

Menu "Neraca Saldo" telah ditambahkan di sidebar Finance, posisi setelah "Buku Besar" dan sebelum "Neraca Lajur".

## 📦 Files Created/Modified

### Created:

1. `resources/views/admin/finance/neraca-saldo/index.blade.php`
2. `resources/views/admin/finance/neraca-saldo/pdf.blade.php`
3. `app/Exports/TrialBalanceExport.php`
4. `NERACA_SALDO_IMPLEMENTATION.md`

### Modified:

1. `app/Http/Controllers/FinanceAccountantController.php` - Added trial balance methods
2. `app/Services/FinanceExportService.php` - Added neraca-saldo support
3. `routes/web.php` - Added trial balance routes
4. `resources/views/components/sidebar.blade.php` - Added menu item

## ✨ Next Steps (Optional Enhancements)

1. **Komparasi Periode**: Tambahkan fitur untuk membandingkan neraca saldo antar periode
2. **Drill-down**: Tambahkan kemampuan untuk drill-down ke level akun yang lebih detail
3. **Chart Visualization**: Tambahkan grafik untuk visualisasi distribusi saldo
4. **Auto-refresh**: Tambahkan auto-refresh untuk data real-time
5. **Print Preview**: Tambahkan preview sebelum print/export

## 🎯 Kesimpulan

Fitur Neraca Saldo telah berhasil diimplementasikan dengan lengkap, mengikuti pola dan standar yang sama dengan modul finance lainnya. Fitur ini terintegrasi dengan baik dengan modul Jurnal, Buku Besar, dan sistem akuntansi secara keseluruhan.
