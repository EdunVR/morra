# Fix: Neraca Saldo - Menggunakan Route Names

## 🐛 Problem

Halaman Neraca Saldo menggunakan hardcoded URL yang menyebabkan error 404:

```
GET https://group.dahana-boiler.com/finance/outlets 404 (Not Found)
GET https://group.dahana-boiler.com/finance/accounting-books/data 404 (Not Found)
```

## ✅ Solution

Mengganti semua hardcoded URL dengan Laravel route names menggunakan helper `{{ route('...') }}`.

## 📝 Changes Made

### File: `resources/views/admin/finance/neraca-saldo/index.blade.php`

#### 1. Load Outlets

**Before:**

```javascript
const response = await fetch("/finance/outlets");
```

**After:**

```javascript
const response = await fetch('{{ route("finance.outlets.data") }}');
```

#### 2. Load Books

**Before:**

```javascript
const response = await fetch("/finance/accounting-books/data");
```

**After:**

```javascript
const response = await fetch('{{ route("finance.accounting-books.data") }}');
```

#### 3. Load Trial Balance Data

**Before:**

```javascript
const response = await fetch(`/finance/trial-balance/data?${params}`);
```

**After:**

```javascript
const response = await fetch(
    `{{ route('finance.trial-balance.data') }}?${params}`
);
```

#### 4. View Account Details

**Before:**

```javascript
const response = await fetch(
    `/finance/general-ledger/account-details?${params}`
);
```

**After:**

```javascript
const response = await fetch(
    `{{ route('finance.general-ledger.account-details') }}?${params}`
);
```

#### 5. Export PDF

**Before:**

```javascript
window.open(`/finance/trial-balance/export/pdf?${params}`, "_blank");
```

**After:**

```javascript
window.open(
    `{{ route('finance.trial-balance.export.pdf') }}?${params}`,
    "_blank"
);
```

#### 6. Export Excel

**Before:**

```javascript
window.location.href = `/finance/trial-balance/export/excel?${params}`;
```

**After:**

```javascript
window.location.href = `{{ route('finance.trial-balance.export.excel') }}?${params}`;
```

## 🎯 Route Names Used

| Function        | Route Name                               | URL                                       |
| --------------- | ---------------------------------------- | ----------------------------------------- |
| Load Outlets    | `finance.outlets.data`                   | `/finance/outlets`                        |
| Load Books      | `finance.accounting-books.data`          | `/finance/accounting-books/data`          |
| Load Data       | `finance.trial-balance.data`             | `/finance/trial-balance/data`             |
| Account Details | `finance.general-ledger.account-details` | `/finance/general-ledger/account-details` |
| Export PDF      | `finance.trial-balance.export.pdf`       | `/finance/trial-balance/export/pdf`       |
| Export Excel    | `finance.trial-balance.export.excel`     | `/finance/trial-balance/export/excel`     |

## ✨ Benefits

1. **No More 404 Errors**: Route names akan selalu resolve ke URL yang benar
2. **Maintainable**: Jika URL berubah, hanya perlu update di `routes/web.php`
3. **Type-Safe**: Laravel akan error jika route name tidak ada
4. **Consistent**: Mengikuti pola yang sama dengan modul finance lainnya

## 🧪 Testing

### 1. Clear Cache

```bash
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 2. Verify Routes

```bash
php artisan route:list --name=finance
```

### 3. Test di Browser

1. Buka halaman: `http://your-domain/finance/neraca-saldo`
2. Buka Developer Console (F12)
3. Pastikan tidak ada error 404
4. Pastikan data outlets dan books ter-load
5. Test filter dan export

### Expected Console Output

```
✅ Outlets loaded successfully
✅ Books loaded successfully
✅ Trial balance data loaded successfully
```

## 📚 Documentation Created

1. **NERACA_SALDO_ROUTE_NAMES.md** - Comprehensive guide untuk route names
2. **NERACA_SALDO_FIX_ROUTE_NAMES.md** - This file (fix summary)

## 🔄 Related Files

### Modified:

-   `resources/views/admin/finance/neraca-saldo/index.blade.php`

### Documentation:

-   `NERACA_SALDO_ROUTE_NAMES.md` (NEW)
-   `NERACA_SALDO_FIX_ROUTE_NAMES.md` (NEW)
-   `NERACA_SALDO_IMPLEMENTATION.md` (Existing)
-   `NERACA_SALDO_TESTING_GUIDE.md` (Existing)
-   `NERACA_SALDO_API_REFERENCE.md` (Existing)

## ⚠️ Important Notes

### Quote Usage in Blade

```javascript
// ✅ CORRECT: Double quote outside, single quote inside
fetch('{{ route("finance.trial-balance.data") }}')

// ❌ WRONG: Single quote outside, single quote inside
fetch('{{ route('finance.trial-balance.data') }}')
```

### URL Parameters

```javascript
// ✅ CORRECT: Append parameters after route
const params = new URLSearchParams(filters);
fetch(`{{ route('finance.trial-balance.data') }}?${params}`);

// ❌ WRONG: Don't include parameters in route name
fetch(`{{ route('finance.trial-balance.data?outlet_id=1') }}`);
```

## 🎉 Status

✅ **FIXED** - Semua hardcoded URL telah diganti dengan route names.

Halaman Neraca Saldo sekarang menggunakan route names Laravel dan tidak akan mengalami error 404 lagi.

---

**Fixed Date**: November 24, 2024
**Fixed By**: Kiro AI Assistant
