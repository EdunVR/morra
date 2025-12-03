# 🚀 Implementation Guide: Permission & Outlet Access Control

## ✅ Yang Sudah Dibuat:

### 1. Core Files

-   ✅ `app/Http/Middleware/CheckPermission.php` - Middleware untuk check permission
-   ✅ `app/Traits/HasOutletFilter.php` - Trait untuk filter outlet di controller
-   ✅ `database/seeders/CompletePermissionSeeder.php` - Seeder lengkap semua permissions
-   ✅ `PERMISSION_OUTLET_ACCESS_IMPLEMENTATION.md` - Dokumentasi lengkap

### 2. Existing Features

-   ✅ User Model sudah punya method `hasPermission()`, `hasRole()`, `hasAccessToOutlet()`
-   ✅ Blade directives sudah ada: `@hasPermission`, `@hasRole`, `@hasOutletAccess`
-   ✅ Middleware sudah registered di `bootstrap/app.php`

---

## 📋 LANGKAH IMPLEMENTASI:

### STEP 1: Seed Permissions

```bash
php artisan db:seed --class=CompletePermissionSeeder
```

Ini akan create semua permissions untuk:

-   Finance (biaya, jurnal, rab, rekonsiliasi, dll)
-   CRM (pelanggan, tipe, leads, dll)
-   Inventory (barang, stok, opname, dll)
-   Procurement (supplier, PO, PR, dll)
-   Sales (quotation, SO, invoice, dll)
-   HRM (karyawan, absensi, payroll, dll)
-   Production (work order, BOM, dll)
-   Project (projects, tasks, dll)
-   POS (kasir, shift, retur)
-   System (users, roles, outlets, dll)

Dan auto-assign ke roles:

-   **Super Admin** → All permissions
-   **Admin** → View, Create, Update, Export (kecuali sistem)
-   **User** → View only (kecuali sistem)

### STEP 2: Update Controller (Contoh: Biaya)

```php
<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Models\Expense;

class FinanceAccountantController extends Controller
{
    use HasOutletFilter; // Add trait

    public function biaya(Request $request)
    {
        // Get user's accessible outlets
        $outlets = $this->getUserOutlets();

        // Query with outlet filter
        $query = Expense::query();
        $query = $this->applyOutletFilter($query, 'outlet_id');

        $expenses = $query->orderBy('tanggal', 'desc')->get();

        return view('admin.finance.biaya.index', compact('expenses', 'outlets'));
    }

    public function storeBiaya(Request $request)
    {
        // Validate outlet access
        $this->authorizeOutletAccess($request->outlet_id);

        // Create expense
        $expense = Expense::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Biaya berhasil ditambahkan'
        ]);
    }

    public function updateBiaya(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // Validate outlet access
        $this->authorizeOutletAccess($expense->outlet_id);

        // Update expense
        $expense->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Biaya berhasil diupdate'
        ]);
    }

    public function destroyBiaya($id)
    {
        $expense = Expense::findOrFail($id);

        // Validate outlet access
        $this->authorizeOutletAccess($expense->outlet_id);

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Biaya berhasil dihapus'
        ]);
    }
}
```

### STEP 3: Update Routes dengan Permission Middleware

```php
// routes/web.php

Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {

    // Biaya Routes
    Route::middleware('permission:finance.biaya.view')->group(function () {
        Route::get('/biaya', [FinanceAccountantController::class, 'biaya'])->name('biaya.index');
        Route::get('/biaya/export', [FinanceAccountantController::class, 'exportBiaya'])
            ->middleware('permission:finance.biaya.export')->name('biaya.export');
    });

    Route::post('/biaya', [FinanceAccountantController::class, 'storeBiaya'])
        ->middleware('permission:finance.biaya.create')->name('biaya.store');

    Route::put('/biaya/{id}', [FinanceAccountantController::class, 'updateBiaya'])
        ->middleware('permission:finance.biaya.update')->name('biaya.update');

    Route::delete('/biaya/{id}', [FinanceAccountantController::class, 'destroyBiaya'])
        ->middleware('permission:finance.biaya.delete')->name('biaya.destroy');

    // RAB Routes
    Route::middleware('permission:finance.rab.view')->group(function () {
        Route::get('/rab', [FinanceAccountantController::class, 'rab'])->name('rab.index');
    });

    Route::post('/rab', [FinanceAccountantController::class, 'storeRab'])
        ->middleware('permission:finance.rab.create')->name('rab.store');

    Route::put('/rab/{id}', [FinanceAccountantController::class, 'updateRab'])
        ->middleware('permission:finance.rab.update')->name('rab.update');

    Route::delete('/rab/{id}', [FinanceAccountantController::class, 'destroyRab'])
        ->middleware('permission:finance.rab.delete')->name('rab.destroy');

    // Rekonsiliasi Routes
    Route::middleware('permission:finance.rekonsiliasi.view')->group(function () {
        Route::get('/rekonsiliasi', [BankReconciliationController::class, 'index'])->name('rekonsiliasi.index');
    });

    Route::post('/rekonsiliasi', [BankReconciliationController::class, 'store'])
        ->middleware('permission:finance.rekonsiliasi.create')->name('rekonsiliasi.store');
});

// CRM Routes
Route::middleware(['auth'])->prefix('crm')->name('crm.')->group(function () {

    Route::middleware('permission:crm.pelanggan.view')->group(function () {
        Route::get('/pelanggan', [CustomerManagementController::class, 'index'])->name('pelanggan.index');
    });

    Route::post('/pelanggan', [CustomerManagementController::class, 'store'])
        ->middleware('permission:crm.pelanggan.create')->name('pelanggan.store');

    Route::put('/pelanggan/{id}', [CustomerManagementController::class, 'update'])
        ->middleware('permission:crm.pelanggan.update')->name('pelanggan.update');

    Route::delete('/pelanggan/{id}', [CustomerManagementController::class, 'destroy'])
        ->middleware('permission:crm.pelanggan.delete')->name('pelanggan.destroy');
});
```

### STEP 4: Update Sidebar dengan Permission Check

```blade
{{-- resources/views/partials/sidebar/finance.blade.php --}}

<ul class="sub-menu">
    @hasPermission('finance.biaya.view')
    <li class="{{ request()->routeIs('finance.biaya.index') ? 'active' : '' }}">
        <a href="{{ route('finance.biaya.index') }}">
            <i data-feather="dollar-sign"></i> <span>Biaya</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('finance.jurnal.view')
    <li class="{{ request()->routeIs('financial.journal.index') ? 'active' : '' }}">
        <a href="{{ route('financial.journal.index') }}">
            <i data-feather="book"></i> <span>Jurnal Transaksi</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('finance.rab.view')
    <li class="{{ request()->routeIs('rab_template.index') ? 'active' : '' }}">
        <a href="{{ route('rab_template.index') }}">
            <i data-feather="book"></i> <span>Manajemen RAB</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('finance.rekonsiliasi.view')
    <li class="{{ request()->routeIs('finance.rekonsiliasi.index') ? 'active' : '' }}">
        <a href="{{ route('finance.rekonsiliasi.index') }}">
            <i data-feather="check-square"></i> <span>Rekonsiliasi Bank</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('finance.ledger.view')
    <li class="{{ request()->routeIs('financial.ledger.index') ? 'active' : '' }}">
        <a href="{{ route('financial.ledger.index') }}">
            <i data-feather="book"></i> <span>Buku Besar</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('finance.neraca.view')
    <li class="{{ request()->routeIs('financial.balance-sheet.index') ? 'active' : '' }}">
        <a href="{{ route('financial.balance-sheet.index') }}">
            <i data-feather="book"></i> <span>Neraca</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('finance.laba-rugi.view')
    <li class="{{ request()->routeIs('financial.profit-loss.index') ? 'active' : '' }}">
        <a href="{{ route('financial.profit-loss.index') }}">
            <i data-feather="book"></i> <span>Laba Rugi</span>
        </a>
    </li>
    @endhasPermission
</ul>
```

### STEP 5: Update View dengan Permission Check untuk Tombol CRUD

```blade
{{-- resources/views/admin/finance/biaya/index.blade.php --}}

<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Biaya</h1>

    <div class="flex gap-2">
        @hasPermission('finance.biaya.export')
        <button onclick="exportData()" class="btn btn-success">
            <i class='bx bx-download'></i> Export
        </button>
        @endhasPermission

        @hasPermission('finance.biaya.create')
        <button onclick="openModal()" class="btn btn-primary">
            <i class='bx bx-plus'></i> Tambah Biaya
        </button>
        @endhasPermission
    </div>
</div>

{{-- Dropdown Outlet - hanya show outlet yang accessible --}}
<select name="outlet_id" class="form-control" required>
    <option value="">Pilih Outlet</option>
    @foreach(auth()->user()->outlets as $outlet)
        <option value="{{ $outlet->id_outlet }}">
            {{ $outlet->nama_outlet }}
        </option>
    @endforeach
</select>

{{-- Table dengan tombol CRUD berdasarkan permission --}}
<table class="table">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Jumlah</th>
            <th>Outlet</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->tanggal }}</td>
            <td>{{ $expense->keterangan }}</td>
            <td>{{ number_format($expense->jumlah) }}</td>
            <td>{{ $expense->outlet->nama_outlet ?? '-' }}</td>
            <td>
                <div class="flex gap-2">
                    @hasPermission('finance.biaya.update')
                    <button onclick="editData({{ $expense->id }})" class="btn btn-sm btn-info">
                        <i class='bx bx-edit'></i>
                    </button>
                    @endhasPermission

                    @hasPermission('finance.biaya.delete')
                    <button onclick="deleteData({{ $expense->id }})" class="btn btn-sm btn-danger">
                        <i class='bx bx-trash'></i>
                    </button>
                    @endhasPermission
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

---

## 🎯 CHECKLIST IMPLEMENTASI PER MODUL:

### Finance Module

-   [ ] Update `FinanceAccountantController` - add `HasOutletFilter` trait
-   [ ] Update routes dengan middleware `permission:finance.*`
-   [ ] Update `resources/views/partials/sidebar/finance.blade.php`
-   [ ] Update `resources/views/admin/finance/biaya/index.blade.php`
-   [ ] Update `resources/views/admin/finance/jurnal/index.blade.php`
-   [ ] Update `resources/views/admin/finance/rab/index.blade.php`
-   [ ] Update `resources/views/admin/finance/rekonsiliasi/index.blade.php`

### CRM Module

-   [ ] Update `CustomerManagementController` - add `HasOutletFilter` trait
-   [ ] Update routes dengan middleware `permission:crm.*`
-   [ ] Update `resources/views/partials/sidebar/customer-service.blade.php`
-   [ ] Update `resources/views/admin/crm/pelanggan/index.blade.php`
-   [ ] Update `resources/views/admin/crm/tipe/index.blade.php`

### Inventory Module

-   [ ] Update controller - add `HasOutletFilter` trait
-   [ ] Update routes dengan middleware `permission:inventory.*`
-   [ ] Update sidebar
-   [ ] Update views

### Procurement Module

-   [ ] Update controller - add `HasOutletFilter` trait
-   [ ] Update routes dengan middleware `permission:procurement.*`
-   [ ] Update sidebar
-   [ ] Update views

### Sales Module

-   [ ] Update controller - add `HasOutletFilter` trait
-   [ ] Update routes dengan middleware `permission:sales.*`
-   [ ] Update sidebar
-   [ ] Update views

### HRM Module

-   [ ] Update controller - add `HasOutletFilter` trait
-   [ ] Update routes dengan middleware `permission:hrm.*`
-   [ ] Update sidebar
-   [ ] Update views

---

## 🧪 TESTING:

### 1. Test dengan Super Admin

```
Login sebagai Super Admin
✅ Harus bisa akses semua menu
✅ Harus bisa CRUD semua data
✅ Harus bisa lihat semua outlet
```

### 2. Test dengan Admin (custom permissions)

```
Login sebagai Admin
✅ Hanya bisa akses menu sesuai permission
✅ Tombol CRUD muncul sesuai permission
✅ Hanya bisa lihat data outlet yang di-assign
```

### 3. Test dengan User (view only)

```
Login sebagai User
✅ Hanya bisa akses menu view
✅ Tombol create/edit/delete tidak muncul
✅ Hanya bisa lihat data outlet yang di-assign
```

### 4. Test Outlet Access

```
User dengan outlet A
✅ Bisa lihat data outlet A
❌ Tidak bisa lihat data outlet B
❌ Error 403 jika akses data outlet B
```

---

## 💡 QUICK COMMANDS:

```bash
# Seed permissions
php artisan db:seed --class=CompletePermissionSeeder

# Clear cache
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check user permissions
php artisan tinker
>>> auth()->user()->role->permissions->pluck('name')

# Check user outlets
>>> auth()->user()->outlets->pluck('nama_outlet')
```

---

## 📝 NOTES:

1. **Super Admin** selalu bypass semua permission check
2. Implementasi dilakukan **bertahap per modul**
3. Test setiap modul setelah implementasi
4. Backup database sebelum seed permissions
5. Update dokumentasi jika ada perubahan
