# Fix: Invoice Draft Status on Create ✅

## Issue

Saat pertama kali buat invoice, statusnya langsung "menunggu" padahal harusnya "draft" dulu.

## Root Cause

Method `store()` di `SalesManagementController` masih set status = 'menunggu' dan langsung:

-   Generate nomor invoice
-   Create piutang
-   Reduce stock
-   Create penjualan detail
-   Create journal entry

## Solution

### 1. Update `store()` Method

**File:** `app/Http/Controllers/SalesManagementController.php`

**Changes:**

```php
// BEFORE:
'no_invoice' => 'TEMP-' . time(),
'status' => 'menunggu',
// Then generate invoice number
// Then create piutang
// Then reduce stock
// Then create journal

// AFTER:
'no_invoice' => 'DRAFT',
'status' => 'draft',
// Skip invoice number generation
// Skip piutang creation
// Skip stock reduction
// Skip journal creation
```

**Specific Changes:**

1. **Invoice Creation:**

    ```php
    $invoiceData = [
        'no_invoice' => 'DRAFT', // Will be generated when confirmed
        'status' => 'draft', // Start as draft
        // ... other fields
    ];
    ```

2. **Skip Piutang Creation:**

    ```php
    // Piutang will be created when invoice is confirmed (not in draft)
    \Log::info('Skipping piutang creation - invoice is draft');
    ```

3. **Skip Stock Reduction:**

    ```php
    // Stock reduction and penjualan detail will be created when invoice is confirmed
    \Log::info('Skipping stock reduction - invoice is draft');
    ```

4. **Skip Journal Creation:**
    ```php
    // Journal will be created when invoice is confirmed
    \Log::info('Skipping journal creation - invoice is draft');
    ```

---

### 2. Update `confirmInvoice()` Method

**File:** `app/Http/Controllers/SalesManagementController.php`

**Added Logic:**

```php
DB::transaction(function () use ($invoice) {
    // 1. Generate invoice number
    $invoice->no_invoice = $this->generateInvoiceNumber($invoice->id_outlet);

    // 2. Update status to menunggu
    $invoice->status = 'menunggu';
    $invoice->save();

    // 3. Process invoice items: reduce stock and create penjualan details
    $this->processInvoiceItemsOnConfirm($invoice);

    // 4. Create piutang record
    $this->createPiutangFromInvoice($invoice);

    // 5. Create journal entry
    $this->createJournalFromInvoice($invoice);
});
```

---

### 3. New Method: `processInvoiceItemsOnConfirm()`

**File:** `app/Http/Controllers/SalesManagementController.php`

**Purpose:** Process all invoice items when confirming:

-   Check stock availability
-   Reduce stock
-   Create penjualan detail

```php
private function processInvoiceItemsOnConfirm($invoice)
{
    $items = $invoice->items;

    foreach ($items as $item) {
        if ($item->tipe === 'produk' && $item->id_produk) {
            $produk = Produk::find($item->id_produk);

            if ($produk) {
                // Check stock
                if ($produk->stok < $item->kuantitas) {
                    throw new \Exception("Stok tidak mencukupi...");
                }

                // Reduce stock
                $hpp = $produk->calculateHppBarangDagang();
                $produk->reduceStock($item->kuantitas);

                // Create penjualan detail
                \App\Models\PenjualanDetail::create([
                    'id_penjualan' => $invoice->id_penjualan,
                    'id_produk' => $item->id_produk,
                    'hpp' => $hpp,
                    'harga_jual' => $item->harga,
                    'jumlah' => $item->kuantitas,
                    'diskon' => 0,
                    'subtotal' => $item->subtotal,
                ]);
            }
        }
    }
}
```

---

### 4. Updated Method: `createPiutangFromInvoice()`

**File:** `app/Http/Controllers/SalesManagementController.php`

**Changes:**

-   Added `tanggal_tempo` field
-   Added `nama` field with invoice number
-   Added more logging

```php
private function createPiutangFromInvoice($invoice)
{
    // Check if piutang already exists
    $existingPiutang = Piutang::where('id_penjualan', $invoice->id_penjualan)->first();
    if ($existingPiutang) {
        return; // Already exists
    }

    $piutangData = [
        'tanggal_tempo' => $invoice->due_date,
        'id_member' => $invoice->id_member,
        'id_outlet' => $invoice->id_outlet,
        'id_penjualan' => $invoice->id_penjualan,
        'nama' => 'Invoice ' . $invoice->no_invoice, // Use generated invoice number
        'piutang' => $invoice->total,
        'jumlah_piutang' => $invoice->total,
        'sisa_piutang' => $invoice->total,
        'status' => 'belum_lunas',
    ];

    Piutang::create($piutangData);
}
```

---

### 5. Updated Method: `createJournalFromInvoice()`

**File:** `app/Http/Controllers/SalesManagementController.php`

**Changes:**

-   Now actually calls journal service
-   Added error handling

```php
private function createJournalFromInvoice($invoice)
{
    try {
        $this->journalService->createSalesInvoiceJournal($invoice, 'menunggu');
        \Log::info('Journal entry created for invoice: ' . $invoice->no_invoice);
    } catch (\Exception $e) {
        \Log::error('Error creating journal for invoice: ' . $e->getMessage());
        // Don't throw - journal creation failure shouldn't block confirmation
    }
}
```

---

## New Workflow

### Create Invoice (Draft)

```
User clicks "Invoice Baru"
  ↓
Fill form & click "Simpan"
  ↓
Controller store() method:
  - Create invoice with status = 'draft'
  - Set no_invoice = 'DRAFT'
  - Create penjualan record
  - Create invoice items
  - Skip: piutang, stock reduction, journal
  ↓
Invoice saved as DRAFT
```

### Confirm Invoice

```
User clicks "Konfirmasi" on draft invoice
  ↓
Controller confirmInvoice() method:
  - Generate invoice number (001/PBU/INV/XI/2025)
  - Update status to 'menunggu'
  - Process items:
    * Check stock availability
    * Reduce stock
    * Create penjualan details
  - Create piutang
  - Create journal entry
  ↓
Invoice confirmed and ready for payment
```

---

## Benefits

### 1. **Flexibility**

-   User can create invoice without committing
-   Can edit draft multiple times
-   No stock impact until confirmed

### 2. **Data Integrity**

-   Stock only reduced when invoice is confirmed
-   Piutang only created when invoice is confirmed
-   Journal only created when invoice is confirmed

### 3. **Better UX**

-   Clear distinction between draft and confirmed
-   User knows when invoice is "locked"
-   Can review before finalizing

### 4. **Audit Trail**

-   Clear log of when invoice was created (draft)
-   Clear log of when invoice was confirmed
-   All stock movements tied to confirmed invoices

---

## Testing Checklist

### Create Draft Invoice

-   [ ] Create new invoice
-   [ ] Verify status = "draft"
-   [ ] Verify no_invoice = "DRAFT"
-   [ ] Verify NO piutang created
-   [ ] Verify stock NOT reduced
-   [ ] Verify NO penjualan detail created
-   [ ] Verify NO journal entry created

### Edit Draft Invoice

-   [ ] Edit draft invoice
-   [ ] Modify items (add/remove/change qty)
-   [ ] Save changes
-   [ ] Verify changes saved
-   [ ] Verify still status = "draft"
-   [ ] Verify stock still NOT reduced

### Confirm Invoice

-   [ ] Click "Konfirmasi" on draft
-   [ ] Verify invoice number generated
-   [ ] Verify status = "menunggu"
-   [ ] Verify piutang created
-   [ ] Verify stock reduced correctly
-   [ ] Verify penjualan details created
-   [ ] Verify journal entry created
-   [ ] Verify Edit button NOT visible
-   [ ] Verify Bayar button IS visible

### Stock Validation on Confirm

-   [ ] Create draft with product qty = 10
-   [ ] Reduce product stock to 5
-   [ ] Try to confirm invoice
-   [ ] Verify error: "Stok tidak mencukupi"
-   [ ] Verify invoice still draft
-   [ ] Verify stock NOT reduced further

---

## Database Changes

### Before Confirm (Draft)

```sql
-- sales_invoice
status = 'draft'
no_invoice = 'DRAFT'

-- piutang
(no record)

-- penjualan_detail
(no records)

-- produk
stok = (unchanged)
```

### After Confirm

```sql
-- sales_invoice
status = 'menunggu'
no_invoice = '001/PBU/INV/XI/2025'

-- piutang
nama = 'Invoice 001/PBU/INV/XI/2025'
jumlah_piutang = 1000000
sisa_piutang = 1000000
status = 'belum_lunas'

-- penjualan_detail
(records created for each product)

-- produk
stok = (reduced by qty)
```

---

## Rollback Plan

If issues occur, revert these changes:

1. Restore `store()` method to create status = 'menunggu'
2. Restore immediate stock reduction
3. Restore immediate piutang creation
4. Remove `processInvoiceItemsOnConfirm()` method

---

## Status: ✅ COMPLETE

All changes implemented and ready for testing.

**Next Steps:**

1. Test create draft invoice
2. Test edit draft invoice
3. Test confirm invoice
4. Test stock validation
5. Verify piutang creation
6. Verify journal creation
