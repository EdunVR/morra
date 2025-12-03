# Modal Realisasi - Final Version

## Design Philosophy

**Simplified & Focused:**

-   Tidak perlu dropdown pilih komponen (sudah otomatis dari RAB yang dipilih)
-   History langsung tampil (tidak perlu toggle)
-   Input langsung per komponen yang ada

## Modal Structure

```
┌─────────────────────────────────────────────────────────────┐
│ Input Realisasi Pemakaian                                   │
│ RAB: Test RAB                                               │
├─────────────────────────────────────────────────────────────┤
│ Summary                                                      │
│ ┌──────────────┬──────────────┬──────────────┐            │
│ │ Budget       │ Total        │ Sisa Budget  │            │
│ │ Disetujui    │ Terpakai     │              │            │
│ │ Rp 100.000   │ Rp 50.000    │ Rp 50.000    │            │
│ └──────────────┴──────────────┴──────────────┘            │
│                                                              │
│ Progress: ████████░░░░░░░░░░ 50%                           │
├─────────────────────────────────────────────────────────────┤
│ History Realisasi                                           │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Komponen A • Pembelian awal          Rp 50.000      │   │
│ │ 24 Nov 2025 • Admin                                  │   │
│ └──────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────┤
│ Input Realisasi Per Komponen                                │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Komponen A                    Budget: Rp 100.000     │   │
│ │ Realisasi Saat Ini: Rp 50.000  Sisa: Rp 50.000     │   │
│ │ [Tambah: 30.000] [Keterangan: Pembelian material]   │   │
│ └──────────────────────────────────────────────────────┘   │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Komponen B                    Budget: Rp 80.000      │   │
│ │ Realisasi Saat Ini: Rp 0       Sisa: Rp 80.000      │   │
│ │ [Tambah: 20.000] [Keterangan: Ongkos kirim]         │   │
│ └──────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────┤
│                                    [Batal] [Simpan Realisasi]│
└─────────────────────────────────────────────────────────────┘
```

## Features

### 1. Auto Load Components ✅

-   Semua komponen dari RAB langsung ditampilkan
-   Tidak perlu pilih dari dropdown
-   Tidak perlu tambah/hapus row

### 2. History Always Visible ✅

-   History langsung tampil jika ada
-   Tidak perlu klik toggle
-   Scroll jika history banyak

### 3. Per Component Input ✅

-   Setiap komponen punya input sendiri
-   Tampilkan realisasi saat ini
-   Tampilkan sisa budget
-   Warning jika over budget (text merah)

### 4. Smart Validation ✅

-   Hanya simpan komponen yang diisi (tambahan_realisasi > 0)
-   Auto use nama_komponen jika keterangan kosong
-   Alert jika tidak ada yang diisi

## Data Flow

```
User klik "Realisasi" pada RAB
  ↓
Modal terbuka
  ↓
Load data:
  - RAB details (komponen)
  - History realisasi
  ↓
Tampilkan:
  - Summary budget
  - History (jika ada)
  - List komponen dengan input
  ↓
User input per komponen:
  - Komponen A: +30000, "Pembelian material"
  - Komponen B: +20000, "Ongkos kirim"
  ↓
Klik "Simpan Realisasi"
  ↓
POST /admin/finance/rab/{id}/realisasi
{
  "details": [
    {
      "id": 1,
      "tambahan_realisasi": 30000,
      "keterangan": "Pembelian material"
    },
    {
      "id": 2,
      "tambahan_realisasi": 20000,
      "keterangan": "Ongkos kirim"
    }
  ]
}
  ↓
Backend:
  - Update rab_detail.realisasi_pemakaian
  - Insert to rab_realisasi_history
  ↓
Response success
  ↓
Reload data & close modal
```

## Component Card Layout

```html
<div class="p-3 rounded-lg border">
    <!-- Header -->
    <div class="flex justify-between">
        <div class="font-medium">Komponen A</div>
        <div class="text-xs">Budget: Rp 100.000</div>
    </div>

    <!-- Current Status -->
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label>Realisasi Saat Ini</label>
            <div class="text-emerald-600">Rp 50.000</div>
        </div>
        <div>
            <label>Sisa Budget</label>
            <div>Rp 50.000</div>
        </div>
    </div>

    <!-- Input -->
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label>Tambah Realisasi</label>
            <input type="text" placeholder="0" />
        </div>
        <div>
            <label>Keterangan</label>
            <input type="text" placeholder="Keterangan..." />
        </div>
    </div>
</div>
```

## Functions

### `openRealisasi(r)`

```javascript
async openRealisasi(r){
  this.realisasiData = JSON.parse(JSON.stringify(this.normalize(r)));

  // Initialize tambahan_realisasi for each detail
  this.realisasiData.details.forEach(d => {
    d.tambahan_realisasi = 0;
    d.keterangan_realisasi = '';
  });

  // Load history
  await this.loadRealisasiHistory(r.id);

  this.showRealisasi = true;
}
```

### `saveRealisasi()`

```javascript
async saveRealisasi(){
  // Filter only details with tambahan_realisasi > 0
  const detailsWithRealisasi = this.realisasiData.details
    .filter(d => d.tambahan_realisasi > 0)
    .map(d => ({
      id: d.id,
      tambahan_realisasi: d.tambahan_realisasi,
      keterangan: d.keterangan_realisasi || d.nama_komponen
    }));

  if(detailsWithRealisasi.length === 0){
    alert('Tidak ada realisasi yang diinput');
    return;
  }

  // POST to /admin/finance/rab/{id}/realisasi
  const response = await fetch(url, {
    method: 'POST',
    body: JSON.stringify({ details: detailsWithRealisasi })
  });
}
```

## User Experience

### Advantages ✅

1. **Simpler**

    - Tidak perlu pilih komponen (sudah otomatis)
    - Tidak perlu tambah/hapus row
    - Langsung lihat semua komponen

2. **Clearer**

    - History langsung terlihat
    - Status per komponen jelas (realisasi saat ini, sisa budget)
    - Warning visual jika over budget

3. **Faster**

    - Tidak perlu klik-klik banyak
    - Input langsung per komponen
    - Auto format number

4. **Safer**
    - Tidak bisa salah pilih komponen
    - Validasi per komponen
    - Tracking lengkap di history

### Workflow

```
1. Klik "Realisasi" → Modal terbuka
2. Lihat history (jika ada)
3. Scroll ke komponen yang mau diisi
4. Input tambahan realisasi
5. Input keterangan (optional)
6. Ulangi untuk komponen lain
7. Klik "Simpan Realisasi"
8. Done!
```

## Example Usage

### Scenario: Input Realisasi untuk 2 Komponen

```
RAB: Pembelian Material Proyek A

Komponen A: Semen
- Budget: Rp 1.000.000
- Realisasi Saat Ini: Rp 500.000
- Sisa: Rp 500.000
- Input: Tambah Rp 300.000, Keterangan: "Pembelian 10 sak"

Komponen B: Pasir
- Budget: Rp 800.000
- Realisasi Saat Ini: Rp 0
- Sisa: Rp 800.000
- Input: Tambah Rp 200.000, Keterangan: "Pembelian 2 kubik"

Komponen C: Batu
- Budget: Rp 600.000
- Realisasi Saat Ini: Rp 0
- Sisa: Rp 600.000
- Input: (tidak diisi)

Klik "Simpan Realisasi"
→ Hanya Komponen A dan B yang tersimpan
```

## Validation

### Frontend

```javascript
// Only save details with tambahan_realisasi > 0
const detailsWithRealisasi = this.realisasiData.details.filter(
    (d) => d.tambahan_realisasi > 0
);

if (detailsWithRealisasi.length === 0) {
    alert("Tidak ada realisasi yang diinput");
    return;
}
```

### Backend

```php
$validator = Validator::make($request->all(), [
    'details' => 'required|array',
    'details.*.id' => 'required',
    'details.*.tambahan_realisasi' => 'required|numeric|min:0',
    'details.*.keterangan' => 'nullable|string'
]);
```

## Database

### rab_detail (Updated)

```
id | nama_komponen | realisasi_pemakaian
1  | Semen         | 800000  (500000 + 300000)
2  | Pasir         | 200000  (0 + 200000)
3  | Batu          | 0       (tidak diupdate)
```

### rab_realisasi_history (New Records)

```
id | id_rab_detail | jumlah | keterangan          | user_id
1  | 1             | 300000 | Pembelian 10 sak    | 1
2  | 2             | 200000 | Pembelian 2 kubik   | 1
```

## Summary

✅ Modal lebih sederhana (tidak perlu dropdown)
✅ History langsung tampil
✅ Input per komponen yang sudah ada
✅ Tampilkan status lengkap (realisasi saat ini, sisa budget)
✅ Warning visual jika over budget
✅ Auto format number
✅ Smart validation
✅ User friendly & efficient
