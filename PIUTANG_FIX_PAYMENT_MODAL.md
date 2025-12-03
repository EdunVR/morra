# Fix: Error "showPaymentHistory is not a function"

## 🐛 Problem

Saat klik tombol "Bayar" di halaman Piutang dan diarahkan ke halaman PO, muncul error:

```
Uncaught (in promise) TypeError: this.showPaymentHistory is not a function
at purchase-order?po_id=3&open_payment=1:3831:28
```

## 🔍 Root Cause

Nama fungsi yang salah. Di halaman Purchase Order, fungsi yang benar adalah:

-   ✅ `viewPaymentHistory(po)`
-   ❌ `showPaymentHistory(po)` (tidak ada)

## ✅ Solution

Update pemanggilan fungsi di `resources/views/admin/pembelian/purchase-order/index.blade.php`

**Sebelum:**

```javascript
await this.showPaymentHistory(po);
```

**Sesudah:**

```javascript
await this.viewPaymentHistory(po);
```

## 📁 File Changed

-   `resources/views/admin/pembelian/purchase-order/index.blade.php` (line ~2710)

## 🧪 Testing

### Test Steps:

1. Buka halaman Piutang: `/finance/piutang`
2. Klik tombol "Bayar" pada salah satu piutang
3. **Expected:** Redirect ke halaman PO
4. Tunggu 1-2 detik
5. **Expected:** Modal "Riwayat Pembayaran" terbuka otomatis
6. **Expected:** Tidak ada error di console

### Success Criteria:

-   ✅ Redirect berhasil
-   ✅ Modal terbuka otomatis
-   ✅ Data PO ditampilkan dengan benar
-   ✅ Tidak ada JavaScript error
-   ✅ URL parameters di-clean setelah modal terbuka

## 🎯 Status

**FIXED** ✅

Error sudah diperbaiki dan fitur berfungsi dengan baik.

---

**Fixed Date:** November 24, 2025  
**Issue:** TypeError - function not found  
**Solution:** Correct function name from `showPaymentHistory` to `viewPaymentHistory`
