# ✅ POS COA Modal - PPN & Account Filtering Implementation

## 🎯 Objective

Update modal Setting COA di halaman POS index untuk:

1. Tambah field **Akun PPN**
2. Filter akun berdasarkan tipe (Asset, Liability, Revenue, Expense)
3. Hanya tampilkan leaf accounts (akun tanpa child)
4. Pisahkan PPN ke akun tersendiri dalam jurnal entry

## 📝 Changes Made

### 1. **Database Migration** ✅

**File:** `database/migrations/2025_12_01_add_akun_ppn_to_setting_coa_pos.php`

-   Added `akun_ppn` column to `setting_coa_pos` table
-   Migration executed successfully

### 2. **Model Update** ✅

**File:** `app/Models/SettingCOAPos.php`

-   Added `akun_ppn` to `$fillable` array

### 3. **Controller Update** ✅

**File:** `app/Http/Controllers/PosController.php`

#### A. COA Settings Method

-   Filter leaf accounts only
-   Group accounts by type
-   Return `$accountsByType` to view

#### B. Validation

-   Added `'akun_ppn' => 'nullable|string'`

#### C. Save Settings

-   Include `akun_ppn` in `updateOrCreateForOutlet()`

#### D. Journal Entry Logic

-   Calculate `$pendapatanBersih = $subtotal - $diskon`
-   Separate PPN to dedicated liability account
-   Graceful fallback if `akun_ppn` not set

### 4. **View Update - POS Index Modal** ✅

**File:** `resources/views/admin/penjualan/pos/index.blade.php`

#### A. JavaScript Data Structure

**Added accountsByType:**

```javascript
accountsByType: {
  asset: [],
  liability: [],
  equity: [],
  revenue: [],
  expense: []
}
```

**Updated coaForm:**

```javascript
coaForm: {
  accounting_book_id: '',
  akun_kas: '',
  akun_bank: '',
  akun_piutang_usaha: '',
  akun_pendapatan_penjualan: '',
  akun_hpp: '',
  akun_persediaan: '',
  akun_ppn: ''  // NEW
}
```

#### B. Load COA Data Function

**Before:**

```javascript
const accData = await accRes.json();
if (accData.success) {
    this.accounts = accData.data || [];
}
```

**After:**

```javascript
const accData = await accRes.json();
if (accData.success) {
    const allAccounts = accData.data || [];
    this.accounts = allAccounts;

    // Filter leaf accounts only (accounts without children)
    const leafAccounts = allAccounts.filter((account) => {
        return !allAccounts.some((child) => child.parent_code === account.code);
    });

    // Group by account type
    this.accountsByType = {
        asset: leafAccounts.filter((a) => a.type === "asset"),
        liability: leafAccounts.filter((a) => a.type === "liability"),
        equity: leafAccounts.filter((a) => a.type === "equity"),
        revenue: leafAccounts.filter((a) => a.type === "revenue"),
        expense: leafAccounts.filter((a) => a.type === "expense"),
    };
}
```

#### C. Modal HTML - Filtered Dropdowns

**Akun Kas (Asset only):**

```html
<select x-model="coaForm.akun_kas" required>
    <option value="">Pilih Akun Kas (Asset)</option>
    <template x-for="acc in accountsByType.asset" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    💵 Untuk pembayaran tunai (Tipe: Asset)
</p>
```

**Akun Bank (Asset only):**

```html
<select x-model="coaForm.akun_bank" required>
    <option value="">Pilih Akun Bank (Asset)</option>
    <template x-for="acc in accountsByType.asset" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    🏦 Untuk pembayaran transfer/QRIS (Tipe: Asset)
</p>
```

**Akun Piutang Usaha (Asset only):**

```html
<select x-model="coaForm.akun_piutang_usaha" required>
    <option value="">Pilih Akun Piutang (Asset)</option>
    <template x-for="acc in accountsByType.asset" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    📋 Untuk transaksi bon/piutang (Tipe: Asset)
</p>
```

**Akun Pendapatan Penjualan (Revenue only):**

```html
<select x-model="coaForm.akun_pendapatan_penjualan" required>
    <option value="">Pilih Akun Pendapatan (Revenue)</option>
    <template x-for="acc in accountsByType.revenue" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    💰 Pendapatan dari penjualan (Tipe: Revenue)
</p>
```

**Akun PPN (Liability only) - NEW:**

```html
<select x-model="coaForm.akun_ppn">
    <option value="">Pilih Akun PPN (Liability - Opsional)</option>
    <template x-for="acc in accountsByType.liability" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    📊 Untuk mencatat PPN 10% (Tipe: Liability)
</p>
```

**Akun HPP (Expense only):**

```html
<select x-model="coaForm.akun_hpp">
    <option value="">Pilih Akun HPP (Expense - Opsional)</option>
    <template x-for="acc in accountsByType.expense" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    📦 Untuk mencatat HPP produk yang terjual (Tipe: Expense)
</p>
```

**Akun Persediaan (Asset only):**

```html
<select x-model="coaForm.akun_persediaan">
    <option value="">Pilih Akun Persediaan (Asset - Opsional)</option>
    <template x-for="acc in accountsByType.asset" :key="acc.code">
        <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
    </template>
</select>
<p class="text-xs text-slate-500 mt-1">
    📦 Untuk mengurangi nilai persediaan (Tipe: Asset)
</p>
```

## 📊 Journal Entry Logic

### Before (PPN included in Revenue):

```
Transaction: Rp 100,000 + PPN 10% = Rp 110,000

Kas (D)                     Rp 110,000
    Pendapatan Penjualan (K)            Rp 110,000  ❌ PPN tercampur
```

### After (PPN separated):

```
Transaction: Rp 100,000 + PPN 10% = Rp 110,000

Kas (D)                     Rp 110,000
    Pendapatan Penjualan (K)            Rp 100,000  ✅ Bersih
    PPN Keluaran (K)                    Rp  10,000  ✅ Terpisah
```

## ✨ Benefits

### 1. **Accurate Tax Reporting**

-   PPN terpisah dari pendapatan
-   Mudah untuk laporan pajak
-   Sesuai standar akuntansi Indonesia

### 2. **Better UX - Account Filtering**

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

### Test 1: Open COA Settings Modal

1. Buka halaman **POS** (`/penjualan/pos`)
2. Klik button **⚙️ Setting COA** di header
3. **Verify:** Modal terbuka

### Test 2: Verify Account Filtering

1. Di modal, check setiap dropdown
2. **Verify:**
    - Akun Kas → Hanya Asset accounts
    - Akun Bank → Hanya Asset accounts
    - Akun Piutang → Hanya Asset accounts
    - Akun Pendapatan → Hanya Revenue accounts
    - **Akun PPN** → Hanya Liability accounts ⭐
    - Akun HPP → Hanya Expense accounts
    - Akun Persediaan → Hanya Asset accounts

### Test 3: Verify Leaf Accounts Only

1. Check dropdown accounts
2. **Verify:** Tidak ada parent accounts (akun yang punya child)
3. Hanya akun detail/leaf yang muncul

### Test 4: Save Settings

1. Pilih semua akun termasuk **Akun PPN**
2. Klik **💾 Simpan Setting**
3. **Verify:** Alert "✅ Setting COA POS berhasil disimpan"
4. Refresh page dan buka modal lagi
5. **Verify:** Akun PPN tersimpan

### Test 5: POS Transaction with PPN

1. Buat transaksi POS
2. Tambah produk Rp 100,000
3. Centang **PPN 10%**
4. **Verify:** Total = Rp 110,000
5. Bayar dengan Cash
6. Simpan transaksi

### Test 6: Verify Journal Entry

1. Buka **Accounting > Journal Entries**
2. Cari jurnal POS yang baru dibuat
3. **Verify entries:**
    ```
    Kas (D)                     Rp 110,000
        Pendapatan Penjualan (K)            Rp 100,000
        PPN Keluaran (K)                    Rp  10,000
    ```
4. **Verify:** PPN terpisah ke akun liability

### Test 7: BON Transaction with PPN

1. Buat transaksi POS BON
2. Tambah produk Rp 200,000
3. Centang **PPN 10%**
4. Pilih customer
5. Pilih **BON**
6. Simpan

### Test 8: Verify BON Journal

1. Check journal entry
2. **Verify:**
    ```
    Piutang Usaha (D)           Rp 220,000
        Pendapatan Penjualan (K)            Rp 200,000
        PPN Keluaran (K)                    Rp  20,000
    ```

### Test 9: Without PPN Account

1. Buka Setting COA
2. Kosongkan **Akun PPN**
3. Simpan
4. Buat transaksi dengan PPN
5. **Verify:** Jurnal tetap dibuat (fallback: PPN tidak dipisah)

## 🔄 Backward Compatibility

✅ **Fully backward compatible:**

-   Akun PPN bersifat opsional
-   Jika tidak diset, PPN tidak dipisah (behavior lama)
-   Existing transactions tidak terpengaruh
-   No breaking changes

## 📦 Files Modified

1. ✅ `database/migrations/2025_12_01_add_akun_ppn_to_setting_coa_pos.php` - NEW
2. ✅ `app/Models/SettingCOAPos.php` - Added `akun_ppn` to fillable
3. ✅ `app/Http/Controllers/PosController.php` - Updated COA logic & journal
4. ✅ `resources/views/admin/penjualan/pos/index.blade.php` - Updated modal

## 🎯 Key Features Implemented

1. ✅ PPN separated to dedicated liability account
2. ✅ Account filtering by type in modal dropdowns
3. ✅ Leaf accounts only (no parent accounts)
4. ✅ Visual indicators with emojis
5. ✅ Helper text for each field
6. ✅ Backward compatible (optional PPN account)
7. ✅ Graceful fallback if PPN account not set

## 📋 Summary

**Modal Setting COA di halaman POS sekarang memiliki:**

-   ✅ Field Akun PPN (Liability)
-   ✅ Dropdown filtered by account type
-   ✅ Hanya leaf accounts yang ditampilkan
-   ✅ Visual indicators (emoji + helper text)
-   ✅ PPN terpisah dalam jurnal entry

**Jurnal Entry sekarang:**

-   ✅ Pendapatan Penjualan = Subtotal - Diskon (bersih)
-   ✅ PPN 10% = Entry terpisah ke akun Liability
-   ✅ Total Debit = Total Kredit (balanced)

---

**Status:** ✅ COMPLETE
**Date:** December 1, 2025
**Location:** Modal Setting COA di `/penjualan/pos` (POS Index Page)
**Impact:** Improved accounting accuracy and better UX for COA configuration
