# Realisasi Modal Improvement

## Changes Made

### 1. Tambah Row Komponen Dinamis ✅

Modal realisasi sekarang bisa tambah row baru untuk input realisasi, tidak terbatas pada komponen yang sudah ada.

**Features:**

-   Dropdown pilih komponen (dari list komponen RAB)
-   Input jumlah realisasi (auto format ribuan)
-   Input keterangan
-   Tombol hapus row

### 2. Tampilkan History Realisasi ✅

Modal menampilkan history realisasi yang sudah pernah diinput sebelumnya.

**Features:**

-   List history dengan keterangan, jumlah, tanggal, dan user
-   Toggle show/hide history
-   Scroll jika history banyak

### 3. Struktur Data Baru

#### Frontend State:

```javascript
realisasiData: {
  id: 1,
  name: "RAB Test",
  details: [
    {
      id: 1,
      nama_komponen: "Komponen A",
      budget: 100000,
      realisasi_pemakaian: 50000  // Total realisasi saat ini
    }
  ],
  realisasi_baru: [
    {
      id_detail: 1,
      jumlah: 30000,
      keterangan: "Pembelian material"
    }
  ],
  history: [
    {
      id: 1,
      id_rab_detail: 1,
      jumlah: 50000,
      keterangan: "Pembelian awal",
      user_name: "Admin",
      nama_komponen: "Komponen A",
      created_at: "2025-11-24 10:00:00"
    }
  ],
  showHistory: false
}
```

## Modal Layout

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
│ History Realisasi                    [Tampilkan/Sembunyikan]│
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Pembelian awal                        Rp 50.000      │   │
│ │ 24 Nov 2025 • Admin                                  │   │
│ └──────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────┤
│ Input Realisasi Baru                      [+ Tambah Baris]  │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ [Pilih Komponen ▼] [30.000] [Keterangan...] [🗑️]    │   │
│ │ [Pilih Komponen ▼] [20.000] [Keterangan...] [🗑️]    │   │
│ └──────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────┤
│ Total: Rp 50.000                                            │
│                                    [Batal] [Simpan Realisasi]│
└─────────────────────────────────────────────────────────────┘
```

## API Endpoints

### 1. Get History

```
GET /admin/finance/rab/{id}/history

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "id_rab_detail": 1,
      "jumlah": 50000,
      "keterangan": "Pembelian awal",
      "user_id": 1,
      "user_name": "Admin",
      "nama_komponen": "Komponen A",
      "created_at": "2025-11-24 10:00:00",
      "updated_at": "2025-11-24 10:00:00"
    }
  ]
}
```

### 2. Save Realisasi

```
POST /admin/finance/rab/{id}/realisasi

Request:
{
  "details": [
    {
      "id": 1,
      "tambahan_realisasi": 30000,
      "keterangan": "Pembelian material"
    }
  ]
}

Response:
{
  "success": true,
  "message": "Realisasi berhasil disimpan"
}
```

## Functions

### Frontend (Alpine.js)

#### `openRealisasi(r)`

```javascript
async openRealisasi(r){
  this.realisasiData = JSON.parse(JSON.stringify(this.normalize(r)));
  this.realisasiData.realisasi_baru = [];
  this.realisasiData.showHistory = false;

  // Load history
  await this.loadRealisasiHistory(r.id);

  this.showRealisasi = true;
}
```

#### `loadRealisasiHistory(rabId)`

```javascript
async loadRealisasiHistory(rabId){
  const response = await fetch(`/admin/finance/rab/${rabId}/history`);
  const result = await response.json();

  if(result.success){
    this.realisasiData.history = result.data || [];
  }
}
```

#### `addRealisasiBaru()`

```javascript
addRealisasiBaru(){
  this.realisasiData.realisasi_baru.push({
    id_detail: '',
    jumlah: 0,
    keterangan: ''
  });
}
```

#### `saveRealisasi()`

```javascript
async saveRealisasi(){
  const detailsWithRealisasi = this.realisasiData.realisasi_baru
    .filter(r => r.id_detail && r.jumlah > 0)
    .map(r => ({
      id: r.id_detail,
      tambahan_realisasi: r.jumlah,
      keterangan: r.keterangan || this.getDetailName(r.id_detail)
    }));

  const response = await fetch(`/admin/finance/rab/${this.realisasiData.id}/realisasi`, {
    method: 'POST',
    body: JSON.stringify({ details: detailsWithRealisasi })
  });
}
```

### Backend (Controller)

#### `getRealisasiHistory($id)`

```php
public function getRealisasiHistory($id): JsonResponse
{
    $rab = RabTemplate::find($id);

    $detailIds = RabDetail::where('id_rab', $rab->id_rab)->pluck('id');

    $history = DB::table('rab_realisasi_history')
        ->whereIn('id_rab_detail', $detailIds)
        ->leftJoin('users', 'rab_realisasi_history.user_id', '=', 'users.id')
        ->leftJoin('rab_detail', 'rab_realisasi_history.id_rab_detail', '=', 'rab_detail.id')
        ->select(
            'rab_realisasi_history.*',
            'users.name as user_name',
            'rab_detail.nama_komponen'
        )
        ->orderBy('rab_realisasi_history.created_at', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $history
    ]);
}
```

## User Flow

### 1. Buka Modal Realisasi

```
1. Klik tombol "Realisasi" pada RAB yang sudah APPROVED
2. Modal terbuka
3. System load history realisasi (jika ada)
4. Tampilkan summary budget dan progress
```

### 2. Lihat History (Optional)

```
1. Klik "Tampilkan" pada section History Realisasi
2. List history muncul dengan detail:
   - Keterangan
   - Jumlah
   - Tanggal & User
```

### 3. Input Realisasi Baru

```
1. Klik "Tambah Baris"
2. Pilih komponen dari dropdown
3. Input jumlah (auto format: 50000 → 50.000)
4. Input keterangan (optional)
5. Ulangi untuk komponen lain
```

### 4. Simpan

```
1. Klik "Simpan Realisasi"
2. System validasi:
   - Minimal 1 row dengan komponen & jumlah
3. Data tersimpan ke:
   - rab_detail.realisasi_pemakaian (updated)
   - rab_realisasi_history (new records)
4. Modal tutup
5. Data refresh
```

## Benefits

### 1. Flexibility ✅

-   Bisa input realisasi untuk komponen yang sama berkali-kali
-   Tidak terbatas pada komponen yang sudah ada
-   Bisa input multiple realisasi sekaligus

### 2. Transparency ✅

-   History realisasi terlihat jelas
-   Tahu siapa yang input dan kapan
-   Tracking lengkap per komponen

### 3. User Friendly ✅

-   Dropdown komponen (tidak perlu ketik manual)
-   Auto format number (tidak pusing dengan ribuan/jutaan)
-   Summary budget real-time
-   Progress bar visual

## Testing

### 1. Test Tambah Realisasi

```
1. Buka modal realisasi
2. Klik "Tambah Baris"
3. Pilih "Komponen A"
4. Input jumlah: 50000
5. Input keterangan: "Pembelian material"
6. Klik "Tambah Baris" lagi
7. Pilih "Komponen B"
8. Input jumlah: 30000
9. Simpan
```

### 2. Test History

```
1. Buka modal realisasi (RAB yang sudah punya realisasi)
2. Klik "Tampilkan" pada History
3. Verifikasi history muncul dengan benar
4. Klik "Sembunyikan"
5. History tersembunyi
```

### 3. Test Validation

```
1. Tambah baris tapi tidak pilih komponen
2. Simpan
3. Alert: "Tidak ada realisasi yang diinput"
```

### 4. Test Database

```sql
-- Cek rab_realisasi_history
SELECT
    h.*,
    d.nama_komponen,
    u.name as user_name
FROM rab_realisasi_history h
LEFT JOIN rab_detail d ON h.id_rab_detail = d.id
LEFT JOIN users u ON h.user_id = u.id
ORDER BY h.created_at DESC;
```

## Summary

✅ Modal realisasi bisa tambah row dinamis
✅ Dropdown pilih komponen
✅ History realisasi ditampilkan
✅ Toggle show/hide history
✅ Auto format number
✅ Data tersimpan ke rab_realisasi_history
✅ User friendly & flexible
