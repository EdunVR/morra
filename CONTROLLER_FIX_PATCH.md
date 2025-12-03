# Controller Fix - Create RAB Detail

## Problem

Komponen tidak tersimpan ke `rab_detail` karena format data tidak sesuai.

## Solution

Update bagian create RabDetail di method `storeRab()` dan `updateRab()` untuk support format baru dengan qty, satuan, harga_satuan.

## Code to Replace

### In `storeRab()` method (line ~7903-7930)

**FIND:**

```php
// Support new format: {uraian, biaya}
if (is_array($component) && isset($component['uraian'])) {
    $uraian = $component['uraian'];
    $biaya = $component['biaya'] ?? 0;

    if (!empty($uraian)) {
        \App\Models\RabDetail::create([
            'id_rab' => $rab->id_rab,
            'item' => $uraian,
            'nama_komponen' => $uraian,
            'deskripsi' => '',
            'qty' => 1,
            'jumlah' => 1,
            'satuan' => 'pcs',
            'harga' => $biaya,
            'harga_satuan' => $biaya,
            'subtotal' => $biaya,
            'budget' => $biaya,
            'biaya' => $biaya,
            'nilai_disetujui' => 0,
            'realisasi_pemakaian' => 0,
            'disetujui' => false
        ]);
    }
}
```

**REPLACE WITH:**

```php
// Support new format: {uraian, qty, satuan, harga_satuan, biaya}
if (is_array($component) && isset($component['uraian'])) {
    $uraian = $component['uraian'];
    $qty = $component['qty'] ?? 1;
    $satuan = $component['satuan'] ?? 'pcs';
    $harga_satuan = $component['harga_satuan'] ?? 0;
    $biaya = $component['biaya'] ?? ($qty * $harga_satuan);

    if (!empty($uraian)) {
        \App\Models\RabDetail::create([
            'id_rab' => $rab->id_rab,
            'item' => $uraian,
            'nama_komponen' => $uraian,
            'deskripsi' => $component['deskripsi'] ?? '',
            'qty' => $qty,
            'jumlah' => $qty,
            'satuan' => $satuan,
            'harga' => $harga_satuan,
            'harga_satuan' => $harga_satuan,
            'subtotal' => $biaya,
            'budget' => $biaya,
            'biaya' => $biaya,
            'nilai_disetujui' => 0,
            'realisasi_pemakaian' => 0,
            'disetujui' => false
        ]);
    }
}
```

### In `updateRab()` method (line ~8059-8086)

**Do the same replacement as above**

## Manual Steps

1. Open `app/Http/Controllers/FinanceAccountantController.php`
2. Find line ~7903 (in `storeRab` method)
3. Replace the code block as shown above
4. Find line ~8059 (in `updateRab` method)
5. Replace the code block as shown above
6. Save file

## Test

1. Create new RAB with components
2. Check database `rab_detail` table
3. Verify all fields are saved correctly:
    - item, nama_komponen
    - qty, jumlah
    - satuan
    - harga, harga_satuan
    - subtotal, budget, biaya
