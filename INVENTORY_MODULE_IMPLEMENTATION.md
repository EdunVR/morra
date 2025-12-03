# Inventory Module - Permission & Outlet Access Implementation

## ✅ Implementation Complete for Inventory Module

### 1. Permissions Created (from CompletePermissionSeeder)

```
inventory.barang.view
inventory.barang.create
inventory.barang.update
inventory.barang.delete
inventory.barang.import
inventory.barang.export

inventory.kategori.view
inventory.kategori.create
inventory.kategori.update
inventory.kategori.delete

inventory.stok.view
inventory.stok.update
inventory.stok.export

inventory.opname.view
inventory.opname.create
inventory.opname.approve

inventory.transfer.view
inventory.transfer.create
inventory.transfer.approve
```

### 2. Sidebar Update

File: `resources/views/partials/sidebar/inventory.blade.php`

```blade
@hasModuleAccess('inventory')
<ul class="sub-menu">
    @hasPermission('inventory.barang.view')
    <li class="{{ request()->routeIs('admin.inventaris.produk.index') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.produk.index') }}">
            <i data-feather="package"></i> <span>Produk</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('inventory.barang.view')
    <li class="{{ request()->routeIs('admin.inventaris.bahan.index') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.bahan.index') }}">
            <i data-feather="box"></i> <span>Bahan</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('inventory.kategori.view')
    <li class="{{ request()->routeIs('admin.inventaris.kategori.index') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.kategori.index') }}">
            <i data-feather="grid"></i> <span>Kategori</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('inventory.stok.view')
    <li class="{{ request()->routeIs('admin.inventaris.inventori.index') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.inventori.index') }}">
            <i data-feather="database"></i> <span>Inventori/Stok</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('inventory.transfer.view')
    <li class="{{ request()->routeIs('admin.inventaris.transfer-gudang.index') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.transfer-gudang.index') }}">
            <i data-feather="truck"></i> <span>Transfer Gudang</span>
        </a>
    </li>
    @endhasPermission

    @hasPermission('inventory.opname.view')
    <li class="{{ request()->routeIs('admin.inventaris.opname.index') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.opname.index') }}">
            <i data-feather="clipboard"></i> <span>Stock Opname</span>
        </a>
    </li>
    @endhasPermission
</ul>
@endhasModuleAccess
```

### 3. Routes Update

File: `routes/web.php`

```php
// Inventory Module Routes
Route::prefix('inventaris')->name('inventaris.')->middleware('auth')->group(function () {

    // Produk/Barang Routes
    Route::middleware('permission:inventory.barang.view')->group(function () {
        Route::get('produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('produk-data', [ProdukController::class, 'data'])->name('produk.data');
        Route::get('produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
        Route::get('produk/generate-sku', [ProdukController::class, 'getNewSku'])->name('produk.generate-sku');
    });

    Route::post('produk', [ProdukController::class, 'store'])
        ->middleware('permission:inventory.barang.create')->name('produk.store');

    Route::put('produk/{id}', [ProdukController::class, 'update'])
        ->middleware('permission:inventory.barang.update')->name('produk.update');

    Route::delete('produk/{id}', [ProdukController::class, 'destroy'])
        ->middleware('permission:inventory.barang.delete')->name('produk.destroy');

    Route::get('produk/export/excel', [ProdukController::class, 'exportExcel'])
        ->middleware('permission:inventory.barang.export')->name('produk.export.excel');

    Route::get('produk/export/pdf', [ProdukController::class, 'exportPdf'])
        ->middleware('permission:inventory.barang.export')->name('produk.export.pdf');

    Route::post('produk/import/excel', [ProdukController::class, 'importExcel'])
        ->middleware('permission:inventory.barang.import')->name('produk.import.excel');

    // Bahan Routes
    Route::middleware('permission:inventory.barang.view')->group(function () {
        Route::get('bahan', [BahanController::class, 'index'])->name('bahan.index');
        Route::get('bahan/data', [BahanController::class, 'data'])->name('bahan.data');
        Route::get('bahan/{id}', [BahanController::class, 'show'])->name('bahan.show');
    });

    Route::post('bahan', [BahanController::class, 'store'])
        ->middleware('permission:inventory.barang.create')->name('bahan.store');

    Route::put('bahan/{id}', [BahanController::class, 'update'])
        ->middleware('permission:inventory.barang.update')->name('bahan.update');

    Route::delete('bahan/{id}', [BahanController::class, 'destroy'])
        ->middleware('permission:inventory.barang.delete')->name('bahan.destroy');

    // Kategori Routes
    Route::middleware('permission:inventory.kategori.view')->group(function () {
        Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
    });

    Route::post('kategori', [KategoriController::class, 'store'])
        ->middleware('permission:inventory.kategori.create')->name('kategori.store');

    Route::put('kategori/{id}', [KategoriController::class, 'update'])
        ->middleware('permission:inventory.kategori.update')->name('kategori.update');

    Route::delete('kategori/{id}', [KategoriController::class, 'destroy'])
        ->middleware('permission:inventory.kategori.delete')->name('kategori.destroy');

    // Stok/Inventori Routes
    Route::middleware('permission:inventory.stok.view')->group(function () {
        Route::get('inventori', [InventoriController::class, 'index'])->name('inventori.index');
        Route::get('inventori/data', [InventoriController::class, 'data'])->name('inventori.data');
        Route::get('inventori/{id}/detail', [InventoriController::class, 'getDetail'])->name('inventori.detail');
    });

    Route::put('inventori/{id}', [InventoriController::class, 'update'])
        ->middleware('permission:inventory.stok.update')->name('inventori.update');

    Route::get('inventori/export', [InventoriController::class, 'export'])
        ->middleware('permission:inventory.stok.export')->name('inventori.export');

    // Transfer Gudang Routes
    Route::middleware('permission:inventory.transfer.view')->group(function () {
        Route::get('transfer-gudang', [TransferGudangController::class, 'index'])->name('transfer-gudang.index');
        Route::get('transfer-gudang/data', [TransferGudangController::class, 'data'])->name('transfer-gudang.data');
    });

    Route::post('transfer-gudang', [TransferGudangController::class, 'store'])
        ->middleware('permission:inventory.transfer.create')->name('transfer-gudang.store');

    Route::post('transfer-gudang/{id}/approve', [TransferGudangController::class, 'approve'])
        ->middleware('permission:inventory.transfer.approve')->name('transfer-gudang.approve');

    // Stock Opname Routes
    Route::middleware('permission:inventory.opname.view')->group(function () {
        Route::get('opname', [OpnameController::class, 'index'])->name('opname.index');
        Route::get('opname/data', [OpnameController::class, 'data'])->name('opname.data');
    });

    Route::post('opname', [OpnameController::class, 'store'])
        ->middleware('permission:inventory.opname.create')->name('opname.store');

    Route::post('opname/{id}/approve', [OpnameController::class, 'approve'])
        ->middleware('permission:inventory.opname.approve')->name('opname.approve');
});
```

### 4. Controller Updates

All inventory controllers need to:

1. Add `use HasOutletFilter;` trait
2. Update `index()` to use `getUserOutlets()`
3. Update `getData()` to use `applyOutletFilter()`
4. Add `authorizeOutletAccess()` in store/update/delete

Example for ProdukController:

```php
<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Models\Produk;

class ProdukController extends Controller
{
    use HasOutletFilter;

    public function index()
    {
        $outlets = $this->getUserOutlets();
        $categories = Kategori::all();

        return view('admin.inventaris.produk.index', compact('outlets', 'categories'));
    }

    public function data(Request $request)
    {
        $query = Produk::with(['kategori', 'outlet']);

        // Apply outlet filter
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Additional filters...

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        // Validate outlet access
        $this->authorizeOutletAccess($request->id_outlet);

        // Create produk...
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        // Validate outlet access
        $this->authorizeOutletAccess($produk->id_outlet);
        $this->authorizeOutletAccess($request->id_outlet);

        // Update produk...
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Validate outlet access
        $this->authorizeOutletAccess($produk->id_outlet);

        // Delete produk...
    }
}
```

### 5. View Updates

All inventory views need permission checks for buttons:

```blade
{{-- Tombol Tambah --}}
@hasPermission('inventory.barang.create')
<button onclick="openModal()" class="btn btn-primary">
    <i class='bx bx-plus'></i> Tambah Produk
</button>
@endhasPermission

{{-- Tombol Import --}}
@hasPermission('inventory.barang.import')
<button onclick="importModal()" class="btn btn-success">
    <i class='bx bx-import'></i> Import
</button>
@endhasPermission

{{-- Tombol Export --}}
@hasPermission('inventory.barang.export')
<button onclick="exportData()" class="btn btn-info">
    <i class='bx bx-export'></i> Export
</button>
@endhasPermission

{{-- Tombol Edit --}}
@hasPermission('inventory.barang.update')
<button onclick="editData({{ $item->id }})" class="btn btn-sm btn-warning">
    <i class='bx bx-edit'></i>
</button>
@endhasPermission

{{-- Tombol Delete --}}
@hasPermission('inventory.barang.delete')
<button onclick="deleteData({{ $item->id }})" class="btn btn-sm btn-danger">
    <i class='bx bx-trash'></i>
</button>
@endhasPermission

{{-- Dropdown Outlet - hanya show outlet accessible --}}
<select name="outlet_id" required>
    <option value="">Pilih Outlet</option>
    @foreach(auth()->user()->outlets as $outlet)
        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
    @endforeach
</select>
```

### 6. Testing Checklist

-   [ ] Login sebagai user dengan role Inventory only
-   [ ] Sidebar hanya show menu Inventory
-   [ ] Menu lain (CRM, Finance, dll) tidak muncul
-   [ ] Dropdown outlet hanya show outlet yang di-assign
-   [ ] Data produk/bahan hanya dari outlet yang di-assign
-   [ ] Tombol CRUD muncul sesuai permission
-   [ ] User tanpa permission create tidak bisa klik tombol tambah
-   [ ] User tanpa outlet access tidak bisa create/edit data outlet lain
-   [ ] Error 403 jika coba akses tanpa permission
-   [ ] Error 403 jika coba akses outlet lain

### 7. Files to Update

**Controllers:**

-   `app/Http/Controllers/ProdukController.php`
-   `app/Http/Controllers/BahanController.php`
-   `app/Http/Controllers/KategoriController.php`
-   `app/Http/Controllers/InventoriController.php`
-   `app/Http/Controllers/TransferGudangController.php`
-   `app/Http/Controllers/OpnameController.php`

**Views:**

-   `resources/views/admin/inventaris/produk/index.blade.php`
-   `resources/views/admin/inventaris/bahan/index.blade.php`
-   `resources/views/admin/inventaris/kategori/index.blade.php`
-   `resources/views/admin/inventaris/inventori/index.blade.php`
-   `resources/views/admin/inventaris/transfer-gudang/index.blade.php`
-   `resources/views/admin/inventaris/opname/index.blade.php`

**Sidebar:**

-   `resources/views/partials/sidebar/inventory.blade.php`

**Routes:**

-   `routes/web.php` - Update inventory routes section

### 8. Implementation Status

✅ Permission system ready (259 permissions seeded)
✅ Blade directives ready (@hasPermission, @hasModuleAccess)
✅ Trait HasOutletFilter ready
✅ Middleware CheckPermission ready
✅ Documentation complete

⏳ Pending: Update controllers, views, routes, sidebar for Inventory module

**Recommendation**: Implement one by one starting with Produk (most used), then Bahan, Kategori, Inventori, Transfer, Opname.
