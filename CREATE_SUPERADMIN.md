# 🚀 Create Super Admin Account

## ⚡ Quick Setup (3 Steps)

### 1️⃣ Run Migration (if not done)

```bash
php artisan migrate
```

### 2️⃣ Run Seeders

```bash
# Run role & permission seeder first
php artisan db:seed --class=RolePermissionSeeder

# Then create super admin
php artisan db:seed --class=SuperAdminSeeder
```

### 3️⃣ Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔐 Login Credentials

```
URL: http://localhost/login
Email: superadmin@morra.com
Password: SuperAdmin@123
```

---

## ✨ What This Super Admin Has

✅ **Full Access:**

-   All 30 permissions
-   Access to all outlets
-   Can manage users & roles
-   Can access all modules

✅ **Account Details:**

-   Name: Super Administrator
-   Email: superadmin@morra.com
-   Phone: 081234567890
-   Status: Active
-   Email Verified: Yes

---

## 🔄 Re-run Seeder (if needed)

Jika Anda perlu reset password atau update super admin:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

Seeder ini **safe to re-run** - akan update existing user jika sudah ada.

---

## 🛡️ Security Features

✅ **Routes Protected:**

-   All `/admin/*` routes require authentication
-   User Management routes require auth
-   Role Management routes require auth
-   Redirect to login if not authenticated

✅ **Password:**

-   Hashed with bcrypt
-   Strong password: SuperAdmin@123
-   Can be changed after first login

---

## 📍 After Login

1. **Dashboard** - Main admin dashboard
2. **Sidebar → Sistem** - Access user management
3. **User Management** - Create/manage users
4. **Role & Permission** - Manage roles

---

## 🧪 Quick Test

```bash
# 1. Run seeders
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SuperAdminSeeder

# 2. Clear cache
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

# 3. Open browser
# URL: http://localhost/login
# Login with credentials above

# 4. Check access
# - Should see all menu items
# - Can access User Management
# - Can access Role Management
```

---

## 🐛 Troubleshooting

### Cannot login

```bash
# Clear all cache
php artisan config:clear
php artisan cache:clear
php artisan session:table
php artisan migrate
```

### User not found

```bash
# Re-run seeder
php artisan db:seed --class=SuperAdminSeeder
```

### Permission denied

```bash
# Re-run permission seeder
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SuperAdminSeeder
```

### Routes not working

```bash
php artisan route:clear
php artisan route:cache
php artisan route:list | grep admin
```

---

## 📊 Database Tables

Seeder akan mengisi:

-   ✅ `roles` - Super Admin role
-   ✅ `permissions` - All 30 permissions
-   ✅ `role_permission` - Link role to permissions
-   ✅ `users` - Super admin user
-   ✅ `user_role` - Link user to role
-   ✅ `user_outlets` - Link user to outlets (if exists)

---

## 🎉 Ready!

Setelah menjalankan seeder, Anda bisa langsung login dan mulai menggunakan sistem!

**Status:** ✅ READY TO USE
