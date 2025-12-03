# ⚡ Setup Super Admin - SEKARANG!

## 🚀 1 Command Setup

Jalankan command ini untuk setup semuanya:

```bash
php artisan setup:superadmin
```

**Command ini akan:**

-   ✅ Run migrations
-   ✅ Create roles & permissions
-   ✅ Create Super Admin user
-   ✅ Clear all cache

---

## 🔐 Login Credentials

```
URL: http://localhost/login
Email: superadmin@morra.com
Password: SuperAdmin@123
```

---

## 📝 Manual Setup (Alternative)

Jika command di atas tidak work, jalankan manual:

```bash
# 1. Migration
php artisan migrate

# 2. Seeders
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SuperAdminSeeder

# 3. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## ✅ What's Protected Now

Semua halaman admin sekarang **require login**:

-   ✅ `/admin/*` - All admin pages
-   ✅ `/admin/users` - User Management
-   ✅ `/admin/roles` - Role Management
-   ✅ Dashboard & all modules

**Redirect to login** jika belum login!

---

## 🎯 After Login

1. Login dengan credentials di atas
2. Klik **Sistem** di sidebar
3. Klik **Submenu**
4. Akses **User Management** atau **Role & Permission**

---

## 🎉 DONE!

Jalankan command dan langsung login!

```bash
php artisan setup:superadmin
```
