# Fix Komponen RAB - Uraian & Biaya

## ✅ Perubahan yang Dilakukan

### 1. **Komponen Sekarang Punya 2 Kolom**

Sebelumnya komponen hanya string, sekarang object dengan:

-   `uraian`: Deskripsi komponen (string)
-   `biaya`: Biaya komponen (number)

**Format Baru**:

```javascript
{
  uraian: "Ongkos ojek (1 pcs)",
  biaya: 50000
}
```

### 2. **Budget Total Auto Calculate**

Budget total sekarang otomatis dihitung dari total biaya semua komponen:

```javascript
budget_total = sum(components.map((c) => c.biaya));
```

Field budget total menjadi **readonly** dan auto-update saat komponen berubah.

### 3. **Auto Format Number di Komponen**

Input biaya komponen menggunakan format number otomatis (ribuan, jutaan):

-   Input: `50000` → Display: `50.000`
-   Input: `1000000` → Display: `1.000.000`

### 4. **Fix Alpine Error - realisasiData null**

Wrap modal realisasi dengan `x-if="realisasiData"` untuk mencegah error saat data null:

```html
<template x-if="realisasiData">
    <!-- Modal content -->
</template>
```

### 5. **Backward Compatibility**

Normalize function mendukung format lama (string) dan format baru (object):

```javascript
// Old format: ["Ongkos ojek", "Bahan baku"]
// New format: [{uraian: "Ongkos ojek", biaya: 50000}, ...]
```

## Tampilan Komponen

### Form Input:

```
┌─────────────────────────────────────────────────────────────┐
│ Komponen Biaya *                    [+ Tambah Komponen]     │
├─────────────────────────────────────────────────────────────┤
│ Uraian                          │ Biaya (Rp)      │ [🗑️]    │
├─────────────────────────────────────────────────────────────┤
│ Ongkos ojek (1 pcs)            │ 50.000          │ [🗑️]    │
│ Bahan baku material            │ 150.000         │ [🗑️]    │
│ Upah tukang                    │ 200.000         │ [🗑️]    │
└─────────────────────────────────────────────────────────────┘

Budget Total (Auto): Rp 400.000
```

### Tampilan Tabel:

```
Deskripsi & Komponen:
- Ongkos ojek (1 pcs) - Rp 50.000
- Bahan baku material - Rp 150.000
- Upah tukang - Rp 200.000
```

## Functions Baru

### `addComponent()`

Menambah komponen baru dengan format object:

```javascript
this.form.components.push({ uraian: "", biaya: 0 });
```

### `updateComponentBiaya(idx, val)`

Update biaya komponen dan recalculate budget:

```javascript
this.form.components[idx].biaya = this.parseNumber(val);
this.recalculateBudget();
```

### `recalculateBudget()`

Hitung ulang budget total dari semua komponen:

```javascript
this.form.budget_total = this.form.components.reduce(
    (sum, c) => sum + Number(c.biaya || 0),
    0
);
```

## Testing

1. ✅ Tambah komponen baru
2. ✅ Isi uraian dan biaya
3. ✅ Budget total auto update
4. ✅ Format number otomatis
5. ✅ Hapus komponen
6. ✅ Simpan RAB
7. ✅ Komponen tersimpan dengan benar
8. ✅ Tampilan di tabel dan detail
9. ✅ Modal realisasi tidak error
10. ✅ Backward compatibility dengan data lama

## Catatan

-   Budget total tidak bisa diinput manual lagi (readonly)
-   Minimal 1 komponen harus diisi
-   Komponen kosong (uraian kosong) akan difilter saat simpan
-   Format lama (string) otomatis dikonversi ke format baru saat load data
