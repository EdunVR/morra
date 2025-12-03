# Requirements Document - Integrasi Aktiva Tetap dengan Jurnal Akuntansi

## Introduction

Fitur ini bertujuan untuk melengkapi modul Aktiva Tetap (Fixed Assets) pada sistem ERP dengan integrasi penuh ke sistem jurnal akuntansi. Saat ini, modul aktiva tetap sudah memiliki struktur dasar untuk pencatatan aset dan perhitungan penyusutan, namun belum terintegrasi dengan jurnal akuntansi seperti modul penjualan dan pembelian. Fitur ini akan memastikan setiap transaksi aktiva tetap (perolehan, penyusutan, pelepasan) secara otomatis mencatat jurnal akuntansi yang sesuai dengan prinsip akuntansi yang berlaku.

## Glossary

-   **System**: Sistem ERP modul Finance - Aktiva Tetap
-   **Fixed_Asset**: Aset tetap perusahaan yang memiliki masa manfaat lebih dari satu tahun
-   **Depreciation**: Penyusutan nilai aset tetap secara berkala
-   **Journal_Entry**: Catatan transaksi akuntansi dalam jurnal
-   **Chart_of_Account**: Daftar akun dalam sistem akuntansi
-   **Accounting_Book**: Buku akuntansi yang mencatat semua transaksi
-   **Acquisition**: Perolehan atau pembelian aset tetap baru
-   **Disposal**: Pelepasan aset tetap (penjualan atau penghapusan)
-   **Book_Value**: Nilai buku aset setelah dikurangi akumulasi penyusutan
-   **Accumulated_Depreciation**: Total penyusutan yang telah dicatat sejak perolehan aset
-   **Salvage_Value**: Nilai residu atau nilai sisa aset di akhir masa manfaat
-   **Useful_Life**: Masa manfaat ekonomis aset dalam tahun
-   **Depreciation_Method**: Metode perhitungan penyusutan (garis lurus, saldo menurun, dll)
-   **Asset_Account**: Akun aset tetap dalam chart of accounts
-   **Depreciation_Expense_Account**: Akun beban penyusutan dalam chart of accounts
-   **Accumulated_Depreciation_Account**: Akun akumulasi penyusutan (kontra aset)
-   **Outlet**: Cabang atau lokasi usaha dalam sistem ERP

## Requirements

### Requirement 1: Integrasi Perolehan Aktiva Tetap dengan Jurnal

**User Story:** Sebagai akuntan, saya ingin sistem secara otomatis mencatat jurnal akuntansi ketika aktiva tetap baru dicatat, sehingga saldo akun aset dan kas/hutang terupdate dengan benar.

#### Acceptance Criteria

1. WHEN pengguna menyimpan data Fixed_Asset baru, THE System SHALL membuat Journal_Entry dengan debit pada Asset_Account dan kredit pada akun pembayaran
2. WHEN Fixed_Asset disimpan dengan status "active", THE System SHALL memposting Journal_Entry secara otomatis dengan status "posted"
3. THE System SHALL menggunakan acquisition_date dari Fixed_Asset sebagai transaction_date pada Journal_Entry
4. THE System SHALL menggunakan total_cost dari Fixed_Asset sebagai nilai debit dan kredit pada Journal_Entry
5. THE System SHALL menyimpan reference_number pada Journal_Entry dengan format "FA-{asset_code}"
6. WHEN Journal_Entry berhasil dibuat, THE System SHALL memperbarui saldo Chart_of_Account yang terkait
7. IF pembuatan Journal_Entry gagal, THEN THE System SHALL membatalkan penyimpanan Fixed_Asset dan menampilkan pesan error

### Requirement 2: Konfigurasi Akun untuk Aktiva Tetap

**User Story:** Sebagai akuntan, saya ingin dapat mengkonfigurasi akun-akun yang digunakan untuk setiap kategori aktiva tetap, sehingga pencatatan jurnal sesuai dengan chart of accounts perusahaan.

#### Acceptance Criteria

1. THE System SHALL menyediakan field asset_account_id pada Fixed_Asset untuk menyimpan akun aset
2. THE System SHALL menyediakan field depreciation_expense_account_id pada Fixed_Asset untuk menyimpan akun beban penyusutan
3. THE System SHALL menyediakan field accumulated_depreciation_account_id pada Fixed_Asset untuk menyimpan akun akumulasi penyusutan
4. THE System SHALL menyediakan field payment_account_id pada Fixed_Asset untuk menyimpan akun pembayaran (kas/bank/hutang)
5. WHEN pengguna membuat Fixed_Asset baru, THE System SHALL menampilkan dropdown akun yang difilter berdasarkan tipe akun yang sesuai
6. THE System SHALL memvalidasi bahwa asset_account_id memiliki type "asset" pada Chart_of_Account
7. THE System SHALL memvalidasi bahwa depreciation_expense_account_id memiliki type "expense" pada Chart_of_Account
8. THE System SHALL memvalidasi bahwa accumulated_depreciation_account_id memiliki type "asset" dan category "contra_asset" pada Chart_of_Account

### Requirement 3: Pencatatan Jurnal Penyusutan Otomatis

**User Story:** Sebagai akuntan, saya ingin sistem dapat mencatat jurnal penyusutan secara otomatis setiap periode, sehingga beban penyusutan tercatat dengan akurat dan konsisten.

#### Acceptance Criteria

1. WHEN pengguna menjalankan proses perhitungan penyusutan, THE System SHALL membuat Journal_Entry untuk setiap Fixed_Asset yang aktif
2. THE System SHALL membuat Journal_Entry dengan debit pada depreciation_expense_account_id dan kredit pada accumulated_depreciation_account_id
3. THE System SHALL menggunakan nilai amount dari FixedAssetDepreciation sebagai nilai debit dan kredit
4. THE System SHALL menggunakan depreciation_date sebagai transaction_date pada Journal_Entry
5. THE System SHALL menyimpan reference_number dengan format "DEP-{asset_code}-{period}"
6. THE System SHALL menyimpan journal_entry_id pada FixedAssetDepreciation untuk tracking
7. WHEN Journal_Entry penyusutan berhasil dibuat, THE System SHALL mengupdate status FixedAssetDepreciation menjadi "posted"
8. THE System SHALL mencegah posting ulang penyusutan yang sudah memiliki status "posted"

### Requirement 4: Perhitungan Penyusutan dengan Multiple Methods

**User Story:** Sebagai akuntan, saya ingin sistem mendukung berbagai metode penyusutan, sehingga dapat disesuaikan dengan kebijakan akuntansi perusahaan.

#### Acceptance Criteria

1. THE System SHALL menyediakan field depreciation_method pada Fixed_Asset dengan pilihan "straight_line", "declining_balance", "double_declining", "units_of_production"
2. WHEN depreciation_method adalah "straight_line", THE System SHALL menghitung penyusutan dengan formula (total_cost - salvage_value) / useful_life / 12
3. WHEN depreciation_method adalah "declining_balance", THE System SHALL menghitung penyusutan dengan formula book_value _ (1 / useful_life) _ 1.5 / 12
4. WHEN depreciation_method adalah "double_declining", THE System SHALL menghitung penyusutan dengan formula book_value \* (2 / useful_life) / 12
5. THE System SHALL memastikan accumulated_depreciation tidak melebihi (total_cost - salvage_value)
6. THE System SHALL menghentikan penyusutan WHEN book_value mencapai salvage_value
7. THE System SHALL menyimpan depreciation_method yang digunakan pada setiap FixedAssetDepreciation record

### Requirement 5: Pelepasan Aktiva Tetap (Disposal)

**User Story:** Sebagai akuntan, saya ingin dapat mencatat pelepasan aktiva tetap dengan jurnal yang tepat, sehingga keuntungan atau kerugian pelepasan tercatat dengan benar.

#### Acceptance Criteria

1. THE System SHALL menyediakan fungsi untuk mencatat disposal Fixed_Asset
2. WHEN Fixed_Asset dilepas, THE System SHALL menghitung gain_loss dengan formula disposal_value - book_value
3. THE System SHALL membuat Journal_Entry dengan debit pada akun pembayaran sebesar disposal_value
4. THE System SHALL membuat Journal_Entry dengan debit pada accumulated_depreciation_account_id sebesar total akumulasi penyusutan
5. IF gain_loss positif, THEN THE System SHALL membuat Journal_Entry dengan kredit pada akun "Gain on Disposal" sebesar gain_loss
6. IF gain_loss negatif, THEN THE System SHALL membuat Journal_Entry dengan debit pada akun "Loss on Disposal" sebesar absolute gain_loss
7. THE System SHALL membuat Journal_Entry dengan kredit pada asset_account_id sebesar total_cost
8. WHEN disposal berhasil, THE System SHALL mengupdate status Fixed_Asset menjadi "disposed" atau "sold"
9. THE System SHALL menyimpan disposal_date, disposal_value, dan disposal_notes pada Fixed_Asset

### Requirement 6: Laporan Aktiva Tetap Terintegrasi

**User Story:** Sebagai akuntan, saya ingin melihat laporan aktiva tetap yang menampilkan nilai buku, akumulasi penyusutan, dan referensi ke jurnal terkait, sehingga dapat melakukan rekonsiliasi dengan mudah.

#### Acceptance Criteria

1. THE System SHALL menampilkan daftar Fixed_Asset dengan kolom: kode, nama, tanggal perolehan, nilai perolehan, akumulasi penyusutan, nilai buku
2. THE System SHALL menghitung book_value dengan formula total_cost - accumulated_depreciation
3. THE System SHALL menampilkan link ke Journal_Entry terkait untuk setiap Fixed_Asset
4. THE System SHALL menyediakan filter berdasarkan outlet_id, kategori, status, dan periode perolehan
5. THE System SHALL menampilkan total nilai perolehan, total akumulasi penyusutan, dan total nilai buku di bagian summary
6. THE System SHALL menyediakan export laporan ke format Excel dan PDF
7. THE System SHALL menampilkan grafik distribusi aset per kategori dan trend nilai buku

### Requirement 7: Riwayat Penyusutan dengan Tracking Jurnal

**User Story:** Sebagai akuntan, saya ingin melihat riwayat penyusutan setiap aktiva tetap beserta jurnal yang telah diposting, sehingga dapat melakukan audit trail dengan mudah.

#### Acceptance Criteria

1. THE System SHALL menampilkan tabel riwayat FixedAssetDepreciation untuk setiap Fixed_Asset
2. THE System SHALL menampilkan kolom: periode, tanggal, nilai penyusutan, akumulasi, nilai buku, status, nomor jurnal
3. WHEN FixedAssetDepreciation memiliki journal_entry_id, THE System SHALL menampilkan link ke Journal_Entry detail
4. THE System SHALL menyediakan tombol untuk posting penyusutan yang berstatus "draft"
5. THE System SHALL menyediakan tombol untuk reverse penyusutan yang sudah diposting
6. WHEN penyusutan di-reverse, THE System SHALL membuat Journal_Entry pembalik dan mengupdate status menjadi "reversed"
7. THE System SHALL mencegah penghapusan FixedAssetDepreciation yang sudah memiliki Journal_Entry

### Requirement 8: Validasi dan Error Handling

**User Story:** Sebagai akuntan, saya ingin sistem memberikan validasi yang jelas dan mencegah kesalahan pencatatan, sehingga data akuntansi tetap akurat dan konsisten.

#### Acceptance Criteria

1. THE System SHALL memvalidasi bahwa outlet_id pada Fixed_Asset sesuai dengan outlet_id pada Chart_of_Account yang dipilih
2. THE System SHALL memvalidasi bahwa acquisition_date tidak lebih besar dari tanggal hari ini
3. THE System SHALL memvalidasi bahwa useful_life minimal 1 tahun
4. THE System SHALL memvalidasi bahwa salvage_value tidak lebih besar dari total_cost
5. THE System SHALL memvalidasi bahwa semua akun yang dipilih memiliki status "active"
6. WHEN terjadi error saat membuat Journal_Entry, THE System SHALL melakukan rollback transaksi dan menampilkan pesan error yang jelas
7. THE System SHALL mencegah penghapusan Fixed_Asset yang sudah memiliki Journal_Entry dengan status "posted"
8. THE System SHALL mencegah perubahan nilai total_cost pada Fixed_Asset yang sudah memiliki penyusutan

### Requirement 9: Integrasi dengan Outlet dan Multi-Company

**User Story:** Sebagai akuntan, saya ingin aktiva tetap dapat dikelola per outlet dengan chart of accounts yang sesuai, sehingga pelaporan per cabang akurat.

#### Acceptance Criteria

1. THE System SHALL menyediakan field outlet_id pada Fixed_Asset
2. WHEN pengguna membuat Fixed_Asset, THE System SHALL menggunakan outlet_id dari user yang login sebagai default
3. THE System SHALL memfilter Chart_of_Account berdasarkan outlet_id saat memilih akun
4. THE System SHALL membuat Journal_Entry dengan outlet_id yang sama dengan Fixed_Asset
5. THE System SHALL menyediakan filter outlet pada halaman daftar Fixed_Asset
6. THE System SHALL menghitung statistik dan summary per outlet
7. THE System SHALL memvalidasi bahwa Accounting_Book yang dipilih sesuai dengan outlet_id

### Requirement 10: Batch Processing untuk Penyusutan Bulanan

**User Story:** Sebagai akuntan, saya ingin dapat menjalankan proses penyusutan untuk semua aktiva tetap sekaligus, sehingga proses closing bulanan lebih efisien.

#### Acceptance Criteria

1. THE System SHALL menyediakan fungsi batch processing untuk menghitung penyusutan semua Fixed_Asset aktif
2. WHEN batch processing dijalankan, THE System SHALL memfilter Fixed_Asset dengan status "active" dan book_value lebih besar dari salvage_value
3. THE System SHALL membuat FixedAssetDepreciation untuk periode yang belum ada
4. THE System SHALL membuat Journal_Entry untuk setiap penyusutan yang berhasil dihitung
5. THE System SHALL menampilkan progress bar selama proses batch processing
6. WHEN batch processing selesai, THE System SHALL menampilkan summary: jumlah aset diproses, jumlah jurnal dibuat, total nilai penyusutan
7. IF terjadi error pada salah satu aset, THE System SHALL melanjutkan proses untuk aset lainnya dan mencatat error log
8. THE System SHALL menyediakan opsi untuk memilih periode penyusutan (bulan dan tahun)
