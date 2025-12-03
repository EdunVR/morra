# 🧪 QUICK TEST GUIDE - Permission & Outlet Filter

## 🚀 QUICK START

### 1. Pastikan Permission Sudah Di-Seed

```bash
php artisan db:seed --class=CompletePermissionSeeder
```

### 2. Clear All Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Setup Test Users

#### A. Super Admin (Sudah Ada)

```
Email: admin@morra.com
Password: password
Role: super_admin
Akses: Semua outlet, semua permission
```

#### B. Manager Outlet (Create Manual)

```sql
-- Via Tinker atau SQL
INSERT INTO users (name, email, password, akses_outlet) VALUES
('Manager Jakarta', 'manager.jkt@morra.com', bcrypt('password'), '[1]');

-- Assign role
INSERT INTO user_roles (user_id, role_id) VALUES
((SELECT id FROM users WHERE email='manager.jkt@morra.com'),
 (SELECT id FROM roles WHERE name='manager'));
```

#### C. Staff Outlet (Create Manual)

```sql
INSERT INTO users (name, email, password, akses_outlet) VALUES
('Staff Jakarta', 'staff.jkt@morra.com', bcrypt('password'), '[1]');

-- Assign specific permissions
INSERT INTO user_permissions (user_id, permission_id) VALUES
((SELECT id FROM users WHERE email='staff.jkt@morra.com'),
 (SELECT id FROM permissions WHERE name='inventaris.produk.view'));
```

---

## 🧪 TEST SCENARIOS

### Scenario 1: Super Admin Access ✅

**User**: admin@morra.com
**Expected**:

-   ✅ Lihat semua menu di sidebar
-   ✅ Akses semua modul Inventaris
-   ✅ Lihat data dari semua outlet
-   ✅ Semua CRUD button visible
-   ✅ Bisa export/import

**Test Steps**:

1. Login sebagai admin@morra.com
2. Buka menu Inventaris → Kategori
3. Verify: Datatable load semua data
4. Verify: Button Tambah, Edit, Hapus visible
5. Verify: Button Export/Import visible
6. Verify: Dropdown outlet show semua outlet

---

### Scenario 2: Manager dengan Multiple Outlet ✅

**User**: manager.jkt@morra.com
**Outlet Access**: [1, 2] (Jakarta, Bandung)
**Expected**:

-   ✅ Lihat menu sesuai permission role manager
-   ✅ Lihat data hanya dari outlet 1 & 2
-   ✅ Dropdown outlet hanya show outlet 1 & 2
-   ✅ Create data masuk ke outlet 1 atau 2
-   ✅ Tidak bisa lihat data outlet lain

**Test Steps**:

1. Login sebagai manager.jkt@morra.com
2. Buka menu Inventaris → Produk
3. Verify: Datatable hanya show produk outlet 1 & 2
4. Verify: Dropdown outlet hanya show Jakarta & Bandung
5. Click Tambah → Pilih outlet Jakarta → Save
6. Verify: Data tersimpan dengan outlet Jakarta
7. Try direct URL ke outlet 3 data → Should not visible

---

### Scenario 3: Staff dengan Limited Permission ✅

**User**: staff.jkt@morra.com
**Outlet Access**: [1] (Jakarta)
**Permissions**: inventaris.produk.view only
**Expected**:

-   ✅ Hanya lihat menu Produk di sidebar
-   ✅ Lihat data hanya dari outlet 1
-   ✅ Button Tambah, Edit, Hapus TIDAK visible
-   ✅ Button Export/Import TIDAK visible
-   ✅ Direct URL create/edit/delete → 403 Forbidden

**Test Steps**:

1. Login sebagai staff.jkt@morra.com
2. Verify: Sidebar hanya show menu Produk
3. Buka menu Inventaris → Produk
4. Verify: Datatable hanya show produk outlet Jakarta
5. Verify: Button Tambah TIDAK ada
6. Verify: Button Edit/Hapus TIDAK ada
7. Try direct URL: /admin/inventaris/produk/create
8. Verify: Error 403 Forbidden

---

### Scenario 4: User Tanpa Permission ❌

**User**: user.noperm@morra.com
**Permissions**: None
**Expected**:

-   ❌ Menu Inventaris TIDAK muncul di sidebar
-   ❌ Direct URL access → 403 Forbidden
-   ❌ API endpoint access → 403 Forbidden

**Test Steps**:

1. Login sebagai user.noperm@morra.com
2. Verify: Sidebar tidak show menu Inventaris
3. Try direct URL: /admin/inventaris/produk
4. Verify: Error 403 Forbidden atau redirect
5. Try API: /admin/inventaris/produk-data
6. Verify: Error 403 Forbidden

---

## 📊 TEST MATRIX

### Modul: Kategori

| User Type         | View        | Create | Edit | Delete | Export | Import | Outlet Filter |
| ----------------- | ----------- | ------ | ---- | ------ | ------ | ------ | ------------- |
| Super Admin       | ✅ All      | ✅     | ✅   | ✅     | ✅     | ✅     | All Outlets   |
| Manager           | ✅ Assigned | ✅     | ✅   | ✅     | ✅     | ✅     | Assigned Only |
| Staff (View Only) | ✅ Assigned | ❌     | ❌   | ❌     | ❌     | ❌     | Assigned Only |
| No Permission     | ❌          | ❌     | ❌   | ❌     | ❌     | ❌     | N/A           |

### Modul: Produk

| User Type         | View        | Create | Edit | Delete | Export | Import | Outlet Filter |
| ----------------- | ----------- | ------ | ---- | ------ | ------ | ------ | ------------- |
| Super Admin       | ✅ All      | ✅     | ✅   | ✅     | ✅     | ✅     | All Outlets   |
| Manager           | ✅ Assigned | ✅     | ✅   | ✅     | ✅     | ✅     | Assigned Only |
| Staff (View Only) | ✅ Assigned | ❌     | ❌   | ❌     | ❌     | ❌     | Assigned Only |
| No Permission     | ❌          | ❌     | ❌   | ❌     | ❌     | ❌     | N/A           |

---

## 🔍 DEBUGGING CHECKLIST

### Permission Tidak Bekerja?

```bash
# 1. Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 2. Check permission exists
php artisan tinker
>>> App\Models\Permission::where('name', 'inventaris.kategori.view')->first()

# 3. Check user has permission
>>> $user = App\Models\User::find(1);
>>> $user->hasPermission('inventaris.kategori.view')

# 4. Check middleware registered
>>> Route::getRoutes()->match(Request::create('/admin/inventaris/kategori'))->middleware()
```

### Outlet Filter Tidak Bekerja?

```bash
# 1. Check user akses_outlet
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->akses_outlet

# 2. Check trait imported
# Open controller file, verify:
use App\Traits\HasOutletFilter;

# 3. Check method called
# In controller method, verify:
$query = $this->applyOutletFilter($query, 'id_outlet');

# 4. Test query directly
>>> $user = auth()->user();
>>> $outlets = $user->akses_outlet ?? [];
>>> App\Models\Kategori::whereIn('id_outlet', $outlets)->count()
```

### Sidebar Menu Tidak Muncul?

```bash
# 1. Check permission in sidebar.blade.php
# Open: resources/views/components/sidebar.blade.php
# Verify permission array for menu

# 2. Check user has permission
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->hasPermission('inventaris.kategori.view')

# 3. Clear view cache
php artisan view:clear
```

---

## 🎯 QUICK VERIFICATION COMMANDS

### Check All Permissions

```bash
php artisan tinker
>>> App\Models\Permission::where('name', 'LIKE', 'inventaris.%')->pluck('name')
```

### Check User Permissions

```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->permissions->pluck('name')
```

### Check User Roles

```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->roles->pluck('name')
```

### Check User Outlets

```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->akses_outlet
```

### Test Permission Check

```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->hasPermission('inventaris.kategori.view')
>>> $user->hasRole('super_admin')
```

---

## 📝 MANUAL TEST CHECKLIST

### ✅ Kategori Module

-   [ ] Super admin bisa akses
-   [ ] Manager bisa akses (outlet assigned)
-   [ ] Staff view-only bisa lihat, tidak bisa CRUD
-   [ ] User tanpa permission tidak bisa akses
-   [ ] Outlet filter berfungsi
-   [ ] Create data masuk ke outlet yang benar
-   [ ] Export/Import sesuai permission

### ✅ Produk Module

-   [ ] Super admin bisa akses
-   [ ] Manager bisa akses (outlet assigned)
-   [ ] Staff view-only bisa lihat, tidak bisa CRUD
-   [ ] User tanpa permission tidak bisa akses
-   [ ] Outlet filter berfungsi
-   [ ] Create data masuk ke outlet yang benar
-   [ ] Export/Import sesuai permission

### ✅ Bahan Module

-   [ ] Super admin bisa akses
-   [ ] Manager bisa akses (outlet assigned)
-   [ ] Staff view-only bisa lihat, tidak bisa CRUD
-   [ ] User tanpa permission tidak bisa akses
-   [ ] Outlet filter berfungsi
-   [ ] Create data masuk ke outlet yang benar
-   [ ] Export/Import sesuai permission

### ✅ Satuan Module

-   [ ] Super admin bisa akses
-   [ ] Manager bisa akses
-   [ ] Staff view-only bisa lihat, tidak bisa CRUD
-   [ ] User tanpa permission tidak bisa akses
-   [ ] Export/Import sesuai permission
-   [ ] Note: No outlet filter (global data)

### ✅ Outlet Module

-   [ ] Super admin bisa akses
-   [ ] Manager bisa akses
-   [ ] Staff view-only bisa lihat, tidak bisa CRUD
-   [ ] User tanpa permission tidak bisa akses
-   [ ] Export/Import sesuai permission
-   [ ] Note: No outlet filter (master data)

---

## 🚨 COMMON ERRORS & SOLUTIONS

### Error 1: "Permission not found"

```
Solution:
php artisan db:seed --class=CompletePermissionSeeder
```

### Error 2: "Call to undefined method hasPermission()"

```
Solution:
Check User model has hasPermission() method
Check CheckPermission middleware registered
```

### Error 3: "Trying to get property of non-object"

```
Solution:
Check auth()->user() is not null
Check user is logged in
```

### Error 4: "Outlet filter returns empty"

```
Solution:
Check user->akses_outlet is not null
Check user->akses_outlet is array
Check outlet IDs exist in database
```

### Error 5: "Sidebar menu not showing"

```
Solution:
php artisan view:clear
Check permission array in sidebar.blade.php
Check user has required permission
```

---

## 💡 TIPS

### 1. Use Browser DevTools

-   Check Network tab for API responses
-   Check Console for JavaScript errors
-   Check Application tab for session data

### 2. Use Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

### 3. Enable Query Logging

```php
// In controller method
DB::enableQueryLog();
// ... your queries
dd(DB::getQueryLog());
```

### 4. Use Log::info()

```php
Log::info('User outlets:', ['outlets' => auth()->user()->akses_outlet]);
Log::info('Query result:', ['count' => $query->count()]);
```

---

**Last Updated**: 2025-11-30
**Status**: Ready for Testing
**Next**: Run all test scenarios and verify results
