# Fitur Modal Detail Transaksi - Laporan Laba Rugi

## ✅ Selesai!

Fitur modal untuk menampilkan detail transaksi per akun telah ditambahkan.

## Logika Klik

### 1. Parent Account (Induk Akun)

-   **Tidak punya child**: Bisa diklik ✅
-   **Punya child**: Tidak bisa diklik ❌

### 2. Child Account (Anak Akun)

-   **Selalu bisa diklik** ✅

## Fitur Modal

### Informasi yang Ditampilkan:

1. **Header Modal**

    - Kode akun
    - Nama akun

2. **Summary Cards**

    - Total Transaksi
    - Total Debit
    - Total Kredit
    - Saldo

3. **Tabel Transaksi**

    - Tanggal
    - No. Transaksi
    - Deskripsi
    - Buku
    - Debit
    - Kredit

4. **Footer Total**
    - Total Debit
    - Total Kredit

## Implementasi Teknis

### 1. Modal Component

```html
<div x-show="showAccountModal" ...>
    <!-- Modal content -->
</div>
```

### 2. Helper Function

```javascript
renderAccountRow(account, colorClass, isChild);
```

-   Menentukan apakah akun bisa diklik
-   Render button atau span sesuai kondisi
-   Styling berbeda untuk parent dan child

### 3. Click Handler

```javascript
showAccountTransactions(accountId, accountCode, accountName);
```

-   Fetch data dari API
-   Tampilkan loading state
-   Handle error
-   Populate modal dengan data

### 4. API Endpoint

```
GET /finance/profit-loss/account-details
Parameters:
- outlet_id
- account_id
- start_date
- end_date
```

## Visual Indicator

### Akun yang Bisa Diklik:

-   Warna: `text-blue-600`
-   Hover: `hover:text-blue-800 hover:underline`
-   Cursor: `cursor-pointer`

### Akun yang Tidak Bisa Diklik:

-   Warna: `text-slate-800`
-   No hover effect
-   Normal cursor

## Testing

1. **Buka halaman Laba Rugi**
2. **Klik child account** → Modal muncul ✅
3. **Klik parent tanpa child** → Modal muncul ✅
4. **Klik parent dengan child** → Tidak ada aksi ❌
5. **Lihat detail transaksi** → Data lengkap ✅
6. **Tutup modal** → Modal hilang ✅

## File yang Dimodifikasi

**resources/views/admin/finance/labarugi/index.blade.php**

-   Tambah modal component
-   Tambah state management
-   Tambah helper function renderAccountRow()
-   Tambah showAccountTransactions()
-   Tambah closeAccountModal()
-   Update semua section (revenue, other_revenue, expense, other_expense)

## Keuntungan

✅ **User-friendly** - Jelas mana yang bisa diklik
✅ **Konsisten** - Pola sama untuk semua section
✅ **Informative** - Detail transaksi lengkap
✅ **Responsive** - Modal responsive di semua ukuran layar
✅ **Clean code** - Helper function untuk reusability

## Selesai! 🎉

Fitur modal detail transaksi sudah siap digunakan!
