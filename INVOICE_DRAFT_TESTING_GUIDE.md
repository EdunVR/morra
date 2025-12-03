# Invoice Draft Workflow - Testing Guide 🧪

## Quick Test Steps

### Test 1: Create Draft Invoice

1. Login ke sistem
2. Navigate ke **Penjualan → Invoice Penjualan**
3. Click **"Invoice Baru"**
4. Fill form:
    - Select customer
    - Add products
    - Set tanggal & jatuh tempo
5. Click **"Simpan"**
6. **Expected Results:**
    - Invoice created successfully
    - Invoice number shows **"DRAFT"**
    - Status badge shows **"Draft"** (gray)
    - **Edit** button visible
    - **Konfirmasi** button visible
    - **Bayar** button NOT visible
    - Draft count in stats increased by 1

---

### Test 2: Edit Draft Invoice

1. Find a draft invoice
2. Click **"Edit"** button
3. Modify:
    - Change quantity
    - Add/remove products
    - Update keterangan
4. Click **"Simpan"**
5. **Expected Results:**
    - Changes saved successfully
    - Status still **"Draft"**
    - Invoice number still **"DRAFT"**
    - Can edit again if needed

---

### Test 3: Confirm Draft Invoice

1. Find a draft invoice
2. Click **"Konfirmasi"** button
3. Read confirmation dialog:
    > "Konfirmasi invoice ini? Setelah dikonfirmasi, invoice tidak bisa diedit lagi dan nomor invoice akan digenerate."
4. Click **"OK"**
5. **Expected Results:**
    - Success message: "Invoice berhasil dikonfirmasi dan nomor invoice telah digenerate"
    - Invoice number changed from "DRAFT" to actual number (e.g., **001/PBU/INV/XI/2025**)
    - Status changed to **"Menunggu"** (amber badge)
    - **Edit** button NOT visible
    - **Konfirmasi** button NOT visible
    - **Bayar** button IS visible
    - Draft count decreased by 1
    - Menunggu count increased by 1

---

### Test 4: Verify Invoice Number Generation

1. Confirm first invoice of the month
    - Expected: **001/[OUTLET]/INV/[MONTH]/[YEAR]**
2. Confirm second invoice
    - Expected: **002/[OUTLET]/INV/[MONTH]/[YEAR]**
3. Confirm third invoice
    - Expected: **003/[OUTLET]/INV/[MONTH]/[YEAR]**

**Format Breakdown:**

-   `001` = Counter (auto-increment)
-   `PBU` = Outlet code
-   `INV` = Invoice type
-   `XI` = Month in Roman numeral (November = XI)
-   `2025` = Year

---

### Test 5: Verify Piutang Creation

1. Confirm a draft invoice
2. Navigate to **Finance → Piutang**
3. **Expected Results:**
    - New piutang record created
    - `jumlah_piutang` = invoice total
    - `sisa_piutang` = invoice total
    - `status` = "belum_lunas"
    - `id_member` matches invoice customer
    - `id_outlet` matches invoice outlet

---

### Test 6: Payment Flow After Confirmation

1. Find a confirmed invoice (status = "menunggu")
2. Click **"Bayar"** button
3. Enter payment details:
    - Jenis pembayaran: Cash/Transfer
    - Jumlah pembayaran
    - Tanggal pembayaran
    - Upload bukti (if transfer)
4. Click **"Simpan Pembayaran"**
5. **Expected Results:**
    - Payment recorded
    - Status updated to "lunas" or "dibayar_sebagian"
    - **"Lihat Cicilan"** button visible
    - Piutang updated accordingly

---

### Test 7: Stats Dashboard

1. Check stats cards at top of page
2. **Expected:**

    - **Total Invoice** = all invoices
    - **Draft** = invoices with status "draft"
    - **Menunggu** = invoices with status "menunggu"
    - **Lunas** = invoices with status "lunas"
    - **Gagal** = invoices with status "gagal"

3. **Test Actions:**
    - Create draft → Draft count +1
    - Confirm draft → Draft count -1, Menunggu count +1
    - Pay invoice → Menunggu count -1, Lunas count +1

---

### Test 8: Multi-Outlet Scenario

1. Switch to **Outlet A**
2. Create and confirm invoice
    - Expected: **001/A/INV/XI/2025**
3. Switch to **Outlet B**
4. Create and confirm invoice
    - Expected: **001/B/INV/XI/2025**
5. Switch back to **Outlet A**
6. Create and confirm another invoice
    - Expected: **002/A/INV/XI/2025**

**Verify:** Each outlet has separate counter

---

### Test 9: Month Rollover

1. Confirm invoice in November
    - Expected: **001/PBU/INV/XI/2025**
2. Change system date to December (or wait for December)
3. Confirm invoice in December
    - Expected: **001/PBU/INV/XII/2025**

**Verify:** Counter resets to 001 each month

---

### Test 10: Error Handling

1. **Try to confirm non-draft invoice:**

    - Find invoice with status "menunggu"
    - Try to call confirm endpoint directly
    - Expected: Error message "Hanya invoice dengan status draft yang bisa dikonfirmasi"

2. **Try to edit confirmed invoice:**

    - Find confirmed invoice
    - Expected: Edit button NOT visible

3. **Try to pay draft invoice:**
    - Find draft invoice
    - Expected: Bayar button NOT visible

---

## Database Verification

### Check Invoice Table

```sql
SELECT id_sales_invoice, no_invoice, status, total, created_at
FROM sales_invoice
WHERE status = 'draft'
ORDER BY created_at DESC;
```

### Check Piutang Table

```sql
SELECT p.*, si.no_invoice
FROM piutang p
JOIN sales_invoice si ON p.id_penjualan = si.id_penjualan
WHERE si.status = 'menunggu'
ORDER BY p.created_at DESC;
```

### Check Invoice Counter

```sql
SELECT * FROM invoice_sales_counter
ORDER BY year DESC, month DESC, outlet_id;
```

---

## Common Issues & Solutions

### Issue 1: Invoice number not generated

**Symptom:** After confirm, invoice still shows "DRAFT"
**Solution:**

-   Check `invoice_sales_counter` table exists
-   Check outlet has valid `kode` field
-   Check logs: `storage/logs/laravel.log`

### Issue 2: Piutang not created

**Symptom:** No piutang record after confirm
**Solution:**

-   Check `piutang` table exists
-   Check `id_penjualan` field in invoice
-   Check logs for errors

### Issue 3: Stats not updating

**Symptom:** Draft count doesn't change
**Solution:**

-   Refresh page
-   Check browser console for errors
-   Verify `loadStats()` is called after confirm

### Issue 4: Edit button still visible after confirm

**Symptom:** Can still edit confirmed invoice
**Solution:**

-   Clear browser cache
-   Check Alpine.js is loaded
-   Verify template condition: `x-if="invoice.status === 'draft'"`

---

## Performance Testing

### Load Test

1. Create 100 draft invoices
2. Confirm all 100 invoices
3. **Expected:**
    - All invoice numbers sequential
    - No duplicate numbers
    - All piutang records created
    - Stats accurate

### Concurrent Test

1. Open 2 browser tabs
2. Create draft in Tab 1
3. Create draft in Tab 2
4. Confirm both simultaneously
5. **Expected:**
    - Both get unique invoice numbers
    - No race condition
    - Counter increments correctly

---

## Browser Compatibility

Test in:

-   [ ] Chrome
-   [ ] Firefox
-   [ ] Edge
-   [ ] Safari (if available)

**Expected:** All features work consistently across browsers

---

## Mobile Responsiveness

Test on mobile device:

-   [ ] Stats cards display properly
-   [ ] Action buttons accessible
-   [ ] Confirmation dialog readable
-   [ ] Forms usable

---

## Checklist Summary

-   [ ] Create draft invoice
-   [ ] Edit draft invoice multiple times
-   [ ] Confirm draft invoice
-   [ ] Verify invoice number format
-   [ ] Verify piutang creation
-   [ ] Pay confirmed invoice
-   [ ] Check stats accuracy
-   [ ] Test multi-outlet scenario
-   [ ] Test month rollover
-   [ ] Test error handling
-   [ ] Verify database records
-   [ ] Test performance with multiple invoices
-   [ ] Test browser compatibility
-   [ ] Test mobile responsiveness

---

## Sign-off

**Tested by:** ******\_\_\_******  
**Date:** ******\_\_\_******  
**Status:** [ ] Pass [ ] Fail  
**Notes:** **********************\_\_\_**********************

---

## Quick Commands

```bash
# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Check migration status
php artisan migrate:status

# View logs
tail -f storage/logs/laravel.log

# Database query
php artisan tinker
>>> \App\Models\SalesInvoice::where('status', 'draft')->count()
```

---

**Happy Testing! 🎉**
