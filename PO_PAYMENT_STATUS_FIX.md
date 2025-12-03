# Purchase Order Payment Status Fix

## Perubahan yang Dilakukan

### 1. Status Lunas Diubah Menjadi "Payment"

**Sebelumnya:** Status setelah lunas adalah "lunas"  
**Sekarang:** Status setelah lunas adalah "payment" dengan label "Lunas"

### 2. Tambah Status "Dibayar Sebagian" (partial)

Menambahkan status baru untuk PO yang sudah dibayar sebagian tetapi belum lunas.

## File yang Dimodifikasi

### Backend (Controller)

**File:** `app/Http/Controllers/PurchaseManagementController.php`

#### 1. Method `processPayment` (Line 3537-3539)

Status sudah benar:

-   Jika `sisa_pembayaran <= 0` → status = `'payment'` (Lunas)
-   Jika `total_dibayar > 0` → status = `'partial'` (Dibayar Sebagian)

#### 2. Method `purchaseOrderStatusCounts` (Line 1214-1224)

Menambahkan counter untuk status 'partial':

```php
$counts = [
    'total' => $query->count(),
    'permintaan_pembelian' => ...,
    'request_quotation' => ...,
    'purchase_order' => ...,
    'penerimaan_barang' => ...,
    'vendor_bill' => ...,
    'partial' => $query->clone()->where('status', 'partial')->count(), // BARU
    'payment' => $query->clone()->where('status', 'payment')->count(),
    'dibatalkan' => ...,
];
```

#### 3. Method `updateStatus` - Validasi Status (Line 664)

Menambahkan 'partial' ke validasi:

```php
'status' => 'required|in:permintaan_pembelian,request_quotation,purchase_order,penerimaan_barang,vendor_bill,partial,payment,dibatalkan',
```

#### 4. Method `updateStatus` - Valid Transitions (Line 707-715)

Menambahkan transisi status untuk 'partial':

```php
$validTransitions = [
    ...
    'vendor_bill' => ['partial', 'payment', 'dibatalkan'],
    'partial' => ['payment'], // BARU
    'payment' => [],
    ...
];
```

#### 5. Method `updateStatus` - Validasi Pembatalan (Line 699)

Update validasi pembatalan untuk include 'partial':

```php
if ($newStatus === 'dibatalkan' && in_array($oldStatus, ['penerimaan_barang', 'vendor_bill', 'partial', 'payment'])) {
    throw new \Exception("PO tidak dapat dibatalkan setelah status Penerimaan Barang");
}
```

### Frontend (View)

**File:** `resources/views/admin/pembelian/purchase-order/index.blade.php`

#### 1. Stats Object (Line 2534-2543)

Menambahkan counter untuk 'partial':

```javascript
stats: {
    total: 0,
    permintaan_pembelian: 0,
    request_quotation: 0,
    purchase_order: 0,
    penerimaan_barang: 0,
    vendor_bill: 0,
    partial: 0,        // BARU
    payment: 0,
    dibatalkan: 0
},
```

#### 2. Stats Cards (Line 70-150)

-   Mengubah grid dari `lg:grid-cols-7` menjadi `lg:grid-cols-8`
-   Menambahkan card untuk "Dibayar Sebagian":

```html
<!-- Dibayar Sebagian -->
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="flex items-center gap-3">
        <div
            class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"
        >
            <i class="bx bx-wallet text-xl text-blue-600"></i>
        </div>
        <div>
            <div class="text-lg font-bold" x-text="stats.partial"></div>
            <div class="text-xs text-slate-600">Sebagian</div>
        </div>
    </div>
</div>
```

-   Mengubah label "Payment" menjadi "Lunas"

#### 3. Status Tabs (Line 239-244)

Menambahkan tab untuk "Dibayar Sebagian":

```html
<button
    :class="activeTab === 'partial' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-white text-slate-700 border-slate-200'"
    @click="setActiveTab('partial')"
    class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-slate-50"
>
    <i class="bx bx-wallet text-blue-600"></i> Dibayar Sebagian
    <span
        class="bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full"
        x-text="stats.partial"
    ></span>
</button>
```

#### 4. Function `getStatusText` (Line 3661-3676)

Mengubah label status:

```javascript
getStatusText(status) {
    const statusMap = {
        'permintaan_pembelian': 'Permintaan Pembelian',
        'request_quotation': 'Request Quotation',
        'purchase_order': 'Purchase Order',
        'penerimaan_barang': 'Penerimaan Barang',
        'vendor_bill': 'Vendor Bill',
        'partial': 'Dibayar Sebagian',  // BARU
        'payment': 'Lunas',              // DIUBAH dari 'Payment'
        'dibatalkan': 'Dibatalkan',
        // Payment statuses
        'pending': 'Belum Dibayar',
        'paid': 'Lunas'
    };
    return statusMap[status] || status;
},
```

## Flow Status Pembayaran

### Sebelum Perubahan:

```
vendor_bill → payment (lunas)
```

### Setelah Perubahan:

```
vendor_bill → partial (dibayar sebagian) → payment (lunas)
            ↘ payment (langsung lunas jika bayar penuh)
```

## Status Badge Colors

| Status                     | Badge Color                           | Icon             |
| -------------------------- | ------------------------------------- | ---------------- |
| Dibayar Sebagian (partial) | Blue (`bg-blue-100 text-blue-800`)    | `bx-wallet`      |
| Lunas (payment)            | Green (`bg-green-100 text-green-800`) | `bx-credit-card` |

## Testing Checklist

-   [x] Backend: Status 'partial' ditambahkan ke validasi
-   [x] Backend: Counter untuk status 'partial' ditambahkan
-   [x] Backend: Transisi status mendukung 'partial'
-   [x] Frontend: Stats card untuk "Dibayar Sebagian" ditambahkan
-   [x] Frontend: Tab untuk "Dibayar Sebagian" ditambahkan
-   [x] Frontend: Label "Payment" diubah menjadi "Lunas"
-   [x] Frontend: Status badge dan text untuk 'partial' sudah ada

## Cara Testing

1. **Buat PO baru** dan proses hingga status Vendor Bill
2. **Bayar sebagian** dari total PO
    - Status harus berubah menjadi "Dibayar Sebagian" (partial)
    - Tab "Dibayar Sebagian" harus menampilkan PO ini
3. **Bayar sisa pembayaran**
    - Status harus berubah menjadi "Lunas" (payment)
    - Tab "Lunas" harus menampilkan PO ini
4. **Bayar penuh langsung** dari Vendor Bill
    - Status harus langsung menjadi "Lunas" (payment)

## Notes

-   Status 'partial' hanya bisa transisi ke 'payment'
-   Status 'payment' adalah status final (tidak bisa diubah lagi)
-   PO dengan status 'partial' atau 'payment' tidak bisa dibatalkan
-   Counter stats sudah include status 'partial' untuk tracking yang lebih baik

## Perbaikan Keterangan Jurnal Pembayaran

**File:** `app/Http/Controllers/PurchaseManagementController.php`  
**Method:** `createPaymentJournal` (Line 3788-3797)

### Sebelum:

```php
$description = "Pembayaran PO {$purchaseOrder->no_po} - {$supplierName}";
```

### Sesudah:

```php
$invoiceNumber = $purchaseOrder->no_vendor_bill ?? null;
if ($invoiceNumber) {
    $description = "Pembayaran Invoice {$invoiceNumber} dari PO {$purchaseOrder->no_po} - {$supplierName}";
} else {
    $description = "Pembayaran PO {$purchaseOrder->no_po} - {$supplierName}";
}
```

### Contoh Keterangan:

-   **Dengan Invoice:** "Pembayaran Invoice INV-2024-001 dari PO PO-2024-001 - PT Supplier ABC"
-   **Tanpa Invoice:** "Pembayaran PO PO-2024-001 - PT Supplier ABC"

### Memo Entry:

-   **Debit (Hutang Usaha):** "Pembayaran Invoice INV-2024-001" atau "Pembayaran hutang ke PT Supplier ABC"
-   **Kredit (Kas/Bank):** "Pembayaran tunai/transfer ke PT Supplier ABC"

### Manfaat:

-   Keterangan jurnal lebih informatif dengan mencantumkan nomor invoice
-   Memudahkan tracking pembayaran berdasarkan nomor invoice
-   Konsisten dengan format "Pembayaran Invoice XXX dari PO YYY"
