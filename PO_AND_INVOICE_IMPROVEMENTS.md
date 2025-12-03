# Purchase Order & Invoice Penjualan Improvements

## Requirements

### 1. Purchase Order - Remove Outlet Filter

-   ❌ Hilangkan dropdown filter outlet "Semua Outlet"
-   ✅ Langsung gunakan akses outlet user
-   ✅ Jika user punya multiple outlets, default ke outlet pertama
-   ✅ Jika tidak ada akses outlet, default ke outlet 1

### 2. Invoice Penjualan - Add "Dibayar Sebagian" Tab

-   ✅ Tambah tab status "Dibayar Sebagian" di halaman invoice
-   ✅ Tampilkan invoice yang sudah dibayar sebagian (partial payment)

### 3. Bukti Pembayaran - Make Optional

-   ✅ Bukti pembayaran tidak wajib diupload
-   ✅ User bisa submit pembayaran tanpa bukti transfer

### 4. Sisa Tagihan - Fix Calculation

-   ❌ Bug: Saat pembayaran awal, sisa tagihan tertulis 0
-   ✅ Fix: Rumus = Total Invoice - Sudah Bayar
-   ✅ Berlaku untuk pembayaran awal dan cicilan

## Implementation Plan

### Task 1: Fix Purchase Order Outlet Filter

**Files to modify:**

-   `resources/views/admin/pembelian/purchase-order/index.blade.php`
-   `app/Http/Controllers/PurchaseManagementController.php` (if needed)

**Changes:**

1. Remove "Semua Outlet" option from dropdown
2. Auto-select user's first outlet on init
3. Remove disabled state from buttons when outlet selected
4. Update `init()` method to set default outlet

### Task 2: Add "Dibayar Sebagian" Tab to Invoice

**Files to modify:**

-   `resources/views/admin/penjualan/invoice/index.blade.php`
-   `app/Http/Controllers/SalesManagementController.php`

**Changes:**

1. Add new tab "Dibayar Sebagian" in stats section
2. Add filter for partial payment status
3. Update stats calculation to include partial payments

### Task 3: Make Payment Proof Optional

**Files to modify:**

-   `resources/views/admin/penjualan/invoice/index.blade.php` (payment modal)
-   `app/Http/Controllers/SalesManagementController.php` (validation)

**Changes:**

1. Remove `required` attribute from bukti transfer input
2. Update validation rules to make bukti_transfer optional
3. Allow null/empty bukti_transfer in database

### Task 4: Fix Remaining Balance Calculation

**Files to modify:**

-   `app/Http/Controllers/SalesManagementController.php`
-   `app/Models/SalesInvoice.php` (if has accessor)
-   Frontend display logic

**Changes:**

1. Fix calculation: `sisa_tagihan = total_invoice - total_terbayar`
2. Ensure calculation works for:
    - Initial payment (down payment)
    - Installment payments
    - Full payment
3. Update display in invoice list and detail

## Status

-   [ ] Task 1: Fix PO Outlet Filter
-   [ ] Task 2: Add "Dibayar Sebagian" Tab
-   [ ] Task 3: Make Payment Proof Optional
-   [ ] Task 4: Fix Remaining Balance Calculation
