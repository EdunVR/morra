# Task 2 Implementation Summary - Backend Logic untuk Laporan Laba Rugi

## Overview

Successfully implemented complete backend logic for profit & loss (Laporan Laba Rugi) report calculation including all subtasks.

## Completed Subtasks

### 2.1 ✅ Implementasi method profitLossData() untuk query data

**Location**: `app/Http/Controllers/FinanceAccountantController.php`

**Implemented Features**:

-   Query akun revenue (type: 'revenue') dari chart_of_accounts
-   Query akun other revenue (type: 'otherrevenue') dari chart_of_accounts
-   Query akun expense (type: 'expense') dari chart_of_accounts
-   Query akun other expense (type: 'otherexpense') dari chart_of_accounts
-   Filter hanya journal entries dengan status 'posted'
-   Implementasi recursive calculation untuk parent-child accounts
-   Hitung summary (total revenue, total expense, net income)

**Key Methods**:

-   `profitLossData(Request $request)`: Main endpoint untuk mengambil data laporan laba rugi
-   `calculateProfitLossForPeriod($outletId, $startDate, $endDate)`: Calculate profit & loss untuk periode tertentu
-   `calculateAccountsAmount($accounts, $outletId, $startDate, $endDate)`: Calculate amounts untuk collection of accounts
-   `calculateAccountAmountRecursive($account, $outletId, $startDate, $endDate)`: Recursive calculation termasuk children

**Requirements Met**: 1.3, 1.4, 1.5, 6.1, 6.2, 6.4

### 2.2 ✅ Implementasi comparison mode

**Implemented Features**:

-   Parameter comparison, comparison_start_date, comparison_end_date
-   Query data untuk periode pembanding
-   Hitung delta (selisih) antara periode current dan comparison
-   Hitung persentase perubahan
-   Direction indicator (increase/decrease/stable)

**Key Methods**:

-   `calculateChanges($current, $comparison)`: Calculate changes between periods

**Requirements Met**: 3.1, 3.2, 3.3

### 2.3 ✅ Implementasi perhitungan rasio keuangan

**Implemented Features**:

-   Gross profit margin calculation
-   Net profit margin calculation
-   Operating expense ratio calculation
-   Handle edge case (division by zero) - returns null when revenue is 0

**Key Methods**:

-   `calculateFinancialRatios($totalRevenue, $totalExpense, $grossProfit, $operatingProfit)`: Calculate all financial ratios

**Requirements Met**: 7.1, 7.2, 7.3, 7.4

### 2.4 ✅ Implementasi method profitLossStats() untuk dashboard

**Implemented Features**:

-   Query data untuk current month
-   Query data untuk last month
-   Query data untuk YTD (Year to Date)
-   Query data untuk trends (6 bulan terakhir)
-   Format data untuk Chart.js dengan labels dan datasets

**Key Methods**:

-   `profitLossStats(Request $request)`: Main endpoint untuk statistics
-   `calculateProfitLossTrends($outletId, $months)`: Calculate trends untuk Chart.js

**Requirements Met**: 5.1, 5.2, 5.3, 5.4

## Technical Implementation Details

### Data Flow

```
Request → Validation → calculateProfitLossForPeriod() →
Query Accounts → calculateAccountsAmount() →
calculateAccountAmountRecursive() →
Calculate Summary & Ratios → Response
```

### Account Calculation Logic

1. Query parent accounts by type (revenue, otherrevenue, expense, otherexpense)
2. For each parent account:
    - Calculate direct transactions from journal_entry_details
    - Recursively calculate children amounts
    - Sum all amounts
3. Only include accounts with non-zero amounts
4. Use `credit - debit` for revenue/expense accounts (normal balance)

### Financial Ratios Calculation

-   **Gross Profit Margin**: (Gross Profit / Total Revenue) × 100
-   **Net Profit Margin**: ((Total Revenue - Total Expense) / Total Revenue) × 100
-   **Operating Expense Ratio**: (Total Expense / Total Revenue) × 100
-   Returns `null` when Total Revenue = 0 to avoid division by zero

### Comparison Mode

-   Calculates data for both current and comparison periods
-   Computes delta (difference) for each metric
-   Calculates percentage change: (delta / |comparison_value|) × 100
-   Determines direction: increase, decrease, or stable

## API Endpoints

### 1. GET /finance/profit-loss/data

**Parameters**:

-   `outlet_id` (required): ID outlet
-   `start_date` (required): Tanggal mulai periode
-   `end_date` (required): Tanggal akhir periode
-   `comparison` (optional): boolean untuk aktifkan comparison mode
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
      "accounts": [...],
      "total": 50000000
    },
    "other_revenue": {
      "accounts": [...],
      "total": 1000000
    },
    "expense": {
      "accounts": [...],
      "total": 30000000
    },
    "other_expense": {
      "accounts": [...],
      "total": 1000000
    },
    "summary": {
      "total_revenue": 51000000,
      "total_expense": 31000000,
      "gross_profit": 50000000,
      "operating_profit": 20000000,
      "net_income": 20000000,
      "gross_profit_margin": 98.04,
      "net_profit_margin": 39.22,
      "operating_expense_ratio": 60.78
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

### 2. GET /finance/profit-loss/stats

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
      "labels": ["Jan 2024", "Feb 2024", ...],
      "revenue": [45000000, 48000000, ...],
      "expense": [28000000, 29000000, ...],
      "net_income": [17000000, 19000000, ...]
    }
  }
}
```

## Database Queries

### Main Query Pattern

```sql
SELECT
  SUM(credit - debit) as total
FROM journal_entry_details
WHERE account_id = ?
  AND journal_entry_id IN (
    SELECT id FROM journal_entries
    WHERE outlet_id = ?
      AND status = 'posted'
      AND transaction_date BETWEEN ? AND ?
  )
```

### Performance Considerations

-   Uses eager loading for account relationships (`with('children')`)
-   Only queries active accounts
-   Filters by posted journal entries only
-   Uses indexed columns (outlet_id, status, transaction_date)

## Validation Rules

### profitLossData

-   `outlet_id`: required, must exist in outlets table
-   `start_date`: required, must be valid date
-   `end_date`: required, must be valid date, must be >= start_date
-   `comparison`: optional, boolean
-   `comparison_start_date`: required if comparison is true, must be valid date
-   `comparison_end_date`: required if comparison is true, must be >= comparison_start_date

### profitLossStats

-   `outlet_id`: required, must exist in outlets table
-   `period`: optional, must be one of: monthly, quarterly, yearly

## Error Handling

-   Validates all input parameters
-   Returns 422 for validation errors
-   Returns 500 for server errors
-   Logs all errors with stack trace
-   Returns user-friendly error messages

## Testing Recommendations

### Unit Tests

-   Test `calculateAccountAmountRecursive()` with various account hierarchies
-   Test `calculateFinancialRatios()` with edge cases (zero revenue)
-   Test `calculateChanges()` with positive, negative, and zero values

### Integration Tests

-   Test with real database data
-   Test with different outlet IDs
-   Test with various date ranges
-   Test comparison mode with different periods
-   Test with accounts that have no transactions

### Manual Testing

1. Test dengan outlet berbeda
2. Test dengan periode berbeda (monthly, quarterly, yearly)
3. Test comparison mode
4. Test dengan data kosong
5. Test dengan akun yang memiliki children
6. Test dengan periode yang tidak memiliki transaksi posted

## Next Steps

The following tasks are ready to be implemented:

-   Task 3: Implementasi frontend view
-   Task 4: Implementasi visualisasi grafik
-   Task 5: Implementasi export functionality (XLSX & PDF)
-   Task 6: Implementasi print functionality

## Files Modified

-   `app/Http/Controllers/FinanceAccountantController.php`

## Routes Available

-   GET `/finance/profit-loss` - Display page
-   GET `/finance/profit-loss/data` - Get profit & loss data
-   GET `/finance/profit-loss/stats` - Get statistics
-   GET `/finance/profit-loss/export/xlsx` - Export to XLSX (not yet implemented)
-   GET `/finance/profit-loss/export/pdf` - Export to PDF (not yet implemented)
