# Modal Realisasi - Simple Version (Final)

## Design

**Super Simple & Flexible:**

-   History langsung tampil (tidak perlu toggle)
-   Input realisasi dengan tambah row dinamis
-   Hanya 2 field: Keterangan + Jumlah
-   Tidak terikat pada komponen tertentu

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
│ │ Realisasi Umum • Pembelian awal      Rp 50.000      │   │
│ │ 24 Nov 2025 • Admin                                  │   │
│ └──────────────────────────────────────────────────────┘   │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Realisasi Umum • Ongkos kirim        Rp 30.000      │   │
│ │ 24 Nov 2025 • User                                   │   │
│ └──────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────┤
│ Input Realisasi Baru                      [+ Tambah Baris]  │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ [Keterangan: Pembelian material] [30.000] [🗑️]      │   │
│ │ [Keterangan: Ongkos ojek]        [20.000] [🗑️]      │   │
│ │ [Keterangan: Upah tukang]        [50.000] [🗑️]      │   │
│ └──────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────┤
│ Total: Rp 100.000                                           │
│                                    [Batal] [Simpan Realisasi]│
└─────────────────────────────────────────────────────────────┘
```

## Features

### 1. History Always Visible ✅

-   Langsung tampil tanpa perlu klik
-   Scroll jika banyak
-   Menampilkan: komponen, keterangan, jumlah, tanggal, user

### 2. Dynamic Rows ✅

-   Klik "Tambah Baris" untuk menambah row baru
-   Input keterangan (text)
-   Input jumlah (auto format ribuan)
-   Tombol hapus per row

### 3. Flexible ✅

-   Tidak terikat pada komponen tertentu
-   Bisa input realisasi apapun
-   Cocok untuk pengeluaran yang tidak masuk kategori komponen

### 4. Smart Validation ✅

-   Hanya simpan row yang terisi (keterangan + jumlah > 0)
-   Alert jika tidak ada yang diisi
-   Auto format number

## API Endpoint

### POST /admin/finance/rab/{id}/realisasi-simple

**Request:**

```json
{
    "realisasi": [
        {
            "keterangan": "Pembelian material",
            "jumlah": 30000
        },
        {
            "keterangan": "Ongkos ojek",
            "jumlah": 20000
        }
    ]
}
```

**Response:**

```json
{
    "success": true,
    "message": "Realisasi berhasil disimpan (2 item, total: Rp 50.000)"
}
```

## Backend Logic

```php
public function saveRealisasiSimple(Request $request, $id): JsonResponse
{
    // Get RAB
    $rab = RabTemplate::find($id);

    // Get first detail (or create default)
    $detail = RabDetail::where('id_rab', $rab->id_rab)->first();

    if (!$detail) {
        // Create default detail for general realisasi
        $detail = RabDetail::create([
            'id_rab' => $rab->id_rab,
            'item' => 'Realisasi Umum',
            'nama_komponen' => 'Realisasi Umum',
            // ... other fields
        ]);
    }

    // Save each realisasi
    foreach ($request->realisasi as $item) {
        // Update total realisasi_pemakaian
        $detail->increment('realisasi_pemakaian', $item['jumlah']);

        // Save to history
        DB::table('rab_realisasi_history')->insert([
            'id_rab_detail' => $detail->id,
            'jumlah' => $item['jumlah'],
            'keterangan' => $item['keterangan'],
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => "Realisasi berhasil disimpan"
    ]);
}
```

## Data Flow

```
User klik "Realisasi"
  ↓
Modal terbuka
  ↓
Load history (langsung tampil)
  ↓
User klik "Tambah Baris"
  ↓
Input:
  - Keterangan: "Pembelian material"
  - Jumlah: 30000 (auto format → 30.000)
  ↓
Klik "Tambah Baris" lagi
  ↓
Input:
  - Keterangan: "Ongkos ojek"
  - Jumlah: 20000
  ↓
Klik "Simpan Realisasi"
  ↓
POST /admin/finance/rab/{id}/realisasi-simple
{
  "realisasi": [
    {"keterangan": "Pembelian material", "jumlah": 30000},
    {"keterangan": "Ongkos ojek", "jumlah": 20000}
  ]
}
  ↓
Backend:
  1. Get/Create default detail
  2. Increment realisasi_pemakaian
  3. Insert to rab_realisasi_history (per item)
  ↓
Response success
  ↓
Reload data & close modal
```

## Database

### rab_detail (Updated)

```
id | id_rab | nama_komponen   | realisasi_pemakaian
1  | 1      | Realisasi Umum  | 130000  (50000 + 30000 + 20000 + ...)
```

### rab_realisasi_history (New Records)

```
id | id_rab_detail | jumlah | keterangan          | user_id | created_at
1  | 1             | 50000  | Pembelian awal      | 1       | 2025-11-24 10:00
2  | 1             | 30000  | Pembelian material  | 1       | 2025-11-24 11:00
3  | 1             | 20000  | Ongkos ojek         | 2       | 2025-11-24 12:00
4  | 1             | 30000  | Upah tukang         | 1       | 2025-11-24 13:00
```

## User Experience

### Advantages ✅

1. **Super Simple**

    - Hanya 2 field: Keterangan + Jumlah
    - Tidak perlu pilih komponen
    - Tidak perlu mikir kategori

2. **Flexible**

    - Bisa input realisasi apapun
    - Tidak terbatas pada komponen yang sudah ada
    - Cocok untuk pengeluaran ad-hoc

3. **Transparent**

    - History langsung terlihat
    - Tracking lengkap per transaksi
    - Tahu siapa yang input dan kapan

4. **Fast**
    - Tambah baris → Input → Simpan
    - Tidak perlu banyak klik
    - Auto format number

### Workflow

```
1. Klik "Realisasi" → Modal terbuka
2. Lihat history (langsung tampil)
3. Klik "Tambah Baris"
4. Input keterangan: "Pembelian material"
5. Input jumlah: 30000 (auto format → 30.000)
6. Klik "Tambah Baris" lagi (jika perlu)
7. Ulangi input
8. Klik "Simpan Realisasi"
9. Done!
```

## Example Usage

### Scenario: Input Multiple Realisasi

```
RAB: Pembelian Material Proyek A
Budget: Rp 1.000.000
Realisasi Saat Ini: Rp 500.000
Sisa: Rp 500.000

Input Realisasi Baru:
1. Pembelian semen 10 sak     → Rp 300.000
2. Ongkos kirim                → Rp 50.000
3. Upah bongkar                → Rp 30.000
4. Biaya parkir                → Rp 10.000

Total Input: Rp 390.000

Setelah Simpan:
- Realisasi Saat Ini: Rp 890.000 (500.000 + 390.000)
- Sisa: Rp 110.000
- History: 4 record baru
```

## Validation

### Frontend

```javascript
const realisasiValid = this.realisasiData.realisasi_baru.filter(
    (r) => r.keterangan && r.keterangan.trim() && r.jumlah > 0
);

if (realisasiValid.length === 0) {
    alert("Tidak ada realisasi yang diinput");
    return;
}
```

### Backend

```php
$validator = Validator::make($request->all(), [
    'realisasi' => 'required|array',
    'realisasi.*.keterangan' => 'required|string',
    'realisasi.*.jumlah' => 'required|numeric|min:0'
]);
```

## Routes

```php
// In routes/web.php - admin.finance group
Route::post('rab/{id}/realisasi-simple', [FinanceAccountantController::class, 'saveRealisasiSimple'])
    ->name('rab.realisasi-simple');
```

## Summary

✅ Modal realisasi super simple
✅ History langsung tampil
✅ Tambah row dinamis (keterangan + jumlah)
✅ Tidak perlu pilih komponen
✅ Flexible untuk realisasi apapun
✅ Auto format number
✅ Data tersimpan ke rab_realisasi_history
✅ User friendly & efficient
