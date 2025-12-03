# Finance Export, Import, and Print - User Guide

## Overview

This guide explains how to use the export, import, and print functionality available across the finance module pages including Journal List (Daftar Jurnal), Accounting Book (Buku Akuntansi), Fixed Assets (Aktiva Tetap), and General Ledger (Buku Besar).

## Table of Contents

1. [Export Functionality](#export-functionality)
2. [Import Functionality](#import-functionality)
3. [Print Functionality](#print-functionality)
4. [Sidebar Navigation](#sidebar-navigation)
5. [Troubleshooting](#troubleshooting)

---

## Export Functionality

### Overview

Export functionality allows you to download financial data in two formats:

-   **XLSX (Excel)**: For data analysis and manipulation in spreadsheet applications
-   **PDF**: For formatted reports suitable for printing and sharing

### How to Export Data

1. **Navigate** to any finance page (Journal List, Accounting Book, Fixed Assets, or General Ledger)

2. **Apply Filters** (optional):

    - Select outlet from the dropdown
    - Choose date range using the date pickers
    - Apply any other relevant filters (status, category, etc.)
    - Only filtered data will be exported

3. **Click the Export Button**:

    - Located in the top-right corner of the data table
    - Icon: Download symbol (⬇)

4. **Select Export Format**:

    - Click **"Export ke XLSX"** for Excel format
    - Click **"Export ke PDF"** for PDF format

5. **Wait for Download**:
    - A loading indicator will appear
    - The file will automatically download to your browser's download folder
    - Success notification will appear when complete

### Export File Contents

#### Journal List Export

**XLSX Columns:**

-   Tanggal (Date)
-   No. Jurnal (Journal Number)
-   Kode Akun (Account Code)
-   Nama Akun (Account Name)
-   Deskripsi (Description)
-   Debit
-   Kredit (Credit)
-   Outlet

**PDF Format:**

-   Company header with logo
-   Report title and date range
-   Filtered data in table format
-   Debit/Credit totals at bottom

#### Accounting Book Export

**XLSX Columns:**

-   Varies based on report type selected
-   Includes transaction details, account information, and balances

**PDF Format:**

-   Follows accounting report standards
-   Includes proper sections and page breaks
-   Summary totals included

#### Fixed Assets Export

**XLSX Columns:**

-   Kode Aset (Asset Code)
-   Nama Aset (Asset Name)
-   Kategori (Category)
-   Tanggal Perolehan (Acquisition Date)
-   Harga Perolehan (Acquisition Cost)
-   Metode Penyusutan (Depreciation Method)
-   Umur Ekonomis (Useful Life in Years)
-   Akumulasi Penyusutan (Accumulated Depreciation)
-   Nilai Buku (Book Value)
-   Status

**PDF Format:**

-   Asset listing with depreciation details
-   Grouped by category
-   Summary totals for acquisition cost and book value

#### General Ledger Export

**XLSX Columns:**

-   Kode Akun (Account Code)
-   Nama Akun (Account Name)
-   Tanggal (Date)
-   Deskripsi (Description)
-   Debit
-   Kredit (Credit)
-   Saldo (Balance)

**PDF Format:**

-   Standard ledger report format
-   Running balances displayed
-   Opening and closing balances included

### Tips for Exporting

-   **Apply filters before exporting** to get only the data you need
-   **XLSX format** is best for further data analysis in Excel
-   **PDF format** is best for printing and sharing formatted reports
-   Large exports may take a few seconds to generate
-   Check your browser's download folder if the file doesn't appear immediately

---

## Import Functionality

### Overview

Import functionality allows you to bulk-upload data from Excel/CSV files, saving time compared to manual entry. Currently available for:

-   Journal List (Daftar Jurnal)
-   Fixed Assets (Aktiva Tetap)

### How to Import Data

1. **Download the Template**:

    - Navigate to the page where you want to import data
    - Click the **Import** button (upload icon ⬆)
    - In the import modal, click **"Download Template"**
    - Open the template file in Excel

2. **Fill in Your Data**:

    - Follow the column headers exactly as shown in the template
    - Include sample data as a reference
    - Do not modify column headers
    - Ensure data types match (dates, numbers, text)

3. **Save Your File**:

    - Save as Excel (.xlsx) or CSV (.csv) format
    - Keep file size under 5MB for optimal performance

4. **Upload the File**:

    - Click the **Import** button on the finance page
    - In the modal, click **"Choose File"** or drag and drop your file
    - The file will be validated automatically

5. **Review Results**:
    - Success message shows number of records imported
    - Error messages show specific validation failures with row numbers
    - Skipped records are reported separately

### Import File Format Requirements

#### Journal List Import Template

**Required Columns:**

-   `tanggal` (Date): Format YYYY-MM-DD (e.g., 2025-11-21)
-   `no_jurnal` (Journal Number): Unique identifier (e.g., JRN-001)
-   `kode_akun` (Account Code): Must exist in Chart of Accounts
-   `deskripsi` (Description): Text description (optional)
-   `debit`: Numeric value (0 if credit entry)
-   `kredit` (Credit): Numeric value (0 if debit entry)

**Validation Rules:**

-   Date must be valid and in correct format
-   Journal number must be unique
-   Account code must exist in the system
-   Either debit or credit must be greater than 0 (not both)
-   Debit and credit totals must balance for each journal entry

**Example:**

```
tanggal      | no_jurnal | kode_akun | deskripsi           | debit    | kredit
2025-11-21   | JRN-001   | 1010      | Kas masuk           | 1000000  | 0
2025-11-21   | JRN-001   | 4010      | Pendapatan jasa     | 0        | 1000000
```

#### Fixed Assets Import Template

**Required Columns:**

-   `kode_aset` (Asset Code): Unique identifier
-   `nama_aset` (Asset Name): Asset description
-   `kategori` (Category): Must match existing categories
-   `tanggal_perolehan` (Acquisition Date): Format YYYY-MM-DD
-   `harga_perolehan` (Acquisition Cost): Numeric value
-   `metode_penyusutan` (Depreciation Method): straight_line, declining_balance, or units_of_production
-   `umur_ekonomis` (Useful Life): Number of years
-   `status`: active, disposed, or under_maintenance

**Validation Rules:**

-   Asset code must be unique
-   Category must exist in the system
-   Acquisition date cannot be in the future
-   Acquisition cost must be greater than 0
-   Depreciation method must be one of the allowed values
-   Useful life must be between 1 and 50 years

**Example:**

```
kode_aset | nama_aset      | kategori | tanggal_perolehan | harga_perolehan | metode_penyusutan | umur_ekonomis | status
FA-001    | Laptop Dell    | Elektronik | 2025-01-15      | 15000000       | straight_line     | 5             | active
FA-002    | Meja Kantor    | Furniture  | 2025-02-01      | 2500000        | straight_line     | 10            | active
```

### Import Error Handling

**Common Errors:**

1. **"Validation failed"**: Check that all required fields are filled and data types are correct
2. **"Account code not found"**: Ensure the account code exists in your Chart of Accounts
3. **"Duplicate entry"**: The journal number or asset code already exists
4. **"Invalid date format"**: Use YYYY-MM-DD format for all dates
5. **"File too large"**: Reduce file size or split into multiple imports

**Error Messages Include:**

-   Row number where the error occurred
-   Specific validation failure reason
-   Guidance on how to fix the issue

### Tips for Importing

-   **Always download and use the template** to ensure correct format
-   **Test with a small file first** (5-10 rows) before importing large datasets
-   **Check for duplicates** in your data before importing
-   **Verify account codes** exist in your Chart of Accounts
-   **Keep a backup** of your original data before importing
-   **Review the import summary** to ensure all records were processed correctly

---

## Print Functionality

### Overview

Print functionality generates formatted PDF reports optimized for printing. Available on all finance pages.

### How to Print Reports

1. **Navigate** to the finance page you want to print

2. **Apply Filters** (optional):

    - Select outlet, date range, and other filters
    - Only filtered data will be included in the print

3. **Click the Print Button**:

    - Located in the top-right corner
    - Icon: Printer symbol (🖨)

4. **Review the PDF**:

    - PDF opens in a new browser tab
    - Review the content before printing

5. **Print or Save**:
    - Use browser's print function (Ctrl+P or Cmd+P)
    - Or save the PDF using browser's save function

### Print Report Features

**All Reports Include:**

-   Company header with logo and information
-   Report title and generation date
-   Applied filters displayed
-   Page numbers
-   Professional formatting

**Report-Specific Features:**

-   **Journal List**: Debit/Credit totals, grouped by journal entry
-   **Accounting Book**: Proper accounting sections, page breaks for long reports
-   **Fixed Assets**: Grouped by category, summary totals
-   **General Ledger**: Running balances, opening/closing balances

### Tips for Printing

-   **Apply filters** to print only relevant data
-   **Check page orientation** in print settings (Portrait vs Landscape)
-   **Adjust margins** if content is cut off
-   **Save as PDF** for digital archiving instead of printing
-   **Use print preview** to verify layout before printing

---

## Sidebar Navigation

### Overview

The sidebar now remembers which menus you have expanded, making navigation more efficient.

### How It Works

1. **Auto-Expand**: When you navigate to a page, its parent menu automatically expands

2. **Persistent State**: The expanded state is saved and restored when you:

    - Navigate to different pages
    - Refresh the browser
    - Return to the application later

3. **Manual Control**: You can still manually expand/collapse menus by clicking on them

### Benefits

-   **No repeated clicking**: Parent menus stay expanded as you navigate between related pages
-   **Faster navigation**: Quickly access related pages without re-expanding menus
-   **Consistent experience**: Your menu preferences are remembered across sessions

### Tips

-   The sidebar state is stored in your browser's local storage
-   Clearing browser data will reset the sidebar state
-   Each browser/device maintains its own sidebar state

---

## Troubleshooting

### Export Issues

**Problem**: Export button doesn't respond

-   **Solution**: Check your internet connection and try again
-   **Solution**: Refresh the page and try again

**Problem**: Downloaded file is empty or corrupted

-   **Solution**: Try a different export format (XLSX vs PDF)
-   **Solution**: Reduce the date range to export less data
-   **Solution**: Clear browser cache and try again

**Problem**: Export takes too long

-   **Solution**: Apply more specific filters to reduce data volume
-   **Solution**: Export data in smaller date ranges

### Import Issues

**Problem**: Import fails with validation errors

-   **Solution**: Download the template again and compare your file format
-   **Solution**: Check that all required columns are present
-   **Solution**: Verify data types match requirements (dates, numbers)

**Problem**: "Account code not found" error

-   **Solution**: Verify the account code exists in Chart of Accounts
-   **Solution**: Check for typos or extra spaces in account codes

**Problem**: File upload fails

-   **Solution**: Ensure file size is under 5MB
-   **Solution**: Save file as .xlsx format (not .xls)
-   **Solution**: Check that file is not password-protected

### Print Issues

**Problem**: PDF doesn't open

-   **Solution**: Check if pop-ups are blocked in your browser
-   **Solution**: Allow pop-ups for this site

**Problem**: Content is cut off in print

-   **Solution**: Adjust page margins in print settings
-   **Solution**: Try landscape orientation for wide tables
-   **Solution**: Reduce browser zoom level before printing

### General Issues

**Problem**: Buttons are disabled or grayed out

-   **Solution**: Check that you have the necessary permissions
-   **Solution**: Ensure you have selected an outlet
-   **Solution**: Verify that there is data to export/print

**Problem**: Notifications don't appear

-   **Solution**: Check browser console for JavaScript errors
-   **Solution**: Ensure notifications are not blocked by browser

### Getting Help

If you continue to experience issues:

1. Check the browser console for error messages (F12 key)
2. Take a screenshot of the error
3. Note the steps you took before the error occurred
4. Contact your system administrator with this information

---

## Best Practices

### Data Management

-   **Regular Exports**: Export important data regularly for backup purposes
-   **Consistent Naming**: Use consistent naming conventions for journal numbers and asset codes
-   **Data Validation**: Always validate imported data before processing large batches
-   **Filter Usage**: Use filters to work with manageable data sets

### Performance

-   **Smaller Batches**: Import data in smaller batches (100-500 rows) for better performance
-   **Off-Peak Hours**: Perform large imports during off-peak hours
-   **Browser Cache**: Clear browser cache if experiencing slow performance

### Security

-   **Sensitive Data**: Be cautious when exporting sensitive financial data
-   **File Storage**: Store exported files securely
-   **Access Control**: Only authorized users should have export/import permissions
-   **Audit Trail**: All export/import operations are logged for audit purposes

---

## Keyboard Shortcuts

-   **Ctrl+P / Cmd+P**: Print current page (when PDF is open)
-   **Ctrl+S / Cmd+S**: Save PDF (when PDF is open)
-   **Esc**: Close modal dialogs

---

## Support

For additional assistance or to report issues, please contact your system administrator or IT support team.

**Version**: 1.0  
**Last Updated**: November 2025
