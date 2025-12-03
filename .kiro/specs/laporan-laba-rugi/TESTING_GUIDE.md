# Testing Guide - Laporan Laba Rugi Backend

## Quick Test Commands

### 1. Test profitLossData Endpoint

```bash
# Test basic profit & loss data
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=2024-01-01&end_date=2024-01-31"

# Test with comparison mode
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=2024-02-01&end_date=2024-02-29&comparison=true&comparison_start_date=2024-01-01&comparison_end_date=2024-01-31"
```

### 2. Test profitLossStats Endpoint

```bash
# Test statistics
curl -X GET "http://localhost/finance/profit-loss/stats?outlet_id=1"

# Test with specific period
curl -X GET "http://localhost/finance/profit-loss/stats?outlet_id=1&period=monthly"
```

## Manual Testing Checklist

### Basic Functionality

-   [ ] Access `/finance/profit-loss` page loads successfully
-   [ ] Select outlet from dropdown
-   [ ] Select date range
-   [ ] Data loads without errors
-   [ ] Revenue accounts display correctly
-   [ ] Expense accounts display correctly
-   [ ] Summary calculations are correct

### Account Hierarchy

-   [ ] Parent accounts show total including children
-   [ ] Child accounts display under parent
-   [ ] Recursive calculation works for nested accounts
-   [ ] Only accounts with transactions appear

### Financial Ratios

-   [ ] Gross profit margin calculates correctly
-   [ ] Net profit margin calculates correctly
-   [ ] Operating expense ratio calculates correctly
-   [ ] Ratios show null when revenue is zero

### Comparison Mode

-   [ ] Enable comparison mode
-   [ ] Select comparison period
-   [ ] Comparison data loads correctly
-   [ ] Delta calculations are accurate
-   [ ] Percentage changes are correct
-   [ ] Direction indicators (increase/decrease) are correct

### Statistics

-   [ ] Current month data is accurate
-   [ ] Last month data is accurate
-   [ ] YTD data is accurate
-   [ ] Trends data for 6 months is correct
-   [ ] Chart.js format is valid

### Edge Cases

-   [ ] Empty data (no transactions) returns zero values
-   [ ] Invalid outlet_id returns error
-   [ ] Invalid date range returns validation error
-   [ ] Future dates work correctly
-   [ ] Same start and end date works

### Performance

-   [ ] Response time < 2 seconds for normal data
-   [ ] Large datasets (1000+ transactions) load within 5 seconds
-   [ ] Multiple concurrent requests don't cause issues

## Expected Results

### Sample Response - profitLossData

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
        "summary": {
            "total_revenue": 50000000,
            "total_expense": 30000000,
            "gross_profit": 50000000,
            "operating_profit": 20000000,
            "net_income": 20000000,
            "gross_profit_margin": 100,
            "net_profit_margin": 40,
            "operating_expense_ratio": 60
        }
    }
}
```

### Sample Response - profitLossStats

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
            "labels": [
                "Jan 2024",
                "Feb 2024",
                "Mar 2024",
                "Apr 2024",
                "May 2024",
                "Jun 2024"
            ],
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

## Validation Testing

### Test Invalid Inputs

```bash
# Missing outlet_id
curl -X GET "http://localhost/finance/profit-loss/data?start_date=2024-01-01&end_date=2024-01-31"
# Expected: 422 error with validation message

# Invalid date format
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=invalid&end_date=2024-01-31"
# Expected: 422 error with validation message

# End date before start date
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=2024-02-01&end_date=2024-01-31"
# Expected: 422 error with validation message

# Non-existent outlet
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=99999&start_date=2024-01-01&end_date=2024-01-31"
# Expected: 422 error with validation message
```

## Database Verification

### Check Journal Entries

```sql
-- Verify posted journal entries exist
SELECT COUNT(*) FROM journal_entries
WHERE outlet_id = 1
  AND status = 'posted'
  AND transaction_date BETWEEN '2024-01-01' AND '2024-01-31';

-- Check revenue accounts
SELECT coa.code, coa.name, SUM(jed.credit - jed.debit) as amount
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON jed.account_id = coa.id
LEFT JOIN journal_entries je ON je.id = jed.journal_entry_id
WHERE coa.outlet_id = 1
  AND coa.type = 'revenue'
  AND je.status = 'posted'
  AND je.transaction_date BETWEEN '2024-01-01' AND '2024-01-31'
GROUP BY coa.id;

-- Check expense accounts
SELECT coa.code, coa.name, SUM(jed.credit - jed.debit) as amount
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON jed.account_id = coa.id
LEFT JOIN journal_entries je ON je.id = jed.journal_entry_id
WHERE coa.outlet_id = 1
  AND coa.type = 'expense'
  AND je.status = 'posted'
  AND je.transaction_date BETWEEN '2024-01-01' AND '2024-01-31'
GROUP BY coa.id;
```

## Troubleshooting

### Issue: No data returned

**Solution**:

-   Check if journal entries exist with status 'posted'
-   Verify outlet_id is correct
-   Ensure date range includes transactions
-   Check if accounts are active

### Issue: Incorrect calculations

**Solution**:

-   Verify journal entries are balanced (debit = credit)
-   Check account types are correct (revenue, expense, etc.)
-   Ensure parent-child relationships are set up correctly
-   Verify transaction dates are within the period

### Issue: Slow performance

**Solution**:

-   Add indexes on journal_entries (outlet_id, status, transaction_date)
-   Add indexes on journal_entry_details (account_id, journal_entry_id)
-   Consider caching for frequently accessed periods
-   Optimize recursive queries for deep account hierarchies

## Next Steps After Testing

Once backend testing is complete:

1. Proceed to Task 3: Frontend implementation
2. Implement visualizations (Task 4)
3. Add export functionality (Task 5)
4. Add print functionality (Task 6)
