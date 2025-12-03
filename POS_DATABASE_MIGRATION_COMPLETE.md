# POS Database Migration - Complete

## Problem

Table `setting_coa_pos` tidak ditemukan di database, menyebabkan error:

```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'demo.setting_coa_pos' doesn't exist
```

## Solution

Menjalankan migration yang sudah ada untuk membuat semua tabel POS.

## Migration Executed

```bash
php artisan migrate --path=database/migrations/2025_11_30_create_pos_sales_tables.php
```

## Tables Created

### 1. pos_sales

Tabel utama untuk transaksi POS.

**Columns:**

-   `id` - Primary key
-   `no_transaksi` - Nomor transaksi unik
-   `tanggal` - Tanggal transaksi
-   `id_outlet` - Foreign key ke outlets
-   `id_member` - Foreign key ke member (nullable)
-   `id_user` - Foreign key ke users (nullable)
-   `subtotal` - Subtotal sebelum diskon
-   `diskon_persen` - Diskon dalam persen
-   `diskon_nominal` - Diskon dalam rupiah
-   `total_diskon` - Total diskon
-   `ppn` - PPN 10%
-   `total` - Total akhir
-   `jenis_pembayaran` - Enum: cash, transfer, qris
-   `jumlah_bayar` - Jumlah uang yang dibayar
-   `kembalian` - Kembalian
-   `status` - Enum: lunas, menunggu
-   `catatan` - Catatan transaksi
-   `is_bon` - Boolean untuk transaksi bon/piutang
-   `id_penjualan` - Foreign key ke penjualan (nullable)
-   `timestamps`

**Indexes:**

-   `tanggal`
-   `id_outlet`
-   `status`

### 2. pos_sale_items

Tabel detail item dalam transaksi POS.

**Columns:**

-   `id` - Primary key
-   `pos_sale_id` - Foreign key ke pos_sales
-   `id_produk` - Foreign key ke produk (nullable)
-   `nama_produk` - Nama produk
-   `sku` - SKU produk
-   `kuantitas` - Jumlah item
-   `satuan` - Satuan produk
-   `harga` - Harga per unit
-   `subtotal` - Subtotal item
-   `tipe` - Enum: produk, jasa
-   `timestamps`

**Indexes:**

-   `pos_sale_id`

### 3. setting_coa_pos

Tabel setting Chart of Accounts untuk POS per outlet.

**Columns:**

-   `id` - Primary key
-   `id_outlet` - Foreign key ke outlets (unique)
-   `accounting_book_id` - Foreign key ke accounting_books
-   `akun_kas` - Kode akun kas
-   `akun_bank` - Kode akun bank
-   `akun_piutang_usaha` - Kode akun piutang
-   `akun_pendapatan_penjualan` - Kode akun pendapatan
-   `akun_hpp` - Kode akun HPP (nullable)
-   `akun_persediaan` - Kode akun persediaan (nullable)
-   `timestamps`

**Constraints:**

-   `id_outlet` is unique (one setting per outlet)

## Model: SettingCOAPos

### Location

`app/Models/SettingCOAPos.php`

### Key Methods

#### scopeByOutlet($query, $outletId)

Query scope untuk filter by outlet.

```php
SettingCOAPos::byOutlet(1)->first();
```

#### getByOutlet($outletId)

Static method untuk get setting by outlet.

```php
$setting = SettingCOAPos::getByOutlet(1);
```

#### updateOrCreateForOutlet($outletId, $data)

Static method untuk create atau update setting.

```php
SettingCOAPos::updateOrCreateForOutlet(1, [
    'accounting_book_id' => 1,
    'akun_kas' => '1-1000',
    'akun_bank' => '1-1100',
    // ...
]);
```

#### isCompleteForStatus($status)

Cek apakah setting sudah lengkap untuk status tertentu.

```php
if ($setting->isCompleteForStatus('lunas')) {
    // Setting lengkap untuk transaksi lunas
}
```

### Relationships

#### outlet()

```php
$setting->outlet; // Get outlet data
```

#### accountingBook()

```php
$setting->accountingBook; // Get accounting book data
```

## Controller Usage

### PosController@coaSettings

**GET Request (AJAX):**

```javascript
const response = await fetch("/penjualan/pos/coa-settings?outlet_id=1", {
    headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "id_outlet": 1,
        "accounting_book_id": 1,
        "akun_kas": "1-1000",
        "akun_bank": "1-1100",
        "akun_piutang_usaha": "1-1200",
        "akun_pendapatan_penjualan": "4-1000",
        "akun_hpp": "5-1000",
        "akun_persediaan": "1-1300"
    }
}
```

**POST Request:**

```javascript
const response = await fetch("/penjualan/pos/coa-settings?outlet_id=1", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
    },
    body: JSON.stringify({
        accounting_book_id: 1,
        akun_kas: "1-1000",
        akun_bank: "1-1100",
        akun_piutang_usaha: "1-1200",
        akun_pendapatan_penjualan: "4-1000",
        akun_hpp: "5-1000",
        akun_persediaan: "1-1300",
    }),
});
```

## Verification

### Check Tables Exist

```bash
php artisan db:table setting_coa_pos
php artisan db:table pos_sales
php artisan db:table pos_sale_items
```

### Check Model Works

```bash
php artisan tinker
```

```php
SettingCOAPos::count(); // Should return 0 or more
SettingCOAPos::getByOutlet(1); // Should return null or setting
```

## Next Steps

1. ✅ Migration completed
2. ✅ Tables created
3. ✅ Model configured
4. ✅ Controller ready
5. ⏳ Configure COA settings via POS interface
6. ⏳ Test transactions

## Status

✅ **COMPLETE** - All POS database tables created successfully
