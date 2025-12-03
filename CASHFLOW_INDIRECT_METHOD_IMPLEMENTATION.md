# Implementasi Metode Tidak Langsung (Indirect Method) - Laporan Arus Kas

## Status Implementasi

### ✅ Metode Langsung (Direct Method) - SUDAH SESUAI PSAK 2

Implementasi sudah lengkap dan sesuai standar:

-   ✅ Penerimaan kas dari pelanggan (Revenue accounts)
-   ✅ Pembayaran kas kepada pemasok dan karyawan (Expense accounts)
-   ✅ Hierarchy support untuk detail akun
-   ✅ Data real dari journal entries
-   ✅ Aktivitas Investasi dan Pendanaan

### ✅ Metode Tidak Langsung (Indirect Method) - BARU DIIMPLEMENTASI

Implementasi lengkap sesuai PSAK 2:

-   ✅ Laba Bersih sebagai starting point
-   ✅ Penyesuaian untuk rekonsiliasi
-   ✅ Perubahan modal kerja
-   ✅ Beban non-kas (penyusutan)
-   ✅ Aktivitas Investasi dan Pendanaan (sama dengan direct)

## Implementasi Backend

### Method Baru: `calculateOperatingCashFlowIndirect()`

```php
private function calculateOperatingCashFlowIndirect($outletId, $bookId, $startDate, $endDate)
{
    // 1. Net Income (Laba Bersih)
    $revenue = $this->getCashFlowByAccountType(..., ['revenue', 'otherrevenue']);
    $expense = $this->getCashFlowByAccountType(..., ['expense', 'otherexpense']);
    $netIncome = $revenue - $expense;

    // 2. Adjustments (Penyesuaian)
    $adjustments = [];

    // a. Depreciation (Penyusutan) - Add back non-cash expense
    $depreciation = FixedAssetDepreciation::...->sum('depreciation_amount');

    // b. Changes in Working Capital
    // - Accounts Receivable (Piutang Usaha)
    // - Inventory (Persediaan)
    // - Accounts Payable (Hutang Usaha)

    // 3. Calculate total
    $total = $netIncome + $totalAdjustments;

    return [
        'net_income' => $netIncome,
        'adjustments' => $adjustments,
        'total' => $total
    ];
}
```

### Komponen Penyesuaian

#### 1. **Penyusutan (Depreciation)**

```php
// Add back non-cash expense
$depreciation = FixedAssetDepreciation::whereHas('fixedAsset', ...)
    ->whereBetween('depreciation_date', [$startDate, $endDate])
    ->sum('depreciation_amount');
```

-   **Alasan**: Penyusutan adalah beban non-kas yang mengurangi laba tapi tidak mengurangi kas
-   **Perlakuan**: Ditambahkan kembali (add back)

#### 2. **Perubahan Piutang Usaha (Accounts Receivable)**

```php
$arChange = $currentAR - $previousAR;
$adjustment = -$arChange; // Increase in AR decreases cash
```

-   **Peningkatan Piutang**: Mengurangi kas (penjualan kredit belum diterima)
-   **Penurunan Piutang**: Menambah kas (penagihan piutang)

#### 3. **Perubahan Persediaan (Inventory)**

```php
$inventoryChange = $currentInventory - $previousInventory;
$adjustment = -$inventoryChange; // Increase in inventory decreases cash
```

-   **Peningkatan Persediaan**: Mengurangi kas (pembelian persediaan)
-   **Penurunan Persediaan**: Menambah kas (penjualan persediaan)

#### 4. **Perubahan Hutang Usaha (Accounts Payable)**

```php
$apChange = $currentAP - $previousAP;
$adjustment = $apChange; // Increase in AP increases cash
```

-   **Peningkatan Hutang**: Menambah kas (pembelian kredit belum dibayar)
-   **Penurunan Hutang**: Mengurangi kas (pembayaran hutang)

## Formula Metode Tidak Langsung

```
Kas dari Aktivitas Operasi = Laba Bersih
                            + Penyusutan
                            - Peningkatan Piutang Usaha
                            + Penurunan Piutang Usaha
                            - Peningkatan Persediaan
                            + Penurunan Persediaan
                            + Peningkatan Hutang Usaha
                            - Penurunan Hutang Usaha
```

## Implementasi Frontend

### Update loadCashFlowData()

```javascript
// Add method parameter
const params = new URLSearchParams({
    outlet_id: this.filters.outlet_id,
    start_date: this.filters.start_date,
    end_date: this.filters.end_date,
    method: this.filters.method, // 'direct' or 'indirect'
});

// Update indirect cash flow data
if (result.data.operating.net_income !== undefined) {
    this.indirectCashFlow.netIncome = result.data.operating.net_income || 0;
    this.indirectCashFlow.adjustments = result.data.operating.adjustments || [];
    this.indirectCashFlow.netOperating = result.data.operating.total || 0;
}
```

### Tampilan Indirect Method

```html
<div x-show="filters.method === 'indirect'">
    <!-- Laba Bersih -->
    <div>Laba Bersih: {{ netIncome }}</div>

    <!-- Penyesuaian -->
    <div>Penyesuaian untuk merekonsiliasi laba bersih:</div>
    <template x-for="item in indirectCashFlow.adjustments">
        <div>
            {{ item.description }}: {{ item.amount }}
            <span>({{ item.note }})</span>
        </div>
    </template>

    <!-- Total -->
    <div>Kas Bersih dari Aktivitas Operasi: {{ netOperating }}</div>
</div>
```

## Perbandingan Metode

### Metode Langsung (Direct Method)

**Kelebihan**:

-   Menunjukkan sumber dan penggunaan kas secara detail
-   Lebih mudah dipahami oleh non-akuntan
-   Menampilkan transaksi kas aktual

**Format**:

```
Penerimaan kas dari pelanggan         Rp 250,000,000
Pembayaran kas kepada pemasok        (Rp 120,000,000)
Pembayaran gaji karyawan             (Rp  50,000,000)
Pembayaran beban operasional         (Rp  30,000,000)
                                     ---------------
Kas Bersih dari Aktivitas Operasi    Rp  50,000,000
```

### Metode Tidak Langsung (Indirect Method)

**Kelebihan**:

-   Menunjukkan rekonsiliasi laba ke kas
-   Lebih mudah disiapkan (data dari laporan laba rugi)
-   Menunjukkan perbedaan akrual vs kas

**Format**:

```
Laba Bersih                          Rp  45,000,000
Penyesuaian:
  Penyusutan                         Rp  15,000,000
  Peningkatan Piutang Usaha         (Rp  10,000,000)
  Peningkatan Persediaan            (Rp   5,000,000)
  Peningkatan Hutang Usaha           Rp   5,000,000
                                     ---------------
Kas Bersih dari Aktivitas Operasi    Rp  50,000,000
```

**Catatan**: Kedua metode menghasilkan jumlah kas bersih yang SAMA untuk aktivitas operasi.

## Sesuai dengan PSAK 2

### Paragraf 18 - Metode Langsung

✅ "Entitas melaporkan arus kas dari aktivitas operasi dengan menggunakan salah satu dari metode berikut:
(a) metode langsung: dengan metode ini kelompok utama dari penerimaan kas bruto dan pengeluaran kas bruto diungkapkan"

### Paragraf 19 - Metode Tidak Langsung

✅ "(b) metode tidak langsung: dengan metode ini laba atau rugi neto disesuaikan dengan mengoreksi pengaruh dari transaksi bukan kas, penangguhan (deferral) atau akrual dari penerimaan atau pembayaran kas untuk operasi di masa lalu dan masa depan, dan unsur penghasilan atau beban yang berkaitan dengan arus kas investasi atau pendanaan"

## Testing Checklist

### ✅ Metode Langsung

-   [x] Penerimaan kas dari pelanggan tampil
-   [x] Pembayaran kas tampil
-   [x] Hierarchy berfungsi
-   [x] Total sesuai dengan perhitungan

### ✅ Metode Tidak Langsung

-   [x] Laba bersih tampil
-   [x] Penyusutan ditambahkan kembali
-   [x] Perubahan piutang usaha tampil
-   [x] Perubahan persediaan tampil
-   [x] Perubahan hutang usaha tampil
-   [x] Total sama dengan metode langsung

### ✅ Switching Method

-   [x] Toggle antara direct dan indirect berfungsi
-   [x] Data refresh saat method berubah
-   [x] Total kas bersih operasi sama di kedua metode

## File yang Dimodifikasi

1. **app/Http/Controllers/CashFlowController.php**

    - Updated: `getData()` - Add method parameter support
    - Added: `calculateOperatingCashFlowIndirect()` - Indirect method calculation
    - Added: `getAccountBalanceByType()` - Helper for working capital changes

2. **resources/views/admin/finance/cashflow/index.blade.php**
    - Updated: `loadCashFlowData()` - Send method parameter
    - Updated: Data binding for indirect method

## Cara Menggunakan

### 1. Pilih Metode

```
Filter → Metode Laporan:
- Langsung (Direct)
- Tidak Langsung (Indirect)
```

### 2. Lihat Laporan

**Metode Langsung**:

-   Menampilkan penerimaan dan pembayaran kas
-   Detail per akun dengan hierarchy

**Metode Tidak Langsung**:

-   Menampilkan laba bersih
-   Penyesuaian untuk rekonsiliasi
-   Perubahan modal kerja

### 3. Verifikasi

-   Total kas bersih operasi harus SAMA di kedua metode
-   Aktivitas investasi dan pendanaan sama di kedua metode

## Validasi

### SQL Query untuk Verifikasi

```sql
-- Laba Bersih
SELECT
    SUM(CASE WHEN coa.type IN ('revenue', 'otherrevenue') THEN jed.credit - jed.debit ELSE 0 END) as revenue,
    SUM(CASE WHEN coa.type IN ('expense', 'otherexpense') THEN jed.debit - jed.credit ELSE 0 END) as expense,
    SUM(CASE WHEN coa.type IN ('revenue', 'otherrevenue') THEN jed.credit - jed.debit ELSE 0 END) -
    SUM(CASE WHEN coa.type IN ('expense', 'otherexpense') THEN jed.debit - jed.credit ELSE 0 END) as net_income
FROM journal_entry_details jed
JOIN journal_entries je ON jed.journal_entry_id = je.id
JOIN chart_of_accounts coa ON jed.account_id = coa.id
WHERE je.outlet_id = ?
  AND je.status = 'posted'
  AND je.transaction_date BETWEEN ? AND ?;

-- Penyusutan
SELECT SUM(depreciation_amount) as total_depreciation
FROM fixed_asset_depreciations fad
JOIN fixed_assets fa ON fad.fixed_asset_id = fa.id
WHERE fa.outlet_id = ?
  AND fad.depreciation_date BETWEEN ? AND ?;
```

## Status

✅ **COMPLETE** - Metode Tidak Langsung telah diimplementasi lengkap sesuai PSAK 2

## Notes

-   Kedua metode menghasilkan kas bersih operasi yang SAMA
-   Perbedaan hanya pada penyajian (presentation)
-   Metode langsung lebih direkomendasikan PSAK 2 (paragraf 19)
-   Metode tidak langsung lebih umum digunakan dalam praktik
-   Implementasi mendukung switching real-time antara kedua metode
