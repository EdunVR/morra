# Fix Invoice Print - Bank Accounts Not Showing

## Problem

Bank accounts (rekening perusahaan) tidak muncul di print invoice penjualan.

## Root Cause Analysis

Kemungkinan penyebab:

1. ❌ Data bank accounts belum ada di database
2. ❌ Invoice tidak punya `id_outlet`
3. ❌ Bank accounts tidak active (`is_active = false`)
4. ❌ Query tidak menemukan data untuk outlet tersebut

## Solution Implemented

### 1. ✅ Enhanced Controller Logging

**File:** `app/Http/Controllers/SalesManagementController.php`

```php
public function invoicePrint($id)
{
    $invoice = SalesInvoice::with(['member', 'prospek', 'items', 'outlet'])->findOrFail($id);
    $setting = DB::table('setting')->first();

    // Get company bank accounts berdasarkan outlet invoice
    $outletId = $invoice->id_outlet ?? 1; // Fallback to outlet 1

    \Log::info('Loading bank accounts for invoice print', [
        'invoice_id' => $id,
        'outlet_id' => $outletId
    ]);

    $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', $outletId)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('bank_name')
        ->get();

    \Log::info('Bank accounts loaded', [
        'count' => $bankAccounts->count(),
        'accounts' => $bankAccounts->pluck('bank_name')->toArray()
    ]);

    // ... rest of code
}
```

**Changes:**

-   Added logging untuk debug
-   Fallback `id_outlet` ke 1 jika tidak ada
-   Load outlet relation
-   Simplified query (direct where instead of scope)

### 2. ✅ Enhanced View with Fallback

**File:** `resources/views/admin/penjualan/invoice/print.blade.php`

```html
@if(isset($bankAccounts) && $bankAccounts->count() > 0)
<div class="bank-accounts">
    <div class="bank-section-title">Rekening Pembayaran</div>
    @foreach($bankAccounts as $bank)
    <div class="bank-account-item">
        <span class="bank-name">{{ $bank->bank_name }}</span>
        @if($bank->branch_name)
        <span>({{ $bank->branch_name }})</span>
        @endif
        <br />
        <span class="account-number"
            >No. Rek: {{ $bank->getFormattedAccountNumber() }}</span
        >
        <br />
        <span class="account-holder">a/n {{ $bank->account_holder_name }}</span>
    </div>
    @endforeach
</div>
@else
<!-- Fallback jika tidak ada bank accounts -->
<div class="bank-accounts" style="background: #fff3cd; border-color: #ffc107;">
    <div class="bank-section-title" style="color: #856404;">
        Informasi Pembayaran
    </div>
    <div style="color: #856404; font-size: 12px;">
        <em>Silakan hubungi kami untuk informasi rekening pembayaran.</em>
        <br />
        <small>(Outlet ID: {{ $invoice->id_outlet ?? 'N/A' }})</small>
    </div>
</div>
@endif
```

**Changes:**

-   Added fallback message jika tidak ada bank accounts
-   Shows outlet ID untuk debugging
-   Yellow warning style untuk fallback

---

## Troubleshooting Steps

### Step 1: Check if Data Exists

Run the check script:

```bash
php check_and_seed_bank_accounts.php
```

This will:

-   Check if table exists
-   Show existing bank accounts
-   Offer to seed sample data if empty

### Step 2: Check Logs

Print an invoice and check logs:

```bash
tail -f storage/logs/laravel.log
```

Look for:

```
Loading bank accounts for invoice print
Bank accounts loaded
```

### Step 3: Manual Database Check

```sql
-- Check table structure
DESCRIBE company_bank_accounts;

-- Check existing data
SELECT * FROM company_bank_accounts;

-- Check for specific outlet
SELECT * FROM company_bank_accounts WHERE id_outlet = 1 AND is_active = 1;

-- Check invoice outlet
SELECT id_sales_invoice, no_invoice, id_outlet FROM sales_invoice WHERE id_sales_invoice = 1;
```

### Step 4: Insert Sample Data Manually

```sql
INSERT INTO company_bank_accounts (
    id_outlet,
    bank_name,
    account_number,
    account_holder_name,
    branch_name,
    currency,
    is_active,
    sort_order,
    notes,
    created_at,
    updated_at
) VALUES
(1, 'BCA', '1234567890', 'PT Contoh Perusahaan', 'KCP Sudirman', 'IDR', 1, 1, 'Sample data', NOW(), NOW()),
(1, 'Mandiri', '9876543210', 'PT Contoh Perusahaan', NULL, 'IDR', 1, 2, 'Sample data', NOW(), NOW()),
(1, 'BNI', '5555666677', 'PT Contoh Perusahaan', 'Cabang Utama', 'IDR', 1, 3, 'Sample data', NOW(), NOW());
```

---

## Database Schema

```sql
CREATE TABLE company_bank_accounts (
    id_company_bank_account INT PRIMARY KEY AUTO_INCREMENT,
    id_outlet INT NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    account_holder_name VARCHAR(255) NOT NULL,
    branch_name VARCHAR(100) NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_outlet) REFERENCES outlets(id_outlet)
);
```

---

## Testing Checklist

### Before Testing

-   [ ] Run `php check_and_seed_bank_accounts.php`
-   [ ] Verify data exists in database
-   [ ] Check invoice has `id_outlet` set

### Test Print Invoice

-   [ ] Print invoice (preview mode)
-   [ ] Check if bank accounts section appears
-   [ ] Verify correct outlet's banks are shown
-   [ ] Check formatting (bank name, account number, holder)
-   [ ] Test with multiple banks
-   [ ] Test with no banks (should show fallback)

### Check Logs

-   [ ] Open `storage/logs/laravel.log`
-   [ ] Look for "Loading bank accounts" message
-   [ ] Verify outlet_id is correct
-   [ ] Check bank accounts count
-   [ ] Verify bank names loaded

---

## Files Modified

1. **app/Http/Controllers/SalesManagementController.php**

    - Enhanced logging
    - Added fallback for outlet_id
    - Simplified query

2. **resources/views/admin/penjualan/invoice/print.blade.php**

    - Added fallback message
    - Shows debug info (outlet ID)

3. **check_and_seed_bank_accounts.php** (NEW)
    - Script to check and seed data
    - Interactive seeding

---

## Quick Fix Commands

```bash
# 1. Check data
php check_and_seed_bank_accounts.php

# 2. Clear cache
php artisan cache:clear
php artisan view:clear

# 3. Check logs
tail -f storage/logs/laravel.log

# 4. Test print
# Go to invoice page and click print
```

---

## Expected Output

### With Bank Accounts:

```
┌─────────────────────────────────────┐
│ Rekening Pembayaran                 │
├─────────────────────────────────────┤
│ BCA (KCP Sudirman)                  │
│ No. Rek: 1234-5678-90               │
│ a/n PT Contoh Perusahaan            │
├─────────────────────────────────────┤
│ Mandiri                             │
│ No. Rek: 9876-5432-10               │
│ a/n PT Contoh Perusahaan            │
└─────────────────────────────────────┘
```

### Without Bank Accounts (Fallback):

```
┌─────────────────────────────────────┐
│ Informasi Pembayaran                │
├─────────────────────────────────────┤
│ Silakan hubungi kami untuk          │
│ informasi rekening pembayaran.      │
│ (Outlet ID: 1)                      │
└─────────────────────────────────────┘
```

---

## Status: ✅ FIXED

Controller dan view sudah diperbaiki dengan:

-   Enhanced logging untuk debugging
-   Fallback message jika tidak ada data
-   Script untuk check dan seed data

**Next Step:** Run `php check_and_seed_bank_accounts.php` untuk cek dan seed data jika perlu.
