# Fix Neraca - Sidebar & API Routes

## Masalah yang Diperbaiki

### 1. ❌ Link Sidebar Neraca Salah

**Masalah**: Link sidebar menggunakan route lama `admin.keuangan.neraca.index`
**Solusi**: Update ke route baru `finance.neraca.index`

### 2. ❌ Error 404 pada `/finance/outlets`

**Masalah**:

-   URL menggunakan relative path `/finance/outlets`
-   Di production (https://group.dahana-boiler.com) menyebabkan 404
-   Kemungkinan karena base URL atau routing issue

**Solusi**:

-   Gunakan Laravel route helper `{{ route('finance.outlets.data') }}`
-   Semua fetch URL diupdate menggunakan route helper atau url helper

## Perubahan yang Dilakukan

### 1. Sidebar (`resources/views/components/sidebar.blade.php`)

**Sebelum:**

```php
['Neraca', route('admin.keuangan.neraca.index')],
```

**Sesudah:**

```php
['Neraca', route('finance.neraca.index')],
```

### 2. Neraca JavaScript (`resources/views/admin/finance/neraca/index.blade.php`)

#### a. Load Outlets

**Sebelum:**

```javascript
const response = await fetch("/finance/outlets");
```

**Sesudah:**

```javascript
const response = await fetch('{{ route('finance.outlets.data') }}');
```

#### b. Load Neraca Data

**Sebelum:**

```javascript
const response = await fetch(`/finance/neraca/data?${params}`);
```

**Sesudah:**

```javascript
const response = await fetch(`{{ route('finance.neraca.data') }}?${params}`);
```

#### c. Load Account Details

**Sebelum:**

```javascript
const response = await fetch(
    `/finance/neraca/account-details/${accountId}?${params}`
);
```

**Sesudah:**

```javascript
const response = await fetch(
    `{{ url('finance/neraca/account-details') }}/${accountId}?${params}`
);
```

#### d. Export XLSX

**Sebelum:**

```javascript
window.location.href = `/finance/neraca/export/xlsx?${params}`;
```

**Sesudah:**

```javascript
window.location.href = `{{ route('finance.neraca.export.xlsx') }}?${params}`;
```

#### e. Export PDF

**Sebelum:**

```javascript
window.location.href = `/finance/neraca/export/pdf?${params}`;
```

**Sesudah:**

```javascript
window.location.href = `{{ route('finance.neraca.export.pdf') }}?${params}`;
```

## Keuntungan Menggunakan Route Helper

### 1. **Environment Agnostic**

-   Otomatis menyesuaikan dengan base URL aplikasi
-   Bekerja di localhost, staging, dan production

### 2. **Maintainability**

-   Jika route berubah, hanya perlu update di `routes/web.php`
-   Tidak perlu update manual di semua view

### 3. **Type Safety**

-   Laravel akan error jika route tidak ada
-   Mencegah typo pada URL

### 4. **HTTPS/HTTP Handling**

-   Otomatis menggunakan protokol yang sesuai
-   Tidak perlu hardcode http/https

## Testing

### Manual Testing Checklist

-   [x] Klik menu Neraca di sidebar → harus redirect ke halaman neraca
-   [x] Halaman neraca load → dropdown outlet harus terisi
-   [x] Pilih outlet dan tanggal → data neraca harus muncul
-   [x] Klik akun → modal detail transaksi harus muncul
-   [x] Export XLSX → file harus terdownload
-   [x] Export PDF → file harus terdownload

### Production Testing

```bash
# Test di production
curl https://group.dahana-boiler.com/finance/outlets

# Expected: JSON response dengan data outlets
# Actual: Harus return 200 OK dengan data
```

## Route List

Verifikasi semua route neraca terdaftar:

```bash
php artisan route:list --name=finance.neraca
php artisan route:list --name=finance.outlets
```

**Output yang diharapkan:**

```
GET|HEAD  finance/outlets ........................ finance.outlets.data
GET|HEAD  finance/neraca ......................... finance.neraca.index
GET|HEAD  finance/neraca/data .................... finance.neraca.data
GET|HEAD  finance/neraca/account-details/{id} .... finance.neraca.account-details
GET|HEAD  finance/neraca/export/pdf .............. finance.neraca.export.pdf
GET|HEAD  finance/neraca/export/xlsx ............. finance.neraca.export.xlsx
```

## Troubleshooting

### Jika masih 404 di production:

1. **Clear route cache:**

```bash
php artisan route:clear
php artisan route:cache
```

2. **Clear config cache:**

```bash
php artisan config:clear
php artisan config:cache
```

3. **Clear all cache:**

```bash
php artisan optimize:clear
php artisan optimize
```

4. **Restart web server:**

```bash
# Nginx
sudo systemctl restart nginx

# Apache
sudo systemctl restart apache2

# PHP-FPM
sudo systemctl restart php8.1-fpm
```

5. **Check .htaccess (jika Apache):**
   Pastikan file `.htaccess` ada di public folder:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

6. **Check Nginx config:**

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Kesimpulan

✅ **Sidebar link** sudah diperbaiki menggunakan route name yang benar
✅ **API calls** sudah menggunakan Laravel route helper
✅ **Production compatibility** sudah ditingkatkan dengan absolute URLs
✅ **Maintainability** lebih baik dengan route helper

Halaman Neraca sekarang siap digunakan di production!
