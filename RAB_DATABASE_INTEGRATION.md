# RAB Database Integration - Complete

## ✅ Struktur Database

### 1. **rab_template** (Header RAB)

```sql
- id_rab (PK)
- outlet_id (FK → outlets)
- book_id (FK → accounting_books)
- nama_template
- deskripsi
- total_biaya
- is_active
- created_at, updated_at
```

### 2. **rab_detail** (Detail Komponen)

```sql
- id (PK)
- id_rab (FK → rab_template)
- item
- nama_komponen
- deskripsi
- qty
- jumlah
- satuan
- harga
- harga_satuan
- subtotal
- budget
- biaya
- nilai_disetujui (Admin approve)
- realisasi_pemakaian
- disetujui (boolean)
- bukti_transfer
- sumber_dana
- created_at, updated_at
```

### 3. **rab_realisasi_history** (History Realisasi)

```sql
- id (PK)
- id_rab_detail (FK → rab_detail)
- jumlah
- keterangan
- user_id (FK → users)
- created_at, updated_at
```

## ✅ Perubahan yang Dilakukan

### 1. **Controller - FinanceAccountantController.php**

#### `storeRab()` - Support Format Baru

```php
// Support format baru: {uraian, biaya}
if (is_array($component) && isset($component['uraian'])) {
    $uraian = $component['uraian'];
    $biaya = $component['biaya'] ?? 0;

    RabDetail::create([
        'id_rab' => $rab->id_rab,
        'item' => $uraian,
        'nama_komponen' => $uraian,
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
```

#### `rabData()` - Return Format Baru

```php
'components' => $rab->details->map(function($detail) {
    return [
        'uraian' => $detail->nama_komponen ?? $detail->item,
        'biaya' => (float) ($detail->biaya ?? $detail->budget ?? 0)
    ];
})->toArray(),

'details' => $rab->details->map(function($detail) {
    return [
        'id' => $detail->id,
        'nama_komponen' => $detail->nama_komponen,
        'jumlah' => (float) $detail->jumlah,
        'satuan' => $detail->satuan,
        'harga_satuan' => (float) $detail->harga_satuan,
        'budget' => (float) $detail->budget,
        'nilai_disetujui' => (float) $detail->nilai_disetujui,
        'realisasi_pemakaian' => (float) $detail->realisasi_pemakaian,
        'disetujui' => (bool) $detail->disetujui,
        'deskripsi' => $detail->deskripsi
    ];
})->toArray()
```

### 2. **Frontend - index.blade.php**

#### Modal Approval (Admin)

-   Admin dapat edit qty, satuan, harga_satuan per komponen
-   Admin dapat set nilai_disetujui per komponen
-   Admin dapat approve/reject per komponen
-   Auto calculate budget = qty × harga_satuan
-   Total budget dan total disetujui otomatis

#### Modal Detail View

-   Menampilkan tabel detail komponen lengkap
-   Kolom: Komponen, Qty, Satuan, Harga, Budget, Disetujui, Realisasi
-   Tombol "Approve" untuk admin (jika status DRAFT/PENDING_APPROVAL)

#### Form RAB

-   Komponen dengan 2 kolom: Uraian & Biaya
-   Budget total auto calculate dari sum biaya komponen
-   Field budget total readonly

## Workflow Lengkap

### 1. **Pegawai Buat RAB**

```
1. Klik "Tambah RAB"
2. Isi outlet, buku, nama, deskripsi
3. Tambah komponen:
   - Uraian: "Ongkos ojek (1 pcs)"
   - Biaya: 50000
4. Budget Total: Auto = sum(biaya)
5. Status: DRAFT atau PENDING_APPROVAL
6. Simpan → Data masuk ke rab_template & rab_detail
```

### 2. **Admin Approve RAB**

```
1. Buka RAB (status DRAFT/PENDING_APPROVAL)
2. Klik "Approve"
3. Modal approval terbuka dengan tabel detail
4. Admin edit per komponen:
   - Qty: 1 → 2
   - Satuan: pcs
   - Harga Satuan: 50000
   - Budget: Auto = 2 × 50000 = 100000
   - Nilai Disetujui: 90000
   - Approve: ✓
5. Status: APPROVED / APPROVED_WITH_REV / REJECTED
6. Simpan → Update rab_detail (nilai_disetujui, disetujui, dll)
```

### 3. **Input Realisasi**

```
1. RAB dengan status APPROVED/APPROVED_WITH_REV/TRANSFERRED
2. Klik tombol "Realisasi"
3. Modal realisasi terbuka
4. Tambah baris realisasi:
   - Keterangan: "Pembelian material"
   - Jumlah: 45000
5. Simpan → Update rab_detail.realisasi_pemakaian
   (Opsional: Bisa juga simpan ke rab_realisasi_history)
```

## Data Flow

```
Frontend (Alpine.js)
  ↓
  components: [{uraian, biaya}, ...]
  ↓
Controller (storeRab/updateRab)
  ↓
  rab_template (header)
  rab_detail (komponen dengan semua field)
  ↓
Controller (rabData)
  ↓
  components: [{uraian, biaya}, ...]
  details: [{id, nama_komponen, qty, satuan, harga_satuan, budget, nilai_disetujui, ...}, ...]
  ↓
Frontend (Display)
```

## Testing Checklist

-   [x] Buat RAB baru dengan komponen {uraian, biaya}
-   [x] Komponen tersimpan ke rab_detail
-   [x] Load RAB menampilkan komponen dengan benar
-   [x] Modal detail menampilkan tabel komponen lengkap
-   [x] Admin bisa klik "Approve"
-   [x] Modal approval menampilkan detail komponen
-   [x] Admin bisa edit qty, satuan, harga_satuan
-   [x] Budget auto calculate (qty × harga_satuan)
-   [x] Admin bisa set nilai_disetujui per komponen
-   [x] Total budget dan total disetujui auto calculate
-   [x] Simpan approval update rab_detail
-   [x] Modal realisasi hanya muncul setelah approved
-   [x] Input realisasi update rab_detail.realisasi_pemakaian

## Catatan Penting

1. **Backward Compatibility**: Controller support format lama (string) dan format baru (object)
2. **Auto Calculate**: Budget total = sum(biaya komponen)
3. **Approval Flow**: DRAFT → PENDING_APPROVAL → APPROVED → Input Realisasi
4. **Admin Only**: Modal approval hanya untuk admin
5. **Data Integrity**: Semua field rab_detail terisi dengan benar
