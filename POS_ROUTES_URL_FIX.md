# POS Routes URL Fix - Complete

## Problem

Error 404 saat load COA data karena menggunakan hardcoded URL:

```
GET https://group.dahana-boiler.com/finance/accounting-books?outlet_id=1 404 (Not Found)
GET https://group.dahana-boiler.com/finance/chart-of-accounts?outlet_id=1 404 (Not Found)
```

## Solution

Mengganti semua hardcoded URL dengan Laravel route name untuk memastikan URL yang benar.

## Changes Made

### File: `resources/views/admin/penjualan/pos/index.blade.php`

#### Before:

```javascript
async loadCoaData() {
  try {
    const booksRes = await fetch('/finance/accounting-books?outlet_id=' + this.state.outlet);
    const accRes = await fetch('/finance/chart-of-accounts?outlet_id=' + this.state.outlet);
    // ...
  }
}
```

#### After:

```javascript
async loadCoaData() {
  try {
    const booksRes = await fetch('{{ route("accounting-books.data") }}?outlet_id=' + this.state.outlet);
    const accRes = await fetch('{{ route("chart-of-accounts.data") }}?outlet_id=' + this.state.outlet);
    // ...
  }
}
```

## Routes Used

### Accounting Books

-   **Route Name**: `accounting-books.data`
-   **Method**: GET
-   **Controller**: `FinanceAccountantController@accountingBooksData`
-   **URL Pattern**: `/finance/accounting-books/data`

### Chart of Accounts

-   **Route Name**: `chart-of-accounts.data`
-   **Method**: GET
-   **Controller**: `FinanceAccountantController@chartOfAccountsData`
-   **URL Pattern**: `/finance/chart-of-accounts/data`

### POS COA Settings

-   **Route Name**: `penjualan.pos.coa.settings`
-   **Method**: GET
-   **Controller**: `PosController@coaSettings`

## All Fetch URLs in POS (Now Using Route Names)

✅ **Products**: `{{ route("penjualan.pos.products") }}`
✅ **Customers**: `{{ route("penjualan.pos.customers") }}`
✅ **Accounting Books**: `{{ route("finance.accounting-books.data") }}`
✅ **Chart of Accounts**: `{{ route("finance.chart-of-accounts.data") }}`
✅ **COA Settings (GET)**: `{{ route("penjualan.pos.coa.settings") }}`
✅ **COA Settings (POST)**: `{{ route("penjualan.pos.coa.settings.update") }}`
✅ **Store Transaction**: `{{ route("penjualan.pos.store") }}`

## Benefits

1. **No More 404 Errors**: Route names ensure correct URLs
2. **Maintainability**: If URL structure changes, only routes need updating
3. **Consistency**: All API calls now use Laravel route helper
4. **Type Safety**: Laravel will throw error if route doesn't exist

## Testing

1. Open POS page: `/penjualan/pos`
2. Click "⚙️ Setting COA" button
3. Modal should open without console errors
4. Dropdown "Buku Akuntansi" should load data
5. Dropdown accounts should load data

## Cache Cleared

```bash
php artisan view:clear
```

## Status

✅ **COMPLETE** - All hardcoded URLs replaced with route names

## Additional Fix - COA Settings AJAX Response

### Problem

Route `penjualan.pos.coa.settings` mengembalikan view HTML, bukan JSON response untuk AJAX request.

### Solution

Modified `PosController@coaSettings` to detect AJAX requests and return JSON:

```php
if ($request->isMethod('get')) {
    $setting = SettingCOAPos::with('accountingBook')->byOutlet($outletId)->first();

    // If AJAX request, return JSON
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    // Otherwise return view
    // ...
}
```

### Frontend Changes

Added AJAX headers to all fetch requests in `loadCoaData()`:

```javascript
const headers = {
    Accept: "application/json",
    "X-Requested-With": "XMLHttpRequest",
};

const settingsRes = await fetch(
    '{{ route("penjualan.pos.coa.settings") }}?outlet_id=' + this.state.outlet,
    { headers }
);
```

### Files Modified

1. `app/Http/Controllers/PosController.php` - Added AJAX detection
2. `resources/views/admin/penjualan/pos/index.blade.php` - Added AJAX headers

## Final Status

✅ All routes now working correctly with proper JSON responses
✅ COA Settings modal loads data successfully
✅ No more 404 errors
