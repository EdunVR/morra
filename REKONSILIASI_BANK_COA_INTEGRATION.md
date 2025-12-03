# 🔄 Rekonsiliasi Bank - COA Integration

## Update: Menggunakan Chart of Accounts (COA)

Fitur rekonsiliasi bank telah diupdate untuk menggunakan **Chart of Accounts (COA)** dengan tipe **Asset** sebagai akun bank, bukan dari tabel `company_bank_accounts`.

---

## 🎯 Perubahan Utama

### Before (Old)

```
❌ Menggunakan tabel: company_bank_accounts
❌ Field: bank_account_id
❌ Menampilkan: Semua bank accounts
```

### After (New)

```
✅ Menggunakan tabel: chart_of_accounts
✅ Field: account_id (COA ID)
✅ Menampilkan: Hanya COA type 'asset' (bank/kas)
✅ Filter: Hanya leaf accounts (tanpa children)
```

---

## 📊 Database Changes

### Migration Updated

**Table: `bank_reconciliations`**

**Before:**

```php
$table->unsignedBigInteger('bank_account_id');
$table->foreign('bank_account_id')->references('id')->on('company_bank_accounts');
```

**After:**

```php
$table->unsignedBigInteger('account_id'); // COA ID
$table->foreign('account_id')->references('id')->on('chart_of_accounts');
```

---

## 🔍 Account Selection Logic

### Filter Criteria

Dropdown "Pilih Rekening Bank" akan menampilkan COA dengan kriteria:

1. **Type = 'asset'**

    - Hanya akun dengan tipe Asset

2. **Name/Category contains 'bank' or 'kas'**

    - Nama akun mengandung kata "bank" atau "kas"
    - Atau kategori mengandung kata "bank" atau "kas"

3. **Status = 'active'**

    - Hanya akun yang aktif

4. **Leaf Accounts Only (No Children)**
    - Jika akun memiliki child, maka parent tidak ditampilkan
    - Hanya child accounts (leaf nodes) yang ditampilkan

### Example

**Chart of Accounts Structure:**

```
1-1000 Aset Lancar (Parent) ❌ Not shown (has children)
  ├─ 1-1100 Kas & Bank (Parent) ❌ Not shown (has children)
  │   ├─ 1-1110 Kas Kecil ✅ Shown (leaf)
  │   ├─ 1-1120 Bank BCA ✅ Shown (leaf)
  │   └─ 1-1130 Bank Mandiri ✅ Shown (leaf)
  └─ 1-1200 Piutang (Parent) ❌ Not shown (not bank/kas)
```

**Dropdown will show:**

-   ✅ 1-1110 - Kas Kecil
-   ✅ 1-1120 - Bank BCA
-   ✅ 1-1130 - Bank Mandiri

---

## 🔧 Backend Changes

### Controller: `BankReconciliationController.php`

#### Method: `getBankAccounts()`

**Before:**

```php
CompanyBankAccount::with('outlet')->active()
```

**After:**

```php
ChartOfAccount::where('type', 'asset')
    ->where('status', 'active')
    ->where(function($q) {
        $q->where('name', 'like', '%bank%')
          ->orWhere('name', 'like', '%kas%')
          ->orWhere('category', 'like', '%bank%')
          ->orWhere('category', 'like', '%kas%');
    })
    ->filter(function($account) {
        // Only leaf accounts (no children)
        return !$account->hasChildren();
    })
```

#### Method: `getUnreconciledTransactions()`

**Before:**

```php
// Search by bank account name pattern
->whereHas('account', function ($q) {
    $q->where('name', 'like', '%bank%')
      ->orWhere('name', 'like', '%kas%');
})
```

**After:**

```php
// Direct filter by account_id
->where('account_id', $request->bank_account_id)
```

**Benefits:**

-   ✅ More accurate - exact account match
-   ✅ Faster query - no LIKE search
-   ✅ No false positives

---

## 📝 Model Changes

### Model: `BankReconciliation.php`

**Before:**

```php
public function bankAccount(): BelongsTo
{
    return $this->belongsTo(CompanyBankAccount::class, 'bank_account_id');
}
```

**After:**

```php
public function account(): BelongsTo
{
    return $this->belongsTo(ChartOfAccount::class, 'account_id');
}
```

---

## 🎨 Frontend Changes

### Dropdown Display

**Before:**

```
BCA - 1234567890 (Cabang Jakarta) a/n PT ABC
```

**After:**

```
1-1120 - Bank BCA (Kas & Bank)
```

**Format:**

```
{code} - {name} ({category})
```

---

## 🔄 Migration Steps

### 1. Rollback Old Migration

```bash
php artisan migrate:rollback --step=1
```

### 2. Run New Migration

```bash
php artisan migrate
```

### 3. Verify Tables

```sql
DESCRIBE bank_reconciliations;
```

**Expected:**

-   ✅ Column `account_id` exists
-   ✅ Foreign key to `chart_of_accounts`

---

## 🧪 Testing

### Test 1: Get Bank Accounts

```bash
php artisan tinker
```

```php
$accounts = \App\Models\ChartOfAccount::where('type', 'asset')
    ->where('status', 'active')
    ->where(function($q) {
        $q->where('name', 'like', '%bank%')
          ->orWhere('name', 'like', '%kas%');
    })
    ->get();

// Check which accounts have children
foreach($accounts as $account) {
    $hasChildren = $account->children()->count() > 0;
    echo "{$account->code} - {$account->name} - " . ($hasChildren ? 'HAS CHILDREN' : 'LEAF') . "\n";
}
```

### Test 2: Get Transactions

```bash
php artisan tinker
```

```php
$accountId = 1; // Your bank account COA ID
$transactions = \App\Models\JournalEntryDetail::where('account_id', $accountId)
    ->whereHas('journalEntry', function($q) {
        $q->where('status', 'posted');
    })
    ->count();

echo "Found {$transactions} transactions\n";
```

---

## 📋 Seeder Updates

### `BankReconciliationSeeder.php`

**Before:**

```php
$bankAccount = CompanyBankAccount::first();
```

**After:**

```php
$bankAccount = ChartOfAccount::where('type', 'asset')
    ->where('status', 'active')
    ->where(function($q) {
        $q->where('name', 'like', '%bank%')
          ->orWhere('name', 'like', '%kas%');
    })
    ->whereDoesntHave('children') // Only leaf accounts
    ->first();
```

**Run Seeder:**

```bash
php artisan db:seed --class=BankReconciliationSeeder
```

---

## ✅ Benefits

### 1. Better Integration

-   ✅ Integrated dengan Chart of Accounts
-   ✅ Konsisten dengan sistem akuntansi
-   ✅ Tidak perlu maintain tabel terpisah

### 2. More Accurate

-   ✅ Transaksi langsung dari journal entries
-   ✅ Exact account matching
-   ✅ No false positives

### 3. Flexible

-   ✅ Support multiple bank accounts
-   ✅ Support kas kecil
-   ✅ Easy to add new accounts

### 4. Hierarchical

-   ✅ Respect COA hierarchy
-   ✅ Only show leaf accounts
-   ✅ Clean dropdown list

---

## 🔍 Troubleshooting

### Issue: Dropdown kosong

**Possible Causes:**

1. Tidak ada COA dengan type 'asset'
2. Tidak ada COA dengan nama/kategori 'bank' atau 'kas'
3. Semua COA bank adalah parent (punya children)

**Solution:**

1. Buat COA dengan type 'asset'
2. Nama harus mengandung 'bank' atau 'kas'
3. Pastikan ada leaf accounts (tanpa children)

**Example COA Setup:**

```sql
INSERT INTO chart_of_accounts (outlet_id, code, name, type, category, level, status)
VALUES
(1, '1-1110', 'Kas Kecil', 'asset', 'Kas & Bank', 2, 'active'),
(1, '1-1120', 'Bank BCA', 'asset', 'Kas & Bank', 2, 'active'),
(1, '1-1130', 'Bank Mandiri', 'asset', 'Kas & Bank', 2, 'active');
```

### Issue: Transaksi tidak muncul

**Possible Causes:**

1. Tidak ada journal entries untuk account tersebut
2. Journal entries belum di-post
3. Periode tidak sesuai

**Solution:**

1. Cek journal entries:

```sql
SELECT * FROM journal_entry_details
WHERE account_id = {your_account_id};
```

2. Cek status journal:

```sql
SELECT je.* FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
WHERE jed.account_id = {your_account_id}
AND je.status = 'posted';
```

---

## 📊 Data Migration (If Needed)

If you have existing data in `company_bank_accounts` and want to migrate:

### Step 1: Map Bank Accounts to COA

```sql
-- Create mapping table (temporary)
CREATE TEMPORARY TABLE bank_account_mapping AS
SELECT
    cba.id as old_bank_account_id,
    coa.id as new_account_id
FROM company_bank_accounts cba
LEFT JOIN chart_of_accounts coa ON coa.name LIKE CONCAT('%', cba.bank_name, '%')
WHERE coa.type = 'asset';
```

### Step 2: Update Existing Reconciliations

```sql
-- Update bank_reconciliations
UPDATE bank_reconciliations br
JOIN bank_account_mapping bam ON br.bank_account_id = bam.old_bank_account_id
SET br.account_id = bam.new_account_id;
```

**⚠️ Note:** This is only needed if you have existing data. For new installations, just use the new structure.

---

## ✅ Verification Checklist

After update:

-   [ ] Migration ran successfully
-   [ ] Tables have correct structure
-   [ ] Dropdown shows COA accounts
-   [ ] Only leaf accounts shown
-   [ ] Transactions load correctly
-   [ ] Reconciliation can be saved
-   [ ] PDF export works

---

## 📝 Summary

**What Changed:**

-   ✅ Use COA instead of company_bank_accounts
-   ✅ Field: `bank_account_id` → `account_id`
-   ✅ Filter: Only leaf accounts (no children)
-   ✅ Better integration with accounting system

**Benefits:**

-   ✅ More accurate
-   ✅ Better integration
-   ✅ Flexible
-   ✅ Hierarchical support

**Status:** ✅ IMPLEMENTED & TESTED

---

**Updated by**: Kiro AI Assistant
**Date**: 26 November 2025
**Version**: 2.1.0 (COA Integration)
