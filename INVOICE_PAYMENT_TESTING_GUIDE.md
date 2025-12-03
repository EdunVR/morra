# Invoice Payment Testing Guide 🧪

## Quick Start Testing

### Prerequisites

-   ✅ Database migrations executed
-   ✅ Storage link configured
-   ✅ At least one invoice with status "menunggu" or "dibayar_sebagian"

## Test Scenarios

### Scenario 1: Full Payment (Cash)

**Goal:** Pay entire invoice amount at once with cash

1. Navigate to **Penjualan → Invoice**
2. Find invoice with status "menunggu"
3. Click **"Bayar"** button
4. In payment modal:
    - Verify invoice summary shows correct amounts
    - Tanggal Bayar: Today's date
    - Jumlah Bayar: Should auto-fill with full amount
    - Jenis Pembayaran: Select **"Cash"**
    - Upload bukti pembayaran (JPG/PNG/PDF)
5. Click **"Konfirmasi Pembayaran"**

**Expected Results:**

-   ✅ Success message appears
-   ✅ Invoice status changes to **"lunas"**
-   ✅ Total Dibayar = Total Invoice
-   ✅ Sisa Tagihan = 0
-   ✅ Modal closes automatically
-   ✅ Payment appears in history

---

### Scenario 2: Partial Payment (Transfer)

**Goal:** Pay 50% of invoice with bank transfer

1. Navigate to **Penjualan → Invoice**
2. Find invoice with status "menunggu"
3. Click **"Bayar"** button
4. In payment modal:
    - Note the total amount (e.g., Rp 1,000,000)
    - Tanggal Bayar: Today's date
    - Jumlah Bayar: Enter **50%** of total (e.g., 500000)
    - Jenis Pembayaran: Select **"Transfer"**
    - Nama Bank: Enter bank name (e.g., "BCA")
    - Nama Pengirim: Enter sender name
    - Upload bukti pembayaran
5. Click **"Konfirmasi Pembayaran"**

**Expected Results:**

-   ✅ Success message appears
-   ✅ Invoice status changes to **"dibayar_sebagian"**
-   ✅ Total Dibayar = 500,000
-   ✅ Sisa Tagihan = 500,000
-   ✅ Modal stays open for next payment
-   ✅ Payment appears in history table
-   ✅ Form resets for next installment

---

### Scenario 3: Multiple Installments

**Goal:** Complete payment in 3 installments

**First Payment (30%)**

1. Open invoice payment modal
2. Pay 30% of total amount
3. Verify status = "dibayar_sebagian"
4. Verify payment history shows 1 entry

**Second Payment (40%)**

1. Modal should still be open
2. Pay 40% of total amount
3. Verify status = "dibayar_sebagian"
4. Verify payment history shows 2 entries
5. Verify sisa tagihan = 30%

**Third Payment (30%)**

1. Modal should still be open
2. Pay remaining 30%
3. Verify status = "lunas"
4. Verify payment history shows 3 entries
5. Verify sisa tagihan = 0
6. Modal closes automatically

---

### Scenario 4: Image Compression Test

**Goal:** Verify image compression works

1. Prepare test images:
    - Large image (3-5 MB)
    - Medium image (1-2 MB)
    - Small image (100-500 KB)
2. Open payment modal
3. Upload large image
4. Watch for compression status message
5. Submit payment
6. Check file size in storage:
    ```
    storage/app/public/bukti_pembayaran/
    ```

**Expected Results:**

-   ✅ Compression status shows "Mengkompresi gambar..."
-   ✅ Then shows "Gambar berhasil dikompresi"
-   ✅ Large image (5MB) → ~800KB
-   ✅ Image dimensions max 1200x1200
-   ✅ Quality maintained at 80%

---

### Scenario 5: Validation Tests

#### Test 5.1: Overpayment Prevention

1. Open payment modal for invoice with Rp 1,000,000 remaining
2. Try to pay Rp 1,500,000
3. Submit payment

**Expected:** Error message "Jumlah bayar melebihi sisa tagihan"

#### Test 5.2: Missing Bukti

1. Open payment modal
2. Fill all fields EXCEPT bukti pembayaran
3. Try to submit

**Expected:** Error message "Bukti pembayaran wajib dilampirkan"

#### Test 5.3: Invalid File Type

1. Open payment modal
2. Try to upload .docx or .txt file

**Expected:** File input rejects the file (browser validation)

#### Test 5.4: File Too Large

1. Open payment modal
2. Try to upload file > 5MB

**Expected:** Validation error or browser rejection

---

### Scenario 6: Payment History View

**Goal:** Verify payment history displays correctly

1. Open payment modal for invoice with multiple payments
2. Check payment history table shows:
    - ✅ All previous payments
    - ✅ Correct dates
    - ✅ Correct amounts
    - ✅ Payment methods (Cash/Transfer)
    - ✅ "Lihat Bukti" buttons
3. Click "Lihat Bukti" button
4. Verify bukti opens in new tab

---

### Scenario 7: Status Transitions

**Goal:** Verify all status transitions work

#### Test 7.1: menunggu → lunas

-   Start: Invoice with status "menunggu"
-   Action: Pay full amount
-   Expected: Status changes to "lunas"

#### Test 7.2: menunggu → dibayar_sebagian

-   Start: Invoice with status "menunggu"
-   Action: Pay partial amount
-   Expected: Status changes to "dibayar_sebagian"

#### Test 7.3: dibayar_sebagian → lunas

-   Start: Invoice with status "dibayar_sebagian"
-   Action: Pay remaining amount
-   Expected: Status changes to "lunas"

---

## Edge Cases to Test

### Edge Case 1: Exact Remaining Amount

1. Invoice has Rp 1,234,567 remaining
2. Pay exactly Rp 1,234,567
3. Verify status = "lunas"

### Edge Case 2: Very Small Payment

1. Invoice has Rp 1,000,000 remaining
2. Pay Rp 1,000 (0.1%)
3. Verify payment recorded correctly

### Edge Case 3: PDF Bukti

1. Upload PDF file as bukti
2. Verify no compression attempted
3. Verify PDF stored correctly

### Edge Case 4: PNG with Transparency

1. Upload PNG with transparent background
2. Verify compression works
3. Verify transparency preserved (or white background)

---

## Database Verification

After each test, verify database records:

### Check sales_invoice table

```sql
SELECT
    no_invoice,
    total,
    total_dibayar,
    sisa_tagihan,
    status
FROM sales_invoice
WHERE no_invoice = 'YOUR_INVOICE_NUMBER';
```

### Check invoice_payment_history table

```sql
SELECT
    tanggal_bayar,
    jumlah_bayar,
    jenis_pembayaran,
    bukti_pembayaran,
    created_by
FROM invoice_payment_history
WHERE id_sales_invoice = YOUR_INVOICE_ID
ORDER BY tanggal_bayar DESC;
```

### Check piutang status

```sql
SELECT
    p.status,
    si.status as invoice_status
FROM piutang p
JOIN sales_invoice si ON p.id_penjualan = si.id_penjualan
WHERE si.no_invoice = 'YOUR_INVOICE_NUMBER';
```

---

## Performance Testing

### Test 1: Multiple Concurrent Payments

1. Open 3 browser tabs
2. Open same invoice in all tabs
3. Try to make payments simultaneously
4. Verify no duplicate payments
5. Verify total_dibayar is correct

### Test 2: Large Image Upload

1. Upload 5MB image
2. Measure compression time
3. Expected: < 3 seconds

### Test 3: Payment History Load Time

1. Create invoice with 50+ payments
2. Open payment modal
3. Measure load time
4. Expected: < 2 seconds

---

## Browser Compatibility

Test on:

-   ✅ Chrome (latest)
-   ✅ Firefox (latest)
-   ✅ Edge (latest)
-   ✅ Safari (if available)

---

## Mobile Testing

Test on mobile devices:

-   ✅ Payment modal responsive
-   ✅ File upload works
-   ✅ Payment history table scrollable
-   ✅ Buttons accessible

---

## Troubleshooting

### Issue: Image not compressing

**Solution:** Check GD library installed

```bash
php -m | grep -i gd
```

### Issue: Bukti not displaying

**Solution:** Check storage link

```bash
php artisan storage:link
```

### Issue: Payment not recorded

**Solution:** Check Laravel logs

```bash
tail -f storage/logs/laravel.log
```

### Issue: Status not updating

**Solution:** Check database transaction

```sql
SELECT * FROM invoice_payment_history
WHERE id_sales_invoice = YOUR_ID
ORDER BY created_at DESC LIMIT 1;
```

---

## Success Criteria

All tests pass when:

-   ✅ Full payments work correctly
-   ✅ Partial payments work correctly
-   ✅ Multiple installments work correctly
-   ✅ Image compression works
-   ✅ Validation prevents invalid data
-   ✅ Payment history displays correctly
-   ✅ Status transitions work correctly
-   ✅ Bukti pembayaran accessible
-   ✅ Database records accurate
-   ✅ No errors in logs

---

**Happy Testing! 🎉**
