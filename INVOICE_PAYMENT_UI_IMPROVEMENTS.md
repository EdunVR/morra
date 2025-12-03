# Invoice Payment UI Improvements 🎨

## Changes Made

### 1. ✅ Added "Jumlah Bayar" Field for Cash Payments

**Problem:** Saat pilih Cash, tidak ada form untuk mengisi jumlah bayar

**Solution:**

-   Tambahkan field "Jumlah Bayar" yang selalu muncul untuk Cash dan Transfer
-   Field ini berada di luar section transfer, jadi visible untuk semua jenis pembayaran
-   Dilengkapi dengan tombol "Lunas" untuk quick-fill full amount

**Features:**

-   Input number dengan step 0.01 untuk desimal
-   Tombol "Lunas" untuk auto-fill total invoice
-   Menampilkan total invoice sebagai referensi
-   Default value = sisa tagihan (untuk cicilan)

**Location:** Line ~743-760 in `resources/views/admin/penjualan/invoice/index.blade.php`

---

### 2. ✅ Added "Lihat Cicilan" Button & Modal

**Problem:** Tidak ada cara untuk melihat history pembayaran cicilan dan bukti setiap cicilan

**Solution:**

-   Tambahkan tombol "Lihat Cicilan" di action buttons invoice list
-   Tombol muncul jika `total_dibayar > 0` (ada pembayaran)
-   Buat modal baru untuk menampilkan payment history

**Button Features:**

-   Icon: `bx-history`
-   Color: Purple (bg-purple-100 text-purple-700)
-   Tooltip: "Lihat Cicilan"
-   Muncul di card view dan table view

**Modal Features:**

-   Invoice summary (no invoice, status, total, dibayar, sisa)
-   Payment history table dengan kolom:
    -   No urut
    -   Tanggal bayar
    -   Jumlah bayar
    -   Metode pembayaran (Cash/Transfer)
    -   Bank/Pengirim (untuk transfer)
    -   Bukti pembayaran (link "Lihat Bukti")
    -   Dicatat oleh (user)
-   Informasi box dengan tips
-   Responsive design

**Location:** Line ~910-1040 in `resources/views/admin/penjualan/invoice/index.blade.php`

---

## UI Changes Summary

### Action Buttons Updated

#### Before:

```
[Print] [Edit] [Tandai Lunas] [Batalkan] [Hapus]
```

#### After:

```
[Print] [Edit] [Bayar] [Lihat Cicilan] [Batalkan] [Hapus]
```

**Button Logic:**

-   **Bayar**: Muncul jika status = "menunggu" atau "dibayar_sebagian"
-   **Lihat Cicilan**: Muncul jika total_dibayar > 0 (ada riwayat pembayaran)
-   **Batalkan**: Tetap hanya untuk status "menunggu"

---

## JavaScript Functions Added

### 1. openPaymentModal(invoiceId)

```javascript
// Opens payment modal with pre-filled data
// Sets jumlah_transfer to remaining balance (sisa_tagihan)
// Resets all form fields
```

### 2. openPaymentHistoryModal(invoiceId)

```javascript
// Opens payment history modal
// Fetches payment history from API
// Displays invoice summary and payment list
```

### 3. viewPaymentBukti(buktiUrl)

```javascript
// Opens bukti pembayaran in new tab
// Called when user clicks "Lihat Bukti" in history table
```

---

## API Integration

### Endpoint Used:

```
GET /penjualan/invoice/{id}/payment-history
Route: penjualan.invoice.payment.history
```

### Response Format:

```json
{
    "success": true,
    "data": {
        "invoice": {
            "no_invoice": "001/PBU/INV/XI/2025",
            "total": 1000000,
            "total_dibayar": 500000,
            "sisa_tagihan": 500000,
            "status": "dibayar_sebagian"
        },
        "payment_history": [
            {
                "id": 1,
                "tanggal_bayar": "21/11/2025",
                "jumlah_bayar": 500000,
                "jenis_pembayaran": "cash",
                "nama_bank": null,
                "nama_pengirim": null,
                "bukti_pembayaran": "http://...storage/bukti_pembayaran/...",
                "keterangan": "Pembayaran pertama",
                "created_by": "Admin User",
                "created_at": "21/11/2025 10:30"
            }
        ]
    }
}
```

---

## Alpine.js Data Added

```javascript
showPaymentHistoryModal: false,
paymentHistoryData: {
    invoice: null,
    payment_history: []
}
```

---

## Visual Design

### Payment History Modal

```
┌─────────────────────────────────────────────────┐
│ Riwayat Pembayaran Cicilan                  [X] │
├─────────────────────────────────────────────────┤
│                                                 │
│ ┌─────────────────────────────────────────────┐ │
│ │ Invoice Summary (Gray Background)          │ │
│ │ No: 001/PBU/INV/XI/2025  Status: Sebagian  │ │
│ │ Total: 1,000,000  Dibayar: 500,000         │ │
│ │ Sisa: 500,000                              │ │
│ └─────────────────────────────────────────────┘ │
│                                                 │
│ ┌─────────────────────────────────────────────┐ │
│ │ Payment History Table                      │ │
│ │ ┌──┬────────┬─────────┬────────┬─────────┐ │ │
│ │ │No│Tanggal │Jumlah   │Metode  │Bukti    │ │ │
│ │ ├──┼────────┼─────────┼────────┼─────────┤ │ │
│ │ │1 │21/11/25│500,000  │Cash    │[Lihat]  │ │ │
│ │ └──┴────────┴─────────┴────────┴─────────┘ │ │
│ └─────────────────────────────────────────────┘ │
│                                                 │
│ ┌─────────────────────────────────────────────┐ │
│ │ ℹ️ Informasi (Blue Box)                     │ │
│ │ • Setiap cicilan tercatat dengan bukti     │ │
│ │ • Klik "Lihat Bukti" untuk detail          │ │
│ └─────────────────────────────────────────────┘ │
│                                                 │
├─────────────────────────────────────────────────┤
│                                    [Tutup]      │
└─────────────────────────────────────────────────┘
```

---

## Color Scheme

### Status Colors:

-   **menunggu**: Yellow (bg-yellow-100 text-yellow-800)
-   **dibayar_sebagian**: Blue (bg-blue-100 text-blue-800)
-   **lunas**: Green (bg-green-100 text-green-800)
-   **gagal**: Red (bg-red-100 text-red-800)

### Payment Method Colors:

-   **Cash**: Green (bg-green-100 text-green-800)
-   **Transfer**: Blue (bg-blue-100 text-blue-800)

### Button Colors:

-   **Bayar**: Emerald (bg-emerald-100 text-emerald-700)
-   **Lihat Cicilan**: Purple (bg-purple-100 text-purple-700)

---

## Responsive Design

### Desktop (>768px):

-   Modal width: max-w-4xl
-   Table: Full width with all columns
-   Grid: 3 columns for summary

### Mobile (<768px):

-   Modal: Full width with padding
-   Table: Horizontal scroll
-   Grid: Stacked layout

---

## User Flow

### Scenario 1: Make Payment

1. User clicks "Bayar" button on invoice
2. Payment modal opens with pre-filled data
3. User selects payment type (Cash/Transfer)
4. User enters amount (or clicks "Lunas")
5. User uploads bukti pembayaran
6. User clicks "Konfirmasi Pelunasan"
7. Payment recorded, status updated

### Scenario 2: View Payment History

1. User clicks "Lihat Cicilan" button
2. Payment history modal opens
3. System loads payment history from API
4. User sees invoice summary and payment list
5. User can click "Lihat Bukti" to view proof
6. User clicks "Tutup" to close modal

---

## Files Modified

1. **resources/views/admin/penjualan/invoice/index.blade.php**
    - Added "Jumlah Bayar" field (line ~743-760)
    - Updated action buttons (line ~284-293, ~378-390)
    - Added payment history modal (line ~910-1040)
    - Added JavaScript functions (line ~3227-3280)
    - Added Alpine.js data (line ~3203-3208)

---

## Testing Checklist

### Jumlah Bayar Field

-   [ ] Field muncul untuk Cash
-   [ ] Field muncul untuk Transfer
-   [ ] Tombol "Lunas" berfungsi
-   [ ] Default value = sisa tagihan
-   [ ] Validation works (min 0.01)

### Lihat Cicilan Button

-   [ ] Muncul jika ada pembayaran
-   [ ] Tidak muncul jika belum ada pembayaran
-   [ ] Icon dan warna sesuai
-   [ ] Tooltip muncul on hover

### Payment History Modal

-   [ ] Modal opens correctly
-   [ ] Invoice summary displayed
-   [ ] Payment history table populated
-   [ ] "Lihat Bukti" links work
-   [ ] Empty state displayed if no payments
-   [ ] Modal closes correctly
-   [ ] Responsive on mobile

---

## Status: ✅ COMPLETE

All UI improvements have been implemented and ready for testing.

**Completed:** November 21, 2025
**Files Modified:** 1 file
**Lines Added:** ~200 lines
**New Functions:** 3 functions
**New Modal:** 1 modal
