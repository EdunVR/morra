# Sales Invoice Auto Journal - Technical Design

## Architecture Overview

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Sales Invoice │    │   Event System   │    │ Journal Service │
│                 │───▶│                  │───▶│                 │
│ Status Changes  │    │ Laravel Events   │    │ Auto Creation   │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │
                                ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│ Payment History │    │   COA Settings   │    │ Journal Entries │
│                 │    │                  │    │                 │
│ Installments    │    │ Account Mapping  │    │ Posted Journals │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## Database Design

### New Tables

#### 1. setting_coa_sales

Menyimpan konfigurasi COA untuk sales invoice per outlet.

**Columns:**

-   `id` - Primary key
-   `outlet_id` - Foreign key to outlets
-   `revenue_account_id` - Akun Pendapatan (4000)
-   `receivable_account_id` - Akun Piutang (1300)
-   `cash_account_id` - Akun Kas (1100)
-   `bank_account_id` - Akun Bank (1200)
-   `discount_account_id` - Akun Diskon (optional)
-   `tax_account_id` - Akun Pajak (optional)
-   `is_active` - Status aktif
-   `created_at`, `updated_at` - Timestamps

**Indexes:**

-   UNIQUE on `outlet_id`
-   Foreign keys to all account_id columns

### Modified Tables

#### 1. sales_invoices

**New Columns:**

-   `auto_journal_enabled` - Boolean, default TRUE
-   `sales_journal_id` - Reference to sales journal entry
-   `payment_journal_id` - Reference to full payment journal entry

#### 2. invoice_payment_history

**New Columns:**

-   `journal_entry_id` - Reference to installment payment journal

#### 3. journal_entries

**New Columns:**

-   `reference_type` - ENUM: 'sales_invoice', 'invoice_payment', 'manual', 'other'
-   `reference_id` - ID of referenced record
-   `is_reversal` - Boolean, is this a reversal journal
-   `reversed_journal_id` - Original journal if this is reversal
-   `reversed_at` - Timestamp when reversed
-   `reversed_by` - User who reversed

## Service Layer Architecture

### 1. SalesInvoiceJournalService

Main service untuk handle auto journal creation.

**Responsibilities:**

-   Create sales journal saat invoice confirmed
-   Create payment journal saat invoice paid
-   Create installment journal saat ada cicilan
-   Reverse journals saat invoice cancelled
-   Validate COA settings
-   Calculate journal amounts

**Key Methods:**

```php
// Create sales journal (Debit: Piutang, Credit: Penjualan)
public function createSalesJournal(SalesInvoice $invoice): ?JournalEntry

// Create full payment journal (Debit: Kas/Bank, Credit: Piutang)
public function createPaymentJournal(SalesInvoice $invoice): ?JournalEntry

// Create installment payment journal (Debit: Kas/Bank, Credit: Piutang)
public function createInstallmentJournal(InvoicePaymentHistory $payment): ?JournalEntry

// Reverse all journals related to invoice
public function reverseInvoiceJournals(SalesInvoice $invoice): array

// Check if journal can be created
protected function canCreateJournal(SalesInvoice $invoice): bool

// Calculate amounts for journal
protected function calculateSalesAmounts(SalesInvoice $invoice): array
```

### 2. COASettingService

Service untuk manage COA settings.

**Responsibilities:**

-   Get COA settings by outlet
-   Validate account types
-   Create/update COA settings
-   Check if settings complete

**Key Methods:**

```php
// Get COA settings for outlet
public function getSettings(int $outletId): ?SettingCOASales

// Validate all accounts configured correctly
public function validateSettings(SettingCOASales $settings): bool

// Create or update settings
public function saveSettings(int $outletId, array $data): SettingCOASales

// Check if settings complete for auto journal
public function isComplete(int $outletId): bool
```

## Event System Design

### Events & Listeners

#### 1. InvoiceStatusChanged Event

**Triggered when:** Invoice status changes
**Payload:**

-   `$invoice` - SalesInvoice model
-   `$oldStatus` - Previous status
-   `$newStatus` - New status

**Listener:** CreateInvoiceJournalListener

**Logic:**

```php
if ($newStatus === 'confirmed' && $oldStatus === 'draft') {
    // Create sales journal
    $this->journalService->createSalesJournal($invoice);
}

if ($newStatus === 'paid' && $oldStatus === 'confirmed') {
    // Create payment journal
    $this->journalService->createPaymentJournal($invoice);
}

if ($newStatus === 'cancelled') {
    // Reverse all journals
    $this->journalService->reverseInvoiceJournals($invoice);
}
```

#### 2. InstallmentPaymentCreated Event

**Triggered when:** New installment payment added
**Payload:**

-   `$payment` - InvoicePaymentHistory model

**Listener:** CreateInstallmentJournalListener

**Logic:**

```php
// Create installment payment journal
$journal = $this->journalService->createInstallmentJournal($payment);

// Update payment with journal reference
$payment->update(['journal_entry_id' => $journal->id]);
```

#### 3. InstallmentPaymentDeleted Event

**Triggered when:** Installment payment deleted
**Payload:**

-   `$payment` - InvoicePaymentHistory model

**Listener:** ReverseInstallmentJournalListener

**Logic:**

```php
if ($payment->journal_entry_id) {
    // Reverse the journal
    $this->journalService->reverseJournal($payment->journal_entry_id);
}
```

## Journal Creation Logic

### 1. Sales Journal (Invoice Confirmed)

**Scenario:** Invoice status berubah dari draft ke confirmed

**Journal Entry:**

```
Transaction Date: Invoice Date
Description: Penjualan - Invoice #INV-001
Reference: sales_invoice #123

Details:
  Debit:  Piutang Usaha (1300)     = Rp 1.100.000
  Credit: Penjualan (4000)         = Rp 1.000.000
  Credit: Pajak PPN (2300)         = Rp   100.000
```

**Calculation:**

-   Total = subtotal + tax - discount
-   Piutang = Total
-   Penjualan = Subtotal - Discount
-   Pajak = Tax amount

### 2. Payment Journal (Invoice Paid Full)

**Scenario:** Invoice status berubah dari confirmed ke paid (lunas)

**Journal Entry:**

```
Transaction Date: Payment Date
Description: Pembayaran - Invoice #INV-001
Reference: sales_invoice #123

Details:
  Debit:  Kas/Bank (1100/1200)    = Rp 1.100.000
  Credit: Piutang Usaha (1300)    = Rp 1.100.000
```

**Account Selection:**

-   Kas (1100) jika payment_method = 'cash'
-   Bank (1200) jika payment_method = 'transfer'

### 3. Installment Payment Journal

**Scenario:** Ada pembayaran cicilan

**Journal Entry:**

```
Transaction Date: Payment Date
Description: Cicilan #1 - Invoice #INV-001
Reference: invoice_payment #456

Details:
  Debit:  Kas/Bank (1100/1200)    = Rp 300.000
  Credit: Piutang Usaha (1300)    = Rp 300.000
```

**Notes:**

-   Setiap cicilan membuat jurnal terpisah
-   Piutang berkurang sesuai jumlah cicilan
-   Reference ke payment history

### 4. Reversal Journal (Invoice Cancelled)

**Scenario:** Invoice dibatalkan (cancelled)

**Original Sales Journal:**

```
Debit:  Piutang Usaha (1300)     = Rp 1.100.000
Credit: Penjualan (4000)         = Rp 1.000.000
Credit: Pajak PPN (2300)         = Rp   100.000
```

**Reversal Journal:**

```
Transaction Date: Cancellation Date
Description: Reversal - Penjualan - Invoice #INV-001
Reference: sales_invoice #123
Is Reversal: TRUE
Reversed Journal ID: 789

Details:
  Debit:  Penjualan (4000)         = Rp 1.000.000
  Debit:  Pajak PPN (2300)         = Rp   100.000
  Credit: Piutang Usaha (1300)     = Rp 1.100.000
```

**Process:**

1. Create reversal journal (swap debit/credit)
2. Mark original journal as reversed
3. Link reversal to original journal

## UI Integration

### 1. COA Settings Page

**Location:** `/admin/sales/coa-settings`

**Features:**

-   Form untuk configure COA per outlet
-   Dropdown COA filtered by account type
-   Validation real-time
-   Save settings

**Fields:**

-   Akun Pendapatan (Revenue) - required
-   Akun Piutang (Receivable) - required
-   Akun Kas (Cash) - required
-   Akun Bank (Bank) - required
-   Akun Diskon (Discount) - optional
-   Akun Pajak (Tax) - optional

### 2. Invoice Detail Page Enhancement

**New Section:** "Jurnal Terkait"

**Display:**

-   Sales Journal link (if exists)
-   Payment Journal link (if exists)
-   List of installment journals
-   Status badge (Posted/Reversed)

**Actions:**

-   View journal detail (modal/new page)
-   Print journal
-   Export journal

### 3. Invoice Form Enhancement

**New Field:** "Auto Journal"

**Options:**

-   Checkbox: "Buat jurnal otomatis"
-   Default: checked
-   Can be disabled per invoice

## Validation Rules

### 1. COA Settings Validation

**Rules:**

-   Revenue account must be type 'revenue'
-   Receivable account must be type 'asset'
-   Cash account must be type 'asset'
-   Bank account must be type 'asset'
-   All accounts must belong to same outlet
-   All accounts must be active
-   Required accounts: revenue, receivable, cash, bank

### 2. Journal Creation Validation

**Pre-conditions:**

-   Invoice must have auto_journal_enabled = true
-   COA settings must be complete
-   Invoice must have valid amounts
-   User must have permission 'create_journal'
-   No duplicate journal for same event

**Amount Validation:**

-   Total debit must equal total credit
-   All amounts must be positive
-   Amounts must match invoice totals

### 3. Reversal Validation

**Rules:**

-   Original journal must exist
-   Original journal must be 'posted'
-   Original journal must not be already reversed
-   User must have permission 'reverse_journal'

## Error Handling

### 1. COA Not Configured

**Error:** COANotConfiguredException

**Message:** "COA untuk sales invoice belum dikonfigurasi. Silakan setup di menu COA Settings."

**Action:**

-   Log error
-   Show notification to user
-   Provide link to COA settings page
-   Invoice still saved, journal not created

### 2. Invalid Account Type

**Error:** InvalidAccountTypeException

**Message:** "Akun yang dipilih tidak sesuai dengan tipe yang dibutuhkan."

**Action:**

-   Prevent saving COA settings
-   Show validation error
-   Highlight invalid field

### 3. Duplicate Journal

**Error:** DuplicateJournalException

**Message:** "Jurnal untuk transaksi ini sudah dibuat sebelumnya."

**Action:**

-   Skip journal creation
-   Log warning
-   Continue with invoice processing

### 4. Journal Creation Failed

**Error:** JournalCreationException

**Message:** "Gagal membuat jurnal otomatis. Error: [detail]"

**Action:**

-   Rollback invoice status change
-   Log error with full stack trace
-   Show error to user
-   Send notification to admin

## Performance Considerations

### 1. Database Transactions

**Strategy:** Wrap invoice update + journal creation in single transaction

```php
DB::transaction(function () use ($invoice) {
    // Update invoice status
    $invoice->update(['status' => 'confirmed']);

    // Create journal
    $journal = $this->journalService->createSalesJournal($invoice);

    // Update invoice with journal reference
    $invoice->update(['sales_journal_id' => $journal->id]);
});
```

**Benefits:**

-   Atomic operation
-   Rollback if journal fails
-   Data consistency guaranteed

### 2. Eager Loading

**Optimize queries:**

```php
$invoice = SalesInvoice::with([
    'outlet',
    'salesJournal',
    'paymentJournal',
    'paymentHistory.journalEntry'
])->find($id);
```

### 3. Caching COA Settings

**Strategy:** Cache COA settings per outlet

```php
$coaSettings = Cache::remember(
    "coa_settings_outlet_{$outletId}",
    3600, // 1 hour
    fn() => SettingCOASales::where('outlet_id', $outletId)->first()
);
```

**Cache Invalidation:**

-   When COA settings updated
-   When accounts modified
-   Manual cache clear

### 4. Queue Processing (Optional)

**For high-volume scenarios:**

```php
// Dispatch job instead of immediate processing
CreateInvoiceJournalJob::dispatch($invoice);
```

**Benefits:**

-   Non-blocking UI
-   Better scalability
-   Retry on failure

## Security Considerations

### 1. Permission Checks

**Required Permissions:**

-   `create_journal` - untuk create journal
-   `reverse_journal` - untuk reverse journal
-   `manage_coa_settings` - untuk manage COA settings

**Implementation:**

```php
if (!auth()->user()->can('create_journal')) {
    throw new UnauthorizedException();
}
```

### 2. Audit Trail

**Log all journal operations:**

-   Who created the journal
-   When it was created
-   What triggered it
-   Original values
-   Changes made

**Implementation:**

```php
activity()
    ->performedOn($journal)
    ->causedBy(auth()->user())
    ->withProperties([
        'invoice_id' => $invoice->id,
        'trigger' => 'status_change',
        'old_status' => $oldStatus,
        'new_status' => $newStatus,
    ])
    ->log('journal_created');
```

### 3. Data Validation

**Sanitize all inputs:**

-   Validate amounts are numeric
-   Validate dates are valid
-   Validate account IDs exist
-   Prevent SQL injection
-   Prevent XSS in descriptions

## Testing Strategy

### 1. Unit Tests

**Test Coverage:**

-   SalesInvoiceJournalService methods
-   COASettingService methods
-   Amount calculations
-   Validation rules
-   Error handling

**Example Tests:**

```php
test_creates_sales_journal_when_invoice_confirmed()
test_creates_payment_journal_when_invoice_paid()
test_creates_installment_journal_for_payment()
test_reverses_journals_when_invoice_cancelled()
test_validates_coa_settings_correctly()
test_calculates_amounts_correctly()
test_prevents_duplicate_journals()
test_handles_missing_coa_settings()
```

### 2. Integration Tests

**Test Scenarios:**

-   Complete invoice workflow with journals
-   Multiple installment payments
-   Invoice cancellation with reversal
-   COA settings CRUD operations
-   Permission checks

**Example Tests:**

```php
test_complete_invoice_workflow_creates_all_journals()
test_multiple_installments_create_separate_journals()
test_cancelled_invoice_reverses_all_journals()
test_unauthorized_user_cannot_create_journal()
```

### 3. Feature Tests

**Test User Flows:**

-   Create invoice → confirm → journals created
-   Create invoice → add payments → journals created
-   Create invoice → cancel → journals reversed
-   Configure COA settings → save → validate

### 4. Performance Tests

**Benchmarks:**

-   Journal creation time < 2 seconds
-   Bulk invoice processing
-   Database query count
-   Memory usage

## Migration Strategy

### Phase 1: Database Setup

**Steps:**

1. Create `setting_coa_sales` table
2. Add columns to `sales_invoices`
3. Add columns to `invoice_payment_history`
4. Add columns to `journal_entries`
5. Create indexes

**Migration Files:**

-   `2025_11_22_000001_create_setting_coa_sales_table.php`
-   `2025_11_22_000002_add_auto_journal_to_sales_invoices.php`
-   `2025_11_22_000003_add_journal_ref_to_payment_history.php`
-   `2025_11_22_000004_add_reversal_to_journal_entries.php`

### Phase 2: Service Layer

**Steps:**

1. Create SalesInvoiceJournalService
2. Create COASettingService
3. Create Events & Listeners
4. Add validation rules
5. Add error handling

### Phase 3: UI Integration

**Steps:**

1. Create COA settings page
2. Add journal links to invoice detail
3. Add auto journal checkbox to invoice form
4. Add journal status badges
5. Add error notifications

### Phase 4: Testing & Deployment

**Steps:**

1. Run all tests
2. Test on staging environment
3. Create seed data for COA settings
4. Train users
5. Deploy to production
6. Monitor for issues

## Rollback Plan

**If issues occur:**

1. **Disable auto journal:**

    ```php
    // Set all invoices to manual journal
    SalesInvoice::query()->update(['auto_journal_enabled' => false]);
    ```

2. **Revert migrations:**

    ```bash
    php artisan migrate:rollback --step=4
    ```

3. **Remove event listeners:**

    ```php
    // Comment out listeners in EventServiceProvider
    ```

4. **Restore from backup:**
    - Database backup before deployment
    - Code backup in git

---

**Document Version**: 1.0  
**Created**: November 22, 2024  
**Status**: Draft  
**Author**: System Architect
