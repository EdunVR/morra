# 🔧 Fix: Dropdown Rekening Bank Tidak Muncul

## Issue

Dropdown "Pilih Rekening Bank" tidak menampilkan akun-akun COA.

## Root Cause

Fungsi filter `loadBankAccountsForReconcile()` dan `loadBankAccountsByOutlet()` masih menggunakan logika lama yang mencari berdasarkan `outlet_name`, padahal response API baru mengembalikan `outlet_id`.

### Before (Broken)

```javascript
loadBankAccountsForReconcile() {
  if (this.reconcileData.outlet_id) {
    this.filteredBankAccounts = this.bankAccounts.filter(
      account => account.outlet_name === this.outlets.find(o => o.id_outlet == this.reconcileData.outlet_id)?.nama_outlet
    );
  }
}
```

**Problem:**

-   ❌ `account.outlet_name` tidak ada di response
-   ❌ Filter selalu return empty array
-   ❌ Dropdown kosong

### After (Fixed)

```javascript
loadBankAccountsForReconcile() {
  if (this.reconcileData.outlet_id) {
    this.filteredBankAccounts = this.bankAccounts.filter(
      account => account.outlet_id == this.reconcileData.outlet_id
    );
  }
}
```

**Solution:**

-   ✅ Filter by `outlet_id` directly
-   ✅ Match dengan data yang ada di response
-   ✅ Dropdown terisi dengan benar

## Changes Made

### 1. Update `loadBankAccountsForReconcile()`

**File:** `resources/views/admin/finance/rekonsiliasi/index.blade.php`

```javascript
loadBankAccountsForReconcile() {
  if (this.reconcileData.outlet_id) {
    this.filteredBankAccounts = this.bankAccounts.filter(
      account => account.outlet_id == this.reconcileData.outlet_id
    );
  } else {
    this.filteredBankAccounts = this.bankAccounts;
  }
  this.reconcileData.bank_account_id = '';
}
```

### 2. Update `loadBankAccountsByOutlet()`

**File:** `resources/views/admin/finance/rekonsiliasi/index.blade.php`

```javascript
loadBankAccountsByOutlet() {
  if (this.formData.outlet_id) {
    this.filteredBankAccounts = this.bankAccounts.filter(
      account => account.outlet_id == this.formData.outlet_id
    );
  } else {
    this.filteredBankAccounts = this.bankAccounts;
  }
  this.formData.bank_account_id = '';
}
```

### 3. Initialize `filteredBankAccounts` on Init

**File:** `resources/views/admin/finance/rekonsiliasi/index.blade.php`

```javascript
async init() {
  await this.loadOutlets();
  await this.loadBankAccounts();
  await this.loadStatistics();
  await this.loadData();

  // Initialize filtered bank accounts
  if (this.outlets.length > 0 && this.formData.outlet_id) {
    this.loadBankAccountsByOutlet();
  } else {
    this.filteredBankAccounts = this.bankAccounts;
  }
}
```

## API Response Structure

### Endpoint: `/finance/rekonsiliasi/bank-accounts`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "code": "1000.01",
            "name": "Kas Kecil",
            "category": null,
            "level": 2,
            "balance": "-9000.00",
            "outlet_id": 1,
            "full_info": "1000.01 - Kas Kecil"
        },
        {
            "id": 3,
            "code": "1000.02",
            "name": "Kas Besar",
            "category": null,
            "level": 2,
            "balance": "-2000000.00",
            "outlet_id": 1,
            "full_info": "1000.02 - Kas Besar"
        },
        {
            "id": 5,
            "code": "1000.04",
            "name": "Bank Mandiri 22005757",
            "category": null,
            "level": 2,
            "balance": "-60000000.00",
            "outlet_id": 1,
            "full_info": "1000.04 - Bank Mandiri 22005757"
        }
    ]
}
```

**Key Fields:**

-   ✅ `id` - COA ID
-   ✅ `code` - Account code
-   ✅ `name` - Account name
-   ✅ `outlet_id` - Outlet ID (for filtering)
-   ✅ `full_info` - Display text

## Testing

### Test 1: Check API Response

```bash
php artisan tinker --execute="echo json_encode((new \App\Http\Controllers\BankReconciliationController())->getBankAccounts(new \Illuminate\Http\Request(['outlet_id' => 1]))->getData());"
```

**Expected:**

```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "outlet_id": 1,
            "full_info": "1000.01 - Kas Kecil"
        }
    ]
}
```

### Test 2: Check Dropdown in Browser

1. Open `/finance/rekonsiliasi`
2. Click "Buat Rekonsiliasi"
3. Select outlet
4. Check "Rekening Bank" dropdown

**Expected:**

-   ✅ Dropdown shows accounts
-   ✅ Format: `{code} - {name}`
-   ✅ Only accounts for selected outlet

### Test 3: Filter by Outlet

1. Select different outlet
2. Dropdown should update

**Expected:**

-   ✅ Dropdown updates automatically
-   ✅ Shows only accounts for selected outlet
-   ✅ Previous selection cleared

## Verification Checklist

-   [x] API returns correct data structure
-   [x] `outlet_id` field exists in response
-   [x] Filter function uses `outlet_id`
-   [x] Dropdown populates on init
-   [x] Dropdown updates on outlet change
-   [x] Display format is correct

## Common Issues

### Issue: Dropdown still empty

**Possible Causes:**

1. No COA with type 'asset' and name containing 'bank' or 'kas'
2. All accounts are parent accounts (have children)
3. JavaScript error in console

**Solutions:**

1. **Check COA exists:**

```sql
SELECT * FROM chart_of_accounts
WHERE type = 'asset'
AND status = 'active'
AND (name LIKE '%bank%' OR name LIKE '%kas%');
```

2. **Check for leaf accounts:**

```sql
SELECT coa.*
FROM chart_of_accounts coa
WHERE coa.type = 'asset'
AND coa.status = 'active'
AND (coa.name LIKE '%bank%' OR coa.name LIKE '%kas%')
AND NOT EXISTS (
  SELECT 1 FROM chart_of_accounts child
  WHERE child.parent_id = coa.id
);
```

3. **Check browser console:**

-   Open DevTools (F12)
-   Check Console tab for errors
-   Check Network tab for API response

### Issue: Wrong accounts shown

**Possible Cause:**
Filter by outlet not working

**Solution:**
Check if `outlet_id` in response matches selected outlet:

```javascript
console.log("Selected outlet:", this.reconcileData.outlet_id);
console.log("Bank accounts:", this.bankAccounts);
console.log("Filtered:", this.filteredBankAccounts);
```

## Summary

**What was fixed:**

-   ✅ Filter function now uses `outlet_id` instead of `outlet_name`
-   ✅ Dropdown initializes correctly on page load
-   ✅ Dropdown updates when outlet changes

**Result:**

-   ✅ Dropdown shows COA accounts
-   ✅ Filter by outlet works
-   ✅ Display format is correct

**Status:** ✅ FIXED

---

**Fixed by:** Kiro AI Assistant
**Date:** 26 November 2025
**Time:** ~5 minutes
