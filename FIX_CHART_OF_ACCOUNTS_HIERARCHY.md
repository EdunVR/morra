# 🔧 Fix: Chart of Accounts - Hierarki Akun Induk & Anak

## 🐛 Problem

Tampilan tabel daftar akun tidak menampilkan hierarki akun induk dan anak dengan jelas. Semua akun ditampilkan rata tanpa indentasi, sehingga sulit membedakan akun induk dan akun anak sesuai kaidah akuntansi.

## 📋 Kaidah Akuntansi untuk Hierarki

Dalam akuntansi, hierarki akun harus ditampilkan dengan:

1. **Akun Induk (Level 1)**: Ditampilkan normal, bold, tanpa indentasi
2. **Akun Anak (Level 2+)**: Ditampilkan dengan indentasi ke kanan dari akun induk
3. **Visual Hierarchy**: Menggunakan simbol atau spacing untuk menunjukkan parent-child relationship

### Contoh Hierarki yang Benar:

```
1000 - Aset                          Rp 100,000,000
  └─ 1100 - Kas                      Rp  50,000,000
  └─ 1200 - Bank                     Rp  50,000,000
2000 - Kewajiban                     Rp  30,000,000
  └─ 2100 - Utang Usaha              Rp  20,000,000
  └─ 2200 - Utang Pajak              Rp  10,000,000
```

## ✅ Solution

### Before (Broken)

```html
<td class="px-4 py-3">
    <div class="font-mono text-sm" x-text="account.code"></div>
</td>
<td class="px-4 py-3">
    <div
        :class="account.level > 1 ? 'pl-' + (account.level * 4) : ''"
        x-text="account.name"
    ></div>
</td>
<td class="px-4 py-3 text-right">
    <div x-text="formatCurrency(account.accumulated_balance)"></div>
</td>
```

**Problems:**

-   Dynamic class `'pl-' + (account.level * 4)` tidak bekerja dengan Tailwind CSS
-   Tidak ada visual indicator untuk hierarki
-   Kode dan saldo tidak ter-indent
-   Sulit membedakan akun induk dan anak

### After (Fixed)

```html
<!-- Kode Akun dengan Indentasi -->
<td class="px-4 py-3">
    <div class="font-mono text-sm flex items-center gap-1">
        <!-- Indentasi untuk akun anak -->
        <template x-if="account.level > 1">
            <span
                class="text-slate-300"
                x-text="'└─'.repeat(account.level - 1)"
            ></span>
        </template>
        <span
            x-text="account.code"
            :class="account.level > 1 ? 'text-slate-600' : 'font-semibold'"
        ></span>
    </div>
</td>

<!-- Nama Akun dengan Indentasi -->
<td class="px-4 py-3">
    <div class="flex items-center gap-1">
        <!-- Indentasi visual untuk hierarki -->
        <template x-if="account.level > 1">
            <span
                class="text-slate-300 text-xs"
                x-text="'└─'.repeat(account.level - 1)"
            ></span>
        </template>
        <span
            :class="account.level > 1 ? 'text-slate-600' : 'font-semibold text-slate-800'"
            x-text="account.name"
        ></span>
    </div>
</td>

<!-- Saldo dengan Indentasi -->
<td class="px-4 py-3">
    <div class="flex items-center justify-end gap-1">
        <!-- Indentasi untuk saldo akun anak -->
        <template x-if="account.level > 1">
            <span class="text-slate-300 text-xs mr-2">└─</span>
        </template>
        <div class="text-right">
            <div
                :class="[
             'font-semibold',
             account.level > 1 ? 'text-sm' : 'text-base',
             getBalanceColor(account.accumulated_balance, account.type)
           ]"
                x-text="formatCurrency(account.accumulated_balance)"
            ></div>
            <template x-if="account.children && account.children.length > 0">
                <div class="text-xs text-slate-500">
                    <span x-text="account.children.length"></span> akun anak
                </div>
            </template>
            <template x-if="!account.children || account.children.length === 0">
                <div class="text-xs text-slate-400 italic">Detail</div>
            </template>
        </div>
    </div>
</td>
```

## 🎨 Visual Improvements

### 1. Hierarki Symbol

-   Menggunakan `└─` untuk menunjukkan child relationship
-   Symbol di-repeat sesuai level: `'└─'.repeat(account.level - 1)`
-   Warna abu-abu muda (`text-slate-300`) agar tidak mengganggu

### 2. Indentasi Konsisten

-   **Kode Akun**: Symbol + kode ter-indent
-   **Nama Akun**: Symbol + nama ter-indent
-   **Saldo**: Symbol + saldo ter-indent ke kanan

### 3. Typography Hierarchy

-   **Akun Induk**:
    -   Font bold (`font-semibold`)
    -   Text size normal (`text-base`)
    -   Warna gelap (`text-slate-800`)
-   **Akun Anak**:
    -   Font normal
    -   Text size lebih kecil (`text-sm`)
    -   Warna lebih terang (`text-slate-600`)

### 4. Background Differentiation

```html
<tr
    :style="account.level > 1 ? 'background-color: rgba(248, 250, 252, 0.5)' : ''"
></tr>
```

Akun anak memiliki background sedikit lebih terang untuk visual separation.

## 📊 Display Examples

### Level 1 (Akun Induk)

```
┌─────────────┬──────────────────────┬─────────────────┐
│ Kode        │ Nama Akun            │ Saldo           │
├─────────────┼──────────────────────┼─────────────────┤
│ 1000        │ Aset                 │ Rp 100,000,000  │
│ (bold)      │ (bold, dark)         │ (large, bold)   │
└─────────────┴──────────────────────┴─────────────────┘
```

### Level 2 (Akun Anak)

```
┌─────────────┬──────────────────────┬─────────────────┐
│ Kode        │ Nama Akun            │ Saldo           │
├─────────────┼──────────────────────┼─────────────────┤
│ └─ 1100     │ └─ Kas               │ └─ Rp 50,000,000│
│ (normal)    │ (normal, lighter)    │ (small, normal) │
└─────────────┴──────────────────────┴─────────────────┘
```

### Level 3 (Sub-Akun Anak)

```
┌─────────────┬──────────────────────┬─────────────────┐
│ Kode        │ Nama Akun            │ Saldo           │
├─────────────┼──────────────────────┼─────────────────┤
│ └─└─ 1110   │ └─└─ Kas Kecil       │ └─ Rp 5,000,000 │
│ (normal)    │ (normal, lighter)    │ (small, normal) │
└─────────────┴──────────────────────┴─────────────────┘
```

## 🎯 Expected Result

### Complete Example

```
No  Kode          Nama Akun                    Tipe      Saldo              Status
1   1000          Aset                         Aset      Rp 100,000,000     Aktif
                                                         3 akun anak
2   └─ 1100       └─ Kas                       Aset      └─ Rp 50,000,000   Aktif
                                                            Detail
3   └─ 1200       └─ Bank                      Aset      └─ Rp 30,000,000   Aktif
                                                            Detail
4   └─ 1300       └─ Piutang                   Aset      └─ Rp 20,000,000   Aktif
                                                            Detail
5   2000          Kewajiban                    Liability Rp 30,000,000      Aktif
                                                         2 akun anak
6   └─ 2100       └─ Utang Usaha               Liability └─ Rp 20,000,000   Aktif
                                                            Detail
7   └─ 2200       └─ Utang Pajak               Liability └─ Rp 10,000,000   Aktif
                                                            Detail
```

## 🔑 Key Features

| Feature          | Implementation                               |
| ---------------- | -------------------------------------------- |
| Visual Hierarchy | `└─` symbol repeated by level                |
| Kode Indentasi   | Symbol before code for child accounts        |
| Nama Indentasi   | Symbol before name for child accounts        |
| Saldo Indentasi  | Symbol before balance for child accounts     |
| Font Weight      | Bold for parent, normal for child            |
| Font Size        | Larger for parent, smaller for child         |
| Text Color       | Darker for parent, lighter for child         |
| Background       | Subtle background for child rows             |
| Info Label       | "X akun anak" for parent, "Detail" for child |

## 🧪 Testing

### Test Case 1: Single Level Hierarchy

1. Buka halaman Chart of Accounts
2. Lihat akun dengan 1 level (parent only)
3. **Expected**:
    - Kode bold, no symbol
    - Nama bold, dark color
    - Saldo large, bold
    - Label "X akun anak" if has children

### Test Case 2: Two Level Hierarchy

1. Lihat akun parent dengan children
2. **Expected**:
    - Parent: Bold, no indent
    - Child: `└─` symbol, lighter color, indented
    - Saldo child: Smaller, with `└─` symbol

### Test Case 3: Three Level Hierarchy

1. Lihat akun dengan 3 levels (grandparent → parent → child)
2. **Expected**:
    - Level 1: No symbol
    - Level 2: `└─` (1x)
    - Level 3: `└─└─` (2x)

### Test Case 4: Filter by Type

1. Filter by "Aset"
2. **Expected**:
    - Hierarchy maintained
    - Only asset accounts shown
    - Indentation still correct

## 💡 Benefits

1. **Sesuai Kaidah Akuntansi**: Hierarki jelas seperti standar akuntansi
2. **Easy to Read**: Visual hierarchy memudahkan pemahaman struktur
3. **Professional Look**: Tampilan lebih profesional dan rapi
4. **Consistent Indentation**: Semua kolom (kode, nama, saldo) ter-indent
5. **Scalable**: Bisa handle multiple levels (2, 3, 4, dst)
6. **Responsive**: Tetap rapi di berbagai ukuran layar

## ✅ Status

**FIXED** ✅

Tabel Chart of Accounts sekarang menampilkan hierarki akun induk dan anak dengan indentasi yang sesuai kaidah akuntansi. Semua kolom (kode, nama akun, saldo) ter-indent dengan konsisten.

**Ready for testing!** 🚀
