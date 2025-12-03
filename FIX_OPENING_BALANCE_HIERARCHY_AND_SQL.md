# 🔧 Fix: Saldo Awal - Hierarki & SQL Error

## 🐛 Problems

### 1. SQL Error: `period_year` doesn't have a default value

```
[2025-11-20 02:39:13] local.ERROR: Error storing opening balance:
SQLSTATE[HY000]: General error: 1364 Field 'period_year' doesn't have a default value
```

**Root Cause**: Method `storeOpeningBalance()` tidak mengisi field `period_year` dan `period_month` yang required di database.

### 2. Tampilan Hierarki Tidak Sesuai Kaidah Akuntansi

Tabel saldo awal tidak menampilkan hierarki akun induk dan anak dengan indentasi yang jelas.

## ✅ Solutions

### 1. Fix SQL Error - Add `period_year` and `period_month`

#### A. Fix `storeOpeningBalance()` Method

**File**: `app/Http/Controllers/FinanceAccountantController.php`

**Before:**

```php
$openingBalance = new OpeningBalance();
$openingBalance->outlet_id = $request->outlet_id;
$openingBalance->book_id = $request->book_id;
$openingBalance->account_id = $request->account_id;
$openingBalance->debit = $request->debit ?? 0;
$openingBalance->credit = $request->credit ?? 0;
$openingBalance->effective_date = $request->effective_date;
$openingBalance->description = $request->description;
$openingBalance->status = 'active';
$openingBalance->created_by = auth()->id();
$openingBalance->save();
```

**After:**

```php
// ✅ Extract year and month from effective_date
$effectiveDate = \Carbon\Carbon::parse($request->effective_date);

$openingBalance = new OpeningBalance();
$openingBalance->outlet_id = $request->outlet_id;
$openingBalance->book_id = $request->book_id;
$openingBalance->account_id = $request->account_id;
$openingBalance->period_year = $effectiveDate->year; // ✅ Added
$openingBalance->period_month = $effectiveDate->month; // ✅ Added
$openingBalance->debit = $request->debit ?? 0;
$openingBalance->credit = $request->credit ?? 0;
$openingBalance->balance = ($request->debit ?? 0) - ($request->credit ?? 0); // ✅ Added
$openingBalance->effective_date = $request->effective_date;
$openingBalance->description = $request->description;
$openingBalance->status = 'active';
$openingBalance->created_by = auth()->id();
$openingBalance->save();
```

#### B. Fix `updateOpeningBalance()` Method

**Before:**

```php
$openingBalance->debit = $request->debit ?? 0;
$openingBalance->credit = $request->credit ?? 0;
$openingBalance->effective_date = $request->effective_date;
$openingBalance->description = $request->description;
$openingBalance->save();
```

**After:**

```php
// ✅ Extract year and month from effective_date if changed
$effectiveDate = \Carbon\Carbon::parse($request->effective_date);

$openingBalance->period_year = $effectiveDate->year; // ✅ Added
$openingBalance->period_month = $effectiveDate->month; // ✅ Added
$openingBalance->debit = $request->debit ?? 0;
$openingBalance->credit = $request->credit ?? 0;
$openingBalance->balance = ($request->debit ?? 0) - ($request->credit ?? 0); // ✅ Added
$openingBalance->effective_date = $request->effective_date;
$openingBalance->description = $request->description;
$openingBalance->save();
```

### 2. Fix Hierarki Tampilan - Add Indentation

**File**: `resources/views/admin/finance/saldo-awal/index.blade.php`

#### A. Kode Akun dengan Indentasi

**Before:**

```html
<td class="px-4 py-3">
    <div class="font-mono text-sm" x-text="item.account.code"></div>
</td>
```

**After:**

```html
<td class="px-4 py-3">
    <div class="font-mono text-sm flex items-center gap-1">
        <!-- Indentasi untuk akun anak -->
        <template x-if="item.account.level > 1">
            <span
                class="text-slate-300"
                x-text="'└─'.repeat(item.account.level - 1)"
            ></span>
        </template>
        <span
            x-text="item.account.code"
            :class="item.account.level > 1 ? 'text-slate-600' : 'font-semibold'"
        ></span>
    </div>
</td>
```

#### B. Nama Akun dengan Indentasi

**Before:**

```html
<td class="px-4 py-3">
    <div class="font-medium text-slate-800" x-text="item.account.name"></div>
    <div class="text-xs text-slate-500" x-text="item.description"></div>
</td>
```

**After:**

```html
<td class="px-4 py-3">
    <div class="flex items-center gap-1">
        <!-- Indentasi visual untuk hierarki -->
        <template x-if="item.account.level > 1">
            <span
                class="text-slate-300 text-xs"
                x-text="'└─'.repeat(item.account.level - 1)"
            ></span>
        </template>
        <div>
            <div
                :class="item.account.level > 1 ? 'text-slate-600' : 'font-semibold text-slate-800'"
                x-text="item.account.name"
            ></div>
            <div class="text-xs text-slate-500" x-text="item.description"></div>
        </div>
    </div>
</td>
```

#### C. Debit, Kredit, Saldo dengan Indentasi

**Before:**

```html
<td class="px-4 py-3 text-right">
    <div
        class="font-semibold"
        :class="item.debit > 0 ? 'text-green-600' : 'text-slate-400'"
        x-text="formatCurrency(item.debit)"
    ></div>
</td>
```

**After:**

```html
<td class="px-4 py-3">
    <div class="flex items-center justify-end gap-1">
        <template x-if="item.account.level > 1">
            <span class="text-slate-300 text-xs mr-2">└─</span>
        </template>
        <div
            :class="[
           'font-semibold text-right',
           item.account.level > 1 ? 'text-sm' : 'text-base',
           item.debit > 0 ? 'text-green-600' : 'text-slate-400'
         ]"
            x-text="formatCurrency(item.debit)"
        ></div>
    </div>
</td>
```

#### D. Background Differentiation

```html
<tr
    :style="item.account.level > 1 ? 'background-color: rgba(248, 250, 252, 0.5)' : ''"
></tr>
```

## 📊 Database Schema

### opening_balances Table

```sql
CREATE TABLE opening_balances (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  outlet_id BIGINT NOT NULL,
  book_id BIGINT,
  account_id BIGINT NOT NULL,
  period_year INT NOT NULL,      -- ✅ Required field
  period_month INT NOT NULL,     -- ✅ Required field
  debit DECIMAL(15,2) DEFAULT 0,
  credit DECIMAL(15,2) DEFAULT 0,
  balance DECIMAL(15,2) DEFAULT 0,
  effective_date DATE,
  description VARCHAR(500),
  status VARCHAR(20),
  is_posted BOOLEAN DEFAULT 0,
  posted_at TIMESTAMP NULL,
  created_by BIGINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

## 🎯 Expected Results

### 1. SQL Error Fixed

**Before:**

```
❌ Error: Field 'period_year' doesn't have a default value
❌ Insert fails
```

**After:**

```
✅ period_year extracted from effective_date (e.g., 2025)
✅ period_month extracted from effective_date (e.g., 11)
✅ balance calculated automatically (debit - credit)
✅ Insert succeeds
```

### 2. Hierarki Display Fixed

**Before:**

```
No  Kode    Nama Akun           Debit           Kredit          Saldo
1   1000    Aset                Rp 100,000,000  Rp 0            Rp 100,000,000
2   1100    Kas                 Rp  50,000,000  Rp 0            Rp  50,000,000
3   1200    Bank                Rp  30,000,000  Rp 0            Rp  30,000,000
```

❌ Tidak jelas hierarki

**After:**

```
No  Kode          Nama Akun                 Debit                 Kredit              Saldo
1   1000          Aset                      Rp 100,000,000        Rp 0                Rp 100,000,000
2   └─ 1100       └─ Kas                    └─ Rp 50,000,000      └─ Rp 0             └─ Rp 50,000,000
3   └─ 1200       └─ Bank                   └─ Rp 30,000,000      └─ Rp 0             └─ Rp 30,000,000
```

✅ Hierarki jelas!

## 🔑 Key Changes

| Change                                       | Purpose                |
| -------------------------------------------- | ---------------------- |
| Extract `period_year` from `effective_date`  | Fix SQL error          |
| Extract `period_month` from `effective_date` | Fix SQL error          |
| Calculate `balance` automatically            | Data consistency       |
| Add `└─` symbol for child accounts           | Visual hierarchy       |
| Indent kode, nama, debit, kredit, saldo      | Consistent indentation |
| Different font size for parent/child         | Typography hierarchy   |
| Subtle background for child rows             | Visual separation      |

## 🧪 Testing

### Test Case 1: Create Opening Balance

1. Buka halaman Saldo Awal
2. Klik "Tambah Saldo"
3. Isi form dengan:
    - Outlet: Pilih outlet
    - Buku: Pilih buku akuntansi
    - Akun: Pilih akun
    - Debit: 100000
    - Tanggal Efektif: 2025-11-20
4. Klik "Simpan"
5. **Expected**:
    - ✅ Data tersimpan tanpa error
    - ✅ `period_year` = 2025
    - ✅ `period_month` = 11
    - ✅ `balance` = 100000

### Test Case 2: View Hierarchy

1. Lihat tabel saldo awal
2. **Expected**:
    - Akun induk: Bold, no symbol
    - Akun anak: `└─` symbol, lighter color, indented
    - Debit/Kredit/Saldo: Ter-indent sesuai level

### Test Case 3: Update Opening Balance

1. Edit saldo awal yang sudah ada
2. Ubah tanggal efektif ke bulan lain
3. **Expected**:
    - ✅ `period_year` dan `period_month` ter-update
    - ✅ `balance` ter-recalculate

## 💡 Benefits

1. **No More SQL Errors**: Field required ter-isi otomatis
2. **Data Consistency**: Balance calculated automatically
3. **Clear Hierarchy**: Sesuai kaidah akuntansi
4. **Professional Look**: Tampilan lebih rapi dan mudah dibaca
5. **Maintainable**: Logic centralized di controller

## ✅ Status

**BOTH ISSUES FIXED** ✅

1. ✅ SQL Error `period_year` doesn't have a default value - RESOLVED
2. ✅ Hierarki tampilan sesuai kaidah akuntansi - IMPLEMENTED

**Ready for testing!** 🚀
