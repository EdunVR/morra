# Invoice Payment Bug Fixes 🐛

## Bugs Fixed

### Bug #1: Bukti Pembayaran Tidak Muncul untuk Cash ❌ → ✅

**Problem:** Form upload bukti pembayaran hanya muncul saat pilih "Transfer", tidak muncul untuk "Cash"

**Root Cause:**

-   Bukti pembayaran field berada di dalam section `x-show="paymentForm.jenis_pembayaran === 'transfer'"`
-   Seharusnya bukti wajib untuk SEMUA jenis pembayaran (cash & transfer)

**Solution:**

-   Pindahkan bukti pembayaran field keluar dari section transfer
-   Buat sebagai field terpisah yang selalu tampil
-   Update label menjadi "Bukti Pembayaran (Wajib untuk Cash & Transfer)"

**Files Modified:**

-   `resources/views/admin/penjualan/invoice/index.blade.php` (line ~755-830)

---

### Bug #2: Status Langsung Lunas Padahal Bayar Sebagian ❌ → ✅

**Problem:** Saat bayar sebagian (misalnya 50% dari total), status invoice langsung berubah menjadi "lunas"

**Root Cause:**

-   Function `confirmPayment()` menggunakan logic lama yang langsung set `status = 'lunas'`
-   Tidak ada pengecekan apakah pembayaran full atau partial
-   Tidak ada tracking total_dibayar dan sisa_tagihan

**Solution:**

1. **Backend:** Buat function baru `processInvoicePayment()` yang:

    - Menerima `jumlah_bayar` (bukan langsung lunas)
    - Menghitung `sisa_tagihan = total - total_dibayar`
    - Set status berdasarkan sisa:
        - `sisa_tagihan <= 0` → status = "lunas"
        - `sisa_tagihan > 0` → status = "dibayar_sebagian"
    - Simpan ke `invoice_payment_history` table

2. **Frontend:** Update `confirmPayment()` untuk:
    - Kirim `jumlah_bayar` ke endpoint baru
    - Handle response untuk partial payment
    - Keep modal open jika belum lunas
    - Reset form untuk cicilan berikutnya

**Files Modified:**

-   `app/Http/Controllers/SalesManagementController.php` (added 3 new methods)
-   `resources/views/admin/penjualan/invoice/index.blade.php` (updated confirmPayment function)
-   `routes/web.php` (added 2 new routes)

---

## Implementation Details

### New Backend Methods

#### 1. processInvoicePayment()

```php
public function processInvoicePayment(Request $request)
{
    // Validates payment amount
    // Checks if amount exceeds remaining balance
    // Saves to invoice_payment_history
    // Updates total_dibayar and sisa_tagihan
    // Sets status based on remaining balance
    // Returns updated invoice data
}
```

#### 2. getPaymentHistory($invoiceId)

```php
public function getPaymentHistory($invoiceId)
{
    // Loads all payments for an invoice
    // Returns payment history with bukti URLs
    // Returns invoice summary (total, paid, remaining)
}
```

#### 3. compressAndSaveBukti($file, $invoiceNumber)

```php
private function compressAndSaveBukti($file, $invoiceNumber)
{
    // Compresses images to max 1200x1200, 80% quality
    // Saves to storage/app/public/bukti_pembayaran/
    // Returns storage path
}
```

### New Routes

```php
// Process payment (installment or full)
POST /penjualan/invoice/payment
Route: penjualan.invoice.payment.process

// Get payment history
GET /penjualan/invoice/{id}/payment-history
Route: penjualan.invoice.payment.history
```

### Frontend Changes

#### Updated confirmPayment() Function

-   Changed from setting status="lunas" to sending jumlah_bayar
-   Added validation for jumlah_bayar > 0
-   Added validation for bukti_pembayaran required
-   Changed endpoint from `update-status` to `payment.process`
-   Added logic to keep modal open for partial payments
-   Added logic to reset form for next installment
-   Added logic to close modal only when fully paid

---

## Status Logic

### Old Logic (Buggy)

```
menunggu → [Pay Any Amount] → lunas ❌
```

### New Logic (Fixed)

```
menunggu → [Pay Full] → lunas ✅
menunggu → [Pay Partial] → dibayar_sebagian ✅
dibayar_sebagian → [Pay Remaining] → lunas ✅
dibayar_sebagian → [Pay Partial Again] → dibayar_sebagian ✅
```

---

## Testing Scenarios

### Test 1: Cash Payment with Bukti ✅

1. Open payment modal
2. Select "Cash"
3. Verify bukti pembayaran field is visible
4. Upload bukti
5. Submit payment
6. Verify payment recorded

### Test 2: Partial Payment Status ✅

1. Invoice total: Rp 1,000,000
2. Pay Rp 500,000 (50%)
3. Verify status = "dibayar_sebagian"
4. Verify total_dibayar = 500,000
5. Verify sisa_tagihan = 500,000

### Test 3: Multiple Installments ✅

1. Invoice total: Rp 1,000,000
2. Pay Rp 300,000 → status = "dibayar_sebagian"
3. Pay Rp 400,000 → status = "dibayar_sebagian"
4. Pay Rp 300,000 → status = "lunas"
5. Verify total_dibayar = 1,000,000
6. Verify sisa_tagihan = 0

### Test 4: Full Payment ✅

1. Invoice total: Rp 1,000,000
2. Pay Rp 1,000,000 (100%)
3. Verify status = "lunas"
4. Verify modal closes automatically

---

## Database Changes

### Fields Used

-   `sales_invoice.total_dibayar` - Total amount paid so far
-   `sales_invoice.sisa_tagihan` - Remaining balance
-   `sales_invoice.status` - Invoice status (menunggu, dibayar_sebagian, lunas)

### New Table

-   `invoice_payment_history` - Tracks all payments with individual bukti

---

## Files Modified Summary

1. **resources/views/admin/penjualan/invoice/index.blade.php**

    - Moved bukti pembayaran field outside transfer section
    - Updated confirmPayment() function
    - Added support for partial payments

2. **app/Http/Controllers/SalesManagementController.php**

    - Added processInvoicePayment() method
    - Added getPaymentHistory() method
    - Added compressAndSaveBukti() method
    - Added InvoicePaymentHistory import

3. **routes/web.php**
    - Added POST /penjualan/invoice/payment
    - Added GET /penjualan/invoice/{id}/payment-history

---

## Status: ✅ FIXED

Both bugs have been fixed and tested. The invoice payment system now:

-   ✅ Shows bukti pembayaran field for both Cash and Transfer
-   ✅ Correctly sets status based on payment amount
-   ✅ Supports installment payments
-   ✅ Tracks payment history
-   ✅ Compresses uploaded images automatically

---

**Fixed Date:** November 21, 2025
**Files Modified:** 3 files
**New Methods Added:** 3 methods
**New Routes Added:** 2 routes
