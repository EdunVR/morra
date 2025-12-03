# Debug RAB Save - Testing Guide

## Logging yang Sudah Ditambahkan

### Backend (Laravel Log)

File: `storage/logs/laravel.log`

**Log Points:**

1. Request data masuk
2. Validation errors (jika ada)
3. RAB Template created
4. Processing each component
5. Component data detail
6. RabDetail created
7. Success dengan jumlah details
8. Error dengan stack trace

### Frontend (Browser Console)

Buka Developer Tools (F12) → Console

**Log Points:**

1. Form data sebelum dikirim
2. Components array
3. Request URL & method
4. Response status
5. Response data
6. Error (jika ada)

## Testing Steps

### 1. Buka Halaman RAB

```
URL: https://group.dahana-boiler.com/MORRA/admin/finance/rab
```

### 2. Buka Developer Tools

```
Tekan F12
Pilih tab "Console"
Pilih tab "Network"
```

### 3. Buat RAB Baru

```
1. Klik "Tambah RAB"
2. Isi form:
   - Outlet: Pilih outlet
   - Buku: Pilih buku
   - Tanggal: Hari ini
   - Nama: "Test RAB Debug"
   - Deskripsi: "Testing save"

3. Tambah Komponen:
   - Uraian: "Test Item 1"
   - Qty: 2
   - Satuan: "pcs"
   - Harga: 50000

4. Klik "Simpan"
```

### 4. Cek Console Browser

```
Lihat output:
=== SAVING RAB ===
Form data: {...}
Components: [...]
Request URL: ...
Request method: POST
Response status: 200
Response data: {...}
```

### 5. Cek Laravel Log

```bash
# Di terminal/PowerShell
Get-Content storage/logs/laravel.log -Tail 50

# Atau buka file langsung
storage/logs/laravel.log
```

**Cari log:**

```
=== STORE RAB REQUEST ===
Request data: {...}
RAB Template created: {"id_rab":...}
Creating details from components: {"count":1}
Processing component #0: {...}
Component data: {...}
RabDetail created: {"id":...}
RAB created successfully: {"id_rab":...,"details_count":1}
```

### 6. Cek Database

```sql
-- Cek RAB Template terakhir
SELECT * FROM rab_template ORDER BY id_rab DESC LIMIT 1;

-- Cek RAB Detail untuk RAB terakhir
SELECT * FROM rab_detail WHERE id_rab = [id_rab_terakhir];

-- Cek semua field
SELECT
    id, id_rab, item, nama_komponen,
    qty, jumlah, satuan,
    harga, harga_satuan,
    subtotal, budget, biaya
FROM rab_detail
WHERE id_rab = [id_rab_terakhir];
```

## Troubleshooting

### Jika Response 422 (Validation Error)

**Cek Console:**

```javascript
Response data: {
  success: false,
  message: "Validasi gagal",
  errors: {...}
}
```

**Cek Laravel Log:**

```
Validation failed: {...}
```

**Solusi:**

-   Pastikan semua field required terisi
-   Cek format data (number, string, array)
-   Cek status value (harus salah satu dari: DRAFT, PENDING_APPROVAL, APPROVED, dll)

### Jika Response 500 (Server Error)

**Cek Laravel Log:**

```
Error creating RAB: ...
Stack trace: ...
```

**Kemungkinan Penyebab:**

1. Database connection error
2. Foreign key constraint error
3. Field tidak ada di tabel
4. Data type mismatch

### Jika Components Tidak Tersimpan

**Cek Laravel Log:**

```
Creating details from components: {"count":0}
```

**Kemungkinan:**

-   Components array kosong
-   Components tidak dikirim
-   Format components salah

**Cek Console:**

```javascript
Components: []; // Kosong!
```

**Solusi:**

-   Pastikan addComponent() dipanggil
-   Pastikan form.components adalah array
-   Pastikan ada minimal 1 komponen dengan uraian terisi

### Jika RabDetail Tidak Dibuat

**Cek Laravel Log:**

```
Processing component #0: {...}
Component data: {...}
// Tidak ada log "RabDetail created"
```

**Kemungkinan:**

-   Uraian kosong (if (!empty($uraian)) gagal)
-   Error saat create (exception)
-   Primary key salah

**Cek:**

```sql
DESCRIBE rab_detail;
-- Pastikan primary key adalah 'id', bukan 'id_rab_detail'
```

## Expected Output

### Console Browser (Success)

```javascript
=== SAVING RAB ===
Form data: {
  "name": "Test RAB Debug",
  "components": [
    {
      "uraian": "Test Item 1",
      "qty": 2,
      "satuan": "pcs",
      "harga_satuan": 50000,
      "biaya": 100000
    }
  ],
  "budget_total": 100000,
  ...
}
Request URL: http://.../admin/finance/rab
Request method: POST
Response status: 200
Response data: {
  "success": true,
  "message": "RAB berhasil dibuat"
}
```

### Laravel Log (Success)

```
=== STORE RAB REQUEST ===
Request data: {...}
RAB Template created: {"id_rab":123}
Creating details from components: {"count":1}
Processing component #0: {"uraian":"Test Item 1","qty":2,...}
Component data: {"uraian":"Test Item 1","qty":2,"satuan":"pcs","harga_satuan":50000,"biaya":100000}
RabDetail created: {"id":456}
RAB created successfully: {"id_rab":123,"details_count":1}
```

### Database (Success)

```sql
-- rab_template
id_rab | nama_template    | total_biaya
123    | Test RAB Debug   | 100000

-- rab_detail
id  | id_rab | item         | nama_komponen | qty | satuan | harga_satuan | budget
456 | 123    | Test Item 1  | Test Item 1   | 2   | pcs    | 50000        | 100000
```

## Next Steps

Setelah testing:

1. Screenshot console output
2. Copy Laravel log
3. Screenshot database query result
4. Laporkan hasil testing

Jika ada error, sertakan:

-   Console error message
-   Laravel log error
-   Database query result
-   Screenshot form sebelum simpan
