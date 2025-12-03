# Sales Invoice Auto Journal - Specification

## 📋 Overview

Spec ini mendefinisikan implementasi sistem jurnal otomatis untuk sales invoice yang akan membuat journal entry secara otomatis saat:

1. Status invoice berubah (draft → confirmed → paid)
2. Ada pembayaran cicilan (installment payment)

## 🎯 Goals

-   **Automation**: Eliminasi input jurnal manual untuk sales invoice
-   **Accuracy**: Mengurangi human error dalam pencatatan jurnal
-   **Traceability**: Setiap invoice memiliki audit trail jurnal yang jelas
-   **Flexibility**: COA dapat dikonfigurasi per outlet
-   **Reliability**: Rollback otomatis jika terjadi error

## 📁 Document Structure

### 1. [requirements.md](./requirements.md)

Dokumen lengkap requirement meliputi:

-   Business Requirements (BR)
-   Functional Requirements (FR)
-   Technical Requirements (TR)
-   User Stories
-   Business Rules
-   Success Criteria
-   Risk Assessment

**Key Highlights:**

-   5 Business Requirements
-   3 Functional Requirements
-   3 Technical Requirements
-   5 User Stories
-   Timeline: 5 weeks

### 2. [design.md](./design.md)

Dokumen technical design meliputi:

-   Architecture Overview
-   Database Design (4 tables)
-   Service Layer Design (2 services)
-   Event System Design (3 events)
-   Journal Creation Logic (4 types)
-   UI Integration
-   Validation Rules
-   Error Handling
-   Performance Considerations
-   Security Considerations
-   Testing Strategy
-   Migration Strategy

**Key Components:**

-   `SalesInvoiceJournalService` - Core service
-   `COASettingService` - Configuration service
-   Event-driven architecture
-   Transaction-based processing

### 3. [tasks.md](./tasks.md)

Breakdown implementasi menjadi 18 tasks dalam 5 phases:

**Phase 1: Foundation (Week 1)**

-   Task 1.1: Database Schema Setup
-   Task 1.2: Create Models
-   Task 1.3: Create COASettingService

**Phase 2: Core Service (Week 2)**

-   Task 2.1: Create SalesInvoiceJournalService
-   Task 2.2: Create Custom Exceptions
-   Task 2.3: Unit Tests for Services

**Phase 3: Event System (Week 2-3)**

-   Task 3.1: Create Events
-   Task 3.2: Create Event Listeners
-   Task 3.3: Integrate Events with Controllers

**Phase 4: UI Integration (Week 3)**

-   Task 4.1: Create COA Settings Page
-   Task 4.2: Enhance Invoice Detail Page
-   Task 4.3: Add Auto Journal Toggle

**Phase 5: Testing & Polish (Week 4)**

-   Task 5.1: Integration Tests
-   Task 5.2: Performance Optimization
-   Task 5.3: Documentation
-   Task 5.4: Seed Data & Demo

## 🔑 Key Features

### 1. Automatic Journal Creation

**Sales Journal (Invoice Confirmed):**

```
Debit:  Piutang Usaha (1300)     = Rp 1.100.000
Credit: Penjualan (4000)         = Rp 1.000.000
Credit: Pajak PPN (2300)         = Rp   100.000
```

**Payment Journal (Invoice Paid):**

```
Debit:  Kas/Bank (1100/1200)    = Rp 1.100.000
Credit: Piutang Usaha (1300)    = Rp 1.100.000
```

**Installment Journal (Partial Payment):**

```
Debit:  Kas/Bank (1100/1200)    = Rp 300.000
Credit: Piutang Usaha (1300)    = Rp 300.000
```

**Reversal Journal (Invoice Cancelled):**

```
Debit:  Penjualan (4000)         = Rp 1.000.000
Debit:  Pajak PPN (2300)         = Rp   100.000
Credit: Piutang Usaha (1300)     = Rp 1.100.000
```

### 2. COA Configuration

Per-outlet configuration untuk:

-   Akun Pendapatan (Revenue)
-   Akun Piutang (Receivable)
-   Akun Kas (Cash)
-   Akun Bank (Bank)
-   Akun Diskon (Discount) - optional
-   Akun Pajak (Tax) - optional

### 3. Event-Driven Architecture

**Events:**

-   `InvoiceStatusChanged` - trigger saat status berubah
-   `InstallmentPaymentCreated` - trigger saat ada pembayaran
-   `InstallmentPaymentDeleted` - trigger saat pembayaran dihapus

**Listeners:**

-   `CreateInvoiceJournalListener` - handle invoice journals
-   `CreateInstallmentJournalListener` - handle installment journals
-   `ReverseInstallmentJournalListener` - handle reversal

### 4. UI Integration

**New Pages:**

-   COA Settings page (`/admin/sales/coa-settings`)

**Enhanced Pages:**

-   Invoice Detail - show related journals
-   Invoice Form - auto journal toggle

## 🗄️ Database Changes

### New Tables

-   `setting_coa_sales` - COA configuration per outlet

### Modified Tables

-   `sales_invoices` - add auto_journal_enabled, sales_journal_id, payment_journal_id
-   `invoice_payment_history` - add journal_entry_id
-   `journal_entries` - add reference_type, reference_id, reversal fields

## 🔒 Security & Permissions

**Required Permissions:**

-   `create_journal` - untuk create journal
-   `reverse_journal` - untuk reverse journal
-   `manage_coa_settings` - untuk manage COA settings

**Audit Trail:**

-   Log semua journal operations
-   Track who, when, what, why
-   Immutable journal records

## ⚡ Performance

**Targets:**

-   Journal creation < 2 seconds
-   Minimal database queries (eager loading)
-   Caching for COA settings
-   Transaction-based processing

**Optimizations:**

-   Database indexes
-   Query optimization
-   Caching strategy
-   Optional queue processing

## 🧪 Testing

**Test Coverage:**

-   Unit Tests (80%+ coverage)
-   Integration Tests (complete workflows)
-   Feature Tests (user flows)
-   Performance Tests (benchmarks)

**Test Scenarios:**

-   Happy path workflows
-   Error scenarios
-   Edge cases
-   Permission checks
-   Performance benchmarks

## 📊 Success Metrics

**Functional:**

-   ✅ Jurnal otomatis dibuat untuk semua status changes
-   ✅ Jurnal otomatis dibuat untuk semua installment payments
-   ✅ COA settings dapat dikonfigurasi per outlet
-   ✅ Jurnal reversal berfungsi dengan benar

**Data Integrity:**

-   ✅ Total debit = total credit di setiap jurnal
-   ✅ Saldo piutang akurat setelah pembayaran
-   ✅ Tidak ada orphaned journal entries

**User Experience:**

-   ✅ Proses berjalan otomatis tanpa input manual
-   ✅ Error handling yang jelas
-   ✅ Audit trail lengkap

**Performance:**

-   ✅ Journal creation < 2 detik
-   ✅ Tidak memblokir UI
-   ✅ Minimal impact pada existing functionality

## 🚀 Implementation Timeline

**Total Duration:** 5 weeks (60 hours)

**Week 1:** Foundation & Database (10 hours)
**Week 2:** Core Service & Events (26 hours)
**Week 3:** UI Integration (12 hours)
**Week 4:** Testing & Polish (16 hours)
**Week 5:** Buffer & Deployment (6 hours)

## 📝 Next Steps

1. **Review Spec** - Review dengan team
2. **Approve Design** - Get approval dari stakeholders
3. **Start Implementation** - Mulai dari Phase 1
4. **Iterative Development** - Implement task by task
5. **Testing** - Test setiap phase
6. **Deployment** - Deploy ke production

## 🔗 Related Specs

-   [PO Installment Payment](../po-installment-payment/) - Similar pattern untuk Purchase Order
-   [Laporan Laba Rugi](../laporan-laba-rugi/) - Menggunakan journal data
-   [Finance Export Import](../finance-export-import-print/) - Export journal data

## 📞 Contact

**Spec Owner:** Finance Team  
**Technical Lead:** Development Team  
**Stakeholders:** Finance Manager, System Administrator

---

**Document Version**: 1.0  
**Created**: November 22, 2024  
**Status**: Ready for Review  
**Priority**: High  
**Complexity**: Medium-High
