# 🧪 Testing Guide - Modul CRM Manajemen Pelanggan

## 📋 Checklist Testing

### ✅ 1. Akses Halaman

-   [ ] Buka browser dan login ke ERP
-   [ ] Klik sidebar → **Pelanggan (CRM)** → **Manajemen Pelanggan**
-   [ ] URL harus: `http://localhost/admin/crm/pelanggan`
-   [ ] Halaman loading dengan benar (tidak ada error 404/500)

**Expected Result**:

-   Halaman terbuka dengan tampilan modern
-   Menampilkan 3 statistik cards (Total Pelanggan, Total Piutang, Outlet Aktif)
-   Menampilkan filter section (Outlet, Tipe Customer, Search, Export buttons)
-   Menampilkan tabel dengan DataTables

---

### ✅ 2. Statistik Cards

-   [ ] Check apakah Total Pelanggan menampilkan angka yang benar
-   [ ] Check apakah Total Piutang menampilkan format Rupiah
-   [ ] Check apakah Outlet Aktif menampilkan jumlah outlet

**Expected Result**:

-   Semua angka statistik muncul dengan benar
-   Format Rupiah: `Rp 1.000.000`
-   Icon muncul di setiap card

---

### ✅ 3. DataTables Loading

-   [ ] Tabel menampilkan data pelanggan dari database
-   [ ] Kolom yang muncul: No, Kode, Nama, Telepon, Alamat, Tipe, Outlet, Piutang, Aksi
-   [ ] Pagination berfungsi (jika data > 10)
-   [ ] Sorting berfungsi (klik header kolom)

**Expected Result**:

-   Data pelanggan muncul di tabel
-   Pagination muncul jika data banyak
-   Sorting berfungsi untuk setiap kolom

---

### ✅ 4. Filter Outlet

-   [ ] Pilih outlet dari dropdown
-   [ ] Tabel otomatis reload dan filter data sesuai outlet
-   [ ] Statistik cards update sesuai filter

**Expected Result**:

-   Data di tabel ter-filter sesuai outlet yang dipilih
-   Statistik update sesuai filter

---

### ✅ 5. Filter Tipe Customer

-   [ ] Pilih tipe customer dari dropdown
-   [ ] Tabel otomatis reload dan filter data sesuai tipe
-   [ ] Kombinasi filter outlet + tipe berfungsi

**Expected Result**:

-   Data di tabel ter-filter sesuai tipe yang dipilih
-   Multiple filter berfungsi bersamaan

---

### ✅ 6. Search Function

-   [ ] Ketik nama pelanggan di search box
-   [ ] Tunggu 500ms, tabel otomatis reload
-   [ ] Coba search dengan telepon
-   [ ] Coba search dengan alamat
-   [ ] Coba search dengan kode member

**Expected Result**:

-   Search berfungsi dengan debounce 500ms
-   Hasil search muncul sesuai keyword
-   Search case-insensitive

---

### ✅ 7. Tambah Pelanggan

-   [ ] Klik tombol "Tambah Pelanggan"
-   [ ] Modal muncul dengan form kosong
-   [ ] Isi form:
    -   Nama: "Test Customer"
    -   Telepon: "08123456789"
    -   Tipe Customer: Pilih salah satu
    -   Outlet: Pilih salah satu
    -   Alamat: "Jl. Test No. 123"
-   [ ] Klik "Simpan"
-   [ ] Modal tertutup
-   [ ] Tabel reload dan data baru muncul
-   [ ] Notifikasi sukses muncul

**Expected Result**:

-   Modal muncul dengan benar
-   Form validation berfungsi (required fields)
-   Data tersimpan ke database
-   Kode member auto-generate
-   Tabel update dengan data baru

---

### ✅ 8. View Detail Pelanggan

-   [ ] Klik tombol "Detail" pada salah satu row
-   [ ] Modal detail muncul
-   [ ] Menampilkan semua informasi pelanggan:
    -   Kode Member
    -   Nama
    -   Telepon
    -   Tipe Customer
    -   Alamat
    -   Outlet
    -   Total Piutang

**Expected Result**:

-   Modal detail muncul dengan benar
-   Semua data ditampilkan lengkap
-   Format Rupiah untuk piutang

---

### ✅ 9. Edit Pelanggan

-   [ ] Klik tombol "Edit" pada salah satu row
-   [ ] Modal edit muncul dengan data ter-isi
-   [ ] Ubah nama menjadi "Test Customer Updated"
-   [ ] Ubah telepon
-   [ ] Klik "Simpan"
-   [ ] Modal tertutup
-   [ ] Tabel reload dengan data terupdate
-   [ ] Notifikasi sukses muncul

**Expected Result**:

-   Modal edit muncul dengan data existing
-   Update berhasil tersimpan
-   Tabel menampilkan data terbaru

---

### ✅ 10. Hapus Pelanggan (Tanpa Transaksi)

-   [ ] Klik tombol "Hapus" pada pelanggan yang tidak punya transaksi
-   [ ] Konfirmasi dialog muncul
-   [ ] Klik "OK"
-   [ ] Data terhapus dari tabel
-   [ ] Notifikasi sukses muncul

**Expected Result**:

-   Konfirmasi dialog muncul
-   Data berhasil dihapus
-   Tabel update tanpa data yang dihapus

---

### ✅ 11. Hapus Pelanggan (Dengan Transaksi)

-   [ ] Klik tombol "Hapus" pada pelanggan yang punya transaksi (invoice/piutang)
-   [ ] Konfirmasi dialog muncul
-   [ ] Klik "OK"
-   [ ] Error message muncul: "Pelanggan tidak dapat dihapus karena memiliki transaksi"
-   [ ] Data tidak terhapus

**Expected Result**:

-   Validasi berfungsi
-   Error message jelas
-   Data tidak terhapus

---

### ✅ 12. Export Excel

-   [ ] Set filter outlet (opsional)
-   [ ] Set filter tipe (opsional)
-   [ ] Klik tombol "Excel"
-   [ ] File .xlsx ter-download
-   [ ] Buka file Excel
-   [ ] Check data sesuai dengan filter

**Expected Result**:

-   File Excel ter-download dengan nama: `pelanggan_YYYY-MM-DD_HHMMSS.xlsx`
-   Data di Excel sesuai dengan filter
-   Format rapi dengan header

---

### ✅ 13. Export PDF

-   [ ] Set filter outlet (opsional)
-   [ ] Set filter tipe (opsional)
-   [ ] Klik tombol "PDF"
-   [ ] File .pdf ter-download
-   [ ] Buka file PDF
-   [ ] Check data sesuai dengan filter

**Expected Result**:

-   File PDF ter-download dengan nama: `pelanggan_YYYY-MM-DD_HHMMSS.pdf`
-   Data di PDF sesuai dengan filter
-   Format landscape A4
-   Header dengan judul dan tanggal

---

### ✅ 14. Responsive Design

-   [ ] Buka halaman di mobile view (resize browser)
-   [ ] Check apakah layout responsive
-   [ ] Check apakah tabel scrollable horizontal
-   [ ] Check apakah modal responsive

**Expected Result**:

-   Layout menyesuaikan dengan ukuran layar
-   Tabel scrollable di mobile
-   Modal tidak overflow

---

### ✅ 15. Error Handling

-   [ ] Coba submit form dengan field kosong
-   [ ] Coba submit form dengan data invalid
-   [ ] Coba akses halaman tanpa login (jika ada auth)
-   [ ] Coba akses dengan koneksi internet lambat

**Expected Result**:

-   Validation error muncul dengan jelas
-   Form tidak submit jika ada error
-   Redirect ke login jika belum login
-   Loading state muncul saat proses

---

## 🔍 Database Verification

### Check Data di Database

```sql
-- Check pelanggan baru
SELECT * FROM member ORDER BY id_member DESC LIMIT 10;

-- Check kode member auto-generate
SELECT kode_member, nama, id_outlet FROM member ORDER BY id_member DESC LIMIT 5;

-- Check relasi dengan tipe
SELECT m.nama, t.nama_tipe
FROM member m
LEFT JOIN tipe t ON m.id_tipe = t.id_tipe
LIMIT 10;

-- Check relasi dengan outlet
SELECT m.nama, o.nama as outlet_nama
FROM member m
LEFT JOIN outlet o ON m.id_outlet = o.id
LIMIT 10;

-- Check total piutang
SELECT m.nama, COALESCE(SUM(p.piutang), 0) as total_piutang
FROM member m
LEFT JOIN piutang p ON m.id_member = p.id_member AND p.status = 'belum_lunas'
GROUP BY m.id_member, m.nama
LIMIT 10;
```

---

## 🐛 Common Issues & Solutions

### Issue 1: DataTables tidak muncul

**Solution**:

-   Check console browser untuk error JavaScript
-   Pastikan jQuery loaded sebelum DataTables
-   Clear browser cache

### Issue 2: Modal tidak muncul

**Solution**:

-   Check Alpine.js loaded dengan benar
-   Check console untuk error
-   Pastikan `x-show` directive berfungsi

### Issue 3: Export tidak berfungsi

**Solution**:

-   Check package Maatwebsite Excel terinstall: `composer require maatwebsite/excel`
-   Check package DomPDF terinstall: `composer require barryvdh/dompdf`
-   Check permission folder storage: `chmod -R 775 storage`

### Issue 4: Route not found

**Solution**:

-   Clear route cache: `php artisan route:clear`
-   Check route registered: `php artisan route:list --name=admin.crm`

### Issue 5: Data tidak muncul

**Solution**:

-   Check database connection
-   Check apakah ada data di tabel `member`
-   Check console network tab untuk error API

---

## ✅ Final Checklist

-   [ ] Semua fitur CRUD berfungsi
-   [ ] Filter dan search berfungsi
-   [ ] Export Excel dan PDF berfungsi
-   [ ] Statistik menampilkan data yang benar
-   [ ] Validasi berfungsi dengan baik
-   [ ] Error handling berfungsi
-   [ ] UI responsive di mobile
-   [ ] Tidak ada error di console browser
-   [ ] Tidak ada error di Laravel log
-   [ ] Performance baik (loading < 3 detik)

---

## 📊 Performance Benchmark

### Expected Performance

-   **Page Load**: < 2 detik
-   **DataTables Load**: < 1 detik (untuk 100 records)
-   **Search Response**: < 500ms
-   **Export Excel**: < 5 detik (untuk 1000 records)
-   **Export PDF**: < 10 detik (untuk 1000 records)

### Optimization Tips

-   Gunakan index pada foreign key
-   Limit data per page di DataTables
-   Cache statistik jika data besar
-   Gunakan queue untuk export data besar

---

## 📝 Notes

1. **Auto-Generate Kode Member**

    - Kode member di-generate per outlet
    - Format: 6 digit dengan leading zero (000001, 000002, dst)
    - Prefix ditambahkan berdasarkan closing type (J, D, JD)

2. **Total Piutang**

    - Dihitung dari tabel `piutang` dengan status `belum_lunas`
    - Real-time update dari modul piutang

3. **Validasi Hapus**

    - Pelanggan dengan transaksi tidak bisa dihapus
    - Check relasi dengan `sales_invoices`

4. **Export**
    - Export mengikuti filter yang aktif
    - Format Excel: .xlsx
    - Format PDF: landscape A4

---

**Happy Testing! 🚀**
