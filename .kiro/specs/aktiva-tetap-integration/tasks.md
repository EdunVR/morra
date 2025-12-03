# Implementation Plan - Integrasi Aktiva Tetap dengan Jurnal Akuntansi

## Task List

-   [x] 1. Setup Database dan Models

    -   Buat migration untuk tabel fixed_assets dengan semua kolom yang diperlukan
    -   Buat migration untuk tabel fixed_asset_depreciations dengan relasi ke fixed_assets dan journal_entries
    -   Buat model FixedAsset dengan fillable, casts, relationships, scopes, dan methods
    -   Buat model FixedAssetDepreciation dengan fillable, casts, relationships, dan scopes
    -   _Requirements: 1.1, 2.1, 2.2, 2.3, 2.4, 9.1_

-   [x] 2. Implementasi Method Perolehan Aktiva Tetap

    -   [x] 2.1 Implementasi fixedAssetsData() untuk menampilkan daftar aset dengan pagination dan filter

        -   Query fixed assets dengan eager loading relationships
        -   Implementasi filter berdasarkan outlet_id, category, status, search
        -   Hitung statistik: total assets, active assets, total acquisition cost, total depreciation, total book value
        -   Return response JSON dengan data, stats, dan meta pagination
        -   _Requirements: 1.1, 6.1, 6.4, 9.5_

    -   [x] 2.2 Implementasi storeFixedAsset() untuk membuat aset baru dengan jurnal otomatis

        -   Validasi input data sesuai validation rules
        -   Validasi tipe akun sesuai dengan fungsinya (asset, expense, contra asset)
        -   Validasi outlet_id sesuai dengan chart of accounts
        -   Buat record FixedAsset dengan book_value = acquisition_cost
        -   Buat JournalEntry untuk perolehan aset (Debit: Asset Account, Credit: Payment Account)
        -   Buat JournalEntryDetail untuk debit dan credit
        -   Post journal entry otomatis dengan status 'posted'
        -   Update saldo Chart of Account yang terkait
        -   Commit transaction atau rollback jika error
        -   _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 2.6, 2.7, 2.8, 8.1, 8.2, 8.6, 9.2, 9.4_

    -   [x] 2.3 Implementasi updateFixedAsset() untuk update data aset

        -   Validasi aset tidak boleh diubah jika sudah ada penyusutan posted
        -   Validasi input data
        -   Update data FixedAsset
        -   Tidak update jurnal yang sudah ada (hanya data master)
        -   _Requirements: 8.8_

    -   [x] 2.4 Implementasi deleteFixedAsset() untuk hapus aset

        -   Validasi aset tidak memiliki journal entry dengan status posted
        -   Validasi aset tidak memiliki depreciation records
        -   Hapus FixedAsset jika validasi lolos
        -   _Requirements: 7.7, 8.7_

    -   [x] 2.5 Implementasi toggleFixedAsset() untuk aktifkan/nonaktifkan aset

        -   Toggle status antara active dan inactive
        -   Return response sukses dengan data updated
        -   _Requirements: 1.1_

    -   [x] 2.6 Implementasi showFixedAsset() untuk detail aset

        -   Get FixedAsset dengan relationships lengkap
        -   Include depreciation history
        -   Include related journal entries
        -   _Requirements: 6.3, 7.1_

    -   [x] 2.7 Implementasi generateAssetCode() untuk generate kode unik

        -   Format: AST-{YYYYMM}-{sequence}
        -   Check uniqueness di database
        -   Return kode yang belum digunakan
        -   _Requirements: 1.1_

-   [x] 3. Implementasi Method Perhitungan Penyusutan

    -   [x] 3.1 Implementasi calculateMonthlyDepreciation() di model FixedAsset

        -   Implementasi metode straight_line: (acquisition_cost - salvage_value) / useful_life / 12
        -   Implementasi metode declining_balance: book_value \* (1.5 / useful_life) / 12
        -   Implementasi metode double_declining: book_value \* (2 / useful_life) / 12
        -   Validasi tidak melebihi (acquisition_cost - salvage_value)
        -   Validasi berhenti jika book_value <= salvage_value
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

    -   [x] 3.2 Implementasi calculateDepreciation() untuk hitung penyusutan periode tertentu

        -   Validasi input: outlet_id, period_month, period_year
        -   Get semua active fixed assets untuk outlet
        -   Filter aset yang book_value > salvage_value
        -   Loop setiap aset dan hitung depreciation amount
        -   Check apakah depreciation untuk period sudah ada
        -   Buat FixedAssetDepreciation dengan status 'draft'
        -   Hitung accumulated_depreciation dan book_value baru
        -   Return summary: jumlah aset diproses, total depreciation amount
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 4.7_

    -   [x] 3.3 Implementasi batchDepreciation() untuk proses batch semua aset

        -   Call calculateDepreciation() untuk semua active assets
        -   Jika auto_post = true, loop dan post setiap depreciation
        -   Track progress dan errors
        -   Return summary: total assets processed, total journals created, total depreciation amount, errors
        -   _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8_

-   [x] 4. Implementasi Method Posting Penyusutan

    -   [x] 4.1 Implementasi postDepreciation() untuk posting penyusutan dengan jurnal

        -   Find FixedAssetDepreciation by ID dengan relationship fixedAsset
        -   Validasi status adalah 'draft'
        -   Validasi tidak ada journal_entry_id
        -   Buat JournalEntry untuk penyusutan (Debit: Depreciation Expense, Credit: Accumulated Depreciation)
        -   Buat JournalEntryDetail untuk debit dan credit
        -   Update FixedAssetDepreciation: set journal_entry_id dan status 'posted'
        -   Update FixedAsset: increment accumulated_depreciation, decrement book_value
        -   Post journal entry dengan status 'posted'
        -   Update saldo Chart of Account yang terkait
        -   Commit transaction atau rollback jika error
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8_

    -   [x] 4.2 Implementasi reverseDepreciation() untuk reverse penyusutan yang sudah posted

        -   Find FixedAssetDepreciation by ID
        -   Validasi status adalah 'posted'
        -   Validasi ada journal_entry_id
        -   Buat JournalEntry pembalik (reverse debit-credit)
        -   Update FixedAssetDepreciation: status 'reversed'
        -   Update FixedAsset: decrement accumulated_depreciation, increment book_value
        -   Post journal entry pembalik
        -   Update saldo Chart of Account
        -   _Requirements: 7.6_

    -   [x] 4.3 Implementasi depreciationHistoryData() untuk riwayat penyusutan

        -   Query FixedAssetDepreciation dengan filter asset_id, month, status
        -   Include relationship ke journalEntry
        -   Format data untuk frontend: date, asset_code, asset_name, amount, accumulated, book_value, status, journal_number
        -   Return response JSON dengan data dan meta
        -   _Requirements: 7.1, 7.2, 7.3_

-   [x] 5. Implementasi Method Pelepasan Aset

    -   [x] 5.1 Implementasi disposeAsset() untuk pelepasan aset dengan jurnal

        -   Validasi input: disposal_date, disposal_value, disposal_notes
        -   Find FixedAsset by ID
        -   Validasi status adalah 'active'
        -   Hitung gain_loss = disposal_value - book_value
        -   Buat JournalEntry untuk disposal dengan 3-4 detail:
            -   Debit: payment_account_id (disposal_value)
            -   Debit: accumulated_depreciation_account_id (accumulated_depreciation)
            -   Debit: Loss on Disposal (jika gain_loss < 0) ATAU Credit: Gain on Disposal (jika gain_loss > 0)
            -   Credit: asset_account_id (acquisition_cost)
        -   Update FixedAsset: status 'sold' atau 'disposed', disposal_date, disposal_value, disposal_notes
        -   Post journal entry
        -   Update saldo Chart of Account
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8, 5.9_

    -   [x] 5.2 Implementasi calculateDisposalGainLoss() helper method

        -   Calculate gain_loss = disposal_value - book_value
        -   Return gain_loss value dan type (gain/loss)
        -   _Requirements: 5.2_

-   [x] 6. Implementasi Method Statistik dan Reporting

    -   [x] 6.1 Implementasi fixedAssetsStats() untuk statistik dashboard

        -   Hitung total assets, active assets per outlet
        -   Hitung total acquisition cost, total depreciation, total book value
        -   Hitung depreciation rate
        -   Group by category untuk distribution
        -   _Requirements: 6.1, 6.5, 9.6_

    -   [x] 6.2 Implementasi assetValueChartData() untuk data chart nilai aset

        -   Query fixed assets dengan group by acquisition year
        -   Hitung total acquisition cost dan book value per year
        -   Format data untuk Chart.js
        -   _Requirements: 6.7_

    -   [x] 6.3 Implementasi assetDistributionData() untuk data chart distribusi

        -   Query fixed assets dengan group by category
        -   Hitung count dan total value per category
        -   Format data untuk Chart.js pie chart
        -   _Requirements: 6.7_

    -   [x] 6.4 Implementasi exportFixedAssets() untuk export Excel/PDF

        -   Query fixed assets dengan filter yang dipilih
        -   Format data untuk export
        -   Generate Excel file menggunakan Maatwebsite\Excel
        -   Return download response
        -   _Requirements: 6.6_

-   [x] 7. Integrasi Frontend dengan Backend API

    -   [x] 7.1 Update Alpine.js component di index.blade.php untuk load data dari API

        -   Update loadAssets() untuk call fixedAssetsData API

        -   Update loadDepreciationHistory() untuk call depreciationHistoryData API
        -   Update loadAccounts() untuk call getActiveAccounts API dengan filter type
        -   Parse response dan update component state
        -   _Requirements: 6.1, 7.1_

    -   [x] 7.2 Implementasi saveAsset() untuk create/update aset

        -   Validasi form data di frontend
        -   Call storeFixedAsset atau updateFixedAsset API
        -   Handle success: reload data dan close modal
        -   Handle error: tampilkan pesan error
        -   _Requirements: 1.1, 2.1_

    -   [x] 7.3 Implementasi calculateDepreciation() untuk batch calculate

        -   Show modal untuk pilih periode (month, year)
        -   Call calculateDepreciation API
        -   Show progress indicator
        -   Show summary result
        -   Reload depreciation history
        -   _Requirements: 3.1, 10.1_

    -   [x] 7.4 Implementasi postDepreciation() untuk posting individual

        -   Confirm dialog sebelum posting
        -   Call postDepreciation API
        -   Handle success: reload data dan update status
        -   Handle error: tampilkan pesan error
        -   _Requirements: 3.1, 7.4_

    -   [x] 7.5 Implementasi reverseDepreciation() untuk reverse posting

        -   Confirm dialog dengan warning
        -   Call reverseDepreciation API
        -   Handle success: reload data
        -   Handle error: tampilkan pesan error
        -   _Requirements: 7.5, 7.6_

    -   [x] 7.6 Implementasi disposeAsset() untuk pelepasan aset

        -   Show modal form untuk disposal
        -   Calculate dan display gain/loss preview
        -   Call disposeAsset API
        -   Handle success: reload data dan close modal
        -   Handle error: tampilkan pesan error
        -   _Requirements: 5.1_

    -   [x] 7.7 Update charts dengan data real dari API

        -   Call assetValueChartData dan update valueChart
        -   Call assetDistributionData dan update distributionChart
        -   Implement chart update on period change
        -   _Requirements: 6.7_

-   [x] 8. Setup Routes untuk Fixed Assets API

    -   Tambahkan route untuk fixedAssetsData (GET)
    -   Tambahkan route untuk storeFixedAsset (POST)
    -   Tambahkan route untuk updateFixedAsset (PUT)
    -   Tambahkan route untuk deleteFixedAsset (DELETE)
    -   Tambahkan route untuk toggleFixedAsset (PATCH)
    -   Tambahkan route untuk showFixedAsset (GET)
    -   Tambahkan route untuk generateAssetCode (GET)
    -   Tambahkan route untuk calculateDepreciation (POST)
    -   Tambahkan route untuk batchDepreciation (POST)
    -   Tambahkan route untuk postDepreciation (POST)
    -   Tambahkan route untuk reverseDepreciation (POST)
    -   Tambahkan route untuk depreciationHistoryData (GET)
    -   Tambahkan route untuk disposeAsset (POST)
    -   Tambahkan route untuk fixedAssetsStats (GET)
    -   Tambahkan route untuk assetValueChartData (GET)
    -   Tambahkan route untuk assetDistributionData (GET)
    -   Tambahkan route untuk exportFixedAssets (GET)
    -   Group semua route dengan prefix 'finance/fixed-assets' dan middleware auth
    -   _Requirements: All_

-   [x] 9. Testing dan Validasi

    -   [x] 9.1 Test perolehan aset dengan jurnal otomatis

        -   Test create asset dengan data valid
        -   Verify journal entry created dengan format benar
        -   Verify account balances updated
        -   Test dengan berbagai kategori aset
        -   _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6_

    -   [x] 9.2 Test perhitungan penyusutan untuk semua metode

        -   Test straight_line method
        -   Test declining_balance method
        -   Test double_declining method
        -   Verify tidak melebihi depreciable amount
        -   Verify berhenti di salvage value
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

    -   [x] 9.3 Test posting penyusutan dengan jurnal

        -   Test post depreciation creates journal
        -   Verify journal format correct
        -   Verify account balances updated
        -   Test prevent double posting
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8_

    -   [x] 9.4 Test pelepasan aset dengan gain/loss

        -   Test disposal dengan gain (disposal_value > book_value)
        -   Test disposal dengan loss (disposal_value < book_value)
        -   Verify journal entries correct
        -   Verify account balances updated
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_

    -   [x] 9.5 Test validasi dan error handling

        -   Test invalid account types
        -   Test salvage value > acquisition cost
        -   Test delete asset with posted journal
        -   Test post already posted depreciation
        -   Test reverse draft depreciation
        -   _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 8.8_

    -   [x] 9.6 Test batch processing

        -   Test batch calculate untuk multiple assets
        -   Test batch post dengan auto_post
        -   Test error handling dalam batch
        -   Verify summary result correct
        -   _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7_

    -   [x] 9.7 Test integrasi frontend-backend

        -   Test load data dari API
        -   Test create/update asset via form
        -   Test calculate dan post depreciation
        -   Test disposal flow
        -   Test charts update dengan data real
        -   _Requirements: 6.1, 6.7, 7.1, 7.2, 7.3_

-   [x] 10. Dokumentasi dan Finalisasi

    -   [x] 10.1 Buat dokumentasi API endpoints

        -   Dokumentasi request/response format

        -   Dokumentasi validation rules
        -   Dokumentasi error codes
        -   _Requirements: All_

    -   [x] 10.2 Buat user guide untuk fitur aktiva tetap

        -   Cara menambah aset baru
        -   Cara menghitung dan posting penyusutan
        -   Cara melepas aset
        -   Cara membaca laporan
        -   _Requirements: All_

    -   [x] 10.3 Review dan cleanup code

        -   Remove unused code
        -   Add comments untuk complex logic
        -   Ensure consistent code style
        -   _Requirements: All_
