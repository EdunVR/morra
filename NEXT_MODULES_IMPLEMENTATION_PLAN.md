# 🗺️ NEXT MODULES IMPLEMENTATION PLAN

## 📊 OVERVIEW

Rencana implementasi permission & outlet filter untuk modul-modul yang belum terintegrasi.

---

## ✅ COMPLETED MODULES (100%)

### 1. User Management System

-   ✅ User CRUD
-   ✅ Role Management
-   ✅ Permission Management
-   ✅ Outlet Access Control
-   ✅ Activity Logging

### 2. CRM (Customer Relationship Management)

-   ✅ Pelanggan (Customer Management)
-   ✅ Tipe Customer
-   ✅ Export/Import
-   ✅ Permission & Outlet Filter

### 3. Inventaris (Inventory Management)

-   ✅ Outlet
-   ✅ Kategori
-   ✅ Satuan
-   ✅ Produk
-   ✅ Bahan
-   ✅ Inventori
-   ✅ Transfer Gudang
-   ✅ Permission & Outlet Filter

---

## 🎯 PRIORITY 1: FINANCE & ACCOUNTING (HIGH)

### Status: 30% Complete

### Modules to Integrate:

#### 1. RAB (Rencana Anggaran Biaya) - 50% ✅

**Controller**: `FinanceAccountantController.php`
**Status**: Sudah ada outlet filter, perlu permission middleware
**Priority**: HIGH

**Permissions Needed**:

```
finance.rab.view
finance.rab.create
finance.rab.edit
finance.rab.delete
finance.rab.export
finance.rab.approve
```

**Implementation**:

```php
public function __construct()
{
    $this->middleware('permission:finance.rab.view')->only([
        'index', 'data', 'show', 'getBooks'
    ]);

    $this->middleware('permission:finance.rab.create')->only([
        'store', 'generateKode'
    ]);

    $this->middleware('permission:finance.rab.edit')->only([
        'update', 'edit'
    ]);

    $this->middleware('permission:finance.rab.delete')->only([
        'destroy'
    ]);

    $this->middleware('permission:finance.rab.export')->only([
        'exportPdf', 'exportExcel'
    ]);

    $this->middleware('permission:finance.rab.approve')->only([
        'approve', 'reject'
    ]);
}
```

#### 2. Biaya (Expenses) - 50% ✅

**Controller**: `ExpenseController.php` (perlu dicek)
**Status**: Sudah ada outlet filter, perlu permission middleware
**Priority**: HIGH

**Permissions Needed**:

```
finance.biaya.view
finance.biaya.create
finance.biaya.edit
finance.biaya.delete
finance.biaya.export
finance.biaya.approve
```

#### 3. Hutang (Accounts Payable) - 0% ⏳

**Controller**: `HutangController.php`
**Status**: Perlu outlet filter & permission
**Priority**: HIGH

**Permissions Needed**:

```
finance.hutang.view
finance.hutang.create
finance.hutang.edit
finance.hutang.delete
finance.hutang.export
finance.hutang.payment
```

**Implementation Steps**:

1. Add `use App\Traits\HasOutletFilter;`
2. Add permission middleware in constructor
3. Update data() method with `applyOutletFilter()`
4. Update getOutlets() method with `getUserOutlets()`
5. Test all CRUD operations

#### 4. Piutang (Accounts Receivable) - 0% ⏳

**Controller**: `PiutangController.php`
**Status**: Perlu outlet filter & permission
**Priority**: HIGH

**Permissions Needed**:

```
finance.piutang.view
finance.piutang.create
finance.piutang.edit
finance.piutang.delete
finance.piutang.export
finance.piutang.payment
```

#### 5. Jurnal (Journal Entry) - 0% ⏳

**Controller**: `JournalController.php`
**Status**: Perlu outlet filter & permission
**Priority**: MEDIUM

**Permissions Needed**:

```
finance.jurnal.view
finance.jurnal.create
finance.jurnal.edit
finance.jurnal.delete
finance.jurnal.export
finance.jurnal.post
```

#### 6. Aktiva Tetap (Fixed Assets) - 0% ⏳

**Controller**: `FixedAssetController.php`
**Status**: Perlu outlet filter & permission
**Priority**: MEDIUM

**Permissions Needed**:

```
finance.aktiva.view
finance.aktiva.create
finance.aktiva.edit
finance.aktiva.delete
finance.aktiva.export
finance.aktiva.depreciation
```

#### 7. Buku Besar (General Ledger) - 0% ⏳

**Controller**: `LedgerController.php`
**Status**: Perlu outlet filter & permission
**Priority**: MEDIUM

**Permissions Needed**:

```
finance.ledger.view
finance.ledger.export
```

#### 8. Laporan Keuangan (Financial Reports) - 0% ⏳

**Controllers**:

-   `ProfitLossController.php`
-   `BalanceSheetController.php`
-   `CashFlowController.php`

**Status**: Perlu outlet filter & permission
**Priority**: MEDIUM

**Permissions Needed**:

```
finance.laba-rugi.view
finance.laba-rugi.export
finance.neraca.view
finance.neraca.export
finance.arus-kas.view
finance.arus-kas.export
```

---

## 🎯 PRIORITY 2: SALES & MARKETING (MEDIUM)

### Status: 20% Complete

### Modules to Integrate:

#### 1. Invoice Penjualan (Sales Invoice) - 0% ⏳

**Controller**: `SalesManagementController.php`
**Status**: Perlu outlet filter & permission
**Priority**: HIGH

**Permissions Needed**:

```
sales.invoice.view
sales.invoice.create
sales.invoice.edit
sales.invoice.delete
sales.invoice.export
sales.invoice.print
sales.invoice.payment
```

#### 2. Point of Sales (POS) - 0% ⏳

**Controller**: `PosController.php`
**Status**: Perlu outlet filter & permission
**Priority**: HIGH

**Permissions Needed**:

```
pos.kasir.view
pos.kasir.create
pos.kasir.payment
pos.kasir.print
```

#### 3. Laporan Penjualan (Sales Report) - 0% ⏳

**Controller**: `SalesReportController.php`
**Status**: Perlu outlet filter & permission
**Priority**: MEDIUM

**Permissions Needed**:

```
sales.laporan.view
sales.laporan.export
```

---

## 🎯 PRIORITY 3: PROCUREMENT (MEDIUM)

### Status: 10% Complete

### Modules to Integrate:

#### 1. Purchase Order - 0% ⏳

**Controller**: `PurchaseManagementController.php`
**Status**: Perlu outlet filter & permission
**Priority**: HIGH

**Permissions Needed**:

```
procurement.purchase-order.view
procurement.purchase-order.create
procurement.purchase-order.edit
procurement.purchase-order.delete
procurement.purchase-order.export
procurement.purchase-order.approve
procurement.purchase-order.receive
```

#### 2. Vendor/Supplier - 0% ⏳

**Controller**: `SupplierController.php`
**Status**: Perlu outlet filter & permission
**Priority**: MEDIUM

**Permissions Needed**:

```
procurement.supplier.view
procurement.supplier.create
procurement.supplier.edit
procurement.supplier.delete
procurement.supplier.export
```

---

## 🎯 PRIORITY 4: PRODUCTION (LOW)

### Status: 0% Complete

### Modules to Integrate:

#### 1. Work Order - 0% ⏳

**Controller**: `ProduksiController.php`
**Status**: Perlu outlet filter & permission
**Priority**: LOW

**Permissions Needed**:

```
production.work-order.view
production.work-order.create
production.work-order.edit
production.work-order.delete
production.work-order.export
production.work-order.start
production.work-order.complete
```

---

## 🎯 PRIORITY 5: HRM (LOW)

### Status: 0% Complete

### Modules to Integrate:

#### 1. Karyawan (Employee) - 0% ⏳

**Permissions Needed**:

```
hrm.karyawan.view
hrm.karyawan.create
hrm.karyawan.edit
hrm.karyawan.delete
hrm.karyawan.export
```

#### 2. Payroll - 0% ⏳

**Permissions Needed**:

```
hrm.payroll.view
hrm.payroll.create
hrm.payroll.edit
hrm.payroll.delete
hrm.payroll.export
hrm.payroll.process
```

#### 3. Absensi (Attendance) - 0% ⏳

**Permissions Needed**:

```
hrm.absensi.view
hrm.absensi.create
hrm.absensi.edit
hrm.absensi.delete
hrm.absensi.export
```

---

## 📋 IMPLEMENTATION TEMPLATE

### Step-by-Step untuk Setiap Modul:

#### 1. Analyze Controller

```bash
# Check if controller exists
# Check current implementation
# Identify methods that need protection
```

#### 2. Add HasOutletFilter Trait (if needed)

```php
use App\Traits\HasOutletFilter;

class YourController extends Controller
{
    use HasOutletFilter;

    // ...
}
```

#### 3. Add Permission Middleware

```php
public function __construct()
{
    $this->middleware('permission:module.action.view')->only([
        'index', 'data', 'show'
    ]);

    $this->middleware('permission:module.action.create')->only([
        'store'
    ]);

    $this->middleware('permission:module.action.edit')->only([
        'update', 'edit'
    ]);

    $this->middleware('permission:module.action.delete')->only([
        'destroy'
    ]);

    $this->middleware('permission:module.action.export')->only([
        'exportPdf', 'exportExcel'
    ]);
}
```

#### 4. Update Data Query

```php
public function data(Request $request)
{
    $query = YourModel::with('relations');

    // Apply outlet filter
    $query = $this->applyOutletFilter($query, 'outlet_column_name');

    // ... rest of query

    return datatables()->of($query)->make(true);
}
```

#### 5. Update getOutlets Method

```php
public function getOutlets()
{
    $outlets = $this->getUserOutlets()
        ->select('id_outlet', 'nama_outlet')
        ->get();

    return response()->json($outlets);
}
```

#### 6. Update View Files

```blade
@hasPermission('module.action.create')
    <button>Tambah</button>
@endhasPermission

@hasPermission('module.action.edit')
    <button>Edit</button>
@endhasPermission

@hasPermission('module.action.delete')
    <button>Hapus</button>
@endhasPermission
```

#### 7. Add to Sidebar

```php
// resources/views/components/sidebar.blade.php
'Module Name' => [
    'module' => 'module_name',
    'items' => [
        ['Submenu 1', route('module.submenu1.index'), ['module.submenu1.view']],
        ['Submenu 2', route('module.submenu2.index'), ['module.submenu2.view']],
    ]
]
```

#### 8. Add Permissions to Seeder

```php
// database/seeders/CompletePermissionSeeder.php
$permissions[] = [
    'name' => 'module.action.view',
    'display_name' => 'View Module',
    'description' => 'Can view module data',
    'module' => 'module_name'
];
```

#### 9. Test

```bash
# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Test as super admin
# Test as user with permission
# Test as user without permission
# Test outlet filter
```

---

## 📊 PROGRESS TRACKING

### Overall Progress: 40%

| Module Category      | Progress | Status         |
| -------------------- | -------- | -------------- |
| User Management      | 100%     | ✅ Complete    |
| CRM                  | 100%     | ✅ Complete    |
| Inventaris           | 100%     | ✅ Complete    |
| Finance & Accounting | 30%      | 🔄 In Progress |
| Sales & Marketing    | 20%      | ⏳ Pending     |
| Procurement          | 10%      | ⏳ Pending     |
| Production           | 0%       | ⏳ Pending     |
| HRM                  | 0%       | ⏳ Pending     |

### Estimated Time:

-   Finance & Accounting: 8-10 hours
-   Sales & Marketing: 6-8 hours
-   Procurement: 4-6 hours
-   Production: 3-4 hours
-   HRM: 4-6 hours

**Total Estimated**: 25-34 hours

---

## 🎯 RECOMMENDED IMPLEMENTATION ORDER

### Week 1: Finance & Accounting (Priority 1)

1. Day 1-2: RAB & Biaya
2. Day 3-4: Hutang & Piutang
3. Day 5: Jurnal & Aktiva Tetap

### Week 2: Sales & Procurement (Priority 2-3)

1. Day 1-2: Invoice Penjualan & POS
2. Day 3-4: Purchase Order
3. Day 5: Laporan & Supplier

### Week 3: Production & HRM (Priority 4-5)

1. Day 1-2: Work Order & Produksi
2. Day 3-4: Karyawan & Payroll
3. Day 5: Testing & Documentation

---

## 💡 TIPS FOR FASTER IMPLEMENTATION

### 1. Use Code Templates

Create snippets for common patterns:

-   Permission middleware constructor
-   HasOutletFilter implementation
-   View @hasPermission directives

### 2. Batch Similar Modules

Implement similar modules together:

-   All Finance modules in one session
-   All Sales modules in one session

### 3. Test as You Go

Don't wait until all modules are done:

-   Test each module after implementation
-   Fix issues immediately

### 4. Document Changes

Keep track of:

-   Controllers modified
-   Permissions added
-   Views updated

---

## 📝 CHECKLIST PER MODULE

### Before Implementation:

-   [ ] Backup controller file
-   [ ] Check existing implementation
-   [ ] Identify outlet column name
-   [ ] List all methods that need protection

### During Implementation:

-   [ ] Add HasOutletFilter trait (if needed)
-   [ ] Add permission middleware
-   [ ] Update data() method
-   [ ] Update getOutlets() method
-   [ ] Update view files
-   [ ] Add to sidebar
-   [ ] Add permissions to seeder

### After Implementation:

-   [ ] Clear cache
-   [ ] Test as super admin
-   [ ] Test as user with permission
-   [ ] Test as user without permission
-   [ ] Test outlet filter
-   [ ] Test CRUD operations
-   [ ] Test export/import
-   [ ] Document changes

---

**Last Updated**: 2025-11-30
**Status**: Planning Complete
**Next**: Start with Finance & Accounting modules
