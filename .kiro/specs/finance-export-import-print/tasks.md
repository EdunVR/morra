# Implementation Plan

-   [x] 1. Set up export/import infrastructure

    -   Install and configure required packages (maatwebsite/excel, dompdf)
    -   Create base service classes for export and import operations
    -   Set up routes for export/import/print endpoints
    -   _Requirements: 1.1, 1.2, 2.1, 3.1_

-   [x] 2. Implement Journal List (Daftar Jurnal) export functionality

    -   [x] 2.1 Create JournalExport class with XLSX support

        -   Implement FromCollection, WithHeadings, WithMapping interfaces
        -   Add proper column headers and data mapping
        -   Include filter support for outlet, date range, and status
        -   _Requirements: 1.2_

    -   [x] 2.2 Create Journal PDF export view and controller method

        -   Design PDF template with company header and journal table
        -   Implement controller method to generate PDF from filtered data
        -   Add proper formatting for debit/credit columns
        -   _Requirements: 1.3_

    -   [x] 2.3 Add export dropdown UI to journal index page

        -   Create Alpine.js component for export dropdown
        -   Add XLSX and PDF export buttons with icons
        -   Implement loading states and error handling
        -   Wire up API calls to backend endpoints
        -   _Requirements: 1.1, 5.1, 5.2, 5.3_

-   [x] 3. Implement Journal List import functionality

    -   [x] 3.1 Create JournalImport class with validation

        -   Implement ToModel, WithHeadingRow, WithValidation interfaces
        -   Add validation rules for required fields
        -   Implement error collection and reporting
        -   _Requirements: 2.2, 2.3_

    -   [x] 3.2 Create import modal UI component

        -   Design file upload modal with drag-and-drop
        -   Add file validation (type, size) on frontend
        -   Implement upload progress indicator
        -   Display import results with success/error counts
        -   _Requirements: 2.1, 2.4, 5.3, 5.4_

    -   [x] 3.3 Create controller method for import processing

        -   Handle file upload and validation
        -   Process import using JournalImport class
        -   Return detailed results including errors
        -   _Requirements: 2.2, 2.3, 2.4, 2.5_

-   [x] 4. Implement Accounting Book export and print functionality

    -   [x] 4.1 Create AccountingBookExport class for XLSX

        -   Map accounting book data to Excel format
        -   Include columns based on report type
        -   Apply filters for outlet and period
        -   _Requirements: 1.4_

    -   [x] 4.2 Create Accounting Book PDF template

        -   Design PDF layout following accounting standards
        -   Implement proper page breaks for long reports
        -   Add summary sections and totals
        -   _Requirements: 1.5, 3.2_

    -   [x] 4.3 Add export/print buttons to accounting book page

        -   Integrate export dropdown component
        -   Add print button with PDF generation
        -   Ensure filters are passed to export/print functions
        -   _Requirements: 1.10, 3.5, 5.1, 5.2_

-   [x] 5. Implement Fixed Assets (Aktiva Tetap) export and import functionality

    -   [x] 5.1 Create FixedAssetsExport class for XLSX

        -   Map asset data including depreciation details
        -   Include columns: code, name, category, acquisition date/cost, depreciation method, useful life, accumulated depreciation, book value
        -   Apply status and category filters
        -   _Requirements: 1.6_

    -   [x] 5.2 Create Fixed Assets PDF export template

        -   Design PDF with asset listing and depreciation details
        -   Add summary totals for acquisition cost and book value
        -   Group by category if applicable
        -   _Requirements: 1.7, 3.3_

    -   [x] 5.3 Create FixedAssetsImport class

        -   Implement validation for asset data
        -   Handle category and depreciation method validation
        -   Calculate initial depreciation if needed
        -   _Requirements: 2.2, 2.3_

    -   [x] 5.4 Update Fixed Assets page UI

        -   Export dropdown already exists, add PDF option
        -   Import button already exists, wire up functionality
        -   Ensure all buttons work with current filters
        -   _Requirements: 1.10, 5.1, 5.2_

-   [x] 6. Implement General Ledger (Buku Besar) export and print functionality

    -   [x] 6.1 Create GeneralLedgerExport class for XLSX

        -   Map ledger entries with running balance
        -   Include columns: account code, account name, date, description, debit, credit, balance
        -   Apply account and date range filters
        -   _Requirements: 1.8_

    -   [x] 6.2 Create General Ledger PDF template

        -   Design PDF showing ledger accounts with running balances
        -   Format similar to standard accounting ledger reports
        -   Include opening and closing balances
        -   _Requirements: 1.9, 3.4_

    -   [x] 6.3 Update General Ledger page UI

        -   Export and print buttons already exist
        -   Add dropdown for XLSX/PDF selection
        -   Wire up to backend endpoints with filters
        -   _Requirements: 1.10, 3.5, 5.1, 5.2_

-   [x] 7. Implement sidebar submenu state persistence

    -   [x] 7.1 Create Alpine.js sidebar state management component

        -   Implement expandedMenus array with localStorage persistence
        -   Add init() method to load saved state
        -   Create expandActiveMenu() to auto-expand based on current route
        -   Implement toggleMenu() and isExpanded() methods
        -   _Requirements: 4.2, 4.5_

    -   [x] 7.2 Update sidebar.blade.php component

        -   Add x-data="sidebarState" to sidebar container
        -   Add data-menu-parent attributes to parent menu items
        -   Bind menu expansion to isExpanded() method
        -   Add @click handlers for menu toggle
        -   _Requirements: 4.1, 4.3, 4.4_

    -   [x] 7.3 Test sidebar state across navigation

        -   Verify state persists on page refresh
        -   Test navigation between different submenus
        -   Ensure only one parent menu expanded at a time (optional behavior)
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

-   [x] 8. Create reusable export/import components

    -   [x] 8.1 Create shared Alpine.js export mixin

        -   Extract common export logic into reusable function
        -   Support both XLSX and PDF export
        -   Handle loading states and error messages
        -   _Requirements: 5.3, 5.4, 5.5_

    -   [x] 8.2 Create shared Alpine.js import mixin

        -   Extract common import logic into reusable function
        -   Handle file validation and upload
        -   Display progress and results
        -   _Requirements: 5.3, 5.4, 5.5_

    -   [x] 8.3 Create shared notification system

        -   Implement toast notification component
        -   Support success, error, and info types
        -   Auto-dismiss with manual close option
        -   _Requirements: 5.4, 5.5_

-   [x] 9. Add backend API endpoints and controllers

    -   [x] 9.1 Create export routes for all modules

        -   Add routes for journal, accounting-book, fixed-assets, general-ledger
        -   Support both /export/xlsx and /export/pdf endpoints
        -   Apply authentication and authorization middleware
        -   _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9_

    -   [x] 9.2 Create import routes for applicable modules

        -   Add routes for journal and fixed-assets import
        -   Include file upload handling
        -   Apply validation middleware
        -   _Requirements: 2.1, 2.2, 2.3, 2.4_

    -   [x] 9.3 Implement controller methods in FinanceAccountantController

        -   Add exportJournalXLSX(), exportJournalPDF() methods
        -   Add importJournal() method
        -   Add similar methods for other modules
        -   Use FinanceExportService and FinanceImportService
        -   _Requirements: 1.1-1.10, 2.1-2.5, 3.1-3.5_

-   [x] 10. Create PDF templates for all modules

    -   [x] 10.1 Create journal PDF template (resources/views/admin/finance/jurnal/pdf.blade.php)

        -   Design header with company info and report title

        -   Create table with journal entries
        -   Add footer with totals and page numbers
        -   _Requirements: 1.3, 3.1_

    -   [x] 10.2 Create accounting book PDF template

        -   Follow accounting report standards
        -   Include proper sections and page breaks
        -   Add summary and totals
        -   _Requirements: 1.5, 3.2_

    -   [x] 10.3 Create fixed assets PDF template

        -   List assets with depreciation details
        -   Group by category
        -   Include summary totals
        -   _Requirements: 1.7, 3.3_

    -   [x] 10.4 Create general ledger PDF template

        -   Format as standard ledger report
        -   Show running balances
        -   Include opening and closing balances
        -   _Requirements: 1.9, 3.4_

-   [x] 11. Implement filter integration for export/print

    -   [x] 11.1 Update frontend to pass filter parameters

        -   Collect current filter values (outlet, date range, status, etc.)

        -   Append to export/print API calls as query parameters
        -   _Requirements: 1.10, 3.5_

    -   [x] 11.2 Update backend to apply filters to export data

        -   Parse filter parameters from request
        -   Apply filters to database queries
        -   Pass filtered data to export classes
        -   _Requirements: 1.10, 3.5_

-   [x] 12. Add download template functionality for imports

    -   [x] 12.1 Create template Excel files

        -   Design template for journal import with sample data
        -   Design template for fixed assets import with sample data
        -   Include column headers and data format examples
        -   _Requirements: 2.1_

    -   [x] 12.2 Add download template buttons

        -   Add "Download Template" link in import modal
        -   Serve template files from storage or generate dynamically
        -   _Requirements: 2.1_

-   [x] 13. Testing and validation

    -   [x] 13.1 Write unit tests for export classes

        -   Test JournalExport data mapping
        -   Test FixedAssetsExport data mapping
        -   Test GeneralLedgerExport data mapping
        -   _Requirements: All export requirements_

    -   [x] 13.2 Write unit tests for import classes

        -   Test JournalImport validation
        -   Test FixedAssetsImport validation
        -   Test error handling and reporting
        -   _Requirements: All import requirements_

    -   [x] 13.3 Write integration tests for complete flows

        -   Test export flow from request to file download
        -   Test import flow from upload to database insertion
        -   Test print flow with PDF generation
        -   Test sidebar state persistence
        -   _Requirements: All requirements_

    -   [x] 13.4 Perform manual testing

        -   Test all export formats with various filters
        -   Test import with valid and invalid files
        -   Test print functionality across all modules
        -   Test sidebar behavior across navigation
        -   Verify UI consistency and responsiveness
        -   _Requirements: All requirements_

-   [x] 14. Documentation and cleanup

    -   [x] 14.1 Update user documentation

        -   Document how to use export functionality
        -   Document import file format requirements
        -   Document print functionality
        -   _Requirements: 5.4, 5.5_

    -   [x] 14.2 Add code comments and docblocks

        -   Document export service methods
        -   Document import service methods
        -   Document Alpine.js components
        -   _Requirements: All requirements_

    -   [x] 14.3 Clean up and optimize code

        -   Remove any debug code
        -   Optimize database queries
        -   Ensure consistent code style
        -   _Requirements: All requirements_
