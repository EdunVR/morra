# ⚡ Quick Optimization Reference

Panduan cepat untuk menggunakan optimasi performa yang telah diimplementasikan.

---

## 🚀 Production Deployment (Quick)

```bash
# 1. Backup
mysqldump -u username -p database_name > backup.sql

# 2. Update code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# 4. Run optimization
optimize-production.bat

# 5. Done! ✅
```

---

## 🔄 Development Mode (Quick)

```bash
# Kembali ke development mode
optimize-development.bat

# Atau manual
php artisan optimize:clear
```

---

## 💾 Cache Usage (Quick)

### Basic Caching

```php
use App\Services\CacheService;

// Cache data (1 hour default)
$data = CacheService::remember('key', fn() => Model::all());

// Cache 5 minutes
$data = CacheService::remember('key', fn() => Model::all(), CacheService::SHORT_TTL);

// Clear cache
CacheService::forget('key');
```

### Outlet-Specific Cache

```php
// Cache per outlet
$key = CacheService::outletKey('products', $outletId);
$products = CacheService::remember($key, fn() => Product::where('outlet_id', $outletId)->get());

// Clear outlet cache
CacheService::clearOutletCache($outletId);
```

### Response Cache Middleware

```php
// routes/web.php
Route::get('/api/products', [ProductController::class, 'index'])
    ->middleware('cache.response:300'); // 5 minutes
```

---

## 🗄️ Query Optimization (Quick)

### Before (Slow)

```php
$products = Produk::with(['satuan', 'kategori'])->get();
```

### After (Fast)

```php
$products = Produk::select(['id_produk', 'nama_produk', 'harga_jual'])
    ->with([
        'satuan:id_satuan,nama_satuan',
        'kategori:id_kategori,nama_kategori'
    ])
    ->get();
```

---

## 📊 Database Indexing (Quick)

### Top Priority Indexes

```sql
-- Products
ALTER TABLE `produk` ADD INDEX `idx_outlet_active` (`id_outlet`, `is_active`);

-- POS Sales
ALTER TABLE `pos_sales` ADD INDEX `idx_outlet_tanggal` (`id_outlet`, `tanggal`);

-- Sales
ALTER TABLE `penjualan` ADD INDEX `idx_outlet_created` (`id_outlet`, `created_at`);
```

Lihat `DATABASE_INDEXING_RECOMMENDATIONS.md` untuk lengkapnya.

---

## 🐛 Troubleshooting (Quick)

### Cache tidak update

```bash
php artisan cache:clear
```

### env() tidak bekerja

```php
# ❌ SALAH
$key = env('API_KEY');

# ✅ BENAR
$key = config('services.api_key');
```

### Route tidak ditemukan

```bash
php artisan route:clear
```

### CSS classes hilang

```javascript
// tailwind.config.js
safelist: ["bg-red-500", "text-blue-600"];
```

---

## 📈 Performance Check (Quick)

### Check Cache Headers

```bash
curl -I http://your-domain.com/api/products
# Look for: X-Cache: HIT or MISS
```

### Check Asset Sizes

```bash
dir public\build\assets
# Expected: app.js ~50-100KB, vendor.js ~150-250KB
```

### Check Query Time

```php
DB::listen(function ($query) {
    Log::info($query->sql . ' - ' . $query->time . 'ms');
});
```

---

## 📚 Full Documentation

-   **OPTIMIZATION_COMPLETE_SUMMARY.md** - Overview lengkap
-   **PERFORMANCE_OPTIMIZATION_GUIDE.md** - Panduan detail
-   **DATABASE_INDEXING_RECOMMENDATIONS.md** - Database optimization

---

## ✅ Quick Checklist

### Development

-   [ ] Run `optimize-development.bat`
-   [ ] Test features
-   [ ] Check logs

### Production

-   [ ] Backup database & files
-   [ ] Run `optimize-production.bat`
-   [ ] Add database indexes
-   [ ] Test critical features
-   [ ] Monitor performance

---

**Need help?** Read `PERFORMANCE_OPTIMIZATION_GUIDE.md` for detailed information.
