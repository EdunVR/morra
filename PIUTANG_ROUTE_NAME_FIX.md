# Fix Piutang - Menggunakan Route Name

## Perubahan yang Dilakukan

### 1. Update Routes di JavaScript

Menambahkan route name untuk semua endpoint yang digunakan:

```javascript
routes: {
  outletsData: '{{ route("finance.outlets.data") }}',
  piutangData: '{{ route("finance.piutang.data") }}',
  piutangDetail: '{{ route("finance.piutang.detail", ":id") }}',
  invoiceIndex: '{{ route("penjualan.invoice.index") }}',
  markPaid: '{{ route("finance.piutang.mark-paid", ":id") }}',
  getSalesInvoiceId: '{{ route("finance.piutang.get-sales-invoice-id", ":id") }}',
  invoicePrint: '{{ route("penjualan.invoice.print", ":id") }}'
}
```

### 2. Update Method showInvoicePDF()

Menggunakan route name untuk fetch dan print URL:

```javascript
async showInvoicePDF(piutangId, penjualanId) {
  // Menggunakan route name untuk API call
  const url = this.routes.getSalesInvoiceId.replace(':id', penjualanId);
  const response = await fetch(url);
  const data = await response.json();

  if (data.success && data.sales_invoice_id) {
    // Menggunakan route name untuk print URL
    this.showPrintModal = true;
    const printUrl = this.routes.invoicePrint.replace(':id', data.sales_invoice_id);
    this.printPdfUrl = printUrl;
  }
}
```

### 3. Update Method redirectToInvoicePayment()

Menggunakan route name untuk fetch:

```javascript
async redirectToInvoicePayment(penjualanId) {
  // Menggunakan route name untuk API call
  const url = this.routes.getSalesInvoiceId.replace(':id', penjualanId);
  const response = await fetch(url);
  const data = await response.json();

  if (data.success && data.sales_invoice_id) {
    // Redirect dengan parameter untuk auto-open modal pembayaran
    window.location.href = `${this.routes.invoiceIndex}?invoice_id=${data.sales_invoice_id}&open_payment=1`;
  }
}
```

## Fitur yang Sudah Bekerja

### 1. Klik Invoice → Modal Print PDF

-   Klik nomor invoice di tabel piutang
-   Sistem fetch sales_invoice_id dari id_penjualan
-   Modal terbuka dengan iframe menampilkan PDF invoice penjualan
-   Menggunakan route name: `finance.piutang.get-sales-invoice-id` dan `penjualan.invoice.print`

### 2. Tombol Bayar → Redirect + Auto-Open Modal

-   Klik tombol "Bayar" di tabel piutang
-   Sistem fetch sales_invoice_id dari id_penjualan
-   Redirect ke halaman invoice penjualan dengan parameter `?invoice_id=X&open_payment=1`
-   Modal konfirmasi pelunasan otomatis terbuka
-   Menggunakan route name: `finance.piutang.get-sales-invoice-id` dan `penjualan.invoice.index`

### 3. Semua Fetch Menggunakan Route Name

✅ `finance.outlets.data` - Load outlets
✅ `finance.piutang.data` - Load piutang data
✅ `finance.piutang.detail` - Load piutang detail
✅ `finance.piutang.get-sales-invoice-id` - Mapping penjualan ID ke sales_invoice ID
✅ `penjualan.invoice.index` - Halaman invoice penjualan
✅ `penjualan.invoice.print` - Print PDF invoice
✅ `finance.piutang.mark-paid` - Mark piutang as paid

## Routes yang Digunakan

### Backend Routes (routes/web.php)

```php
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('outlets', [FinanceAccountantController::class, 'getOutlets'])
        ->name('outlets.data');

    Route::get('piutang', [FinanceAccountantController::class, 'piutangIndex'])
        ->name('piutang.index');

    Route::get('piutang/data', [FinanceAccountantController::class, 'getPiutangData'])
        ->name('piutang.data');

    Route::get('piutang/{id}/detail', [FinanceAccountantController::class, 'getPiutangDetail'])
        ->name('piutang.detail');

    Route::post('piutang/{id}/mark-paid', [FinanceAccountantController::class, 'markPiutangAsPaid'])
        ->name('piutang.mark-paid');

    Route::get('piutang/get-sales-invoice-id/{penjualanId}', [FinanceAccountantController::class, 'getSalesInvoiceId'])
        ->name('piutang.get-sales-invoice-id');
});

Route::prefix('penjualan')->name('penjualan.')->group(function () {
    Route::get('invoice', [SalesManagementController::class, 'index'])
        ->name('invoice.index');

    Route::get('invoice/{id}/print', [SalesManagementController::class, 'invoicePrint'])
        ->name('invoice.print');
});
```

### Controller Methods

#### FinanceAccountantController

```php
public function getSalesInvoiceId($penjualanId)
{
    $salesInvoice = DB::table('sales_invoice')
        ->where('id_penjualan', $penjualanId)
        ->first();

    if ($salesInvoice) {
        return response()->json([
            'success' => true,
            'sales_invoice_id' => $salesInvoice->id_sales_invoice,
            'penjualan_id' => $penjualanId
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Sales invoice tidak ditemukan'
    ], 404);
}
```

## Testing Guide

### Test 1: Klik Invoice untuk Print PDF

1. Buka halaman Piutang: `/finance/piutang`
2. Klik nomor invoice di kolom "No Invoice"
3. **Expected**: Modal terbuka dengan PDF invoice penjualan
4. **Verify**: URL di iframe menggunakan route name yang benar

### Test 2: Tombol Bayar untuk Pelunasan

1. Buka halaman Piutang: `/finance/piutang`
2. Klik tombol "Bayar" pada piutang yang belum lunas
3. **Expected**:
    - Redirect ke halaman invoice penjualan
    - Modal konfirmasi pelunasan otomatis terbuka
    - Data invoice sudah terisi
4. **Verify**: URL menggunakan route name yang benar

### Test 3: Verifikasi Route Name

1. Buka browser console
2. Inspect network tab
3. Klik invoice atau tombol bayar
4. **Verify**: Semua request menggunakan URL dari route name (bukan hardcoded)

## Keuntungan Menggunakan Route Name

1. **Maintainability**: Jika URL berubah, cukup update di routes/web.php
2. **Type Safety**: Laravel akan error jika route name tidak ada
3. **Consistency**: Semua URL dikelola di satu tempat
4. **Refactoring**: Mudah untuk rename atau reorganize routes
5. **Documentation**: Route name lebih deskriptif daripada hardcoded URL

## Status

✅ Semua fitur sudah menggunakan route name
✅ Modal print PDF bekerja dengan benar
✅ Auto-open modal pembayaran bekerja dengan benar
✅ Redirect ke halaman invoice bekerja dengan benar
✅ Mapping ID penjualan ke sales_invoice bekerja dengan benar

## File yang Dimodifikasi

-   `resources/views/admin/finance/piutang/index.blade.php` - Update JavaScript untuk menggunakan route name
