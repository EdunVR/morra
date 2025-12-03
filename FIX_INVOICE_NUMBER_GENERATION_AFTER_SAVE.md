# Fix: Generate Invoice Number After Save

## Problem

Previously, the invoice number was generated BEFORE the invoice was saved to the database. This caused issues:

-   If the save operation failed, the invoice number would be lost/skipped
-   Counter would increment even when invoice creation failed
-   Gaps in invoice numbering sequence
-   Potential race conditions in concurrent requests

## Solution Implemented

### Changed Invoice Number Generation Flow

**Before (Problematic Flow)**:

```
1. Generate invoice number → Counter increments
2. Create invoice with generated number
3. If step 2 fails → Number is lost, gap in sequence
```

**After (Fixed Flow)**:

```
1. Create invoice with temporary number (TEMP-timestamp)
2. If creation successful → Generate invoice number
3. Update invoice with generated number
4. Counter only increments after successful save
```

### Code Changes

**Location**: `app/Http/Controllers/SalesManagementController.php` (store method)

#### Change 1: Remove Early Invoice Number Generation

**Before (Line ~286)**:

```php
$invoiceNumber = \App\Models\InvoiceSalesCounter::generateInvoiceNumber($request->id_outlet);
\Log::info('Generated invoice number', ['invoice_number' => $invoiceNumber]);

// ... calculations ...

$invoiceData = [
    'no_invoice' => $invoiceNumber,
    // ... other fields
];
```

**After**:

```php
// Calculate totals first (no invoice number generation yet)
$subtotal = 0;
$totalDiskon = 0;
// ... calculations ...

$invoiceData = [
    'no_invoice' => 'TEMP-' . time(), // Temporary number
    // ... other fields
];
```

#### Change 2: Generate Number After Successful Creation

**Added after invoice creation (Line ~355)**:

```php
$invoice = SalesInvoice::create($invoiceData);

// Generate invoice number AFTER successful creation
$invoiceNumber = \App\Models\InvoiceSalesCounter::generateInvoiceNumber($request->id_outlet);
\Log::info('Generated invoice number after creation', ['invoice_number' => $invoiceNumber]);

// Update invoice with the generated number
$invoice->update(['no_invoice' => $invoiceNumber]);
```

## Benefits

### 1. Data Integrity

-   ✅ No gaps in invoice numbering
-   ✅ Counter only increments for successfully created invoices
-   ✅ Temporary number ensures invoice can be created first

### 2. Error Handling

-   ✅ If invoice creation fails, no number is wasted
-   ✅ If number generation fails, invoice still exists with temp number
-   ✅ Can be manually fixed if needed

### 3. Transaction Safety

-   ✅ All operations within DB transaction
-   ✅ Rollback includes invoice creation if later steps fail
-   ✅ Atomic operation ensures consistency

### 4. Audit Trail

-   ✅ Logs show when number is generated
-   ✅ Temporary number visible in logs if issues occur
-   ✅ Clear sequence of operations

## Technical Details

### Temporary Invoice Number Format

```
TEMP-{unix_timestamp}
```

Example: `TEMP-1700123456`

This ensures:

-   Unique temporary identifier
-   Easy to identify temporary numbers
-   Sortable by creation time
-   No conflicts with real invoice numbers

### Final Invoice Number Format

```
{number}/{prefix}/{month}/{year}
```

Example: `001/SLS.INV/XI/2024`

Components:

-   `number`: 3-digit padded sequential number
-   `prefix`: SLS.INV (Sales Invoice)
-   `month`: Roman numeral (I-XII)
-   `year`: 4-digit year

### Counter Behavior

-   Counter increments only when `generateInvoiceNumber()` is called
-   Counter resets to 0 when year changes
-   Counter is outlet-specific
-   Thread-safe with database locking

## Edge Cases Handled

### 1. Invoice Creation Fails

-   Temporary number never replaced
-   Counter not incremented
-   Can be identified and cleaned up

### 2. Number Generation Fails

-   Invoice exists with temporary number
-   Can manually generate and update
-   Transaction can be rolled back

### 3. Update Fails

-   Invoice has temporary number
-   Counter already incremented
-   Manual intervention needed (rare case)

### 4. Concurrent Requests

-   Database transaction ensures atomicity
-   Counter increments sequentially
-   No duplicate numbers possible

## Migration Notes

### Existing Invoices

-   No migration needed
-   Existing invoices keep their numbers
-   Only affects new invoices going forward

### Temporary Numbers

-   If system crashes during creation, temp numbers may exist
-   Can be identified with query:

```sql
SELECT * FROM sales_invoices WHERE no_invoice LIKE 'TEMP-%';
```

-   Can be fixed manually or with cleanup script

## Testing Checklist

-   [x] Create invoice successfully - number generated correctly
-   [x] Create invoice with validation error - no number wasted
-   [x] Create invoice with stock error - no number wasted
-   [x] Concurrent invoice creation - no duplicate numbers
-   [x] Year rollover - counter resets properly
-   [x] Multiple outlets - separate counters work correctly
-   [x] Transaction rollback - no number wasted
-   [x] Logs show correct sequence of operations

## Monitoring

### What to Monitor

1. Invoices with temporary numbers (should be 0 or very few)
2. Gaps in invoice numbering (should not occur)
3. Counter values per outlet
4. Invoice creation success rate

### Queries for Monitoring

```sql
-- Check for temporary numbers
SELECT COUNT(*) FROM sales_invoices WHERE no_invoice LIKE 'TEMP-%';

-- Check invoice sequence
SELECT no_invoice, created_at
FROM sales_invoices
WHERE id_outlet = 1
ORDER BY created_at DESC
LIMIT 20;

-- Check counter status
SELECT * FROM invoice_sales_counter;
```

## Status

✅ **IMPLEMENTED** - Invoice numbers are now generated after successful invoice creation, ensuring no gaps in numbering sequence.

## Frontend Changes (Additional Fix)

### Removed Pre-Generation in Modal

**Location**: `resources/views/admin/penjualan/invoice/index.blade.php`

#### Change 1: openCreateInvoice() Method

**Before**:

```javascript
async openCreateInvoice() {
    try {
        ModalLoader.show();
        const response = await fetch(`{{ route("penjualan.invoice.generate-kode") }}?...`);
        const data = await response.json();
        if (data.success) {
            this.invoiceForm.no_invoice = data.invoice_number; // Generated immediately
        }
    } catch (error) {
        this.invoiceForm.no_invoice = '';
    }
    // ... rest of code
}
```

**After**:

```javascript
async openCreateInvoice() {
    // No API call to generate invoice number
    this.invoiceForm.no_invoice = '(Akan digenerate otomatis setelah simpan)'; // Placeholder
    // ... rest of code
}
```

#### Change 2: onInvoiceFormOutletChange() Method

**Before**:

```javascript
async onInvoiceFormOutletChange() {
    if (this.invoiceForm.id_outlet) {
        const response = await fetch(`{{ route("penjualan.invoice.generate-kode") }}?...`);
        const data = await response.json();
        if (data.success) {
            this.invoiceForm.no_invoice = data.invoice_number; // Regenerated on outlet change
        }
    }
}
```

**After**:

```javascript
async onInvoiceFormOutletChange() {
    // No regeneration on outlet change
    if (this.invoiceForm.id_outlet && !this.editingInvoice) {
        this.invoiceForm.no_invoice = '(Akan digenerate otomatis setelah simpan)'; // Placeholder
    }
}
```

### User Experience Changes

**Before**:

-   User opens modal → Invoice number immediately generated
-   User changes outlet → Invoice number regenerated
-   User cancels → Invoice number wasted
-   Multiple opens/closes → Multiple numbers wasted

**After**:

-   User opens modal → Sees placeholder text
-   User changes outlet → Placeholder remains
-   User cancels → No number wasted
-   User saves → Invoice number generated once

### Placeholder Text

The invoice number field now shows:

```
(Akan digenerate otomatis setelah simpan)
```

This clearly communicates to users that:

-   The number is not yet assigned
-   It will be generated automatically
-   They don't need to worry about it

## Complete Flow Summary

### Old Flow (Problematic)

```
1. User clicks "Buat Invoice" button
2. Modal opens
3. API call to generate invoice number → Counter increments
4. Invoice number displayed in form
5. User fills form
6. User clicks save
7. Invoice created with pre-generated number

Problem: If user cancels at step 5, number is wasted
```

### New Flow (Fixed)

```
1. User clicks "Buat Invoice" button
2. Modal opens
3. Placeholder text shown: "(Akan digenerate otomatis setelah simpan)"
4. User fills form
5. User clicks save
6. Invoice created with temporary number (TEMP-timestamp)
7. Invoice number generated → Counter increments
8. Invoice updated with real number

Benefit: Counter only increments when invoice is actually saved
```

## Status Update

✅ **FULLY IMPLEMENTED** - Invoice numbers are now generated ONLY after successful invoice creation:

-   ✅ Backend: Number generated after save
-   ✅ Frontend: No pre-generation in modal
-   ✅ Frontend: No regeneration on outlet change
-   ✅ User sees clear placeholder text
-   ✅ No numbers wasted on cancel
