# POS Route Order Fix - SOLVED

## Critical Problem

Route `/penjualan/pos/coa-settings` returning 404 error because of incorrect route order in `routes/web.php`.

## Root Cause

Laravel matches routes in the order they are defined. When `/pos/coa-settings` was placed AFTER `/pos/{id}`, Laravel matched `coa-settings` as the `{id}` parameter and routed to `PosController@show` instead of `PosController@coaSettings`.

## The Issue

```php
// ❌ WRONG ORDER - coa-settings matched as {id}
Route::get('/pos/{id}', [PosController::class, 'show'])->name('pos.show');
Route::get('/pos/coa-settings', [PosController::class, 'coaSettings'])->name('pos.coa.settings');
```

## The Solution

```php
// ✅ CORRECT ORDER - specific routes before dynamic routes
Route::get('/pos/coa-settings', [PosController::class, 'coaSettings'])->name('pos.coa.settings');
Route::get('/pos/{id}', [PosController::class, 'show'])->name('pos.show');
```

## Complete Route Order (Correct)

```php
Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');
Route::get('/pos/customers', [PosController::class, 'getCustomers'])->name('pos.customers');
Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');
Route::get('/pos/history-data', [PosController::class, 'historyData'])->name('pos.history.data');
Route::get('/pos/coa-settings', [PosController::class, 'coaSettings'])->name('pos.coa.settings');
Route::post('/pos/coa-settings', [PosController::class, 'coaSettings'])->name('pos.coa.settings.update');
Route::get('/pos/{id}', [PosController::class, 'show'])->name('pos.show');
Route::get('/pos/{id}/print', [PosController::class, 'print'])->name('pos.print');
```

## Rule of Thumb

**Always place specific routes BEFORE dynamic routes with parameters.**

### Examples:

```php
// ✅ CORRECT
Route::get('/users/create', ...);
Route::get('/users/{id}', ...);

// ❌ WRONG
Route::get('/users/{id}', ...);
Route::get('/users/create', ...);  // Will never match!
```

## Files Modified

-   `routes/web.php` - Reordered POS routes

## Cache Cleared

```bash
php artisan route:clear
php artisan view:clear
```

## Verification

```bash
php artisan route:list --name=penjualan.pos.coa
```

Output:

```
GET|HEAD   penjualan/pos/coa-settings ........ penjualan.pos.coa.settings › PosController@coaSettings
POST       penjualan/pos/coa-settings .. penjualan.pos.coa.settings.update › PosController@coaSettings
```

## Status

✅ **RESOLVED** - Route order fixed, COA settings endpoint now accessible
