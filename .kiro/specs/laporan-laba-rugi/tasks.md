# Implementation Plan - Laporan Laba Rugi

## Task List

-   [x] 1. Setup routes dan controller methods dasar

    -   Tambahkan routes untuk profit-loss di routes/web.php
    -   Buat method profitLossIndex() di FinanceAccountantController untuk render view
    -   Buat method profitLossData() skeleton di FinanceAccountantController
    -   Buat method profitLossStats() skeleton di FinanceAccountantController
    -   _Requirements: 1.1, 1.2_

-   [x] 2. Implementasi backend logic untuk perhitungan laba rugi

    -   [x] 2.1 Implementasi method profitLossData() untuk query data

        -   Query akun revenue (type: 'revenue', 'otherrevenue') dari chart_of_accounts
        -   Query akun expense (type: 'expense', 'otherexpense') dari chart_of_accounts
        -   Implementasi logic untuk calculate amount per akun dari journal_entry_details
        -   Filter hanya journal entries dengan status 'posted'
        -   Implementasi recursive calculation untuk parent-child accounts
        -   Hitung summary (total revenue, total expense, net income)
        -   _Requirements: 1.3, 1.4, 1.5, 6.1, 6.2, 6.4_

    -   [x] 2.2 Implementasi comparison mode

        -   Tambahkan parameter comparison, comparison_start_date, comparison_end_date
        -   Query data untuk periode pembanding
        -   Hitung delta (selisih) antara periode current dan comparison
        -   Hitung persentase perubahan
        -   _Requirements: 3.1, 3.2, 3.3_

    -   [x] 2.3 Implementasi perhitungan rasio keuangan

        -   Hitung gross profit margin
        -   Hitung net profit margin
        -   Hitung operating expense ratio
        -   Handle edge case (division by zero)
        -   _Requirements: 7.1, 7.2, 7.3, 7.4_

    -   [x] 2.4 Implementasi method profitLossStats() untuk dashboard

        -   Query data untuk current month, last month, YTD
        -   Query data untuk trends (6 bulan terakhir)
        -   Format data untuk Chart.js
        -   _Requirements: 5.1, 5.2, 5.3, 5.4_

-   [x] 3. Implementasi frontend view

    -   [x] 3.1 Buat file index.blade.php

        -   Buat struktur HTML dengan Tailwind CSS
        -   Implementasi header dengan title dan action buttons
        -   Implementasi filter section (outlet, period, date range)
        -   Implementasi comparison toggle
        -   _Requirements: 1.1, 3.1_

    -   [x] 3.2 Implementasi Alpine.js component profitLossManagement()

        -   Setup state variables (filters, data, UI state)
        -   Implementasi init() method
        -   Implementasi loadOutlets() method
        -   Implementasi loadProfitLossData() method
        -   Implementasi loadStats() method
        -   Implementasi event handlers (onOutletChange, onPeriodChange, toggleComparison)
        -   Implementasi helper methods (formatCurrency, formatDate, calculateChange)
        -   _Requirements: 1.1, 1.2, 3.1_

    -   [x] 3.3 Implementasi summary cards section

        -   Card untuk Total Revenue
        -   Card untuk Total Expense
        -   Card untuk Net Income
        -   Card untuk Profit Margin
        -   Tampilkan comparison indicators jika mode comparison aktif
        -   _Requirements: 1.3, 1.4, 1.5, 3.4, 3.5_

    -   [x] 3.4 Implementasi profit & loss statement table

        -   Tampilkan section PENDAPATAN dengan detail akun
        -   Tampilkan section PENDAPATAN LAIN-LAIN dengan detail akun
        -   Tampilkan section BEBAN OPERASIONAL dengan detail akun
        -   Tampilkan section BEBAN LAIN-LAIN dengan detail akun
        -   Tampilkan TOTAL PENDAPATAN, TOTAL BEBAN, LABA/RUGI BERSIH
        -   Tampilkan RASIO KEUANGAN
        -   Implementasi expand/collapse untuk child accounts
        -   Tampilkan kolom comparison jika mode aktif
        -   _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 7.1, 7.2, 7.3_

-   [x] 4. Implementasi visualisasi grafik

    -   [x] 4.1 Setup Chart.js

        -   Import Chart.js library
        -   Buat method initCharts() di Alpine.js component
        -   Buat method updateCharts() di Alpine.js component
        -   _Requirements: 5.1, 5.2, 5.3_

    -   [x] 4.2 Implementasi Revenue Pie Chart

        -   Chart untuk komposisi pendapatan berdasarkan kategori akun
        -   Implementasi click handler untuk show detail
        -   _Requirements: 5.1, 5.5_

    -   [x] 4.3 Implementasi Expense Pie Chart

        -   Chart untuk komposisi beban berdasarkan kategori akun
        -   Implementasi click handler untuk show detail
        -   _Requirements: 5.2, 5.5_

    -   [x] 4.4 Implementasi Revenue vs Expense Bar Chart

        -   Chart untuk perbandingan total revenue vs total expense
        -   _Requirements: 5.3_

    -   [x] 4.5 Implementasi Trend Line Chart (comparison mode)

        -   Chart untuk tren laba/rugi bersih
        -   Hanya tampil jika comparison mode aktif
        -   _Requirements: 5.4_

-   [x] 5. Implementasi export functionality

    -   [x] 5.1 Buat ProfitLossExport class

        -   Implementasi FromCollection interface
        -   Implementasi WithHeadings interface
        -   Implementasi WithStyles interface
        -   Format data untuk Excel
        -   Tambahkan header dengan outlet name, periode, tanggal generate
        -   _Requirements: 4.1, 4.2, 4.4_

    -   [x] 5.2 Implementasi method exportProfitLossXLSX()

        -   Ambil data menggunakan logic profitLossData()
        -   Generate Excel file menggunakan ProfitLossExport
        -   Return file download
        -   Support comparison mode
        -   _Requirements: 4.1, 4.2, 4.4, 4.5_

    -   [x] 5.3 Buat view pdf.blade.php

        -   Buat template PDF dengan styling
        -   Implementasi header dengan logo dan info perusahaan
        -   Implementasi tabel profit & loss statement
        -   Implementasi footer dengan tanggal generate
        -   Support comparison mode
        -   _Requirements: 4.1, 4.3, 4.4, 4.5_

    -   [x] 5.4 Implementasi method exportProfitLossPDF()

        -   Ambil data menggunakan logic profitLossData()
        -   Render view pdf.blade.php
        -   Generate PDF menggunakan DomPDF
        -   Return file download atau stream
        -   _Requirements: 4.1, 4.3, 4.4, 4.5_

    -   [x] 5.5 Implementasi export buttons di frontend

        -   Button Export dengan dropdown (XLSX, PDF)
        -   Implementasi exportToXLSX() method di Alpine.js
        -   Implementasi exportToPDF() method di Alpine.js
        -   Show loading state saat export
        -   _Requirements: 4.1, 4.2, 4.3_

-   [x] 6. Implementasi print functionality

    -   [x] 6.1 Tambahkan print styles di view

        -   Hide UI elements yang tidak perlu (buttons, filters)
        -   Optimize layout untuk print
        -   Tambahkan header untuk print
        -   _Requirements: 8.2, 8.3_

    -   [x] 6.2 Implementasi print button dan method

        -   Button Print di header
        -   Implementasi printReport() method di Alpine.js
        -   Open print dialog dengan format optimized
        -   Support comparison mode
        -   _Requirements: 8.1, 8.4, 8.5_

-   [x] 7. Implementasi error handling dan validation

    -   Validasi outlet_id required
    -   Validasi date range (end_date >= start_date)
    -   Validasi comparison date range
    -   Handle error response di frontend
    -   Display error notification
    -   Handle empty data state
    -   _Requirements: 6.3_

-   [x] 8. Implementasi detail transaksi per akun

    -   Implementasi click handler pada akun di table
    -   Show modal atau expandable section dengan detail transaksi
    -   Tampilkan journal entry yang mempengaruhi akun
    -   Link ke halaman jurnal untuk detail lebih lanjut
    -   _Requirements: 2.4_

-   [x] 9. Update navigation menu

    -   Tambahkan menu item "Laporan Laba Rugi" di sidebar
    -   Set parent menu ke "Finance"
    -   Set icon dan route
    -   _Requirements: 1.1_

-   [x] 10. Testing dan bug fixes

    -   Test dengan berbagai periode (monthly, quarterly, yearly, custom)
    -   Test dengan outlet berbeda
    -   Test comparison mode
    -   Test export XLSX dan PDF
    -   Test print functionality
    -   Test dengan data kosong
    -   Test dengan akun yang memiliki children
    -   Fix bugs yang ditemukan
    -   _Requirements: All_

-   [ ] 10.1 Advanced testing

    -   Test performa dengan data besar
    -   Load testing untuk concurrent users
    -   Security testing
    -   _Requirements: All_

-

-   [ ] 11. Optimization dan polish

    -   Add loading states
    -   Add empty states
    -   Add success notifications
    -   _Requirements: All_

-   [ ]\* 11.1 Advanced optimization

    -   Optimize database queries dengan caching
    -   Add database indexes
    -   Improve responsive design untuk mobile
    -   Add tooltips untuk rasio keuangan
    -   Polish UI/UX dengan animations
    -   _Requirements: All_

-   [ ]\* 12. Documentation
    -   Tambahkan inline comments di code
    -   Update README jika diperlukan
    -   Buat user guide untuk fitur ini
    -   _Requirements: All_
