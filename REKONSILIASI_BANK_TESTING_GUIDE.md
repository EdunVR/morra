# 🧪 Panduan Testing Rekonsiliasi Bank

## Persiapan Testing

### 1. Jalankan Migration

```bash
php artisan migrate
```

### 2. Pastikan Data Prerequisites Ada

-   ✅ Minimal 1 outlet di tabel `outlets`
-   ✅ Minimal 1 bank account di tabel `company_bank_accounts`
-   ✅ User sudah login ke sistem

### 3. Akses Halaman

```
URL: http://your-domain/admin/finance/rekonsiliasi
Menu: Keuangan (F&A) → Rekonsiliasi Bank
```

---

## 📝 Test Case 1: Buat Rekonsiliasi Baru

### Steps:

1. Klik tombol **"Buat Rekonsiliasi"**
2. Isi form:
    - **Outlet**: Pilih outlet yang tersedia
    - **Rekening Bank**: Pilih rekening bank
    - **Tanggal Rekonsiliasi**: Pilih tanggal hari ini
    - **Periode**: Pilih bulan/tahun (contoh: 2025-11)
    - **Saldo Bank Statement**: 10,000,000
    - **Saldo Buku**: 9,500,000
    - **Catatan**: "Rekonsiliasi bulan November 2025"
3. Perhatikan **Selisih** otomatis terhitung: Rp 500,000
4. Klik **"Simpan"**

### Expected Result:

-   ✅ Notifikasi sukses muncul
-   ✅ Modal tertutup
-   ✅ Data baru muncul di tabel dengan status **Draft**
-   ✅ Statistik card "Draft" bertambah 1

---

## 📝 Test Case 2: Edit Rekonsiliasi Draft

### Steps:

1. Cari rekonsiliasi dengan status **Draft**
2. Klik tombol **"Edit"**
3. Ubah **Saldo Bank Statement** menjadi: 10,500,000
4. Perhatikan **Selisih** berubah menjadi: Rp 1,000,000
5. Klik **"Simpan"**

### Expected Result:

-   ✅ Notifikasi sukses muncul
-   ✅ Data terupdate di tabel
-   ✅ Selisih baru terlihat di tabel

---

## 📝 Test Case 3: Selesaikan Rekonsiliasi

### Steps:

1. Cari rekonsiliasi dengan status **Draft**
2. Klik tombol **"Selesai"**
3. Konfirmasi dialog yang muncul

### Expected Result:

-   ✅ Status berubah menjadi **Selesai** (badge hijau)
-   ✅ Tombol "Edit" hilang
-   ✅ Tombol "Setujui" muncul
-   ✅ Statistik "Draft" berkurang, "Selesai" bertambah

---

## 📝 Test Case 4: Approve Rekonsiliasi

### Steps:

1. Cari rekonsiliasi dengan status **Selesai**
2. Klik tombol **"Setujui"**
3. Konfirmasi dialog yang muncul

### Expected Result:

-   ✅ Status berubah menjadi **Disetujui** (badge biru)
-   ✅ Tombol "Setujui" hilang
-   ✅ Tombol "Hapus" hilang
-   ✅ Statistik "Selesai" berkurang, "Disetujui" bertambah
-   ✅ Kolom "Disetujui Oleh" terisi

---

## 📝 Test Case 5: Export PDF

### Steps:

1. Pilih rekonsiliasi mana saja
2. Klik tombol **"PDF"**

### Expected Result:

-   ✅ File PDF ter-download
-   ✅ PDF berisi:
    -   Header dengan judul "REKONSILIASI BANK"
    -   Informasi outlet dan periode
    -   Detail bank account
    -   Ringkasan saldo
    -   Selisih dengan warna (merah jika ada selisih)
    -   Section tanda tangan

---

## 📝 Test Case 6: Filter Data

### Test 6.1: Filter by Outlet

1. Pilih outlet tertentu di dropdown **Outlet**
2. Data otomatis ter-filter

**Expected**: Hanya rekonsiliasi dari outlet tersebut yang muncul

### Test 6.2: Filter by Status

1. Pilih **"Draft"** di dropdown **Status**
2. Data otomatis ter-filter

**Expected**: Hanya rekonsiliasi dengan status Draft yang muncul

### Test 6.3: Filter by Periode

1. Pilih periode (contoh: 2025-11) di input **Periode**
2. Data otomatis ter-filter

**Expected**: Hanya rekonsiliasi periode tersebut yang muncul

### Test 6.4: Filter by Bank Account

1. Pilih rekening bank di dropdown **Rekening Bank**
2. Data otomatis ter-filter

**Expected**: Hanya rekonsiliasi dari bank tersebut yang muncul

---

## 📝 Test Case 7: Delete Rekonsiliasi

### Test 7.1: Delete Draft (Harus Berhasil)

1. Cari rekonsiliasi dengan status **Draft**
2. Klik tombol **Hapus** (icon trash)
3. Konfirmasi dialog

**Expected**:

-   ✅ Data terhapus dari tabel
-   ✅ Notifikasi sukses muncul
-   ✅ Statistik terupdate

### Test 7.2: Delete Approved (Harus Gagal)

1. Cari rekonsiliasi dengan status **Disetujui**
2. Perhatikan tombol **Hapus** tidak ada

**Expected**:

-   ✅ Tombol hapus tidak muncul untuk status Approved
-   ✅ Jika dipaksa via API, akan error

---

## 📝 Test Case 8: Validasi Form

### Test 8.1: Submit Form Kosong

1. Klik **"Buat Rekonsiliasi"**
2. Langsung klik **"Simpan"** tanpa isi form

**Expected**:

-   ✅ Form tidak tersubmit
-   ✅ Browser validation muncul (required fields)

### Test 8.2: Saldo Negatif

1. Buat rekonsiliasi baru
2. Isi **Saldo Bank Statement**: -1000000
3. Klik **"Simpan"**

**Expected**:

-   ✅ Bisa tersimpan (saldo negatif valid untuk overdraft)
-   ✅ Selisih terhitung dengan benar

### Test 8.3: Tanggal Invalid

1. Buat rekonsiliasi baru
2. Isi tanggal dengan format salah (manual edit HTML)
3. Klik **"Simpan"**

**Expected**:

-   ✅ Backend validation menolak
-   ✅ Error message muncul

---

## 📝 Test Case 9: Responsive Design

### Test 9.1: Mobile View (< 768px)

1. Buka di mobile atau resize browser
2. Navigasi halaman

**Expected**:

-   ✅ Layout menyesuaikan
-   ✅ Tabel bisa di-scroll horizontal
-   ✅ Cards tersusun vertikal
-   ✅ Modal full width

### Test 9.2: Tablet View (768px - 1024px)

1. Resize browser ke ukuran tablet
2. Navigasi halaman

**Expected**:

-   ✅ Layout optimal untuk tablet
-   ✅ Filter grid 2 kolom
-   ✅ Cards grid 2 kolom

---

## 📝 Test Case 10: Performance

### Test 10.1: Load Time

1. Akses halaman rekonsiliasi
2. Perhatikan waktu loading

**Expected**:

-   ✅ Halaman load < 2 detik
-   ✅ Loading indicator muncul saat fetch data

### Test 10.2: Multiple Filters

1. Aktifkan semua filter sekaligus
2. Perhatikan response time

**Expected**:

-   ✅ Data ter-filter dengan cepat
-   ✅ Tidak ada lag

---

## 📝 Test Case 11: Edge Cases

### Test 11.1: Tidak Ada Data

1. Filter dengan kombinasi yang tidak ada datanya
2. Perhatikan empty state

**Expected**:

-   ✅ Empty state muncul
-   ✅ Pesan "Tidak ada data rekonsiliasi"
-   ✅ Tombol "Buat Rekonsiliasi Pertama"

### Test 11.2: Selisih Nol

1. Buat rekonsiliasi dengan saldo sama
    - Saldo Bank: 10,000,000
    - Saldo Buku: 10,000,000
2. Simpan

**Expected**:

-   ✅ Selisih: Rp 0 (warna hijau)
-   ✅ Tersimpan dengan normal

### Test 11.3: Selisih Besar

1. Buat rekonsiliasi dengan selisih besar
    - Saldo Bank: 100,000,000
    - Saldo Buku: 10,000,000
2. Simpan

**Expected**:

-   ✅ Selisih: Rp 90,000,000 (warna merah)
-   ✅ Tersimpan dengan normal

---

## 📝 Test Case 12: Concurrent Users

### Steps:

1. Login dengan 2 user berbeda
2. User A buat rekonsiliasi
3. User B refresh halaman
4. User B lihat data dari User A

**Expected**:

-   ✅ Data sinkron antar user
-   ✅ Tidak ada conflict

---

## 📝 Test Case 13: Browser Compatibility

Test di berbagai browser:

-   ✅ Chrome (latest)
-   ✅ Firefox (latest)
-   ✅ Safari (latest)
-   ✅ Edge (latest)

**Expected**: Semua fitur berfungsi normal di semua browser

---

## 🎯 Checklist Testing Lengkap

### Functional

-   [ ] Create rekonsiliasi
-   [ ] Edit rekonsiliasi draft
-   [ ] Complete rekonsiliasi
-   [ ] Approve rekonsiliasi
-   [ ] Delete rekonsiliasi draft
-   [ ] Export PDF
-   [ ] Filter by outlet
-   [ ] Filter by status
-   [ ] Filter by periode
-   [ ] Filter by bank account
-   [ ] View statistics
-   [ ] Refresh data

### Validation

-   [ ] Required fields
-   [ ] Numeric validation
-   [ ] Date validation
-   [ ] Status transition rules
-   [ ] Delete approved (harus gagal)

### UI/UX

-   [ ] Responsive mobile
-   [ ] Responsive tablet
-   [ ] Loading states
-   [ ] Empty states
-   [ ] Success notifications
-   [ ] Error notifications
-   [ ] Confirmation dialogs
-   [ ] Modal animations

### Performance

-   [ ] Page load time
-   [ ] Filter response time
-   [ ] PDF generation time
-   [ ] Large dataset handling

### Security

-   [ ] CSRF protection
-   [ ] SQL injection prevention
-   [ ] XSS prevention
-   [ ] Authorization checks

---

## 🐛 Bug Report Template

Jika menemukan bug, gunakan template ini:

```
**Bug Title**: [Judul singkat bug]

**Steps to Reproduce**:
1. ...
2. ...
3. ...

**Expected Result**:
...

**Actual Result**:
...

**Screenshots**:
[Attach screenshot]

**Environment**:
- Browser: ...
- OS: ...
- Screen Size: ...

**Additional Notes**:
...
```

---

## ✅ Testing Sign-off

Setelah semua test case passed:

```
Tested By: ___________________
Date: ___________________
Status: [ ] PASSED  [ ] FAILED
Notes: ___________________
```

---

**Happy Testing! 🚀**
