# Fix: Realisasi Tidak Tersimpan ke rab_realisasi_history

## Problem

Data realisasi tidak tersimpan ke tabel `rab_realisasi_history` meskipun log menunjukkan "Updating details from details array".

## Root Cause Analysis

### 1. Wrong Endpoint

Frontend mengirim data ke `updateRab()` (PUT method), bukan ke `saveRealisasi()` (POST method).

```javascript
// Frontend
const url = `{{ url('admin/finance/rab') }}/${this.realisasiData.id}`; // PUT
// Seharusnya:
const url = `{{ url('admin/finance/rab') }}/${this.realisasiData.id}/realisasi`; // POST
```

### 2. Wrong Data Structure

Frontend mengirim `spends` array dengan `desc` dan `amount`, tapi backend mencoba match dengan `nama_komponen`.

```javascript
// Frontend mengirim:
spends: [
  {desc: "Keterangan bebas", amount: 50000}
]

// Backend mencoba match:
$detail = RabDetail::where('nama_komponen', $spend['desc'])->first();
// Tidak akan match karena desc != nama_komponen
```

### 3. No History Saving

Method `updateRab()` hanya update `realisasi_pemakaian` di `rab_detail`, tidak menyimpan ke `rab_realisasi_history`.

## Solution

### 1. Change Modal Structure

Ubah dari input bebas (spends) menjadi input per komponen (details):

**Before:**

```html
<!-- Input bebas -->
<input x-model="realisasiData.spends[idx].desc" placeholder="Keterangan" />
<input x-model="realisasiData.spends[idx].amount" placeholder="Jumlah" />
```

**After:**

```html
<!-- Input per komponen -->
<template x-for="detail in realisasiData.details">
    <div>
        <div>Komponen: {{ detail.nama_komponen }}</div>
        <input
            x-model="detail.tambahan_realisasi"
            placeholder="Tambah Realisasi"
        />
        <input x-model="detail.keterangan_realisasi" placeholder="Keterangan" />
    </div>
</template>
```

### 2. Change Frontend Logic

```javascript
// Before
async saveRealisasi(){
  const url = `{{ url('admin/finance/rab') }}/${this.realisasiData.id}`;  // PUT
  body: JSON.stringify(this.realisasiData)  // Kirim semua data
}

// After
async saveRealisasi(){
  const url = `{{ url('admin/finance/rab') }}/${this.realisasiData.id}/realisasi`;  // POST

  const detailsWithRealisasi = this.realisasiData.details
    .filter(d => d.tambahan_realisasi > 0)
    .map(d => ({
      id: d.id,
      tambahan_realisasi: d.tambahan_realisasi,
      keterangan: d.keterangan_realisasi || d.nama_komponen,
      realisasi_pemakaian: (d.realisasi_pemakaian || 0) + d.tambahan_realisasi
    }));

  body: JSON.stringify({ details: detailsWithRealisasi })
}
```

### 3. Update Backend Method

```php
public function saveRealisasi(Request $request, $id): JsonResponse
{
    // Validate
    $validator = Validator::make($request->all(), [
        'details' => 'required|array',
        'details.*.id' => 'required',
        'details.*.tambahan_realisasi' => 'required|numeric|min:0',
        'details.*.keterangan' => 'nullable|string'
    ]);

    foreach ($request->details as $detailData) {
        $detail = RabDetail::where('id_rab', $rab->id_rab)
            ->where('id', $detailData['id'])
            ->first();

        if ($detail) {
            $oldRealisasi = $detail->realisasi_pemakaian ?? 0;
            $tambahan = $detailData['tambahan_realisasi'];
            $newRealisasi = $oldRealisasi + $tambahan;

            // Update rab_detail
            $detail->update([
                'realisasi_pemakaian' => $newRealisasi
            ]);

            // Save to rab_realisasi_history
            if ($tambahan > 0) {
                DB::table('rab_realisasi_history')->insert([
                    'id_rab_detail' => $detail->id,
                    'jumlah' => $tambahan,
                    'keterangan' => $detailData['keterangan'] ?? $detail->nama_komponen,
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
```

## Database Schema

### rab_realisasi_history

```sql
CREATE TABLE rab_realisasi_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_rab_detail BIGINT NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    keterangan TEXT NULL,
    user_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_rab_detail) REFERENCES rab_detail(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Data Flow

```
Frontend Modal Realisasi
  ↓
  User input per komponen:
  - Komponen A: Tambah 50000, Keterangan: "Pembelian material"
  - Komponen B: Tambah 30000, Keterangan: "Ongkos kirim"
  ↓
  POST /admin/finance/rab/{id}/realisasi
  {
    "details": [
      {
        "id": 1,
        "tambahan_realisasi": 50000,
        "keterangan": "Pembelian material",
        "realisasi_pemakaian": 50000
      },
      {
        "id": 2,
        "tambahan_realisasi": 30000,
        "keterangan": "Ongkos kirim",
        "realisasi_pemakaian": 30000
      }
    ]
  }
  ↓
Controller saveRealisasi()
  ↓
  For each detail:
    1. Update rab_detail.realisasi_pemakaian
    2. Insert to rab_realisasi_history
  ↓
Database:
  - rab_detail: realisasi_pemakaian updated
  - rab_realisasi_history: new records inserted
```

## Changes Made

### Files Modified:

1. `resources/views/admin/finance/rab/index.blade.php`

    - Modal realisasi: Input per komponen (bukan input bebas)
    - Function `openRealisasi()`: Initialize tambahan_realisasi
    - Function `saveRealisasi()`: Kirim ke endpoint `/realisasi` dengan format baru

2. `app/Http/Controllers/FinanceAccountantController.php`
    - Method `saveRealisasi()`: Update validation dan logic
    - Add logging untuk debug

### Route:

```php
Route::post('rab/{id}/realisasi', [FinanceAccountantController::class, 'saveRealisasi'])
    ->name('admin.finance.rab.realisasi');
```

## Testing

### 1. Buka Modal Realisasi

```
1. Buka RAB yang sudah APPROVED
2. Klik tombol "Realisasi"
3. Modal terbuka dengan list komponen
```

### 2. Input Realisasi

```
Komponen A:
- Realisasi Saat Ini: Rp 0
- Tambah Realisasi: 50000
- Keterangan: "Pembelian material"

Komponen B:
- Realisasi Saat Ini: Rp 0
- Tambah Realisasi: 30000
- Keterangan: "Ongkos kirim"
```

### 3. Simpan

```
Klik "Simpan Realisasi"
```

### 4. Cek Log

```
=== SAVE REALISASI REQUEST ===
Processing detail: {...}
History saved: {"id_rab_detail":1,"jumlah":50000}
History saved: {"id_rab_detail":2,"jumlah":30000}
Realisasi saved successfully: {"total_records":2}
```

### 5. Cek Database

```sql
-- Cek rab_detail
SELECT id, nama_komponen, realisasi_pemakaian
FROM rab_detail
WHERE id_rab = [id_rab];

-- Cek rab_realisasi_history
SELECT *
FROM rab_realisasi_history
WHERE id_rab_detail IN (SELECT id FROM rab_detail WHERE id_rab = [id_rab])
ORDER BY created_at DESC;
```

## Expected Result

### Console Log:

```
=== SAVING REALISASI ===
Details with realisasi: [...]
Response status: 200
Response data: {success: true, message: "Realisasi berhasil disimpan"}
```

### Laravel Log:

```
=== SAVE REALISASI REQUEST ===
Processing detail: {detail_id: 1, tambahan: 50000, new_realisasi: 50000}
History saved: {id_rab_detail: 1, jumlah: 50000}
Realisasi saved successfully: {total_records: 2}
```

### Database:

```
rab_detail:
id | nama_komponen | realisasi_pemakaian
1  | Komponen A    | 50000
2  | Komponen B    | 30000

rab_realisasi_history:
id | id_rab_detail | jumlah | keterangan          | user_id
1  | 1             | 50000  | Pembelian material  | 1
2  | 2             | 30000  | Ongkos kirim        | 1
```

## Summary

✅ Modal realisasi diubah: Input per komponen (bukan input bebas)
✅ Frontend mengirim ke endpoint `/realisasi` dengan format baru
✅ Backend menyimpan ke `rab_detail` DAN `rab_realisasi_history`
✅ Logging ditambahkan untuk debug
✅ Data realisasi tersimpan dengan benar
