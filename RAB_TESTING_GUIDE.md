# Panduan Testing Manajemen RAB

## Persiapan

### 1. Jalankan Migration

```bash
php artisan migrate
```

Output yang diharapkan:

```
Migrating: 2025_11_24_000001_add_approval_columns_to_rab_detail_table
Migrated:  2025_11_24_000001_add_approval_columns_to_rab_detail_table (XX.XXms)
Migrating: 2025_11_24_000002_create_rab_realisasi_history_table
Migrated:  2025_11_24_000002_create_rab_realisasi_history_table (XX.XXms)
```

### 2. Clear Cache

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 3. Cek Route

```bash
php artisan route:list | grep rab
```

Output yang diharapkan:

```
GET|HEAD   admin/finance/rab ................ admin.finance.rab.index
GET|HEAD   admin/finance/rab/data ........... admin.finance.rab.data
POST       admin/finance/rab ................ admin.finance.rab.store
PUT        admin/finance/rab/{id} ........... admin.finance.rab.update
DELETE     admin/finance/rab/{id} ........... admin.finance.rab.delete
```

## Testing Manual

### Test 1: Akses Halaman RAB

1. Buka browser
2. Login ke sistem ERP
3. Akses: `http://your-domain/admin/finance/rab`
4. **Expected:** Halaman RAB terbuka dengan tampilan modern

### Test 2: Load Data RAB

1. Buka halaman RAB
2. Buka Developer Tools (F12) > Console
3. Cek apakah ada error
4. **Expected:**
    - Tidak ada error di console
    - Data RAB muncul (jika ada data)
    - Atau pesan "Belum ada data" jika kosong

### Test 3: Tambah RAB Baru

1. Klik tombol "Tambah RAB"
2. Isi form:
    ```
    Tanggal: 2025-11-24
    Nama Template: RAB Testing 1
    Deskripsi: Testing RAB baru
    Komponen:
      - Komponen A
      - Komponen B
    Budget Total: 5000000
    Nilai Disetujui: 4500000
    Status: Draft
    Produk Terkait: Tidak
    ```
3. Klik "Simpan"
4. **Expected:**
    - Alert "Data berhasil disimpan"
    - Modal tertutup
    - Data baru muncul di tabel
    - Data tersimpan di database

### Test 4: Edit RAB

1. Klik tombol "Edit" pada RAB yang baru dibuat
2. Ubah:
    ```
    Nama Template: RAB Testing 1 (Updated)
    Budget Total: 6000000
    ```
3. Klik "Simpan"
4. **Expected:**
    - Alert "Data berhasil diperbarui"
    - Data terupdate di tabel
    - Perubahan tersimpan di database

### Test 5: Lihat Detail RAB

1. Klik tombol "Lihat" pada RAB
2. **Expected:**
    - Modal detail terbuka
    - Menampilkan semua informasi RAB
    - Progress bar realisasi muncul
    - Tombol "Edit" dan "Tutup" berfungsi

### Test 6: Filter Status

1. Pilih dropdown "Status"
2. Pilih "Draft"
3. **Expected:**
    - Hanya RAB dengan status Draft yang muncul
    - RAB dengan status lain tersembunyi

### Test 7: Filter Produk Terkait

1. Pilih dropdown "Produk Terkait"
2. Pilih "Ada"
3. **Expected:**
    - Hanya RAB yang terkait produk yang muncul

### Test 8: Search

1. Ketik di search box: "Testing"
2. **Expected:**
    - Hanya RAB yang mengandung kata "Testing" yang muncul
    - Search bekerja untuk nama dan deskripsi

### Test 9: Sort Data

1. Pilih dropdown "Sort": "Nama Template"
2. Pilih "Terlama"
3. **Expected:**
    - Data tersort berdasarkan nama A-Z

### Test 10: Tambah Komponen Dinamis

1. Klik "Tambah RAB"
2. Klik "Tambah Komponen" beberapa kali
3. Isi komponen
4. Hapus salah satu komponen
5. **Expected:**
    - Komponen bertambah saat klik tambah
    - Komponen terhapus saat klik hapus
    - Form tetap responsif

### Test 11: Input Realisasi

1. Klik "Edit" pada RAB
2. Klik "Tambah Baris" di bagian Realisasi
3. Isi:
    ```
    Keterangan: Pembayaran Termin 1
    Jumlah: 2000000
    ```
4. Klik "Simpan"
5. **Expected:**
    - Realisasi tersimpan
    - Progress bar terupdate
    - Sisa budget berkurang

### Test 12: Export JSON

1. Klik tombol "Export"
2. **Expected:**
    - File JSON terdownload
    - Nama file: `manajemen_rab.json`
    - Isi file berisi semua data RAB

### Test 13: Import JSON

1. Klik tombol "Import"
2. Pilih file JSON yang sudah diexport
3. **Expected:**
    - Alert "Import berhasil"
    - Data dari JSON muncul di tabel
    - Data tersimpan di database

### Test 14: Hapus RAB

1. Klik tombol "Hapus" pada RAB
2. Konfirmasi penghapusan
3. **Expected:**
    - Alert "Data berhasil dihapus"
    - Data hilang dari tabel
    - Data terhapus dari database

### Test 15: Reset Filter

1. Set beberapa filter (status, produk, search)
2. Klik "Reset Filter"
3. **Expected:**
    - Semua filter kembali ke default
    - Semua data muncul kembali

## Testing Database

### Cek Data di Database

```sql
-- Cek tabel rab_template
SELECT * FROM rab_template ORDER BY created_at DESC LIMIT 5;

-- Cek tabel rab_detail
SELECT * FROM rab_detail WHERE id_rab = [ID_RAB] ORDER BY id;

-- Cek kolom baru di rab_detail
DESCRIBE rab_detail;

-- Cek tabel rab_realisasi_history
SELECT * FROM rab_realisasi_history ORDER BY created_at DESC LIMIT 5;

-- Cek relasi produk
SELECT * FROM produk_rab WHERE id_rab = [ID_RAB];
```

### Verifikasi Kolom Baru

```sql
-- Pastikan kolom ini ada di rab_detail:
-- - nilai_disetujui
-- - realisasi_pemakaian
-- - disetujui
-- - bukti_transfer
-- - sumber_dana
-- - nama_komponen
-- - budget
-- - biaya

SHOW COLUMNS FROM rab_detail;
```

## Testing API dengan Postman/cURL

### 1. Get RAB Data

```bash
curl -X GET "http://your-domain/admin/finance/rab/data" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

### 2. Create RAB

```bash
curl -X POST "http://your-domain/admin/finance/rab" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Cookie: laravel_session=YOUR_SESSION" \
  -d '{
    "name": "RAB API Test",
    "description": "Testing via API",
    "created_at": "2025-11-24",
    "components": ["Komponen 1", "Komponen 2"],
    "budget_total": 5000000,
    "approved_value": 4500000,
    "status": "DRAFT",
    "has_product": false,
    "spends": []
  }'
```

### 3. Update RAB

```bash
curl -X PUT "http://your-domain/admin/finance/rab/1" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Cookie: laravel_session=YOUR_SESSION" \
  -d '{
    "name": "RAB API Test Updated",
    "description": "Updated via API",
    "created_at": "2025-11-24",
    "components": ["Komponen 1", "Komponen 2", "Komponen 3"],
    "budget_total": 6000000,
    "approved_value": 5500000,
    "status": "APPROVED_ALL",
    "has_product": false,
    "spends": []
  }'
```

### 4. Delete RAB

```bash
curl -X DELETE "http://your-domain/admin/finance/rab/1" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

## Testing Error Handling

### Test 1: Validasi Form Kosong

1. Klik "Tambah RAB"
2. Klik "Simpan" tanpa mengisi form
3. **Expected:** Alert "Nama & Tanggal wajib diisi"

### Test 2: Budget Negatif

1. Klik "Tambah RAB"
2. Isi budget dengan nilai negatif
3. **Expected:** Validasi error dari backend

### Test 3: Hapus RAB yang Tidak Ada

1. Hapus RAB
2. Coba hapus lagi RAB yang sama
3. **Expected:** Error "RAB tidak ditemukan"

### Test 4: Network Error

1. Matikan koneksi internet
2. Coba tambah RAB
3. **Expected:** Alert "Terjadi kesalahan saat menyimpan data"

## Performance Testing

### Test 1: Load Time

1. Buka halaman RAB
2. Cek waktu load di Network tab
3. **Expected:** < 2 detik untuk load data

### Test 2: Banyak Data

1. Import 100+ RAB via JSON
2. Cek responsivitas halaman
3. **Expected:** Halaman tetap responsif

### Test 3: Filter Performance

1. Set filter dengan banyak data
2. Cek waktu response
3. **Expected:** < 1 detik untuk filter

## Browser Compatibility

Test di browser berikut:

-   [ ] Chrome (latest)
-   [ ] Firefox (latest)
-   [ ] Safari (latest)
-   [ ] Edge (latest)

## Mobile Responsive

Test di device:

-   [ ] Mobile (< 640px)
-   [ ] Tablet (640px - 1024px)
-   [ ] Desktop (> 1024px)

## Checklist Akhir

-   [ ] Semua test manual berhasil
-   [ ] Tidak ada error di console browser
-   [ ] Tidak ada error di log Laravel
-   [ ] Data tersimpan dengan benar di database
-   [ ] API response sesuai expected
-   [ ] Validasi berfungsi dengan baik
-   [ ] Error handling berfungsi
-   [ ] Performance acceptable
-   [ ] Browser compatibility OK
-   [ ] Mobile responsive OK

## Troubleshooting

### Problem: Data tidak muncul

**Solution:**

1. Cek console browser untuk error
2. Cek network tab untuk response API
3. Cek log Laravel: `tail -f storage/logs/laravel.log`
4. Pastikan route terdaftar: `php artisan route:list | grep rab`

### Problem: CSRF token mismatch

**Solution:**

1. Refresh halaman
2. Clear browser cache
3. Cek apakah `@csrf` ada di form

### Problem: Column not found

**Solution:**

1. Jalankan migration: `php artisan migrate`
2. Cek struktur tabel: `DESCRIBE rab_detail`

### Problem: 500 Internal Server Error

**Solution:**

1. Cek log Laravel
2. Enable debug mode di `.env`: `APP_DEBUG=true`
3. Cek permission folder storage

## Hasil Testing

Tanggal: ******\_\_\_******
Tester: ******\_\_\_******

| Test Case        | Status        | Catatan |
| ---------------- | ------------- | ------- |
| Akses Halaman    | ☐ Pass ☐ Fail |         |
| Load Data        | ☐ Pass ☐ Fail |         |
| Tambah RAB       | ☐ Pass ☐ Fail |         |
| Edit RAB         | ☐ Pass ☐ Fail |         |
| Lihat Detail     | ☐ Pass ☐ Fail |         |
| Filter Status    | ☐ Pass ☐ Fail |         |
| Filter Produk    | ☐ Pass ☐ Fail |         |
| Search           | ☐ Pass ☐ Fail |         |
| Sort             | ☐ Pass ☐ Fail |         |
| Komponen Dinamis | ☐ Pass ☐ Fail |         |
| Input Realisasi  | ☐ Pass ☐ Fail |         |
| Export JSON      | ☐ Pass ☐ Fail |         |
| Import JSON      | ☐ Pass ☐ Fail |         |
| Hapus RAB        | ☐ Pass ☐ Fail |         |
| Reset Filter     | ☐ Pass ☐ Fail |         |

**Overall Status:** ☐ PASS ☐ FAIL

**Notes:**

---

---

---
