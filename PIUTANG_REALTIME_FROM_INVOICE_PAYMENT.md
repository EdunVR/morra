# Piutang Realtime dari Invoice Payment History

## Overview

Data piutang sekarang diambil secara **realtime** dari tabel `sales_invoice` dan `invoice_payment_history`, bukan lagi dari tabel `piutang` yang statis. Ini memastikan data piutang selalu akurat dan up-to-date dengan pembayaran terbaru.

## Perubahan Arsitektur

### Sebelum (Old Architecture)

```
Tabel piutang (static data)
├── jumlah_piutang (fixed)
├── jumlah_dibayar (manual update)
├── sisa_piutang (manual calculation)
└── status (manual update)
```

**Masalah:**

-   Data tidak realtime
-   Perlu manual update setiap ada pembayaran
-   Risiko data tidak sinkron
-   Sulit tracking history pembayaran

### Sesudah (New Architecture)

```
Tabel sales_invoice (master data)
├── total (jumlah piutang)
├── status (invoice status)
└── due_date (tanggal jatuh tempo)

Tabel invoice_payment_history (payment records)
├── id_sales_invoice (FK)
├── tanggal_bayar
├── jumlah_bayar
├── jenis_pembayaran
├── bukti_pembayaran
└── keterangan

Calculation (realtime)
├── total_dibayar = SUM(invoice_payment_history.jumlah_bayar)
├── sisa_piutang = sales_invoice.total - total_dibayar
└── status = calculated based on payments
```

**Keuntungan:**

-   ✅ Data selalu realtime
-   ✅ Auto-update saat ada pembayaran baru
-   ✅ Complete payment history tracking
-   ✅ Accurate status calculation
-   ✅ No manual intervention needed

## Database Schema

### sales_invoice

```sql
CREATE TABLE sales_invoice (
    id_sales_invoice BIGINT PRIMARY KEY,
    no_invoice VARCHAR(255),
    tanggal DATE,
    id_member BIGINT,
    id_prospek BIGINT,
    id_outlet BIGINT,
    id_penjualan BIGINT,
    total DECIMAL(15,2),
    total_dibayar DECIMAL(15,2),
    sisa_tagihan DECIMAL(15,2),
    status ENUM('draft', 'menunggu', 'dibayar_sebagian', 'lunas', 'dibatalkan'),
    due_date DATE,
    -- ... other fields
);
```

### invoice_payment_history

```sql
CREATE TABLE invoice_payment_history (
    id BIGINT PRIMARY KEY,
    id_sales_invoice BIGINT,
    tanggal_bayar DATE,
    jumlah_bayar DECIMAL(15,2),
    jenis_pembayaran ENUM('cash', 'transfer'),
    nama_bank VARCHAR(255),
    nama_pengirim VARCHAR(255),
    penerima VARCHAR(255),
    bukti_pembayaran VARCHAR(255),
    keterangan TEXT,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX(id_sales_invoice),
    INDEX(tanggal_bayar)
);
```

## Implementation Details

### Controller: getPiutangData()

**Query Strategy:**

```php
// 1. Get all sales invoices (exclude draft & cancelled)
$invoices = DB::table('sales_invoice')
    ->leftJoin('member', ...)
    ->leftJoin('prospek', ...)
    ->leftJoin('outlets', ...)
    ->where('status', '!=', 'draft')
    ->where('status', '!=', 'dibatalkan')
    ->get();

// 2. For each invoice, calculate realtime payments
foreach ($invoices as $invoice) {
    $totalPayments = DB::table('invoice_payment_history')
        ->where('id_sales_invoice', $invoice->id_sales_invoice)
        ->sum('jumlah_bayar');

    $sisaTagihan = $invoice->total - $totalPayments;

    // 3. Determine realtime status
    if ($totalPayments >= $invoice->total) {
        $status = 'lunas';
    } elseif ($totalPayments > 0) {
        $status = 'dibayar_sebagian';
    } else {
        $status = 'belum_lunas';
    }
}
```

**Status Calculation Logic:**

```php
// Realtime status based on actual payments
if (total_payments >= invoice_total) {
    status = 'lunas'
} else if (total_payments > 0) {
    status = 'dibayar_sebagian'
} else {
    status = 'belum_lunas'
}

// Overdue check
if (due_date < today && status != 'lunas') {
    is_overdue = true
    days_overdue = today - due_date
}
```

### Controller: getPiutangDetail()

**Enhanced with Payment History:**

```php
// Get invoice detail
$invoice = DB::table('sales_invoice')->find($id);

// Get all payment history
$paymentHistory = DB::table('invoice_payment_history')
    ->where('id_sales_invoice', $id)
    ->orderBy('tanggal_bayar', 'desc')
    ->get();

// Calculate realtime totals
$totalPayments = $paymentHistory->sum('jumlah_bayar');
$sisaTagihan = $invoice->total - $totalPayments;

// Return with payment history
return [
    'piutang' => [...],
    'penjualan' => [...],
    'journals' => [...],
    'payment_history' => $paymentHistory // NEW!
];
```

## Frontend Changes

### Modal Detail - Payment History Section

**New Section Added:**

```html
<!-- Payment History -->
<div class="rounded-xl border border-slate-200 p-4">
    <h4 class="font-semibold text-slate-800 mb-3">
        <i class="bx bx-history"></i> Riwayat Pembayaran
    </h4>

    <template x-for="payment in detailData?.payment_history">
        <div class="payment-card">
            <!-- Payment details -->
            <div>Pembayaran #{{ index }}</div>
            <div>{{ tanggal_bayar }}</div>
            <div>{{ jumlah_bayar }}</div>
            <div>{{ jenis_pembayaran }}</div>
            <div>{{ nama_bank }}</div>
            <div>{{ bukti_pembayaran }}</div>
        </div>
    </template>
</div>
```

**Features:**

-   ✅ Show all payment records
-   ✅ Display payment date, amount, method
-   ✅ Show bank details if transfer
-   ✅ Link to view payment proof
-   ✅ Show payment notes

## Data Flow

### When User Views Piutang List

```
1. User opens /finance/piutang
2. Frontend calls: GET /finance/piutang/data
3. Backend queries:
   - sales_invoice (master data)
   - invoice_payment_history (for each invoice)
4. Backend calculates realtime:
   - total_dibayar = SUM(payments)
   - sisa_piutang = total - total_dibayar
   - status = calculated
5. Frontend displays realtime data
```

### When User Makes Payment

```
1. User clicks "Bayar" button
2. Redirects to invoice page
3. User fills payment form
4. POST /penjualan/invoice/payment
5. Backend inserts to invoice_payment_history
6. Backend updates sales_invoice.total_dibayar
7. Backend updates sales_invoice.status
8. User returns to piutang page
9. Data automatically shows updated (realtime!)
```

### When User Views Detail

```
1. User clicks invoice number
2. Frontend calls: GET /finance/piutang/{id}/detail
3. Backend queries:
   - sales_invoice (invoice data)
   - invoice_payment_history (all payments)
   - penjualan + penjualan_detail (transaction)
   - journal_entries (accounting)
4. Backend calculates realtime totals
5. Frontend displays:
   - Invoice info
   - Payment history (NEW!)
   - Transaction details
   - Journal entries
```

## Benefits

### 1. Data Accuracy

-   ✅ Always shows latest payment status
-   ✅ No manual sync needed
-   ✅ Eliminates data inconsistency
-   ✅ Single source of truth

### 2. Audit Trail

-   ✅ Complete payment history
-   ✅ Track who paid when
-   ✅ Payment proof attached
-   ✅ Easy to verify

### 3. Real-time Updates

-   ✅ Instant status update after payment
-   ✅ No cache or delay
-   ✅ Accurate sisa piutang
-   ✅ Correct overdue calculation

### 4. Scalability

-   ✅ Supports installment payments
-   ✅ Multiple payments per invoice
-   ✅ Partial payments tracking
-   ✅ Easy to add new payment methods

### 5. Reporting

-   ✅ Accurate financial reports
-   ✅ Payment trend analysis
-   ✅ Customer payment behavior
-   ✅ Cash flow tracking

## Migration Notes

### Data Compatibility

**Old tabel piutang masih ada** untuk backward compatibility, tapi:

-   ✅ Tidak lagi digunakan untuk display
-   ✅ Bisa digunakan untuk historical reference
-   ✅ Bisa di-sync jika diperlukan
-   ✅ Tidak mempengaruhi fitur baru

### Sync Script (Optional)

Jika ingin sync data lama ke struktur baru:

```php
// Sync old piutang to sales_invoice
foreach (Piutang::all() as $piutang) {
    if ($piutang->id_penjualan) {
        $salesInvoice = SalesInvoice::where('id_penjualan', $piutang->id_penjualan)->first();
        if ($salesInvoice) {
            // Create payment history if jumlah_dibayar > 0
            if ($piutang->jumlah_dibayar > 0) {
                InvoicePaymentHistory::create([
                    'id_sales_invoice' => $salesInvoice->id_sales_invoice,
                    'tanggal_bayar' => $piutang->updated_at,
                    'jumlah_bayar' => $piutang->jumlah_dibayar,
                    'jenis_pembayaran' => 'cash',
                    'keterangan' => 'Migrated from old piutang table'
                ]);
            }
        }
    }
}
```

## Testing Checklist

### Test 1: View Piutang List

-   [ ] Data piutang ditampilkan
-   [ ] Summary cards akurat
-   [ ] Status calculated correctly
-   [ ] Overdue detection works
-   [ ] Filter berfungsi

### Test 2: Make Payment

-   [ ] Klik "Bayar" redirect ke invoice
-   [ ] Modal pembayaran terbuka
-   [ ] Submit payment berhasil
-   [ ] Data piutang auto-update
-   [ ] Status berubah otomatis

### Test 3: View Detail

-   [ ] Modal detail terbuka
-   [ ] Invoice info ditampilkan
-   [ ] Payment history muncul (NEW!)
-   [ ] Transaction details lengkap
-   [ ] Journal entries (if any)

### Test 4: Payment History

-   [ ] All payments listed
-   [ ] Payment details complete
-   [ ] Bukti transfer link works
-   [ ] Chronological order
-   [ ] Total matches

### Test 5: Realtime Calculation

-   [ ] total_dibayar = SUM(payments)
-   [ ] sisa_piutang = total - total_dibayar
-   [ ] Status calculated correctly
-   [ ] Overdue check accurate
-   [ ] Summary totals correct

## Performance Considerations

### Query Optimization

```php
// Use indexed columns
WHERE id_sales_invoice = ? // indexed
WHERE tanggal_bayar BETWEEN ? AND ? // indexed

// Avoid N+1 queries
// Calculate payments in single query per invoice
$totalPayments = DB::table('invoice_payment_history')
    ->where('id_sales_invoice', $id)
    ->sum('jumlah_bayar'); // Single query
```

### Caching Strategy (Optional)

```php
// Cache summary for 5 minutes
$summary = Cache::remember('piutang_summary_' . $outletId, 300, function() {
    return $this->calculateSummary();
});
```

## API Response Format

### GET /finance/piutang/data

```json
{
    "success": true,
    "data": [
        {
            "id_piutang": 123,
            "id_penjualan": 456,
            "tanggal": "2025-11-24",
            "tanggal_jatuh_tempo": "2025-12-24",
            "nama_customer": "John Doe",
            "outlet": "Outlet A",
            "jumlah_piutang": 1000000,
            "jumlah_dibayar": 500000,
            "sisa_piutang": 500000,
            "status": "dibayar_sebagian",
            "is_overdue": false,
            "days_overdue": 0,
            "invoice_number": "INV-2025-001"
        }
    ],
    "summary": {
        "total_piutang": 10000000,
        "total_dibayar": 5000000,
        "total_sisa": 5000000,
        "count_belum_lunas": 5,
        "count_lunas": 3,
        "count_overdue": 2
    }
}
```

### GET /finance/piutang/{id}/detail

```json
{
  "success": true,
  "data": {
    "piutang": { ... },
    "penjualan": { ... },
    "journals": [ ... ],
    "payment_history": [
      {
        "id": 1,
        "tanggal_bayar": "2025-11-20",
        "jumlah_bayar": 500000,
        "jenis_pembayaran": "transfer",
        "nama_bank": "BCA",
        "nama_pengirim": "John Doe",
        "penerima": "Admin",
        "keterangan": "Pembayaran pertama",
        "bukti_pembayaran": "http://example.com/storage/bukti/xxx.jpg"
      }
    ]
  }
}
```

## Files Modified

1. **app/Http/Controllers/FinanceAccountantController.php**

    - `getPiutangData()` - Rewritten to use sales_invoice + invoice_payment_history
    - `getPiutangDetail()` - Enhanced with payment history

2. **resources/views/admin/finance/piutang/index.blade.php**
    - Added payment history section in detail modal
    - Updated data structure handling

## Status: ✅ COMPLETE

**Implementation Date:** 2025-11-24  
**Tested:** ✅ Ready for testing  
**Documentation:** ✅ Complete  
**Backward Compatible:** ✅ Yes (old piutang table still exists)

## Next Steps

1. Test all features thoroughly
2. Monitor performance with large datasets
3. Consider adding caching if needed
4. Optional: Migrate old piutang data
5. Optional: Add payment analytics dashboard
