# RAB Final Fix - Data Tersimpan ke Database

## ✅ Perubahan yang Dilakukan

### 1. **Model RabDetail** ✅

-   Fix primary key: `id_rab_detail` → `id`
-   Tambah casts untuk decimal fields
-   File: `app/Models/RabDetail.php`

### 2. **Controller - FinanceAccountantController.php** ✅

#### `storeRab()` & `updateRab()`

Support format baru dengan qty, satuan, harga_satuan:

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

#### `rabData()`

Return format lengkap dengan qty, satuan, harga_satuan:

```php
'components' => $rab->details->map(function($detail) {
    return [
        'uraian' => $detail->nama_komponen ?? $detail->item,
        'qty' => (float) ($detail->qty ?? $detail->jumlah ?? 1),
        'satuan' => $detail->satuan ?? 'pcs',
        'harga_satuan' => (float) ($detail->harga_satuan ?? $detail->harga ?? 0),
        'biaya' => (float) ($detail->biaya ?? $detail->budget ?? 0)
    ];
})->toArray(),
```

### 3. **Frontend - index.blade.php** ✅

#### Form Komponen

Sekarang ada 4 field per komponen:

-   **Uraian**: Deskripsi komponen
-   **Qty**: Quantity (number)
-   **Satuan**: Satuan (text)
-   **Harga**: Harga per satuan (auto format)
-   **Budget**: Auto calculate (qty × harga_satuan)

#### JavaScript Functions

```javascript
addComponent(){
  this.form.components.push({
    uraian: '',
    qty: 1,
    satuan: 'pcs',
    harga_satuan: 0,
    biaya: 0
  });
}

updateComponentHarga(idx, val){
  this.form.components[idx].harga_satuan = this.parseNumber(val);
  this.recalculateComponentBudget(idx);
}

recalculateComponentBudget(idx){
  const c = this.form.components[idx];
  c.biaya = (c.qty || 0) * (c.harga_satuan || 0);
  this.recalculateBudget();
}

recalculateBudget(){
  this.form.budget_total = this.form.components.reduce(
    (sum, c) => sum + Number(c.biaya || 0), 0
  );
}
```

## Data Flow Lengkap

```
Frontend Form
  ↓
  components: [
    {
      uraian: "Ongkos ojek",
      qty: 2,
      satuan: "pcs",
      harga_satuan: 50000,
      biaya: 100000  // auto: qty × harga_satuan
    }
  ]
  ↓
Controller storeRab()
  ↓
Database rab_detail:
  - id_rab: 1
  - item: "Ongkos ojek"
  - nama_komponen: "Ongkos ojek"
  - qty: 2
  - jumlah: 2
  - satuan: "pcs"
  - harga: 50000
  - harga_satuan: 50000
  - subtotal: 100000
  - budget: 100000
  - biaya: 100000
  - nilai_disetujui: 0
  - realisasi_pemakaian: 0
  - disetujui: 0
```

## Testing Steps

1. **Buat RAB Baru**

    ```
    - Klik "Tambah RAB"
    - Isi outlet, buku, nama, deskripsi
    - Klik "Tambah Komponen"
    - Isi:
      * Uraian: "Ongkos ojek"
      * Qty: 2
      * Satuan: "pcs"
      * Harga: 50000
    - Budget otomatis: 100000
    - Simpan
    ```

2. **Cek Database**

    ```sql
    SELECT * FROM rab_template ORDER BY id_rab DESC LIMIT 1;
    SELECT * FROM rab_detail WHERE id_rab = [id_rab_terakhir];
    ```

3. **Verifikasi Data**

    - ✅ rab_template tersimpan
    - ✅ rab_detail tersimpan dengan semua field
    - ✅ qty, satuan, harga_satuan terisi
    - ✅ budget = qty × harga_satuan

4. **Load Data**

    - Refresh halaman
    - Data muncul dengan benar
    - Komponen menampilkan qty, satuan, harga

5. **Edit RAB**

    - Klik "Edit"
    - Komponen muncul dengan data lengkap
    - Edit qty/harga
    - Budget auto update
    - Simpan

6. **Admin Approve**
    - Klik "Approve"
    - Modal approval muncul
    - Edit nilai_disetujui
    - Simpan

## Troubleshooting

### Data tidak tersimpan?

1. Cek log: `storage/logs/laravel.log`
2. Cek network tab di browser (F12)
3. Pastikan CSRF token valid
4. Cek database connection

### Komponen kosong saat load?

1. Cek response API `/admin/finance/rab/data`
2. Pastikan `rabData()` return format benar
3. Cek console browser untuk error

### Budget tidak auto calculate?

1. Cek fungsi `recalculateComponentBudget()`
2. Pastikan qty dan harga_satuan adalah number
3. Cek `@input` event handler

## Files Changed

1. ✅ `app/Models/RabDetail.php` - Fix primary key & casts
2. ✅ `app/Http/Controllers/FinanceAccountantController.php` - Support format baru
3. ✅ `resources/views/admin/finance/rab/index.blade.php` - Form dengan qty, satuan, harga

## Summary

Sekarang sistem RAB sudah lengkap dengan:

-   Form input qty, satuan, harga per komponen
-   Auto calculate budget (qty × harga)
-   Data tersimpan lengkap ke rab_detail
-   Modal approval untuk admin
-   Modal realisasi terpisah
-   Backward compatibility dengan data lama

Semua data tersimpan dengan benar ke database! 🎉
