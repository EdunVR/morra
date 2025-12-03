# Requirements Document

## Introduction

This feature enhances the finance module by implementing export, import, and print functionality for key financial pages (Journal List, Accounting Book, Fixed Assets, General Ledger). Additionally, it improves the sidebar user experience by maintaining the expanded state of submenus after navigation, eliminating the need for users to repeatedly expand menus.

## Glossary

-   **Finance Module**: The accounting and financial management section of the ERP system
-   **Journal List (Daftar Jurnal)**: The page displaying all journal entries
-   **Accounting Book (Buku Akuntansi)**: The page showing accounting records and transactions
-   **Fixed Assets (Aktiva Tetap)**: The page managing fixed asset records and depreciation
-   **General Ledger (Buku Besar)**: The page displaying general ledger accounts and balances
-   **Sidebar Component**: The navigation menu component located in resources/views/components/sidebar.blade.php
-   **Export Function**: Feature to download data in XLSX (Excel) or PDF format
-   **Import Function**: Feature to upload and process data from Excel/CSV files
-   **Print Function**: Feature to directly print or generate printable PDF reports
-   **Submenu State**: The expanded or collapsed state of navigation menu items

## Requirements

### Requirement 1: Export Functionality for Finance Pages

**User Story:** As a finance user, I want to export data from finance pages to XLSX or PDF format, so that I can analyze data offline or share with stakeholders in their preferred format.

#### Acceptance Criteria

1. WHEN the user clicks the Export button on any finance page, THE Finance Module SHALL display a dropdown menu with options for XLSX and PDF formats
2. WHEN the user selects XLSX export on Journal List page, THE Finance Module SHALL generate an Excel file containing all journal entries with columns: date, journal number, account code, account name, description, debit, credit, and outlet
3. WHEN the user selects PDF export on Journal List page, THE Finance Module SHALL generate a formatted PDF document containing all journal entries with proper headers and company information
4. WHEN the user selects XLSX export on Accounting Book page, THE Finance Module SHALL generate an Excel file containing all accounting transactions with relevant columns based on the selected report type
5. WHEN the user selects PDF export on Accounting Book page, THE Finance Module SHALL generate a formatted PDF document with accounting transactions following accounting report standards
6. WHEN the user selects XLSX export on Fixed Assets page, THE Finance Module SHALL generate an Excel file containing all fixed assets with columns: asset code, name, category, acquisition date, acquisition cost, depreciation method, useful life, accumulated depreciation, and book value
7. WHEN the user selects PDF export on Fixed Assets page, THE Finance Module SHALL generate a formatted PDF document listing all assets with depreciation details and summary totals
8. WHEN the user selects XLSX export on General Ledger page, THE Finance Module SHALL generate an Excel file containing ledger entries with columns: account code, account name, date, description, debit, credit, and balance
9. WHEN the user selects PDF export on General Ledger page, THE Finance Module SHALL generate a formatted PDF document showing ledger accounts with running balances
10. WHERE filters are applied on any page, THE Finance Module SHALL export only the filtered data matching the current view in the selected format

### Requirement 2: Import Functionality for Finance Pages

**User Story:** As a finance user, I want to import data from Excel/CSV files into finance pages, so that I can efficiently bulk-upload records without manual entry.

#### Acceptance Criteria

1. WHEN the user clicks the Import button on Journal List page, THE Finance Module SHALL display a file upload dialog accepting Excel/CSV files
2. WHEN a valid import file is uploaded, THE Finance Module SHALL validate each row against business rules and data constraints
3. IF validation errors are detected during import, THEN THE Finance Module SHALL display detailed error messages indicating row numbers and specific validation failures
4. WHEN import validation succeeds, THE Finance Module SHALL insert all records into the database and display a success message with the count of imported records
5. WHERE the import file contains duplicate entries, THE Finance Module SHALL skip duplicates and report them in the import summary

### Requirement 3: Print Functionality for Finance Pages

**User Story:** As a finance user, I want to print reports from finance pages in PDF format, so that I can maintain physical records and share formatted reports.

#### Acceptance Criteria

1. WHEN the user clicks the Print button on Journal List page, THE Finance Module SHALL generate a PDF report with proper formatting including header, company information, and journal entries table
2. WHEN the user clicks the Print button on Accounting Book page, THE Finance Module SHALL generate a PDF report formatted according to accounting standards with proper page breaks
3. WHEN the user clicks the Print button on Fixed Assets page, THE Finance Module SHALL generate a PDF report listing all assets with depreciation details and summary totals
4. WHEN the user clicks the Print button on General Ledger page, THE Finance Module SHALL generate a PDF report showing ledger accounts with running balances
5. WHERE filters are applied on any page, THE Finance Module SHALL print only the filtered data matching the current view

### Requirement 4: Sidebar Submenu State Persistence

**User Story:** As a system user, I want the sidebar submenu to remain expanded after I navigate to a submenu page, so that I can easily access related pages without repeatedly expanding the menu.

#### Acceptance Criteria

1. WHEN the user clicks a submenu item and navigates to that page, THE Sidebar Component SHALL maintain the expanded state of the parent menu
2. WHEN the page loads with an active submenu item, THE Sidebar Component SHALL automatically expand the parent menu containing that active item
3. WHEN the user navigates between different submenu items under the same parent, THE Sidebar Component SHALL keep the parent menu expanded
4. WHEN the user clicks a different parent menu item, THE Sidebar Component SHALL collapse the previous parent and expand the new parent
5. WHERE the user refreshes the page, THE Sidebar Component SHALL restore the expanded state based on the current active route

### Requirement 5: UI Integration and User Experience

**User Story:** As a finance user, I want export, import, and print buttons to be consistently placed and styled across all finance pages, so that I can easily find and use these features.

#### Acceptance Criteria

1. THE Finance Module SHALL display Export, Import, and Print buttons in a consistent location on all finance pages (top-right of the data table)
2. THE Finance Module SHALL use consistent button styling with appropriate icons for Export (download icon), Import (upload icon), and Print (printer icon)
3. WHEN any export, import, or print operation is in progress, THE Finance Module SHALL display a loading indicator to inform the user
4. WHEN any operation completes successfully, THE Finance Module SHALL display a success notification with relevant details
5. IF any operation fails, THEN THE Finance Module SHALL display an error notification with actionable guidance for the user
