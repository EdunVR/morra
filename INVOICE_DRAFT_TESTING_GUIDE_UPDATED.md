# Invoice Draft Workflow - Updated Testing Guide 🧪

## Critical Tests for Draft Workflow

### ✅ Test 1: Create Draft Invoice (NO Stock Reduction)

**Purpose:** Verify invoice is created as draft without affecting stock

**Steps:**

1. Login ke sistem
2. Navigate to **Penjualan → Invoice Penjualan**
3. Note current stock of a product (e.g., Product A has 100 units)
4. Click **"Invoice Baru"**
5. Fill form:
    - Select customer
    - Add Product A with qty = 10
    - Set tanggal & jatuh tempo
6. Click **"Simpan"**

**Expected Results:**

-   ✅ Invoice created successfully
-   ✅ Invoice number shows **"DRAFT"**
-   ✅ Status badge shows **"Draft"** (gray)
-   ✅ **Edit** button visible
-   ✅ **Konfirmasi** button visible
-   ✅ **Bayar** button NOT visible
-   ✅ Draft count in stats = 1
-   ✅ **Product A stock still 100** (NOT reduced to 90)
-   ✅ **NO piutang record** in Finance → Piutang
-   ✅ **NO penjualan detail** in database

**Database Verification:**

```sql
-- Check invoice
SELECT id_sales_invoice, no_invoice, status, total
FROM sales_invoice
WHERE status = 'draft'
ORDER BY created_at DESC LIMIT 1;
-- Expected: no_invoice = 'DRAFT', status = 'draft'

-- Check piutang (should be empty for this invoice)
SELECT * FROM piutang
WHERE id_penjualan = [penjualan_id_from_invoice];
-- Expected: No records

-- Check penjualan_detail (should be empty)
SELECT * FROM penjualan_detail
WHERE id_penjualan = [penjualan_id_from_invoice];
-- Expected: No records

-- Check product stock (should be unchanged)
SELECT stok FROM produk WHERE id_produk = [product_a_id];
-- Expected: stok = 100 (unchanged)
```

---

### ✅ Test 2: Edit Draft Invoice (Still NO Stock Impact)

**Purpose:** Verify draft can be edited without stock impact

**Steps:**

1. Find the draft invoice from Test 1
2. Click **"Edit"** button
3. Modify:
    - Change Product A qty from 10 to 20
    - Add Product B with qty = 5
4. Click **"Simpan"**

**Expected Results:**

-   ✅ Changes saved successfully
-   ✅ Status still **"Draft"**
-   ✅ Invoice number still **"DRAFT"**
-   ✅ Can edit again if needed
-   ✅ **Product A stock still 100** (NOT reduced)
-   ✅ **Product B stock unchanged**
-   ✅ **Still NO piutang record**
-   ✅ **Still NO penjualan detail**

---

### ✅ Test 3: Confirm Draft Invoice (NOW Stock Reduced)

**Purpose:** Verify confirmation generates invoice number, reduces stock, creates piutang

**Steps:**

1. Find the draft invoice from Test 2
2. Note current stocks:
    - Product A: 100 units
    - Product B: 50 units
3. Click **"Konfirmasi"** button
4. Read confirmation dialog
5. Click **"OK"**

**Expected Results:**

-   ✅ Success message: "Invoice berhasil dikonfirmasi dan nomor invoice telah digenerate"
-   ✅ Invoice number changed to **001/PBU/INV/XI/2025** (or similar)
-   ✅ Status changed to **"Menunggu"** (amber badge)
-   ✅ **Edit** button NOT visible
-   ✅ **Konfirmasi** button NOT visible
-   ✅ **Bayar** button IS visible
-   ✅ Draft count decreased by 1
-   ✅ Menunggu count increased by 1
-   ✅ **Product A stock reduced to 80** (100 - 20)
-   ✅ **Product B stock reduced to 45** (50 - 5)
-   ✅ **Piutang record created** with:
    -   nama = "Invoice 001/PBU/INV/XI/2025"
    -   jumlah_piutang = invoice total
    -   sisa_piutang = invoice total
    -   status = "belum_lunas"
-   ✅ **Penjualan details created** for both products
-   ✅ **Journal entry created** (check logs)

**Database Verification:**

```sql
-- Check invoice
SELECT id_sales_invoice, no_invoice, status, total
FROM sales_invoice
WHERE id_sales_invoice = [invoice_id];
-- Expected: no_invoice = '001/PBU/INV/XI/2025', status = 'menunggu'

-- Check piutang
SELECT * FROM piutang
WHERE id_penjualan = [penjualan_id];
-- Expected: 1 record with nama = 'Invoice 001/PBU/INV/XI/2025'

-- Check penjualan_detail
SELECT * FROM penjualan_detail
WHERE id_penjualan = [penjualan_id];
-- Expected: 2 records (Product A and Product B)

-- Check product stocks
SELECT id_produk, nama_produk, stok FROM produk
WHERE id_produk IN ([product_a_id], [product_b_id]);
-- Expected: Product A stok = 80, Product B stok = 45
```

---

### ✅ Test 4: Stock Validation on Confirm

**Purpose:** Verify system prevents confirmation if stock insufficient

**Steps:**

1. Create new draft invoice
2. Add Product C with qty = 100
3. Save as draft
4. Manually reduce Product C stock to 50 (via product management)
5. Try to confirm the draft invoice

**Expected Results:**

-   ✅ Error message: "Stok tidak mencukupi untuk produk: Product C. Stok tersedia: 50, dibutuhkan: 100"
-   ✅ Invoice remains **"Draft"**
-   ✅ Invoice number still **"DRAFT"**
-   ✅ **NO stock reduction**
-   ✅ **NO piutang created**
-   ✅ **NO penjualan detail created**
-   ✅ Can edit invoice to reduce qty

---

### ✅ Test 5: Multiple Drafts, Confirm One by One

**Purpose:** Verify invoice number increments correctly

**Steps:**

1. Create Draft Invoice #1 (Product A, qty 5)
2. Create Draft Invoice #2 (Product B, qty 10)
3. Create Draft Invoice #3 (Product C, qty 15)
4. Confirm Draft #1
5. Confirm Draft #3
6. Confirm Draft #2

**Expected Results:**

-   ✅ Draft #1 gets number: **001/PBU/INV/XI/2025**
-   ✅ Draft #3 gets number: **002/PBU/INV/XI/2025**
-   ✅ Draft #2 gets number: **003/PBU/INV/XI/2025**
-   ✅ All stocks reduced correctly
-   ✅ All piutang created correctly
-   ✅ Stats accurate:
    -   Draft count = 0
    -   Menunggu count = 3

---

### ✅ Test 6: Edit After Confirm (Should Fail)

**Purpose:** Verify confirmed invoice cannot be edited

**Steps:**

1. Find a confirmed invoice (status = "menunggu")
2. Look for Edit button

**Expected Results:**

-   ✅ **Edit button NOT visible**
-   ✅ Only **Bayar** button visible
-   ✅ Cannot modify invoice

---

### ✅ Test 7: Delete Draft vs Delete Confirmed

**Purpose:** Verify delete behavior for draft vs confirmed

**Steps:**

1. Create draft invoice with Product D, qty 10
2. Note Product D stock (e.g., 100)
3. Delete draft invoice
4. Check Product D stock
5. Create new draft with Product D, qty 10
6. Confirm invoice (stock becomes 90)
7. Try to delete confirmed invoice

**Expected Results for Draft Delete:**

-   ✅ Draft deleted successfully
-   ✅ **Product D stock still 100** (no impact)
-   ✅ No piutang to clean up

**Expected Results for Confirmed Delete:**

-   ✅ Deletion should be restricted OR
-   ✅ If allowed, should restore stock to 100
-   ✅ Should delete piutang record
-   ✅ Should reverse journal entry

---

### ✅ Test 8: Payment Flow After Confirmation

**Purpose:** Verify payment works correctly after confirmation

**Steps:**

1. Confirm a draft invoice
2. Click **"Bayar"** button
3. Enter payment:
    - Jenis: Cash
    - Jumlah: Full amount
4. Save payment

**Expected Results:**

-   ✅ Payment recorded
-   ✅ Status changed to **"Lunas"**
-   ✅ Piutang updated:
    -   sisa_piutang = 0
    -   status = "lunas"
-   ✅ **"Lihat Bukti"** button visible

---

## Quick Verification Commands

### Check Draft Invoices

```sql
SELECT id_sales_invoice, no_invoice, status, total, created_at
FROM sales_invoice
WHERE status = 'draft'
ORDER BY created_at DESC;
```

### Check Stock for Product

```sql
SELECT id_produk, nama_produk, stok
FROM produk
WHERE id_produk = [product_id];
```

### Check Piutang for Invoice

```sql
SELECT p.*, si.no_invoice
FROM piutang p
JOIN sales_invoice si ON p.id_penjualan = si.id_penjualan
WHERE si.id_sales_invoice = [invoice_id];
```

### Check Penjualan Details

```sql
SELECT pd.*, p.nama_produk
FROM penjualan_detail pd
JOIN produk p ON pd.id_produk = p.id_produk
WHERE pd.id_penjualan = [penjualan_id];
```

### Check Invoice Counter

```sql
SELECT * FROM invoice_sales_counter
WHERE outlet_id = [outlet_id]
  AND month = MONTH(NOW())
  AND year = YEAR(NOW());
```

---

## Common Issues & Solutions

### Issue: Stock reduced on draft creation

**Solution:** Check `store()` method - should skip stock reduction for draft

### Issue: Piutang created on draft creation

**Solution:** Check `store()` method - should skip piutang creation for draft

### Issue: Invoice number generated on draft creation

**Solution:** Check `store()` method - should set no_invoice = 'DRAFT'

### Issue: Cannot confirm invoice

**Solution:**

-   Check stock availability
-   Check invoice status is 'draft'
-   Check logs for errors

### Issue: Stock not reduced on confirm

**Solution:** Check `processInvoiceItemsOnConfirm()` method is called

### Issue: Piutang not created on confirm

**Solution:** Check `createPiutangFromInvoice()` method is called

---

## Test Summary Checklist

-   [ ] Create draft - NO stock impact
-   [ ] Edit draft - NO stock impact
-   [ ] Confirm draft - stock reduced, piutang created
-   [ ] Stock validation on confirm
-   [ ] Invoice number generation
-   [ ] Multiple drafts confirmed in order
-   [ ] Cannot edit after confirm
-   [ ] Delete draft vs confirmed
-   [ ] Payment after confirm
-   [ ] Stats accuracy
-   [ ] Database integrity

---

**Status:** Ready for comprehensive testing
**Priority:** HIGH - Critical workflow change
**Risk:** Medium - Affects stock and financial records

**Test Date:** ******\_\_\_******
**Tested By:** ******\_\_\_******
**Result:** [ ] Pass [ ] Fail
**Notes:** **********************\_\_\_**********************
