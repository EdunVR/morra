# Quick Guide: Export PDF & Excel

## 🎯 Ringkasan Singkat

Semua export PDF sekarang **STREAM** (preview dulu), bukan langsung download.
Semua export Excel sekarang **AUTO-WIDTH** dan styling profesional.

---

## 📄 PDF Export - Template Checklist

### 1. CSS Header (Wajib)

```css
@page {
    margin: 10mm;
}

body {
    font-family: "Arial", sans-serif;
    font-size: 9pt; /* 9-10pt untuk landscape */
    line-height: 1.3;
    color: #333;
    padding: 5mm;
}
```

### 2. Struktur HTML

```html
<!-- Header -->
<div class="header">
    <h1>Nama Perusahaan</h1>
    <h2>Judul Laporan</h2>
</div>

<!-- Filter Info -->
<div class="filter-info">
    <p><strong>Filter:</strong></p>
    <p>Periode: ...</p>
</div>

<!-- Table -->
<table>
    <thead>
        ...
    </thead>
    <tbody>
        ...
    </tbody>
    <tfoot>
        ...
    </tfoot>
</table>

<!-- Summary -->
<div class="summary-box">...</div>

<!-- Footer -->
<div class="footer">
    <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
```

### 3. Service Method

```php
public function exportToPDF(...)
{
    $pdf = Pdf::loadView($view, compact('data', 'filters'))
        ->setPaper('a4', 'landscape')
        ->setOption('margin-top', '10mm')
        ->setOption('margin-right', '10mm')
        ->setOption('margin-bottom', '10mm')
        ->setOption('margin-left', '10mm');

    return $pdf->stream($filename);  // STREAM, bukan download!
}
```

---

## 📊 Excel Export - Class Checklist

### 1. Implements (Wajib)

```php
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MyExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    WithEvents
{
    // ...
}
```

### 2. Column Formatting (Angka)

```php
public function columnFormats(): array
{
    return [
        'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Debit
        'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Kredit
    ];
}
```

### 3. Auto-Width & Borders (Wajib)

```php
public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            // Auto-size semua kolom
            foreach (range('A', 'J') as $col) {
                $event->sheet->getDelegate()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }

            // Border untuk semua cells
            $highestRow = $event->sheet->getDelegate()->getHighestRow();
            $event->sheet->getDelegate()
                ->getStyle('A1:J' . $highestRow)
                ->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
        }
    ];
}
```

### 4. Header Styling

```php
public function styles(Worksheet $sheet)
{
    return [
        1 => [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']  // Warna sesuai modul
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ],
    ];
}
```

---

## 🎨 Color Palette

### PDF & Excel Headers:

-   **Jurnal:** `#4F46E5` (Ungu)
-   **Buku Akuntansi:** `#4472C4` (Biru)
-   **Aktiva Tetap:** `#10B981` (Hijau)
-   **Buku Besar:** `#4F46E5` (Ungu)
-   **Laba Rugi:** `#333333` (Hitam)

### Data Colors:

-   **Debit/Positif:** `#059669` (Hijau)
-   **Kredit/Negatif:** `#DC2626` (Merah)
-   **Saldo:** `#2563EB` (Biru)
-   **Background Zebra:** `#F9F9F9` (Abu muda)

---

## ✅ Testing Checklist

### PDF:

```bash
# 1. Buka di browser
✓ PDF muncul di tab baru (stream mode)
✓ Bisa scroll dan zoom
✓ Ada tombol download di browser

# 2. Layout
✓ Margin 10mm dari semua sisi
✓ Tidak ada konten terpotong
✓ Font size readable (9-10pt)

# 3. Print Preview
✓ Ctrl+P → Preview bagus
✓ Fit to page
✓ Footer muncul
```

### Excel:

```bash
# 1. Download & Open
✓ File .xlsx ter-download
✓ Bisa dibuka di Excel/LibreOffice

# 2. Layout
✓ Semua kolom terlihat penuh (auto-width)
✓ Header bold dan berwarna
✓ Border muncul di semua cells

# 3. Data
✓ Angka format ribuan (1.234,56)
✓ Tanggal format dd/mm/yyyy
✓ Text tidak terpotong
```

---

## 🚀 Copy-Paste Templates

### PDF Template Minimal:

```html
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <title>Laporan</title>
        <style>
            @page {
                margin: 10mm;
            }
            body {
                font-family: Arial;
                font-size: 10pt;
                padding: 5mm;
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #333;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th {
                background: #4f46e5;
                color: white;
                padding: 8px;
            }
            td {
                border: 1px solid #ddd;
                padding: 6px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>{{ $title }}</h1>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Col1</th>
                    <th>Col2</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td>{{ $row->col1 }}</td>
                    <td>{{ $row->col2 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
```

### Excel Export Minimal:

```php
<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithEvents};
use Maatwebsite\Excel\Events\AfterSheet;

class MyExport implements FromCollection, WithHeadings, WithEvents
{
    protected $data;

    public function __construct($data) { $this->data = $data; }

    public function collection() { return collect($this->data); }

    public function headings(): array { return ['Col1', 'Col2']; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                foreach (range('A', 'Z') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
```

---

## 📝 Notes

1. **PDF Stream vs Download:**

    - `stream()` = Preview di browser ✅
    - `download()` = Langsung download ❌

2. **Excel Auto-Width:**

    - Wajib pakai `WithEvents` + `AfterSheet`
    - Loop semua kolom yang dipakai

3. **Number Formatting:**

    - Pakai `NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1`
    - Jangan manual format di map()

4. **Localization:**
    - Semua label dalam bahasa Indonesia
    - Format tanggal: dd/mm/yyyy
    - Format angka: 1.234,56

---

**Last Updated:** 22 November 2025
