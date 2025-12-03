# POS Piutang Relationship Fixed ✅

## Masalah

1. Controller menggunakan relasi `piutang` (singular) yang tidak ada di model Member
2. Field piutang yang digunakan tidak sesuai dengan struktur tabel

## Solusi

### 1. Perbaikan Relasi di Controller

**File:** `app/Http/Controllers/PosController.php`

**Sebelum:**

```php
->with(['piutang' => function($query) {
    $query->where('status', 'belum_lunas')
          ->orWhere('sisa_piutang', '>', 0);
}])
```

**Sesudah:**

```php
->with(['piutangBelumLunas'])
```

**Penjelasan:**

-   Model Member sudah punya relasi `piutangBelumLunas()` yang sudah filter status belum lunas
-   Tidak perlu query tambahan karena sudah di-handle di model

### 2. Perbaikan Field Piutang saat Create

**File:** `app/Http/Controllers/PosController.php`

**Sebelum:**

```php
Piutang::create([
    'id_penjualan' => $penjualan->id_penjualan,
    'id_member' => $request->id_member ?? null,
    'id_outlet' => $outletId,
    'tanggal' => $request->tanggal,
    'jatuh_tempo' => $dueDate,
    'total_piutang' => $total,
    'sisa_piutang' => $total,
    'status' => 'belum_lunas',
    'keterangan' => 'Piutang dari POS - ' . $noTransaksi,
]);
```

**Sesudah:**

```php
Piutang::create([
    'id_penjualan' => $penjualan->id_penjualan,
    'id_member' => $request->id_member ?? null,
    'id_outlet' => $outletId,
    'tanggal_tempo' => $request->tanggal,
    'tanggal_jatuh_tempo' => $dueDate,
    'piutang' => $total,
    'jumlah_piutang' => $total,
    'jumlah_dibayar' => 0,
    'sisa_piutang' => $total,
    'status' => 'belum_lunas',
    'nama' => $request->id_member ? Member::find($request->id_member)->nama : 'Pelanggan Umum',
]);
```

**Penjelasan:**

-   Field `tanggal` → `tanggal_tempo`
-   Field `jatuh_tempo` → `tanggal_jatuh_tempo`
-   Tambah field `piutang` (total awal)
-   Tambah field `jumlah_piutang` (total piutang)
-   Tambah field `jumlah_dibayar` (0 untuk bon baru)
-   Tambah field `nama` (nama customer)
-   Hapus field `keterangan` (tidak ada di fillable)

## Struktur Tabel Piutang

Berdasarkan model `Piutang.php`:

```php
protected $fillable = [
    'id_penjualan',
    'tanggal_tempo',
    'tanggal_jatuh_tempo',
    'nama',
    'piutang',
    'jumlah_piutang',
    'jumlah_dibayar',
    'sisa_piutang',
    'id_member',
    'id_outlet',
    'status',
];
```

## Relasi di Model Member

Model `Member.php` sudah punya relasi:

```php
// Semua piutang
public function piutangs()
{
    return $this->hasMany(Piutang::class, 'id_member');
}

// Piutang belum lunas
public function piutangBelumLunas()
{
    return $this->hasMany(Piutang::class, 'id_member')
                ->where('status', 'belum_lunas');
}
```

## Testing

### 1. Test Load Customer dengan Piutang

```
GET /penjualan/pos/customers
```

Response:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Customer A",
            "telepon": "08123456789",
            "piutang": 100000
        }
    ]
}
```

### 2. Test Transaksi Bon

1. Buka halaman POS
2. Tambah produk ke keranjang
3. Pilih customer
4. Centang "Bon (Piutang)"
5. Klik "Bayar & Cetak"
6. Cek tabel `piutang` - harus ada record baru

### 3. Cek Data Piutang

```sql
SELECT * FROM piutang WHERE status = 'belum_lunas' ORDER BY id_piutang DESC LIMIT 5;
```

## Status

✅ Relasi piutang diperbaiki
✅ Field piutang disesuaikan dengan struktur tabel
✅ Cache di-clear
✅ Siap untuk testing

## Catatan Penting

1. **Field piutang vs jumlah_piutang:**

    - `piutang` = Total awal piutang
    - `jumlah_piutang` = Total piutang (bisa sama dengan piutang)
    - `jumlah_dibayar` = Total yang sudah dibayar
    - `sisa_piutang` = Sisa yang belum dibayar

2. **Tanggal:**

    - `tanggal_tempo` = Tanggal transaksi
    - `tanggal_jatuh_tempo` = Tanggal jatuh tempo (default +30 hari)

3. **Status:**
    - `belum_lunas` = Masih ada sisa piutang
    - `lunas` = Sudah dibayar penuh

---

**Status: FIXED** ✅
