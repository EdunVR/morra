# Cash Flow Clickable Items - Testing Guide

## Quick Test Steps

### 1. Buka Halaman Laporan Arus Kas

```
URL: http://localhost/finance/cashflow
atau sesuai dengan route Anda
```

### 2. Pilih Filter

-   Pilih **Outlet**
-   Pilih **Periode** (misalnya: Bulan Ini)
-   Pilih **Metode**: Direct atau Indirect

### 3. Test Direct Method

#### A. Operating Activities

Cari section "A. Arus Kas dari Aktivitas Operasi"

**Yang harus terlihat:**

-   ✅ "Penerimaan Kas dari Pelanggan" (header, tidak clickable)
-   ✅ Di bawahnya ada child items (nama akun revenue) - **HARUS BERWARNA BIRU**
-   ✅ "Pembayaran Kas kepada Pemasok dan Karyawan" (header, tidak clickable)
-   ✅ Di bawahnya ada child items (nama akun expense) - **HARUS BERWARNA BIRU**

**Test:**

1. Hover mouse ke item berwarna biru → harus ada underline
2. Klik item tersebut → modal harus muncul
3. Modal menampilkan:
    - Kode dan nama akun
    - Summary cards (Total Transaksi, Debit, Kredit, Arus Kas Bersih)
    - Tabel transaksi detail

#### B. Investing Activities

Cari section "B. Arus Kas dari Aktivitas Investasi"

**Yang harus terlihat:**

-   ✅ Items seperti "Pembelian Aset Tetap", "Penjualan Aset Tetap"
-   ✅ Jika ada akun aset tetap, items harus **BERWARNA BIRU**

**Test:**

1. Klik item berwarna biru
2. Modal muncul dengan detail transaksi

#### C. Financing Activities

Cari section "C. Arus Kas dari Aktivitas Pendanaan"

**Yang harus terlihat:**

-   ✅ Items seperti "Penerimaan Pinjaman", "Pembayaran Pinjaman", "Setoran Modal"
-   ✅ Jika ada akun liability/equity, items harus **BERWARNA BIRU**

**Test:**

1. Klik item berwarna biru
2. Modal muncul dengan detail transaksi

### 4. Test Indirect Method

Ubah **Metode** menjadi "Tidak Langsung (Indirect)"

#### A. Operating Activities - Adjustments

Cari section "Penyesuaian untuk merekonsiliasi laba bersih..."

**Yang harus terlihat:**

-   ✅ "Penyusutan" - **HARUS BERWARNA BIRU** (jika ada akun penyusutan)
-   ✅ "Perubahan Piutang Usaha" - **HARUS BERWARNA BIRU** (jika ada akun piutang)
-   ✅ "Perubahan Persediaan" - **HARUS BERWARNA BIRU** (jika ada akun persediaan)
-   ✅ "Perubahan Hutang Usaha" - **HARUS BERWARNA BIRU** (jika ada akun hutang)

**Test:**

1. Klik "Penyusutan" → modal muncul dengan transaksi beban penyusutan
2. Klik "Perubahan Piutang Usaha" → modal muncul dengan transaksi piutang
3. Klik "Perubahan Persediaan" → modal muncul dengan transaksi persediaan
4. Klik "Perubahan Hutang Usaha" → modal muncul dengan transaksi hutang

#### B. Investing & Financing

Sama seperti Direct Method

### 5. Test Modal Functionality

Ketika modal terbuka:

**Summary Cards:**

-   ✅ Total Transaksi: menampilkan jumlah transaksi
-   ✅ Total Debit: menampilkan total debit dalam format Rupiah
-   ✅ Total Kredit: menampilkan total kredit dalam format Rupiah
-   ✅ Arus Kas Bersih: menampilkan selisih (Kredit - Debit)

**Tabel Transaksi:**

-   ✅ Kolom: Tanggal, No. Transaksi, Deskripsi, Buku, Debit, Kredit
-   ✅ Data transaksi ditampilkan dengan benar
-   ✅ Format tanggal: DD/MM/YYYY
-   ✅ Format currency: Rp xxx.xxx
-   ✅ Footer menampilkan total

**Interaksi:**

-   ✅ Klik "Tutup" → modal tertutup
-   ✅ Klik di luar modal (overlay) → modal tertutup
-   ✅ Scroll jika transaksi banyak

### 6. Test Edge Cases

#### A. Item Tanpa Account ID

Jika ada item yang tidak memiliki account_id:

-   ✅ Item tetap ditampilkan
-   ✅ Item TIDAK berwarna biru (warna normal/hitam)
-   ✅ Item TIDAK clickable
-   ✅ Tidak ada hover effect

#### B. Tidak Ada Transaksi

Jika klik item tapi tidak ada transaksi:

-   ✅ Modal tetap muncul
-   ✅ Summary cards menampilkan 0
-   ✅ Tabel menampilkan pesan "Tidak ada transaksi untuk akun ini dalam periode yang dipilih"

#### C. Error Handling

Jika terjadi error saat load data:

-   ✅ Modal menampilkan error message
-   ✅ Icon error ditampilkan
-   ✅ User bisa close modal

### 7. Test Filter Integration

**Test dengan berbagai filter:**

1. Ubah **Outlet** → items harus update
2. Ubah **Tanggal** → items harus update
3. Pilih **Buku Akuntansi** tertentu → items harus filter sesuai buku
4. Klik item → modal harus menampilkan data sesuai filter

### 8. Browser Console Check

Buka Developer Tools (F12) → Console

**Yang TIDAK boleh ada:**

-   ❌ Error JavaScript
-   ❌ Error "Cannot read properties of undefined"
-   ❌ Error "account_id is not defined"
-   ❌ 404 errors untuk route

**Yang boleh ada:**

-   ✅ Log info (jika ada)
-   ✅ Network requests yang sukses (200 OK)

## Expected Behavior Summary

### Items yang HARUS Clickable (Berwarna Biru):

1. **Direct Method - Operating:**

    - Child items dari revenue accounts
    - Child items dari expense accounts

2. **Direct Method - Investing:**

    - Items yang memiliki account_id (akun aset tetap/investasi)

3. **Direct Method - Financing:**

    - Items yang memiliki account_id (akun liability/equity)

4. **Indirect Method - Adjustments:**

    - Penyusutan (jika ada akun beban penyusutan)
    - Perubahan Piutang (jika ada akun piutang)
    - Perubahan Persediaan (jika ada akun persediaan)
    - Perubahan Hutang (jika ada akun hutang)

5. **Indirect Method - Investing & Financing:**
    - Sama seperti Direct Method

### Items yang TIDAK Clickable:

-   Header items (yang memiliki children)
-   Items tanpa account_id
-   Generic items dari fallback mechanism

## Troubleshooting

### Problem: Items tidak berwarna biru

**Possible Causes:**

1. Item tidak memiliki `account_id`
2. CSS tidak loaded
3. Alpine.js condition `x-show="item.account_id"` tidak terpenuhi

**Solution:**

-   Check browser console untuk errors
-   Inspect element untuk melihat apakah button element ada
-   Check response data dari API apakah include `account_id`

### Problem: Modal tidak muncul

**Possible Causes:**

1. JavaScript error
2. Route tidak ditemukan
3. `window.cashFlowApp` tidak terdefinisi

**Solution:**

-   Check console untuk errors
-   Verify route `finance.cashflow.account-details` exists
-   Check Alpine.js initialization

### Problem: Modal muncul tapi data kosong

**Possible Causes:**

1. Tidak ada transaksi untuk akun tersebut
2. Filter tidak sesuai
3. Backend error

**Solution:**

-   Check network tab untuk response
-   Verify account_id yang dikirim
-   Check backend logs

## Success Criteria

✅ **PASS** jika:

-   Semua items dengan account_id berwarna biru dan clickable
-   Modal muncul dengan data yang benar
-   Summary cards akurat
-   Tabel transaksi lengkap
-   Filter berfungsi dengan baik
-   Tidak ada error di console

❌ **FAIL** jika:

-   Items tidak clickable
-   Modal tidak muncul
-   Data tidak sesuai
-   Ada error di console
-   Filter tidak berfungsi

---

**Happy Testing!** 🚀
