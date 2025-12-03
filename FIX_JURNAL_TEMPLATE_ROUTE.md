# Fix: Route [finance.jurnal.template] Not Defined

## Problem

Error occurred when accessing the Journal List page:

```
Route [finance.jurnal.template] not defined.
```

## Root Cause

The view `resources/views/admin/finance/jurnal/index.blade.php` was using an incorrect route name:

-   **Incorrect**: `finance.jurnal.template`
-   **Correct**: `finance.journals.template`

The route was defined in `routes/web.php` under the `journals` prefix (plural), but the view was referencing `jurnal` (singular).

## Solution Applied

### Changed in `resources/views/admin/finance/jurnal/index.blade.php`

**Line 574 - Before:**

```blade
<a href="{{ route('finance.jurnal.template') }}"
```

**Line 574 - After:**

```blade
<a href="{{ route('finance.journals.template') }}"
```

## Route Definition

The correct route is defined in `routes/web.php` (line ~453):

```php
Route::prefix('journals')->name('journals.')->group(function () {
    Route::get('export/xlsx', [FinanceAccountantController::class, 'exportJournalsXLSX'])->name('export.xlsx');
    Route::get('export/pdf', [FinanceAccountantController::class, 'exportJournalsPDF'])->name('export.pdf');
    Route::post('import', [FinanceAccountantController::class, 'importJournals'])->name('import');
    Route::get('template', [FinanceAccountantController::class, 'downloadJournalsTemplate'])->name('template');
});
```

This creates the route name: `finance.journals.template`

## Verification

All other route references in the view are correct:

-   ✅ `finance.journals.data`
-   ✅ `finance.journals.stats`
-   ✅ `finance.journals.show`
-   ✅ `finance.journals.store`
-   ✅ `finance.journals.update`
-   ✅ `finance.journals.post`
-   ✅ `finance.journals.delete`
-   ✅ `finance.journals.export.xlsx`
-   ✅ `finance.journals.export.pdf`
-   ✅ `finance.journals.import`
-   ✅ `finance.journals.template` (fixed)

## Status

✅ **FIXED** - The route name has been corrected and the error should no longer occur.
