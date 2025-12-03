# Fix Stock Issue - POS

## Error yang Terjadi

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'stok' in 'where clause'
SQL: select * from `produk` where `id_outlet` = 1 and `stok` > 0
```

## Penyebab

POS Controller mencoba query kolom `stok` langsung dari tabel `produk`, padahal:

-   Kolom `stok` **tidak ada** di tabel `produk`
-   Stok produk disimpan di tabel `hpp_produk`
-   Model `Produk` memiliki accessor `getStokAttribute()` yang menghitung total stok dari relasi `hppProduk`

## Solusi

### 1. Perbaikan `getProducts()` Method

**Sebelum:**

```php
$products = Produk::with(['satuan', 'kategori'])
    ->where('id_outlet', $outletId)
    ->where('stok', '>', 0) // ❌ Error: kolom stok tidak ada
    ->get()
```

**Sesudah:**

```php
$products = Produk::with(['satuan', 'kategori', 'hppProduk'])
    ->where('id_outlet', $outletId)
    ->get()
    ->filter(function($produk) {
        return $produk->stok > 0; // ✅ Menggunakan accessor
    })
```

### 2. Perbaikan Pengurangan Stok

**Sebelum:**

```php
$produk->decrement('stok', $item['kuantitas']); // ❌ Salah
```

**Sesudah:**

```php
$produk->reduceStock($item['kuantitas']); // ✅ Menggunakan FIFO
```

## Cara Kerja Stok di Sistem

### Struktur Database

```
produk
├── id_produk
├── nama_produk
├── harga_jual
└── (tidak ada kolom stok)

hpp_produk
├── id
├── id_produk (FK)
├── hpp (harga pokok)
├── stok (stok per batch)
└── created_at (untuk FIFO)
```

### Accessor di Model Produk

```php
public function getStokAttribute()
{
    return $this->hppProduk()->sum('stok');
}
```

### Method Pengurangan Stok (FIFO)

```php
public function reduceStock($jumlah)
{
    // Ambil hpp_produk dengan stok > 0, urutkan FIFO
    $hppProduks = $this->hppProduk()
        ->where('stok', '>', 0)
        ->orderBy('created_at', 'asc')
        ->get();

    // Kurangi stok dari batch paling lama dulu
    foreach ($hppProduks as $hppProduk) {
        // ... logic FIFO
    }
}
```

## File yang Diperbaiki

✅ `app/Http/Controllers/PosController.php`

-   Method `getProducts()` - Load relasi hppProduk dan filter dengan accessor
-   Method `store()` - Gunakan `reduceStock()` untuk pengurangan stok FIFO

## Testing

### 1. Test Get Products

```bash
# Akses API products
curl http://localhost/penjualan/pos/products?outlet_id=1
```

**Expected Response:**

```json
{
    "success": true,
    "data": [
        {
            "id_produk": 1,
            "sku": "BRK-001",
            "name": "Briket Kayu 25kg",
            "category": "Barang",
            "price": 80000,
            "stock": 150, // ✅ Total dari hpp_produk
            "satuan": "kg"
        }
    ]
}
```

### 2. Test Transaksi POS

1. Buka `/penjualan/pos`
2. Pilih produk
3. Lakukan transaksi
4. **Check:**
    - ✅ Stok berkurang di tabel `hpp_produk`
    - ✅ Menggunakan FIFO (batch lama dikurangi dulu)
    - ✅ Tidak ada error

### 3. Verify Stock Reduction

```sql
-- Sebelum transaksi
SELECT id_produk, hpp, stok, created_at
FROM hpp_produk
WHERE id_produk = 1
ORDER BY created_at ASC;

-- Lakukan transaksi qty 10

-- Sesudah transaksi
SELECT id_produk, hpp, stok, created_at
FROM hpp_produk
WHERE id_produk = 1
ORDER BY created_at ASC;

-- Batch paling lama harus berkurang dulu (FIFO)
```

## Catatan Penting

### 1. Accessor vs Direct Column

-   ❌ **Jangan**: `->where('stok', '>', 0)` (query database)
-   ✅ **Gunakan**: `->filter(fn($p) => $p->stok > 0)` (setelah load)

### 2. Pengurangan Stok

-   ❌ **Jangan**: `$produk->decrement('stok', $qty)`
-   ✅ **Gunakan**: `$produk->reduceStock($qty)` (FIFO)

### 3. Penambahan Stok

-   ❌ **Jangan**: `$produk->increment('stok', $qty)`
-   ✅ **Gunakan**: `$produk->addStock($hpp, $qty)`

### 4. Cek Stok

-   ✅ **Gunakan**: `$produk->stok` (accessor)
-   ✅ **Atau**: `$produk->getHppProdukSumStokAttribute()`

## Keuntungan Sistem Ini

1. **FIFO Automatic**: Stok lama keluar duluan
2. **HPP Accurate**: Setiap batch punya HPP sendiri
3. **Traceability**: Bisa track dari batch mana stok keluar
4. **Flexible**: Bisa handle multiple batch dengan HPP berbeda

## Status

✅ **FIXED** - Stock system sudah diperbaiki dan mengikuti pola yang benar

---

**Updated**: 30 November 2025  
**Status**: Production Ready
