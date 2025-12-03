# Implementasi Halaman Neraca (Balance Sheet)

## Ringkasan

Halaman Neraca telah berhasil diimplementasikan untuk modul Finance ERP baru. Neraca menampilkan posisi keuangan perusahaan pada tanggal tertentu dengan struktur Aset = Kewajiban + Ekuitas.

## Fitur yang Diimplementasikan

### 1. Halaman Neraca

-   **Path**: `/finance/neraca`
-   **View**: `resources/views/admin/finance/neraca/index.blade.php`
-   **Controller**: `FinanceAccountantController@neracaIndex`

### 2. Fitur Utama

✅ **Tampilan Hierarki Akun**

-   Menampilkan akun parent dan child dengan indentasi
-   Struktur dua kolom: Aset di kiri, Kewajiban & Ekuitas di kanan
-   Saldo otomatis terakumulasi dari child ke parent

✅ **Filter**

-   Filter berdasarkan Outlet
-   Filter berdasarkan Tanggal Neraca (end date)

✅ **Detail Transaksi Akun (Klik untuk Detail)**

-   Setiap akun dapat diklik untuk melihat detail transaksi
-   Modal menampilkan:
    -   Daftar transaksi jurnal yang mempengaruhi akun
    -   Total Debit dan Kredit
    -   Saldo Opening dan Current Balance
    -   Jumlah transaksi

✅ **Laba Ditahan (Retained Earnings)**

-   Otomatis dihitung dari Laporan Laba Rugi
-   Formula: (Pendapatan + Pendapatan Lain) - (Beban + Beban Lain)
-   Ditampilkan sebagai bagian dari Ekuitas

✅ **Balance Check**

-   Validasi otomatis: Total Aset = Total Kewajiban + Ekuitas
-   Alert jika neraca tidak balance dengan menampilkan selisih

✅ **Export**

-   Export ke PDF (format landscape, siap cetak)
-   Export ke XLSX (Excel) dengan formatting

✅ **Print**

-   Fungsi print langsung dari browser
-   Layout optimized untuk cetak

## Struktur File

### Backend

```
app/Http/Controllers/FinanceAccountantController.php
├── neracaIndex()                      # Halaman utama
├── neracaData()                       # API get data neraca
├── getNeracaAccountDetails()          # API detail transaksi akun
├── exportNeracaPDF()                  # Export PDF
├── exportNeracaXLSX()                 # Export Excel
├── getAccountsByType()                # Helper: ambil akun by type
├── buildAccountHierarchy()            # Helper: build hierarki
├── calculateAccountBalanceUpToDate()  # Helper: hitung saldo
├── calculateRetainedEarnings()        # Helper: hitung laba ditahan
└── flattenAccountForExport()          # Helper: flatten untuk export
```

### Frontend

```
resources/views/admin/finance/neraca/
├── index.blade.php    # Halaman utama dengan Alpine.js
└── pdf.blade.php      # Template PDF
```

### Export

```
app/Exports/NeracaExport.php           # Excel export class
app/Services/FinanceExportService.php  # Updated untuk support neraca
```

### Routes

```php
// routes/web.php
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('neraca', [FinanceAccountantController::class, 'neracaIndex'])
        ->name('neraca.index');
    Route::get('neraca/data', [FinanceAccountantController::class, 'neracaData'])
        ->name('neraca.data');
    Route::get('neraca/account-details/{id}', [FinanceAccountantController::class, 'getNeracaAccountDetails'])
        ->name('neraca.account-details');
    Route::get('neraca/export/pdf', [FinanceAccountantController::class, 'exportNeracaPDF'])
        ->name('neraca.export.pdf');
    Route::get('neraca/export/xlsx', [FinanceAccountantController::class, 'exportNeracaXLSX'])
        ->name('neraca.export.xlsx');
});
```

## Integrasi dengan Sistem

### 1. Data Source

-   **Chart of Accounts**: Mengambil akun dari tabel `chart_of_accounts`
-   **Journal Entries**: Menghitung saldo dari `journal_entries` dan `journal_entry_details`
-   **Opening Balance**: Menggunakan `opening_balances` untuk saldo awal
-   **Retained Earnings**: Dihitung dari akun revenue dan expense

### 2. Perhitungan Saldo

```php
Saldo Akun = Opening Balance + (Total Debit - Total Kredit dari Jurnal)
```

### 3. Struktur Neraca

```
ASET
├── Aset Lancar
│   ├── Kas
│   ├── Bank
│   └── Piutang
└── Aset Tetap
    ├── Tanah
    └── Bangunan

KEWAJIBAN
├── Kewajiban Lancar
│   ├── Hutang Usaha
│   └── Hutang Pajak
└── Kewajiban Jangka Panjang

EKUITAS
├── Modal
├── Laba Ditahan (dari Laporan Laba Rugi)
└── Prive
```

## Cara Penggunaan

### 1. Akses Halaman

```
URL: http://your-domain/finance/neraca
```

### 2. Filter Data

1. Pilih Outlet dari dropdown
2. Pilih Tanggal Neraca (default: hari ini)
3. Data akan otomatis ter-load

### 3. Lihat Detail Transaksi

1. Klik pada nama akun yang ingin dilihat detailnya
2. Modal akan muncul menampilkan:
    - Daftar transaksi jurnal
    - Summary (Total Debit, Kredit, Saldo)
    - Informasi akun

### 4. Export Data

1. Klik tombol "Export" di header
2. Pilih format:
    - **XLSX**: Untuk analisis di Excel
    - **PDF**: Untuk cetak atau arsip

### 5. Print

1. Klik tombol "Print"
2. Browser akan membuka dialog print
3. Pilih printer atau Save as PDF

## Catatan Teknis

### 1. Perhitungan Balance

-   Sistem otomatis memvalidasi: `Total Aset = Total Kewajiban + Ekuitas`
-   Jika tidak balance, akan muncul warning dengan selisih

### 2. Laba Ditahan

-   Dihitung real-time dari transaksi revenue dan expense
-   Tidak disimpan di database, selalu dihitung on-the-fly
-   Formula: `Net Income = Revenue - Expense`

### 3. Hierarki Akun

-   Parent account: saldo = sum of children
-   Child account: saldo = dari transaksi jurnal
-   Mendukung multi-level hierarchy

### 4. Performance

-   Query dioptimasi dengan eager loading
-   Limit 100 transaksi terakhir untuk detail modal
-   Caching bisa ditambahkan jika diperlukan

## Testing

### Manual Testing Checklist

-   [ ] Pilih outlet dan tanggal
-   [ ] Verifikasi total aset = kewajiban + ekuitas
-   [ ] Klik akun untuk lihat detail transaksi
-   [ ] Export ke XLSX dan verifikasi data
-   [ ] Export ke PDF dan verifikasi format
-   [ ] Test print functionality
-   [ ] Test dengan outlet berbeda
-   [ ] Test dengan tanggal berbeda
-   [ ] Verifikasi laba ditahan sesuai dengan laporan laba rugi

## Integrasi dengan Modul Lain

### 1. Laporan Laba Rugi

-   Laba ditahan di Neraca = Net Income dari Laba Rugi
-   Kedua laporan harus konsisten

### 2. Jurnal Umum

-   Setiap transaksi jurnal mempengaruhi saldo akun di Neraca
-   Link ke jurnal umum dari detail transaksi akun

### 3. Buku Besar

-   Neraca menggunakan data yang sama dengan Buku Besar
-   Saldo di Neraca harus match dengan Buku Besar

## Troubleshooting

### Neraca Tidak Balance

**Penyebab**:

-   Ada transaksi jurnal yang tidak balance (debit ≠ kredit)
-   Opening balance tidak sesuai
-   Ada akun yang tidak ter-mapping dengan benar

**Solusi**:

1. Cek jurnal yang tidak balance
2. Validasi opening balance
3. Pastikan semua akun memiliki type yang benar

### Saldo Tidak Sesuai

**Penyebab**:

-   Transaksi jurnal belum di-post
-   Filter tanggal tidak sesuai
-   Ada transaksi yang ter-void

**Solusi**:

1. Pastikan semua jurnal sudah di-post
2. Cek filter tanggal
3. Verifikasi status transaksi

## Future Enhancements

### Potensial Improvements

1. **Comparative Balance Sheet**: Perbandingan 2 periode
2. **Trend Analysis**: Grafik perubahan posisi keuangan
3. **Drill-down**: Dari neraca ke jurnal langsung
4. **Export to Multiple Formats**: Word, CSV, JSON
5. **Email Report**: Kirim neraca via email otomatis
6. **Scheduled Reports**: Generate neraca otomatis per periode
7. **Multi-Currency**: Support multiple currencies
8. **Consolidation**: Konsolidasi neraca multi-outlet

## Kesimpulan

Halaman Neraca telah berhasil diimplementasikan dengan fitur lengkap:

-   ✅ Tampilan hierarki akun yang jelas
-   ✅ Detail transaksi per akun (klik untuk detail)
-   ✅ Perhitungan laba ditahan otomatis
-   ✅ Balance validation
-   ✅ Export PDF & Excel
-   ✅ Print functionality
-   ✅ Responsive design
-   ✅ Terintegrasi dengan modul finance lainnya

Sistem siap digunakan untuk monitoring posisi keuangan perusahaan secara real-time.
