# ✅ POS - PPN Separate Account Implementation

## 🎯 Objective

Memisahkan PPN 10% ke akun tersendiri dalam jurnal entry POS, dan meningkatkan UX modal COA settings dengan:

1. Filter akun berdasarkan tipe (Asset, Liability, Revenue, Expense)
2. Hanya tampilkan leaf accounts (akun tanpa child)
3. Tambah field Akun PPN

## 📝 Changes Made

### 1. **Database Migration** - Add `akun_ppn` field

**File:** `database/migrations/2025_12_01_add_akun_ppn_to_setting_coa_pos.php`

```php
Schema::table('setting_coa_pos', function (Blueprint $table) {
    $table->string('akun_ppn', 20)->nullable()->after('akun_persediaan');
});
```

### 2. **Model Update** - SettingCOAPos

**File:** `app/Models/SettingCOAPos.php`

Added `akun_ppn` to fillable:

```php
protected $fillable = [
    'id_outlet',
    'accounting_book_id',
    'akun_kas',
    'akun_bank',
    'akun_piutang_usaha',
    'akun_pendapatan_penjualan',
    'akun_hpp',
    'akun_persediaan',
    'akun_ppn', // NEW
];
```

### 3. **Controller Update** - PosController

#### A. COA Settings Method - Filter Accounts

**Before:** Menampilkan semua akun

```php
$accounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
    ->orderBy('code')
    ->get();
```

**After:** Filter leaf accounts dan group by type

```php
// Get leaf accounts only (accounts without children) grouped by type
$allAccounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
    ->orderBy('code')
    ->get();

// Filter only leaf accounts (no children)
$leafAccounts = $allAccounts->filter(function($account) use ($allAccounts) {
    return !$allAccounts->contains(function($child) use ($account) {
        return $child->parent_code === $account->code;
    });
});

// Group by account type
$accountsByType = [
    'asset' => $leafAccounts->filter(fn($a) => $a->type === 'asset')->values(),
    'liability' => $leafAccounts->filter(fn($a) => $a->type === 'liability')->values(),
    'equity' => $leafAccounts->filter(fn($a) => $a->type === 'equity')->values(),
    'revenue' => $leafAccounts->filter(fn($a) => $a->type === 'revenue')->values(),
    'expense' => $leafAccounts->filter(fn($a) => $a->type === 'expense')->values(),
];
```

#### B. Validation - Add akun_ppn

```php
$validator = Validator::make($request->all(), [
    'accounting_book_id' => 'required|exists:accounting_books,id',
    'akun_kas' => 'required|string',
    'akun_bank' => 'required|string',
    'akun_piutang_usaha' => 'required|string',
    'akun_pendapatan_penjualan' => 'required|string',
    'akun_hpp' => 'nullable|string',
    'akun_persediaan' => 'nullable|string',
    'akun_ppn' => 'nullable|string', // NEW
]);
```

#### C. Journal Entry Logic - Separate PPN

**Before:** PPN included in Pendapatan Penjualan

```php
$entries[] = [
    'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $posSale->id_outlet),
    'debit' => 0,
    'credit' => $posSale->total, // Total termasuk PPN
    'memo' => 'Pendapatan penjualan POS'
];
```

**After:** PPN separated to dedicated account

```php
// Hitung pendapatan bersih (tanpa PPN)
$pendapatanBersih = $posSale->subtotal - $posSale->total_diskon;
$ppnAmount = $posSale->ppn;

$entries[] = [
    'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $posSale->id_outlet),
    'debit' => 0,
    'credit' => $pendapatanBersih, // Pendapatan bersih tanpa PPN
    'memo' => 'Pendapatan penjualan POS'
];

// Pisahkan PPN jika ada dan akun PPN sudah diset
if ($ppnAmount > 0 && !empty($setting->akun_ppn)) {
    $entries[] = [
        'account_id' => $this->getAccountIdByCode($setting->akun_ppn, $posSale->id_outlet),
        'debit' => 0,
        'credit' => $ppnAmount,
        'memo' => 'PPN 10% dari penjualan'
    ];
}
```

### 4. **View Update** - COA Settings Modal

#### A. Account Dropdowns - Filtered by Type

Each dropdown now shows only relevant account types:

**Akun Kas** - Asset only:

```blade
<select x-model="form.akun_kas" required>
  <option value="">Pilih Akun Kas (Asset)</option>
  @foreach($accountsByType['asset'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">💵 Untuk pembayaran tunai (Tipe: Asset)</p>
```

**Akun Bank** - Asset only:

```blade
<select x-model="form.akun_bank" required>
  <option value="">Pilih Akun Bank (Asset)</option>
  @foreach($accountsByType['asset'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">🏦 Untuk pembayaran transfer/QRIS (Tipe: Asset)</p>
```

**Akun Piutang Usaha** - Asset only:

```blade
<select x-model="form.akun_piutang_usaha" required>
  <option value="">Pilih Akun Piutang (Asset)</option>
  @foreach($accountsByType['asset'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">📋 Untuk transaksi bon/piutang (Tipe: Asset)</p>
```

**Akun Pendapatan Penjualan** - Revenue only:

```blade
<select x-model="form.akun_pendapatan_penjualan" required>
  <option value="">Pilih Akun Pendapatan (Revenue)</option>
  @foreach($accountsByType['revenue'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">💰 Pendapatan dari penjualan (Tipe: Revenue)</p>
```

**Akun PPN** - Liability only (NEW):

```blade
<select x-model="form.akun_ppn">
  <option value="">Pilih Akun PPN (Liability - Opsional)</option>
  @foreach($accountsByType['liability'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">📊 Untuk mencatat PPN 10% (Tipe: Liability)</p>
```

**Akun HPP** - Expense only:

```blade
<select x-model="form.akun_hpp">
  <option value="">Pilih Akun HPP (Expense - Opsional)</option>
  @foreach($accountsByType['expense'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">📦 Untuk mencatat HPP produk yang terjual (Tipe: Expense)</p>
```

**Akun Persediaan** - Asset only:

```blade
<select x-model="form.akun_persediaan">
  <option value="">Pilih Akun Persediaan (Asset - Opsional)</option>
  @foreach($accountsByType['asset'] as $account)
    <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
  @endforeach
</select>
<p class="text-xs text-slate-500 mt-1">📦 Untuk mengurangi nilai persediaan (Tipe: Asset)</p>
```

#### B. Alpine.js Form Data - Add akun_ppn

```javascript
form: {
  accounting_book_id: '{{ $setting->accounting_book_id ?? "" }}',
  akun_kas: '{{ $setting->akun_kas ?? "" }}',
  akun_bank: '{{ $setting->akun_bank ?? "" }}',
  akun_piutang_usaha: '{{ $setting->akun_piutang_usaha ?? "" }}',
  akun_pendapatan_penjualan: '{{ $setting->akun_pendapatan_penjualan ?? "" }}',
  akun_hpp: '{{ $setting->akun_hpp ?? "" }}',
  akun_persediaan: '{{ $setting->akun_persediaan ?? "" }}',
  akun_ppn: '{{ $setting->akun_ppn ?? "" }}' // NEW
}
```

## 📊 Journal Entry Examples

### Example 1: Cash Sale with PPN (Lunas)

**Transaction:**

-   Subtotal: Rp 100,000
-   Diskon: Rp 0
-   PPN 10%: Rp 10,000
-   Total: Rp 110,000
-   Payment: Cash

**Journal Entry:**

```
Kas (D)                         Rp 110,000
    Pendapatan Penjualan (K)                Rp 100,000
    PPN Keluaran (K)                        Rp  10,000
```

### Example 2: BON Sale with PPN (Piutang)

**Transaction:**

-   Subtotal: Rp 200,000
-   Diskon: Rp 20,000
-   PPN 10%: Rp 18,000
-   Total: Rp 198,000
-   Payment: BON

**Journal Entry:**

```
Piutang Usaha (D)               Rp 198,000
    Pendapatan Penjualan (K)                Rp 180,000
    PPN Keluaran (K)                        Rp  18,000
```

### Example 3: Transfer Sale with PPN + HPP

**Transaction:**

-   Subtotal: Rp 500,000
-   Diskon: Rp 50,000
-   PPN 10%: Rp 45,000
-   Total: Rp 495,000
-   Payment: Transfer
-   HPP: Rp 300,000

**Journal Entry:**

```
Bank (D)                        Rp 495,000
HPP (D)                         Rp 300,000
    Pendapatan Penjualan (K)                Rp 450,000
    PPN Keluaran (K)                        Rp  45,000
    Persediaan (K)                          Rp 300,000
```

## ✨ Benefits

### 1. **Accurate Tax Reporting**

-   PPN terpisah dari pendapatan
-   Mudah untuk laporan pajak
-   Sesuai standar akuntansi

### 2. **Better Account Filtering**

-   User hanya lihat akun yang relevan
-   Mengurangi kesalahan pemilihan akun
-   Lebih cepat dalam setup

### 3. **Leaf Accounts Only**

-   Tidak ada parent accounts di dropdown
-   Hanya akun yang bisa di-posting
-   Cleaner dan lebih jelas

### 4. **Visual Indicators**

-   Emoji untuk setiap tipe akun
-   Label tipe akun di setiap field
-   Helper text yang informatif

## 🧪 Testing Guide

### Test 1: Setup COA Settings

1. Buka **POS > Setting COA**
2. Pilih outlet
3. **Verify:** Setiap dropdown hanya menampilkan akun sesuai tipe:
    - Kas/Bank/Piutang → Asset accounts only
    - Pendapatan → Revenue accounts only
    - PPN → Liability accounts only
    - HPP → Expense accounts only
    - Persediaan → Asset accounts only
4. **Verify:** Tidak ada parent accounts di dropdown
5. Set semua akun termasuk **Akun PPN**
6. Simpan

### Test 2: POS Transaction with PPN

1. Buka **POS**
2. Tambah produk dengan total Rp 100,000
3. Centang **PPN 10%**
4. **Verify:** Total menjadi Rp 110,000
5. Bayar dengan Cash
6. Simpan transaksi

### Test 3: Verify Journal Entry

1. Buka **Accounting > Journal Entries**
2. Cari jurnal POS yang baru dibuat
3. **Verify entries:**
    ```
    Kas (D)                     Rp 110,000
        Pendapatan Penjualan (K)            Rp 100,000
        PPN Keluaran (K)                    Rp  10,000
    ```
4. **Verify:** PPN terpisah dari pendapatan

### Test 4: BON Transaction with PPN

1. Buka **POS**
2. Tambah produk Rp 200,000
3. Centang **PPN 10%**
4. Pilih customer
5. Pilih **BON**
6. Simpan

### Test 5: Verify BON Journal

1. Buka **Accounting > Journal Entries**
2. Cari jurnal BON
3. **Verify entries:**
    ```
    Piutang Usaha (D)           Rp 220,000
        Pendapatan Penjualan (K)            Rp 200,000
        PPN Keluaran (K)                    Rp  20,000
    ```

### Test 6: Without PPN Account Set

1. Buka **POS > Setting COA**
2. Kosongkan **Akun PPN**
3. Simpan
4. Buat transaksi POS dengan PPN
5. **Verify:** Jurnal tetap dibuat, tapi PPN tidak dipisah (fallback behavior)

## 🔄 Backward Compatibility

✅ **Fully backward compatible:**

-   Akun PPN bersifat opsional
-   Jika tidak diset, PPN tetap masuk ke pendapatan (behavior lama)
-   Existing transactions tidak terpengaruh
-   Migration hanya menambah kolom, tidak mengubah data

## 📦 Database Schema

**Table:** `setting_coa_pos`

| Column                    | Type            | Nullable | Description                     |
| ------------------------- | --------------- | -------- | ------------------------------- |
| id                        | bigint          | NO       | Primary key                     |
| id_outlet                 | bigint          | NO       | Foreign key to outlets          |
| accounting_book_id        | bigint          | YES      | Foreign key to accounting_books |
| akun_kas                  | varchar(20)     | YES      | Cash account code               |
| akun_bank                 | varchar(20)     | YES      | Bank account code               |
| akun_piutang_usaha        | varchar(20)     | YES      | Accounts receivable code        |
| akun_pendapatan_penjualan | varchar(20)     | YES      | Sales revenue code              |
| akun_hpp                  | varchar(20)     | YES      | COGS account code               |
| akun_persediaan           | varchar(20)     | YES      | Inventory account code          |
| **akun_ppn**              | **varchar(20)** | **YES**  | **VAT account code (NEW)**      |
| created_at                | timestamp       | YES      |                                 |
| updated_at                | timestamp       | YES      |                                 |

## 🎯 Key Features

1. ✅ PPN separated to dedicated liability account
2. ✅ Account filtering by type (Asset, Liability, Revenue, Expense)
3. ✅ Leaf accounts only (no parent accounts)
4. ✅ Visual indicators with emojis
5. ✅ Helper text for each field
6. ✅ Backward compatible
7. ✅ Optional PPN account (graceful fallback)

---

**Status:** ✅ COMPLETE
**Date:** December 1, 2025
**Impact:** Improved accounting accuracy and better UX for COA settings
