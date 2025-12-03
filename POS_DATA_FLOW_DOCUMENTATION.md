# POS Data Flow - Dokumentasi Lengkap

## Overview

Dokumentasi lengkap tentang kemana data transaksi POS disimpan dan bagaimana alur integrasinya dengan sistem ERP.

---

## Alur Penyimpanan Data

Ketika tombol "Bayar & Cetak" diklik, data transaksi POS akan disimpan ke **MULTIPLE TABLES** untuk integrasi penuh dengan sistem ERP:

```
┌─────────────────────────────────────────────────────────────────┐
│                    POS Transaction Flow                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │  1. pos_sales   │ ← Transaksi POS utama
                    └─────────────────┘
                              │
                ┌─────────────┼─────────────┐
                ▼             ▼             ▼
        ┌──────────┐  ┌──────────┐  ┌──────────┐
        │ 2. Items │  │ 3. Stock │  │ 4. Sales │
        └──────────┘  └──────────┘  └──────────┘
                              │
                ┌─────────────┼─────────────┐
                ▼             ▼             ▼
        ┌──────────┐  ┌──────────┐  ┌──────────┐
        │ 5. Piut. │  │ 6. Jurnal│  │ 7. HPP   │
        └──────────┘  └──────────┘  └──────────┘
```

---

## 1. Tabel pos_sales (Transaksi POS Utama)

### Lokasi

`pos_sales` table

### Data yang Disimpan

```php
PosSale::create([
    'no_transaksi' => 'POS-20251201-001',      // Auto-generated
    'tanggal' => '2025-12-01 10:30:00',
    'id_outlet' => 1,
    'id_member' => 456,                         // Nullable
    'id_user' => 2,                             // User yang login
    'subtotal' => 20000,
    'diskon_persen' => 10,
    'diskon_nominal' => 0,
    'total_diskon' => 2000,
    'ppn' => 1800,
    'total' => 19800,
    'jenis_pembayaran' => 'cash',               // cash/transfer/qris
    'jumlah_bayar' => 20000,
    'kembalian' => 200,
    'status' => 'lunas',                        // lunas/menunggu
    'catatan' => 'Transaksi POS',
    'is_bon' => false,
    'id_penjualan' => 789                       // Link ke tabel penjualan
]);
```

### Fungsi

-   **Primary record** untuk transaksi POS
-   Menyimpan semua informasi header transaksi
-   Digunakan untuk laporan POS
-   Link ke sistem penjualan lama via `id_penjualan`

---

## 2. Tabel pos_sale_items (Detail Item)

### Lokasi

`pos_sale_items` table

### Data yang Disimpan

```php
foreach ($items as $item) {
    PosSaleItem::create([
        'pos_sale_id' => $posSale->id,
        'id_produk' => 123,
        'nama_produk' => 'Product Name',
        'sku' => 'PRD001',
        'kuantitas' => 2,
        'satuan' => 'pcs',
        'harga' => 10000,
        'subtotal' => 20000,
        'tipe' => 'produk'                      // produk/jasa
    ]);
}
```

### Fungsi

-   Menyimpan detail setiap item dalam transaksi
-   Digunakan untuk laporan detail penjualan
-   Tracking produk yang terjual
-   Support untuk produk dan jasa

---

## 3. Tabel penjualan (Kompatibilitas Sistem Lama)

### Lokasi

`penjualan` table

### Data yang Disimpan

```php
Penjualan::create([
    'id_member' => 456,
    'id_outlet' => 1,
    'total_item' => 2,
    'total_harga' => 19800,
    'total_diskon' => 2000,
    'diskon' => 10,
    'bayar' => 19800,
    'diterima' => 20000,
    'id_user' => 2,
    'created_at' => '2025-12-01 10:30:00'
]);
```

### Fungsi

-   **Backward compatibility** dengan sistem penjualan existing
-   Memastikan laporan lama tetap berfungsi
-   Integrasi dengan modul penjualan yang sudah ada

---

## 4. Tabel penjualan_detail (Detail Penjualan)

### Lokasi

`penjualan_detail` table

### Data yang Disimpan

```php
foreach ($items as $item) {
    if ($item['tipe'] === 'produk') {
        PenjualanDetail::create([
            'id_penjualan' => $penjualan->id_penjualan,
            'id_produk' => 123,
            'harga_jual' => 10000,
            'jumlah' => 2,
            'diskon' => 10,
            'subtotal' => 20000,
            'hpp' => 7500                       // Calculated HPP
        ]);
    }
}
```

### Fungsi

-   Detail item untuk sistem penjualan lama
-   Menyimpan HPP untuk perhitungan profit
-   Digunakan untuk laporan laba rugi

---

## 5. Pengurangan Stok (hpp_produk)

### Lokasi

`hpp_produk` table (via FIFO method)

### Proses

```php
$produk->reduceStock($quantity);
```

### Yang Terjadi

1. Sistem mencari stok masuk tertua (FIFO)
2. Mengurangi qty_keluar di `hpp_produk`
3. Update stok real-time
4. Hitung HPP berdasarkan FIFO

### Contoh Data

```php
// Sebelum transaksi
hpp_produk: [
    { id_produk: 123, qty_masuk: 10, qty_keluar: 0, hpp: 7500 },
    { id_produk: 123, qty_masuk: 5, qty_keluar: 0, hpp: 8000 }
]

// Setelah transaksi (jual 2 pcs)
hpp_produk: [
    { id_produk: 123, qty_masuk: 10, qty_keluar: 2, hpp: 7500 },  // FIFO
    { id_produk: 123, qty_masuk: 5, qty_keluar: 0, hpp: 8000 }
]
```

### Fungsi

-   **Real-time stock management**
-   FIFO costing method
-   Accurate HPP calculation
-   Inventory tracking

---

## 6. Tabel piutang (Jika Transaksi Bon)

### Lokasi

`piutang` table

### Kondisi

Hanya dibuat jika `is_bon = true`

### Data yang Disimpan

```php
if ($isBon) {
    Piutang::create([
        'id_penjualan' => $penjualan->id_penjualan,
        'id_member' => 456,
        'id_outlet' => 1,
        'tanggal_tempo' => '2025-12-01',
        'tanggal_jatuh_tempo' => '2025-12-31',  // +30 days
        'piutang' => 19800,
        'jumlah_piutang' => 19800,
        'jumlah_dibayar' => 0,
        'sisa_piutang' => 19800,
        'status' => 'belum_lunas',
        'nama' => 'Customer Name'
    ]);
}
```

### Fungsi

-   Tracking piutang customer
-   Integrasi dengan modul piutang
-   Reminder jatuh tempo
-   Pelunasan bertahap

---

## 7. Jurnal Akuntansi (journal_entries)

### Lokasi

`journal_entries` dan `journal_entry_details` tables

### Proses

```php
$this->createPosJournal($posSale);
```

### Jurnal yang Dibuat

#### A. Transaksi LUNAS (Cash/Transfer/QRIS)

**Jurnal Penjualan:**

```
Debit:  Kas/Bank              Rp 19,800
Credit: Pendapatan Penjualan  Rp 19,800
```

**Jurnal HPP (jika ada produk):**

```
Debit:  HPP                   Rp 15,000
Credit: Persediaan            Rp 15,000
```

#### B. Transaksi BON (Piutang)

**Jurnal Penjualan:**

```
Debit:  Piutang Usaha         Rp 19,800
Credit: Pendapatan Penjualan  Rp 19,800
```

**Jurnal HPP (jika ada produk):**

```
Debit:  HPP                   Rp 15,000
Credit: Persediaan            Rp 15,000
```

### Akun yang Digunakan

Berdasarkan setting di `setting_coa_pos`:

-   `akun_kas` - Untuk pembayaran cash
-   `akun_bank` - Untuk transfer/QRIS
-   `akun_piutang_usaha` - Untuk bon
-   `akun_pendapatan_penjualan` - Pendapatan
-   `akun_hpp` - Harga Pokok Penjualan (optional)
-   `akun_persediaan` - Persediaan (optional)

### Fungsi

-   **Automatic accounting integration**
-   Real-time financial reporting
-   Accurate profit calculation
-   Audit trail

---

## Flow Chart Lengkap

```
┌─────────────────────────────────────────────────────────────────┐
│                    User: Klik "Bayar & Cetak"                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │   Validation    │
                    └─────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │ DB::transaction │ ← Start Transaction
                    └─────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ 1. Generate  │    │ 2. Calculate │    │ 3. Create    │
│ No Transaksi │    │ Totals       │    │ pos_sales    │
└──────────────┘    └──────────────┘    └──────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ 4. Create    │    │ 5. Update    │    │ 6. Create    │
│ penjualan    │    │ pos_sales    │    │ pos_sale_    │
│              │    │ (link)       │    │ items        │
└──────────────┘    └──────────────┘    └──────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ 7. Reduce    │    │ 8. Calculate │    │ 9. Create    │
│ Stock (FIFO) │    │ HPP          │    │ penjualan_   │
│              │    │              │    │ detail       │
└──────────────┘    └──────────────┘    └──────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ 10. Create   │    │ 11. Create   │    │ 12. Commit   │
│ Piutang      │    │ Journal      │    │ Transaction  │
│ (if bon)     │    │ Entry        │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │ Return Success  │
                    └─────────────────┘
```

---

## Database Tables Summary

### Tables yang Terpengaruh

| No  | Table                   | Purpose                    | When              |
| --- | ----------------------- | -------------------------- | ----------------- |
| 1   | `pos_sales`             | Transaksi POS utama        | Always            |
| 2   | `pos_sale_items`        | Detail item POS            | Always            |
| 3   | `penjualan`             | Kompatibilitas sistem lama | Always            |
| 4   | `penjualan_detail`      | Detail penjualan lama      | If produk         |
| 5   | `hpp_produk`            | Pengurangan stok FIFO      | If produk         |
| 6   | `piutang`               | Piutang customer           | If is_bon         |
| 7   | `journal_entries`       | Header jurnal              | If COA configured |
| 8   | `journal_entry_details` | Detail jurnal              | If COA configured |

### Total: 8 Tables

---

## Integration Points

### 1. Inventory Management

-   ✅ Real-time stock reduction
-   ✅ FIFO costing method
-   ✅ HPP calculation
-   ✅ Stock tracking

### 2. Sales Management

-   ✅ Backward compatibility
-   ✅ Sales reporting
-   ✅ Customer tracking
-   ✅ Sales history

### 3. Receivables Management

-   ✅ Piutang creation
-   ✅ Due date tracking
-   ✅ Payment tracking
-   ✅ Customer credit limit

### 4. Accounting Integration

-   ✅ Automatic journal entries
-   ✅ Real-time financial data
-   ✅ Profit calculation
-   ✅ Balance sheet updates

---

## Error Handling

### Transaction Rollback

Jika terjadi error di salah satu step, **SEMUA perubahan akan di-rollback**:

```php
DB::transaction(function () {
    // All operations here
    // If any fails, everything rolls back
});
```

### Common Errors

1. **Stok tidak cukup**

    - Error: "Gagal mengurangi stok"
    - Rollback: Yes
    - Solution: Cek stok sebelum transaksi

2. **COA tidak dikonfigurasi**

    - Error: "Akun dengan kode X tidak ditemukan"
    - Rollback: Yes
    - Solution: Konfigurasi COA settings

3. **Customer tidak ditemukan**
    - Error: "Member not found"
    - Rollback: Yes
    - Solution: Pilih customer yang valid

---

## Logging

### Success Log

```php
Log::info('POS transaction created successfully', [
    'pos_sale_id' => 123,
    'no_transaksi' => 'POS-20251201-001',
    'total' => 19800
]);
```

### Error Log

```php
Log::error('POS transaction error: ' . $e->getMessage(), [
    'trace' => $e->getTraceAsString()
]);
```

### Log Location

`storage/logs/laravel.log`

---

## Query untuk Cek Data

### 1. Cek Transaksi POS

```sql
SELECT * FROM pos_sales
WHERE no_transaksi = 'POS-20251201-001';
```

### 2. Cek Detail Items

```sql
SELECT * FROM pos_sale_items
WHERE pos_sale_id = 123;
```

### 3. Cek Penjualan

```sql
SELECT * FROM penjualan
WHERE id_penjualan = 789;
```

### 4. Cek Stok

```sql
SELECT id_produk,
       SUM(qty_masuk) - SUM(qty_keluar) as stok
FROM hpp_produk
WHERE id_produk = 123
GROUP BY id_produk;
```

### 5. Cek Piutang

```sql
SELECT * FROM piutang
WHERE id_penjualan = 789;
```

### 6. Cek Jurnal

```sql
SELECT je.*, jed.*
FROM journal_entries je
JOIN journal_entry_details jed ON je.id = jed.journal_entry_id
WHERE je.source_type = 'pos'
AND je.source_id = 123;
```

---

## Performance Considerations

### Indexes

Pastikan index ada di:

-   `pos_sales.no_transaksi` (unique)
-   `pos_sales.id_outlet`
-   `pos_sales.tanggal`
-   `pos_sale_items.pos_sale_id`
-   `hpp_produk.id_produk`
-   `piutang.id_penjualan`

### Transaction Isolation

-   Menggunakan `DB::transaction()` untuk ACID compliance
-   Automatic rollback on error
-   Prevents partial data

### Optimization Tips

1. Use eager loading untuk relationships
2. Batch insert untuk multiple items
3. Cache COA settings
4. Index foreign keys

---

## Testing Checklist

### Basic Transaction

-   [ ] Create cash transaction
-   [ ] Verify pos_sales created
-   [ ] Verify pos_sale_items created
-   [ ] Verify penjualan created
-   [ ] Verify stock reduced
-   [ ] Verify journal created

### Bon Transaction

-   [ ] Create bon transaction
-   [ ] Verify piutang created
-   [ ] Verify correct journal (piutang)
-   [ ] Verify status = 'menunggu'

### Multiple Items

-   [ ] Transaction with 5+ items
-   [ ] Mixed produk and jasa
-   [ ] Verify all items saved
-   [ ] Verify correct totals

### Error Scenarios

-   [ ] Insufficient stock
-   [ ] Invalid customer
-   [ ] Missing COA settings
-   [ ] Verify rollback works

---

## Conclusion

Data transaksi POS disimpan ke **8 tabel berbeda** untuk memastikan:

1. ✅ **Integritas data** - Transaction ensures all-or-nothing
2. ✅ **Kompatibilitas** - Works with existing systems
3. ✅ **Real-time updates** - Stock, piutang, accounting
4. ✅ **Audit trail** - Complete transaction history
5. ✅ **Reporting** - Multiple report sources

Sistem ini dirancang untuk **full ERP integration** dengan backward compatibility ke sistem yang sudah ada.

---

**Document Version:** 1.0  
**Last Updated:** December 1, 2025  
**Author:** Development Team
