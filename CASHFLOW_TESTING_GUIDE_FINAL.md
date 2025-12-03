# Panduan Testing Laporan Arus Kas

## URL Akses

```
http://localhost/finance/cashflow
```

## Checklist Testing

### 1. **Load Halaman**

-   [ ] Halaman terbuka tanpa error
-   [ ] Filter outlet terisi otomatis
-   [ ] Tanggal default ke bulan ini
-   [ ] Loading indicator muncul saat fetch data

### 2. **Filter & Data Loading**

-   [ ] Pilih outlet berbeda → Data refresh
-   [ ] Pilih buku akuntansi → Data refresh
-   [ ] Ubah metode (Direct/Indirect) → Tampilan berubah
-   [ ] Ubah periode (Bulanan/Triwulan/Tahunan) → Tanggal update
-   [ ] Set custom date range → Data sesuai periode

### 3. **Tampilan Hierarchy**

-   [ ] Parent accounts tampil bold
-   [ ] Child accounts ter-indent 20px
-   [ ] Multi-level hierarchy ter-indent bertahap
-   [ ] Kode akun tampil dalam kurung
-   [ ] Subtotal parent = jumlah children

### 4. **Aktivitas Operasi**

-   [ ] Penerimaan kas dari pelanggan tampil
-   [ ] Pembayaran kas tampil
-   [ ] Hierarchy revenue accounts benar
-   [ ] Hierarchy expense accounts benar
-   [ ] Total kas bersih operasi benar

### 5. **Aktivitas Investasi**

-   [ ] Pembelian aset tetap tampil (jika ada)
-   [ ] Penjualan aset tetap tampil (jika ada)
-   [ ] Total kas bersih investasi benar

### 6. **Aktivitas Pendanaan**

-   [ ] Penerimaan pinjaman tampil (jika ada)
-   [ ] Pembayaran pinjaman tampil (jika ada)
-   [ ] Setoran modal tampil (jika ada)
-   [ ] Pembayaran dividen tampil (jika ada)
-   [ ] Total kas bersih pendanaan benar

### 7. **Summary Cards (Top)**

-   [ ] Arus Kas Bersih = Operasi + Investasi + Pendanaan
-   [ ] Kas dari Operasi sesuai dengan section A
-   [ ] Kas dari Investasi sesuai dengan section B
-   [ ] Kas dari Pendanaan sesuai dengan section C
-   [ ] Warna hijau untuk positif, merah untuk negatif

### 8. **Chart - Trend Arus Kas**

-   [ ] Line chart tampil
-   [ ] 3 lines: Operasi (hijau), Investasi (ungu), Pendanaan (orange)
-   [ ] Labels bulan tampil (6 bulan terakhir)
-   [ ] Data sesuai dengan transaksi real
-   [ ] Tooltip menampilkan nilai saat hover
-   [ ] Format currency dalam jutaan (Rp X.X Jt)

### 9. **Chart - Komposisi Arus Kas**

-   [ ] Doughnut chart tampil
-   [ ] 3 segments dengan warna berbeda
-   [ ] Tooltip menampilkan nilai dengan tanda +/-
-   [ ] Legend di bawah chart
-   [ ] Proporsi sesuai dengan nilai aktual

### 10. **Rasio Arus Kas**

-   [ ] Operating Cash Flow Ratio tampil dengan nilai
-   [ ] Cash Flow Margin tampil dalam persen
-   [ ] Free Cash Flow tampil dengan nilai
-   [ ] Indikator healthy/benchmark tampil
-   [ ] Warna sesuai (hijau, biru, ungu)

### 11. **Proyeksi Arus Kas**

-   [ ] 3 bulan ke depan tampil
-   [ ] Nama bulan dan quarter benar
-   [ ] Nilai proyeksi tampil
-   [ ] Trend persentase tampil
-   [ ] Warna hijau untuk positif, merah untuk negatif

### 12. **Kas Awal & Akhir Periode**

-   [ ] Kas awal periode tampil
-   [ ] Kas akhir periode = Kas awal + Arus kas bersih
-   [ ] Nilai sesuai dengan saldo akun kas/bank

### 13. **Tombol Action**

-   [ ] Print → Membuka print dialog
-   [ ] Export → Download PDF
-   [ ] Refresh → Reload data terbaru

### 14. **Responsive Design**

-   [ ] Tampilan mobile friendly
-   [ ] Chart responsive
-   [ ] Filter stack di mobile
-   [ ] Cards stack di mobile

### 15. **Error Handling**

-   [ ] Error message tampil jika gagal load data
-   [ ] Loading state tampil saat fetch
-   [ ] Validasi outlet wajib dipilih
-   [ ] Validasi tanggal mulai < tanggal akhir

## Test Cases dengan Data

### Case 1: Outlet dengan Transaksi Normal

```
Outlet: [Pilih outlet yang aktif]
Periode: Bulan ini
Expected:
- Ada data di semua aktivitas
- Chart menampilkan trend
- Rasio terhitung
```

### Case 2: Outlet Tanpa Transaksi

```
Outlet: [Pilih outlet baru/tidak aktif]
Periode: Bulan ini
Expected:
- Semua nilai = 0
- Chart kosong atau minimal
- Rasio = 0 atau N/A
```

### Case 3: Periode Custom

```
Outlet: [Pilih outlet aktif]
Periode: Custom (3 bulan lalu s/d sekarang)
Expected:
- Data sesuai periode yang dipilih
- Trend chart menampilkan 6 bulan terakhir
- Proyeksi dari data historis
```

### Case 4: Filter Buku Akuntansi

```
Outlet: [Pilih outlet aktif]
Buku: [Pilih buku tertentu]
Expected:
- Hanya transaksi dari buku tersebut
- Total berbeda dengan "Semua Buku"
```

## Validasi Perhitungan

### Manual Check:

1. Buka database
2. Query total debit-credit untuk revenue accounts
3. Bandingkan dengan "Penerimaan Kas dari Pelanggan"
4. Query total debit-credit untuk expense accounts
5. Bandingkan dengan "Pembayaran Kas"
6. Hitung: Penerimaan - Pembayaran = Kas Bersih Operasi
7. Verifikasi dengan tampilan di UI

### SQL Query untuk Validasi:

```sql
-- Revenue (Penerimaan Kas)
SELECT SUM(jed.credit - jed.debit) as total_revenue
FROM journal_entry_details jed
JOIN journal_entries je ON jed.journal_entry_id = je.id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.outlet_id = [OUTLET_ID]
  AND je.status = 'posted'
  AND je.transaction_date BETWEEN '[START_DATE]' AND '[END_DATE]'
  AND coa.type IN ('revenue', 'otherrevenue');

-- Expense (Pembayaran Kas)
SELECT SUM(jed.debit - jed.credit) as total_expense
FROM journal_entry_details jed
JOIN journal_entries je ON jed.journal_entry_id = je.id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.outlet_id = [OUTLET_ID]
  AND je.status = 'posted'
  AND je.transaction_date BETWEEN '[START_DATE]' AND '[END_DATE]'
  AND coa.type IN ('expense', 'otherexpense');
```

## Browser Testing

-   [ ] Chrome (latest)
-   [ ] Firefox (latest)
-   [ ] Edge (latest)
-   [ ] Safari (if available)
-   [ ] Mobile Chrome
-   [ ] Mobile Safari

## Performance Check

-   [ ] Load time < 3 detik
-   [ ] Chart render smooth
-   [ ] Filter change responsive
-   [ ] No memory leaks (check DevTools)
-   [ ] No console errors
-   [ ] No console warnings

## Accessibility

-   [ ] Keyboard navigation works
-   [ ] Screen reader friendly
-   [ ] Color contrast sufficient
-   [ ] Focus indicators visible

## Status Akhir

Setelah semua checklist di atas passed:
✅ **READY FOR PRODUCTION**

## Troubleshooting

### Chart tidak muncul

-   Check console untuk error Chart.js
-   Pastikan CDN Chart.js loaded
-   Clear browser cache

### Data tidak muncul

-   Check network tab untuk API response
-   Check Laravel log untuk backend error
-   Pastikan outlet memiliki transaksi

### Hierarchy tidak indent

-   Check CSS loading
-   Check Alpine.js loaded
-   Inspect element untuk style

### Rasio salah

-   Validasi dengan SQL query manual
-   Check formula di controller
-   Check data kewajiban lancar

## Contact

Jika menemukan bug atau issue:

1. Screenshot error
2. Check browser console
3. Check Laravel log
4. Catat langkah reproduksi
5. Report ke developer
