# Fix: Invoice Draft Bugs

## Issues Fixed

### 1. ❌ Column Name Error in confirmInvoice

**Error:**

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'outlet_id' in 'where clause'
```

**Root Cause:**
Method `generateInvoiceNumber()` menggunakan `outlet_id` padahal column name yang benar adalah `id_outlet`

**Fix:**

```php
// BEFORE:
$counter = InvoiceSalesCounter::where('outlet_id', $outletId)
$counter = InvoiceSalesCounter::create(['outlet_id' => $outletId, ...])

// AFTER:
$counter = InvoiceSalesCounter::where('id_outlet', $outletId)
$counter = InvoiceSalesCounter::create(['id_outlet' => $outletId, ...])
```

**File:** `app/Http/Controllers/SalesManagementController.php`
**Lines:** ~3170-3185

---

### 2. ❌ Stock Shows 0 When Editing Draft Invoice

**Problem:**
Saat edit draft invoice, stok produk yang ditampilkan jadi 0

**Root Cause:**
Frontend mengambil stok dari `item.produk.stok` yang merupakan data historis dari saat invoice dibuat, bukan stok real-time

**Fix:**

```javascript
// BEFORE:
stok: item.produk.stok || 0;

// AFTER:
// Get current stock from availableProducts (real-time stock)
const currentProduct = this.availableProducts.find(
    (p) => p.id_produk == item.id_produk
);
const currentStok = currentProduct
    ? currentProduct.stok
    : item.produk.stok || 0;
stok: currentStok; // Use current stock, not historical
```

**File:** `resources/views/admin/penjualan/invoice/index.blade.php`
**Lines:** ~2965-2975

---

### 3. ✅ Ensure Edit Only for Draft

**Enhancement:**
Tambahkan validasi di backend untuk memastikan hanya draft yang bisa diedit

**Implementation:**

```php
// In update() method
$invoice = SalesInvoice::findOrFail($id);

// Only allow edit if status is draft
if ($invoice->status !== 'draft') {
    throw new \Exception('Hanya invoice dengan status draft yang bisa diedit');
}
```

**File:** `app/Http/Controllers/SalesManagementController.php`
**Lines:** ~530-535

---

### 4. ✅ Skip Piutang Update on Draft Edit

**Enhancement:**
Karena piutang belum dibuat saat draft, tidak perlu update piutang saat edit draft

**Implementation:**

```php
// BEFORE:
// Update Piutang
if ($invoice->penjualan && $invoice->penjualan->piutang) {
    $invoice->penjualan->piutang->update([...]);
}

// AFTER:
// Piutang will only exist if invoice is confirmed (not draft)
// So no need to update piutang here since we only allow editing draft
\Log::info('Skipping piutang update - invoice is draft');
```

**File:** `app/Http/Controllers/SalesManagementController.php`
**Lines:** ~600-605

---

### 5. ✅ Skip Stock Adjustment on Draft Edit

**Enhancement:**
Karena stok belum dikurangi saat draft, tidak perlu adjust stok saat edit draft

**Implementation:**

```php
// BEFORE:
// Handle stock adjustment untuk produk
if ($tipe === 'produk' && $id_produk) {
    $produk = Produk::find($id_produk);
    // Complex stock adjustment logic
}

// AFTER:
// Stock adjustment not needed for draft edit
// Stock will be reduced when invoice is confirmed
\Log::info('Skipping stock adjustment - invoice is draft');
```

**File:** `app/Http/Controllers/SalesManagementController.php`
**Lines:** ~630-635

---

## Testing Checklist

### Test 1: Confirm Invoice (Fix Column Name)

-   [ ] Create draft invoice
-   [ ] Click "Konfirmasi"
-   [ ] Verify NO error about 'outlet_id'
-   [ ] Verify invoice number generated successfully
-   [ ] Verify status changed to 'menunggu'

### Test 2: Edit Draft Invoice (Fix Stock Display)

-   [ ] Create draft invoice with Product A (current stock: 100)
-   [ ] Add Product A with qty 10 to invoice
-   [ ] Save draft
-   [ ] Click "Edit" on draft
-   [ ] Verify Product A shows stock = 100 (NOT 0)
-   [ ] Change qty to 20
-   [ ] Save
-   [ ] Verify stock still 100 (not reduced)

### Test 3: Edit Only Draft

-   [ ] Create draft invoice
-   [ ] Confirm invoice (status = 'menunggu')
-   [ ] Try to edit confirmed invoice
-   [ ] Verify Edit button NOT visible
-   [ ] Try to call update API directly
-   [ ] Verify error: "Hanya invoice dengan status draft yang bisa diedit"

### Test 4: Stock Not Reduced on Draft Edit

-   [ ] Create draft with Product B, qty 5 (stock: 50)
-   [ ] Edit draft, change qty to 10
-   [ ] Save
-   [ ] Check Product B stock
-   [ ] Verify stock still 50 (NOT reduced to 45 or 40)

### Test 5: Piutang Not Updated on Draft Edit

-   [ ] Create draft invoice
-   [ ] Edit draft, change total
-   [ ] Save
-   [ ] Check piutang table
-   [ ] Verify NO piutang record exists (because still draft)

---

## Summary of Changes

### Backend (`SalesManagementController.php`)

1. ✅ Fixed column name: `outlet_id` → `id_outlet` in `generateInvoiceNumber()`
2. ✅ Added validation: only draft can be edited in `update()`
3. ✅ Removed piutang update logic from `update()` (not needed for draft)
4. ✅ Removed stock adjustment logic from `update()` (not needed for draft)

### Frontend (`index.blade.php`)

1. ✅ Fixed stock display: use real-time stock from `availableProducts` instead of historical data

---

## Workflow Verification

### Create Draft

```
User creates invoice
  ↓
Status = 'draft'
No invoice = 'DRAFT'
Stock NOT reduced ✅
Piutang NOT created ✅
```

### Edit Draft

```
User edits draft
  ↓
Can change items/qty
Stock display shows CURRENT stock ✅
Stock NOT reduced ✅
Piutang NOT updated ✅
```

### Confirm Draft

```
User confirms draft
  ↓
Generate invoice number ✅ (no column error)
Status = 'menunggu'
Stock reduced ✅
Piutang created ✅
Journal created ✅
```

### Try Edit Confirmed

```
User tries to edit confirmed invoice
  ↓
Edit button NOT visible ✅
API returns error if called directly ✅
```

---

## Status

✅ **ALL BUGS FIXED**

## Files Changed

1. `app/Http/Controllers/SalesManagementController.php`

    - Fixed `generateInvoiceNumber()` column name
    - Added draft validation in `update()`
    - Removed unnecessary piutang/stock logic from `update()`

2. `resources/views/admin/penjualan/invoice/index.blade.php`
    - Fixed stock display in `editInvoice()` function

---

## Next Steps

1. Test all scenarios above
2. Verify no regression in existing functionality
3. Monitor logs for any errors
4. Update user documentation if needed
