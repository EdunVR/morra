# Requirements Document - Laporan Laba Rugi

## Introduction

Laporan Laba Rugi (Income Statement / Profit & Loss Statement) adalah laporan keuangan yang menunjukkan kinerja keuangan perusahaan dalam periode tertentu dengan menampilkan pendapatan, beban, dan laba/rugi bersih. Fitur ini akan terintegrasi dengan sistem akuntansi yang sudah ada (Chart of Accounts, Journal Entry, General Ledger) dan menggunakan data transaksi yang telah diposting untuk menghasilkan laporan laba rugi secara otomatis.

## Glossary

-   **Laporan Laba Rugi System**: Sistem yang menghasilkan laporan laba rugi berdasarkan data jurnal yang telah diposting
-   **Revenue Account**: Akun dengan tipe 'revenue' atau 'otherrevenue' yang mencatat pendapatan
-   **Expense Account**: Akun dengan tipe 'expense' atau 'otherexpense' yang mencatat beban
-   **Net Income**: Laba bersih yang dihitung dari total pendapatan dikurangi total beban
-   **Comparative Period**: Periode pembanding untuk membandingkan kinerja periode saat ini dengan periode sebelumnya
-   **FinanceAccountantController**: Controller yang menangani operasi modul finance termasuk laporan laba rugi
-   **Posted Journal**: Jurnal dengan status 'posted' yang sudah divalidasi dan mempengaruhi saldo akun

## Requirements

### Requirement 1

**User Story:** Sebagai Finance Manager, saya ingin melihat laporan laba rugi untuk periode tertentu, sehingga saya dapat mengetahui kinerja keuangan perusahaan

#### Acceptance Criteria

1. WHEN user mengakses halaman laporan laba rugi, THE Laporan Laba Rugi System SHALL menampilkan form filter dengan pilihan outlet, periode awal, dan periode akhir
2. WHEN user memilih outlet dan periode, THE Laporan Laba Rugi System SHALL menampilkan laporan laba rugi dengan struktur: Pendapatan, Beban, dan Laba/Rugi Bersih
3. WHEN laporan ditampilkan, THE Laporan Laba Rugi System SHALL menghitung total pendapatan dari semua akun dengan tipe 'revenue' dan 'otherrevenue'
4. WHEN laporan ditampilkan, THE Laporan Laba Rugi System SHALL menghitung total beban dari semua akun dengan tipe 'expense' dan 'otherexpense'
5. WHEN laporan ditampilkan, THE Laporan Laba Rugi System SHALL menghitung laba/rugi bersih sebagai selisih antara total pendapatan dan total beban

### Requirement 2

**User Story:** Sebagai Finance Manager, saya ingin melihat detail akun dalam laporan laba rugi, sehingga saya dapat menganalisis komponen pendapatan dan beban secara detail

#### Acceptance Criteria

1. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menampilkan daftar akun pendapatan dengan kode akun, nama akun, dan jumlah
2. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menampilkan daftar akun beban dengan kode akun, nama akun, dan jumlah
3. WHEN akun memiliki child accounts, THE Laporan Laba Rugi System SHALL menampilkan parent account dengan total akumulasi dari semua child accounts
4. WHEN user mengklik akun, THE Laporan Laba Rugi System SHALL menampilkan detail transaksi yang mempengaruhi akun tersebut dalam periode yang dipilih
5. WHEN akun tidak memiliki transaksi dalam periode, THE Laporan Laba Rugi System SHALL tidak menampilkan akun tersebut dalam laporan

### Requirement 3

**User Story:** Sebagai Finance Manager, saya ingin membandingkan laporan laba rugi dengan periode sebelumnya, sehingga saya dapat melihat tren kinerja keuangan

#### Acceptance Criteria

1. WHEN user mengaktifkan mode perbandingan, THE Laporan Laba Rugi System SHALL menampilkan kolom tambahan untuk periode pembanding
2. WHEN periode pembanding ditampilkan, THE Laporan Laba Rugi System SHALL menghitung selisih nominal antara periode saat ini dan periode pembanding
3. WHEN periode pembanding ditampilkan, THE Laporan Laba Rugi System SHALL menghitung persentase perubahan antara periode saat ini dan periode pembanding
4. WHEN selisih positif, THE Laporan Laba Rugi System SHALL menampilkan indikator kenaikan dengan warna hijau
5. WHEN selisih negatif, THE Laporan Laba Rugi System SHALL menampilkan indikator penurunan dengan warna merah

### Requirement 4

**User Story:** Sebagai Finance Manager, saya ingin mengekspor laporan laba rugi ke berbagai format, sehingga saya dapat membagikan atau mencetak laporan

#### Acceptance Criteria

1. WHEN user mengklik tombol export, THE Laporan Laba Rugi System SHALL menampilkan pilihan format export (XLSX, PDF)
2. WHEN user memilih export XLSX, THE Laporan Laba Rugi System SHALL menghasilkan file Excel dengan format laporan laba rugi yang terstruktur
3. WHEN user memilih export PDF, THE Laporan Laba Rugi System SHALL menghasilkan file PDF dengan format laporan laba rugi yang siap cetak
4. WHEN export dilakukan, THE Laporan Laba Rugi System SHALL menyertakan header dengan nama outlet, periode laporan, dan tanggal generate
5. WHEN export dilakukan dengan mode perbandingan aktif, THE Laporan Laba Rugi System SHALL menyertakan data periode pembanding dalam file export

### Requirement 5

**User Story:** Sebagai Finance Manager, saya ingin melihat visualisasi grafik dari laporan laba rugi, sehingga saya dapat memahami komposisi pendapatan dan beban dengan lebih mudah

#### Acceptance Criteria

1. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menampilkan grafik pie chart untuk komposisi pendapatan berdasarkan kategori akun
2. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menampilkan grafik pie chart untuk komposisi beban berdasarkan kategori akun
3. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menampilkan grafik bar chart untuk perbandingan total pendapatan vs total beban
4. WHEN mode perbandingan aktif, THE Laporan Laba Rugi System SHALL menampilkan grafik line chart untuk tren laba/rugi bersih
5. WHEN user mengklik segmen grafik, THE Laporan Laba Rugi System SHALL menampilkan detail akun yang terkait dengan segmen tersebut

### Requirement 6

**User Story:** Sebagai Finance Manager, saya ingin sistem menghitung data laporan laba rugi hanya dari jurnal yang sudah diposting, sehingga laporan akurat dan tidak terpengaruh oleh draft jurnal

#### Acceptance Criteria

1. WHEN sistem menghitung laporan laba rugi, THE Laporan Laba Rugi System SHALL hanya menggunakan data dari journal entries dengan status 'posted'
2. WHEN sistem menghitung laporan laba rugi, THE Laporan Laba Rugi System SHALL mengabaikan journal entries dengan status 'draft' atau 'void'
3. WHEN tidak ada journal entries yang posted dalam periode, THE Laporan Laba Rugi System SHALL menampilkan laporan dengan nilai nol untuk semua akun
4. WHEN sistem menghitung saldo akun, THE Laporan Laba Rugi System SHALL menggunakan data dari journal_entry_details yang terkait dengan journal entries yang posted
5. WHEN outlet berubah, THE Laporan Laba Rugi System SHALL memfilter data berdasarkan outlet_id yang dipilih

### Requirement 7

**User Story:** Sebagai Finance Manager, saya ingin melihat rasio keuangan dasar dalam laporan laba rugi, sehingga saya dapat menilai efisiensi operasional

#### Acceptance Criteria

1. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menghitung dan menampilkan gross profit margin
2. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menghitung dan menampilkan net profit margin
3. WHEN laporan laba rugi ditampilkan, THE Laporan Laba Rugi System SHALL menghitung dan menampilkan operating expense ratio
4. WHEN total pendapatan adalah nol, THE Laporan Laba Rugi System SHALL menampilkan rasio sebagai 'N/A' atau tidak dapat dihitung
5. WHEN mode perbandingan aktif, THE Laporan Laba Rugi System SHALL menampilkan perubahan rasio antara periode saat ini dan periode pembanding

### Requirement 8

**User Story:** Sebagai Finance Manager, saya ingin mencetak laporan laba rugi langsung dari browser, sehingga saya dapat mencetak dengan cepat tanpa perlu export terlebih dahulu

#### Acceptance Criteria

1. WHEN user mengklik tombol print, THE Laporan Laba Rugi System SHALL membuka dialog print browser dengan format laporan yang sudah dioptimalkan untuk cetak
2. WHEN print dialog dibuka, THE Laporan Laba Rugi System SHALL menyembunyikan elemen UI yang tidak perlu (tombol, filter) dari hasil cetak
3. WHEN print dialog dibuka, THE Laporan Laba Rugi System SHALL menampilkan header dengan logo, nama perusahaan, dan informasi laporan
4. WHEN print dilakukan, THE Laporan Laba Rugi System SHALL menggunakan format portrait atau landscape sesuai dengan lebar konten
5. WHEN mode perbandingan aktif, THE Laporan Laba Rugi System SHALL menyertakan data periode pembanding dalam hasil cetak
