# Design Document

## Overview

This design document outlines the technical implementation for adding export (XLSX/PDF), import, and print functionality to finance pages (Journal List, Accounting Book, Fixed Assets, General Ledger), along with sidebar submenu state persistence. The solution leverages Laravel's existing packages (Maatwebsite Excel, DomPDF/TCPDF) and Alpine.js for frontend interactions.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer (Alpine.js)              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Export Menu  │  │ Import Modal │  │ Print Button │     │
│  │ (XLSX/PDF)   │  │              │  │              │     │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘     │
│         │                  │                  │              │
└─────────┼──────────────────┼──────────────────┼──────────────┘
          │                  │                  │
          ▼                  ▼                  ▼
┌─────────────────────────────────────────────────────────────┐
│              Backend Layer (Laravel Controllers)             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Export       │  │ Import       │  │ PDF          │     │
│  │ Controller   │  │ Controller   │  │ Generator    │     │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘     │
│         │                  │                  │              │
└─────────┼──────────────────┼──────────────────┼──────────────┘
          │                  │                  │
          ▼                  ▼                  ▼
┌─────────────────────────────────────────────────────────────┐
│                    Data Layer (Models)                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ JournalEntry │  │ FixedAsset   │  │ ChartOfAccount│    │
│  │ AccountingBook│  │ GeneralLedger│  │              │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### Component Interaction Flow

1. **Export Flow**: User clicks Export → Dropdown shows XLSX/PDF → Selection triggers API call → Backend generates file → Download initiated
2. **Import Flow**: User clicks Import → File upload modal → File validation → Backend processes → Success/Error feedback
3. **Print Flow**: User clicks Print → Backend generates PDF → Opens in new tab for printing
4. **Sidebar Flow**: Page loads → JavaScript detects active route → Expands parent menu → Stores state in localStorage

## Components and Interfaces

### 1. Frontend Components (Alpine.js)

#### Export Dropdown Component

```javascript
// Reusable export dropdown component
exportDropdown() {
    return {
        isOpen: false,
        isExporting: false,

        async exportToXLSX() {
            this.isExporting = true;
            try {
                const response = await fetch(`/api/finance/${this.module}/export/xlsx?${this.getFilterParams()}`);
                const blob = await response.blob();
                this.downloadFile(blob, `${this.module}_${Date.now()}.xlsx`);
                this.showSuccess('Data berhasil diekspor ke XLSX');
            } catch (error) {
                this.showError('Gagal mengekspor data: ' + error.message);
            } finally {
                this.isExporting = false;
                this.isOpen = false;
            }
        },

        async exportToPDF() {
            this.isExporting = true;
            try {
                const response = await fetch(`/api/finance/${this.module}/export/pdf?${this.getFilterParams()}`);
                const blob = await response.blob();
                this.downloadFile(blob, `${this.module}_${Date.now()}.pdf`);
                this.showSuccess('Data berhasil diekspor ke PDF');
            } catch (error) {
                this.showError('Gagal mengekspor data: ' + error.message);
            } finally {
                this.isExporting = false;
                this.isOpen = false;
            }
        }
    }
}
```

#### Import Modal Component

```javascript
// Reusable import modal component
importModal() {
    return {
        isOpen: false,
        file: null,
        isUploading: false,
        uploadProgress: 0,

        async uploadFile() {
            if (!this.file) {
                this.showError('Pilih file terlebih dahulu');
                return;
            }

            this.isUploading = true;
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('outlet_id', this.selectedOutlet);

            try {
                const response = await fetch(`/api/finance/${this.module}/import`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.showSuccess(`Berhasil mengimpor ${result.imported_count} data`);
                    this.loadData(); // Reload table data
                    this.closeModal();
                } else {
                    this.showError(result.message);
                }
            } catch (error) {
                this.showError('Gagal mengimpor data: ' + error.message);
            } finally {
                this.isUploading = false;
            }
        }
    }
}
```

#### Sidebar State Management

```javascript
// Sidebar state persistence
document.addEventListener("alpine:init", () => {
    Alpine.data("sidebarState", () => ({
        expandedMenus: [],

        init() {
            // Load saved state from localStorage
            const saved = localStorage.getItem("sidebar_expanded_menus");
            if (saved) {
                this.expandedMenus = JSON.parse(saved);
            }

            // Auto-expand menu containing active route
            this.expandActiveMenu();
        },

        expandActiveMenu() {
            const currentPath = window.location.pathname;
            const activeMenuItem = document.querySelector(
                `a[href="${currentPath}"]`
            );

            if (activeMenuItem) {
                const parentMenu = activeMenuItem.closest("[data-menu-parent]");
                if (parentMenu) {
                    const menuId = parentMenu.dataset.menuParent;
                    if (!this.expandedMenus.includes(menuId)) {
                        this.expandedMenus.push(menuId);
                        this.saveState();
                    }
                }
            }
        },

        toggleMenu(menuId) {
            const index = this.expandedMenus.indexOf(menuId);
            if (index > -1) {
                this.expandedMenus.splice(index, 1);
            } else {
                this.expandedMenus.push(menuId);
            }
            this.saveState();
        },

        isExpanded(menuId) {
            return this.expandedMenus.includes(menuId);
        },

        saveState() {
            localStorage.setItem(
                "sidebar_expanded_menus",
                JSON.stringify(this.expandedMenus)
            );
        },
    }));
});
```

### 2. Backend Components (Laravel)

#### Export Service Class

```php
// app/Services/FinanceExportService.php
namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceExportService
{
    public function exportToXLSX(string $module, array $data, array $filters)
    {
        $exportClass = $this->getExportClass($module);
        return Excel::download(new $exportClass($data, $filters), "{$module}_export.xlsx");
    }

    public function exportToPDF(string $module, array $data, array $filters)
    {
        $view = $this->getPDFView($module);
        $pdf = Pdf::loadView($view, compact('data', 'filters'));
        return $pdf->download("{$module}_export.pdf");
    }

    private function getExportClass(string $module): string
    {
        return match($module) {
            'journal' => \App\Exports\JournalExport::class,
            'accounting-book' => \App\Exports\AccountingBookExport::class,
            'fixed-assets' => \App\Exports\FixedAssetsExport::class,
            'general-ledger' => \App\Exports\GeneralLedgerExport::class,
        };
    }

    private function getPDFView(string $module): string
    {
        return match($module) {
            'journal' => 'admin.finance.jurnal.pdf',
            'accounting-book' => 'admin.finance.accounting-book.pdf',
            'fixed-assets' => 'admin.finance.aktiva-tetap.pdf',
            'general-ledger' => 'admin.finance.buku-besar.pdf',
        };
    }
}
```

#### Import Service Class

```php
// app/Services/FinanceImportService.php
namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class FinanceImportService
{
    public function import(string $module, $file, array $additionalData = [])
    {
        $importClass = $this->getImportClass($module);

        try {
            $import = new $importClass($additionalData);
            Excel::import($import, $file);

            return [
                'success' => true,
                'imported_count' => $import->getImportedCount(),
                'skipped_count' => $import->getSkippedCount(),
                'errors' => $import->getErrors()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function getImportClass(string $module): string
    {
        return match($module) {
            'journal' => \App\Imports\JournalImport::class,
            'fixed-assets' => \App\Imports\FixedAssetsImport::class,
        };
    }
}
```

## Data Models

### Export Classes Structure

#### JournalExport

```php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class JournalExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Jurnal',
            'Kode Akun',
            'Nama Akun',
            'Deskripsi',
            'Debit',
            'Kredit',
            'Outlet'
        ];
    }

    public function map($row): array
    {
        return [
            $row->date,
            $row->journal_number,
            $row->account_code,
            $row->account_name,
            $row->description,
            $row->debit,
            $row->credit,
            $row->outlet_name
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
```

#### FixedAssetsExport

```php
namespace App\Exports;

class FixedAssetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function headings(): array
    {
        return [
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Tanggal Perolehan',
            'Harga Perolehan',
            'Metode Penyusutan',
            'Umur Ekonomis (Tahun)',
            'Akumulasi Penyusutan',
            'Nilai Buku',
            'Status'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_code,
            $asset->asset_name,
            $asset->category,
            $asset->acquisition_date,
            $asset->acquisition_cost,
            $asset->depreciation_method,
            $asset->useful_life,
            $asset->accumulated_depreciation,
            $asset->book_value,
            $asset->status
        ];
    }
}
```

### Import Classes Structure

#### JournalImport

```php
namespace App\Imports;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JournalImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $outletId;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function __construct($additionalData)
    {
        $this->outletId = $additionalData['outlet_id'];
    }

    public function model(array $row)
    {
        try {
            // Validate and create journal entry
            $journal = JournalEntry::create([
                'outlet_id' => $this->outletId,
                'journal_number' => $row['no_jurnal'],
                'date' => $row['tanggal'],
                'description' => $row['deskripsi'],
            ]);

            // Create journal detail
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_code' => $row['kode_akun'],
                'debit' => $row['debit'] ?? 0,
                'credit' => $row['kredit'] ?? 0,
            ]);

            $this->importedCount++;
            return $journal;
        } catch (\Exception $e) {
            $this->skippedCount++;
            $this->errors[] = "Row {$row['no_jurnal']}: " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'no_jurnal' => 'required|string',
            'kode_akun' => 'required|exists:chart_of_accounts,code',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function getImportedCount() { return $this->importedCount; }
    public function getSkippedCount() { return $this->skippedCount; }
    public function getErrors() { return $this->errors; }
}
```

## Error Handling

### Frontend Error Handling

-   Display user-friendly error messages using toast notifications
-   Show validation errors inline for import operations
-   Provide retry options for failed operations
-   Log errors to browser console for debugging

### Backend Error Handling

```php
try {
    // Export/Import operation
} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
    return response()->json([
        'success' => false,
        'message' => 'Validasi gagal',
        'errors' => $e->failures()
    ], 422);
} catch (\Exception $e) {
    \Log::error("Export/Import error: " . $e->getMessage());
    return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem'
    ], 500);
}
```

## Testing Strategy

### Unit Tests

-   Test export service methods for each module
-   Test import validation logic
-   Test PDF generation with various data sets
-   Test sidebar state management functions

### Integration Tests

-   Test complete export flow (request → file generation → download)
-   Test complete import flow (upload → validation → database insertion)
-   Test print functionality with filtered data
-   Test sidebar state persistence across page navigation

### Manual Testing Checklist

-   [ ] Export to XLSX with filters applied
-   [ ] Export to PDF with proper formatting
-   [ ] Import valid file successfully
-   [ ] Import invalid file shows proper errors
-   [ ] Print generates correct PDF
-   [ ] Sidebar remains expanded after navigation
-   [ ] Sidebar state persists after page refresh
-   [ ] All operations work across different outlets

## UI/UX Considerations

### Button Placement

-   Export, Import, Print buttons positioned consistently in top-right corner
-   Buttons use recognizable icons (download, upload, printer)
-   Loading states show spinner and disable buttons during operations

### Export Dropdown Design

```html
<div x-data="{ exportOpen: false }" class="relative">
    <button
        @click="exportOpen = !exportOpen"
        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50"
    >
        <i class="bx bx-export"></i> Export
        <i class="bx bx-chevron-down text-sm"></i>
    </button>

    <div
        x-show="exportOpen"
        @click.away="exportOpen = false"
        class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10"
    >
        <button
            @click="exportToXLSX()"
            class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2"
        >
            <i class="bx bx-file"></i> Export ke XLSX
        </button>
        <button
            @click="exportToPDF()"
            class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2"
        >
            <i class="bx bxs-file-pdf"></i> Export ke PDF
        </button>
    </div>
</div>
```

### Import Modal Design

-   File upload with drag-and-drop support
-   Progress bar during upload
-   Clear error messages with row numbers
-   Download template button for correct format

### Notification System

-   Success: Green toast with checkmark icon
-   Error: Red toast with error icon
-   Info: Blue toast with info icon
-   Auto-dismiss after 5 seconds with manual close option

## Performance Considerations

### Export Optimization

-   Use chunking for large datasets (>10,000 rows)
-   Queue export jobs for very large exports
-   Cache frequently exported data
-   Compress PDF files for faster downloads

### Import Optimization

-   Batch insert records (100 rows at a time)
-   Use database transactions for data integrity
-   Validate in chunks to provide faster feedback
-   Queue import jobs for large files (>1000 rows)

### Sidebar Performance

-   Use localStorage for state persistence (lightweight)
-   Debounce state save operations
-   Minimize DOM manipulations

## Security Considerations

-   Validate file types and sizes on both frontend and backend
-   Sanitize imported data to prevent SQL injection
-   Check user permissions before allowing export/import
-   Use CSRF tokens for all POST requests
-   Limit file upload size (max 5MB)
-   Scan uploaded files for malware
-   Log all export/import operations for audit trail

## Dependencies

### PHP Packages

-   `maatwebsite/excel`: ^3.1 - Excel import/export
-   `barryvdh/laravel-dompdf`: ^2.0 - PDF generation
-   Laravel framework: ^10.0

### JavaScript Libraries

-   Alpine.js: ^3.x (already in use)
-   Chart.js: ^4.x (already in use)

### Browser Requirements

-   Modern browsers with ES6 support
-   localStorage support for sidebar state
-   Blob API support for file downloads
