# Invoice Draft Workflow - Implementation Complete ✅

## Overview

Implementasi lengkap fitur Draft Invoice yang mengubah workflow invoice menjadi:

1. **Draft** → Bisa diedit, no invoice = "DRAFT"
2. **Konfirmasi** → Generate nomor invoice + jurnal + piutang, status = "menunggu"
3. **Menunggu/Dibayar** → Tidak bisa diedit lagi

---

## 🎯 Changes Implemented

### 1. Database Migration ✅

**File:** `database/migrations/2025_11_21_160000_add_draft_status_to_sales_invoice.php`

```sql
ALTER TABLE sales_invoice MODIFY COLUMN status
ENUM('draft', 'menunggu', 'dibayar_sebagian', 'lunas', 'gagal')
NOT NULL DEFAULT 'draft'
```

**Status:** Migration executed successfully

---

### 2. Backend Changes ✅

#### A. Controller Updates

**File:** `app/Http/Controllers/SalesManagementController.php`

**New Imports:**

```php
use App\Models\Piutang;
```

**New Methods:**

1. **`confirmInvoice($invoiceId)`** - Main method untuk konfirmasi invoice

    - Validasi status draft
    - Generate nomor invoice
    - Update status ke 'menunggu'
    - Create piutang record
    - Create journal entry
    - Return JSON response

2. **`generateInvoiceNumber($outletId)`** - Generate nomor invoice dengan format

    - Format: `001/PBU/INV/XI/2025`
    - Auto-increment per bulan per outlet
    - Menggunakan Roman numeral untuk bulan

3. **`numberToRoman($number)`** - Convert angka bulan ke Roman numeral

    - 1 → I, 2 → II, ..., 12 → XII

4. **`createPiutangFromInvoice($invoice)`** - Create piutang record

    - Check existing piutang
    - Create new piutang dengan sisa_piutang = total

5. **`createJournalFromInvoice($invoice)`** - Create journal entry
    - Placeholder untuk integrasi dengan journal system

**Updated Methods:**

**Stats Method** - Added draft and dibayar_sebagian counts:

```php
$counts = [
    'all' => SalesInvoice::where('id_outlet', $outletId)->count(),
    'draft' => SalesInvoice::where('status', 'draft')->where('id_outlet', $outletId)->count(),
    'menunggu' => SalesInvoice::where('status', 'menunggu')->where('id_outlet', $outletId)->count(),
    'dibayar_sebagian' => SalesInvoice::where('status', 'dibayar_sebagian')->where('id_outlet', $outletId)->count(),
    'lunas' => SalesInvoice::where('status', 'lunas')->where('id_outlet', $outletId)->count(),
    'gagal' => SalesInvoice::where('status', 'gagal')->where('id_outlet', $outletId)->count(),
];
```

---

### 3. Routes ✅

**File:** `routes/web.php`

```php
// Invoice Confirm Route (Draft to Menunggu)
Route::post('invoice/{id}/confirm', [SalesManagementController::class, 'confirmInvoice'])
    ->name('invoice.confirm');
```

---

### 4. Frontend Changes ✅

#### A. JavaScript Functions

**File:** `resources/views/admin/penjualan/invoice/index.blade.php`

**New Function: `confirmInvoice(invoiceId)`**

```javascript
async confirmInvoice(invoiceId) {
    // Confirmation dialog
    // POST request to /invoice/{id}/confirm
    // Reload invoices and stats on success
    // Show toast message
}
```

**Updated Function: `openCreateInvoice()`**

```javascript
this.invoiceForm.no_invoice = "DRAFT";
this.invoiceForm.status = "draft";
```

**New Function: `getStatusText(status)`**

```javascript
getStatusText(status) {
    const textMap = {
        'draft': 'Draft',
        'menunggu': 'Menunggu',
        'dibayar_sebagian': 'Dibayar Sebagian',
        'lunas': 'Lunas',
        'gagal': 'Gagal'
    };
    return textMap[status] || status;
}
```

**Updated Function: `getStatusBadgeClass(status)`**

```javascript
getStatusBadgeClass(status) {
    const classMap = {
        'draft': 'bg-gray-100 text-gray-800',
        'menunggu': 'bg-amber-100 text-amber-800',
        'dibayar_sebagian': 'bg-blue-100 text-blue-800',
        'lunas': 'bg-emerald-100 text-emerald-800',
        'gagal': 'bg-red-100 text-red-800'
    };
    return classMap[status] || 'bg-slate-100 text-slate-800';
}
```

#### B. UI Updates

**1. Stats Cards** - Added Draft card (5 cards total):

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    <!-- Total Invoice -->
    <!-- Draft (NEW) -->
    <!-- Menunggu -->
    <!-- Lunas -->
    <!-- Gagal -->
</div>
```

**2. Action Buttons** - Conditional rendering based on status:

```html
<!-- Edit button - only for draft -->
<template x-if="invoice.status === 'draft'">
    <button @click="editInvoice(invoice)">
        <i class="bx bx-edit"></i> Edit
    </button>
</template>

<!-- Konfirmasi button - only for draft -->
<template x-if="invoice.status === 'draft'">
    <button @click="confirmInvoice(invoice.id_sales_invoice)">
        <i class="bx bx-check-circle"></i> Konfirmasi
    </button>
</template>

<!-- Bayar button - only for menunggu/dibayar_sebagian -->
<template
    x-if="invoice.status === 'menunggu' || invoice.status === 'dibayar_sebagian'"
>
    <button @click="openPaymentModal(invoice.id_sales_invoice)">
        <i class="bx bx-money"></i> Bayar
    </button>
</template>
```

**3. Invoice Number Display** - Show "DRAFT" for draft invoices:

```html
<div x-text="invoice.status === 'draft' ? 'DRAFT' : invoice.no_invoice"></div>
```

**4. Status Badge** - Added draft styling:

-   Draft: Gray badge
-   Menunggu: Amber badge
-   Dibayar Sebagian: Blue badge
-   Lunas: Green badge
-   Gagal: Red badge

---

## 🔄 Workflow

### Create Invoice (Draft)

1. User clicks "Invoice Baru"
2. Fill invoice details
3. Save → Status = "draft", no_invoice = "DRAFT"
4. Invoice can be edited multiple times

### Confirm Invoice

1. User clicks "Konfirmasi" button on draft invoice
2. Confirmation dialog appears
3. System:
    - Generates invoice number (e.g., 001/PBU/INV/XI/2025)
    - Updates status to "menunggu"
    - Creates piutang record
    - Creates journal entry
4. Invoice can no longer be edited

### Payment

1. User clicks "Bayar" on menunggu/dibayar_sebagian invoice
2. Enter payment details
3. System updates payment history and status

---

## 📊 Status Flow

```
[Create] → draft → [Konfirmasi] → menunggu → [Bayar] → dibayar_sebagian/lunas
                                           ↓
                                        [Batalkan] → gagal
```

---

## 🎨 UI/UX Improvements

1. **Clear Visual Indicators**

    - Draft invoices show "DRAFT" instead of invoice number
    - Gray badge for draft status
    - Different action buttons for each status

2. **Intuitive Actions**

    - Edit only available for draft
    - Konfirmasi only available for draft
    - Bayar only available for menunggu/dibayar_sebagian

3. **Stats Dashboard**

    - 5 cards showing all status counts
    - Easy to see how many drafts need confirmation

4. **Confirmation Dialog**
    - Warns user that invoice cannot be edited after confirmation
    - Mentions that invoice number will be generated

---

## 🧪 Testing Checklist

### Create Draft Invoice

-   [ ] Create new invoice
-   [ ] Verify status = "draft"
-   [ ] Verify no_invoice = "DRAFT"
-   [ ] Verify Edit button is visible
-   [ ] Verify Konfirmasi button is visible
-   [ ] Verify Bayar button is NOT visible

### Edit Draft Invoice

-   [ ] Click Edit on draft invoice
-   [ ] Modify invoice details
-   [ ] Save changes
-   [ ] Verify changes are saved
-   [ ] Verify status remains "draft"

### Confirm Invoice

-   [ ] Click Konfirmasi on draft invoice
-   [ ] Verify confirmation dialog appears
-   [ ] Confirm action
-   [ ] Verify invoice number is generated (format: 001/PBU/INV/XI/2025)
-   [ ] Verify status changed to "menunggu"
-   [ ] Verify Edit button is NOT visible
-   [ ] Verify Konfirmasi button is NOT visible
-   [ ] Verify Bayar button IS visible
-   [ ] Verify piutang record is created
-   [ ] Check database for journal entry log

### Stats

-   [ ] Create draft invoice → verify draft count increases
-   [ ] Confirm invoice → verify draft count decreases, menunggu count increases
-   [ ] Pay invoice → verify menunggu count decreases, lunas count increases

### Invoice Number Generation

-   [ ] Confirm first invoice of month → verify number is 001
-   [ ] Confirm second invoice → verify number is 002
-   [ ] Change month → verify number resets to 001
-   [ ] Test with different outlets → verify separate counters

---

## 🚀 Deployment Notes

1. **Run Migration:**

    ```bash
    php artisan migrate
    ```

2. **Clear Cache:**

    ```bash
    php artisan cache:clear
    php artisan view:clear
    php artisan config:clear
    ```

3. **Test in Browser:**
    - Create draft invoice
    - Edit draft invoice
    - Confirm invoice
    - Verify invoice number generation
    - Check stats

---

## 📝 Future Enhancements

1. **Journal Integration**

    - Complete implementation of `createJournalFromInvoice()`
    - Auto-create debit/credit entries

2. **Bulk Confirm**

    - Add ability to confirm multiple draft invoices at once

3. **Draft Expiry**

    - Auto-delete or archive old draft invoices

4. **Draft Templates**

    - Save draft as template for recurring invoices

5. **Approval Workflow**
    - Add approval step before confirmation for certain users

---

## ✅ Implementation Status

-   [x] Database migration
-   [x] Backend controller methods
-   [x] Routes
-   [x] Frontend JavaScript functions
-   [x] UI updates (buttons, badges, stats)
-   [x] Invoice number generation
-   [x] Piutang creation
-   [x] Status workflow
-   [x] Documentation

**Status:** COMPLETE AND READY FOR TESTING

---

## 🎉 Summary

Fitur Draft Invoice telah berhasil diimplementasikan dengan lengkap. User sekarang bisa:

1. Membuat invoice sebagai draft
2. Edit draft invoice berkali-kali
3. Konfirmasi draft untuk generate nomor invoice dan create piutang
4. Invoice yang sudah dikonfirmasi tidak bisa diedit lagi

Workflow ini memberikan fleksibilitas untuk user dalam membuat invoice tanpa harus langsung commit, sambil tetap menjaga integritas data setelah invoice dikonfirmasi.
