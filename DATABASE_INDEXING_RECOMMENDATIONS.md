# Rekomendasi Database Indexing untuk Optimasi Performa

## ⚠️ PENTING

File ini berisi **rekomendasi** indexing untuk meningkatkan performa query database.
**TIDAK ADA PERUBAHAN STRUKTUR TABEL** - hanya menambahkan index untuk mempercepat query.

## 📋 Cara Menggunakan

### Option 1: Manual via phpMyAdmin / MySQL Workbench

Copy-paste SQL commands di bawah ke MySQL client Anda.

### Option 2: Via Laravel Migration

Buat migration baru dan jalankan:

```bash
php artisan make:migration add_performance_indexes
```

---

## 🎯 Index Recommendations

### 1. Table: `produk` (Products)

**Kolom yang Sering Di-Query:**

-   `id_outlet` - Filter by outlet
-   `is_active` - Filter active products
-   `id_kategori` - Join dengan kategori
-   `id_satuan` - Join dengan satuan

**SQL Commands:**

```sql
-- Index untuk filter outlet + active status
ALTER TABLE `produk` ADD INDEX `idx_outlet_active` (`id_outlet`, `is_active`);

-- Index untuk kategori lookup
ALTER TABLE `produk` ADD INDEX `idx_kategori` (`id_kategori`);

-- Index untuk satuan lookup
ALTER TABLE `produk` ADD INDEX `idx_satuan` (`id_satuan`);

-- Index untuk search by kode
ALTER TABLE `produk` ADD INDEX `idx_kode_produk` (`kode_produk`);
```

**Alasan:**

-   Query `WHERE id_outlet = ? AND is_active = 1` akan 10-100x lebih cepat
-   Join dengan kategori dan satuan akan lebih efisien
-   Search by SKU/kode akan instant

---

### 2. Table: `pos_sales` (POS Transactions)

**Kolom yang Sering Di-Query:**

-   `id_outlet` - Filter by outlet
-   `tanggal` - Date range queries
-   `status` - Filter by status
-   `id_penjualan` - Join dengan penjualan

**SQL Commands:**

```sql
-- Composite index untuk filter outlet + date range
ALTER TABLE `pos_sales` ADD INDEX `idx_outlet_tanggal` (`id_outlet`, `tanggal`);

-- Index untuk status filter
ALTER TABLE `pos_sales` ADD INDEX `idx_status` (`status`);

-- Index untuk join dengan penjualan
ALTER TABLE `pos_sales` ADD INDEX `idx_penjualan` (`id_penjualan`);

-- Index untuk search by transaction number
ALTER TABLE `pos_sales` ADD INDEX `idx_no_transaksi` (`no_transaksi`);

-- Index untuk member lookup
ALTER TABLE `pos_sales` ADD INDEX `idx_member` (`id_member`);
```

**Alasan:**

-   History queries dengan filter outlet + date range akan sangat cepat
-   Status filtering untuk dashboard akan instant
-   Search by transaction number akan cepat

---

### 3. Table: `pos_sale_items` (POS Transaction Items)

**Kolom yang Sering Di-Query:**

-   `pos_sale_id` - Join dengan pos_sales
-   `id_produk` - Join dengan produk
-   `tipe` - Filter by type (produk/jasa)

**SQL Commands:**

```sql
-- Index untuk join dengan pos_sales (biasanya sudah ada foreign key)
ALTER TABLE `pos_sale_items` ADD INDEX `idx_pos_sale` (`pos_sale_id`);

-- Index untuk join dengan produk
ALTER TABLE `pos_sale_items` ADD INDEX `idx_produk` (`id_produk`);

-- Composite index untuk filter by sale + type
ALTER TABLE `pos_sale_items` ADD INDEX `idx_sale_tipe` (`pos_sale_id`, `tipe`);
```

**Alasan:**

-   Join dengan pos_sales akan lebih cepat
-   Filter items by type (produk only) akan efisien
-   Margin report queries akan lebih cepat

---

### 4. Table: `penjualan` (Sales/Invoices)

**Kolom yang Sering Di-Query:**

-   `id_outlet` - Filter by outlet
-   `created_at` - Date range queries
-   `id_member` - Customer lookup

**SQL Commands:**

```sql
-- Composite index untuk outlet + date
ALTER TABLE `penjualan` ADD INDEX `idx_outlet_created` (`id_outlet`, `created_at`);

-- Index untuk member lookup
ALTER TABLE `penjualan` ADD INDEX `idx_member` (`id_member`);

-- Index untuk user tracking
ALTER TABLE `penjualan` ADD INDEX `idx_user` (`id_user`);
```

**Alasan:**

-   Sales report dengan filter outlet + date range akan cepat
-   Customer sales history akan instant
-   User performance tracking akan efisien

---

### 5. Table: `penjualan_detail` (Sales Details)

**Kolom yang Sering Di-Query:**

-   `id_penjualan` - Join dengan penjualan
-   `id_produk` - Join dengan produk

**SQL Commands:**

```sql
-- Index untuk join dengan penjualan (biasanya sudah ada)
ALTER TABLE `penjualan_detail` ADD INDEX `idx_penjualan` (`id_penjualan`);

-- Index untuk join dengan produk
ALTER TABLE `penjualan_detail` ADD INDEX `idx_produk` (`id_produk`);

-- Composite index untuk margin calculations
ALTER TABLE `penjualan_detail` ADD INDEX `idx_penjualan_produk` (`id_penjualan`, `id_produk`);
```

**Alasan:**

-   Join dengan penjualan akan lebih cepat
-   Product sales analysis akan efisien
-   Margin report calculations akan lebih cepat

---

### 6. Table: `piutang` (Receivables)

**Kolom yang Sering Di-Query:**

-   `id_penjualan` - Join dengan penjualan
-   `id_member` - Customer lookup
-   `id_outlet` - Filter by outlet
-   `status` - Filter by payment status
-   `tanggal_jatuh_tempo` - Due date queries

**SQL Commands:**

```sql
-- Index untuk join dengan penjualan
ALTER TABLE `piutang` ADD INDEX `idx_penjualan` (`id_penjualan`);

-- Index untuk member lookup
ALTER TABLE `piutang` ADD INDEX `idx_member` (`id_member`);

-- Composite index untuk outlet + status
ALTER TABLE `piutang` ADD INDEX `idx_outlet_status` (`id_outlet`, `status`);

-- Index untuk due date queries
ALTER TABLE `piutang` ADD INDEX `idx_jatuh_tempo` (`tanggal_jatuh_tempo`);

-- Composite index untuk overdue queries
ALTER TABLE `piutang` ADD INDEX `idx_status_tempo` (`status`, `tanggal_jatuh_tempo`);
```

**Alasan:**

-   Piutang lookup dari penjualan akan instant
-   Customer piutang summary akan cepat
-   Overdue piutang report akan efisien

---

### 7. Table: `members` (Customers)

**Kolom yang Sering Di-Query:**

-   `nama` - Search by name
-   `telepon` - Search by phone

**SQL Commands:**

```sql
-- Index untuk search by name
ALTER TABLE `members` ADD INDEX `idx_nama` (`nama`);

-- Index untuk search by phone
ALTER TABLE `members` ADD INDEX `idx_telepon` (`telepon`);
```

**Alasan:**

-   Customer search akan instant
-   Phone lookup akan cepat

---

### 8. Table: `outlets`

**Kolom yang Sering Di-Query:**

-   `is_active` - Filter active outlets

**SQL Commands:**

```sql
-- Index untuk filter active outlets
ALTER TABLE `outlets` ADD INDEX `idx_active` (`is_active`);
```

**Alasan:**

-   Outlet dropdown akan lebih cepat

---

### 9. Table: `hpp_produk` (Product Cost History)

**Kolom yang Sering Di-Query:**

-   `id_produk` - Join dengan produk
-   `stok` - Filter by stock availability

**SQL Commands:**

```sql
-- Composite index untuk produk + stok
ALTER TABLE `hpp_produk` ADD INDEX `idx_produk_stok` (`id_produk`, `stok`);
```

**Alasan:**

-   Stock availability checks akan sangat cepat
-   HPP calculations akan lebih efisien

---

### 10. Table: `chart_of_accounts` (COA)

**Kolom yang Sering Di-Query:**

-   `outlet_id` - Filter by outlet
-   `code` - Lookup by account code
-   `type` - Filter by account type
-   `parent_code` - Hierarchy queries

**SQL Commands:**

```sql
-- Composite index untuk outlet + code
ALTER TABLE `chart_of_accounts` ADD INDEX `idx_outlet_code` (`outlet_id`, `code`);

-- Index untuk type filter
ALTER TABLE `chart_of_accounts` ADD INDEX `idx_type` (`type`);

-- Index untuk hierarchy queries
ALTER TABLE `chart_of_accounts` ADD INDEX `idx_parent_code` (`parent_code`);
```

**Alasan:**

-   Account lookup by code akan instant
-   Hierarchy queries untuk reports akan cepat
-   Type filtering akan efisien

---

### 11. Table: `journal_entries` (Journal Entries)

**Kolom yang Sering Di-Query:**

-   `accounting_book_id` - Filter by book
-   `transaction_date` - Date range queries
-   `reference_type` - Filter by source
-   `reference_number` - Lookup by reference

**SQL Commands:**

```sql
-- Composite index untuk book + date
ALTER TABLE `journal_entries` ADD INDEX `idx_book_date` (`accounting_book_id`, `transaction_date`);

-- Composite index untuk reference lookup
ALTER TABLE `journal_entries` ADD INDEX `idx_reference` (`reference_type`, `reference_number`);

-- Index untuk outlet filter
ALTER TABLE `journal_entries` ADD INDEX `idx_outlet` (`outlet_id`);
```

**Alasan:**

-   Journal queries dengan filter book + date akan cepat
-   Reference lookup untuk audit trail akan instant
-   Outlet-based filtering akan efisien

---

## 📊 Expected Performance Improvements

Setelah menambahkan index-index di atas:

| Query Type               | Before      | After     | Improvement        |
| ------------------------ | ----------- | --------- | ------------------ |
| Product List (by outlet) | 500-1000ms  | 10-50ms   | **10-100x faster** |
| POS History (date range) | 1000-2000ms | 50-100ms  | **10-40x faster**  |
| Sales Report             | 2000-5000ms | 100-300ms | **10-50x faster**  |
| Margin Report            | 3000-8000ms | 200-500ms | **10-40x faster**  |
| Customer Piutang         | 500-1000ms  | 20-50ms   | **10-50x faster**  |
| Account Lookup           | 200-500ms   | 5-10ms    | **20-100x faster** |

---

## 🔍 How to Check Current Indexes

```sql
-- Check indexes untuk table tertentu
SHOW INDEX FROM `produk`;
SHOW INDEX FROM `pos_sales`;
SHOW INDEX FROM `penjualan`;

-- Check semua indexes di database
SELECT
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as COLUMNS
FROM
    INFORMATION_SCHEMA.STATISTICS
WHERE
    TABLE_SCHEMA = 'your_database_name'
GROUP BY
    TABLE_NAME, INDEX_NAME
ORDER BY
    TABLE_NAME, INDEX_NAME;
```

---

## ⚠️ Important Notes

### 1. Index Trade-offs

**Pros:**

-   ✅ Dramatically faster SELECT queries
-   ✅ Faster JOIN operations
-   ✅ Faster WHERE clause filtering
-   ✅ Faster ORDER BY operations

**Cons:**

-   ❌ Slightly slower INSERT/UPDATE/DELETE (minimal impact)
-   ❌ Additional disk space (~5-10% of table size)
-   ❌ Need to maintain indexes

**Verdict:** Untuk aplikasi ERP dengan banyak READ operations, index adalah **MUST HAVE**.

### 2. When to Add Indexes

✅ **DO add index when:**

-   Column is frequently used in WHERE clause
-   Column is used in JOIN conditions
-   Column is used in ORDER BY
-   Column is used in GROUP BY
-   Table has > 1000 rows

❌ **DON'T add index when:**

-   Table has < 100 rows
-   Column has very low cardinality (e.g., boolean with only 2 values)
-   Column is rarely queried
-   Table has very frequent INSERT/UPDATE operations

### 3. Monitoring Index Usage

```sql
-- Check index usage statistics (MySQL 5.6+)
SELECT
    object_schema,
    object_name,
    index_name,
    count_star,
    count_read,
    count_write
FROM
    performance_schema.table_io_waits_summary_by_index_usage
WHERE
    object_schema = 'your_database_name'
ORDER BY
    count_read DESC;
```

---

## 🚀 Implementation Steps

### Step 1: Backup Database

```bash
# Backup sebelum menambahkan indexes
mysqldump -u username -p database_name > backup_before_indexes.sql
```

### Step 2: Add Indexes (Recommended Order)

**Priority 1 - High Impact:**

```sql
-- Tables dengan query paling sering
ALTER TABLE `produk` ADD INDEX `idx_outlet_active` (`id_outlet`, `is_active`);
ALTER TABLE `pos_sales` ADD INDEX `idx_outlet_tanggal` (`id_outlet`, `tanggal`);
ALTER TABLE `penjualan` ADD INDEX `idx_outlet_created` (`id_outlet`, `created_at`);
```

**Priority 2 - Medium Impact:**

```sql
-- Join tables
ALTER TABLE `pos_sale_items` ADD INDEX `idx_pos_sale` (`pos_sale_id`);
ALTER TABLE `penjualan_detail` ADD INDEX `idx_penjualan` (`id_penjualan`);
ALTER TABLE `piutang` ADD INDEX `idx_penjualan` (`id_penjualan`);
```

**Priority 3 - Nice to Have:**

```sql
-- Search and lookup
ALTER TABLE `members` ADD INDEX `idx_nama` (`nama`);
ALTER TABLE `produk` ADD INDEX `idx_kode_produk` (`kode_produk`);
```

### Step 3: Test Performance

```sql
-- Test query performance dengan EXPLAIN
EXPLAIN SELECT * FROM produk WHERE id_outlet = 1 AND is_active = 1;

-- Check execution time
SET profiling = 1;
SELECT * FROM produk WHERE id_outlet = 1 AND is_active = 1;
SHOW PROFILES;
```

### Step 4: Monitor

Monitor aplikasi selama 1-2 hari untuk memastikan:

-   Query lebih cepat
-   Tidak ada error
-   INSERT/UPDATE masih normal

---

## 📝 Laravel Migration Example

Jika ingin menggunakan Laravel migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Produk indexes
        Schema::table('produk', function (Blueprint $table) {
            $table->index(['id_outlet', 'is_active'], 'idx_outlet_active');
            $table->index('id_kategori', 'idx_kategori');
            $table->index('id_satuan', 'idx_satuan');
            $table->index('kode_produk', 'idx_kode_produk');
        });

        // POS Sales indexes
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->index(['id_outlet', 'tanggal'], 'idx_outlet_tanggal');
            $table->index('status', 'idx_status');
            $table->index('id_penjualan', 'idx_penjualan');
            $table->index('no_transaksi', 'idx_no_transaksi');
            $table->index('id_member', 'idx_member');
        });

        // Add more tables as needed...
    }

    public function down()
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropIndex('idx_outlet_active');
            $table->dropIndex('idx_kategori');
            $table->dropIndex('idx_satuan');
            $table->dropIndex('idx_kode_produk');
        });

        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropIndex('idx_outlet_tanggal');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_penjualan');
            $table->dropIndex('idx_no_transaksi');
            $table->dropIndex('idx_member');
        });

        // Drop more indexes as needed...
    }
};
```

---

## ✅ Conclusion

Menambahkan indexes adalah cara **paling efektif** untuk meningkatkan performa database tanpa mengubah struktur tabel atau kode aplikasi.

**Recommended Action:**

1. Backup database
2. Add Priority 1 indexes first
3. Test performance
4. Add Priority 2 & 3 indexes gradually
5. Monitor and adjust

**Expected Result:**

-   10-100x faster queries
-   Better user experience
-   Lower server load
-   Happier users 😊

---

**Catatan:** Indexes ini adalah **rekomendasi** berdasarkan analisis query patterns. Anda bisa adjust sesuai kebutuhan spesifik aplikasi Anda.
