# 🔐 GUIDE: Menambahkan Permission Middleware ke Routes

## 📋 OVERVIEW

Dokumentasi ini menjelaskan cara menambahkan middleware permission ke routes yang sudah ada tanpa merusak fungsionalitas existing.

## 🎯 STRATEGI IMPLEMENTASI

### Opsi 1: Middleware di Controller Constructor (RECOMMENDED)

Lebih mudah dan tidak perlu ubah routes yang sudah ada.

### Opsi 2: Middleware di Routes

Lebih eksplisit tapi perlu refactor routes yang sudah ada.

---

## ✅ OPSI 1: MIDDLEWARE DI CONTROLLER (RECOMMENDED)

### Keuntungan:

-   Tidak perlu ubah routes
-   Lebih mudah maintain
-   Permission logic terpusat di controller

### Implementasi:

#### 1. KategoriController

```php
<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

class KategoriController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        // View permission untuk index dan data
        $this->middleware('permission:inventaris.kategori.view')->only([
            'index', 'data', 'show'
        ]);

        // Create permission
        $this->middleware('permission:inventaris.kategori.create')->only([
            'store', 'getNewKode'
        ]);

        // Edit permission
        $this->middleware('permission:inventaris.kategori.edit')->only([
            'update'
        ]);

        // Delete permission
        $this->middleware('permission:inventaris.kategori.delete')->only([
            'destroy'
        ]);

        // Export permission
        $this->middleware('permission:inventaris.kategori.export')->only([
            'exportPdf', 'exportExcel', 'downloadTemplate'
        ]);

        // Import permission
        $this->middleware('permission:inventaris.kategori.import')->only([
            'importExcel'
        ]);
    }

    // ... rest of controller methods
}
```

#### 2. BahanController

```php
<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

class BahanController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('permission:inventaris.bahan.view')->only([
            'index', 'data', 'show'
        ]);

        $this->middleware('permission:inventaris.bahan.create')->only([
            'store', 'getNewKode'
        ]);

        $this->middleware('permission:inventaris.bahan.edit')->only([
            'update', 'updateHarga', 'editHarga'
        ]);

        $this->middleware('permission:inventaris.bahan.delete')->only([
            'destroy', 'destroyHarga', 'deleteSelected'
        ]);

        $this->middleware('permission:inventaris.bahan.export')->only([
            'exportPdf', 'exportExcel', 'downloadTemplate'
        ]);

        $this->middleware('permission:inventaris.bahan.import')->only([
            'importExcel'
        ]);
    }

    // ... rest of controller methods
}
```

#### 3. SatuanController

```php
<?php

namespace App\Http\Controllers;

class SatuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inventaris.satuan.view')->only([
            'index', 'data', 'show', 'getSatuanUtama'
        ]);

        $this->middleware('permission:inventaris.satuan.create')->only([
            'store', 'getNewKode'
        ]);

        $this->middleware('permission:inventaris.satuan.edit')->only([
            'update'
        ]);

        $this->middleware('permission:inventaris.satuan.delete')->only([
            'destroy'
        ]);

        $this->middleware('permission:inventaris.satuan.export')->only([
            'exportPdf', 'exportExcel', 'downloadTemplate'
        ]);

        $this->middleware('permission:inventaris.satuan.import')->only([
            'importExcel'
        ]);
    }

    // ... rest of controller methods
}
```

#### 4. OutletController

```php
<?php

namespace App\Http\Controllers;

class OutletController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inventaris.outlet.view')->only([
            'index', 'data', 'show', 'getCities'
        ]);

        $this->middleware('permission:inventaris.outlet.create')->only([
            'store', 'getNewKode'
        ]);

        $this->middleware('permission:inventaris.outlet.edit')->only([
            'update'
        ]);

        $this->middleware('permission:inventaris.outlet.delete')->only([
            'destroy'
        ]);

        $this->middleware('permission:inventaris.outlet.export')->only([
            'exportPdf', 'exportExcel', 'downloadTemplate'
        ]);

        $this->middleware('permission:inventaris.outlet.import')->only([
            'importExcel'
        ]);
    }

    // ... rest of controller methods
}
```

#### 5. ProdukController (Update)

```php
<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

class ProdukController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('permission:inventaris.produk.view')->only([
            'index', 'data', 'show', 'getCategories', 'getUnits', 'getIdMappings'
        ]);

        $this->middleware('permission:inventaris.produk.create')->only([
            'store', 'getNewSku'
        ]);

        $this->middleware('permission:inventaris.produk.edit')->only([
            'update'
        ]);

        $this->middleware('permission:inventaris.produk.delete')->only([
            'destroy'
        ]);

        $this->middleware('permission:inventaris.produk.export')->only([
            'exportPdf', 'exportExcel', 'downloadTemplate'
        ]);

        $this->middleware('permission:inventaris.produk.import')->only([
            'importExcel'
        ]);
    }

    // ... rest of controller methods
}
```

---

## 🔧 OPSI 2: MIDDLEWARE DI ROUTES

### Keuntungan:

-   Lebih eksplisit
-   Mudah dilihat permission requirement dari routes file

### Implementasi:

```php
// routes/web.php

Route::prefix('inventaris')->name('inventaris.')->group(function () {

    // ===== OUTLET ROUTES =====
    Route::middleware(['permission:inventaris.outlet.view'])->group(function () {
        Route::get('outlet', [OutletController::class, 'index'])->name('outlet.index');
        Route::get('outlet-data', [OutletController::class, 'data'])->name('outlet.data');
        Route::get('outlet/{outlet}', [OutletController::class, 'show'])->name('outlet.show');
        Route::get('outlet/cities', [OutletController::class, 'getCities'])->name('outlet.cities');
    });

    Route::middleware(['permission:inventaris.outlet.create'])->group(function () {
        Route::get('outlet/create', [OutletController::class, 'create'])->name('outlet.create');
        Route::post('outlet', [OutletController::class, 'store'])->name('outlet.store');
        Route::get('outlet/generate-kode', [OutletController::class, 'getNewKode'])->name('outlet.generate-kode');
    });

    Route::middleware(['permission:inventaris.outlet.edit'])->group(function () {
        Route::get('outlet/{outlet}/edit', [OutletController::class, 'edit'])->name('outlet.edit');
        Route::put('outlet/{outlet}', [OutletController::class, 'update'])->name('outlet.update');
        Route::patch('outlet/{outlet}', [OutletController::class, 'update']);
    });

    Route::middleware(['permission:inventaris.outlet.delete'])->group(function () {
        Route::delete('outlet/{outlet}', [OutletController::class, 'destroy'])->name('outlet.destroy');
    });

    Route::middleware(['permission:inventaris.outlet.export'])->group(function () {
        Route::get('outlet/export/pdf', [OutletController::class, 'exportPdf'])->name('outlet.export.pdf');
        Route::get('outlet/export/excel', [OutletController::class, 'exportExcel'])->name('outlet.export.excel');
        Route::get('outlet/download-template', [OutletController::class, 'downloadTemplate'])->name('outlet.download-template');
    });

    Route::middleware(['permission:inventaris.outlet.import'])->group(function () {
        Route::post('outlet/import/excel', [OutletController::class, 'importExcel'])->name('outlet.import.excel');
    });

    // ===== KATEGORI ROUTES =====
    Route::middleware(['permission:inventaris.kategori.view'])->group(function () {
        Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('kategori-data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::get('kategori/{kategori}', [KategoriController::class, 'show'])->name('kategori.show');
        Route::get('kategori/groups', [KategoriController::class, 'getGroups'])->name('kategori.groups');
        Route::get('kategori/outlets', [KategoriController::class, 'getOutlets'])->name('kategori.outlets');
    });

    Route::middleware(['permission:inventaris.kategori.create'])->group(function () {
        Route::get('kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('kategori/generate-kode', [KategoriController::class, 'getNewKode'])->name('kategori.generate-kode');
    });

    Route::middleware(['permission:inventaris.kategori.edit'])->group(function () {
        Route::get('kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::patch('kategori/{kategori}', [KategoriController::class, 'update']);
    });

    Route::middleware(['permission:inventaris.kategori.delete'])->group(function () {
        Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    });

    Route::middleware(['permission:inventaris.kategori.export'])->group(function () {
        Route::get('kategori/export/pdf', [KategoriController::class, 'exportPdf'])->name('kategori.export.pdf');
        Route::get('kategori/export/excel', [KategoriController::class, 'exportExcel'])->name('kategori.export.excel');
        Route::get('kategori/download-template', [KategoriController::class, 'downloadTemplate'])->name('kategori.download-template');
    });

    Route::middleware(['permission:inventaris.kategori.import'])->group(function () {
        Route::post('kategori/import/excel', [KategoriController::class, 'importExcel'])->name('kategori.import.excel');
    });

    // Ulangi pattern yang sama untuk Bahan, Satuan, Produk, dll.
});
```

---

## 🚀 IMPLEMENTASI STEP-BY-STEP

### Step 1: Backup Routes

```bash
cp routes/web.php routes/web.php.backup
```

### Step 2: Pilih Strategi

Pilih Opsi 1 (Controller Constructor) atau Opsi 2 (Routes Middleware)

### Step 3: Implementasi Bertahap

Implementasi per modul, test, lalu lanjut ke modul berikutnya:

1. Outlet
2. Kategori
3. Satuan
4. Produk
5. Bahan
6. Inventori
7. Transfer Gudang

### Step 4: Testing

Untuk setiap modul:

```bash
# Test sebagai Super Admin
# Test sebagai user dengan permission
# Test sebagai user tanpa permission
```

### Step 5: Clear Cache

```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

---

## 🧪 TESTING SCRIPT

### Test Permission Middleware

```php
// tests/Feature/InventarisPermissionTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class InventarisPermissionTest extends TestCase
{
    public function test_user_without_permission_cannot_access_kategori()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.inventaris.kategori.index'));

        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_access_kategori()
    {
        $user = User::factory()->create();
        $permission = Permission::where('name', 'inventaris.kategori.view')->first();
        $user->permissions()->attach($permission->id);

        $response = $this->actingAs($user)
            ->get(route('admin.inventaris.kategori.index'));

        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_all_modules()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'super_admin')->first();
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)
            ->get(route('admin.inventaris.kategori.index'));

        $response->assertStatus(200);
    }
}
```

---

## 📊 PERMISSION CHECKLIST

### Outlet

-   [ ] inventaris.outlet.view
-   [ ] inventaris.outlet.create
-   [ ] inventaris.outlet.edit
-   [ ] inventaris.outlet.delete
-   [ ] inventaris.outlet.export
-   [ ] inventaris.outlet.import

### Kategori

-   [ ] inventaris.kategori.view
-   [ ] inventaris.kategori.create
-   [ ] inventaris.kategori.edit
-   [ ] inventaris.kategori.delete
-   [ ] inventaris.kategori.export
-   [ ] inventaris.kategori.import

### Satuan

-   [ ] inventaris.satuan.view
-   [ ] inventaris.satuan.create
-   [ ] inventaris.satuan.edit
-   [ ] inventaris.satuan.delete
-   [ ] inventaris.satuan.export
-   [ ] inventaris.satuan.import

### Produk

-   [ ] inventaris.produk.view
-   [ ] inventaris.produk.create
-   [ ] inventaris.produk.edit
-   [ ] inventaris.produk.delete
-   [ ] inventaris.produk.export
-   [ ] inventaris.produk.import

### Bahan

-   [ ] inventaris.bahan.view
-   [ ] inventaris.bahan.create
-   [ ] inventaris.bahan.edit
-   [ ] inventaris.bahan.delete
-   [ ] inventaris.bahan.export
-   [ ] inventaris.bahan.import

---

## 💡 BEST PRACTICES

### 1. Consistent Naming

```
module.submodule.action
inventaris.kategori.view
inventaris.kategori.create
```

### 2. Granular Permissions

Pisahkan permission untuk setiap action (view, create, edit, delete, export, import)

### 3. Super Admin Bypass

Super admin selalu punya akses ke semua permission

### 4. Helper Methods

```php
// Check single permission
if (auth()->user()->hasPermission('inventaris.kategori.view')) {
    // ...
}

// Check multiple permissions (OR)
if (auth()->user()->hasAnyPermission(['inventaris.kategori.view', 'inventaris.kategori.edit'])) {
    // ...
}

// Check multiple permissions (AND)
if (auth()->user()->hasAllPermissions(['inventaris.kategori.view', 'inventaris.kategori.edit'])) {
    // ...
}
```

---

## 🔍 TROUBLESHOOTING

### Issue: Permission middleware tidak bekerja

**Solution**: Clear cache

```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### Issue: Super admin tidak bisa akses

**Solution**: Cek method hasPermission di User model

```php
public function hasPermission($permission)
{
    // Super admin bypass
    if ($this->hasRole('super_admin')) {
        return true;
    }

    // Check permission
    return $this->permissions()->where('name', $permission)->exists();
}
```

### Issue: 403 Forbidden untuk semua user

**Solution**: Cek apakah permission sudah di-seed

```bash
php artisan db:seed --class=CompletePermissionSeeder
```

---

**Status**: 📝 GUIDE READY
**Recommended**: Gunakan Opsi 1 (Controller Constructor)
**Next Step**: Implementasi middleware di controller satu per satu
