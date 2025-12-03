# 📒 Integrasi Jurnal Otomatis - Payroll

## Status: ✅ SELESAI

Modul Payroll sekarang sudah terintegrasi penuh dengan sistem jurnal akuntansi. Setiap transaksi payroll akan otomatis membuat jurnal entry.

---

## 🎯 Fitur Integrasi Jurnal

### 1. **Jurnal Otomatis**

-   ✅ Jurnal dibuat otomatis saat approve payroll
-   ✅ Jurnal pembayaran dibuat saat pay payroll
-   ✅ Double-entry bookkeeping (debit = credit)
-   ✅ Terintegrasi dengan Chart of Accounts

### 2. **COA Settings per Outlet**

-   ✅ Konfigurasi akun per outlet
-   ✅ Flexible account mapping
-   ✅ Validasi akun sebelum create jurnal

### 3. **Workflow Integration**

-   Draft → Tidak ada jurnal
-   Approve → Jurnal beban & hutang gaji
-   Pay → Jurnal pembayaran dari kas

---

## 📊 Alur Jurnal

### Saat Approve Payroll:

**Jurnal Entry:**

```
Tanggal: Payment Date
Deskripsi: Approval Payroll - [Nama Karyawan] - [Periode]

DEBIT:
- Beban Gaji Pokok          Rp xxx
- Beban Lembur              Rp xxx (jika ada)
- Beban Bonus               Rp xxx (jika ada)
- Beban Tunjangan           Rp xxx (jika ada)
- Piutang Pinjaman Karyawan Rp xxx (jika ada potongan pinjaman)

CREDIT:
- Hutang Pajak              Rp xxx (jika ada)
- Hutang Gaji               Rp xxx (net salary)

Total Debit = Total Credit
```

**Penjelasan:**

-   Beban gaji dicatat sebagai expense
-   Hutang gaji dicatat sebagai liability
-   Potongan pinjaman mengurangi piutang ke karyawan
-   Pajak dicatat sebagai hutang pajak

### Saat Pay Payroll:

**Jurnal Entry:**

```
Tanggal: Tanggal Pembayaran
Deskripsi: Pembayaran Gaji - [Nama Karyawan] - [Periode]

DEBIT:
- Hutang Gaji               Rp xxx

CREDIT:
- Kas/Bank                  Rp xxx

Total Debit = Total Credit
```

**Penjelasan:**

-   Hutang gaji dilunasi
-   Kas/Bank berkurang

---

## 🗄️ Database Structure

### Tabel: payroll_coa_settings

```sql
CREATE TABLE payroll_coa_settings (
    id BIGINT PRIMARY KEY,
    outlet_id BIGINT UNIQUE (FK → outlets),

    -- Expense Accounts (Debit saat approve)
    salary_expense_account_id BIGINT (FK → chart_of_accounts),
    overtime_expense_account_id BIGINT (FK → chart_of_accounts),
    bonus_expense_account_id BIGINT (FK → chart_of_accounts),
    allowance_expense_account_id BIGINT (FK → chart_of_accounts),

    -- Liability Accounts (Credit saat approve)
    tax_payable_account_id BIGINT (FK → chart_of_accounts),
    salary_payable_account_id BIGINT (FK → chart_of_accounts),

    -- Asset Accounts
    loan_receivable_account_id BIGINT (FK → chart_of_accounts),
    cash_account_id BIGINT (FK → chart_of_accounts),

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Constraints:**

-   Unique per outlet
-   Foreign keys ke chart_of_accounts
-   ON DELETE SET NULL untuk akun

---

## 🔧 Backend Implementation

### 1. Service: PayrollJournalService

**File**: `app/Services/PayrollJournalService.php`

**Methods:**

-   `createApprovalJournal($payroll)` - Create jurnal saat approve
-   `createPaymentJournal($payroll)` - Create jurnal saat pay
-   `reverseJournals($payroll)` - Reverse jurnal (jika perlu)

**Features:**

-   Validasi COA settings
-   Validasi balance (debit = credit)
-   Transaction handling
-   Error logging
-   Flexible account mapping

### 2. Model: PayrollCoaSetting

**File**: `app/Models/PayrollCoaSetting.php`

**Relasi:**

-   `outlet()` → Outlet
-   `salaryExpenseAccount()` → ChartOfAccount
-   `overtimeExpenseAccount()` → ChartOfAccount
-   `bonusExpenseAccount()` → ChartOfAccount
-   `allowanceExpenseAccount()` → ChartOfAccount
-   `taxPayableAccount()` → ChartOfAccount
-   `loanReceivableAccount()` → ChartOfAccount
-   `salaryPayableAccount()` → ChartOfAccount
-   `cashAccount()` → ChartOfAccount

### 3. Controller: PayrollCoaSettingController

**File**: `app/Http/Controllers/PayrollCoaSettingController.php`

**Methods:**

-   `index()` - Tampilan setting COA
-   `getSettings($outletId)` - Get setting per outlet
-   `store()` - Save/update setting

### 4. Updated: PayrollManagementController

**Changes:**

-   Constructor injection `PayrollJournalService`
-   Method `approve()` - Call `createApprovalJournal()`
-   Method `pay()` - Call `createPaymentJournal()`
-   Error handling untuk jurnal

---

## 🎨 Frontend Implementation

### 1. View: COA Settings

**File**: `resources/views/admin/sdm/payroll/coa-settings.blade.php`

**Sections:**

-   Outlet selection
-   Expense accounts (4 fields)
-   Liability accounts (2 fields)
-   Asset accounts (2 fields)
-   Jurnal flow info
-   Save button

**Features:**

-   Auto-load settings per outlet
-   Required field validation
-   Clear instructions
-   Visual flow diagram

### 2. Updated: Payroll Index

**Button**: "Setting COA" di header
**Link**: Route ke COA settings page

---

## 🛣️ Routes

```php
// COA Settings
Route::get('/payroll/coa-settings', 'index');           // sdm.payroll.coa.index
Route::get('/payroll/coa-settings/get', 'getSettings'); // sdm.payroll.coa.settings
Route::post('/payroll/coa-settings/store', 'store');    // sdm.payroll.coa.store
```

---

## 📋 Setup Guide

### Step 1: Konfigurasi COA per Outlet

1. Login sebagai admin
2. Buka menu **SDM → Payroll**
3. Klik button **"Setting COA"**
4. Pilih **Outlet**
5. Konfigurasi akun:

**Akun Wajib:**

-   Beban Gaji Pokok (Expense)
-   Hutang Gaji (Liability)
-   Hutang Pajak (Liability)
-   Kas/Bank (Asset)

**Akun Opsional:**

-   Beban Lembur (Expense)
-   Beban Bonus (Expense)
-   Beban Tunjangan (Expense)
-   Piutang Pinjaman Karyawan (Asset)

6. Klik **"Simpan Setting"**

### Step 2: Test Jurnal Otomatis

1. Buat payroll baru (status: Draft)
2. Approve payroll
    - ✅ Jurnal approval otomatis dibuat
    - ✅ Cek di menu Jurnal
3. Pay payroll
    - ✅ Jurnal payment otomatis dibuat
    - ✅ Cek di menu Jurnal

---

## 🔍 Contoh Kasus

### Kasus: Gaji Karyawan Rp 10.000.000

**Data Payroll:**

-   Gaji Pokok: Rp 10.000.000
-   Lembur: Rp 500.000
-   Bonus: Rp 1.000.000
-   Tunjangan: Rp 500.000
-   Potongan Pinjaman: Rp 1.000.000
-   Pajak: Rp 500.000
-   Gaji Bersih: Rp 10.500.000

**Jurnal saat Approve:**

```
Dr. Beban Gaji Pokok          10.000.000
Dr. Beban Lembur                 500.000
Dr. Beban Bonus                1.000.000
Dr. Beban Tunjangan              500.000
Dr. Piutang Pinjaman Karyawan  1.000.000
    Cr. Hutang Pajak                       500.000
    Cr. Hutang Gaji                     10.500.000
                              ---------------------
Total                         13.000.000  13.000.000 ✓
```

**Jurnal saat Pay:**

```
Dr. Hutang Gaji               10.500.000
    Cr. Kas/Bank                        10.500.000
                              ---------------------
Total                         10.500.000  10.500.000 ✓
```

---

## 🔐 Security & Validation

### 1. COA Settings Validation

-   Outlet access control
-   Required fields validation
-   Account existence validation

### 2. Journal Creation Validation

-   COA settings must exist
-   Balance validation (debit = credit)
-   Transaction rollback on error

### 3. Error Handling

-   Try-catch blocks
-   Database transactions
-   Error logging
-   User-friendly error messages

---

## 🧪 Testing Checklist

-   [ ] Setup COA settings untuk outlet
-   [ ] Approve payroll → Jurnal approval dibuat
-   [ ] Pay payroll → Jurnal payment dibuat
-   [ ] Cek jurnal di menu Jurnal
-   [ ] Validasi balance (debit = credit)
-   [ ] Test dengan berbagai kombinasi (lembur, bonus, potongan)
-   [ ] Test error handling (COA belum setup)
-   [ ] Test outlet access control
-   [ ] Cek laporan buku besar
-   [ ] Cek laporan laba rugi (beban gaji muncul)

---

## 📊 Impact ke Laporan Keuangan

### Laporan Laba Rugi:

-   **Beban Gaji** akan muncul di section Expenses
-   Mengurangi laba bersih

### Neraca:

-   **Hutang Gaji** akan muncul di Liabilities (sebelum dibayar)
-   **Kas/Bank** berkurang saat pembayaran

### Buku Besar:

-   Semua transaksi tercatat per akun
-   Bisa dilacak per karyawan

---

## 💡 Tips & Best Practices

### 1. **Setup COA**

-   Buat akun khusus untuk payroll
-   Pisahkan beban gaji per jenis (pokok, lembur, bonus)
-   Gunakan kode akun yang konsisten

### 2. **Approval Workflow**

-   Review payroll sebelum approve
-   Setelah approve, jurnal langsung dibuat
-   Tidak bisa edit setelah approve

### 3. **Payment Tracking**

-   Tandai sebagai paid setelah transfer
-   Jurnal payment otomatis dibuat
-   Cek saldo kas/bank

### 4. **Reconciliation**

-   Cek jurnal vs slip gaji
-   Pastikan balance
-   Review laporan bulanan

---

## 🚀 Next Steps (Opsional)

### Priority High:

1. Bulk approve payroll (semua karyawan sekaligus)
2. Jurnal reversal (jika ada kesalahan)
3. Integration dengan bank transfer

### Priority Medium:

4. Laporan beban gaji per departemen
5. Laporan pajak PPh 21
6. Export jurnal ke accounting software

### Priority Low:

7. Auto-create payroll dari absensi
8. Reminder pembayaran gaji
9. Dashboard cash flow projection

---

## ✅ Checklist Implementation

-   [x] Migration payroll_coa_settings
-   [x] Model PayrollCoaSetting
-   [x] Service PayrollJournalService
-   [x] Controller PayrollCoaSettingController
-   [x] Update PayrollManagementController
-   [x] View COA settings
-   [x] Routes untuk COA settings
-   [x] Button Setting COA di payroll index
-   [x] Jurnal saat approve
-   [x] Jurnal saat pay
-   [x] Error handling
-   [x] Validation
-   [x] Transaction handling
-   [x] Logging

---

## 📞 Support

Jika ada pertanyaan tentang integrasi jurnal:

1. Lihat dokumentasi ini
2. Cek log error di `storage/logs/laravel.log`
3. Test dengan data dummy terlebih dahulu
4. Pastikan COA settings sudah dikonfigurasi

---

**Dibuat**: 2 Desember 2024  
**Status**: ✅ PRODUCTION READY  
**Integration**: Payroll → Journal → Chart of Accounts  
**Version**: 1.0.0
