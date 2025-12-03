# 🧪 Testing Guide - Modul SDM Kepegawaian

## Pre-requisites

1. ✅ Database migration sudah dijalankan
2. ✅ Routes sudah terdaftar
3. ✅ User sudah login dengan permission yang sesuai

## Test Scenarios

### 1. Test Akses Menu ✅

**Steps:**

1. Login ke sistem
2. Lihat sidebar kiri
3. Cari menu "SDM"
4. Klik menu "SDM"
5. Klik submenu "Kepegawaian & Rekrutmen"

**Expected Result:**

-   Menu SDM muncul di sidebar
-   Submenu "Kepegawaian & Rekrutmen" dapat diklik
-   Halaman kepegawaian terbuka tanpa error
-   URL: `/admin/sdm/kepegawaian`

---

### 2. Test Tampilan Awal ✅

**Steps:**

1. Buka halaman kepegawaian

**Expected Result:**

-   Header "Kepegawaian & Rekrutmen" muncul
-   4 stats cards muncul (Aktif, Tidak Aktif, Resign, Total)
-   Filter section muncul (Status, Departemen, Search)
-   Tombol "Tambah Karyawan" muncul
-   Tombol export PDF dan Excel muncul
-   Tabel data muncul (bisa kosong jika belum ada data)

---

### 3. Test Tambah Karyawan ✅

**Steps:**

1. Klik tombol "Tambah Karyawan"
2. Isi form:
    ```
    Nama: Test User 1
    Posisi: Software Developer
    Departemen: IT
    Status: Aktif
    Telepon: 08123456789
    Email: test1@example.com
    Gaji: 10000000
    Tarif Per Jam: 50000
    Tanggal Bergabung: 01/12/2024
    ID Fingerprint: FP001
    Alamat: Jl. Test No. 1
    ```
3. Tambah Job Description:
    - Klik tombol + (hijau)
    - Isi: "Develop web applications"
    - Klik + lagi
    - Isi: "Code review"
4. Klik "Simpan"

**Expected Result:**

-   Modal tertutup
-   Alert "Karyawan berhasil ditambahkan" muncul
-   Data muncul di tabel
-   Stats cards terupdate (Total +1, Aktif +1)

---

### 4. Test Edit Karyawan ✅

**Steps:**

1. Klik icon Edit (pensil) pada karyawan yang baru ditambahkan
2. Ubah data:
    ```
    Posisi: Senior Software Developer
    Gaji: 15000000
    ```
3. Klik "Simpan"

**Expected Result:**

-   Modal tertutup
-   Alert "Data karyawan berhasil diupdate" muncul
-   Data di tabel terupdate
-   Posisi berubah menjadi "Senior Software Developer"
-   Gaji berubah menjadi "Rp 15.000.000"

---

### 5. Test Filter Status ✅

**Steps:**

1. Tambah beberapa karyawan dengan status berbeda:
    - 2 karyawan status "Aktif"
    - 1 karyawan status "Tidak Aktif"
    - 1 karyawan status "Resign"
2. Pilih filter Status: "Aktif"
3. Klik "Filter"

**Expected Result:**

-   Hanya karyawan dengan status "Aktif" yang muncul (2 karyawan)
-   Stats cards terupdate sesuai filter

---

### 6. Test Filter Departemen ✅

**Steps:**

1. Pastikan ada karyawan dari departemen berbeda (IT, HR, Finance)
2. Pilih filter Departemen: "IT"
3. Klik "Filter"

**Expected Result:**

-   Hanya karyawan dari departemen IT yang muncul
-   Dropdown departemen menampilkan semua departemen yang ada

---

### 7. Test Search ✅

**Steps:**

1. Ketik "Test" di search box
2. Tunggu 500ms (debounce)

**Expected Result:**

-   Data otomatis terfilter
-   Hanya karyawan dengan nama/posisi/telepon/email yang mengandung "Test" yang muncul

---

### 8. Test Job Description Dynamic Fields ✅

**Steps:**

1. Klik "Tambah Karyawan"
2. Di bagian Job Description:
    - Isi field pertama: "Task 1"
    - Klik tombol + (hijau)
    - Isi field kedua: "Task 2"
    - Klik tombol + lagi
    - Isi field ketiga: "Task 3"
    - Klik tombol - (merah) pada field kedua
3. Simpan

**Expected Result:**

-   Field baru muncul saat klik +
-   Field terhapus saat klik -
-   Data tersimpan hanya "Task 1" dan "Task 3"

---

### 9. Test Delete Karyawan ✅

**Steps:**

1. Klik icon Delete (tempat sampah) pada salah satu karyawan
2. Klik "OK" pada konfirmasi

**Expected Result:**

-   Alert "Yakin ingin menghapus karyawan ini?" muncul
-   Setelah konfirmasi, data terhapus dari tabel
-   Stats cards terupdate (Total -1)
-   Alert "Karyawan berhasil dihapus" muncul

---

### 10. Test Export PDF ✅

**Steps:**

1. Pastikan ada beberapa data karyawan
2. (Opsional) Set filter tertentu
3. Klik icon PDF (merah)

**Expected Result:**

-   File PDF terdownload
-   Nama file: `data-karyawan-YYYY-MM-DD.pdf`
-   PDF berisi:
    -   Header "Laporan Data Karyawan"
    -   Tanggal cetak
    -   Tabel data karyawan sesuai filter
    -   Total karyawan

---

### 11. Test Export Excel ✅

**Steps:**

1. Pastikan ada beberapa data karyawan
2. (Opsional) Set filter tertentu
3. Klik icon Excel (hijau)

**Expected Result:**

-   File Excel terdownload
-   Nama file: `data-karyawan-YYYY-MM-DD.xlsx`
-   Excel berisi:
    -   Header bold
    -   Data karyawan sesuai filter
    -   Kolom: No, Nama, Posisi, Departemen, Status, Telepon, Email, Gaji, Tarif Per Jam, Tgl Bergabung, ID Fingerprint

---

### 12. Test Stats Cards Update ✅

**Steps:**

1. Perhatikan stats cards awal
2. Tambah 1 karyawan status "Aktif"
3. Tambah 1 karyawan status "Resign"

**Expected Result:**

-   Total Karyawan: +2
-   Karyawan Aktif: +1
-   Resign: +1
-   Stats update otomatis setelah setiap operasi

---

### 13. Test Validation ✅

**Steps:**

1. Klik "Tambah Karyawan"
2. Kosongkan field "Nama" (required)
3. Klik "Simpan"

**Expected Result:**

-   Form tidak tersubmit
-   Browser menampilkan pesan "Please fill out this field"

**Steps 2:**

1. Isi Nama tapi kosongkan Posisi
2. Klik "Simpan"

**Expected Result:**

-   Form tidak tersubmit
-   Browser menampilkan pesan "Please fill out this field" pada Posisi

---

### 14. Test Responsive Design ✅

**Steps:**

1. Buka halaman di desktop (1920px)
2. Resize browser ke tablet (768px)
3. Resize browser ke mobile (375px)

**Expected Result:**

-   Desktop: Grid 4 kolom untuk stats cards
-   Tablet: Grid 2 kolom untuk stats cards
-   Mobile: Grid 1 kolom untuk stats cards
-   Tabel scrollable horizontal di mobile
-   Modal responsive di semua ukuran

---

### 15. Test Error Handling ✅

**Steps:**

1. Matikan koneksi internet
2. Coba tambah karyawan
3. Nyalakan kembali koneksi

**Expected Result:**

-   Alert "Gagal menyimpan data" muncul
-   Data tidak tersimpan
-   Setelah koneksi kembali, operasi bisa dilakukan normal

---

## Test Results Summary

| No  | Test Case         | Status | Notes |
| --- | ----------------- | ------ | ----- |
| 1   | Akses Menu        | ✅     | -     |
| 2   | Tampilan Awal     | ✅     | -     |
| 3   | Tambah Karyawan   | ✅     | -     |
| 4   | Edit Karyawan     | ✅     | -     |
| 5   | Filter Status     | ✅     | -     |
| 6   | Filter Departemen | ✅     | -     |
| 7   | Search            | ✅     | -     |
| 8   | Job Description   | ✅     | -     |
| 9   | Delete Karyawan   | ✅     | -     |
| 10  | Export PDF        | ✅     | -     |
| 11  | Export Excel      | ✅     | -     |
| 12  | Stats Update      | ✅     | -     |
| 13  | Validation        | ✅     | -     |
| 14  | Responsive        | ✅     | -     |
| 15  | Error Handling    | ✅     | -     |

## Performance Testing

### Load Time

-   **Target**: < 2 detik untuk load halaman
-   **Target**: < 1 detik untuk filter/search

### Data Volume

-   **Tested with**: 100 karyawan
-   **Expected**: Smooth operation
-   **Recommendation**: Implement pagination jika > 500 karyawan

## Browser Compatibility

Tested on:

-   ✅ Chrome (latest)
-   ✅ Firefox (latest)
-   ✅ Edge (latest)
-   ✅ Safari (latest)

## Known Issues

Tidak ada issue yang ditemukan saat ini.

## Recommendations

1. ✅ Semua fitur dasar sudah berfungsi dengan baik
2. 💡 Pertimbangkan menambahkan pagination untuk data besar
3. 💡 Pertimbangkan menambahkan bulk actions (delete multiple)
4. 💡 Pertimbangkan menambahkan import Excel
5. 💡 Pertimbangkan menambahkan upload foto karyawan

---

**Tested by**: Developer  
**Date**: 2 Desember 2024  
**Status**: All Tests Passed ✅
