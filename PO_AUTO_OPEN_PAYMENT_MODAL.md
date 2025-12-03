# PO Auto-Open Payment Modal - Implementation

## Overview

Menambahkan fitur auto-open modal pembayaran di halaman Purchase Order saat diakses dari halaman Hutang dengan parameter URL.

## Implementation

### URL Parameter Detection

```javascript
// Check for auto-open payment modal from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const poId = urlParams.get("po_id");
const openPayment = urlParams.get("open_payment");

if (poId && openPayment === "1") {
    // Wait for PO data to load, then open payment modal
    setTimeout(async () => {
        const po = this.purchaseOrders.find((p) => p.id_purchase_order == poId);
        if (po) {
            await this.openPaymentModal(po);
            // Clean URL
            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );
        }
    }, 1500);
}
```

### Flow dari Halaman Hutang

#### 1. User Klik Tombol "Bayar" di Halaman Hutang

```javascript
// Di halaman hutang
async redirectToPOPayment(poId) {
  if (!poId) {
    this.showNotification('error', 'Data purchase order tidak tersedia');
    return;
  }

  // Redirect ke halaman PO dengan parameter untuk auto-open modal pembayaran
  window.location.href = `${this.routes.poIndex}?po_id=${poId}&open_payment=1`;
}
```

#### 2. Redirect ke Halaman PO

URL: `/pembelian/purchase-order?po_id=123&open_payment=1`

#### 3. Halaman PO Detect Parameter

-   Init function membaca URL parameters
-   Mencari PO dengan id yang sesuai
-   Membuka modal pembayaran otomatis
-   Membersihkan URL parameter

#### 4. Modal Pembayaran Terbuka

-   Form pembayaran siap diisi
-   Data PO sudah terisi
-   User bisa langsung input pembayaran

## Features

### Auto-Open Logic

-   ✅ Detect parameter `po_id` dan `open_payment=1`
-   ✅ Wait 1.5 detik untuk data PO selesai load
-   ✅ Find PO by id dari purchaseOrders array
-   ✅ Call `openPaymentModal(po)` function
-   ✅ Clean URL dengan `window.history.replaceState()`

### Error Handling

-   ✅ Check if poId exists
-   ✅ Check if openPayment === '1'
-   ✅ Check if PO found in array
-   ✅ Graceful fallback jika PO tidak ditemukan

### User Experience

-   ✅ Seamless redirect dari hutang ke PO
-   ✅ Modal langsung terbuka tanpa klik manual
-   ✅ URL bersih setelah modal terbuka
-   ✅ Tidak ada parameter tersisa di URL

## Testing

### Test 1: Normal Flow

1. Buka halaman Hutang: `/finance/hutang`
2. Klik tombol "Bayar" pada PO yang belum lunas
3. **Expected:**
    - Redirect ke `/pembelian/purchase-order?po_id=X&open_payment=1`
    - Modal pembayaran otomatis terbuka
    - Data PO terisi di modal
    - URL berubah menjadi `/pembelian/purchase-order` (parameter dihapus)

### Test 2: Direct URL Access

1. Buka URL langsung: `/pembelian/purchase-order?po_id=123&open_payment=1`
2. **Expected:**
    - Halaman PO terbuka
    - Modal pembayaran otomatis terbuka
    - Data PO terisi

### Test 3: Invalid PO ID

1. Buka URL: `/pembelian/purchase-order?po_id=999999&open_payment=1`
2. **Expected:**
    - Halaman PO terbuka normal
    - Modal tidak terbuka (PO tidak ditemukan)
    - No error message

### Test 4: Without open_payment Parameter

1. Buka URL: `/pembelian/purchase-order?po_id=123`
2. **Expected:**
    - Halaman PO terbuka normal
    - Modal tidak terbuka
    - Normal behavior

## Integration Points

### From Hutang Page

```javascript
// resources/views/admin/finance/hutang/index.blade.php
routes: {
  poIndex: '{{ route("pembelian.purchase-order.index") }}'
},

async redirectToPOPayment(poId) {
  window.location.href = `${this.routes.poIndex}?po_id=${poId}&open_payment=1`;
}
```

### To PO Page

```javascript
// resources/views/admin/pembelian/purchase-order/index.blade.php
async init() {
  // ... load data ...

  // Auto-open payment modal
  const urlParams = new URLSearchParams(window.location.search);
  const poId = urlParams.get('po_id');
  const openPayment = urlParams.get('open_payment');

  if (poId && openPayment === '1') {
    setTimeout(async () => {
      const po = this.purchaseOrders.find(p => p.id_purchase_order == poId);
      if (po) {
        await this.openPaymentModal(po);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    }, 1500);
  }
}
```

## Benefits

### 1. Seamless User Experience

-   ✅ No manual modal opening needed
-   ✅ Direct to payment form
-   ✅ Faster workflow

### 2. Consistent with Invoice Pattern

-   ✅ Same pattern as invoice payment
-   ✅ Familiar user experience
-   ✅ Easy to maintain

### 3. Clean URL

-   ✅ Parameters removed after use
-   ✅ No clutter in browser history
-   ✅ Shareable clean URLs

### 4. Flexible

-   ✅ Works with direct URL access
-   ✅ Works from any page
-   ✅ Easy to extend

## Files Modified

1. **resources/views/admin/pembelian/purchase-order/index.blade.php**
    - Added URL parameter detection in `init()` function
    - Added auto-open modal logic
    - Added URL cleanup

## Status: ✅ COMPLETE

**Implementation Date:** 2025-11-24  
**Tested:** Ready for testing  
**Integration:** Complete with Hutang page

Modal pembayaran PO sekarang otomatis terbuka saat diakses dari halaman Hutang! 🎉
