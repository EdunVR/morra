# Testing Guide - Task 3: Backend Payment Processing

## Prerequisites

-   Database migrations completed (tasks 1.1 and 1.2)
-   POPaymentHistory model created (task 2.1)
-   PurchaseOrder model updated (task 2.2)
-   At least one Purchase Order exists in the database

## API Endpoints to Test

### 1. Process Payment (POST)

**Endpoint:** `/pembelian/purchase-order/payment`
**Method:** POST
**Route Name:** `pembelian.purchase-order.payment`

**Test Cases:**

#### Test 1.1: Successful Cash Payment

```bash
POST /pembelian/purchase-order/payment
Content-Type: multipart/form-data

{
  "po_id": 1,
  "jumlah_pembayaran": 5000000,
  "jenis_pembayaran": "cash",
  "tanggal_pembayaran": "2025-11-21",
  "penerima": "John Doe",
  "catatan": "Pembayaran pertama"
}
```

**Expected Result:**

-   Status: 200 OK
-   Response: `{"success": true, "message": "Pembayaran berhasil dicatat"}`
-   PO `total_dibayar` updated
-   PO `sisa_pembayaran` calculated
-   PO status updated to `partial` or `paid`
-   Payment history record created
-   Hutang updated
-   Journal entry created

#### Test 1.2: Successful Transfer Payment with Bukti

```bash
POST /pembelian/purchase-order/payment
Content-Type: multipart/form-data

{
  "po_id": 1,
  "jumlah_pembayaran": 3000000,
  "jenis_pembayaran": "transfer",
  "tanggal_pembayaran": "2025-11-21",
  "penerima": "Jane Smith",
  "bukti_pembayaran": [image file],
  "catatan": "Pembayaran kedua via transfer"
}
```

**Expected Result:**

-   Status: 200 OK
-   Image compressed and saved
-   File stored in `storage/app/public/po_bukti_pembayaran/`
-   All other updates as Test 1.1

#### Test 1.3: Overpayment Prevention

```bash
POST /pembelian/purchase-order/payment
{
  "po_id": 1,
  "jumlah_pembayaran": 99999999, // Amount > remaining balance
  "jenis_pembayaran": "cash",
  "tanggal_pembayaran": "2025-11-21",
  "penerima": "Test User"
}
```

**Expected Result:**

-   Status: 500
-   Error message: "Jumlah pembayaran melebihi sisa pembayaran"

#### Test 1.4: Validation Errors

```bash
POST /pembelian/purchase-order/payment
{
  "po_id": 1,
  "jumlah_pembayaran": -100, // Invalid amount
  "jenis_pembayaran": "invalid", // Invalid type
  "tanggal_pembayaran": "invalid-date"
}
```

**Expected Result:**

-   Status: 422
-   Validation errors returned

#### Test 1.5: Invalid File Type

```bash
POST /pembelian/purchase-order/payment
{
  "po_id": 1,
  "jumlah_pembayaran": 1000000,
  "jenis_pembayaran": "transfer",
  "tanggal_pembayaran": "2025-11-21",
  "penerima": "Test User",
  "bukti_pembayaran": [.exe file] // Invalid file type
}
```

**Expected Result:**

-   Status: 422
-   Validation error: "bukti_pembayaran must be jpg, jpeg, png, or pdf"

### 2. Get Payment History (GET)

**Endpoint:** `/pembelian/purchase-order/{id}/payment-history`
**Method:** GET
**Route Name:** `pembelian.purchase-order.payment-history`

**Test Cases:**

#### Test 2.1: Get History for PO with Payments

```bash
GET /pembelian/purchase-order/1/payment-history
```

**Expected Result:**

-   Status: 200 OK
-   Response includes:
    -   `purchase_order` object with totals and status
    -   `payment_history` array with all payments
    -   Dates formatted as d/m/Y
    -   All payment details present

**Sample Response:**

```json
{
    "success": true,
    "data": {
        "purchase_order": {
            "no_po": "PO/2025/001",
            "total": 10000000,
            "total_dibayar": 5000000,
            "sisa_pembayaran": 5000000,
            "status": "partial"
        },
        "payment_history": [
            {
                "id": 1,
                "tanggal_pembayaran": "21/11/2025",
                "jumlah_pembayaran": 5000000,
                "jenis_pembayaran": "cash",
                "penerima": "John Doe",
                "bukti_pembayaran": "po_bukti_pembayaran/bukti_po_...",
                "catatan": "Pembayaran pertama",
                "created_at": "21/11/2025 10:30"
            }
        ]
    }
}
```

#### Test 2.2: Get History for PO without Payments

```bash
GET /pembelian/purchase-order/2/payment-history
```

**Expected Result:**

-   Status: 200 OK
-   `payment_history` array is empty
-   PO details still returned

#### Test 2.3: Get History for Non-existent PO

```bash
GET /pembelian/purchase-order/99999/payment-history
```

**Expected Result:**

-   Status: 500
-   Error message returned

### 3. Download Bukti Transfer (GET)

**Endpoint:** `/pembelian/purchase-order/payment/{id}/download-bukti`
**Method:** GET
**Route Name:** `pembelian.purchase-order.download-bukti`

**Test Cases:**

#### Test 3.1: Download Existing Bukti

```bash
GET /pembelian/purchase-order/payment/1/download-bukti
```

**Expected Result:**

-   Status: 200 OK
-   File download initiated
-   Filename: `Bukti-Pembayaran-PO-1.jpg` (or .pdf)
-   Content-Type header set correctly

#### Test 3.2: Download Non-existent Bukti

```bash
GET /pembelian/purchase-order/payment/99999/download-bukti
```

**Expected Result:**

-   Status: 500
-   Error message: "Bukti pembayaran tidak ditemukan"

#### Test 3.3: Download for Payment without Bukti

```bash
GET /pembelian/purchase-order/payment/2/download-bukti
// Assuming payment 2 has no bukti_pembayaran
```

**Expected Result:**

-   Status: 404
-   Error message: "Bukti pembayaran tidak ditemukan"

## Database Verification

### Check Payment History Table

```sql
SELECT * FROM po_payment_history WHERE id_purchase_order = 1;
```

**Verify:**

-   Records created with correct data
-   `bukti_pembayaran` path stored correctly
-   Timestamps populated

### Check Purchase Order Updates

```sql
SELECT
  id_purchase_order,
  no_po,
  total,
  total_dibayar,
  sisa_pembayaran,
  status
FROM purchase_order
WHERE id_purchase_order = 1;
```

**Verify:**

-   `total_dibayar` = sum of all payments
-   `sisa_pembayaran` = total - total_dibayar
-   `status` updated correctly:
    -   `pending` if no payments
    -   `partial` if 0 < total_dibayar < total
    -   `paid` if total_dibayar >= total

### Check Hutang Updates

```sql
SELECT * FROM hutang
WHERE id_supplier = (SELECT id_supplier FROM purchase_order WHERE id_purchase_order = 1);
```

**Verify:**

-   `hutang` reduced by payment amount
-   `status` = 'lunas' if fully paid

### Check Journal Entries

```sql
SELECT * FROM journal_entries
WHERE reference_type = 'pembelian_payment'
AND reference_id = 1;
```

**Verify:**

-   Journal entry created
-   Debit to Hutang Usaha
-   Credit to Kas/Bank
-   Amounts match payment

## File System Verification

### Check Bukti Files

```bash
# Check if directory exists
ls storage/app/public/po_bukti_pembayaran/

# Check file properties
ls -lh storage/app/public/po_bukti_pembayaran/bukti_po_*
```

**Verify:**

-   Files exist in correct directory
-   Image files compressed (smaller than original)
-   PDF files stored without modification
-   File permissions correct (readable)

## Log Verification

### Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

**Look for:**

-   "PO payment processed successfully"
-   "PO bukti image compressed" (for image uploads)
-   "Jurnal pembayaran created untuk PO"
-   Any error messages

## Integration Testing Scenarios

### Scenario 1: Full Payment Flow

1. Create a new PO with total 10,000,000
2. Make first payment of 3,000,000 (cash)
3. Verify status = 'partial'
4. Make second payment of 4,000,000 (transfer with bukti)
5. Verify status still 'partial'
6. Make final payment of 3,000,000 (cash)
7. Verify status = 'paid'
8. Get payment history - should show 3 payments
9. Download bukti for payment 2

### Scenario 2: Overpayment Prevention

1. Create PO with total 5,000,000
2. Make payment of 3,000,000
3. Try to make payment of 3,000,000 (should fail)
4. Make payment of 2,000,000 (should succeed)
5. Verify status = 'paid'

### Scenario 3: Image Compression

1. Upload large image (> 2MB, > 1200px)
2. Verify compressed file is smaller
3. Verify dimensions are max 1200x1200
4. Verify quality is acceptable
5. Download and view the file

## Performance Testing

### Test Large File Upload

-   Upload 5MB image
-   Verify compression completes in reasonable time (< 5 seconds)
-   Verify memory usage doesn't spike

### Test Multiple Payments

-   Create 50 payments for a single PO
-   Get payment history
-   Verify response time is acceptable (< 1 second)

## Error Handling Testing

### Test Database Connection Loss

-   Simulate database connection failure during payment
-   Verify transaction rollback
-   Verify no partial data saved

### Test File System Issues

-   Simulate storage directory not writable
-   Verify error handling
-   Verify payment still recorded (without bukti)

## Security Testing

### Test Authorization

-   Attempt to access endpoints without authentication
-   Verify proper authentication required

### Test File Upload Security

-   Try to upload executable files
-   Try to upload files > 5MB
-   Verify all rejected properly

### Test SQL Injection

-   Try malicious input in payment fields
-   Verify Laravel's query builder prevents injection

## Success Criteria

✅ All test cases pass
✅ Database records created correctly
✅ Files stored and retrievable
✅ Status updates work correctly
✅ Hutang integration works
✅ Journal entries created
✅ No errors in logs
✅ Validation works properly
✅ Error handling graceful
✅ Performance acceptable

## Common Issues and Solutions

### Issue: "Bukti pembayaran tidak ditemukan"

**Solution:** Check file path in database matches actual file location

### Issue: Image not compressed

**Solution:** Verify GD library installed: `php -m | grep -i gd`

### Issue: Journal entry not created

**Solution:** Check COA settings configured for outlet

### Issue: Hutang not updated

**Solution:** Verify hutang record exists for supplier/outlet

### Issue: Status not updating

**Solution:** Check calculation logic in processPayment method
