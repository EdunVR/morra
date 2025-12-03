# Quick Test Guide - Piutang Realtime

## Pre-Test Setup

### 1. Check Database

```sql
-- Check if tables exist
SHOW TABLES LIKE 'sales_invoice';
SHOW TABLES LIKE 'invoice_payment_history';

-- Check sample data
SELECT COUNT(*) FROM sales_invoice WHERE status != 'draft' AND status != 'dibatalkan';
SELECT COUNT(*) FROM invoice_payment_history;

-- Check invoice with payments
SELECT
    si.id_sales_invoice,
    si.no_invoice,
    si.total,
    si.status,
    COUNT(iph.id) as payment_count,
    SUM(iph.jumlah_bayar) as total_dibayar
FROM sales_invoice si
LEFT JOIN invoice_payment_history iph ON si.id_sales_invoice = iph.id_sales_invoice
WHERE si.status != 'draft' AND si.status != 'dibatalkan'
GROUP BY si.id_sales_invoice
LIMIT 10;
```

## Test 1: View Piutang List (Realtime Data)

### Steps:

1. Open browser: `http://localhost/finance/piutang`
2. Login if needed
3. Wait for data to load

### Expected Results:

✅ Tabel piutang menampilkan data dari `sales_invoice`
✅ Kolom "No Invoice" menampilkan `no_invoice` (bukan dari tabel piutang lama)
✅ Summary cards menampilkan total yang akurat
✅ Status dihitung realtime dari payment history

### Verify in Console:

```javascript
// Open Developer Tools > Network
// Check request to: /finance/piutang/data
// Response should show data from sales_invoice
```

### Check Response Format:

```json
{
    "success": true,
    "data": [
        {
            "id_piutang": 123, // This is id_sales_invoice now
            "invoice_number": "INV-2025-001", // From sales_invoice.no_invoice
            "jumlah_dibayar": 500000, // Calculated from invoice_payment_history
            "sisa_piutang": 500000, // Calculated: total - jumlah_dibayar
            "status": "dibayar_sebagian" // Calculated based on payments
        }
    ]
}
```

## Test 2: Make Payment & See Realtime Update

### Steps:

1. Di tabel piutang, cari invoice dengan status "Belum Lunas"
2. Note the `sisa_piutang` amount
3. Klik tombol "Bayar"
4. Redirect ke halaman invoice
5. Modal pembayaran terbuka
6. Isi form pembayaran:
    - Jumlah: 100000 (atau sebagian dari sisa)
    - Tanggal: today
    - Metode: Transfer
    - Bank: BCA
    - Pengirim: Test User
7. Klik "Proses Pembayaran"
8. Wait for success notification
9. Kembali ke halaman piutang: `/finance/piutang`

### Expected Results:

✅ Payment berhasil disimpan ke `invoice_payment_history`
✅ Data piutang **otomatis terupdate** (realtime!)
✅ `jumlah_dibayar` bertambah
✅ `sisa_piutang` berkurang
✅ Status berubah ke "Dibayar Sebagian" atau "Lunas"
✅ Summary cards terupdate

### Verify in Database:

```sql
-- Check new payment record
SELECT * FROM invoice_payment_history
WHERE id_sales_invoice = [your_invoice_id]
ORDER BY created_at DESC
LIMIT 1;

-- Check calculated totals
SELECT
    si.id_sales_invoice,
    si.no_invoice,
    si.total,
    SUM(iph.jumlah_bayar) as total_dibayar,
    si.total - SUM(iph.jumlah_bayar) as sisa_piutang
FROM sales_invoice si
LEFT JOIN invoice_payment_history iph ON si.id_sales_invoice = iph.id_sales_invoice
WHERE si.id_sales_invoice = [your_invoice_id]
GROUP BY si.id_sales_invoice;
```

## Test 3: View Detail with Payment History

### Steps:

1. Di tabel piutang, klik nomor invoice (kolom pertama)
2. Modal detail terbuka
3. Scroll down to "Riwayat Pembayaran" section

### Expected Results:

✅ Modal detail terbuka
✅ Section "Riwayat Pembayaran" muncul (NEW!)
✅ All payment records ditampilkan
✅ Payment details lengkap:

-   Tanggal bayar
-   Jumlah bayar
-   Jenis pembayaran
-   Bank (if transfer)
-   Pengirim
-   Penerima
-   Keterangan
-   Link bukti transfer (if exists)
    ✅ Payments sorted by date (newest first)
    ✅ Total payments = jumlah_dibayar

### Visual Check:

```
┌─────────────────────────────────────┐
│ Riwayat Pembayaran                  │
├─────────────────────────────────────┤
│ ┌─────────────────────────────────┐ │
│ │ Pembayaran #1                   │ │
│ │ 24 Nov 2025    Rp 500.000      │ │
│ │ Transfer - BCA                  │ │
│ │ Pengirim: John Doe              │ │
│ │ [Lihat Bukti Transfer]          │ │
│ └─────────────────────────────────┘ │
│ ┌─────────────────────────────────┐ │
│ │ Pembayaran #2                   │ │
│ │ 20 Nov 2025    Rp 300.000      │ │
│ │ Cash                            │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

## Test 4: Status Calculation Accuracy

### Test Case A: Belum Lunas

```sql
-- Create test invoice
INSERT INTO sales_invoice (no_invoice, total, status, ...)
VALUES ('TEST-001', 1000000, 'menunggu', ...);

-- No payments yet
-- Expected: status = 'belum_lunas', sisa_piutang = 1000000
```

### Test Case B: Dibayar Sebagian

```sql
-- Add partial payment
INSERT INTO invoice_payment_history (id_sales_invoice, jumlah_bayar, ...)
VALUES ([invoice_id], 400000, ...);

-- Expected: status = 'dibayar_sebagian', sisa_piutang = 600000
```

### Test Case C: Lunas

```sql
-- Add remaining payment
INSERT INTO invoice_payment_history (id_sales_invoice, jumlah_bayar, ...)
VALUES ([invoice_id], 600000, ...);

-- Expected: status = 'lunas', sisa_piutang = 0
```

### Test Case D: Overpayment

```sql
-- Add overpayment
INSERT INTO invoice_payment_history (id_sales_invoice, jumlah_bayar, ...)
VALUES ([invoice_id], 100000, ...);

-- Expected: status = 'lunas', sisa_piutang = -100000 (or 0)
```

## Test 5: Overdue Detection

### Test Case: Overdue Invoice

```sql
-- Create invoice with past due date
INSERT INTO sales_invoice (no_invoice, total, due_date, status, ...)
VALUES ('TEST-002', 500000, '2025-11-01', 'menunggu', ...);

-- Expected: is_overdue = true, days_overdue = 23 (if today is 2025-11-24)
```

### Verify in UI:

✅ Invoice shows "Jatuh Tempo" badge (red)
✅ Days overdue displayed: "Terlambat 23 hari"
✅ Summary card "Jatuh Tempo" count includes this invoice

## Test 6: Filter & Search

### Test Filters:

1. **Filter by Outlet**
    - Select different outlet
    - Data filtered correctly
2. **Filter by Status**

    - Select "Belum Lunas"
    - Only unpaid invoices shown
    - Select "Lunas"
    - Only paid invoices shown

3. **Filter by Date Range**

    - Set start date: 2025-11-01
    - Set end date: 2025-11-30
    - Only invoices in range shown

4. **Search**
    - Type invoice number
    - Results filtered
    - Type customer name
    - Results filtered

## Test 7: Performance Test

### Large Dataset Test:

```sql
-- Check record count
SELECT COUNT(*) FROM sales_invoice WHERE status != 'draft';
SELECT COUNT(*) FROM invoice_payment_history;

-- If > 1000 records, test performance
```

### Steps:

1. Open piutang page
2. Measure load time (should be < 2 seconds)
3. Apply filters
4. Measure filter time (should be < 1 second)
5. Open detail modal
6. Measure modal load time (should be < 1 second)

### Performance Metrics:

-   ✅ Initial load: < 2s
-   ✅ Filter apply: < 1s
-   ✅ Detail modal: < 1s
-   ✅ No lag on scroll
-   ✅ Smooth animations

## Test 8: Error Handling

### Test Case A: No Data

```sql
-- Clear all invoices (in test DB only!)
DELETE FROM invoice_payment_history;
DELETE FROM sales_invoice;
```

Expected: "Tidak ada data piutang" message

### Test Case B: Network Error

1. Open DevTools > Network
2. Set throttling to "Offline"
3. Refresh page
   Expected: Error notification "Gagal memuat data piutang"

### Test Case C: Invalid Invoice ID

1. Open detail modal with invalid ID
   Expected: Error notification "Invoice tidak ditemukan"

## Test 9: Backward Compatibility

### Check Old Piutang Table:

```sql
-- Old table still exists
SELECT * FROM piutang LIMIT 10;
```

### Verify:

✅ Old table not used for display
✅ New implementation doesn't break old data
✅ Can still access old data if needed

## Test 10: Integration Test

### Full Flow Test:

1. ✅ View piutang list (realtime data)
2. ✅ Click "Bayar" button
3. ✅ Redirect to invoice page
4. ✅ Modal pembayaran auto-open
5. ✅ Submit payment
6. ✅ Payment saved to invoice_payment_history
7. ✅ Return to piutang page
8. ✅ Data auto-updated (realtime!)
9. ✅ Click invoice number
10. ✅ Modal detail shows payment history
11. ✅ All data accurate and consistent

## Success Criteria

### Data Accuracy

-   [ ] All amounts calculated correctly
-   [ ] Status reflects actual payments
-   [ ] Overdue detection accurate
-   [ ] Summary totals match detail

### Realtime Updates

-   [ ] Data updates immediately after payment
-   [ ] No manual refresh needed
-   [ ] No cache delay
-   [ ] Consistent across all views

### Payment History

-   [ ] All payments listed
-   [ ] Details complete
-   [ ] Chronological order
-   [ ] Bukti transfer accessible

### Performance

-   [ ] Load time acceptable
-   [ ] No lag or freeze
-   [ ] Smooth user experience
-   [ ] Handles large datasets

### UI/UX

-   [ ] Clear and intuitive
-   [ ] Responsive design
-   [ ] Error messages helpful
-   [ ] Loading states shown

## Troubleshooting

### Issue: Data tidak muncul

**Check:**

```sql
SELECT COUNT(*) FROM sales_invoice WHERE status != 'draft' AND status != 'dibatalkan';
```

If 0, create test data.

### Issue: Status tidak akurat

**Check:**

```sql
SELECT
    si.id_sales_invoice,
    si.total,
    SUM(iph.jumlah_bayar) as total_dibayar,
    si.total - SUM(iph.jumlah_bayar) as sisa
FROM sales_invoice si
LEFT JOIN invoice_payment_history iph ON si.id_sales_invoice = iph.id_sales_invoice
GROUP BY si.id_sales_invoice;
```

### Issue: Payment history tidak muncul

**Check:**

```sql
SELECT * FROM invoice_payment_history WHERE id_sales_invoice = [your_id];
```

### Issue: Performance lambat

**Check:**

```sql
-- Check indexes
SHOW INDEX FROM sales_invoice;
SHOW INDEX FROM invoice_payment_history;

-- Should have indexes on:
-- - sales_invoice.id_sales_invoice (PRIMARY)
-- - invoice_payment_history.id_sales_invoice (INDEX)
-- - invoice_payment_history.tanggal_bayar (INDEX)
```

## Test Report Template

```
# Piutang Realtime Test Report

Date: [DATE]
Tester: [NAME]
Environment: [DEV/STAGING/PROD]

## Test Results

### Test 1: View Piutang List
Status: [PASS/FAIL]
Notes: [...]

### Test 2: Make Payment
Status: [PASS/FAIL]
Notes: [...]

### Test 3: View Detail
Status: [PASS/FAIL]
Notes: [...]

### Test 4: Status Calculation
Status: [PASS/FAIL]
Notes: [...]

### Test 5: Overdue Detection
Status: [PASS/FAIL]
Notes: [...]

## Issues Found
1. [Issue description]
2. [Issue description]

## Overall Status
[PASS/FAIL]

## Recommendations
[...]
```

---

**Ready for Testing!** 🚀

All features implemented and documented. Start testing from Test 1 and work through all test cases.
