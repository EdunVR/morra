# Design Document: Purchase Order Installment Payment

## Overview

Implementasi sistem pembayaran cicilan untuk Purchase Order mengikuti pattern yang sama dengan Sales Invoice untuk konsistensi dan maintainability.

## Architecture

### Database Schema

#### 1. Add Columns to `purchase_order` Table

```sql
ALTER TABLE purchase_order ADD COLUMN total_dibayar DECIMAL(15,2) DEFAULT 0;
ALTER TABLE purchase_order ADD COLUMN sisa_pembayaran DECIMAL(15,2);
-- Update existing: sisa_pembayaran = total_amount - total_dibayar
```

#### 2. Create `po_payment_history` Table

```sql
CREATE TABLE po_payment_history (
    id_payment BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_purchase_order BIGINT UNSIGNED NOT NULL,
    tanggal_pembayaran DATE NOT NULL,
    jumlah_pembayaran DECIMAL(15,2) NOT NULL,
    jenis_pembayaran ENUM('cash', 'transfer') NOT NULL,
    bukti_pembayaran VARCHAR(255),
    penerima VARCHAR(100),
    catatan TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_purchase_order) REFERENCES purchase_order(id)
);
```

### Status Flow

```
[Create PO] → status: 'pending'
     ↓
[First Payment] → status: 'partial' (if not full)
     ↓
[Full Payment] → status: 'paid'
```

**Status Values:**

-   `pending` - Belum ada pembayaran
-   `partial` - Sudah dibayar sebagian
-   `paid` - Sudah lunas
-   `cancelled` - Dibatalkan

## Components and Interfaces

### Backend Components

#### 1. Migration

**File:** `database/migrations/2025_11_21_180000_add_installment_to_purchase_order.php`

-   Add `total_dibayar` and `sisa_pembayaran` columns
-   Update existing records

#### 2. Migration

**File:** `database/migrations/2025_11_21_180001_create_po_payment_history_table.php`

-   Create payment history table

#### 3. Model

**File:** `app/Models/POPaymentHistory.php`

-   Eloquent model for payment history
-   Relationships with PurchaseOrder

#### 4. Controller Methods

**File:** `app/Http/Controllers/PurchaseOrderController.php`

**New Methods:**

-   `processPayment(Request $request)` - Process payment
-   `getPaymentHistory($id)` - Get payment history for PO
-   `downloadBuktiTransfer($id)` - Download payment proof

**Updated Methods:**

-   `store()` - Initialize payment fields
-   `update()` - Recalculate sisa_pembayaran

#### 5. Routes

**File:** `routes/web.php`

```php
Route::post('purchase-order/payment', [PurchaseOrderController::class, 'processPayment']);
Route::get('purchase-order/{id}/payment-history', [PurchaseOrderController::class, 'getPaymentHistory']);
Route::get('purchase-order/{id}/download-bukti', [PurchaseOrderController::class, 'downloadBuktiTransfer']);
```

### Frontend Components

#### 1. Payment Modal

**Location:** `resources/views/admin/pembelian/purchase-order/index.blade.php`

**Features:**

-   Display PO information
-   Show total, paid, remaining
-   Input payment amount
-   Select payment type (Cash/Transfer)
-   Upload payment proof (for Transfer)
-   Preview uploaded file
-   Submit payment

#### 2. Payment History Modal

**Location:** Same file

**Features:**

-   List all payments
-   Show date, amount, type, receiver
-   View payment proof button
-   Total paid summary

#### 3. Bukti Modal

**Location:** Same file

**Features:**

-   Display payment proof (image/PDF)
-   Download button
-   Close button

#### 4. Action Buttons

**Updates:**

-   Show "Bayar" button for pending/partial status
-   Show "Lihat Pembayaran" button if has payments
-   Update status badges

## Data Models

### PurchaseOrder Model Updates

```php
class PurchaseOrder extends Model
{
    protected $fillable = [
        // ... existing fields
        'total_dibayar',
        'sisa_pembayaran',
    ];

    protected $casts = [
        'total_dibayar' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
    ];

    public function paymentHistory()
    {
        return $this->hasMany(POPaymentHistory::class, 'id_purchase_order');
    }
}
```

### POPaymentHistory Model

```php
class POPaymentHistory extends Model
{
    protected $table = 'po_payment_history';
    protected $primaryKey = 'id_payment';

    protected $fillable = [
        'id_purchase_order',
        'tanggal_pembayaran',
        'jumlah_pembayaran',
        'jenis_pembayaran',
        'bukti_pembayaran',
        'penerima',
        'catatan',
    ];

    protected $casts = [
        'jumlah_pembayaran' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_purchase_order');
    }
}
```

## Error Handling

### Validation Rules

```php
$rules = [
    'po_id' => 'required|exists:purchase_order,id',
    'jumlah_pembayaran' => 'required|numeric|min:0.01',
    'jenis_pembayaran' => 'required|in:cash,transfer',
    'tanggal_pembayaran' => 'required|date',
    'penerima' => 'required|string|max:100',
    'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
];
```

### Error Scenarios

1. **Overpayment:** Jumlah > sisa_pembayaran
2. **Invalid File:** Format tidak didukung
3. **File Too Large:** > 5MB
4. **PO Not Found:** ID tidak valid
5. **Already Paid:** Status sudah 'paid'

## Testing Strategy

### Unit Tests

-   Payment calculation logic
-   Status update logic
-   File upload validation
-   Image compression

### Integration Tests

-   Complete payment flow
-   Hutang update
-   Journal entry creation
-   Multiple payments

### UI Tests

-   Modal interactions
-   File upload
-   Form validation
-   Status display

## Implementation Plan

### Phase 1: Database & Models

1. Create migrations
2. Run migrations
3. Create POPaymentHistory model
4. Update PurchaseOrder model

### Phase 2: Backend Logic

1. Add processPayment method
2. Add getPaymentHistory method
3. Add downloadBuktiTransfer method
4. Add routes
5. Update store/update methods

### Phase 3: Frontend UI

1. Add payment modal
2. Add payment history modal
3. Add bukti modal
4. Update action buttons
5. Update status badges

### Phase 4: Testing & Polish

1. Test payment flow
2. Test file upload
3. Test status updates
4. Test hutang integration
5. Fix bugs and polish UI

## Security Considerations

1. **File Upload:** Validate file type and size
2. **Authorization:** Check user permissions
3. **SQL Injection:** Use parameterized queries
4. **XSS:** Sanitize user inputs
5. **CSRF:** Use CSRF tokens

## Performance Optimizations

1. **Image Compression:** Compress uploaded images
2. **Lazy Loading:** Load payment history on demand
3. **Caching:** Cache PO data
4. **Indexing:** Add indexes on foreign keys
5. **Pagination:** Paginate payment history if many records

## Maintenance & Monitoring

1. **Logging:** Log all payment transactions
2. **Error Tracking:** Track payment errors
3. **Audit Trail:** Record who made payments
4. **Backup:** Regular database backups
5. **Monitoring:** Monitor payment success rate
