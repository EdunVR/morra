# Contoh Implementasi Export PDF & Excel

## Skenario: Export Daftar Supplier

Mari kita buat export PDF dan Excel untuk modul Supplier sebagai contoh lengkap.

---

## 1. Controller Method

```php
<?php
// app/Http/Controllers/SupplierController.php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Exports\SupplierExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Export suppliers to PDF (stream mode)
     */
    public function exportPdf(Request $request)
    {
        // Get filters
        $filters = [
            'status' => $request->get('status', 'all'),
            'search' => $request->get('search', ''),
            'company_name' => config('app.name'),
            'outlet_name' => auth()->user()->outlet->nama_outlet ?? 'Semua Outlet'
        ];

        // Get data
        $query = Supplier::query();

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('kode', 'like', '%' . $filters['search'] . '%');
            });
        }

        $data = $query->orderBy('nama')->get();

        // Generate PDF
        $pdf = Pdf::loadView('admin.supplier.pdf', compact('data', 'filters'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-right', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm');

        $filename = 'supplier_' . now()->format('Y-m-d_His') . '.pdf';

        // Stream untuk preview
        return $pdf->stream($filename);
    }

    /**
     * Export suppliers to Excel
     */
    public function exportExcel(Request $request)
    {
        // Get filters (sama seperti PDF)
        $filters = [
            'status' => $request->get('status', 'all'),
            'search' => $request->get('search', ''),
        ];

        // Get data
        $query = Supplier::query();

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('kode', 'like', '%' . $filters['search'] . '%');
            });
        }

        $data = $query->orderBy('nama')->get();

        $filename = 'supplier_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new SupplierExport($data, $filters), $filename);
    }
}
```

---

## 2. PDF Template

```blade
{{-- resources/views/admin/supplier/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Supplier</title>
    <style>
        @page {
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
            padding: 5mm;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            color: #1a1a1a;
        }

        .header h2 {
            font-size: 14pt;
            margin-bottom: 10px;
            color: #4a4a4a;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #3B82F6;
        }

        .filter-info p {
            margin: 3px 0;
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #3B82F6;
            color: white;
        }

        table th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #ddd;
        }

        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            display: inline-block;
        }

        .status-active {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-inactive {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .summary-box {
            margin-top: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $filters['company_name'] ?? 'Nama Perusahaan' }}</h1>
        <h2>Daftar Supplier</h2>
        @if(isset($filters['outlet_name']))
            <p style="font-size: 10pt; color: #666;">{{ $filters['outlet_name'] }}</p>
        @endif
    </div>

    @if(!empty($filters))
    <div class="filter-info">
        <p><strong>Filter yang Diterapkan:</strong></p>
        @if(isset($filters['status']) && $filters['status'] !== 'all')
            <p>Status: {{ ucfirst($filters['status']) }}</p>
        @endif
        @if(isset($filters['search']) && $filters['search'])
            <p>Pencarian: {{ $filters['search'] }}</p>
        @endif
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Kode</th>
                <th style="width: 20%;">Nama Supplier</th>
                <th style="width: 15%;">Kontak</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $supplier)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $supplier->kode }}</td>
                    <td>{{ $supplier->nama }}</td>
                    <td>{{ $supplier->telepon ?? '-' }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td>{{ $supplier->alamat ?? '-' }}</td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $supplier->status }}">
                            {{ $supplier->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada data supplier
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($data) > 0)
    <div class="summary-box">
        <p><strong>Total Supplier:</strong> {{ count($data) }}</p>
        <p><strong>Aktif:</strong> {{ $data->where('status', 'active')->count() }}</p>
        <p><strong>Nonaktif:</strong> {{ $data->where('status', 'inactive')->count() }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
```

---

## 3. Excel Export Class

```php
<?php
// app/Exports/SupplierExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SupplierExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithEvents
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    /**
     * Return collection of data to export
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Supplier',
            'Nama Supplier',
            'Kontak Person',
            'Telepon',
            'Email',
            'Alamat',
            'Kota',
            'Provinsi',
            'Kode Pos',
            'Status',
            'Tanggal Daftar'
        ];
    }

    /**
     * Map data to columns
     */
    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->kode ?? '',
            $row->nama ?? '',
            $row->contact_person ?? '',
            $row->telepon ?? '',
            $row->email ?? '',
            $row->alamat ?? '',
            $row->kota ?? '',
            $row->provinsi ?? '',
            $row->kode_pos ?? '',
            $this->getStatusName($row->status ?? 'active'),
            $row->created_at ? $row->created_at->format('d/m/Y') : ''
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    /**
     * Set worksheet title
     */
    public function title(): string
    {
        return 'Daftar Supplier';
    }

    /**
     * Register events for additional styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size all columns
                foreach (range('A', 'L') as $col) {
                    $event->sheet->getDelegate()
                        ->getColumnDimension($col)
                        ->setAutoSize(true);
                }

                // Add borders to all cells with data
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getDelegate()
                    ->getStyle('A1:L' . $highestRow)
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC']
                            ]
                        ]
                    ]);

                // Center align for specific columns
                $event->sheet->getDelegate()
                    ->getStyle('A2:A' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()
                    ->getStyle('K2:K' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add alternating row colors
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $event->sheet->getDelegate()
                            ->getStyle('A' . $row . ':L' . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F9FAFB');
                    }
                }
            }
        ];
    }

    /**
     * Get status name in Indonesian
     */
    private function getStatusName($status): string
    {
        return match($status) {
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            default => 'Aktif'
        };
    }
}
```

---

## 4. Routes

```php
// routes/web.php

Route::middleware(['auth'])->group(function () {
    // Supplier routes
    Route::get('supplier/export/pdf', [SupplierController::class, 'exportPdf'])
        ->name('supplier.export.pdf');
    Route::get('supplier/export/excel', [SupplierController::class, 'exportExcel'])
        ->name('supplier.export.excel');
});
```

---

## 5. View (Button Export)

```blade
{{-- resources/views/admin/supplier/index.blade.php --}}

<div class="card">
    <div class="card-header">
        <h3>Daftar Supplier</h3>
        <div class="btn-group">
            {{-- Export PDF --}}
            <a href="{{ route('supplier.export.pdf', request()->query()) }}"
               class="btn btn-danger"
               target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            {{-- Export Excel --}}
            <a href="{{ route('supplier.export.excel', request()->query()) }}"
               class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card-body">
        <form method="GET" action="{{ route('supplier.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari supplier..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card-body">
        <table class="table table-striped">
            {{-- ... table content ... --}}
        </table>
    </div>
</div>
```

---

## 6. Testing

### Test PDF:

```bash
# 1. Klik tombol "Export PDF"
# 2. Verifikasi:
✓ PDF terbuka di tab baru (stream mode)
✓ Bisa scroll dan zoom
✓ Margin 10mm dari semua sisi
✓ Filter info muncul
✓ Data lengkap dan tidak terpotong
✓ Footer dengan tanggal cetak
✓ Bisa download dari browser

# 3. Print Preview (Ctrl+P):
✓ Layout pas dengan kertas A4
✓ Tidak ada halaman kosong
✓ Footer muncul di setiap halaman
```

### Test Excel:

```bash
# 1. Klik tombol "Export Excel"
# 2. File ter-download
# 3. Buka di Excel/LibreOffice
# 4. Verifikasi:
✓ Semua kolom terlihat penuh (auto-width)
✓ Header bold dengan background biru
✓ Border muncul di semua cells
✓ Zebra striping (baris genap abu-abu)
✓ Status dalam bahasa Indonesia
✓ Tanggal format dd/mm/yyyy
✓ Nomor urut center aligned
```

---

## 7. Troubleshooting

### PDF tidak stream (langsung download):

```php
// ❌ Salah
return $pdf->download($filename);

// ✅ Benar
return $pdf->stream($filename);
```

### Excel kolom terpotong:

```php
// Pastikan ada registerEvents() dengan auto-size
public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            foreach (range('A', 'L') as $col) {
                $event->sheet->getDelegate()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }
        }
    ];
}
```

### PDF margin tidak pas:

```css
/* Pastikan ada @page rule */
@page {
    margin: 10mm;
}

body {
    padding: 5mm;
}
```

---

## 8. Checklist Implementasi

### Setup:

-   [ ] Controller method `exportPdf()` dan `exportExcel()`
-   [ ] Routes untuk export
-   [ ] Button export di view

### PDF:

-   [ ] Template blade dengan @page margin
-   [ ] Body padding 5mm
-   [ ] Header dengan border
-   [ ] Filter info section
-   [ ] Table dengan styling
-   [ ] Summary box
-   [ ] Footer dengan tanggal
-   [ ] Gunakan `stream()` bukan `download()`

### Excel:

-   [ ] Export class dengan semua interfaces
-   [ ] `headings()` method
-   [ ] `map()` method
-   [ ] `styles()` untuk header
-   [ ] `registerEvents()` untuk auto-width
-   [ ] Border styling
-   [ ] Localization (bahasa Indonesia)

### Testing:

-   [ ] PDF stream di browser
-   [ ] PDF margin pas
-   [ ] Excel auto-width
-   [ ] Excel styling bagus
-   [ ] Filter berfungsi
-   [ ] Data lengkap

---

**Status:** ✅ Template Siap Pakai
**Tanggal:** 22 November 2025
