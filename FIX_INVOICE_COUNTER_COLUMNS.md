# Fix: Invoice Counter Column Names

## Issue

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'month' in 'where clause'
```

**Problem:**
Code menggunakan column names yang tidak sesuai dengan struktur table `invoice_sales_counter`

## Table Structure

```sql
DESCRIBE invoice_sales_counter;
```

**Actual Columns:**

-   `id` - Primary key
-   `id_outlet` - Foreign key to outlets
-   `invoice_prefix` - Prefix for invoice (e.g., 'INV')
-   `last_number` - Last used number
-   `year` - Year
-   `created_at` - Timestamp
-   `updated_at` - Timestamp

**Missing Columns:**

-   ❌ `month` - Not in table
-   ❌ `counter` - Not in table (should use `last_number`)

## Root Cause

```php
// WRONG CODE:
$counter = InvoiceSalesCounter::where('id_outlet', $outletId)
                              ->where('month', $month)  // ❌ Column doesn't exist
                              ->where('year', $year)
                              ->first();

$counter = InvoiceSalesCounter::create([
    'id_outlet' => $outletId,
    'month' => $month,      // ❌ Column doesn't exist
    'year' => $year,
    'counter' => 1          // ❌ Should be 'last_number'
]);

$nextNumber = $counter->counter + 1;  // ❌ Should be 'last_number'
```

## Solution

### Updated Code

**File:** `app/Http/Controllers/SalesManagementController.php`

```php
// CORRECT CODE:
$counter = InvoiceSalesCounter::where('id_outlet', $outletId)
                              ->where('year', $year)  // ✅ Only filter by year
                              ->first();

if (!$counter) {
    $counter = InvoiceSalesCounter::create([
        'id_outlet' => $outletId,
        'invoice_prefix' => 'INV',  // ✅ Add prefix
        'last_number' => 1,         // ✅ Use last_number
        'year' => $year
    ]);
    $nextNumber = 1;
} else {
    $nextNumber = $counter->last_number + 1;  // ✅ Use last_number
    $counter->update(['last_number' => $nextNumber]);
}
```

## Counter Logic

### Before (WRONG - per month):

```
Outlet 1, Month 11, Year 2025 → Counter 1
Outlet 1, Month 12, Year 2025 → Counter 1 (resets each month)
```

### After (CORRECT - per year):

```
Outlet 1, Year 2025 → Counter 1, 2, 3, ... (continuous throughout year)
Outlet 1, Year 2026 → Counter 1 (resets each year)
```

## Invoice Number Format

**Format:** `001/PBU/INV/XI/2025`

**Components:**

-   `001` = Sequential number (from `last_number`)
-   `PBU` = Outlet code
-   `INV` = Invoice prefix
-   `XI` = Month in Roman numeral (November = XI)
-   `2025` = Year

**Example Sequence:**

```
001/PBU/INV/XI/2025  (November 2025, #1)
002/PBU/INV/XI/2025  (November 2025, #2)
003/PBU/INV/XII/2025 (December 2025, #3)
004/PBU/INV/I/2026   (January 2026, #4 - continues from previous year)
```

Wait, this doesn't match the expected behavior. Let me reconsider...

Actually, looking at the format, it seems like the counter should reset each year, not each month. The month in Roman numeral is just for display, not for filtering.

## Correct Behavior

### Counter Resets Annually

```
January 2025:   001/PBU/INV/I/2025
February 2025:  002/PBU/INV/II/2025
March 2025:     003/PBU/INV/III/2025
...
December 2025:  012/PBU/INV/XII/2025
January 2026:   001/PBU/INV/I/2026  ← Resets to 001
```

This makes sense because:

-   Counter is stored per outlet + year
-   Month is just displayed in the invoice number
-   Counter increments continuously throughout the year
-   Resets when year changes

## Testing

### Test 1: First Invoice of Year

```
Outlet: 1
Year: 2025
Month: November

Expected: 001/PBU/INV/XI/2025
```

### Test 2: Second Invoice Same Year

```
Outlet: 1
Year: 2025
Month: November

Expected: 002/PBU/INV/XI/2025
```

### Test 3: Invoice Next Month Same Year

```
Outlet: 1
Year: 2025
Month: December

Expected: 003/PBU/INV/XII/2025
```

### Test 4: Invoice Next Year

```
Outlet: 1
Year: 2026
Month: January

Expected: 001/PBU/INV/I/2026  ← Resets to 001
```

### Test 5: Multiple Outlets

```
Outlet 1, Year 2025: 001/PBU/INV/XI/2025
Outlet 2, Year 2025: 001/CAB/INV/XI/2025  ← Separate counter
```

## Database Verification

### Check Counter

```sql
SELECT * FROM invoice_sales_counter
WHERE id_outlet = 1 AND year = 2025;
```

**Expected:**

```
| id | id_outlet | invoice_prefix | last_number | year |
|----|-----------|----------------|-------------|------|
| 1  | 1         | INV            | 3           | 2025 |
```

### Check Invoices

```sql
SELECT id_sales_invoice, no_invoice, status, created_at
FROM sales_invoice
WHERE id_outlet = 1 AND YEAR(tanggal) = 2025
ORDER BY created_at;
```

**Expected:**

```
| id | no_invoice           | status   | created_at          |
|----|----------------------|----------|---------------------|
| 1  | 001/PBU/INV/XI/2025  | menunggu | 2025-11-21 10:00:00 |
| 2  | 002/PBU/INV/XI/2025  | menunggu | 2025-11-21 11:00:00 |
| 3  | 003/PBU/INV/XII/2025 | menunggu | 2025-12-01 09:00:00 |
```

## Summary of Changes

### Before:

-   ❌ Used `month` column (doesn't exist)
-   ❌ Used `counter` column (should be `last_number`)
-   ❌ Filtered by month (unnecessary)

### After:

-   ✅ Removed `month` filter
-   ✅ Use `last_number` column
-   ✅ Use `invoice_prefix` column
-   ✅ Counter per outlet + year
-   ✅ Month only for display in invoice number

## Status

✅ **FIXED** - Ready for testing

## Files Changed

1. `app/Http/Controllers/SalesManagementController.php`
    - Updated `generateInvoiceNumber()` method
    - Fixed column names
    - Fixed counter logic

## Next Steps

1. Test invoice number generation
2. Verify counter increments correctly
3. Verify counter resets per year
4. Test multiple outlets
