# Purchase Order Print Invoice Improvements

## Perubahan yang Dilakukan

### 1. Status 'Partial' Menggunakan Format Invoice

Status "Dibayar Sebagian" (partial) sekarang menggunakan format print yang sama dengan Vendor Bill (invoice format).

### 2. Menambahkan Informasi Bank Supplier

Menampilkan informasi pembayaran supplier di print invoice:

-   Nama Bank
-   Nomor Rekening
-   Atas Nama

## File yang Dimodifikasi

### Backend (Controller)

**File:** `app/Http/Controllers/PurchaseManagementController.php`

#### Method `printDocument` (Line 1116-1131)

**Sebelumnya:**

```php
elseif ($purchaseOrder->status === 'vendor_bill' || $purchaseOrder->status === 'payment') {
    // Invoice - layout invoice
    $view = 'admin.pembelian.purchase-order.print-invoice';
    $printNumber = $purchaseOrder->no_vendor_bill ?? $purchaseOrder->invoices->first()->no_invoice ?? $purchaseOrder->no_po;
    $documentTitle = $purchaseOrder->status === 'vendor_bill' ? 'VENDOR BILL / INVOICE' : 'PAYMENT RECEIPT';
}
```

**Sesudah:**

```php
elseif ($purchaseOrder->status === 'vendor_bill' || $purchaseOrder->status === 'partial' || $purchaseOrder->status === 'payment') {
    // Invoice - layout invoice (untuk vendor_bill, partial, dan payment)
    $view = 'admin.pembelian.purchase-order.print-invoice';
    $printNumber = $purchaseOrder->no_vendor_bill ?? $purchaseOrder->invoices->first()->no_invoice ?? $purchaseOrder->no_po;

    if ($purchaseOrder->status === 'vendor_bill') {
        $documentTitle = 'VENDOR BILL / INVOICE';
    } elseif ($purchaseOrder->status === 'partial') {
        $documentTitle = 'VENDOR BILL / INVOICE (DIBAYAR SEBAGIAN)';
    } else {
        $documentTitle = 'PAYMENT RECEIPT';
    }
}
```

### Frontend (View)

**File:** `resources/views/admin/pembelian/purchase-order/print-invoice.blade.php`

#### 1. Informasi Bank Supplier (Line 235-254)

Menambahkan section baru untuk menampilkan informasi pembayaran supplier:

```html
<!-- Informasi Bank Supplier -->
@if($purchaseOrder->supplier && ($purchaseOrder->supplier->bank ||
$purchaseOrder->supplier->no_rekening))
<div class="bank-info">
    <h4>Informasi Pembayaran - {{ $purchaseOrder->supplier->nama }}</h4>
    @if($purchaseOrder->supplier->bank)
    <div class="info-item">
        <span class="info-label">Bank:</span>
        <span class="info-value">{{ $purchaseOrder->supplier->bank }}</span>
    </div>
    @endif @if($purchaseOrder->supplier->no_rekening)
    <div class="info-item">
        <span class="info-label">No. Rekening:</span>
        <span class="info-value"
            >{{ $purchaseOrder->supplier->no_rekening }}</span
        >
    </div>
    @endif @if($purchaseOrder->supplier->atas_nama)
    <div class="info-item">
        <span class="info-label">Atas Nama:</span>
        <span class="info-value"
            >{{ $purchaseOrder->supplier->atas_nama }}</span
        >
    </div>
    @endif
</div>
@endif
```

**Styling untuk Bank Info:**

```css
.bank-info {
    background: #d1fae5;
    border: 1px solid #a7f3d0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.bank-info h4 {
    font-size: 13px;
    font-weight: 600;
    color: #065f46;
    margin-bottom: 8px;
}
```

#### 2. Status Pembayaran (Line 390-410)

**Sebelumnya:**

```html
<h3>Status</h3>
<div class="info-item">
    <span class="info-label">Status:</span>
    <span class="info-value">
        @if($purchaseOrder->status === 'vendor_bill') Belum Dibayar
        @elseif($purchaseOrder->status === 'payment') LUNAS @else {{
        ucfirst($purchaseOrder->status) }} @endif
    </span>
</div>
```

**Sesudah:**

```html
<h3>Status Pembayaran</h3>
<div class="info-item">
    <span class="info-label">Status:</span>
    <span class="info-value">
        @if($purchaseOrder->status === 'vendor_bill') Belum Dibayar
        @elseif($purchaseOrder->status === 'partial') DIBAYAR SEBAGIAN
        @elseif($purchaseOrder->status === 'payment') LUNAS @else {{
        ucfirst($purchaseOrder->status) }} @endif
    </span>
</div>
@if($purchaseOrder->status === 'partial' || $purchaseOrder->status ===
'payment')
<div class="info-item">
    <span class="info-label">Sudah Dibayar:</span>
    <span class="info-value"
        >Rp {{ number_format($purchaseOrder->total_dibayar ?? 0, 0, ',', '.')
        }}</span
    >
</div>
@endif @if($purchaseOrder->status === 'partial')
<div class="info-item">
    <span class="info-label">Sisa Pembayaran:</span>
    <span class="info-value" style="color: #dc2626; font-weight: 600;"
        >Rp {{ number_format($purchaseOrder->sisa_pembayaran ?? 0, 0, ',', '.')
        }}</span
    >
</div>
@endif
```

## Tampilan Print Invoice

### Informasi yang Ditampilkan:

#### Header

-   Nama Perusahaan
-   Alamat, Telepon, Email
-   Judul Dokumen (VENDOR BILL / INVOICE atau VENDOR BILL / INVOICE (DIBAYAR SEBAGIAN))
-   Nomor Invoice
-   Nomor PO

#### Informasi Bank Supplier (Baru!)

Ditampilkan dalam box hijau dengan informasi:

-   Bank: [Nama Bank]
-   No. Rekening: [Nomor Rekening]
-   Atas Nama: [Nama Pemilik Rekening]

#### Informasi Supplier

-   Nama Supplier
-   Telepon
-   Alamat

#### Informasi Invoice

-   Tanggal Invoice
-   Jatuh Tempo
-   Outlet
-   Tanggal Bayar (jika sudah lunas)

#### Tabel Items

-   No, Deskripsi, Satuan, Qty, Harga, Diskon, Subtotal

#### Ringkasan Pembayaran

-   Subtotal
-   Total Diskon
-   Total

#### Status Pembayaran (Updated!)

-   Status: Belum Dibayar / DIBAYAR SEBAGIAN / LUNAS
-   Sudah Dibayar: Rp xxx (untuk partial & payment)
-   Sisa Pembayaran: Rp xxx (untuk partial, ditampilkan merah)
-   Dibuat Oleh
-   Keterangan

#### Tanda Tangan

-   Finance
-   Supplier

## Model Supplier

**File:** `app/Models/Supplier.php`

Model Supplier sudah memiliki field untuk informasi bank:

```php
protected $fillable = [
    'nama',
    'telepon',
    'alamat',
    'email',
    'id_outlet',
    'is_active',
    // Field bank
    'bank',
    'no_rekening',
    'atas_nama'
];
```

Accessor untuk mendapatkan info bank lengkap:

```php
public function getInfoBankAttribute()
{
    if ($this->bank && $this->no_rekening && $this->atas_nama) {
        return $this->bank . ' - ' . $this->no_rekening . ' a/n ' . $this->atas_nama;
    }
    return null;
}
```

## Contoh Tampilan

### Status: Vendor Bill (Belum Dibayar)

```
┌─────────────────────────────────────────────┐
│ VENDOR BILL / INVOICE                       │
│ INV-2024-001                                │
│ Berdasarkan PO: PO-2024-001                 │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ Informasi Pembayaran - PT Supplier ABC     │
│ Bank: BCA                                   │
│ No. Rekening: 1234567890                    │
│ Atas Nama: PT Supplier ABC                  │
└─────────────────────────────────────────────┘

Status: Belum Dibayar
```

### Status: Partial (Dibayar Sebagian)

```
┌─────────────────────────────────────────────┐
│ VENDOR BILL / INVOICE (DIBAYAR SEBAGIAN)    │
│ INV-2024-001                                │
│ Berdasarkan PO: PO-2024-001                 │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ Informasi Pembayaran - PT Supplier ABC     │
│ Bank: BCA                                   │
│ No. Rekening: 1234567890                    │
│ Atas Nama: PT Supplier ABC                  │
└─────────────────────────────────────────────┘

Status: DIBAYAR SEBAGIAN
Sudah Dibayar: Rp 5.000.000
Sisa Pembayaran: Rp 5.000.000 (merah)
```

### Status: Payment (Lunas)

```
┌─────────────────────────────────────────────┐
│ PAYMENT RECEIPT                             │
│ INV-2024-001                                │
│ Berdasarkan PO: PO-2024-001                 │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ Informasi Pembayaran - PT Supplier ABC     │
│ Bank: BCA                                   │
│ No. Rekening: 1234567890                    │
│ Atas Nama: PT Supplier ABC                  │
└─────────────────────────────────────────────┘

Status: LUNAS
Sudah Dibayar: Rp 10.000.000
```

## Testing Checklist

-   [x] Status 'partial' menggunakan print-invoice view
-   [x] Document title untuk partial: "VENDOR BILL / INVOICE (DIBAYAR SEBAGIAN)"
-   [x] Informasi bank supplier ditampilkan (jika ada)
-   [x] Status pembayaran menampilkan "DIBAYAR SEBAGIAN" untuk partial
-   [x] Menampilkan jumlah sudah dibayar untuk partial & payment
-   [x] Menampilkan sisa pembayaran untuk partial (warna merah)
-   [x] Model Supplier memiliki field bank, no_rekening, atas_nama

## Cara Testing

1. **Buat PO dan proses hingga Vendor Bill**
2. **Print Invoice** - harus menampilkan informasi bank supplier
3. **Bayar sebagian** dari total PO
4. **Print Invoice** - harus menampilkan:
    - Title: "VENDOR BILL / INVOICE (DIBAYAR SEBAGIAN)"
    - Status: "DIBAYAR SEBAGIAN"
    - Sudah Dibayar: Rp xxx
    - Sisa Pembayaran: Rp xxx (merah)
    - Informasi bank supplier
5. **Bayar sisa pembayaran**
6. **Print Invoice** - harus menampilkan:
    - Title: "PAYMENT RECEIPT"
    - Status: "LUNAS"
    - Sudah Dibayar: Rp xxx
    - Informasi bank supplier

## Notes

-   Informasi bank supplier hanya ditampilkan jika minimal ada data bank atau no_rekening
-   Box informasi bank menggunakan background hijau untuk highlight
-   Sisa pembayaran ditampilkan dengan warna merah untuk emphasis
-   Format print invoice konsisten untuk vendor_bill, partial, dan payment
-   Document title berbeda untuk setiap status untuk memudahkan identifikasi
