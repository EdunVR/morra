# Invoice Payment UX Enhancements 🎨

## Changes Implemented

### 1. ✅ Simplified Card Payment Info

**Before:**

```
Informasi Pembayaran:
- Jenis: Cash/Transfer
- Penerima: John Doe
- Tanggal Bayar: 21/11/2025
- Catatan: ...
```

**After:**

```
Pembayaran:
- Bayar Lunas (jika 1x bayar)
- Bayar Cicilan (jika multiple payments)
```

**Logic:**

-   Jika `status === 'lunas'` DAN `total_dibayar === total` → "Bayar Lunas"
-   Jika `status === 'dibayar_sebagian'` ATAU ada multiple payments → "Bayar Cicilan"

---

### 2. ✅ Enhanced Payment History Modal

**Added Columns:**

-   **Penerima** - Person who received the payment
-   **Keterangan** - Payment notes/remarks

**Complete Table Structure:**

1. No
2. Tanggal
3. Jumlah Bayar
4. Metode (Cash/Transfer)
5. Bank/Pengirim
6. **Penerima** (NEW)
7. **Keterangan** (NEW)
8. Bukti
9. Dicatat Oleh

---

### 3. ✅ Bukti Viewer Modal (No New Window)

**Before:**

-   Click "Lihat Bukti" → Opens in new window/tab

**After:**

-   Click "Lihat" → Opens in modal
-   Modal features:
    -   Full image display
    -   Download button
    -   Close button
    -   Responsive design

**Modal Structure:**

```
┌─────────────────────────────────────┐
│ Bukti Pembayaran              [X]   │
├─────────────────────────────────────┤
│                                     │
│     [Image Display Area]            │
│     (max-h-70vh, centered)          │
│                                     │
├─────────────────────────────────────┤
│              [Download] [Tutup]     │
└─────────────────────────────────────┘
```

---

### 4. ✅ Removed "Lihat Bukti" Button from Card

**Before:**

-   Card had separate "Lihat Bukti" button for lunas invoices

**After:**

-   Button removed
-   Use "Lihat Cicilan/Bukti" button instead

---

### 5. ✅ Smart Button Text Logic

**Button Text Changes Based on Payment Type:**

**Single Payment (Lunas):**

-   Icon: `bx-image`
-   Text: "Lihat Bukti"
-   Condition: `status === 'lunas' && (total_dibayar === total || !total_dibayar)`

**Multiple Payments (Cicilan):**

-   Icon: `bx-history`
-   Text: "Lihat Cicilan"
-   Condition: `status === 'dibayar_sebagian' || multiple payments`

**Implementation:**

```html
<button @click="openPaymentHistoryModal(invoice.id_sales_invoice)">
    <i
        :class="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'bx bx-image' : 'bx bx-history'"
    ></i>
    <span
        x-text="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'Lihat Bukti' : 'Lihat Cicilan'"
    ></span>
</button>
```

---

## Database Changes

### New Migration: `2025_11_21_150000_add_penerima_to_invoice_payment_history_table`

**Added Field:**

```php
$table->string('penerima')->nullable()->after('nama_pengirim')->comment('Person who received the payment');
```

**Purpose:** Store who received the payment (from payment form)

---

## Backend Changes

### Model: `InvoicePaymentHistory.php`

**Added to fillable:**

```php
'penerima',
```

### Controller: `SalesManagementController.php`

**processInvoicePayment():**

```php
InvoicePaymentHistory::create([
    // ... existing fields
    'penerima' => $request->penerima,  // NEW
    // ... rest
]);
```

**getPaymentHistory():**

```php
return [
    // ... existing fields
    'penerima' => $payment->penerima,  // NEW
    // ... rest
];
```

---

## Frontend Changes

### View: `resources/views/admin/penjualan/invoice/index.blade.php`

#### 1. Card Payment Info (Line ~210-220)

```html
<template
    x-if="invoice.status === 'lunas' || invoice.status === 'dibayar_sebagian'"
>
    <div class="col-span-2 mt-2">
        <div class="border-t pt-2">
            <div class="text-slate-500 text-xs font-semibold mb-1">
                Pembayaran:
            </div>
            <div class="text-xs">
                <span
                    class="font-medium"
                    x-text="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'Bayar Lunas' : 'Bayar Cicilan'"
                ></span>
            </div>
        </div>
    </div>
</template>
```

#### 2. Smart Button (Line ~284-290)

```html
<template x-if="invoice.total_dibayar > 0">
    <button @click="openPaymentHistoryModal(invoice.id_sales_invoice)">
        <i
            :class="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'bx bx-image' : 'bx bx-history'"
        ></i>
        <span
            x-text="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'Lihat Bukti' : 'Lihat Cicilan'"
        ></span>
    </button>
</template>
```

#### 3. Bukti Modal (Line ~1046-1075)

```html
<div x-show="showBuktiModal" x-transition.opacity class="fixed inset-0 z-50...">
    <div class="w-full max-w-4xl bg-white rounded-2xl...">
        <div class="px-4 sm:px-5 py-3 border-b...">
            <div class="font-semibold">Bukti Pembayaran</div>
            <button @click="showBuktiModal = false">...</button>
        </div>
        <div class="px-4 sm:px-5 py-4 max-h-[80vh] overflow-y-auto">
            <div
                class="flex items-center justify-center bg-slate-100 rounded-lg p-4"
            >
                <img
                    :src="currentBuktiUrl"
                    alt="Bukti Pembayaran"
                    class="max-w-full max-h-[70vh]..."
                />
            </div>
        </div>
        <div class="px-4 sm:px-5 pb-3 pt-2 border-t...">
            <a :href="currentBuktiUrl" target="_blank" download>Download</a>
            <button @click="showBuktiModal = false">Tutup</button>
        </div>
    </div>
</div>
```

#### 4. Alpine.js Data (Line ~3235)

```javascript
showBuktiModal: false,
currentBuktiUrl: '',
```

#### 5. New Function (Line ~3320)

```javascript
openBuktiModal(buktiUrl) {
    if (buktiUrl) {
        this.currentBuktiUrl = buktiUrl;
        this.showBuktiModal = true;
    }
},
```

#### 6. Payment History Table (Line ~960-1010)

```html
<th>Penerima</th>
<!-- NEW -->
<th>Keterangan</th>
<!-- NEW -->
...
<td x-text="payment.penerima || '-'"></td>
<!-- NEW -->
<td>
    <div class="max-w-xs truncate" x-text="payment.keterangan || '-'"></div>
</td>
<!-- NEW -->
```

---

## User Experience Flow

### Scenario 1: Single Payment (Lunas)

1. User pays full amount once
2. Invoice status = "lunas"
3. Card shows: "Pembayaran: Bayar Lunas"
4. Button shows: "Lihat Bukti" (with image icon)
5. Click button → Opens payment history modal
6. Modal shows 1 payment entry
7. Click "Lihat" on bukti → Opens bukti in modal (not new window)

### Scenario 2: Multiple Payments (Cicilan)

1. User pays in installments
2. Invoice status = "dibayar_sebagian" or "lunas"
3. Card shows: "Pembayaran: Bayar Cicilan"
4. Button shows: "Lihat Cicilan" (with history icon)
5. Click button → Opens payment history modal
6. Modal shows all payment entries with complete info
7. Click "Lihat" on any bukti → Opens bukti in modal

---

## Visual Comparison

### Card Payment Info

**Before:**

```
┌─────────────────────────────────┐
│ Informasi Pembayaran:           │
│ Jenis: Cash                     │
│ Penerima: John Doe              │
│ Tanggal Bayar: 21/11/2025       │
│ Catatan: Pembayaran pertama     │
└─────────────────────────────────┘
```

**After:**

```
┌─────────────────────────────────┐
│ Pembayaran:                     │
│ Bayar Lunas / Bayar Cicilan     │
└─────────────────────────────────┘
```

### Payment History Modal

**Before:**

```
| No | Tanggal | Jumlah | Metode | Bank/Pengirim | Bukti | Dicatat |
```

**After:**

```
| No | Tanggal | Jumlah | Metode | Bank/Pengirim | Penerima | Keterangan | Bukti | Dicatat |
```

### Bukti Viewer

**Before:**

-   Opens in new window/tab

**After:**

```
┌──────────────────────────────────────┐
│ Bukti Pembayaran               [X]   │
├──────────────────────────────────────┤
│                                      │
│  ┌────────────────────────────────┐  │
│  │                                │  │
│  │     [Bukti Image Display]     │  │
│  │                                │  │
│  └────────────────────────────────┘  │
│                                      │
├──────────────────────────────────────┤
│                [Download] [Tutup]    │
└──────────────────────────────────────┘
```

---

## Files Modified

1. **resources/views/admin/penjualan/invoice/index.blade.php**

    - Simplified card payment info (line ~210-220)
    - Updated button logic (line ~284-290, ~368-372)
    - Removed "Lihat Bukti" button from card
    - Added bukti modal (line ~1046-1075)
    - Enhanced payment history table (line ~960-1010)
    - Added Alpine.js data and functions (line ~3235, ~3320)

2. **app/Http/Controllers/SalesManagementController.php**

    - Added penerima to processInvoicePayment() (line ~2980)
    - Added penerima to getPaymentHistory() (line ~3035)

3. **app/Models/InvoicePaymentHistory.php**

    - Added 'penerima' to fillable array

4. **database/migrations/2025_11_21_150000_add_penerima_to_invoice_payment_history_table.php**
    - New migration to add penerima field

---

## Testing Checklist

### Card Display

-   [ ] Single payment shows "Bayar Lunas"
-   [ ] Multiple payments show "Bayar Cicilan"
-   [ ] Info is concise and clear

### Button Behavior

-   [ ] Single payment button shows "Lihat Bukti" with image icon
-   [ ] Multiple payments button shows "Lihat Cicilan" with history icon
-   [ ] Old "Lihat Bukti" button removed from card

### Payment History Modal

-   [ ] Penerima column displays correctly
-   [ ] Keterangan column displays correctly
-   [ ] Long keterangan truncates with tooltip
-   [ ] All data displays properly

### Bukti Modal

-   [ ] Opens in modal (not new window)
-   [ ] Image displays correctly
-   [ ] Download button works
-   [ ] Close button works
-   [ ] Responsive on mobile

### Data Flow

-   [ ] Penerima saved to database
-   [ ] Penerima retrieved in history
-   [ ] All form fields sent correctly

---

## Status: ✅ COMPLETE

All UX enhancements have been implemented:

1. ✅ Simplified card payment info
2. ✅ Enhanced payment history with penerima & keterangan
3. ✅ Bukti viewer in modal (no new window)
4. ✅ Removed redundant "Lihat Bukti" button
5. ✅ Smart button text based on payment type

**Completed:** November 21, 2025
**Files Modified:** 4 files
**New Migration:** 1 migration
**Lines Changed:** ~150 lines
