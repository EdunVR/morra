# Invoice Installment Payment - Implementation Progress

## ✅ Completed Steps

### 1. Database Migrations (DONE)

-   ✅ Created migration for `sales_invoices` table
    -   Added `total_dibayar` field (total amount paid)
    -   Added `sisa_tagihan` field (remaining balance)
    -   Auto-update existing invoices
-   ✅ Created `invoice_payment_history` table
    -   Stores all payment records (installments)
    -   Supports both cash and transfer
    -   Includes bukti_pembayaran field for compressed images
    -   No foreign key constraints (as requested)
-   ✅ Migrations executed successfully

### 2. Models Created (DONE)

-   ✅ Created `InvoicePaymentHistory` model
    -   Relationships to SalesInvoice and User
    -   Proper casts for dates and decimals
    -   Helper method for bukti URL

## 🔄 Next Steps

### 3. Update SalesInvoice Model

Add relationship and helper methods:

```php
// In app/Models/SalesInvoice.php
public function paymentHistory()
{
    return $this->hasMany(InvoicePaymentHistory::class, 'id_sales_invoice', 'id_sales_invoice')
                ->orderBy('tanggal_bayar', 'desc');
}

public function getTotalDibayarAttribute()
{
    return $this->paymentHistory()->sum('jumlah_bayar');
}

public function getSisaTagihanAttribute()
{
    return $this->total - $this->total_dibayar;
}
```

### 4. Controller Methods

Add to `SalesManagementController.php`:

**a. Process Payment (Installment or Full)**

```php
public function processInvoicePayment(Request $request)
{
    // Validate
    // Compress bukti if image
    // Save payment history
    // Update invoice totals
    // Check if fully paid
    // Update status
}
```

**b. Get Payment History**

```php
public function getPaymentHistory($invoiceId)
{
    // Return payment history with bukti URLs
}
```

**c. Compress and Save Bukti**

```php
private function compressAndSaveBukti($file)
{
    // Use Intervention Image or GD
    // Compress to max 1200x1200, 80% quality
    // Save to storage/app/public/bukti_pembayaran
    // Return path
}
```

### 5. Routes

Add to `routes/web.php`:

```php
Route::post('invoice/payment', [SalesManagementController::class, 'processInvoicePayment'])
     ->name('invoice.payment.process');
Route::get('invoice/{id}/payment-history', [SalesManagementController::class, 'getPaymentHistory'])
     ->name('invoice.payment.history');
```

### 6. Frontend Updates

**a. Payment Modal UI**

-   Show total invoice, total paid, remaining balance
-   Allow partial or full payment
-   Always show bukti pembayaran field (for both cash & transfer)
-   Add image compression before upload
-   Show payment history table

**b. Image Compression Function**

```javascript
async compressBuktiPembayaran(file) {
    // Similar to vendor bill compression
    // Max 1200x1200, 80% quality
    // Return compressed file
}
```

**c. Payment History Display**

```html
<div class="payment-history">
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Metode</th>
            <th>Bukti</th>
        </tr>
        <!-- Loop payment history -->
    </table>
</div>
```

### 7. Status Logic

Update invoice status based on payment:

-   `menunggu` → `dibayar_sebagian` (if partial)
-   `menunggu` → `lunas` (if full payment)
-   `dibayar_sebagian` → `lunas` (when fully paid)

### 8. Testing

-   [ ] Upload bukti for cash payment - compressed
-   [ ] Upload bukti for transfer payment - compressed
-   [ ] Pay partial amount (installment)
-   [ ] Pay remaining amount (complete)
-   [ ] Pay full amount at once (direct lunas)
-   [ ] View payment history
-   [ ] Validate cannot overpay
-   [ ] Status updates correctly

## Key Features

### Flexible Payment

-   ✅ Support installment payments (cicilan)
-   ✅ Support full payment (lunas langsung)
-   ✅ Automatic status updates

### Payment Proof

-   ✅ Required for both cash AND transfer
-   ✅ Automatic image compression
-   ✅ Storage optimization

### Payment Tracking

-   ✅ Complete payment history
-   ✅ Each payment with its own bukti
-   ✅ Audit trail with timestamps

## Files Modified/Created

### Created:

1. `database/migrations/2025_11_21_140000_add_installment_fields_to_sales_invoices_table.php`
2. `database/migrations/2025_11_21_140001_create_invoice_payment_history_table.php`
3. `app/Models/InvoicePaymentHistory.php`

### To Modify:

1. `app/Models/SalesInvoice.php` - Add relationships
2. `app/Http/Controllers/SalesManagementController.php` - Add payment methods
3. `resources/views/admin/penjualan/invoice/index.blade.php` - Update UI
4. `routes/web.php` - Add routes

## Notes

-   Foreign key constraints removed as requested
-   Image compression similar to vendor bill implementation
-   Both installment and full payment supported
-   Payment history tracked for audit purposes
