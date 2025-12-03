# Manual Testing Checklist

This document provides a comprehensive checklist for manually testing the finance export, import, and print functionality.

## Test Results Summary

### Automated Tests Status

-   ✅ Unit Tests for Export Classes: **PASSED** (13 tests, 73 assertions)
    -   JournalExportTest: 6 tests passed
    -   FixedAssetsExportTest: 7 tests passed
    -   GeneralLedgerExportTest: Created and ready
-   ✅ Unit Tests for Import Classes: **CREATED**
    -   JournalImportTest: Created with validation tests
    -   FixedAssetsImportTest: Created with validation tests
-   ✅ Integration Tests: **CREATED**
    -   FinanceExportIntegrationTest: Created
    -   FinanceImportIntegrationTest: Created
    -   FinancePrintIntegrationTest: Created
    -   SidebarStatePersistenceTest: Already exists

## Manual Testing Checklist

### 1. Journal List (Daftar Jurnal) Export

#### XLSX Export

-   [ ] Navigate to Journal List page
-   [ ] Click Export button
-   [ ] Select "Export ke XLSX" option
-   [ ] Verify file downloads successfully
-   [ ] Open file in Excel/LibreOffice
-   [ ] Verify all columns are present: Tanggal, No. Transaksi, Kode Akun, Nama Akun, Deskripsi, Debit, Kredit, Outlet, Buku, Status
-   [ ] Verify data is correctly formatted
-   [ ] Verify numbers are formatted with thousand separators
-   [ ] Verify header row is styled (bold, colored background)

#### PDF Export

-   [ ] Navigate to Journal List page
-   [ ] Click Export button
-   [ ] Select "Export ke PDF" option
-   [ ] Verify PDF opens/downloads successfully
-   [ ] Verify PDF contains company header
-   [ ] Verify PDF contains all journal entries
-   [ ] Verify PDF formatting is professional
-   [ ] Verify page numbers are present
-   [ ] Verify totals are calculated correctly

#### Export with Filters

-   [ ] Apply date range filter (e.g., January 2024)
-   [ ] Export to XLSX
-   [ ] Verify only filtered data is exported
-   [ ] Apply status filter (e.g., "Posted")
-   [ ] Export to PDF
-   [ ] Verify only filtered data is exported
-   [ ] Apply outlet filter
-   [ ] Export to both formats
-   [ ] Verify outlet-specific data is exported

### 2. Journal List Import

#### Valid File Import

-   [ ] Click "Download Template" button
-   [ ] Verify template file downloads
-   [ ] Open template and fill with valid data
-   [ ] Click Import button
-   [ ] Upload the filled template
-   [ ] Verify success message appears
-   [ ] Verify imported count is correct
-   [ ] Verify data appears in journal list
-   [ ] Verify journal entries are in "draft" status

#### Invalid File Import

-   [ ] Create file with missing required fields
-   [ ] Attempt to import
-   [ ] Verify error messages are displayed
-   [ ] Verify row numbers are indicated in errors
-   [ ] Create file with invalid account codes
-   [ ] Attempt to import
-   [ ] Verify appropriate error messages
-   [ ] Create file with unbalanced entries (debit ≠ credit)
-   [ ] Attempt to import
-   [ ] Verify validation error is shown

#### Import Error Handling

-   [ ] Upload non-Excel file (e.g., .txt)
-   [ ] Verify file type validation error
-   [ ] Upload file larger than 5MB
-   [ ] Verify file size validation error
-   [ ] Upload file with duplicate transaction numbers
-   [ ] Verify duplicates are skipped with message

### 3. Fixed Assets (Aktiva Tetap) Export

#### XLSX Export

-   [ ] Navigate to Fixed Assets page
-   [ ] Click Export button
-   [ ] Select "Export ke XLSX"
-   [ ] Verify file downloads
-   [ ] Open file and verify columns: Kode Aset, Nama Aset, Kategori, Lokasi, Tanggal Perolehan, Harga Perolehan, Nilai Residu, Metode Penyusutan, Umur Ekonomis, Akumulasi Penyusutan, Nilai Buku, Status, Outlet
-   [ ] Verify categories are in Indonesian (Bangunan, Kendaraan, etc.)
-   [ ] Verify depreciation methods are in Indonesian (Garis Lurus, etc.)
-   [ ] Verify status is in Indonesian (Aktif, Tidak Aktif, etc.)
-   [ ] Verify numeric formatting is correct

#### PDF Export

-   [ ] Navigate to Fixed Assets page
-   [ ] Click Export button
-   [ ] Select "Export ke PDF"
-   [ ] Verify PDF downloads/opens
-   [ ] Verify PDF contains asset listing
-   [ ] Verify depreciation details are shown
-   [ ] Verify summary totals are present
-   [ ] Verify grouping by category (if applicable)

#### Export with Filters

-   [ ] Apply category filter (e.g., "Computer")
-   [ ] Export to XLSX
-   [ ] Verify only selected category is exported
-   [ ] Apply status filter (e.g., "Active")
-   [ ] Export to PDF
-   [ ] Verify only active assets are exported

### 4. Fixed Assets Import

#### Valid File Import

-   [ ] Click "Download Template" button
-   [ ] Verify template downloads
-   [ ] Fill template with valid asset data
-   [ ] Upload file
-   [ ] Verify success message
-   [ ] Verify imported count
-   [ ] Verify assets appear in list
-   [ ] Verify book values are calculated correctly

#### Invalid File Import

-   [ ] Create file with acquisition cost = 0
-   [ ] Attempt import
-   [ ] Verify validation error
-   [ ] Create file with salvage value > acquisition cost
-   [ ] Attempt import
-   [ ] Verify validation error
-   [ ] Create file with useful life < 1
-   [ ] Attempt import
-   [ ] Verify validation error
-   [ ] Create file with invalid category
-   [ ] Attempt import
-   [ ] Verify validation error
-   [ ] Create file with invalid depreciation method
-   [ ] Attempt import
-   [ ] Verify validation error

#### Bilingual Field Support

-   [ ] Create file with Indonesian field names (kode_aset, nama_aset, etc.)
-   [ ] Import successfully
-   [ ] Create file with English field names (code, name, etc.)
-   [ ] Import successfully
-   [ ] Verify both formats work correctly

### 5. General Ledger (Buku Besar) Export

#### XLSX Export

-   [ ] Navigate to General Ledger page
-   [ ] Select date range
-   [ ] Click Export button
-   [ ] Select "Export ke XLSX"
-   [ ] Verify file downloads
-   [ ] Open file and verify structure
-   [ ] Verify opening balance rows are present
-   [ ] Verify transactions are listed
-   [ ] Verify account totals are shown
-   [ ] Verify grand total is present
-   [ ] Verify running balance is calculated correctly
-   [ ] Verify styling (opening balance, totals highlighted)

#### PDF Export

-   [ ] Navigate to General Ledger page
-   [ ] Select date range
-   [ ] Click Export button
-   [ ] Select "Export ke PDF"
-   [ ] Verify PDF downloads/opens
-   [ ] Verify ledger format follows accounting standards
-   [ ] Verify running balances are shown
-   [ ] Verify opening and closing balances are present
-   [ ] Verify page breaks are appropriate

#### Export with Filters

-   [ ] Select specific account
-   [ ] Export to XLSX
-   [ ] Verify only selected account is exported
-   [ ] Select date range (e.g., Q1 2024)
-   [ ] Export to PDF
-   [ ] Verify only transactions in date range are exported

### 6. Accounting Book (Buku Akuntansi) Export

#### XLSX Export

-   [ ] Navigate to Accounting Book page
-   [ ] Select report type
-   [ ] Click Export button
-   [ ] Select "Export ke XLSX"
-   [ ] Verify file downloads
-   [ ] Verify columns match report type
-   [ ] Verify data is correctly formatted

#### PDF Export

-   [ ] Navigate to Accounting Book page
-   [ ] Select report type
-   [ ] Click Export button
-   [ ] Select "Export ke PDF"
-   [ ] Verify PDF follows accounting standards
-   [ ] Verify proper sections and page breaks
-   [ ] Verify summary and totals are present

### 7. Print Functionality

#### Journal Print

-   [ ] Navigate to Journal List page
-   [ ] Click Print button
-   [ ] Verify PDF opens in new tab
-   [ ] Verify print dialog appears (or can be triggered)
-   [ ] Verify PDF is print-ready
-   [ ] Print to PDF/printer
-   [ ] Verify output quality

#### Fixed Assets Print

-   [ ] Navigate to Fixed Assets page
-   [ ] Click Print button
-   [ ] Verify PDF opens
-   [ ] Verify asset listing with depreciation details
-   [ ] Verify summary totals
-   [ ] Verify print quality

#### General Ledger Print

-   [ ] Navigate to General Ledger page
-   [ ] Select date range
-   [ ] Click Print button
-   [ ] Verify PDF opens
-   [ ] Verify ledger format
-   [ ] Verify running balances
-   [ ] Verify print quality

#### Accounting Book Print

-   [ ] Navigate to Accounting Book page
-   [ ] Select report type
-   [ ] Click Print button
-   [ ] Verify PDF opens
-   [ ] Verify accounting report format
-   [ ] Verify print quality

### 8. Sidebar Submenu State Persistence

#### Initial State

-   [ ] Navigate to any finance page (e.g., Journal List)
-   [ ] Verify Finance submenu is expanded
-   [ ] Verify active menu item is highlighted

#### Navigation Within Submenu

-   [ ] Click on another finance submenu item (e.g., Fixed Assets)
-   [ ] Verify Finance submenu remains expanded
-   [ ] Verify new active item is highlighted
-   [ ] Click on another finance submenu item (e.g., General Ledger)
-   [ ] Verify Finance submenu remains expanded

#### Page Refresh

-   [ ] While on a finance page, refresh the browser
-   [ ] Verify Finance submenu is still expanded
-   [ ] Verify active item is still highlighted

#### Navigation to Different Menu

-   [ ] Click on a different parent menu (e.g., Inventory)
-   [ ] Verify Finance submenu collapses
-   [ ] Verify Inventory submenu expands
-   [ ] Navigate back to Finance submenu
-   [ ] Verify Finance submenu expands again

#### LocalStorage Persistence

-   [ ] Open browser developer tools
-   [ ] Check localStorage for 'sidebar_expanded_menus' key
-   [ ] Verify it contains the expanded menu IDs
-   [ ] Navigate between different menus
-   [ ] Verify localStorage is updated correctly

### 9. UI/UX Consistency

#### Button Placement

-   [ ] Verify Export, Import, Print buttons are in top-right corner on all pages
-   [ ] Verify buttons are consistently styled
-   [ ] Verify icons are appropriate (download, upload, printer)

#### Loading States

-   [ ] Click Export button
-   [ ] Verify loading indicator appears
-   [ ] Verify button is disabled during export
-   [ ] Click Import button
-   [ ] Verify loading indicator during upload
-   [ ] Verify progress bar (if applicable)

#### Notifications

-   [ ] Perform successful export
-   [ ] Verify success notification appears
-   [ ] Verify notification auto-dismisses after 5 seconds
-   [ ] Verify manual close button works
-   [ ] Perform failed import
-   [ ] Verify error notification appears
-   [ ] Verify error message is clear and actionable

#### Responsive Design

-   [ ] Test on desktop (1920x1080)
-   [ ] Verify all buttons are visible and functional
-   [ ] Test on tablet (768x1024)
-   [ ] Verify layout adapts appropriately
-   [ ] Test on mobile (375x667)
-   [ ] Verify buttons are accessible
-   [ ] Verify dropdowns work correctly

### 10. Performance Testing

#### Large Dataset Export

-   [ ] Create/use dataset with 1000+ journal entries
-   [ ] Export to XLSX
-   [ ] Verify export completes within reasonable time (< 30 seconds)
-   [ ] Verify file size is reasonable
-   [ ] Open file and verify all data is present

#### Large Dataset Import

-   [ ] Create import file with 500+ rows
-   [ ] Import file
-   [ ] Verify import completes within reasonable time
-   [ ] Verify progress indicator updates
-   [ ] Verify all valid rows are imported

#### PDF Generation Performance

-   [ ] Generate PDF with 100+ pages of data
-   [ ] Verify PDF generates within reasonable time
-   [ ] Verify PDF opens correctly
-   [ ] Verify all pages are rendered

### 11. Security Testing

#### Authentication

-   [ ] Log out
-   [ ] Attempt to access export URL directly
-   [ ] Verify redirect to login page
-   [ ] Attempt to access import URL directly
-   [ ] Verify redirect to login page

#### Authorization

-   [ ] Log in as user with limited permissions
-   [ ] Attempt to export data from unauthorized outlet
-   [ ] Verify access denied or filtered results
-   [ ] Attempt to import data to unauthorized outlet
-   [ ] Verify access denied

#### File Upload Security

-   [ ] Attempt to upload executable file (.exe)
-   [ ] Verify file type validation
-   [ ] Attempt to upload script file (.php, .js)
-   [ ] Verify file type validation
-   [ ] Attempt SQL injection in import data
-   [ ] Verify data is sanitized

### 12. Cross-Browser Testing

#### Chrome

-   [ ] Test all export functionality
-   [ ] Test all import functionality
-   [ ] Test all print functionality
-   [ ] Test sidebar state persistence

#### Firefox

-   [ ] Test all export functionality
-   [ ] Test all import functionality
-   [ ] Test all print functionality
-   [ ] Test sidebar state persistence

#### Edge

-   [ ] Test all export functionality
-   [ ] Test all import functionality
-   [ ] Test all print functionality
-   [ ] Test sidebar state persistence

#### Safari (if available)

-   [ ] Test all export functionality
-   [ ] Test all import functionality
-   [ ] Test all print functionality
-   [ ] Test sidebar state persistence

## Test Results

### Date: ******\_\_\_******

### Tester: ******\_\_\_******

### Summary

-   Total Tests: ******\_\_\_******
-   Passed: ******\_\_\_******
-   Failed: ******\_\_\_******
-   Blocked: ******\_\_\_******

### Issues Found

1. ***
2. ***
3. ***

### Notes

---

---

---

## Sign-off

Tested by: ******\_\_\_****** Date: ******\_\_\_******
Approved by: ******\_\_\_****** Date: ******\_\_\_******
