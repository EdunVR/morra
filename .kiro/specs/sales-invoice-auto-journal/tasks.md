# Sales Invoice Auto Journal - Implementation Tasks

## Task Breakdown

### Phase 1: Foundation & Database (Week 1)

#### Task 1.1: Database Schema Setup

**Priority:** High  
**Estimated Time:** 4 hours  
**Dependencies:** None

**Subtasks:**

-   [ ] Create migration for `setting_coa_sales` table
-   [ ] Create migration to add auto journal columns to `sales_invoices`
-   [ ] Create migration to add journal reference to `invoice_payment_history`
-   [ ] Create migration to add reversal columns to `journal_entries`
-   [ ] Run migrations on dev environment
-   [ ] Verify all foreign keys and indexes

**Acceptance Criteria:**

-   All migrations run successfully
-   Foreign keys properly configured
-   Indexes created for performance
-   No breaking changes to existing data

**Files to Create:**

-   `database/migrations/2025_11_22_000001_create_setting_coa_sales_table.php`
-   `database/migrations/2025_11_22_000002_add_auto_journal_to_sales_invoices.php`
-   `database/migrations/2025_11_22_000003_add_journal_ref_to_payment_history.php`
-   `database/migrations/2025_11_22_000004_add_reversal_to_journal_entries.php`

---

#### Task 1.2: Create Models

**Priority:** High  
**Estimated Time:** 2 hours  
**Dependencies:** Task 1.1

**Subtasks:**

-   [ ] Create `SettingCOASales` model
-   [ ] Add relationships to `SalesInvoice` model
-   [ ] Add relationships to `InvoicePaymentHistory` model
-   [ ] Add relationships to `JournalEntry` model
-   [ ] Define fillable fields and casts
-   [ ] Add model validation rules

**Acceptance Criteria:**

-   All models created with proper relationships
-   Fillable fields defined
-   Casts configured correctly
-   Relationships work bidirectionally

**Files to Create/Modify:**

-   `app/Models/SettingCOASales.php` (new)
-   `app/Models/SalesInvoice.php` (modify)
-   `app/Models/InvoicePaymentHistory.php` (modify)
-   `app/Models/JournalEntry.php` (modify)

---

#### Task 1.3: Create COASettingService

**Priority:** High  
**Estimated Time:** 4 hours  
**Dependencies:** Task 1.2

**Subtasks:**

-   [ ] Create `COASettingService` class
-   [ ] Implement `getSettings($outletId)` method
-   [ ] Implement `saveSettings($outletId, $data)` method
-   [ ] Implement `validateSettings($settings)` method
-   [ ] Implement `isComplete($outletId)` method
-   [ ] Add caching for settings
-   [ ] Add validation for account types

**Acceptance Criteria:**

-   Service can get/save COA settings
-   Validation works correctly
-   Cache invalidation works
-   Account type validation enforced

**Files to Create:**

-   `app/Services/COASettingService.php`

---

### Phase 2: Core Journal Service (Week 2)

#### Task 2.1: Create SalesInvoiceJournalService

**Priority:** High  
**Estimated Time:** 8 hours  
**Dependencies:** Task 1.3

**Subtasks:**

-   [ ] Create `SalesInvoiceJournalService` class
-   [ ] Implement `createSalesJournal($invoice)` method
-   [ ] Implement `createPaymentJournal($invoice)` method
-   [ ] Implement `createInstallmentJournal($payment)` method
-   [ ] Implement `reverseInvoiceJournals($invoice)` method
-   [ ] Implement `reverseJournal($journalId)` method
-   [ ] Implement `canCreateJournal($invoice)` method
-   [ ] Implement `calculateSalesAmounts($invoice)` method
-   [ ] Add duplicate journal prevention
-   [ ] Add error handling

**Acceptance Criteria:**

-   All journal creation methods work
-   Amounts calculated correctly
-   Reversal logic works properly
-   No duplicate journals created
-   Proper error handling

**Files to Create:**

-   `app/Services/SalesInvoiceJournalService.php`

---

#### Task 2.2: Create Custom Exceptions

**Priority:** Medium  
**Estimated Time:** 2 hours  
**Dependencies:** Task 2.1

**Subtasks:**

-   [ ] Create `COANotConfiguredException`
-   [ ] Create `InvalidAccountTypeException`
-   [ ] Create `DuplicateJournalException`
-   [ ] Create `JournalCreationException`
-   [ ] Add exception handlers
-   [ ] Add user-friendly error messages

**Acceptance Criteria:**

-   All exceptions created
-   Proper error messages
-   Exception handling in place
-   Logging configured

**Files to Create:**

-   `app/Exceptions/COANotConfiguredException.php`
-   `app/Exceptions/InvalidAccountTypeException.php`
-   `app/Exceptions/DuplicateJournalException.php`
-   `app/Exceptions/JournalCreationException.php`

---

#### Task 2.3: Unit Tests for Services

**Priority:** High  
**Estimated Time:** 6 hours  
**Dependencies:** Task 2.1, 2.2

**Subtasks:**

-   [ ] Test COASettingService methods
-   [ ] Test SalesInvoiceJournalService methods
-   [ ] Test amount calculations
-   [ ] Test validation rules
-   [ ] Test error handling
-   [ ] Test duplicate prevention
-   [ ] Test reversal logic
-   [ ] Achieve 80%+ code coverage

**Acceptance Criteria:**

-   All service methods tested
-   Edge cases covered
-   80%+ code coverage
-   All tests passing

**Files to Create:**

-   `tests/Unit/COASettingServiceTest.php`
-   `tests/Unit/SalesInvoiceJournalServiceTest.php`

---

### Phase 3: Event System (Week 2-3)

#### Task 3.1: Create Events

**Priority:** High  
**Estimated Time:** 3 hours  
**Dependencies:** Task 2.1

**Subtasks:**

-   [ ] Create `InvoiceStatusChanged` event
-   [ ] Create `InstallmentPaymentCreated` event
-   [ ] Create `InstallmentPaymentDeleted` event
-   [ ] Add event properties
-   [ ] Add event broadcasting (optional)

**Acceptance Criteria:**

-   All events created
-   Properties defined correctly
-   Events can be dispatched
-   Broadcasting configured (if needed)

**Files to Create:**

-   `app/Events/InvoiceStatusChanged.php`
-   `app/Events/InstallmentPaymentCreated.php`
-   `app/Events/InstallmentPaymentDeleted.php`

---

#### Task 3.2: Create Event Listeners

**Priority:** High  
**Estimated Time:** 4 hours  
**Dependencies:** Task 3.1

**Subtasks:**

-   [ ] Create `CreateInvoiceJournalListener`
-   [ ] Create `CreateInstallmentJournalListener`
-   [ ] Create `ReverseInstallmentJournalListener`
-   [ ] Implement listener logic
-   [ ] Add error handling in listeners
-   [ ] Register listeners in EventServiceProvider

**Acceptance Criteria:**

-   All listeners created
-   Logic implemented correctly
-   Error handling in place
-   Listeners registered

**Files to Create:**

-   `app/Listeners/CreateInvoiceJournalListener.php`
-   `app/Listeners/CreateInstallmentJournalListener.php`
-   `app/Listeners/ReverseInstallmentJournalListener.php`

**Files to Modify:**

-   `app/Providers/EventServiceProvider.php`

---

#### Task 3.3: Integrate Events with Controllers

**Priority:** High  
**Estimated Time:** 3 hours  
**Dependencies:** Task 3.2

**Subtasks:**

-   [ ] Add event dispatch in SalesManagementController
-   [ ] Add event dispatch when status changes
-   [ ] Add event dispatch when payment created
-   [ ] Add event dispatch when payment deleted
-   [ ] Wrap in database transactions
-   [ ] Add logging

**Acceptance Criteria:**

-   Events dispatched at correct times
-   Database transactions work
-   Rollback on failure
-   Proper logging

**Files to Modify:**

-   `app/Http/Controllers/SalesManagementController.php`

---

### Phase 4: UI Integration (Week 3)

#### Task 4.1: Create COA Settings Page

**Priority:** High  
**Estimated Time:** 6 hours  
**Dependencies:** Task 1.3

**Subtasks:**

-   [ ] Create COA settings controller
-   [ ] Create COA settings view
-   [ ] Add form with COA dropdowns
-   [ ] Add validation
-   [ ] Add save functionality
-   [ ] Add success/error notifications
-   [ ] Add route

**Acceptance Criteria:**

-   Settings page accessible
-   Form works correctly
-   Validation works
-   Settings saved properly
-   User-friendly interface

**Files to Create:**

-   `app/Http/Controllers/COASettingController.php`
-   `resources/views/admin/sales/coa-settings.blade.php`

**Files to Modify:**

-   `routes/web.php`

---

#### Task 4.2: Enhance Invoice Detail Page

**Priority:** Medium  
**Estimated Time:** 4 hours  
**Dependencies:** Task 2.1

**Subtasks:**

-   [ ] Add "Jurnal Terkait" section
-   [ ] Display sales journal link
-   [ ] Display payment journal link
-   [ ] Display installment journals list
-   [ ] Add status badges
-   [ ] Add view journal modal/link
-   [ ] Style with Tailwind CSS

**Acceptance Criteria:**

-   Journal section visible
-   Links work correctly
-   Status badges display
-   Responsive design
-   Good UX

**Files to Modify:**

-   `resources/views/admin/penjualan/invoice/index.blade.php`

---

#### Task 4.3: Add Auto Journal Toggle

**Priority:** Low  
**Estimated Time:** 2 hours  
**Dependencies:** Task 4.1

**Subtasks:**

-   [ ] Add checkbox to invoice form
-   [ ] Default to checked
-   [ ] Save preference with invoice
-   [ ] Show warning if disabled
-   [ ] Add tooltip explanation

**Acceptance Criteria:**

-   Checkbox works
-   Preference saved
-   Warning displayed
-   Tooltip helpful

**Files to Modify:**

-   `resources/views/admin/penjualan/invoice/form.blade.php` (if exists)

---

### Phase 5: Testing & Polish (Week 4)

#### Task 5.1: Integration Tests

**Priority:** High  
**Estimated Time:** 6 hours  
**Dependencies:** All previous tasks

**Subtasks:**

-   [ ] Test complete invoice workflow
-   [ ] Test multiple installment payments
-   [ ] Test invoice cancellation
-   [ ] Test COA settings CRUD
-   [ ] Test permission checks
-   [ ] Test error scenarios

**Acceptance Criteria:**

-   All workflows tested
-   Edge cases covered
-   Tests passing
-   No regressions

**Files to Create:**

-   `tests/Feature/SalesInvoiceAutoJournalTest.php`
-   `tests/Feature/COASettingsTest.php`

---

#### Task 5.2: Performance Optimization

**Priority:** Medium  
**Estimated Time:** 4 hours  
**Dependencies:** Task 5.1

**Subtasks:**

-   [ ] Add eager loading
-   [ ] Implement caching
-   [ ] Optimize queries
-   [ ] Add database indexes
-   [ ] Profile performance
-   [ ] Optimize slow queries

**Acceptance Criteria:**

-   Journal creation < 2 seconds
-   Minimal query count
-   Cache working
-   No N+1 queries

---

#### Task 5.3: Documentation

**Priority:** Medium  
**Estimated Time:** 4 hours  
**Dependencies:** All previous tasks

**Subtasks:**

-   [ ] Write user guide
-   [ ] Write API documentation
-   [ ] Write setup guide
-   [ ] Create video tutorial (optional)
-   [ ] Update README
-   [ ] Add inline code comments

**Acceptance Criteria:**

-   Complete documentation
-   Clear instructions
-   Examples provided
-   Easy to follow

**Files to Create:**

-   `.kiro/specs/sales-invoice-auto-journal/USER_GUIDE.md`
-   `.kiro/specs/sales-invoice-auto-journal/API_DOCUMENTATION.md`
-   `.kiro/specs/sales-invoice-auto-journal/SETUP_GUIDE.md`

---

#### Task 5.4: Seed Data & Demo

**Priority:** Low  
**Estimated Time:** 2 hours  
**Dependencies:** Task 5.3

**Subtasks:**

-   [ ] Create seeder for COA settings
-   [ ] Create demo invoices
-   [ ] Create demo payments
-   [ ] Create demo journals
-   [ ] Test on fresh database

**Acceptance Criteria:**

-   Seeder works
-   Demo data realistic
-   Fresh install works
-   Good for testing

**Files to Create:**

-   `database/seeders/COASettingsSeeder.php`
-   `database/seeders/DemoInvoiceJournalSeeder.php`

---

## Summary

**Total Tasks:** 18  
**Total Estimated Time:** 60 hours (3 weeks)  
**Priority Breakdown:**

-   High: 12 tasks
-   Medium: 4 tasks
-   Low: 2 tasks

**Phase Breakdown:**

-   Phase 1 (Foundation): 10 hours
-   Phase 2 (Core Service): 16 hours
-   Phase 3 (Events): 10 hours
-   Phase 4 (UI): 12 hours
-   Phase 5 (Testing): 16 hours

**Critical Path:**

1. Database Setup → Models → Services
2. Events → Listeners → Controller Integration
3. UI → Testing → Documentation

---

**Document Version**: 1.0  
**Created**: November 22, 2024  
**Status**: Ready for Implementation
