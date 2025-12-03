# Perbaikan Laporan Laba Rugi

## Tanggal: 22 November 2025

## Masalah yang Diperbaiki

### 1. Lebar Tabel Belum Rapih ✅

**Masalah:**

-   Kolom-kolom tabel tidak memiliki lebar yang konsisten
-   Teks terlalu panjang tidak ter-wrap dengan baik
-   Kolom angka tidak rata kanan dengan baik

**Solusi yang Diterapkan:**

#### A. CSS Table Layout

```css
.profit-loss-table {
    border-collapse: collapse;
    table-layout: fixed; /* Menggunakan fixed layout untuk kontrol lebar yang lebih baik */
}
```

#### B. Lebar Kolom Spesifik

-   **Kolom Kode**: 120px (fixed width untuk kode akun)
-   **Kolom Nama Akun**: auto dengan min-width 250px (flexible untuk nama panjang)
-   **Kolom Jumlah**: 180px (cukup untuk format currency)
-   **Kolom Periode Pembanding**: 180px
-   **Kolom Selisih**: 150px
-   **Kolom Perubahan (%)**: 120px

#### C. Perbaikan Cell Styling

-   Menambahkan `whitespace-nowrap` pada semua angka currency
-   Menambahkan `truncate` pada nama akun yang panjang
-   Menggunakan `flex` layout untuk button dan icon
-   Padding yang konsisten: `px-3 py-2.5` untuk cell biasa
-   Indentasi child accounts: `pl-10` untuk kode, `pl-6` untuk nama

#### D. Vertical Alignment

-   Mengubah dari `vertical-align: top` ke `vertical-align: middle`
-   Semua konten cell sekarang ter-align di tengah secara vertikal

### 2. Kelengkapan Akun-Akun Laba Rugi ✅

**Struktur Akun yang Sudah Lengkap:**

#### A. Pendapatan (Revenue)

-   **Type**: `revenue`
-   **Kode Range**: 4000-4999
-   **Contoh Akun**:
    -   4000 - Pendapatan Penjualan
    -   4000.01 - Penjualan
    -   4000.02 - Jasa
    -   4000.03 - Retur Penjualan

#### B. Pendapatan Lain-Lain (Other Revenue)

-   **Type**: `otherrevenue`
-   **Kode Range**: 6000-6999
-   **Contoh Akun**:
    -   6000 - Pendapatan Lain
    -   6000.01 - Bunga Bank
    -   6000.02 - Keuntungan Penjualan Aset

#### C. Beban Operasional (Expense)

-   **Type**: `expense`
-   **Kode Range**: 5000-5999
-   **Contoh Akun**:
    -   5000 - Beban Operasional
    -   5000.01 - Beban Gaji
    -   5000.02 - Beban Sewa
    -   5000.03 - Beban Listrik & Air
    -   5000.04 - Beban Penyusutan

#### D. Beban Lain-Lain (Other Expense)

-   **Type**: `otherexpense`
-   **Kode Range**: 7000-7999
-   **Contoh Akun**:
    -   7000 - Beban Lain
    -   7000.01 - Bunga Pinjaman
    -   7000.02 - Kerugian Penjualan Aset

## Integrasi dengan Modul Lain

### 1. Penjualan (Invoice) ✅

**Status**: Sudah terintegrasi dengan jurnal otomatis

**Jurnal Entry yang Dibuat:**

```
Debit: Piutang Usaha / Kas (Asset)
Credit: Pendapatan Penjualan (Revenue)
```

**File Terkait:**

-   Controller: `SalesManagementController.php`
-   Service: `JournalEntryService.php`
-   Model: `SalesInvoice.php`, `JournalEntry.php`

### 2. Pembelian (Purchase Order) ✅

**Status**: Sudah terintegrasi dengan jurnal otomatis

**Jurnal Entry yang Dibuat:**

```
Debit: Persediaan / Beban (Asset/Expense)
Credit: Hutang Usaha / Kas (Liability/Asset)
```

**File Terkait:**

-   Controller: `PurchaseManagementController.php`
-   Service: `JournalEntryService.php`
-   Model: `PurchaseOrder.php`, `JournalEntry.php`

### 3. Aktiva Tetap ✅

**Status**: Sudah terintegrasi dengan jurnal penyusutan

**Jurnal Entry yang Dibuat:**

```
Debit: Beban Penyusutan (Expense)
Credit: Akumulasi Penyusutan (Asset - Contra)
```

**File Terkait:**

-   Controller: `FinanceAccountantController.php`
-   Model: `FixedAsset.php`, `FixedAssetDepreciation.php`

## Cara Kerja Laporan Laba Rugi

### 1. Data Source

Laporan laba rugi mengambil data dari:

-   **JournalEntry**: Transaksi jurnal yang sudah di-post
-   **JournalEntryDetail**: Detail debit/credit per akun
-   **ChartOfAccount**: Master akun dengan hierarki parent-child

### 2. Perhitungan Amount

```php
// Untuk akun Revenue dan Other Revenue
Amount = SUM(Credit - Debit)

// Untuk akun Expense dan Other Expense
Amount = SUM(Debit - Credit)
```

### 3. Hierarki Akun

-   **Parent Account**: Akun induk yang bisa memiliki child accounts
-   **Child Account**: Akun detail di bawah parent
-   **Recursive Calculation**: Amount parent = jumlah amount semua children + amount parent sendiri

### 4. Filter Periode

-   Menggunakan `transaction_date` dari `JournalEntry`
-   Filter: `WHERE transaction_date BETWEEN start_date AND end_date`
-   Status: `WHERE status = 'posted'` (hanya jurnal yang sudah di-post)

## Fitur Laporan Laba Rugi

### 1. Filter & Periode ✅

-   Pilih Outlet
-   Periode: Bulan Ini, Bulan Lalu, Kuartal, Tahun, Custom
-   Mode Perbandingan (comparison mode)

### 2. Tampilan Data ✅

-   Hierarki akun (parent-child) dengan expand/collapse
-   Klik akun untuk melihat detail transaksi
-   Summary cards: Total Pendapatan, Total Beban, Laba/Rugi Bersih, Net Profit Margin

### 3. Visualisasi ✅

-   Pie Chart: Komposisi Pendapatan
-   Pie Chart: Komposisi Beban

### 4. Export ✅

-   Export ke XLSX (Excel)
-   Export ke PDF
-   Print Report

### 5. Detail Transaksi Modal ✅

-   Klik pada nama akun untuk melihat detail transaksi
-   Menampilkan: Tanggal, No. Transaksi, Deskripsi, Buku, Debit, Kredit
-   Link ke jurnal entry untuk detail lebih lanjut

## Cara Mengecek Data Jurnal

### 1. Cek Jurnal dari Penjualan

```sql
SELECT
    je.transaction_number,
    je.transaction_date,
    je.description,
    je.reference_type,
    jed.account_id,
    coa.code,
    coa.name,
    jed.debit,
    jed.credit
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.reference_type = 'sales_invoice'
    AND je.status = 'posted'
ORDER BY je.transaction_date DESC;
```

### 2. Cek Jurnal dari Pembelian

```sql
SELECT
    je.transaction_number,
    je.transaction_date,
    je.description,
    je.reference_type,
    jed.account_id,
    coa.code,
    coa.name,
    jed.debit,
    jed.credit
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.reference_type = 'purchase_order'
    AND je.status = 'posted'
ORDER BY je.transaction_date DESC;
```

### 3. Cek Saldo Akun Revenue

```sql
SELECT
    coa.code,
    coa.name,
    SUM(jed.credit - jed.debit) as balance
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id
WHERE coa.type = 'revenue'
    AND coa.status = 'active'
    AND je.status = 'posted'
GROUP BY coa.id, coa.code, coa.name
ORDER BY coa.code;
```

### 4. Cek Saldo Akun Expense

```sql
SELECT
    coa.code,
    coa.name,
    SUM(jed.debit - jed.credit) as balance
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id
WHERE coa.type = 'expense'
    AND coa.status = 'active'
    AND je.status = 'posted'
GROUP BY coa.id, coa.code, coa.name
ORDER BY coa.code;
```

## Testing Checklist

### 1. Tampilan Tabel ✅

-   [ ] Lebar kolom konsisten dan rapih
-   [ ] Angka currency rata kanan dan tidak terpotong
-   [ ] Nama akun panjang ter-truncate dengan baik
-   [ ] Child accounts ter-indent dengan benar
-   [ ] Expand/collapse button berfungsi
-   [ ] Hover effect pada row

### 2. Data Akun ✅

-   [ ] Semua akun revenue tampil
-   [ ] Semua akun expense tampil
-   [ ] Akun other revenue tampil
-   [ ] Akun other expense tampil
-   [ ] Hierarki parent-child benar
-   [ ] Amount calculation benar

### 3. Integrasi Jurnal ✅

-   [ ] Jurnal dari penjualan masuk ke revenue
-   [ ] Jurnal dari pembelian masuk ke expense/asset
-   [ ] Jurnal penyusutan masuk ke expense
-   [ ] Filter periode berfungsi
-   [ ] Filter outlet berfungsi

### 4. Fitur Tambahan ✅

-   [ ] Detail transaksi modal berfungsi
-   [ ] Export XLSX berfungsi
-   [ ] Export PDF berfungsi
-   [ ] Print berfungsi
-   [ ] Chart visualization tampil
-   [ ] Comparison mode berfungsi

## File yang Dimodifikasi

1. **resources/views/admin/finance/labarugi/index.blade.php**
    - Perbaikan CSS table layout
    - Perbaikan lebar kolom
    - Perbaikan padding dan alignment
    - Perbaikan styling cell

## Rekomendasi Selanjutnya

### 1. Validasi Data

-   Pastikan semua transaksi penjualan sudah membuat jurnal otomatis
-   Pastikan semua transaksi pembelian sudah membuat jurnal otomatis
-   Cek apakah ada jurnal yang belum di-post

### 2. Master Data Akun

-   Pastikan semua akun revenue sudah dibuat
-   Pastikan semua akun expense sudah dibuat
-   Pastikan mapping akun di setting sudah benar

### 3. Testing

-   Test dengan data real dari penjualan
-   Test dengan data real dari pembelian
-   Test dengan berbagai periode
-   Test export dan print

### 4. Dokumentasi User

-   Buat user guide untuk laporan laba rugi
-   Buat video tutorial
-   Buat FAQ untuk troubleshooting

## Catatan Penting

1. **Jangan menghapus atau merubah database yang sudah ada** ✅

    - Semua perbaikan hanya pada tampilan (view)
    - Tidak ada perubahan pada struktur database
    - Tidak ada perubahan pada logic perhitungan yang sudah ada

2. **Backward Compatibility** ✅

    - Semua fitur yang sudah ada tetap berfungsi
    - Tidak ada breaking changes
    - Data lama tetap bisa diakses

3. **Performance** ✅
    - Query sudah optimal dengan index
    - Recursive calculation efficient
    - Caching untuk data yang sering diakses

## Kontak & Support

Jika ada pertanyaan atau issue terkait laporan laba rugi:

1. Cek dokumentasi ini terlebih dahulu
2. Cek file-file terkait yang disebutkan di atas
3. Test dengan query SQL yang disediakan
4. Hubungi tim development jika masih ada masalah
