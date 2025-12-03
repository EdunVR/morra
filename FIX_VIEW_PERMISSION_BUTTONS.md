# 🔧 FIX: Add Permission Directives to View Buttons

## 📋 ISSUE

Button Create, Edit, Delete, Export, dan Import masih muncul meskipun user tidak memiliki permission.

## ✅ SOLUTION

Tambahkan `@hasPermission` directive ke semua button di view files.

---

## 📝 FILES TO UPDATE

### 1. Satuan ✅ (DONE)

**File**: `resources/views/admin/inventaris/satuan/index.blade.php`

**Changes**:

-   ✅ Tambah button wrapped dengan `@hasPermission('inventaris.satuan.create')`
-   ✅ Edit button wrapped dengan `@hasPermission('inventaris.satuan.edit')`
-   ✅ Delete button wrapped dengan `@hasPermission('inventaris.satuan.delete')`
-   ✅ Export buttons wrapped dengan `@hasPermission('inventaris.satuan.export')`
-   ✅ Import button wrapped dengan `@hasPermission('inventaris.satuan.import')`

### 2. Kategori ⏳ (PENDING)

**File**: `resources/views/admin/inventaris/kategori/index.blade.php`

**Pattern to Apply**:

```blade
<!-- Header Buttons -->
@hasPermission('inventaris.kategori.create')
<button x-on:click="openCreate()">Tambah Kategori</button>
@endhasPermission

@hasPermission('inventaris.kategori.export')
<button x-on:click="exportPdf()">Export PDF</button>
<button x-on:click="exportExcel()">Export Excel</button>
<button x-on:click="downloadTemplate()">Template</button>
@endhasPermission

@hasPermission('inventaris.kategori.import')
<label>Import Excel</label>
@endhasPermission

<!-- Grid View Buttons -->
@hasPermission('inventaris.kategori.edit')
<button x-on:click="openEdit(u)">Edit</button>
@endhasPermission

@hasPermission('inventaris.kategori.delete')
<button x-on:click="confirmDelete(u)">Hapus</button>
@endhasPermission

<!-- Table View Buttons -->
@hasPermission('inventaris.kategori.edit')
<button x-on:click="openEdit(u)">Edit</button>
@endhasPermission

@hasPermission('inventaris.kategori.delete')
<button x-on:click="confirmDelete(u)">Hapus</button>
@endhasPermission
```

### 3. Outlet ⏳ (PENDING)

**File**: `resources/views/admin/inventaris/outlet/index.blade.php`

**Pattern to Apply**:

```blade
<!-- Header Buttons -->
@hasPermission('inventaris.outlet.create')
<button x-on:click="openCreate()">Tambah Outlet</button>
@endhasPermission

@hasPermission('inventaris.outlet.export')
<button x-on:click="exportPdf()">Export PDF</button>
<button x-on:click="exportExcel()">Export Excel</button>
<button x-on:click="downloadTemplate()">Template</button>
@endhasPermission

@hasPermission('inventaris.outlet.import')
<label>Import Excel</label>
@endhasPermission

<!-- Grid View Buttons -->
@hasPermission('inventaris.outlet.edit')
<button x-on:click="openEdit(u)">Edit</button>
@endhasPermission

@hasPermission('inventaris.outlet.delete')
<button x-on:click="confirmDelete(u)">Hapus</button>
@endhasPermission

<!-- Table View Buttons -->
@hasPermission('inventaris.outlet.edit')
<button x-on:click="openEdit(u)">Edit</button>
@endhasPermission

@hasPermission('inventaris.outlet.delete')
<button x-on:click="confirmDelete(u)">Hapus</button>
@endhasPermission
```

### 4. Produk ⏳ (PENDING)

**File**: `resources/views/admin/inventaris/produk/index.blade.php`

**Pattern**: Same as above with `inventaris.produk.*` permissions

### 5. Bahan ✅ (ALREADY DONE from previous session)

**File**: `resources/views/admin/inventaris/bahan/index.blade.php`

---

## 🔧 MANUAL FIX REQUIRED

Karena file view cukup besar dan kompleks, perubahan perlu dilakukan manual dengan pattern berikut:

### Step 1: Locate Button Sections

Cari bagian-bagian ini di setiap file:

1. Header buttons (Tambah, Export, Import)
2. Grid view action buttons (Edit, Hapus)
3. Table view action buttons (Edit, Hapus)

### Step 2: Wrap with @hasPermission

Wrap setiap button dengan directive yang sesuai:

```blade
@hasPermission('module.action.permission')
    <button>Action</button>
@endhasPermission
```

### Step 3: Test

Setelah update, test dengan:

1. Login sebagai user tanpa permission
2. Verify button tidak muncul
3. Login sebagai user dengan permission
4. Verify button muncul

---

## 📊 PERMISSION MAPPING

| Module   | Create                     | Edit                     | Delete                     | Export                     | Import                     |
| -------- | -------------------------- | ------------------------ | -------------------------- | -------------------------- | -------------------------- |
| Outlet   | inventaris.outlet.create   | inventaris.outlet.edit   | inventaris.outlet.delete   | inventaris.outlet.export   | inventaris.outlet.import   |
| Kategori | inventaris.kategori.create | inventaris.kategori.edit | inventaris.kategori.delete | inventaris.kategori.export | inventaris.kategori.import |
| Satuan   | inventaris.satuan.create   | inventaris.satuan.edit   | inventaris.satuan.delete   | inventaris.satuan.export   | inventaris.satuan.import   |
| Produk   | inventaris.produk.create   | inventaris.produk.edit   | inventaris.produk.delete   | inventaris.produk.export   | inventaris.produk.import   |
| Bahan    | inventaris.bahan.create    | inventaris.bahan.edit    | inventaris.bahan.delete    | inventaris.bahan.export    | inventaris.bahan.import    |

---

## ✅ COMPLETION CHECKLIST

### Satuan:

-   [x] Header: Tambah button
-   [x] Header: Export buttons
-   [x] Header: Import button
-   [x] Grid: Edit button
-   [x] Grid: Delete button
-   [x] Table: Edit button
-   [x] Table: Delete button

### Kategori:

-   [ ] Header: Tambah button
-   [ ] Header: Export buttons
-   [ ] Header: Import button
-   [ ] Grid: Edit button
-   [ ] Grid: Delete button
-   [ ] Table: Edit button
-   [ ] Table: Delete button

### Outlet:

-   [ ] Header: Tambah button
-   [ ] Header: Export buttons
-   [ ] Header: Import button
-   [ ] Grid: Edit button
-   [ ] Grid: Delete button
-   [ ] Table: Edit button
-   [ ] Table: Delete button

### Produk:

-   [ ] Header: Tambah button
-   [ ] Header: Export buttons
-   [ ] Header: Import button
-   [ ] Grid: Edit button
-   [ ] Grid: Delete button
-   [ ] Table: Edit button
-   [ ] Table: Delete button

### Bahan:

-   [x] Already done (from previous session)

---

## 🧪 TESTING SCRIPT

### Test Permission Hiding:

```bash
# 1. Create test user without permissions
php artisan tinker
>>> $user = App\Models\User::create([
    'name' => 'Test View Only',
    'email' => 'test.view@test.com',
    'password' => bcrypt('password'),
    'akses_outlet' => [1]
]);

# 2. Assign only view permission
>>> $perm = App\Models\Permission::where('name', 'inventaris.kategori.view')->first();
>>> $user->permissions()->attach($perm->id);

# 3. Login and verify
# - Tambah button should NOT appear
# - Edit button should NOT appear
# - Delete button should NOT appear
# - Export button should NOT appear
# - Import button should NOT appear
```

---

## 💡 TIPS

### 1. Use Find & Replace

Untuk mempercepat, gunakan find & replace dengan pattern:

-   Find: `<button x-on:click="openCreate()`
-   Replace: `@hasPermission('inventaris.MODULE.create')\n<button x-on:click="openCreate(`

### 2. Check Both Grid and Table Views

Pastikan update button di kedua view (grid dan table)

### 3. Test After Each File

Test setelah update setiap file untuk memastikan tidak ada error

### 4. Clear View Cache

```bash
php artisan view:clear
```

---

**Status**: Satuan ✅ | Kategori ⏳ | Outlet ⏳ | Produk ⏳ | Bahan ✅
**Next**: Update Kategori, Outlet, dan Produk views manually
