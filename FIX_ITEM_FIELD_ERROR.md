# Fix: Field 'item' doesn't have a default value

## Error

```
SQLSTATE[HY000]: General error: 1364 Field 'item' doesn't have a default value
```

## Root Cause

Saat update RAB, data dikirim dalam format `details` array (bukan `components`), tapi field `item` tidak diisi di insert statement.

### SQL yang Error:

```sql
INSERT INTO `rab_detail` (
    `id_rab`, `nama_komponen`, `jumlah`, `satuan`,
    `harga_satuan`, `budget`, `nilai_disetujui`,
    `realisasi_pemakaian`, `disetujui`, `deskripsi`,
    `updated_at`, `created_at`
) VALUES (...)
```

**Missing:** Field `item` tidak ada!

## Database Schema

Table `rab_detail` memiliki field:

-   `item` (required, no default value)
-   `nama_komponen`
-   `qty`
-   `jumlah`
-   `satuan`
-   `harga`
-   `harga_satuan`
-   `subtotal`
-   `budget`
-   `biaya`
-   dll.

## Solution

Tambahkan semua required fields saat create RabDetail dari details array:

### Before:

```php
foreach ($request->details as $detail) {
    \App\Models\RabDetail::create([
        'id_rab' => $rab->id_rab,
        'nama_komponen' => $detail['nama_komponen'] ?? '',
        'jumlah' => $detail['jumlah'] ?? 1,
        'satuan' => $detail['satuan'] ?? 'pcs',
        'harga_satuan' => $detail['harga_satuan'] ?? 0,
        'budget' => $detail['budget'] ?? 0,
        // Missing: item, qty, harga, subtotal, biaya
    ]);
}
```

### After:

```php
foreach ($request->details as $detail) {
    $namaKomponen = $detail['nama_komponen'] ?? '';

    \App\Models\RabDetail::create([
        'id_rab' => $rab->id_rab,
        'item' => $namaKomponen,              // ✅ Added
        'nama_komponen' => $namaKomponen,
        'deskripsi' => $detail['deskripsi'] ?? '',
        'qty' => $detail['jumlah'] ?? 1,      // ✅ Added
        'jumlah' => $detail['jumlah'] ?? 1,
        'satuan' => $detail['satuan'] ?? 'pcs',
        'harga' => $detail['harga_satuan'] ?? 0,  // ✅ Added
        'harga_satuan' => $detail['harga_satuan'] ?? 0,
        'subtotal' => $detail['budget'] ?? 0,     // ✅ Added
        'budget' => $detail['budget'] ?? 0,
        'biaya' => $detail['budget'] ?? 0,        // ✅ Added
        'nilai_disetujui' => $detail['nilai_disetujui'] ?? 0,
        'realisasi_pemakaian' => $detail['realisasi_pemakaian'] ?? 0,
        'disetujui' => $detail['disetujui'] ?? false
    ]);
}
```

## Fields Mapping

| Database Field  | Source          |
| --------------- | --------------- |
| `item`          | `nama_komponen` |
| `nama_komponen` | `nama_komponen` |
| `qty`           | `jumlah`        |
| `jumlah`        | `jumlah`        |
| `harga`         | `harga_satuan`  |
| `harga_satuan`  | `harga_satuan`  |
| `subtotal`      | `budget`        |
| `budget`        | `budget`        |
| `biaya`         | `budget`        |

## Why This Happened

Ada 2 path untuk create RabDetail:

1. **From components** (buat baru):

    ```php
    // Sudah lengkap dengan semua field
    'item' => $uraian,
    'qty' => $qty,
    'harga' => $harga_satuan,
    // dll
    ```

2. **From details** (update/approval):
    ```php
    // Missing beberapa field!
    // Hanya ada: nama_komponen, jumlah, satuan, dll
    ```

Path kedua tidak lengkap karena asumsi data sudah ada di database, tapi saat update kita delete semua dan create ulang, jadi perlu semua field.

## Changes Made

### Files Modified:

-   `app/Http/Controllers/FinanceAccountantController.php`

### Methods Updated:

1. `storeRab()` - Details array path
2. `updateRab()` - Details array path

### Occurrences Fixed:

-   2 occurrences (storeRab + updateRab)

## Testing

1. **Buat RAB baru** - Harus berhasil ✅
2. **Edit RAB** - Harus berhasil ✅
3. **Update komponen** - Harus berhasil ✅
4. **Cek database** - Semua field terisi ✅

### Test Steps:

```
1. Buat RAB baru dengan komponen
2. Simpan (berhasil)
3. Edit RAB
4. Ubah qty/harga komponen
5. Simpan (sekarang berhasil, sebelumnya error)
6. Cek database - semua field terisi
```

## Prevention

Untuk mencegah error serupa:

1. Selalu isi semua required fields (yang tidak punya default value)
2. Gunakan same structure untuk create, baik dari components maupun details
3. Buat helper method untuk create RabDetail agar konsisten

## Summary

✅ Field `item` dan field lainnya sudah ditambahkan di details array path
✅ Sekarang update RAB tidak akan error lagi
✅ Semua field database terisi dengan benar
✅ Konsisten antara create dan update
