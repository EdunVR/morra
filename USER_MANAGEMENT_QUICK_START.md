# ⚡ User Management System - Quick Start

## 🚀 3 Langkah Setup

### Step 1: Run Migration & Seeders

```bash
php artisan migrate
php artisan db:seed
```

### Step 2: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Step 3: Login

-   URL: `http://localhost/login`
-   Email: `admin@system.com`
-   Password: `Admin@123`

---

## ✅ System Ready!

Semua konfigurasi sudah otomatis ter-register:

-   ✅ Middleware registered di `bootstrap/app.php`
-   ✅ BladeServiceProvider registered di `bootstrap/providers.php`
-   ✅ Routes added di `routes/web.php`
-   ✅ Migration & Seeders ready

---

## 📍 Access URLs

-   **Login**: `/login`
-   **User Management**: `/admin/users`
-   **Role Management**: `/admin/roles`

---

## 🎯 Default Roles

1. **Super Admin** - Full access
2. **Admin** - Management access
3. **User** - Limited access

---

## 📝 Next: Add to Sidebar

Edit your sidebar layout dan tambahkan:

```blade
@hasPermission('users.view')
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link">
        <i class="fas fa-users"></i> User Management
    </a>
</li>
@endhasPermission

@hasPermission('roles.view')
<li class="nav-item">
    <a href="{{ route('admin.roles.index') }}" class="nav-link">
        <i class="fas fa-user-shield"></i> Role Management
    </a>
</li>
@endhasPermission
```

---

## 🎉 Done!

Lihat `USER_MANAGEMENT_SETUP_GUIDE.md` untuk dokumentasi lengkap.
