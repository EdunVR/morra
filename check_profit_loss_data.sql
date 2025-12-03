-- ============================================
-- Script untuk Mengecek Data Laporan Laba Rugi
-- ============================================

-- 1. CEK AKUN REVENUE (PENDAPATAN)
-- ============================================
SELECT 
    'AKUN REVENUE' as kategori,
    coa.id,
    coa.code,
    coa.name,
    coa.type,
    coa.parent_id,
    coa.level,
    coa.status,
    COUNT(DISTINCT jed.id) as jumlah_transaksi,
    SUM(jed.credit - jed.debit) as saldo
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id AND je.status = 'posted'
WHERE coa.type = 'revenue'
GROUP BY coa.id, coa.code, coa.name, coa.type, coa.parent_id, coa.level, coa.status
ORDER BY coa.code;

-- 2. CEK AKUN OTHER REVENUE (PENDAPATAN LAIN)
-- ============================================
SELECT 
    'AKUN OTHER REVENUE' as kategori,
    coa.id,
    coa.code,
    coa.name,
    coa.type,
    coa.parent_id,
    coa.level,
    coa.status,
    COUNT(DISTINCT jed.id) as jumlah_transaksi,
    SUM(jed.credit - jed.debit) as saldo
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id AND je.status = 'posted'
WHERE coa.type = 'otherrevenue'
GROUP BY coa.id, coa.code, coa.name, coa.type, coa.parent_id, coa.level, coa.status
ORDER BY coa.code;

-- 3. CEK AKUN EXPENSE (BEBAN OPERASIONAL)
-- ============================================
SELECT 
    'AKUN EXPENSE' as kategori,
    coa.id,
    coa.code,
    coa.name,
    coa.type,
    coa.parent_id,
    coa.level,
    coa.status,
    COUNT(DISTINCT jed.id) as jumlah_transaksi,
    SUM(jed.debit - jed.credit) as saldo
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id AND je.status = 'posted'
WHERE coa.type = 'expense'
GROUP BY coa.id, coa.code, coa.name, coa.type, coa.parent_id, coa.level, coa.status
ORDER BY coa.code;

-- 4. CEK AKUN OTHER EXPENSE (BEBAN LAIN)
-- ============================================
SELECT 
    'AKUN OTHER EXPENSE' as kategori,
    coa.id,
    coa.code,
    coa.name,
    coa.type,
    coa.parent_id,
    coa.level,
    coa.status,
    COUNT(DISTINCT jed.id) as jumlah_transaksi,
    SUM(jed.debit - jed.credit) as saldo
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id AND je.status = 'posted'
WHERE coa.type = 'otherexpense'
GROUP BY coa.id, coa.code, coa.name, coa.type, coa.parent_id, coa.level, coa.status
ORDER BY coa.code;

-- 5. CEK JURNAL DARI PENJUALAN (SALES INVOICE)
-- ============================================
SELECT 
    'JURNAL PENJUALAN' as kategori,
    je.id,
    je.transaction_number,
    je.transaction_date,
    je.description,
    je.reference_type,
    je.reference_number,
    je.status,
    je.total_debit,
    je.total_credit,
    coa.code as akun_code,
    coa.name as akun_name,
    coa.type as akun_type,
    jed.debit,
    jed.credit
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.reference_type = 'sales_invoice'
ORDER BY je.transaction_date DESC, je.id, jed.id
LIMIT 50;

-- 6. CEK JURNAL DARI PEMBELIAN (PURCHASE ORDER)
-- ============================================
SELECT 
    'JURNAL PEMBELIAN' as kategori,
    je.id,
    je.transaction_number,
    je.transaction_date,
    je.description,
    je.reference_type,
    je.reference_number,
    je.status,
    je.total_debit,
    je.total_credit,
    coa.code as akun_code,
    coa.name as akun_name,
    coa.type as akun_type,
    jed.debit,
    jed.credit
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.reference_type = 'purchase_order'
ORDER BY je.transaction_date DESC, je.id, jed.id
LIMIT 50;

-- 7. CEK JURNAL PENYUSUTAN (DEPRECIATION)
-- ============================================
SELECT 
    'JURNAL PENYUSUTAN' as kategori,
    je.id,
    je.transaction_number,
    je.transaction_date,
    je.description,
    je.reference_type,
    je.reference_number,
    je.status,
    je.total_debit,
    je.total_credit,
    coa.code as akun_code,
    coa.name as akun_name,
    coa.type as akun_type,
    jed.debit,
    jed.credit
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.reference_type = 'depreciation'
ORDER BY je.transaction_date DESC, je.id, jed.id
LIMIT 50;

-- 8. SUMMARY JURNAL PER REFERENCE TYPE
-- ============================================
SELECT 
    'SUMMARY JURNAL' as kategori,
    je.reference_type,
    je.status,
    COUNT(*) as jumlah_jurnal,
    SUM(je.total_debit) as total_debit,
    SUM(je.total_credit) as total_credit,
    MIN(je.transaction_date) as tanggal_awal,
    MAX(je.transaction_date) as tanggal_akhir
FROM journal_entries je
GROUP BY je.reference_type, je.status
ORDER BY je.reference_type, je.status;

-- 9. CEK BALANCE JURNAL (DEBIT = CREDIT)
-- ============================================
SELECT 
    'BALANCE CHECK' as kategori,
    je.id,
    je.transaction_number,
    je.transaction_date,
    je.status,
    je.total_debit,
    je.total_credit,
    (je.total_debit - je.total_credit) as selisih,
    CASE 
        WHEN ABS(je.total_debit - je.total_credit) < 0.01 THEN 'BALANCE'
        ELSE 'TIDAK BALANCE'
    END as status_balance
FROM journal_entries je
WHERE je.status = 'posted'
    AND ABS(je.total_debit - je.total_credit) >= 0.01
ORDER BY ABS(je.total_debit - je.total_credit) DESC;

-- 10. LAPORAN LABA RUGI SEDERHANA (BULAN INI)
-- ============================================
SELECT 
    'LABA RUGI BULAN INI' as kategori,
    'PENDAPATAN' as jenis,
    SUM(CASE WHEN coa.type = 'revenue' THEN jed.credit - jed.debit ELSE 0 END) as revenue,
    SUM(CASE WHEN coa.type = 'otherrevenue' THEN jed.credit - jed.debit ELSE 0 END) as other_revenue,
    SUM(CASE WHEN coa.type IN ('revenue', 'otherrevenue') THEN jed.credit - jed.debit ELSE 0 END) as total_revenue
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.status = 'posted'
    AND je.transaction_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    AND je.transaction_date <= LAST_DAY(CURDATE())

UNION ALL

SELECT 
    'LABA RUGI BULAN INI' as kategori,
    'BEBAN' as jenis,
    SUM(CASE WHEN coa.type = 'expense' THEN jed.debit - jed.credit ELSE 0 END) as expense,
    SUM(CASE WHEN coa.type = 'otherexpense' THEN jed.debit - jed.credit ELSE 0 END) as other_expense,
    SUM(CASE WHEN coa.type IN ('expense', 'otherexpense') THEN jed.debit - jed.credit ELSE 0 END) as total_expense
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.status = 'posted'
    AND je.transaction_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    AND je.transaction_date <= LAST_DAY(CURDATE());

-- 11. CEK AKUN YANG TIDAK MEMILIKI TRANSAKSI
-- ============================================
SELECT 
    'AKUN TANPA TRANSAKSI' as kategori,
    coa.code,
    coa.name,
    coa.type,
    coa.status
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
WHERE coa.type IN ('revenue', 'expense', 'otherrevenue', 'otherexpense')
    AND coa.status = 'active'
    AND jed.id IS NULL
ORDER BY coa.type, coa.code;

-- 12. CEK TRANSAKSI TERAKHIR PER AKUN
-- ============================================
SELECT 
    'TRANSAKSI TERAKHIR' as kategori,
    coa.code,
    coa.name,
    coa.type,
    MAX(je.transaction_date) as transaksi_terakhir,
    COUNT(DISTINCT jed.id) as jumlah_transaksi
FROM chart_of_accounts coa
LEFT JOIN journal_entry_details jed ON coa.id = jed.account_id
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id AND je.status = 'posted'
WHERE coa.type IN ('revenue', 'expense', 'otherrevenue', 'otherexpense')
    AND coa.status = 'active'
GROUP BY coa.id, coa.code, coa.name, coa.type
ORDER BY coa.type, coa.code;

-- 13. CEK OUTLET DAN JURNAL
-- ============================================
SELECT 
    'JURNAL PER OUTLET' as kategori,
    o.id_outlet,
    o.nama_outlet,
    COUNT(DISTINCT je.id) as jumlah_jurnal,
    SUM(je.total_debit) as total_debit,
    SUM(je.total_credit) as total_credit,
    MIN(je.transaction_date) as tanggal_awal,
    MAX(je.transaction_date) as tanggal_akhir
FROM outlets o
LEFT JOIN journal_entries je ON o.id_outlet = je.outlet_id AND je.status = 'posted'
GROUP BY o.id_outlet, o.nama_outlet
ORDER BY o.nama_outlet;

-- 14. CEK BUKU AKUNTANSI DAN JURNAL
-- ============================================
SELECT 
    'JURNAL PER BUKU' as kategori,
    ab.id,
    ab.code,
    ab.name,
    ab.type,
    ab.status,
    COUNT(DISTINCT je.id) as jumlah_jurnal,
    SUM(je.total_debit) as total_debit,
    SUM(je.total_credit) as total_credit
FROM accounting_books ab
LEFT JOIN journal_entries je ON ab.id = je.book_id AND je.status = 'posted'
GROUP BY ab.id, ab.code, ab.name, ab.type, ab.status
ORDER BY ab.code;

-- 15. CEK KONSISTENSI DATA
-- ============================================
-- Cek apakah ada journal entry detail tanpa journal entry
SELECT 
    'ORPHAN JOURNAL DETAILS' as kategori,
    COUNT(*) as jumlah
FROM journal_entry_details jed
LEFT JOIN journal_entries je ON jed.journal_entry_id = je.id
WHERE je.id IS NULL;

-- Cek apakah ada journal entry detail dengan account yang tidak aktif
SELECT 
    'DETAIL DENGAN AKUN NON-AKTIF' as kategori,
    COUNT(*) as jumlah
FROM journal_entry_details jed
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE coa.status != 'active';

-- Cek apakah ada journal entry dengan outlet yang tidak aktif
SELECT 
    'JURNAL DENGAN OUTLET NON-AKTIF' as kategori,
    COUNT(*) as jumlah
FROM journal_entries je
JOIN outlets o ON je.outlet_id = o.id_outlet
WHERE o.status != 'active';
