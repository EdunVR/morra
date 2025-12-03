# 🧪 Export Testing Checklist

## Finance Modules - PDF & Excel Export Testing

Gunakan checklist ini untuk memastikan semua export berfungsi dengan baik.

---

## 1. Jurnal (Journal Entries)

### PDF Export

**URL:** `/admin/finance/jurnal` → Klik "Export PDF"

-   [ ] PDF terbuka di tab baru (stream mode)
-   [ ] Header menampilkan nama perusahaan
-   [ ] Filter info muncul (tanggal, status, buku)
-   [ ] Tabel dengan kolom: No, Tanggal, No. Transaksi, Kode Akun, Nama Akun, Deskripsi, Debit, Kredit, Status
-   [ ] Zebra striping (baris genap abu-abu)
-   [ ] Status badge dengan warna (Draft/Posted/Void)
-   [ ] Total debit dan kredit di footer tabel
-   [ ] Summary box dengan total dan selisih
-   [ ] Footer dengan tanggal cetak
-   [ ] Margin 10mm dari semua sisi
-   [ ] Print preview (Ctrl+P) bagus

### Excel Export

**URL:** `/admin/finance/jurnal` → Klik "Export Excel"

-   [ ] File .xlsx ter-download
-   [ ] Bisa dibuka di Excel/LibreOffice
-   [ ] Header bold dengan background ungu (#4F46E5)
-   [ ] Semua kolom terlihat penuh (auto-width)
-   [ ] Border tipis di semua cells
-   [ ] Angka debit/kredit format ribuan (1.234,56)
-   [ ] Status dalam bahasa Indonesia
-   [ ] Tanggal format dd/mm/yyyy
-   [ ] Tidak ada data terpotong

---

## 2. Buku Akuntansi (Accounting Books)

### PDF Export

**URL:** `/admin/finance/buku` → Klik "Export PDF"

-   [ ] PDF stream di browser
-   [ ] Header dengan info perusahaan lengkap
-   [ ] Filter info section
-   [ ] Tabel dengan kolom: Kode, Nama Buku, Tipe, Mata Uang, Periode, Saldo Awal, Saldo Akhir, Entri, Status
-   [ ] Status badge dengan warna
-   [ ] Summary section dengan ringkasan
-   [ ] Footer dengan tanggal cetak
-   [ ] Margin konsisten
-   [ ] Print preview bagus

### Excel Export

**URL:** `/admin/finance/buku` → Klik "Export Excel"

-   [ ] File ter-download
-   [ ] Header bold dengan background biru (#4472C4)
-   [ ] Auto-width columns
-   [ ] Border di semua cells
-   [ ] Saldo format ribuan
-   [ ] Tipe dan status dalam bahasa Indonesia
-   [ ] Tanggal format dd/mm/yyyy

---

## 3. Aktiva Tetap (Fixed Assets)

### PDF Export

**URL:** `/admin/finance/aktiva-tetap` → Klik "Export PDF"

-   [ ] PDF stream di browser
-   [ ] Header dengan nama perusahaan
-   [ ] Filter info (status, kategori)
-   [ ] Grouping by category (jika diaktifkan)
-   [ ] Category header dengan background hijau
-   [ ] Tabel dengan kolom lengkap (13 kolom)
-   [ ] Status badge dengan warna
-   [ ] Subtotal per kategori
-   [ ] Summary box dengan total nilai buku
-   [ ] Tingkat penyusutan dalam %
-   [ ] Footer dengan tanggal
-   [ ] Font size 9pt (readable)
-   [ ] Margin konsisten

### Excel Export

**URL:** `/admin/finance/aktiva-tetap` → Klik "Export Excel"

-   [ ] File ter-download
-   [ ] Header bold dengan background hijau (#10B981)
-   [ ] 13 kolom lengkap
-   [ ] Auto-width columns
-   [ ] Border di semua cells
-   [ ] Nilai finansial format ribuan
-   [ ] Kategori dalam bahasa Indonesia
-   [ ] Metode penyusutan dalam bahasa Indonesia
-   [ ] Status dalam bahasa Indonesia

---

## 4. Buku Besar (General Ledger)

### PDF Export

**URL:** `/admin/finance/buku-besar` → Klik "Export PDF"

-   [ ] PDF stream di browser
-   [ ] Header dengan nama perusahaan
-   [ ] Filter info (periode, akun)
-   [ ] Account header untuk setiap akun
-   [ ] Opening balance row dengan highlight biru
-   [ ] Transaksi dengan running balance
-   [ ] Color coding: debit (hijau), kredit (merah), saldo (biru)
-   [ ] Account total dengan border tebal
-   [ ] Grand total dengan border ganda
-   [ ] Summary box dengan total debit/kredit
-   [ ] Footer dengan tanggal
-   [ ] Font size 9pt
-   [ ] Margin konsisten

### Excel Export

**URL:** `/admin/finance/buku-besar` → Klik "Export Excel"

-   [ ] File ter-download
-   [ ] Header bold dengan background ungu
-   [ ] Flattened structure (opening balance, transactions, totals)
-   [ ] Auto-width columns
-   [ ] Border di semua cells
-   [ ] Opening balance rows dengan highlight
-   [ ] Account total rows dengan border medium
-   [ ] Grand total dengan border thick
-   [ ] Spacer rows antar akun
-   [ ] Number format ribuan

---

## 5. Laporan Laba Rugi (Profit & Loss)

### PDF Export

**URL:** `/admin/finance/labarugi` → Klik "Export PDF"

-   [ ] PDF stream di browser
-   [ ] Header dengan nama perusahaan dan periode
-   [ ] Section headers (PENDAPATAN, BEBAN, dll)
-   [ ] Hierarchical display dengan indentasi
-   [ ] Total rows dengan border
-   [ ] Grand total dengan background color
-   [ ] Laba/Rugi bersih dengan warna (hijau/merah)
-   [ ] Financial ratios section
-   [ ] Support comparison mode (jika diaktifkan)
-   [ ] Footer dengan tanggal
-   [ ] Margin konsisten
-   [ ] Print preview bagus

### Excel Export

**URL:** `/admin/finance/labarugi` → Klik "Export Excel"

-   [ ] File ter-download
-   [ ] Header information di baris atas
-   [ ] Section headers dengan background abu-abu
-   [ ] Hierarchical structure dengan indentasi
-   [ ] Total rows dengan border ganda
-   [ ] Financial ratios section
-   [ ] Auto-width columns
-   [ ] Number format ribuan
-   [ ] Support comparison mode

---

## General Testing (Semua Modul)

### PDF General:

-   [ ] **Stream Mode:** PDF terbuka di browser, bukan langsung download
-   [ ] **Margin:** 10mm dari semua sisi (cek dengan print preview)
-   [ ] **Font Size:** 9-10pt, readable
-   [ ] **Color Coding:** Konsisten (hijau=debit, merah=kredit)
-   [ ] **Header:** Jelas dengan border bawah
-   [ ] **Filter Info:** Muncul jika ada filter
-   [ ] **Table:** Border jelas, zebra striping
-   [ ] **Summary:** Ada di akhir dengan total
-   [ ] **Footer:** Tanggal cetak dan info halaman
-   [ ] **Print:** Ctrl+P → Preview bagus, fit to page

### Excel General:

-   [ ] **Download:** File .xlsx ter-download
-   [ ] **Open:** Bisa dibuka di Excel dan LibreOffice
-   [ ] **Auto-Width:** Semua kolom terlihat penuh
-   [ ] **Header:** Bold, colored background, centered
-   [ ] **Border:** Thin borders di semua cells
-   [ ] **Number Format:** Ribuan dengan koma (1.234,56)
-   [ ] **Date Format:** dd/mm/yyyy
-   [ ] **Localization:** Semua label dalam bahasa Indonesia
-   [ ] **No Truncation:** Tidak ada data terpotong

---

## Filter Testing

### Test dengan berbagai filter:

#### No Filter:

-   [ ] PDF: Semua data muncul
-   [ ] Excel: Semua data muncul
-   [ ] Filter info: "Semua data" atau tidak ada filter info

#### Filter by Date:

-   [ ] PDF: Filter info menampilkan periode
-   [ ] Excel: Data sesuai periode
-   [ ] Data filtered correctly

#### Filter by Status:

-   [ ] PDF: Filter info menampilkan status
-   [ ] Excel: Data sesuai status
-   [ ] Data filtered correctly

#### Multiple Filters:

-   [ ] PDF: Semua filter muncul di filter info
-   [ ] Excel: Data sesuai semua filter
-   [ ] Data filtered correctly

---

## Performance Testing

### Small Data (< 100 rows):

-   [ ] PDF: Generate < 2 detik
-   [ ] Excel: Generate < 1 detik
-   [ ] File size reasonable

### Medium Data (100-1000 rows):

-   [ ] PDF: Generate < 5 detik
-   [ ] Excel: Generate < 3 detik
-   [ ] File size reasonable
-   [ ] No memory issues

### Large Data (> 1000 rows):

-   [ ] PDF: Generate < 10 detik atau show warning
-   [ ] Excel: Generate < 5 detik
-   [ ] File size reasonable
-   [ ] Consider pagination for PDF
-   [ ] Consider queue for very large exports

---

## Browser Compatibility

### PDF Stream:

-   [ ] Chrome: Stream works
-   [ ] Firefox: Stream works
-   [ ] Edge: Stream works
-   [ ] Safari: Stream works (if applicable)

### Excel Download:

-   [ ] Chrome: Download works
-   [ ] Firefox: Download works
-   [ ] Edge: Download works
-   [ ] Safari: Download works (if applicable)

---

## Mobile Testing (Optional)

### PDF on Mobile:

-   [ ] Android Chrome: Stream works
-   [ ] iOS Safari: Stream works
-   [ ] Readable on mobile screen
-   [ ] Can download from browser

### Excel on Mobile:

-   [ ] Android: Download works
-   [ ] iOS: Download works
-   [ ] Can open in Excel mobile app
-   [ ] Can open in Google Sheets

---

## Error Handling

### No Data:

-   [ ] PDF: Shows "Tidak ada data" message
-   [ ] Excel: Shows empty sheet with headers
-   [ ] No errors thrown

### Invalid Filter:

-   [ ] PDF: Shows all data or error message
-   [ ] Excel: Shows all data or error message
-   [ ] No fatal errors

### Permission Issues:

-   [ ] PDF: Shows 403 or redirects to login
-   [ ] Excel: Shows 403 or redirects to login
-   [ ] Proper error handling

---

## Regression Testing

### After Code Changes:

-   [ ] All PDF exports still work
-   [ ] All Excel exports still work
-   [ ] No new errors in logs
-   [ ] Performance not degraded
-   [ ] Styling still consistent

---

## User Acceptance Testing

### User Feedback:

-   [ ] PDF preview is helpful
-   [ ] Margin pas untuk print
-   [ ] Excel tidak perlu resize kolom
-   [ ] Styling profesional
-   [ ] Loading time acceptable
-   [ ] Easy to use
-   [ ] Meets requirements

---

## Sign-Off

### Developer:

-   [ ] All code changes committed
-   [ ] Documentation updated
-   [ ] Tests passed
-   [ ] No diagnostics errors

**Developer:** ******\_\_\_\_******
**Date:** ******\_\_\_\_******

### QA:

-   [ ] All test cases passed
-   [ ] No critical bugs
-   [ ] Performance acceptable
-   [ ] Ready for production

**QA:** ******\_\_\_\_******
**Date:** ******\_\_\_\_******

### Product Owner:

-   [ ] Meets requirements
-   [ ] User feedback positive
-   [ ] Approved for production

**PO:** ******\_\_\_\_******
**Date:** ******\_\_\_\_******

---

## Notes

### Issues Found:

```
[List any issues found during testing]
```

### Improvements Suggested:

```
[List any improvements suggested]
```

### Follow-up Actions:

```
[List any follow-up actions needed]
```

---

**Testing Date:** ******\_\_\_\_******
**Tester:** ******\_\_\_\_******
**Environment:** ******\_\_\_\_******
**Status:** [ ] PASS [ ] FAIL [ ] PENDING

---

**Last Updated:** 22 November 2025
