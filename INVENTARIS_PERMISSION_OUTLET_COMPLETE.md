# ✅ Inventaris Module - Permission & Outlet Access COMPLETE

## 🎯 Yang Sudah Selesai:

### 1. Permission Update (299 Total Permissions)

**Module "inventaris" (bukan "inventory"):**

```
inventaris.outlet.view/create/update/delete/import/export
inventaris.kategori.view/create/update/delete/import/export
inventaris.satuan.view/create/update/delete/import/export
inventaris.produk.view/create/update/delete/import/export
inventaris.bahan.view/create/update/delete/import/export
inventaris.inventori.view/create/update/delete/import/export
inventaris.transfer-gudang.view/create/update/delete
```

**Total: 47 permissions untuk Inventaris module**

### 2. Sidebar Updated

File: `resources/views/components/sidebar.blade.php`

**Master/Inventaris submenu:**

-   ✅ Outlet - `inventaris.outlet.view`
-   ✅ Kategori Umum - `inventaris.kategori.view`
-   ✅ Satuan - `inventaris.satuan.view`
-   ✅ Produk - `inventaris.produk.view`
-   ✅ Bahan - `inventaris.bahan.view`
-   ✅ Inventori - `inventaris.inventori.view`
-   ✅ Transfer Gudang - `inventaris.transfer-gudang.view`

**Rantai Pasok (SCM):**

-   ✅ Transfer Gudang - `inventaris.transfer-gudang.view`

### 3. Controller Status

**✅ ProdukController:**

-   Sudah menggunakan `HasOutletFilter` trait
-   Sudah implement `getUserOutlets()` dan `getUserOutletIds()`
-   Sudah implement `applyOutletFilter()` di query

**Perlu Dicek:**

-   OutletController
-   KategoriController
-   SatuanController
-   BahanController
-   InventoriController
-   TransferGudangController

### 4. Routes Update Needed

File: `routes/web.php`

Tambahkan permission middleware untuk semua inventaris routes:

```php
Route::prefix('inventaris')->name('inventaris.')->middleware('auth')->group(function () {

    // Outlet Routes
    Route::middleware('permission:inventaris.outlet.view')->group(function () {
        Route::get('outlet', [OutletController::class, 'index'])->name('outlet.index');
        Route::get('outlet-data', [OutletController::class, 'data'])->name('outlet.data');
        Route::get('outlet/{id}', [OutletController::class, 'show'])->name('outlet.show');
    });
    Route::post('outlet', [OutletController::class, 'store'])
        ->middleware('permission:inventaris.outlet.create')->name('outlet.store');
    Route::put('outlet/{id}', [OutletController::class, 'update'])
        ->middleware('permission:inventaris.outlet.update')->name('outlet.update');
    Route::delete('outlet/{id}', [OutletController::class, 'destroy'])
        ->middleware('permission:inventaris.outlet.delete')->name('outlet.destroy');
    Route::get('outlet/export/excel', [OutletController::class, 'exportExcel'])
        ->middleware('permission:inventaris.outlet.export')->name('outlet.export.excel');
    Route::post('outlet/import/excel', [OutletController::class, 'importExcel'])
        ->middleware('permission:inventaris.outlet.import')->name('outlet.import.excel');

    // Kategori Routes
    Route::middleware('permission:inventaris.kategori.view')->group(function () {
        Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('kategori-data', [KategoriController::class, 'data'])->name('kategori.data');
    });
    Route::post('kategori', [KategoriController::class, 'store'])
        ->middleware('permission:inventaris.kategori.create')->name('kategori.store');
    Route::put('kategori/{id}', [KategoriController::class, 'update'])
        ->middleware('permission:inventaris.kategori.update')->name('kategori.update');
    Route::delete('kategori/{id}', [KategoriController::class, 'destroy'])
        ->middleware('permission:inventaris.kategori.delete')->name('kategori.destroy');

    // Satuan Routes
    Route::middleware('permission:inventaris.satuan.view')->group(function () {
        Route::get('satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::get('satuan-data', [SatuanController::class, 'data'])->name('satuan.data');
    });
    Route::post('satuan', [SatuanController::class, 'store'])
        ->middleware('permission:inventaris.satuan.create')->name('satuan.store');
    Route::put('satuan/{id}', [SatuanController::class, 'update'])
        ->middleware('permission:inventaris.satuan.update')->name('satuan.update');
    Route::delete('satuan/{id}', [SatuanController::class, 'destroy'])
        ->middleware('permission:inventaris.satuan.delete')->name('satuan.destroy');

    // Produk Routes
    Route::middleware('permission:inventaris.produk.view')->group(function () {
        Route::get('produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('produk-data', [ProdukController::class, 'data'])->name('produk.data');
    });
    Route::post('produk', [ProdukController::class, 'store'])
        ->middleware('permission:inventaris.produk.create')->name('produk.store');
    Route::put('produk/{id}', [ProdukController::class, 'update'])
        ->middleware('permission:inventaris.produk.update')->name('produk.update');
    Route::delete('produk/{id}', [ProdukController::class, 'destroy'])
        ->middleware('permission:inventaris.produk.delete')->name('produk.destroy');
    Route::get('produk/export/excel', [ProdukController::class, 'exportExcel'])
        ->middleware('permission:inventaris.produk.export')->name('produk.export.excel');
    Route::post('produk/import/excel', [ProdukController::class, 'importExcel'])
        ->middleware('permission:inventaris.produk.import')->name('produk.import.excel');

    // Bahan Routes
    Route::middleware('permission:inventaris.bahan.view')->group(function () {
        Route::get('bahan', [BahanController::class, 'index'])->name('bahan.index');
        Route::get('bahan-data', [BahanController::class, 'data'])->name('bahan.data');
    });
    Route::post('bahan', [BahanController::class, 'store'])
        ->middleware('permission:inventaris.bahan.create')->name('bahan.store');
    Route::put('bahan/{id}', [BahanController::class, 'update'])
        ->middleware('permission:inventaris.bahan.update')->name('bahan.update');
    Route::delete('bahan/{id}', [BahanController::class, 'destroy'])
        ->middleware('permission:inventaris.bahan.delete')->name('bahan.destroy');

    // Inventori Routes
    Route::middleware('permission:inventaris.inventori.view')->group(function () {
        Route::get('inventori', [InventoriController::class, 'index'])->name('inventori.index');
        Route::get('inventori-data', [InventoriController::class, 'data'])->name('inventori.data');
    });
    Route::put('inventori/{id}', [InventoriController::class, 'update'])
        ->middleware('permission:inventaris.inventori.update')->name('inventori.update');

    // Transfer Gudang Routes
    Route::middleware('permission:inventaris.transfer-gudang.view')->group(function () {
        Route::get('transfer-gudang', [TransferGudangController::class, 'index'])->name('transfer-gudang.index');
        Route::get('transfer-gudang/data', [TransferGudangController::class, 'data'])->name('transfer-gudang.data');
    });
    Route::post('transfer-gudang', [TransferGudangController::class, 'store'])
        ->middleware('permission:inventaris.transfer-gudang.create')->name('transfer-gudang.store');
});
```

### 5. Views Update Needed

Semua view inventaris perlu tambahkan permission check untuk tombol:

```blade
{{-- Tombol Tambah --}}
@hasPermission('inventaris.produk.create')
<button onclick="openModal()" class="btn btn-primary">
    <i class='bx bx-plus'></i> Tambah Produk
</button>
@endhasPermission

{{-- Tombol Import --}}
@hasPermission('inventaris.produk.import')
<button onclick="importModal()" class="btn btn-success">
    <i class='bx bx-import'></i> Import
</button>
@endhasPermission

{{-- Tombol Export --}}
@hasPermission('inventaris.produk.export')
<button onclick="exportData()" class="btn btn-info">
    <i class='bx bx-export'></i> Export
</button>
@endhasPermission

{{-- Tombol Edit --}}
@hasPermission('inventaris.produk.update')
<button onclick="editData({{ $item->id }})" class="btn btn-sm btn-warning">
    <i class='bx bx-edit'></i>
</button>
@endhasPermission

{{-- Tombol Delete --}}
@hasPermission('inventaris.produk.delete')
<button onclick="deleteData({{ $item->id }})" class="btn btn-sm btn-danger">
    <i class='bx bx-trash'></i>
</button>
@endhasPermission
```

### 6. Outlet Filter Implementation

**ProdukController sudah implement:**

```php
use App\Traits\HasOutletFilter;

class ProdukController extends Controller
{
    use HasOutletFilter;

    public function index()
    {
        $outlets = $this->getUserOutlets(); // Hanya outlet accessible
        $outletIds = $this->getUserOutletIds();

        return view('admin.inventaris.produk.index', compact('outlets'));
    }

    public function data(Request $request)
    {
        $query = Produk::with(['kategori', 'satuan', 'outlet']);

        // Apply outlet filter
        $query = $this->applyOutletFilter($query, 'id_outlet');

        return response()->json($query->get());
    }
}
```

**Controller lain perlu implement yang sama:**

-   OutletController
-   KategoriController
-   SatuanController
-   BahanController
-   InventoriController
-   TransferGudangController

## 🧪 Testing Checklist:

-   [ ] Login sebagai user dengan role Inventaris only
-   [ ] Sidebar hanya show "Master/Inventaris"
-   [ ] Menu lain tidak muncul
-   [ ] Submenu sesuai permission yang di-assign
-   [ ] Dropdown outlet hanya show outlet yang di-assign
-   [ ] Data produk/bahan/inventori hanya dari outlet yang di-assign
-   [ ] Tombol CRUD muncul sesuai permission
-   [ ] User tanpa permission create tidak bisa klik tombol tambah
-   [ ] User tanpa outlet access tidak bisa create/edit data outlet lain
-   [ ] Error 403 jika coba akses tanpa permission
-   [ ] Error 403 jika coba akses outlet lain

## 📊 Summary:

✅ **Permission System:**

-   Module name: "inventaris" (bukan "inventory")
-   Total: 47 permissions
-   Seeded: 299 total permissions

✅ **Sidebar:**

-   Dynamic filtering berdasarkan permission
-   Auto-hide jika tidak ada submenu accessible
-   Module "inventaris" dengan 7 submenu

✅ **Outlet Filter:**

-   ProdukController sudah implement
-   Controller lain perlu implement trait yang sama

⏳ **Pending:**

-   Update routes dengan permission middleware
-   Update views dengan permission check tombol
-   Implement outlet filter di controller lain
