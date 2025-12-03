# Perbaikan Tampilan Akun Laporan Laba Rugi

## Masalah

Akun-akun tidak muncul di laporan laba rugi meskipun data sudah dikirim dari controller dengan benar.

## Analisis

1. Controller (`FinanceAccountantController::calculateAccountsAmount`) sudah mengirim data dengan struktur:

    ```php
    [
        'id' => $account->id,
        'code' => $account->code ?? '',
        'name' => $account->name ?? 'Unnamed Account',
        'amount' => abs($amount),
        'children' => $childrenData,
        'is_parent' => count($childrenData) > 0
    ]
    ```

2. Data `code` dan `name` sudah ada dan tidak null
3. Masalah ada di view - kondisi filtering yang terlalu ketat atau rendering yang tidak tepat

## Perbaikan yang Dilakukan

### 1. Perbaikan Rendering Akun

-   Menghapus kondisi `x-if` yang membatasi tampilan akun
-   Memastikan semua akun dengan amount > 0 atau memiliki children ditampilkan
-   Menambahkan fallback untuk code dan name yang lebih robust

### 2. Perbaikan Struktur Template

-   Menyederhanakan logika conditional rendering
-   Memastikan loop `x-for` berjalan dengan benar
-   Menambahkan debug logging untuk troubleshooting

### 3. Perbaikan Auto-Expand

-   Memastikan akun dengan children otomatis di-expand
-   Memperbaiki logika `expandedAccounts` array

## File yang Diubah

-   `resources/views/admin/finance/labarugi/index.blade.php`

## Testing

1. Buka halaman Laporan Laba Rugi
2. Pilih outlet dan periode
3. Verifikasi bahwa semua akun muncul dengan code dan name yang benar
4. Verifikasi bahwa akun dengan children bisa di-expand/collapse
5. Klik pada nama akun untuk melihat detail transaksi

## Catatan

-   Backup file asli disimpan di `index.blade.php.backup`
-   Jika masih ada masalah, periksa console browser untuk error JavaScript
-   Pastikan data dari API endpoint `/finance/profit-loss/data` mengembalikan struktur yang benar
