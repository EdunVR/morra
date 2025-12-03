# Fix: Draft Invoice Unique Constraint Error

## Issue

```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'DRAFT' for key 'sales_invoice_no_invoice_unique'
```

**Problem:**

-   Column `no_invoice` memiliki unique constraint
-   Semua draft invoice menggunakan nomor "DRAFT" yang sama
-   Saat create draft kedua, terjadi duplicate entry error

## Root Cause

```php
// BEFORE:
'no_invoice' => 'DRAFT', // Same for all drafts!
```

Semua draft invoice menggunakan string "DRAFT" yang sama, sehingga melanggar unique constraint.

## Solution

Generate unique draft number menggunakan timestamp dan uniqid:

### Backend Fix

**File:** `app/Http/Controllers/SalesManagementController.php`

```php
// AFTER:
'no_invoice' => 'DRAFT-' . time() . '-' . uniqid(), // Unique for each draft
```

**Format:** `DRAFT-1732197319-655e4b2f3a1c8`

-   `DRAFT-` = Prefix untuk identifikasi draft
-   `1732197319` = Unix timestamp (detik sejak 1970)
-   `655e4b2f3a1c8` = Unique ID

### Confirm Invoice Fix

**File:** `app/Http/Controllers/SalesManagementController.php`

```php
// BEFORE:
if (empty($invoice->no_invoice) || $invoice->no_invoice === 'DRAFT') {

// AFTER:
if (empty($invoice->no_invoice) || str_starts_with($invoice->no_invoice, 'DRAFT-')) {
```

Menggunakan `str_starts_with()` untuk mendeteksi semua draft number yang dimulai dengan "DRAFT-"

### Frontend Fix

**File:** `resources/views/admin/penjualan/invoice/index.blade.php`

```javascript
// BEFORE:
this.invoiceForm.no_invoice = "DRAFT";

// AFTER:
this.invoiceForm.no_invoice =
    "DRAFT-" + Date.now() + "-" + Math.random().toString(36).substr(2, 9);
```

**Format:** `DRAFT-1732197319000-k3j5h8m2p`

-   `DRAFT-` = Prefix
-   `1732197319000` = JavaScript timestamp (milliseconds)
-   `k3j5h8m2p` = Random string

### UI Display

**File:** `resources/views/admin/penjualan/invoice/index.blade.php`

```html
<!-- Display "DRAFT" in UI, hide the timestamp -->
<div x-text="invoice.status === 'draft' ? 'DRAFT' : invoice.no_invoice"></div>
```

User tetap melihat "DRAFT" di UI, tapi di database tersimpan dengan unique number.

---

## Examples

### Database Records

```sql
SELECT id_sales_invoice, no_invoice, status FROM sales_invoice WHERE status = 'draft';
```

**Result:**

```
| id | no_invoice                      | status |
|----|----------------------------------|--------|
| 1  | DRAFT-1732197319-655e4b2f3a1c8  | draft  |
| 2  | DRAFT-1732197325-655e4b35a2d9f  | draft  |
| 3  | DRAFT-1732197331-655e4b3ba3e0a  | draft  |
```

### UI Display

User melihat:

```
Invoice #1: DRAFT
Invoice #2: DRAFT
Invoice #3: DRAFT
```

### After Confirm

```sql
SELECT id_sales_invoice, no_invoice, status FROM sales_invoice WHERE id_sales_invoice = 1;
```

**Result:**

```
| id | no_invoice           | status   |
|----|----------------------|----------|
| 1  | 001/PBU/INV/XI/2025 | menunggu |
```

---

## Benefits

### 1. **Unique Constraint Satisfied** ✅

-   Setiap draft memiliki nomor unik
-   Tidak ada duplicate entry error
-   Database integrity terjaga

### 2. **User-Friendly Display** ✅

-   User tetap melihat "DRAFT" yang simple
-   Tidak perlu melihat timestamp yang panjang
-   Clean UI

### 3. **Easy to Identify** ✅

-   Semua draft dimulai dengan "DRAFT-"
-   Mudah query: `WHERE no_invoice LIKE 'DRAFT-%'`
-   Mudah detect saat confirm

### 4. **Scalable** ✅

-   Bisa create unlimited drafts
-   Timestamp memastikan uniqueness
-   uniqid() menambah extra uniqueness

---

## Testing

### Test 1: Create Multiple Drafts

```
1. Create draft invoice #1
2. Create draft invoice #2
3. Create draft invoice #3
```

**Expected:**

-   ✅ All drafts created successfully
-   ✅ No duplicate entry error
-   ✅ Each has unique no_invoice in database
-   ✅ All show "DRAFT" in UI

### Test 2: Confirm Draft

```
1. Create draft (no_invoice = DRAFT-1732197319-655e4b2f3a1c8)
2. Click "Konfirmasi"
```

**Expected:**

-   ✅ Invoice number generated (001/PBU/INV/XI/2025)
-   ✅ Status changed to 'menunggu'
-   ✅ Old draft number replaced

### Test 3: Database Query

```sql
-- Find all drafts
SELECT * FROM sales_invoice WHERE no_invoice LIKE 'DRAFT-%';

-- Find specific draft
SELECT * FROM sales_invoice WHERE no_invoice = 'DRAFT-1732197319-655e4b2f3a1c8';
```

**Expected:**

-   ✅ Query works correctly
-   ✅ Can find drafts easily

---

## Code Changes Summary

### Backend

**File:** `app/Http/Controllers/SalesManagementController.php`

1. **store() method:**

    ```php
    'no_invoice' => 'DRAFT-' . time() . '-' . uniqid(),
    ```

2. **confirmInvoice() method:**
    ```php
    if (empty($invoice->no_invoice) || str_starts_with($invoice->no_invoice, 'DRAFT-')) {
    ```

### Frontend

**File:** `resources/views/admin/penjualan/invoice/index.blade.php`

1. **openCreateInvoice() function:**

    ```javascript
    this.invoiceForm.no_invoice =
        "DRAFT-" + Date.now() + "-" + Math.random().toString(36).substr(2, 9);
    ```

2. **Display (already correct):**
    ```html
    <div
        x-text="invoice.status === 'draft' ? 'DRAFT' : invoice.no_invoice"
    ></div>
    ```

---

## Alternative Solutions Considered

### Option 1: Remove Unique Constraint ❌

**Pros:** Simple fix
**Cons:**

-   Loses database integrity
-   Could have duplicate invoice numbers
-   Not recommended

### Option 2: Use Auto-Increment ❌

**Pros:** Simple, guaranteed unique
**Cons:**

-   Exposes internal ID
-   Not semantic
-   Harder to identify drafts

### Option 3: Timestamp + Unique ID ✅ (CHOSEN)

**Pros:**

-   Maintains unique constraint
-   Easy to identify drafts
-   User-friendly display
-   Scalable
    **Cons:**
-   Slightly longer string in database

---

## Migration Notes

### No Migration Needed

-   Unique constraint already exists
-   No schema changes required
-   Only code changes

### Existing Drafts

If there are existing drafts with "DRAFT" number:

```sql
-- Update existing drafts to have unique numbers
UPDATE sales_invoice
SET no_invoice = CONCAT('DRAFT-', UNIX_TIMESTAMP(), '-', UUID())
WHERE no_invoice = 'DRAFT' AND status = 'draft';
```

---

## Status

✅ **FIXED** - Ready for testing

## Files Changed

1. `app/Http/Controllers/SalesManagementController.php`

    - Updated `store()` method
    - Updated `confirmInvoice()` method

2. `resources/views/admin/penjualan/invoice/index.blade.php`
    - Updated `openCreateInvoice()` function

---

## Next Steps

1. Test creating multiple drafts
2. Test confirming drafts
3. Verify database records
4. Verify UI display
5. Update existing drafts if any
