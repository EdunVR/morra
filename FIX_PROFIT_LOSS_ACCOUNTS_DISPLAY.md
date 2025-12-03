# Fix Laporan Laba Rugi - Account Display & Chart

## Masalah

1. Nama dan kode akun tidak muncul di laporan laba rugi
2. Hierarki akun tidak jelas (akun anak tidak menjorok)
3. Chart tidak muncul dengan baik

## Perbaikan yang Dilakukan

### 1. Backend - Controller (FinanceAccountantController.php)

✅ Diperbaiki method `calculateAccountsAmount()` untuk:

-   Memastikan `code` dan `name` selalu ada (dengan fallback)
-   Menambahkan flag `is_parent` dan `is_child` untuk hierarki
-   Menampilkan parent account meskipun amount = 0 jika punya children dengan amount

### 2. Frontend - View (resources/views/admin/finance/labarugi/index.blade.php)

Perlu diperbaiki di 4 tempat (Revenue, Other Revenue, Expense, Other Expense):

#### Perbaikan untuk Parent Account Row:

```blade
<tr class="border-t border-slate-100 hover:bg-slate-50">
  <td class="px-4 py-2 border-r border-slate-100">
    <span class="font-mono text-xs font-semibold text-slate-700" x-text="account.code || '-'"></span>
  </td>
  <td class="px-4 py-2 border-r border-slate-100">
    <div class="flex items-center gap-2">
      <button x-show="account.children && account.children.length > 0"
              @click="toggleAccountDetails(account.id)"
              class="text-slate-400 hover:text-slate-600">
        <i class='bx text-sm' :class="expandedAccounts.includes(account.id) ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
      </button>
      <button @click="showAccountTransactions(account)"
              class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1"
              :title="'Klik untuk melihat detail transaksi'">
        <span class="font-semibold text-slate-800" x-text="account.name || 'Unnamed Account'"></span>
        <i class='bx bx-info-circle text-xs opacity-50'></i>
      </button>
    </div>
  </td>
  <td class="px-4 py-2 text-right border-r border-slate-100">
    <span class="font-semibold text-green-600" x-text="formatCurrency(account.amount)"></span>
  </td>
  <!-- comparison columns... -->
</tr>
```

#### Perbaikan untuk Child Account Row (dengan indentasi):

```blade
<tr class="border-t border-slate-50 bg-slate-25 hover:bg-slate-50">
  <td class="px-4 py-2 pl-12 border-r border-slate-100">
    <span class="font-mono text-xs text-slate-500" x-text="child.code || '-'"></span>
  </td>
  <td class="px-4 py-2 pl-8 border-r border-slate-100">
    <button @click="showAccountTransactions(child)"
            class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1 text-sm"
            :title="'Klik untuk melihat detail transaksi'">
      <span class="text-slate-600" x-text="child.name || 'Unnamed Account'"></span>
      <i class='bx bx-info-circle text-xs opacity-50'></i>
    </button>
  </td>
  <td class="px-4 py-2 text-right border-r border-slate-100">
    <span class="text-sm text-green-600" x-text="formatCurrency(child.amount)"></span>
  </td>
  <!-- comparison columns... -->
</tr>
```

### 3. Chart Fixes

Chart sudah menggunakan `acc.name` dengan benar. Jika chart tidak muncul, kemungkinan:

1. **Chart.js tidak ter-load**: Pastikan ada di layout

```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
```

2. **Canvas tidak visible**: Pastikan `chartsLoaded` di-set true setelah data dimuat

3. **Data kosong**: Chart akan menampilkan "Tidak ada data" jika semua account amount = 0

## Testing

1. Buka halaman Laporan Laba Rugi
2. Pilih outlet dan periode
3. Klik "Tampilkan Laporan"
4. Verifikasi:
    - ✅ Kode akun muncul di kolom pertama
    - ✅ Nama akun muncul di kolom kedua
    - ✅ Akun anak menjorok (indentasi lebih dalam)
    - ✅ Chart pie untuk revenue muncul
    - ✅ Chart pie untuk expense muncul
    - ✅ Chart bar untuk comparison muncul (jika mode comparison aktif)
    - ✅ Chart line untuk trend muncul

## Catatan Penting

-   Fallback `|| '-'` dan `|| 'Unnamed Account'` memastikan selalu ada tampilan meskipun data kosong
-   `pl-12` untuk kode dan `pl-8` untuk nama memberikan indentasi visual yang jelas
-   `font-semibold` untuk parent account membedakan dari child account
-   Background `bg-slate-25` untuk child row membedakan dari parent

## Next Steps

Jika masih ada masalah:

1. Cek console browser untuk error JavaScript
2. Cek network tab untuk melihat response API `/finance/profit-loss/data`
3. Pastikan ada data transaksi di database untuk periode yang dipilih
4. Pastikan Chart.js library ter-load dengan benar
