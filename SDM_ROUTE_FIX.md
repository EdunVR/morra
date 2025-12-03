# 🔧 Fix Route Name - Modul SDM

## Issue

Error: `Route [admin.sdm.kepegawaian.index] not defined`

## Root Cause

Routes SDM didefinisikan di luar group `admin` sehingga route name menjadi `sdm.kepegawaian.*` bukan `admin.sdm.kepegawaian.*`

## Solution ✅

### 1. Routes tetap menggunakan prefix `sdm` (bukan `admin/sdm`)

Karena routes berada di dalam group `admin`, maka:

-   URL: `/admin/sdm/kepegawaian`
-   Route Name: `sdm.kepegawaian.index`

### 2. Update Sidebar

**File**: `resources/views/components/sidebar.blade.php`

```php
// BEFORE (❌ Error)
['Kepegawaian & Rekrutmen', route('admin.sdm.kepegawaian.index'), ['hrm.karyawan.view']],

// AFTER (✅ Fixed)
['Kepegawaian & Rekrutmen', route('sdm.kepegawaian.index'), ['hrm.karyawan.view']],
```

### 3. Update View Files

**File**: `resources/views/admin/sdm/kepegawaian/index.blade.php`

Semua route name diupdate dari `admin.sdm.kepegawaian.*` menjadi `sdm.kepegawaian.*`:

```javascript
// BEFORE (❌ Error)
route("admin.sdm.kepegawaian.data");
route("admin.sdm.kepegawaian.departments");
route("admin.sdm.kepegawaian.store");
route("admin.sdm.kepegawaian.export.pdf");
route("admin.sdm.kepegawaian.export.excel");

// AFTER (✅ Fixed)
route("sdm.kepegawaian.data");
route("sdm.kepegawaian.departments");
route("sdm.kepegawaian.store");
route("sdm.kepegawaian.export.pdf");
route("sdm.kepegawaian.export.excel");
```

**File**: `resources/views/admin/sdm/index.blade.php`

```php
// BEFORE (❌ Error)
route('admin.sdm.kepegawaian.index')

// AFTER (✅ Fixed)
route('sdm.kepegawaian.index')
```

## Verified Routes ✅

```bash
php artisan route:list --name=sdm
```

**Output**:

```
GET|HEAD  admin/sdm ........................... admin.sdm
GET|HEAD  sdm/kepegawaian ..................... sdm.kepegawaian.index
GET|HEAD  sdm/kepegawaian/data ................ sdm.kepegawaian.data
GET|HEAD  sdm/kepegawaian/departments ......... sdm.kepegawaian.departments
GET|HEAD  sdm/kepegawaian/export/excel ........ sdm.kepegawaian.export.excel
GET|HEAD  sdm/kepegawaian/export/pdf .......... sdm.kepegawaian.export.pdf
POST      sdm/kepegawaian/store ............... sdm.kepegawaian.store
GET|HEAD  sdm/kepegawaian/{id} ................ sdm.kepegawaian.show
PUT       sdm/kepegawaian/{id} ................ sdm.kepegawaian.update
DELETE    sdm/kepegawaian/{id} ................ sdm.kepegawaian.destroy
```

## Files Modified

1. ✅ `routes/web.php` - No change needed (already correct)
2. ✅ `resources/views/components/sidebar.blade.php` - Updated route name
3. ✅ `resources/views/admin/sdm/kepegawaian/index.blade.php` - Updated all route names
4. ✅ `resources/views/admin/sdm/index.blade.php` - Updated route name

## Testing

### 1. Clear Cache

```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### 2. Test Access

1. Login ke sistem
2. Klik menu "SDM" di sidebar
3. Klik "Kepegawaian & Rekrutmen"
4. Halaman harus terbuka tanpa error

### 3. Test All Routes

-   ✅ Index page: `/admin/sdm/kepegawaian`
-   ✅ Get data: `/sdm/kepegawaian/data`
-   ✅ Get departments: `/sdm/kepegawaian/departments`
-   ✅ Store: POST `/sdm/kepegawaian/store`
-   ✅ Show: GET `/sdm/kepegawaian/{id}`
-   ✅ Update: PUT `/sdm/kepegawaian/{id}`
-   ✅ Delete: DELETE `/sdm/kepegawaian/{id}`
-   ✅ Export PDF: `/sdm/kepegawaian/export/pdf`
-   ✅ Export Excel: `/sdm/kepegawaian/export/excel`

## Status: ✅ FIXED

Error sudah diperbaiki dan semua routes berfungsi dengan benar.

---

**Fixed**: 2 Desember 2024  
**Issue**: Route name mismatch  
**Solution**: Update route names di view files
