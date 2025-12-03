# Quick Fix Guide - MORRA ERP

## Error: Route Not Defined

### Gejala

```
Route [nama.route] not defined.
```

### Langkah Cepat

1. **Cek route yang tersedia:**

```bash
php artisan route:list | Select-String "nama-route"
```

2. **Clear cache:**

```bash
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

3. **Test route:**

```bash
php test_sidebar_routes.php
```

---

## Error: View Not Found

### Gejala

```
View [nama.view] not found.
```

### Langkah Cepat

1. **Cek file view:**

```bash
Get-ChildItem -Path resources/views -Filter "nama*.blade.php" -Recurse
```

2. **Clear view cache:**

```bash
php artisan view:clear
Remove-Item storage/framework/views/*.php -Force
```

---

## Error: 419 Page Expired

### Gejala

```
419 | Page Expired
```

### Langkah Cepat

1. **Clear session:**

```bash
php artisan session:clear
```

2. **Clear browser cache** (Ctrl+Shift+Delete)

3. **Pastikan CSRF token ada di form:**

```blade
@csrf
```

---

## Error: 500 Internal Server Error

### Langkah Cepat

1. **Cek log error:**

```bash
Get-Content storage/logs/laravel.log -Tail 50
```

2. **Enable debug mode** (`.env`):

```
APP_DEBUG=true
```

3. **Clear semua cache:**

```bash
php artisan optimize:clear
```

---

## Error: Database Connection

### Gejala

```
SQLSTATE[HY000] [2002] Connection refused
```

### Langkah Cepat

1. **Cek database service:**

```bash
# Pastikan MySQL/MariaDB running
```

2. **Cek konfigurasi** (`.env`):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

3. **Test koneksi:**

```bash
php artisan migrate:status
```

---

## Error: Permission Denied

### Gejala

```
Permission denied
```

### Langkah Cepat

1. **Set permission folder:**

```bash
# Windows (Run as Administrator)
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T
```

---

## Maintenance Commands

### Clear All Cache

```bash
php artisan optimize:clear
```

### Rebuild Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Reset Application

```bash
php artisan optimize:clear
composer dump-autoload
php artisan migrate:fresh --seed
```

---

## Testing Commands

### Test Routes

```bash
php test_sidebar_routes.php
```

### Test Database

```bash
php artisan migrate:status
```

### Test Queue

```bash
php artisan queue:work --once
```

---

## Useful Commands

### Generate Key

```bash
php artisan key:generate
```

### Create Symlink

```bash
php artisan storage:link
```

### List Routes

```bash
php artisan route:list
```

### List Config

```bash
php artisan config:show
```

---

## Emergency Reset

Jika aplikasi benar-benar error dan tidak bisa diakses:

```bash
# 1. Clear semua cache
php artisan optimize:clear

# 2. Hapus compiled views
Remove-Item storage/framework/views/*.php -Force

# 3. Clear log
Clear-Content storage/logs/laravel.log

# 4. Rebuild autoload
composer dump-autoload

# 5. Restart web server
# Apache: Restart dari XAMPP Control Panel
# Nginx: Restart service

# 6. Clear browser cache
# Ctrl+Shift+Delete
```

---

## Preventive Maintenance

### Daily

-   Monitor log error
-   Backup database

### Weekly

-   Clear cache
-   Update dependencies
-   Check disk space

### Monthly

-   Full backup
-   Security audit
-   Performance optimization

---

## Support

Jika masalah tidak teratasi:

1. **Dokumentasi:** Baca file `ERROR_FIX_SUMMARY.md`
2. **Test Script:** Jalankan `php test_sidebar_routes.php`
3. **Log:** Cek `storage/logs/laravel.log`
4. **Contact:** WhatsApp +62 857-9548-3498

---

**Last Updated:** 2 Desember 2024
