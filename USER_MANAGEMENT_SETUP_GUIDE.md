# 🚀 User Management System - Setup Guide

## 📋 Installation Steps

### 1. Register Middleware

Edit `app/Http/Kernel.php` dan tambahkan di `$middlewareAliases`:

```php
protected $middlewareAliases = [
    // ... existing middleware
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'outlet.access' => \App\Http\Middleware\CheckOutletAccess::class,
];
```

### 2. Register Service Provider

Edit `config/app.php` dan tambahkan di `providers`:

```php
'providers' => [
    // ... existing providers
    App\Providers\BladeServiceProvider::class,
],
```

Atau jika menggunakan Laravel 11+, tambahkan di `bootstrap/providers.php`:

```php
return [
    App\Providers\BladeServiceProvider::class,
];
```

### 3. Run Migration & Seeders

```bash
# Run migration
php artisan migrate

# Run seeders
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=DefaultUserSeeder

# Atau run semua seeder sekaligus
php artisan db:seed
```

### 4. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔐 Default Login Credentials

**Super Admin:**

-   Email: `admin@system.com`
-   Password: `Admin@123`

---

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── UserManagementController.php
│   │   ├── RoleManagementController.php
│   │   └── DashboardController.php
│   └── Middleware/
│       ├── CheckPermission.php
│       └── CheckOutletAccess.php
├── Models/
│   ├── User.php (updated)
│   ├── Role.php
│   ├── Permission.php
│   ├── UserOutlet.php
│   └── UserActivityLog.php
├── Helpers/
│   └── PermissionHelper.php
└── Providers/
    └── BladeServiceProvider.php

database/
├── migrations/
│   └── 2025_11_26_create_users_roles_permissions_tables.php
└── seeders/
    ├── RolePermissionSeeder.php
    ├── DefaultUserSeeder.php
    └── DatabaseSeeder.php (updated)

resources/views/
├── auth/
│   └── login.blade.php
└── admin/
    └── user-management/
        ├── users/
        │   ├── index.blade.php
        │   └── modal.blade.php
        └── roles/
            ├── index.blade.php
            └── modal.blade.php
```

---

## 🎯 Usage Examples

### In Controllers

```php
// Check permission
if (!auth()->user()->hasPermission('users.create')) {
    abort(403);
}

// Check role
if (auth()->user()->hasRole('Super Admin')) {
    // Do something
}

// Check outlet access
if (!auth()->user()->hasOutletAccess($outletId)) {
    abort(403);
}

// Get accessible outlets
$outletIds = auth()->user()->getAccessibleOutletIds();
```

### In Routes

```php
// Protect route with permission
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:users.view');

// Protect route with outlet access
Route::get('/outlet/{id}', [OutletController::class, 'show'])
    ->middleware('outlet.access');
```

### In Blade Templates

```blade
@hasPermission('users.create')
    <button>Create User</button>
@endhasPermission

@hasRole('Super Admin')
    <div>Admin Panel</div>
@endhasRole

@hasAnyRole('Admin', 'Manager')
    <div>Management Panel</div>
@endhasAnyRole

@hasOutletAccess($outletId)
    <div>Outlet Content</div>
@endhasOutletAccess
```

---

## 🔧 Configuration

### Add to Sidebar Menu

Edit your sidebar layout file dan tambahkan:

```blade
@hasPermission('users.view')
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link">
        <i class="fas fa-users"></i>
        <span>User Management</span>
    </a>
</li>
@endhasPermission

@hasPermission('roles.view')
<li class="nav-item">
    <a href="{{ route('admin.roles.index') }}" class="nav-link">
        <i class="fas fa-user-shield"></i>
        <span>Role Management</span>
    </a>
</li>
@endhasPermission
```

---

## 🧪 Testing

### Test Login

1. Buka browser: `http://localhost/login`
2. Login dengan credentials default
3. Seharusnya redirect ke dashboard

### Test User Management

1. Akses: `http://localhost/admin/users`
2. Coba create, edit, delete user
3. Test assign roles dan outlets

### Test Role Management

1. Akses: `http://localhost/admin/roles`
2. Coba create custom role
3. Test assign permissions

### Test Permissions

1. Login sebagai user dengan role berbeda
2. Coba akses menu yang tidak ada permission-nya
3. Seharusnya muncul error 403

---

## 📊 Default Roles & Permissions

### Super Admin

-   Full access ke semua fitur
-   Tidak bisa dihapus atau diubah

### Admin

-   Akses ke semua modul
-   Tidak bisa manage Super Admin

### User

-   Akses terbatas sesuai permission
-   Hanya bisa view data

### Permission Groups

-   **Users**: users.view, users.create, users.edit, users.delete
-   **Roles**: roles.view, roles.create, roles.edit, roles.delete
-   **Outlets**: outlets.view, outlets.create, outlets.edit, outlets.delete
-   **Finance**: finance.view, finance.create, finance.edit, finance.delete
-   **Inventory**: inventory.view, inventory.create, inventory.edit, inventory.delete
-   **Sales**: sales.view, sales.create, sales.edit, sales.delete
-   **Purchase**: purchase.view, purchase.create, purchase.edit, purchase.delete
-   **Reports**: reports.view, reports.export

---

## 🔒 Security Features

1. **Password Hashing**: Menggunakan bcrypt
2. **Activity Logging**: Semua aktivitas user tercatat
3. **Session Management**: Auto logout setelah inaktif
4. **Permission-based Access**: Granular permission control
5. **Outlet-based Access**: Multi-outlet support
6. **Role Hierarchy**: Super Admin > Admin > User

---

## 🐛 Troubleshooting

### Login tidak berfungsi

```bash
php artisan config:clear
php artisan cache:clear
```

### Permission tidak bekerja

```bash
# Re-run seeder
php artisan db:seed --class=RolePermissionSeeder
```

### Route tidak ditemukan

```bash
php artisan route:clear
php artisan route:cache
```

### View error

```bash
php artisan view:clear
```

---

## 📝 Next Steps

1. ✅ Setup middleware di Kernel.php
2. ✅ Register BladeServiceProvider
3. ✅ Run migration & seeders
4. ✅ Test login functionality
5. ✅ Add menu items to sidebar
6. ✅ Test permissions
7. ⏭️ Customize roles sesuai kebutuhan
8. ⏭️ Add more permissions jika diperlukan

---

## 🎉 System Ready!

User Management System sudah siap digunakan. Login dengan credentials default dan mulai manage users & roles!
