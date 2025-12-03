# Permission & Outlet Access Control Implementation

## ✅ Sudah Dibuat:

### 1. Middleware

-   ✅ `CheckPermission` - untuk check permission di routes
-   ✅ `CheckOutletAccess` - untuk check outlet access
-   ✅ Registered di `bootstrap/app.php` dengan alias:
    -   `permission:nama.permission`
    -   `outlet.access`

### 2. Blade Directives

-   ✅ `@hasPermission('permission.name')` - check permission
-   ✅ `@hasRole('role_name')` - check role
-   ✅ `@hasOutletAccess($outletId)` - check outlet access

### 3. User Model Methods

-   ✅ `hasPermission($permission)` - check single permission
-   ✅ `hasAnyPermission($permissions)` - check any permission
-   ✅ `hasAllPermissions($permissions)` - check all permissions
-   ✅ `hasRole($role)` - check role
-   ✅ `hasAccessToOutlet($outletId)` - check outlet access

---

## 📋 Cara Implementasi:

### A. PROTECT ROUTES dengan Permission

```php
// routes/web.php

// Single permission
Route::get('/finance/biaya', [Controller::class, 'index'])
    ->middleware('permission:finance.biaya.view');

// Multiple permissions (any)
Route::post('/finance/biaya', [Controller::class, 'store'])
    ->middleware('permission:finance.biaya.create');

// Group routes
Route::middleware(['auth', 'permission:finance.biaya.view'])->group(function () {
    Route::get('/finance/biaya', [Controller::class, 'index']);
    Route::get('/finance/biaya/{id}', [Controller::class, 'show']);
});
```

### B. HIDE SIDEBAR MENU berdasarkan Permission

```blade
{{-- resources/views/partials/sidebar/finance.blade.php --}}

@hasPermission('finance.biaya.view')
<li class="nav-item">
    <a href="{{ route('finance.biaya.index') }}" class="nav-link">
        <i class='bx bx-money'></i>
        <span>Biaya</span>
    </a>
</li>
@endhasPermission

@hasPermission('finance.jurnal.view')
<li class="nav-item">
    <a href="{{ route('finance.jurnal.index') }}" class="nav-link">
        <i class='bx bx-book'></i>
        <span>Jurnal</span>
    </a>
</li>
@endhasPermission
```

### C. HIDE TOMBOL CRUD berdasarkan Permission

```blade
{{-- resources/views/admin/finance/biaya/index.blade.php --}}

{{-- Tombol Tambah --}}
@hasPermission('finance.biaya.create')
<button onclick="openModal()" class="btn btn-primary">
    <i class='bx bx-plus'></i> Tambah Biaya
</button>
@endhasPermission

{{-- Tombol Edit --}}
@hasPermission('finance.biaya.update')
<button onclick="editData({{ $item->id }})" class="btn btn-sm btn-info">
    <i class='bx bx-edit'></i>
</button>
@endhasPermission

{{-- Tombol Delete --}}
@hasPermission('finance.biaya.delete')
<button onclick="deleteData({{ $item->id }})" class="btn btn-sm btn-danger">
    <i class='bx bx-trash'></i>
</button>
@endhasPermission

{{-- Tombol Export --}}
@hasPermission('finance.biaya.export')
<button onclick="exportData()" class="btn btn-success">
    <i class='bx bx-download'></i> Export
</button>
@endhasPermission
```

### D. FILTER DATA berdasarkan Outlet di Controller

```php
// app/Http/Controllers/YourController.php

public function index(Request $request)
{
    $user = auth()->user();

    // Query builder
    $query = YourModel::query();

    // Filter by outlet access (if not super admin)
    if (!$user->hasRole('super_admin')) {
        $outletIds = $user->outlets->pluck('id_outlet');
        $query->whereIn('outlet_id', $outletIds);
    }

    // Get data
    $data = $query->get();

    return view('your.view', compact('data'));
}

public function store(Request $request)
{
    // Validate outlet access
    if (!auth()->user()->hasAccessToOutlet($request->outlet_id)) {
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak memiliki akses ke outlet ini'
        ], 403);
    }

    // Create data
    YourModel::create($request->all());
}
```

### E. FILTER DROPDOWN OUTLET berdasarkan User Access

```blade
{{-- resources/views/your/view.blade.php --}}

<select name="outlet_id" class="form-control" required>
    <option value="">Pilih Outlet</option>
    @foreach(auth()->user()->outlets as $outlet)
        <option value="{{ $outlet->id_outlet }}">
            {{ $outlet->nama_outlet }}
        </option>
    @endforeach
</select>
```

---

## 🎯 Permission Naming Convention:

Format: `{module}.{menu}.{action}`

### Modules:

-   `finance` - Finance & Accounting
-   `crm` - Customer Relationship Management
-   `inventory` - Inventory Management
-   `hrm` - Human Resource Management
-   `procurement` - Procurement
-   `production` - Production
-   `sales` - Sales
-   `pos` - Point of Sale
-   `project` - Project Management
-   `sistem` - System Settings

### Actions:

-   `view` - Lihat/Read
-   `create` - Tambah/Create
-   `update` - Edit/Update
-   `delete` - Hapus/Delete
-   `export` - Export data
-   `import` - Import data
-   `approve` - Approve/Verifikasi
-   `print` - Print/PDF

### Contoh Permissions:

```
finance.biaya.view
finance.biaya.create
finance.biaya.update
finance.biaya.delete
finance.biaya.export

crm.pelanggan.view
crm.pelanggan.create
crm.pelanggan.update
crm.pelanggan.delete
crm.pelanggan.import

sistem.users.view
sistem.users.create
sistem.users.update
sistem.users.delete
```

---

## 🔧 Implementasi per Modul:

### 1. Finance Module

**Permissions yang dibutuhkan:**

```
finance.biaya.view
finance.biaya.create
finance.biaya.update
finance.biaya.delete
finance.biaya.export

finance.jurnal.view
finance.jurnal.create
finance.jurnal.update
finance.jurnal.delete
finance.jurnal.export

finance.rab.view
finance.rab.create
finance.rab.update
finance.rab.delete
finance.rab.approve

finance.rekonsiliasi.view
finance.rekonsiliasi.create
finance.rekonsiliasi.update
finance.rekonsiliasi.delete
```

**Update Routes:**

```php
Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {

    // Biaya
    Route::middleware('permission:finance.biaya.view')->group(function () {
        Route::get('/biaya', [FinanceAccountantController::class, 'biaya'])->name('biaya.index');
    });
    Route::post('/biaya', [FinanceAccountantController::class, 'storeBiaya'])
        ->middleware('permission:finance.biaya.create')->name('biaya.store');
    Route::put('/biaya/{id}', [FinanceAccountantController::class, 'updateBiaya'])
        ->middleware('permission:finance.biaya.update')->name('biaya.update');
    Route::delete('/biaya/{id}', [FinanceAccountantController::class, 'destroyBiaya'])
        ->middleware('permission:finance.biaya.delete')->name('biaya.destroy');

    // RAB
    Route::middleware('permission:finance.rab.view')->group(function () {
        Route::get('/rab', [FinanceAccountantController::class, 'rab'])->name('rab.index');
    });
    Route::post('/rab', [FinanceAccountantController::class, 'storeRab'])
        ->middleware('permission:finance.rab.create')->name('rab.store');
    Route::put('/rab/{id}', [FinanceAccountantController::class, 'updateRab'])
        ->middleware('permission:finance.rab.update')->name('rab.update');
    Route::delete('/rab/{id}', [FinanceAccountantController::class, 'destroyRab'])
        ->middleware('permission:finance.rab.delete')->name('rab.destroy');
});
```

### 2. CRM Module

**Permissions:**

```
crm.pelanggan.view
crm.pelanggan.create
crm.pelanggan.update
crm.pelanggan.delete
crm.pelanggan.import
crm.pelanggan.export

crm.tipe.view
crm.tipe.create
crm.tipe.update
crm.tipe.delete
```

### 3. System Module

**Permissions:**

```
sistem.users.view
sistem.users.create
sistem.users.update
sistem.users.delete

sistem.roles.view
sistem.roles.create
sistem.roles.update
sistem.roles.delete

sistem.outlets.view
sistem.outlets.create
sistem.outlets.update
sistem.outlets.delete
```

---

## 📝 Checklist Implementasi:

### Per Halaman/Module:

-   [ ] Update routes dengan middleware `permission`
-   [ ] Update controller untuk filter data by outlet
-   [ ] Update sidebar untuk hide menu berdasarkan permission
-   [ ] Update view untuk hide tombol CRUD berdasarkan permission
-   [ ] Update dropdown outlet untuk show hanya outlet yang accessible
-   [ ] Test dengan user berbeda role dan outlet

### Testing:

1. **Super Admin** - harus bisa akses semua
2. **Admin** - harus bisa akses sesuai permission role
3. **User** - harus bisa akses sesuai permission dan outlet
4. **User tanpa permission** - tidak bisa akses menu/tombol
5. **User tanpa outlet** - tidak bisa lihat data outlet lain

---

## 🚀 Quick Start:

### 1. Seed Permissions (jika belum)

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 2. Assign Permissions ke Role

Via UI: Admin > Role Management > Edit Role > Pilih Permissions

### 3. Assign Outlets ke User

Via UI: Admin > User Management > Edit User > Pilih Outlets

### 4. Test Access

Login dengan user berbeda dan test akses menu, CRUD, dan outlet

---

## 💡 Tips:

1. **Super Admin** selalu bypass semua permission check
2. **Permission check** dilakukan di 3 layer:
    - Route (middleware)
    - Controller (manual check jika perlu)
    - View (Blade directive)
3. **Outlet filter** dilakukan di controller query
4. **Dropdown outlet** hanya show outlet yang accessible
5. **Error 403** akan muncul jika user tidak punya permission

---

## 🔍 Debugging:

```php
// Check user permissions
auth()->user()->role->permissions->pluck('name');

// Check specific permission
auth()->user()->hasPermission('finance.biaya.view');

// Check outlet access
auth()->user()->outlets->pluck('id_outlet');
auth()->user()->hasAccessToOutlet(1);
```
