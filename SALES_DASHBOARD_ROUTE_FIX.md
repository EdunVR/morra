# Sales Dashboard - Route Name Fix

## Issue

Multiple errors related to undefined routes after restructuring penjualan routes into admin group.

## Root Cause

When penjualan routes were moved into admin group, all route names changed from `penjualan.*` to `admin.penjualan.*`, but old views were still using the old route names.

## Errors Fixed

### 1. Route [penjualan.data] not defined

**File:** `resources/views/penjualan/index.blade.php`

```php
// Before
url: '{{ route('penjualan.data') }}'

// After
url: '{{ route('admin.penjualan.data') }}'
```

### 2. Sidebar Menu Routes

**File:** `resources/views/partials/sidebar/sales.blade.php`

```php
// Before
route('penjualan.index')

// After
route('admin.penjualan.index')
```

**File:** `resources/views/components/sidebar.blade.php`

```php
// Before
route('penjualan.laporan.index')
route('penjualan.margin.index')
route('penjualan.agen_gerobak.index')
route('penjualan.agen.index')

// After
route('admin.penjualan.laporan.index')
route('admin.penjualan.margin.index')
route('admin.penjualan.agen_gerobak.index')
route('admin.penjualan.agen.index')
```

### 3. Invoice View API Calls

**File:** `resources/views/admin/penjualan/invoice/index.blade.php`

```php
// Before
route('penjualan.customers')
route('penjualan.customer-price.edit')

// After
route('admin.penjualan.customers')
route('admin.penjualan.customer-price.edit')
```

### 4. Dashboard API Route

**File:** `resources/views/admin/penjualan/index.blade.php`

```php
// Before
route('penjualan.dashboard.data')

// After
route('admin.penjualan.dashboard.data')
```

## Route Structure Changes

### Before

```
penjualan/
├── / → PenjualanController@index
├── /data → PenjualanController@data
├── /pos → PosController@index
├── /laporan-penjualan → SalesReportController@index
└── /laporan-margin → MarginReportController@index
```

### After

```
admin/penjualan/
├── / → PenjualanController@index (old)
├── /dashboard → SalesDashboardController@index (new)
├── /dashboard/data → SalesDashboardController@getData
├── /data → PenjualanController@data
├── /pos → PosController@index
├── /laporan-penjualan → SalesReportController@index
└── /laporan-margin → MarginReportController@index
```

## Route Name Mapping

| Old Route Name                  | New Route Name                        | URL                                    |
| ------------------------------- | ------------------------------------- | -------------------------------------- |
| `penjualan.index`               | `admin.penjualan.index`               | `/admin/penjualan`                     |
| `penjualan.data`                | `admin.penjualan.data`                | `/admin/penjualan/data`                |
| `penjualan.dashboard.index`     | `admin.penjualan.dashboard.index`     | `/admin/penjualan/dashboard`           |
| `penjualan.dashboard.data`      | `admin.penjualan.dashboard.data`      | `/admin/penjualan/dashboard/data`      |
| `penjualan.pos.index`           | `admin.penjualan.pos.index`           | `/admin/penjualan/pos`                 |
| `penjualan.laporan.index`       | `admin.penjualan.laporan.index`       | `/admin/penjualan/laporan-penjualan`   |
| `penjualan.margin.index`        | `admin.penjualan.margin.index`        | `/admin/penjualan/laporan-margin`      |
| `penjualan.customers`           | `admin.penjualan.customers`           | `/admin/penjualan/customers`           |
| `penjualan.customer-price.edit` | `admin.penjualan.customer-price.edit` | `/admin/penjualan/customer-price/{id}` |

## Files Modified

1. ✅ `resources/views/penjualan/index.blade.php`
2. ✅ `resources/views/partials/sidebar/sales.blade.php`
3. ✅ `resources/views/components/sidebar.blade.php`
4. ✅ `resources/views/admin/penjualan/invoice/index.blade.php`
5. ✅ `resources/views/admin/penjualan/index.blade.php`
6. ✅ `routes/web.php` (admin group closing bracket added)

## Testing Checklist

### Routes

-   ✅ `php artisan route:list` shows no errors
-   ✅ All penjualan routes have `admin.penjualan.*` prefix
-   ✅ Dashboard route accessible at `/admin/penjualan/dashboard`

### Views

-   ✅ Old penjualan index loads without error
-   ✅ New dashboard loads without error
-   ✅ Sidebar menus work correctly
-   ✅ Invoice page loads without error
-   ✅ All API calls use correct route names

### Functionality

-   ✅ Dashboard displays data
-   ✅ Filters work
-   ✅ Charts render
-   ✅ Table shows transactions
-   ✅ Invoice page functions normally

## Commands Run

```bash
# Clear caches
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Verify routes
php artisan route:list --name=penjualan
php artisan route:list --name=admin.penjualan
```

## Prevention

To avoid similar issues in the future:

1. **Use route() helper consistently** - Always use `route('name')` instead of hardcoded URLs
2. **Update all references** - When changing route structure, search and replace all occurrences
3. **Test after changes** - Run `php artisan route:list` to verify routes
4. **Clear caches** - Always clear view and route caches after route changes
5. **Document changes** - Keep track of route name changes in migration docs

## Search Patterns Used

To find all occurrences:

```bash
# Find old route names
grep -r "route('penjualan\." resources/views/

# Find specific routes
grep -r "penjualan\.data" resources/views/
grep -r "penjualan\.customers" resources/views/
```

## PowerShell Commands Used

```powershell
# Replace in file
(Get-Content file.blade.php) -replace "old", "new" | Set-Content file.blade.php

# Examples
(Get-Content resources/views/admin/penjualan/invoice/index.blade.php) -replace "route\('penjualan\.customers'\)", "route('admin.penjualan.customers')" | Set-Content resources/views/admin/penjualan/invoice/index.blade.php
```

## Status

✅ **FIXED** - All route names updated and working correctly

---

**Date:** December 1, 2024
**Impact:** High - Affects all penjualan module pages
**Priority:** Critical - Blocking access to pages
