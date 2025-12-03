# 📋 Next Tasks: Export/Import/Print & Sidebar Fix

## Task 1: Fungsikan Export, Import, Print

### Halaman yang Perlu Difungsikan:

1. **Jurnal** (`resources/views/admin/finance/jurnal/index.blade.php`)
2. **Accounting Book** (`resources/views/admin/finance/buku/index.blade.php`)
3. **Aktiva Tetap** (`resources/views/admin/finance/aktiva-tetap/index.blade.php`)
4. **Buku Besar** (`resources/views/admin/finance/buku-besar/index.blade.php`)

### Implementation Guide:

#### A. Export to Excel

**Frontend (Blade)**:

```javascript
async exportData() {
  try {
    const params = new URLSearchParams({
      outlet_id: this.filters.outlet,
      // ... other filters
    });

    const response = await fetch(`{{ route('finance.xxx.export') }}?${params}`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    });

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `export-${Date.now()}.xlsx`;
    document.body.appendChild(a);
    a.click();
    a.remove();
  } catch (error) {
    console.error('Export error:', error);
    alert('Gagal export data');
  }
}
```

**Backend (Controller)**:

```php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JournalExport; // Create this

public function exportJournal(Request $request)
{
    $outletId = $request->get('outlet_id');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    return Excel::download(
        new JournalExport($outletId, $startDate, $endDate),
        'jurnal-' . date('Y-m-d') . '.xlsx'
    );
}
```

**Export Class** (`app/Exports/JournalExport.php`):

```php
<?php

namespace App\Exports;

use App\Models\JournalEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JournalExport implements FromCollection, WithHeadings, WithMapping
{
    protected $outletId;
    protected $startDate;
    protected $endDate;

    public function __construct($outletId, $startDate, $endDate)
    {
        $this->outletId = $outletId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return JournalEntry::with(['details.account'])
            ->where('outlet_id', $this->outletId)
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Transaksi',
            'Tanggal',
            'Deskripsi',
            'Akun',
            'Debit',
            'Kredit',
            'Status'
        ];
    }

    public function map($journal): array
    {
        $rows = [];
        foreach ($journal->details as $detail) {
            $rows[] = [
                $journal->transaction_number,
                $journal->transaction_date->format('Y-m-d'),
                $journal->description,
                $detail->account->code . ' - ' . $detail->account->name,
                $detail->debit,
                $detail->credit,
                $journal->status
            ];
        }
        return $rows;
    }
}
```

#### B. Import from Excel

**Frontend**:

```javascript
async importData() {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.xlsx,.xls';

  input.onchange = async (e) => {
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append('file', file);
    formData.append('outlet_id', this.filters.outlet);

    try {
      const response = await fetch('{{ route("finance.xxx.import") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      });

      const result = await response.json();
      if (result.success) {
        alert(`Berhasil import ${result.imported} data`);
        this.loadData();
      } else {
        alert('Gagal import: ' + result.message);
      }
    } catch (error) {
      console.error('Import error:', error);
      alert('Gagal import data');
    }
  };

  input.click();
}
```

**Backend**:

```php
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JournalImport;

public function importJournal(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
        'outlet_id' => 'required'
    ]);

    try {
        $import = new JournalImport($request->outlet_id);
        Excel::import($import, $request->file('file'));

        return response()->json([
            'success' => true,
            'imported' => $import->getRowCount(),
            'message' => 'Data berhasil diimport'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
```

#### C. Print/PDF

**Frontend**:

```javascript
async printData() {
  // Option 1: Browser Print
  window.print();

  // Option 2: Generate PDF
  const params = new URLSearchParams({
    outlet_id: this.filters.outlet,
    // ... filters
  });

  window.open(`{{ route('finance.xxx.pdf') }}?${params}`, '_blank');
}
```

**Backend (using DomPDF)**:

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function generatePDF(Request $request)
{
    $outletId = $request->get('outlet_id');
    $data = JournalEntry::where('outlet_id', $outletId)->get();

    $pdf = Pdf::loadView('admin.finance.jurnal.pdf', [
        'data' => $data,
        'outlet' => Outlet::find($outletId)
    ]);

    return $pdf->download('jurnal-' . date('Y-m-d') . '.pdf');
    // or: return $pdf->stream(); // for preview
}
```

**PDF View** (`resources/views/admin/finance/jurnal/pdf.blade.php`):

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Jurnal - {{ $outlet->nama_outlet }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .text-right {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <h2>Jurnal Umum</h2>
        <p>Outlet: {{ $outlet->nama_outlet }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No Transaksi</th>
                    <th>Deskripsi</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $journal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $journal->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $journal->transaction_number }}</td>
                    <td>{{ $journal->description }}</td>
                    <td class="text-right">
                        {{ number_format($journal->total_debit, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($journal->total_credit, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
```

### Routes to Add:

```php
// routes/web.php
Route::prefix('finance')->name('finance.')->group(function () {
    // Jurnal
    Route::get('jurnal/export', [FinanceAccountantController::class, 'exportJournal'])->name('jurnal.export');
    Route::post('jurnal/import', [FinanceAccountantController::class, 'importJournal'])->name('jurnal.import');
    Route::get('jurnal/pdf', [FinanceAccountantController::class, 'generateJournalPDF'])->name('jurnal.pdf');

    // Accounting Book
    Route::get('buku/export', [FinanceAccountantController::class, 'exportBooks'])->name('books.export');
    Route::post('buku/import', [FinanceAccountantController::class, 'importBooks'])->name('books.import');

    // Aktiva Tetap
    Route::get('aktiva-tetap/export', [FinanceAccountantController::class, 'exportFixedAssets'])->name('fixed-assets.export');
    Route::post('aktiva-tetap/import', [FinanceAccountantController::class, 'importFixedAssets'])->name('fixed-assets.import');

    // Buku Besar
    Route::get('buku-besar/export', [FinanceAccountantController::class, 'exportLedger'])->name('ledger.export');
    Route::get('buku-besar/pdf', [FinanceAccountantController::class, 'generateLedgerPDF'])->name('ledger.pdf');
});
```

---

## Task 2: Fix Sidebar - Keep Submenu Expanded

### Problem:

Setelah klik submenu dan masuk ke halaman, submenu collapse lagi. User harus expand lagi untuk navigasi ke submenu lain.

### Solution:

#### A. Detect Active Menu & Keep Expanded

**Find Sidebar Component/File**:

-   Check: `resources/views/components/layouts/admin.blade.php`
-   Or: `resources/views/components/sidebar.blade.php`
-   Or: `resources/views/partials/sidebar.blade.php`

**Implementation**:

```html
<!-- Sidebar with Alpine.js -->
<div
    x-data="{ 
    openMenus: JSON.parse(localStorage.getItem('openMenus') || '[]'),
    currentPath: '{{ request()->path() }}'
}"
    x-init="
    // Auto-expand menu if current page is in submenu
    if (currentPath.includes('finance/')) {
        if (!openMenus.includes('finance')) {
            openMenus.push('finance');
            localStorage.setItem('openMenus', JSON.stringify(openMenus));
        }
    }
"
>
    <!-- Finance Menu -->
    <div>
        <button
            @click="
            if (openMenus.includes('finance')) {
                openMenus = openMenus.filter(m => m !== 'finance');
            } else {
                openMenus.push('finance');
            }
            localStorage.setItem('openMenus', JSON.stringify(openMenus));
        "
            class="menu-item"
        >
            <i class="bx bx-wallet"></i>
            <span>Finance</span>
            <i
                class="bx bx-chevron-down"
                :class="openMenus.includes('finance') ? 'rotate-180' : ''"
            ></i>
        </button>

        <!-- Submenu -->
        <div x-show="openMenus.includes('finance')" x-collapse class="submenu">
            <a
                href="{{ route('finance.jurnal.index') }}"
                :class="currentPath === 'finance/jurnal' ? 'active' : ''"
            >
                Jurnal
            </a>
            <a
                href="{{ route('finance.buku.index') }}"
                :class="currentPath === 'finance/buku' ? 'active' : ''"
            >
                Accounting Book
            </a>
            <!-- ... more submenus -->
        </div>
    </div>
</div>
```

#### B. Alternative: Use Session Storage

```javascript
// In Alpine.js component
{
    openMenus: [],

    init() {
        // Load from localStorage
        const saved = localStorage.getItem('sidebarState');
        if (saved) {
            this.openMenus = JSON.parse(saved);
        }

        // Auto-expand based on current URL
        const path = window.location.pathname;
        if (path.includes('/finance/')) {
            if (!this.openMenus.includes('finance')) {
                this.openMenus.push('finance');
                this.saveState();
            }
        }
    },

    toggleMenu(menuName) {
        const index = this.openMenus.indexOf(menuName);
        if (index > -1) {
            this.openMenus.splice(index, 1);
        } else {
            this.openMenus.push(menuName);
        }
        this.saveState();
    },

    saveState() {
        localStorage.setItem('sidebarState', JSON.stringify(this.openMenus));
    }
}
```

### Quick Fix (CSS Only):

If sidebar uses CSS `:hover` or similar, add class to keep expanded:

```css
/* Keep submenu expanded if any child is active */
.menu-item:has(.submenu a.active) .submenu {
    display: block !important;
}

.menu-item:has(.submenu a.active) .chevron {
    transform: rotate(180deg);
}
```

---

## Installation Requirements:

### For Excel Export/Import:

```bash
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### For PDF:

```bash
composer require barryvdh/laravel-dompdf
```

---

## Priority Order:

1. **Sidebar Fix** (Quick, high impact on UX)
2. **Export Excel** (Most requested feature)
3. **Print/PDF** (Important for reports)
4. **Import** (Less critical, can be done later)

---

## Testing Checklist:

### Export:

-   [ ] Export dengan filter
-   [ ] Export tanpa filter (all data)
-   [ ] File downloaded dengan nama yang benar
-   [ ] Data di Excel sesuai dengan tampilan

### Import:

-   [ ] Import file valid
-   [ ] Error handling untuk file invalid
-   [ ] Validation untuk data
-   [ ] Feedback jumlah data yang diimport

### Print:

-   [ ] Print preview
-   [ ] PDF generated dengan format yang benar
-   [ ] Header/footer sesuai
-   [ ] Page breaks di tempat yang tepat

### Sidebar:

-   [ ] Submenu tetap expand setelah navigate
-   [ ] State tersimpan setelah refresh
-   [ ] Multiple submenu bisa expand bersamaan
-   [ ] Smooth animation

---

**Ready to implement! 🚀**
