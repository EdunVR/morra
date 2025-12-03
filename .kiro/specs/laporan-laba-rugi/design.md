# Design Document - Laporan Laba Rugi

## Overview

Laporan Laba Rugi adalah fitur yang menghasilkan laporan keuangan untuk menampilkan kinerja keuangan perusahaan dalam periode tertentu. Fitur ini akan terintegrasi dengan sistem akuntansi yang sudah ada (Chart of Accounts, Journal Entry, General Ledger) dan menggunakan pattern yang sama dengan modul finance lainnya.

### Key Features

-   Tampilan laporan laba rugi dengan filter outlet dan periode
-   Perhitungan otomatis dari data jurnal yang sudah diposting
-   Struktur hierarki akun (parent-child)
-   Perbandingan dengan periode sebelumnya
-   Export ke XLSX dan PDF
-   Visualisasi grafik (pie chart, bar chart, line chart)
-   Perhitungan rasio keuangan
-   Print langsung dari browser

### Technology Stack

-   **Backend**: Laravel (PHP)
-   **Frontend**: Blade Templates + Alpine.js
-   **Styling**: Tailwind CSS
-   **Charts**: Chart.js
-   **Export**: Maatwebsite Excel, DomPDF
-   **Database**: MySQL (existing tables)

## Architecture

### MVC Pattern

```
┌─────────────────────────────────────────────────────────────┐
│                         Frontend                             │
│  resources/views/admin/finance/labarugi/index.blade.php    │
│  - Alpine.js component: profitLossManagement()              │
│  - Chart.js untuk visualisasi                               │
│  - Tailwind CSS untuk styling                               │
└──────────────────────┬──────────────────────────────────────┘
                       │ HTTP Request/Response (JSON)
┌──────────────────────▼──────────────────────────────────────┐
│                      Controller                              │
│     app/Http/Controllers/FinanceAccountantController.php    │
│  - profitLossData()                                         │
│  - profitLossStats()                                        │
│  - exportProfitLossXLSX()                                   │
│  - exportProfitLossPDF()                                    │
└──────────────────────┬──────────────────────────────────────┘
                       │ Query/Update
┌──────────────────────▼──────────────────────────────────────┐
│                       Models                                 │
│  - ChartOfAccount (existing)                                │
│  - JournalEntry (existing)                                  │
│  - JournalEntryDetail (existing)                            │
│  - Outlet (existing)                                        │
└─────────────────────────────────────────────────────────────┘
```

### Data Flow

```
User Action → Alpine.js → API Call → Controller → Model Query →
Database → Model → Controller → JSON Response → Alpine.js →
UI Update
```

## Components and Interfaces

### 1. Backend Components

#### Controller Methods (FinanceAccountantController.php)

##### profitLossData(Request $request): JsonResponse

**Purpose**: Mengambil data laporan laba rugi untuk periode tertentu

**Parameters**:

-   `outlet_id` (required): ID outlet
-   `start_date` (required): Tanggal mulai periode
-   `end_date` (required): Tanggal akhir periode
-   `comparison` (optional): boolean, aktifkan mode perbandingan
-   `comparison_start_date` (optional): Tanggal mulai periode pembanding
-   `comparison_end_date` (optional): Tanggal akhir periode pembanding

**Response Structure**:

```json
{
    "success": true,
    "data": {
        "period": {
            "start_date": "2024-01-01",
            "end_date": "2024-01-31",
            "outlet_name": "Outlet Pusat"
        },
        "revenue": {
            "accounts": [
                {
                    "id": 1,
                    "code": "4000",
                    "name": "Pendapatan Penjualan",
                    "amount": 50000000,
                    "children": [
                        {
                            "id": 2,
                            "code": "4000.01",
                            "name": "Pendapatan Produk A",
                            "amount": 30000000
                        }
                    ]
                }
            ],
            "total": 50000000
        },
        "other_revenue": {
            "accounts": [],
            "total": 0
        },
        "expense": {
            "accounts": [
                {
                    "id": 10,
                    "code": "5000",
                    "name": "Beban Operasional",
                    "amount": 30000000,
                    "children": []
                }
            ],
            "total": 30000000
        },
        "other_expense": {
            "accounts": [],
            "total": 0
        },
        "summary": {
            "total_revenue": 50000000,
            "total_expense": 30000000,
            "gross_profit": 50000000,
            "operating_profit": 20000000,
            "net_income": 20000000,
            "gross_profit_margin": 100,
            "net_profit_margin": 40,
            "operating_expense_ratio": 60
        },
        "comparison": {
            "enabled": false,
            "period": null,
            "revenue": null,
            "expense": null,
            "summary": null,
            "changes": null
        }
    }
}
```

**Logic**:

1. Validasi outlet_id dan periode
2. Query akun revenue (type: 'revenue', 'otherrevenue')
3. Query akun expense (type: 'expense', 'otherexpense')
4. Untuk setiap akun, hitung saldo dari journal_entry_details
5. Hanya gunakan journal entries dengan status 'posted'
6. Jika akun memiliki children, hitung total akumulasi
7. Hitung summary (total revenue, total expense, net income)
8. Hitung rasio keuangan
9. Jika comparison aktif, ulangi proses untuk periode pembanding
10. Hitung perubahan (delta dan persentase)

##### profitLossStats(Request $request): JsonResponse

**Purpose**: Mengambil statistik untuk dashboard/infografis

**Parameters**:

-   `outlet_id` (required): ID outlet
-   `period` (optional): 'monthly', 'quarterly', 'yearly'

**Response Structure**:

```json
{
    "success": true,
    "data": {
        "current_month": {
            "revenue": 50000000,
            "expense": 30000000,
            "net_income": 20000000
        },
        "last_month": {
            "revenue": 45000000,
            "expense": 28000000,
            "net_income": 17000000
        },
        "ytd": {
            "revenue": 500000000,
            "expense": 300000000,
            "net_income": 200000000
        },
        "trends": {
            "labels": ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            "revenue": [
                45000000, 48000000, 50000000, 52000000, 51000000, 50000000
            ],
            "expense": [
                28000000, 29000000, 30000000, 31000000, 30000000, 30000000
            ],
            "net_income": [
                17000000, 19000000, 20000000, 21000000, 21000000, 20000000
            ]
        }
    }
}
```

##### exportProfitLossXLSX(Request $request)

**Purpose**: Export laporan laba rugi ke format Excel

**Parameters**: Same as profitLossData()

**Logic**:

1. Ambil data menggunakan logic yang sama dengan profitLossData()
2. Generate Excel file menggunakan Maatwebsite Excel
3. Format dengan header, sub-header, dan footer
4. Return file download

##### exportProfitLossPDF(Request $request)

**Purpose**: Export laporan laba rugi ke format PDF

**Parameters**: Same as profitLossData()

**Logic**:

1. Ambil data menggunakan logic yang sama dengan profitLossData()
2. Render view PDF (resources/views/admin/finance/labarugi/pdf.blade.php)
3. Generate PDF menggunakan DomPDF
4. Return file download atau stream untuk print

### 2. Frontend Components

#### Alpine.js Component: profitLossManagement()

**State Variables**:

```javascript
{
  // Filters
  filters: {
    outlet_id: '',
    start_date: '',
    end_date: '',
    period: 'monthly', // monthly, quarterly, yearly, custom
    comparison: false,
    comparison_start_date: '',
    comparison_end_date: ''
  },

  // Data
  outlets: [],
  profitLossData: {
    revenue: { accounts: [], total: 0 },
    other_revenue: { accounts: [], total: 0 },
    expense: { accounts: [], total: 0 },
    other_expense: { accounts: [], total: 0 },
    summary: {}
  },
  stats: {},

  // UI State
  isLoading: false,
  error: null,
  expandedAccounts: [],

  // Charts
  revenueChart: null,
  expenseChart: null,
  comparisonChart: null,
  trendChart: null
}
```

**Methods**:

-   `init()`: Initialize component, load outlets, set default values
-   `loadOutlets()`: Fetch outlets data
-   `loadProfitLossData()`: Fetch profit loss data
-   `loadStats()`: Fetch statistics
-   `onOutletChange()`: Handle outlet selection change
-   `onPeriodChange()`: Handle period selection change
-   `toggleComparison()`: Toggle comparison mode
-   `toggleAccountDetails(accountId)`: Expand/collapse account details
-   `exportToXLSX()`: Trigger XLSX export
-   `exportToPDF()`: Trigger PDF export
-   `printReport()`: Open print dialog
-   `initCharts()`: Initialize Chart.js charts
-   `updateCharts()`: Update charts with new data
-   `formatCurrency(amount)`: Format number to currency
-   `formatDate(date)`: Format date
-   `calculateChange(current, previous)`: Calculate change percentage

#### View Structure (index.blade.php)

```
┌─────────────────────────────────────────────────────────────┐
│ Header                                                       │
│ - Title: "Laporan Laba Rugi"                                │
│ - Action Buttons: Export, Print, Refresh                    │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Filter Section                                               │
│ - Outlet Selector                                           │
│ - Period Selector (Monthly, Quarterly, Yearly, Custom)     │
│ - Date Range (Start Date, End Date)                        │
│ - Comparison Toggle                                         │
│ - Comparison Date Range (if enabled)                       │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Summary Cards                                                │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│ │ Revenue  │ │ Expense  │ │Net Income│ │ Margin   │       │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Charts Section                                               │
│ ┌────────────────────┐ ┌────────────────────┐              │
│ │ Revenue Pie Chart  │ │ Expense Pie Chart  │              │
│ └────────────────────┘ └────────────────────┘              │
│ ┌──────────────────────────────────────────┐               │
│ │ Revenue vs Expense Bar Chart             │               │
│ └──────────────────────────────────────────┘               │
│ ┌──────────────────────────────────────────┐ (if comparison)│
│ │ Trend Line Chart                         │               │
│ └──────────────────────────────────────────┘               │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Profit & Loss Statement Table                                │
│                                                              │
│ PENDAPATAN                                                   │
│   4000 - Pendapatan Penjualan        50,000,000            │
│     4000.01 - Produk A                30,000,000            │
│     4000.02 - Produk B                20,000,000            │
│   Total Pendapatan                    50,000,000            │
│                                                              │
│ PENDAPATAN LAIN-LAIN                                        │
│   6000 - Pendapatan Bunga             1,000,000             │
│   Total Pendapatan Lain-Lain          1,000,000             │
│                                                              │
│ TOTAL PENDAPATAN                      51,000,000            │
│                                                              │
│ BEBAN OPERASIONAL                                           │
│   5000 - Beban Gaji                   20,000,000            │
│   5100 - Beban Sewa                   10,000,000            │
│   Total Beban Operasional             30,000,000            │
│                                                              │
│ BEBAN LAIN-LAIN                                             │
│   7000 - Beban Bunga                  1,000,000             │
│   Total Beban Lain-Lain               1,000,000             │
│                                                              │
│ TOTAL BEBAN                           31,000,000            │
│                                                              │
│ LABA/RUGI BERSIH                      20,000,000            │
│                                                              │
│ RASIO KEUANGAN                                              │
│   Gross Profit Margin                 100%                  │
│   Net Profit Margin                   39.22%                │
│   Operating Expense Ratio             58.82%                │
└─────────────────────────────────────────────────────────────┘
```

### 3. Export Components

#### ProfitLossExport (Excel)

**Location**: `app/Exports/ProfitLossExport.php`

**Structure**:

```php
class ProfitLossExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;
    protected $outlet;
    protected $period;

    public function collection()
    {
        // Return collection of data
    }

    public function headings(): array
    {
        // Return column headings
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styling
    }
}
```

#### PDF View

**Location**: `resources/views/admin/finance/labarugi/pdf.blade.php`

**Structure**:

-   Header dengan logo dan info perusahaan
-   Judul laporan dan periode
-   Tabel laporan laba rugi
-   Footer dengan tanggal generate dan user

## Data Models

### Existing Models (No Changes Required)

#### ChartOfAccount

-   Sudah memiliki method untuk calculate accumulated balance
-   Sudah memiliki scope untuk filter by type
-   Sudah memiliki relasi parent-child

#### JournalEntry

-   Sudah memiliki relasi ke JournalEntryDetail
-   Sudah memiliki status (posted, draft, void)
-   Sudah memiliki filter by outlet

#### JournalEntryDetail

-   Sudah memiliki relasi ke ChartOfAccount
-   Sudah memiliki debit dan credit

### Query Logic

#### Calculate Revenue/Expense for Period

```sql
SELECT
    coa.id,
    coa.code,
    coa.name,
    coa.type,
    coa.parent_id,
    SUM(jed.debit - jed.credit) as amount
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON jed.account_id = coa.id
LEFT JOIN journal_entries je ON je.id = jed.journal_entry_id
WHERE coa.outlet_id = ?
    AND coa.type IN ('revenue', 'otherrevenue', 'expense', 'otherexpense')
    AND coa.status = 'active'
    AND je.status = 'posted'
    AND je.transaction_date BETWEEN ? AND ?
GROUP BY coa.id
ORDER BY coa.code
```

#### Calculate with Hierarchy

```php
// Pseudo code
function calculateAccountAmount($account, $startDate, $endDate) {
    $amount = 0;

    // Get direct transactions
    $directAmount = JournalEntryDetail::whereHas('journalEntry', function($q) use ($startDate, $endDate) {
            $q->where('status', 'posted')
              ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->where('account_id', $account->id)
        ->selectRaw('SUM(debit - credit) as total')
        ->value('total');

    $amount += $directAmount ?? 0;

    // Get children amounts recursively
    if ($account->children->count() > 0) {
        foreach ($account->children as $child) {
            $amount += calculateAccountAmount($child, $startDate, $endDate);
        }
    }

    return $amount;
}
```

## Error Handling

### Validation Errors

-   Outlet tidak dipilih
-   Periode tidak valid (end_date < start_date)
-   Outlet tidak ditemukan
-   Tidak ada data untuk periode yang dipilih

### Error Responses

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Error detail"]
    }
}
```

### Frontend Error Handling

-   Display error message in notification
-   Log error to console
-   Disable action buttons during loading
-   Show empty state when no data

## Testing Strategy

### Unit Tests

-   Test calculateAccountAmount() dengan berbagai skenario
-   Test rasio calculation dengan edge cases (division by zero)
-   Test date range validation
-   Test hierarchy calculation

### Integration Tests

-   Test profitLossData() endpoint dengan data sample
-   Test export XLSX dengan data lengkap
-   Test export PDF dengan data lengkap
-   Test comparison mode

### Frontend Tests

-   Test Alpine.js component initialization
-   Test filter changes
-   Test chart rendering
-   Test export triggers

### Manual Testing Checklist

1. Load halaman dengan outlet berbeda
2. Ubah periode (monthly, quarterly, yearly, custom)
3. Aktifkan comparison mode
4. Export ke XLSX dan verifikasi format
5. Export ke PDF dan verifikasi format
6. Print langsung dari browser
7. Test dengan data kosong
8. Test dengan akun yang memiliki children
9. Test dengan periode yang tidak memiliki transaksi
10. Test performa dengan data besar

## Performance Considerations

### Database Optimization

-   Index pada `journal_entries.transaction_date`
-   Index pada `journal_entries.status`
-   Index pada `journal_entries.outlet_id`
-   Index pada `chart_of_accounts.type`
-   Index pada `chart_of_accounts.parent_id`

### Query Optimization

-   Use eager loading untuk relasi
-   Cache hasil perhitungan untuk periode yang sama
-   Limit query hanya untuk akun yang memiliki transaksi
-   Use raw queries untuk perhitungan kompleks

### Frontend Optimization

-   Lazy load charts
-   Debounce filter changes
-   Cache outlet list
-   Minimize re-renders

## Security Considerations

### Authorization

-   Verify user has access to selected outlet
-   Verify user has permission to view financial reports
-   Verify user has permission to export data

### Data Protection

-   Sanitize all user inputs
-   Validate date ranges
-   Prevent SQL injection
-   Prevent XSS attacks

### Audit Trail

-   Log setiap export (user, outlet, periode, timestamp)
-   Log setiap akses ke laporan
-   Track perubahan filter

## Integration Points

### Existing Systems

-   Chart of Accounts: Read account structure and balances
-   Journal Entry: Read posted transactions
-   Outlet: Read outlet information
-   User: Read user permissions

### Future Enhancements

-   Integration dengan Budget module (compare actual vs budget)
-   Integration dengan Forecast module
-   Integration dengan Dashboard module
-   Email scheduled reports
-   API untuk external systems

## Deployment Notes

### Database Changes

-   No database migrations required (using existing tables)

### Configuration

-   No new configuration required

### Dependencies

-   Maatwebsite Excel (already installed)
-   DomPDF (already installed)
-   Chart.js (add to package.json if not exists)

### Routes to Add

```php
// routes/web.php
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('profit-loss', [FinanceAccountantController::class, 'profitLossIndex'])->name('profit-loss.index');
    Route::get('profit-loss/data', [FinanceAccountantController::class, 'profitLossData'])->name('profit-loss.data');
    Route::get('profit-loss/stats', [FinanceAccountantController::class, 'profitLossStats'])->name('profit-loss.stats');
    Route::get('profit-loss/export/xlsx', [FinanceAccountantController::class, 'exportProfitLossXLSX'])->name('profit-loss.export.xlsx');
    Route::get('profit-loss/export/pdf', [FinanceAccountantController::class, 'exportProfitLossPDF'])->name('profit-loss.export.pdf');
});
```

### Menu/Navigation Update

Add menu item in sidebar:

```php
// In sidebar component
[
    'label' => 'Laporan Laba Rugi',
    'route' => 'finance.profit-loss.index',
    'icon' => 'bx-line-chart',
    'parent' => 'Finance'
]
```
