# 🚀 START HERE - TESTING PERMISSION & OUTLET FILTER

## ✅ QUICK START CHECKLIST

Ikuti langkah-langkah ini untuk testing sistem permission dan outlet filter yang baru diimplementasikan.

---

## 📋 PRE-TESTING CHECKLIST

### 1. Pastikan Database Sudah Di-Seed ✅

```bash
php artisan db:seed --class=CompletePermissionSeeder
```

**Expected Output**:

```
✓ Permissions seeded successfully
✓ Roles seeded successfully
✓ Super admin created
```

### 2. Clear All Cache ✅

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

**Expected Output**:

```
✓ Configuration cache cleared
✓ Route cache cleared
✓ Application cache cleared
✓ Compiled views cleared
```

### 3. Verify Super Admin Exists ✅

```bash
php artisan tinker
>>> App\Models\User::where('email', 'admin@morra.com')->first()
```

**Expected**: User object with super_admin role

---

## 🧪 TESTING SCENARIOS

### ✅ SCENARIO 1: Super Admin Access

**Login Credentials**:

```
Email: admin@morra.com
Password: password
```

**Test Steps**:

1. [ ] Login berhasil
2. [ ] Dashboard load dengan benar
3. [ ] Sidebar menampilkan semua menu
4. [ ] Buka menu **Inventaris → Kategori**
5. [ ] Datatable load semua data
6. [ ] Button **Tambah** visible
7. [ ] Button **Edit** visible di setiap row
8. [ ] Button **Hapus** visible di setiap row
9. [ ] Button **Export PDF** visible
10. [ ] Button **Export Excel** visible
11. [ ] Dropdown outlet menampilkan semua outlet
12. [ ] Click **Tambah** → Modal muncul
13. [ ] Isi form → Save → Data tersimpan
14. [ ] Click **Edit** → Modal muncul dengan data
15. [ ] Update data → Save → Data terupdate
16. [ ] Click **Hapus** → Konfirmasi muncul → Data terhapus

**Expected Result**: ✅ Semua test passed

---

### ✅ SCENARIO 2: Manager dengan Multiple Outlet

**Setup User** (via Tinker):

```bash
php artisan tinker

# Create manager user
$user = App\Models\User::create([
    'name' => 'Manager Jakarta',
    'email' => 'manager.jkt@morra.com',
    'password' => bcrypt('password'),
    'akses_outlet' => [1, 2] // Jakarta & Bandung
]);

# Assign manager role
$role = App\Models\Role::where('name', 'manager')->first();
$user->roles()->attach($role->id);

# Verify
$user->refresh();
echo "User created: " . $user->email;
echo "\nOutlets: " . json_encode($user->akses_outlet);
echo "\nRole: " . $user->roles->first()->name;
```

**Login Credentials**:

```
Email: manager.jkt@morra.com
Password: password
```

**Test Steps**:

1. [ ] Login berhasil
2. [ ] Dashboard load dengan benar
3. [ ] Sidebar menampilkan menu sesuai permission
4. [ ] Buka menu **Inventaris → Produk**
5. [ ] Datatable hanya menampilkan produk dari outlet 1 & 2
6. [ ] Dropdown outlet hanya menampilkan Jakarta & Bandung
7. [ ] Click **Tambah** → Modal muncul
8. [ ] Dropdown outlet di form hanya show Jakarta & Bandung
9. [ ] Pilih outlet Jakarta → Isi form → Save
10. [ ] Data tersimpan dengan outlet Jakarta
11. [ ] Verify data muncul di datatable
12. [ ] Try filter outlet → Hanya show outlet 1 & 2
13. [ ] Try search → Hanya search di outlet 1 & 2

**Expected Result**: ✅ Hanya bisa akses data outlet 1 & 2

---

### ✅ SCENARIO 3: Staff dengan View-Only Permission

**Setup User** (via Tinker):

```bash
php artisan tinker

# Create staff user
$user = App\Models\User::create([
    'name' => 'Staff Jakarta',
    'email' => 'staff.jkt@morra.com',
    'password' => bcrypt('password'),
    'akses_outlet' => [1] // Jakarta only
]);

# Assign view-only permissions
$permissions = App\Models\Permission::whereIn('name', [
    'inventaris.produk.view',
    'inventaris.kategori.view',
    'inventaris.bahan.view'
])->get();

foreach ($permissions as $perm) {
    $user->permissions()->attach($perm->id);
}

# Verify
$user->refresh();
echo "User created: " . $user->email;
echo "\nOutlets: " . json_encode($user->akses_outlet);
echo "\nPermissions: " . $user->permissions->pluck('name')->implode(', ');
```

**Login Credentials**:

```
Email: staff.jkt@morra.com
Password: password
```

**Test Steps**:

1. [ ] Login berhasil
2. [ ] Dashboard load dengan benar
3. [ ] Sidebar hanya menampilkan menu: Produk, Kategori, Bahan
4. [ ] Menu lain (Outlet, Satuan, dll) TIDAK muncul
5. [ ] Buka menu **Inventaris → Produk**
6. [ ] Datatable hanya menampilkan produk dari outlet 1 (Jakarta)
7. [ ] Button **Tambah** TIDAK visible
8. [ ] Button **Edit** TIDAK visible
9. [ ] Button **Hapus** TIDAK visible
10. [ ] Button **Export** TIDAK visible
11. [ ] Try direct URL: `/admin/inventaris/produk/create`
12. [ ] Expected: Error 403 Forbidden atau redirect
13. [ ] Try API: `/admin/inventaris/produk-data`
14. [ ] Expected: Data load (view permission ada)

**Expected Result**: ✅ Hanya bisa view, tidak bisa CRUD

---

### ✅ SCENARIO 4: User Tanpa Permission

**Setup User** (via Tinker):

```bash
php artisan tinker

# Create user without permissions
$user = App\Models\User::create([
    'name' => 'User No Permission',
    'email' => 'user.noperm@morra.com',
    'password' => bcrypt('password'),
    'akses_outlet' => [1]
]);

# Verify
echo "User created: " . $user->email;
echo "\nPermissions: " . $user->permissions->count();
```

**Login Credentials**:

```
Email: user.noperm@morra.com
Password: password
```

**Test Steps**:

1. [ ] Login berhasil
2. [ ] Dashboard load dengan benar
3. [ ] Sidebar TIDAK menampilkan menu Inventaris
4. [ ] Try direct URL: `/admin/inventaris/produk`
5. [ ] Expected: Error 403 Forbidden atau redirect
6. [ ] Try direct URL: `/admin/inventaris/kategori`
7. [ ] Expected: Error 403 Forbidden atau redirect
8. [ ] Try API: `/admin/inventaris/produk-data`
9. [ ] Expected: Error 403 Forbidden

**Expected Result**: ✅ Tidak bisa akses apapun

---

## 📊 TESTING MATRIX

| Feature       | Super Admin | Manager     | Staff (View) | No Permission |
| ------------- | ----------- | ----------- | ------------ | ------------- |
| View Data     | ✅ All      | ✅ Assigned | ✅ Assigned  | ❌            |
| Create        | ✅          | ✅          | ❌           | ❌            |
| Edit          | ✅          | ✅          | ❌           | ❌            |
| Delete        | ✅          | ✅          | ❌           | ❌            |
| Export        | ✅          | ✅          | ❌           | ❌            |
| Import        | ✅          | ✅          | ❌           | ❌            |
| Outlet Filter | All         | Assigned    | Assigned     | N/A           |
| Menu Visible  | All         | All         | Limited      | None          |

---

## 🔍 VERIFICATION COMMANDS

### Check User Permissions

```bash
php artisan tinker

# Check specific user
$user = App\Models\User::where('email', 'staff.jkt@morra.com')->first();

# Check permissions
$user->permissions->pluck('name');

# Check roles
$user->roles->pluck('name');

# Check outlets
$user->akses_outlet;

# Test permission check
$user->hasPermission('inventaris.produk.view');
$user->hasRole('super_admin');
```

### Check All Permissions

```bash
php artisan tinker

# List all inventaris permissions
App\Models\Permission::where('name', 'LIKE', 'inventaris.%')->pluck('name');

# Count permissions
App\Models\Permission::where('name', 'LIKE', 'inventaris.%')->count();
```

### Check Outlets

```bash
php artisan tinker

# List all outlets
App\Models\Outlet::select('id_outlet', 'nama_outlet')->get();

# Count outlets
App\Models\Outlet::count();
```

---

## 🚨 COMMON ISSUES & SOLUTIONS

### Issue 1: "Permission not found"

```bash
Solution:
php artisan db:seed --class=CompletePermissionSeeder
```

### Issue 2: "403 Forbidden for super admin"

```bash
Solution:
# Check super admin role
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@morra.com')->first();
>>> $user->hasRole('super_admin');

# If false, assign role
>>> $role = App\Models\Role::where('name', 'super_admin')->first();
>>> $user->roles()->attach($role->id);
```

### Issue 3: "Outlet filter not working"

```bash
Solution:
# Check user outlets
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->akses_outlet;

# If null, set outlets
>>> $user->akses_outlet = [1, 2];
>>> $user->save();
```

### Issue 4: "Sidebar menu not showing"

```bash
Solution:
php artisan view:clear
php artisan cache:clear
```

### Issue 5: "Changes not reflected"

```bash
Solution:
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

---

## ✅ POST-TESTING CHECKLIST

### After All Tests Passed:

-   [ ] All 4 scenarios tested
-   [ ] All test steps passed
-   [ ] No errors in browser console
-   [ ] No errors in Laravel log
-   [ ] Performance acceptable
-   [ ] UI/UX smooth

### If Issues Found:

1. [ ] Document the issue
2. [ ] Check Laravel log: `storage/logs/laravel.log`
3. [ ] Check browser console
4. [ ] Try solutions from "Common Issues"
5. [ ] If still not resolved, check documentation

---

## 📚 NEXT STEPS

### If All Tests Passed ✅:

1. **Deploy to Production** (if ready)
2. **Start Finance & Accounting Module** (next priority)
3. **Create User Documentation**
4. **Train Users**

### If Tests Failed ❌:

1. **Review Documentation**:
    - [QUICK_TEST_GUIDE.md](QUICK_TEST_GUIDE.md)
    - [ADD_PERMISSION_MIDDLEWARE_GUIDE.md](ADD_PERMISSION_MIDDLEWARE_GUIDE.md)
2. **Check Implementation**:
    - [INVENTARIS_INTEGRATION_COMPLETE.md](INVENTARIS_INTEGRATION_COMPLETE.md)
3. **Debug**:
    - Check Laravel log
    - Use `Log::info()` for debugging
    - Use Laravel Debugbar

---

## 📞 NEED HELP?

### Documentation:

-   📖 [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md) - All documentation
-   🧪 [QUICK_TEST_GUIDE.md](QUICK_TEST_GUIDE.md) - Detailed testing guide
-   📝 [ADD_PERMISSION_MIDDLEWARE_GUIDE.md](ADD_PERMISSION_MIDDLEWARE_GUIDE.md) - Implementation guide

### Debugging:

```bash
# Enable query log
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());

# Check logs
tail -f storage/logs/laravel.log

# Use tinker
php artisan tinker
```

---

## 🎯 SUCCESS CRITERIA

### All Tests Must Pass:

-   ✅ Super admin bisa akses semua
-   ✅ Manager bisa akses outlet yang assigned
-   ✅ Staff view-only tidak bisa CRUD
-   ✅ User tanpa permission tidak bisa akses
-   ✅ Outlet filter berfungsi dengan benar
-   ✅ Sidebar filtering berfungsi
-   ✅ CRUD operations berfungsi
-   ✅ Export/Import berfungsi (jika ada permission)

### Performance:

-   ✅ Page load < 3 seconds
-   ✅ Datatable load < 2 seconds
-   ✅ No JavaScript errors
-   ✅ No PHP errors

### Security:

-   ✅ Direct URL access blocked tanpa permission
-   ✅ API endpoint protected
-   ✅ Outlet filter prevents unauthorized access
-   ✅ CSRF protection working

---

## 🎉 COMPLETION

Setelah semua test passed:

1. ✅ Mark this checklist as complete
2. ✅ Document any issues found
3. ✅ Update status in [IMPLEMENTATION_COMPLETE_SUMMARY.md](IMPLEMENTATION_COMPLETE_SUMMARY.md)
4. ✅ Proceed to next module (Finance & Accounting)

---

**Good Luck with Testing! 🚀**

**Last Updated**: 2025-11-30
**Version**: 1.0
**Status**: Ready for Testing
