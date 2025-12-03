# Invoice Payment Installment Feature

## Requirements Summary

### 1. Payment Proof for Both Cash and Transfer

-   **Current**: Bukti pembayaran hanya untuk transfer
-   **New**: Bukti pembayaran wajib untuk cash DAN transfer
-   Bukti harus di-compress sebelum upload (untuk menghemat storage)

### 2. Installment Payment Support

-   **Current**: Pembayaran harus lunas penuh
-   **New**: Pembayaran bisa dicicil beberapa kali
-   Setiap cicilan dapat melampirkan bukti pembayaran
-   Invoice baru bisa ke tahap berikutnya setelah total cicilan = total tagihan

## Implementation Plan

### Phase 1: Add Payment Proof Compression for Cash

**Files to modify:**

-   `resources/views/admin/penjualan/invoice/index.blade.php`

**Changes needed:**

1. Remove conditional logic that hides bukti transfer for cash
2. Make bukti pembayaran field always visible
3. Add image compression function (similar to vendor bill)
4. Apply compression for both cash and transfer payments

### Phase 2: Create Payment History Table

**New migration needed:**

```sql
CREATE TABLE invoice_payment_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_sales_invoice BIGINT UNSIGNED NOT NULL,
    tanggal_bayar DATE NOT NULL,
    jumlah_bayar DECIMAL(15,2) NOT NULL,
    jenis_pembayaran ENUM('cash', 'transfer') NOT NULL,
    nama_bank VARCHAR(255) NULL,
    nama_pengirim VARCHAR(255) NULL,
    bukti_pembayaran VARCHAR(255) NULL,
    keterangan TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_sales_invoice) REFERENCES sales_invoices(id_sales_invoice) ON DELETE CASCADE
);
```

### Phase 3: Modify Payment Logic

**Files to modify:**

-   `app/Http/Controllers/SalesManagementController.php`
-   `app/Models/SalesInvoice.php`

**Changes needed:**

1. **Add new fields to SalesInvoice:**

    - `total_dibayar` (total amount paid so far)
    - `sisa_tagihan` (remaining balance)

2. **Create InvoicePaymentHistory model:**

```php
class InvoicePaymentHistory extends Model
{
    protected $table = 'invoice_payment_history';
    protected $fillable = [
        'id_sales_invoice',
        'tanggal_bayar',
        'jumlah_bayar',
        'jenis_pembayaran',
        'nama_bank',
        'nama_pengirim',
        'bukti_pembayaran',
        'keterangan',
        'created_by'
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'id_sales_invoice');
    }
}
```

3. **Update payment processing:**

```php
public function processPayment(Request $request)
{
    $invoice = SalesInvoice::findOrFail($request->invoice_id);

    // Validate payment amount
    $sisaTagihan = $invoice->total - $invoice->total_dibayar;
    if ($request->jumlah_bayar > $sisaTagihan) {
        return response()->json([
            'success' => false,
            'message' => 'Jumlah bayar melebihi sisa tagihan'
        ]);
    }

    // Compress bukti pembayaran if exists
    $buktiPath = null;
    if ($request->hasFile('bukti_pembayaran')) {
        $buktiPath = $this->compressAndSaveBukti($request->file('bukti_pembayaran'));
    }

    // Save payment history
    InvoicePaymentHistory::create([
        'id_sales_invoice' => $invoice->id_sales_invoice,
        'tanggal_bayar' => $request->tanggal_bayar,
        'jumlah_bayar' => $request->jumlah_bayar,
        'jenis_pembayaran' => $request->jenis_pembayaran,
        'nama_bank' => $request->nama_bank,
        'nama_pengirim' => $request->nama_pengirim,
        'bukti_pembayaran' => $buktiPath,
        'keterangan' => $request->keterangan,
        'created_by' => auth()->id()
    ]);

    // Update invoice
    $invoice->total_dibayar += $request->jumlah_bayar;
    $invoice->sisa_tagihan = $invoice->total - $invoice->total_dibayar;

    // Check if fully paid
    if ($invoice->sisa_tagihan <= 0) {
        $invoice->status = 'lunas';
        // Update piutang status
        $invoice->piutang()->update(['status' => 'lunas']);
    } else {
        $invoice->status = 'dibayar_sebagian';
    }

    $invoice->save();

    return response()->json([
        'success' => true,
        'message' => 'Pembayaran berhasil dicatat',
        'data' => [
            'total_dibayar' => $invoice->total_dibayar,
            'sisa_tagihan' => $invoice->sisa_tagihan
        ]
    ]);
}
```

### Phase 4: Update Frontend UI

**Files to modify:**

-   `resources/views/admin/penjualan/invoice/index.blade.php`

**UI Changes needed:**

1. **Payment Modal Updates:**

    - Always show bukti pembayaran field (for both cash and transfer)
    - Show payment history table
    - Show total paid and remaining balance
    - Allow partial payment

2. **Payment History Display:**

```html
<div class="payment-history">
    <h4>Riwayat Pembayaran</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Metode</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="payment in paymentHistory">
                <tr>
                    <td x-text="payment.tanggal_bayar"></td>
                    <td x-text="formatCurrency(payment.jumlah_bayar)"></td>
                    <td x-text="payment.jenis_pembayaran"></td>
                    <td>
                        <button @click="viewBukti(payment.bukti_pembayaran)">
                            Lihat
                        </button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <div class="payment-summary">
        <div>
            Total Invoice: <span x-text="formatCurrency(invoice.total)"></span>
        </div>
        <div>
            Total Dibayar:
            <span x-text="formatCurrency(invoice.total_dibayar)"></span>
        </div>
        <div>
            Sisa Tagihan:
            <span x-text="formatCurrency(invoice.sisa_tagihan)"></span>
        </div>
    </div>
</div>
```

3. **Add Image Compression Function:**

```javascript
async compressBuktiPembayaran(file) {
    if (!file.type.startsWith('image/')) {
        return file; // Return as-is for non-images
    }

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Max dimensions
                const maxWidth = 1200;
                const maxHeight = 1200;

                if (width > maxWidth || height > maxHeight) {
                    const ratio = Math.min(maxWidth / width, maxHeight / height);
                    width = width * ratio;
                    height = height * ratio;
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    const compressedFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    resolve(compressedFile);
                }, 'image/jpeg', 0.8);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}
```

### Phase 5: Update Status Logic

**New invoice statuses:**

-   `menunggu` - Waiting for payment
-   `dibayar_sebagian` - Partially paid
-   `lunas` - Fully paid
-   `dikirim` - Shipped (only after fully paid)
-   `selesai` - Completed

**Status transition rules:**

```
menunggu → dibayar_sebagian (when first payment < total)
menunggu → lunas (when first payment = total)
dibayar_sebagian → lunas (when total payments = total)
lunas → dikirim (manual action)
dikirim → selesai (manual action)
```

## Database Migration

```php
// Migration file: add_installment_fields_to_sales_invoices_table.php
public function up()
{
    Schema::table('sales_invoices', function (Blueprint $table) {
        $table->decimal('total_dibayar', 15, 2)->default(0)->after('total');
        $table->decimal('sisa_tagihan', 15, 2)->default(0)->after('total_dibayar');
    });

    // Update existing invoices
    DB::statement('UPDATE sales_invoices SET sisa_tagihan = total WHERE sisa_tagihan = 0');
}

// Migration file: create_invoice_payment_history_table.php
public function up()
{
    Schema::create('invoice_payment_history', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_sales_invoice');
        $table->date('tanggal_bayar');
        $table->decimal('jumlah_bayar', 15, 2);
        $table->enum('jenis_pembayaran', ['cash', 'transfer']);
        $table->string('nama_bank')->nullable();
        $table->string('nama_pengirim')->nullable();
        $table->string('bukti_pembayaran')->nullable();
        $table->text('keterangan')->nullable();
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();

        $table->foreign('id_sales_invoice')
              ->references('id_sales_invoice')
              ->on('sales_invoices')
              ->onDelete('cascade');
    });
}
```

## Testing Checklist

-   [ ] Upload bukti pembayaran untuk cash - compressed
-   [ ] Upload bukti pembayaran untuk transfer - compressed
-   [ ] Bayar cicilan pertama (partial)
-   [ ] Bayar cicilan kedua (partial)
-   [ ] Bayar cicilan terakhir (lunas)
-   [ ] View payment history
-   [ ] View bukti pembayaran dari history
-   [ ] Status invoice berubah sesuai pembayaran
-   [ ] Tidak bisa bayar melebihi sisa tagihan
-   [ ] Tidak bisa lanjut ke tahap berikutnya sebelum lunas

## Benefits

1. **Better Payment Tracking**: Riwayat pembayaran lengkap dengan bukti
2. **Flexibility**: Customer bisa bayar cicilan sesuai kemampuan
3. **Storage Efficiency**: Bukti pembayaran di-compress otomatis
4. **Audit Trail**: Semua pembayaran tercatat dengan timestamp dan user
5. **Cash Accountability**: Bukti pembayaran cash untuk transparansi

## Implementation Priority

1. **High Priority**: Payment history table and installment logic
2. **High Priority**: Image compression for payment proofs
3. **Medium Priority**: UI updates for payment history display
4. **Low Priority**: Advanced reporting for payment analytics

## Notes

-   Existing invoices will need data migration for new fields
-   Consider adding payment reminder notifications
-   Consider adding payment receipt generation
-   May need to update accounting journal entries for partial payments
