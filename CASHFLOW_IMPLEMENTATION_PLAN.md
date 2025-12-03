# Cash Flow Implementation Plan

## Overview

Mengintegrasikan data real dari database ke halaman Arus Kas (Cash Flow) yang saat ini masih menggunakan data dummy.

## Current State

-   ✅ Frontend sudah ada di `resources/views/admin/finance/cashflow/index.blade.php`
-   ❌ Data masih dummy (hardcoded di JavaScript)
-   ❌ Belum ada backend API
-   ❌ Belum ada integrasi dengan Chart of Accounts
-   ❌ Belum ada detail transaksi per akun

## Target Implementation

### 1. Backend API (FinanceAccountantController)

#### a. Method `cashFlowIndex()`

```php
public function cashFlowIndex(Request $request)
{
    return view('admin.finance.cashflow.index');
}
```

#### b. Method `cashFlowData()` - Main API

```php
public function cashFlowData(Request $request): JsonResponse
{
    $outletId = $request->get('outlet_id');
    $bookId = $request->get('book_id');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    $method = $request->get('method', 'direct'); // direct or indirect

    // Calculate cash flow components
    $operating = $this->calculateOperatingCashFlow($outletId, $bookId, $startDate, $endDate, $method);
    $investing = $this->calculateInvestingCashFlow($outletId, $bookId, $startDate, $endDate);
    $financing = $this->calculateFinancingCashFlow($outletId, $bookId, $startDate, $endDate);

    // Calculate net cash flow
    $netCashFlow = $operating['total'] + $investing['total'] + $financing['total'];

    // Get beginning and ending cash
    $beginningCash = $this->getBeginningCash($outletId, $bookId, $startDate);
    $endingCash = $beginningCash + $netCashFlow;

    return response()->json([
        'success' => true,
        'data' => [
            'operating' => $operating,
            'investing' => $investing,
            'financing' => $financing,
            'net_cash_flow' => $netCashFlow,
            'beginning_cash' => $beginningCash,
            'ending_cash' => $endingCash,
            'stats' => [
                'net_cash_flow' => $netCashFlow,
                'operating_cash' => $operating['total'],
                'investing_cash' => $investing['total'],
                'financing_cash' => $financing['total']
            ]
        ]
    ]);
}
```

### 2. Cash Flow Categories

#### A. Operating Activities (Aktivitas Operasi)

**Direct Method:**

-   Penerimaan kas dari pelanggan
-   Pembayaran kas kepada pemasok
-   Pembayaran kas untuk beban operasi
-   Pembayaran bunga
-   Pembayaran pajak

**Indirect Method:**

-   Laba bersih
-   Penyesuaian non-kas (depresiasi, amortisasi)
-   Perubahan modal kerja

**Account Types:**

-   Revenue accounts (4xxx)
-   Expense accounts (5xxx)
-   Current assets & liabilities

#### B. Investing Activities (Aktivitas Investasi)

-   Pembelian aset tetap
-   Penjualan aset tetap
-   Investasi jangka panjang

**Account Types:**

-   Fixed assets (1xxx - specific categories)
-   Long-term investments

#### C. Financing Activities (Aktivitas Pendanaan)

-   Penerimaan dari pinjaman
-   Pembayaran pinjaman
-   Setoran modal
-   Pembayaran dividen/prive

**Account Types:**

-   Long-term liabilities (2xxx - specific)
-   Equity accounts (3xxx)

### 3. Helper Methods

```php
private function calculateOperatingCashFlow($outletId, $bookId, $startDate, $endDate, $method)
{
    if ($method === 'direct') {
        return $this->calculateDirectMethod($outletId, $bookId, $startDate, $endDate);
    } else {
        return $this->calculateIndirectMethod($outletId, $bookId, $startDate, $endDate);
    }
}

private function calculateDirectMethod($outletId, $bookId, $startDate, $endDate)
{
    // Get cash receipts from customers (Revenue accounts)
    $cashReceipts = $this->getCashFromRevenue($outletId, $bookId, $startDate, $endDate);

    // Get cash payments to suppliers (COGS, Inventory)
    $cashPayments = $this->getCashToSuppliers($outletId, $bookId, $startDate, $endDate);

    // Get cash payments for operating expenses
    $operatingExpenses = $this->getCashForExpenses($outletId, $bookId, $startDate, $endDate);

    $total = $cashReceipts - $cashPayments - $operatingExpenses;

    return [
        'items' => [
            ['name' => 'Penerimaan dari Pelanggan', 'amount' => $cashReceipts, 'accounts' => [...]],
            ['name' => 'Pembayaran kepada Pemasok', 'amount' => -$cashPayments, 'accounts' => [...]],
            ['name' => 'Pembayaran Beban Operasi', 'amount' => -$operatingExpenses, 'accounts' => [...]]
        ],
        'total' => $total
    ];
}

private function calculateIndirectMethod($outletId, $bookId, $startDate, $endDate)
{
    // Start with net income
    $netIncome = $this->calculateNetIncome($outletId, $bookId, $startDate, $endDate);

    // Add back non-cash expenses (depreciation)
    $depreciation = $this->getDepreciation($outletId, $bookId, $startDate, $endDate);

    // Adjust for changes in working capital
    $workingCapitalChanges = $this->getWorkingCapitalChanges($outletId, $bookId, $startDate, $endDate);

    $total = $netIncome + $depreciation + $workingCapitalChanges;

    return [
        'items' => [
            ['name' => 'Laba Bersih', 'amount' => $netIncome],
            ['name' => 'Penyusutan', 'amount' => $depreciation],
            ['name' => 'Perubahan Modal Kerja', 'amount' => $workingCapitalChanges]
        ],
        'total' => $total
    ];
}
```

### 4. Account Mapping

Perlu mapping akun ke kategori cash flow:

```php
// Operating Activities
$operatingAccounts = [
    'revenue' => ['4000-4999'], // All revenue
    'expense' => ['5000-5999'], // All expenses
    'current_assets' => ['1100-1199'], // Piutang, Persediaan
    'current_liabilities' => ['2100-2199'] // Hutang Usaha
];

// Investing Activities
$investingAccounts = [
    'fixed_assets' => ['1200-1299'], // Aset Tetap
    'investments' => ['1300-1399'] // Investasi
];

// Financing Activities
$financingAccounts = [
    'long_term_debt' => ['2200-2299'], // Hutang Jangka Panjang
    'equity' => ['3000-3999'] // Modal, Prive
];
```

### 5. Frontend Integration

Update JavaScript untuk call API:

```javascript
async loadCashFlowData() {
    this.isLoading = true;

    try {
        const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id,
            book_id: this.filters.book_id,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            method: this.filters.method
        });

        const response = await fetch(`{{ route('finance.cashflow.data') }}?${params}`);
        const result = await response.json();

        if (result.success) {
            this.cashFlowData = result.data;
            this.cashFlowStats = result.data.stats;
            this.updateCharts();
        }
    } catch (error) {
        console.error('Error loading cash flow:', error);
    } finally {
        this.isLoading = false;
    }
}
```

### 6. Click to View Details

Tambahkan modal untuk detail transaksi:

```javascript
async viewAccountDetails(accountId, category) {
    // Similar to neraca implementation
    const response = await fetch(`{{ route('finance.cashflow.account-details') }}/${accountId}?...`);
    // Show modal with transactions
}
```

### 7. Routes

```php
// routes/web.php
Route::prefix('finance')->name('finance.')->group(function () {
    // ... existing routes ...

    Route::get('cashflow', [FinanceAccountantController::class, 'cashFlowIndex'])->name('cashflow.index');
    Route::get('cashflow/data', [FinanceAccountantController::class, 'cashFlowData'])->name('cashflow.data');
    Route::get('cashflow/account-details/{id}', [FinanceAccountantController::class, 'getCashFlowAccountDetails'])->name('cashflow.account-details');
    Route::get('cashflow/export/pdf', [FinanceAccountantController::class, 'exportCashFlowPDF'])->name('cashflow.export.pdf');
    Route::get('cashflow/export/xlsx', [FinanceAccountantController::class, 'exportCashFlowXLSX'])->name('cashflow.export.xlsx');
});
```

## Implementation Steps

### Phase 1: Basic Structure ✅

1. Create backend methods in FinanceAccountantController
2. Create routes
3. Test basic API response

### Phase 2: Operating Activities

1. Implement direct method calculation
2. Implement indirect method calculation
3. Map accounts to categories
4. Test with real data

### Phase 3: Investing & Financing

1. Implement investing activities calculation
2. Implement financing activities calculation
3. Test calculations

### Phase 4: Frontend Integration

1. Update JavaScript to call real API
2. Remove dummy data
3. Add loading states
4. Test UI with real data

### Phase 5: Details & Export

1. Add click-to-view details
2. Implement PDF export
3. Implement Excel export
4. Add filters (outlet, book, date range)

## Complexity Note

Cash Flow Statement adalah laporan keuangan yang paling kompleks karena:

-   Memerlukan klasifikasi transaksi ke 3 kategori
-   Ada 2 metode (Direct & Indirect)
-   Perlu rekonsiliasi dengan Neraca dan Laba Rugi
-   Memerlukan analisis perubahan modal kerja

Estimasi waktu: 4-6 jam untuk implementasi lengkap.

## Recommendation

Karena kompleksitas tinggi, saya sarankan:

1. **Mulai dengan metode Direct** (lebih mudah dipahami)
2. **Fokus pada Operating Activities dulu** (paling penting)
3. **Gunakan account mapping yang jelas**
4. **Test dengan data sample dulu**
5. **Implementasi Indirect method nanti** (optional)

Apakah Anda ingin saya lanjutkan dengan implementasi lengkap atau fokus pada bagian tertentu dulu?
