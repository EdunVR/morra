# Fix Neraca - Column Error & Book Filter

## Masalah yang Diperbaiki

### 1. ❌ Error Column `period_date` Not Found

**Error Message:**

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'period_date' in 'where clause'
```

**Penyebab:**

-   Code menggunakan kolom `period_date` yang tidak ada di tabel `opening_balances`
-   Code juga menggunakan kolom `balance` yang tidak ada
-   Kolom yang benar adalah `effective_date` untuk tanggal dan `debit`/`credit` untuk saldo

**Solusi:**

-   Update semua query `opening_balances` menggunakan `effective_date` bukan `period_date`
-   Hitung balance dari `debit - credit` bukan dari kolom `balance`

### 2. ❌ Tidak Ada Filter Buku Akuntansi

**Masalah:**

-   Neraca menampilkan semua transaksi dari semua buku
-   Tidak bisa filter berdasarkan buku akuntansi tertentu

**Solusi:**

-   Tambah filter "Buku Akuntansi" di UI
-   Tambah parameter `book_id` di semua method backend
-   Filter transaksi jurnal berdasarkan `book_id` jika dipilih

## Perubahan yang Dilakukan

### 1. Backend - FinanceAccountantController.php

#### a. Method `calculateAccountBalanceUpToDate()`

**Sebelum:**

```php
private function calculateAccountBalanceUpToDate($accountId, $outletId, $endDate)
{
    $openingBalance = OpeningBalance::where('account_id', $accountId)
        ->where('outlet_id', $outletId)
        ->where('period_date', '<=', $endDate)  // ❌ Kolom tidak ada
        ->orderBy('period_date', 'desc')
        ->value('balance') ?? 0;  // ❌ Kolom tidak ada
}
```

**Sesudah:**

```php
private function calculateAccountBalanceUpToDate($accountId, $outletId, $endDate, $bookId = null)
{
    $openingBalanceRecord = OpeningBalance::where('account_id', $accountId)
        ->where('outlet_id', $outletId)
        ->where('effective_date', '<=', $endDate)  // ✅ Kolom yang benar
        ->when($bookId, function($query) use ($bookId) {
            $query->where('book_id', $bookId);  // ✅ Filter buku
        })
        ->orderBy('effective_date', 'desc')
        ->first();

    $openingBalance = 0;
    if ($openingBalanceRecord) {
        $openingBalance = floatval($openingBalanceRecord->debit) - floatval($openingBalanceRecord->credit);  // ✅ Hitung dari debit-credit
    }

    // Journal balance juga di-filter berdasarkan book_id
    $journalBalance = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $endDate, $bookId) {
        $query->where('outlet_id', $outletId)
            ->where('status', 'posted')
            ->where('transaction_date', '<=', $endDate);

        if ($bookId) {
            $query->where('book_id', $bookId);  // ✅ Filter buku
        }
    })
    ->where('account_id', $accountId)
    ->selectRaw('SUM(debit - credit) as balance')
    ->value('balance') ?? 0;

    return floatval($openingBalance) + floatval($journalBalance);
}
```

#### b. Method `neracaData()`

Tambah parameter `book_id`:

```php
public function neracaData(Request $request): JsonResponse
{
    $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
    $bookId = $request->get('book_id', null);  // ✅ Tambah parameter
    $endDate = $request->get('end_date', now()->format('Y-m-d'));

    // Pass bookId ke semua method helper
    $assets = $this->getAccountsByType($outletId, 'asset', $endDate, $bookId);
    $liabilities = $this->getAccountsByType($outletId, 'liability', $endDate, $bookId);
    $equity = $this->getAccountsByType($outletId, 'equity', $endDate, $bookId);

    $retainedEarnings = $this->calculateRetainedEarnings($outletId, $endDate, $bookId);
    // ...
}
```

#### c. Method Helper Lainnya

Semua method helper diupdate untuk menerima dan menggunakan parameter `$bookId`:

-   `getAccountsByType()` ✅
-   `buildAccountHierarchy()` ✅
-   `calculateRetainedEarnings()` ✅
-   `calculateAccountTypeBalance()` ✅
-   `getNeracaAccountDetails()` ✅
-   `exportNeracaPDF()` ✅
-   `exportNeracaXLSX()` ✅

### 2. Frontend - neraca/index.blade.php

#### a. Tambah Filter Buku

**HTML:**

```html
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label>Outlet</label>
        <select x-model="filters.outlet_id" @change="onOutletChange()">
            <!-- options -->
        </select>
    </div>

    <!-- ✅ Filter Buku Baru -->
    <div>
        <label>Buku Akuntansi</label>
        <select x-model="filters.book_id" @change="loadNeracaData()">
            <option value="">Semua Buku</option>
            <template x-for="book in books" :key="book.id">
                <option :value="book.id" x-text="book.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label>Tanggal Neraca</label>
        <input
            type="date"
            x-model="filters.end_date"
            @change="loadNeracaData()"
        />
    </div>
</div>
```

#### b. JavaScript Alpine.js

**Data:**

```javascript
{
  outlets: [],
  books: [],  // ✅ Tambah array books
  filters: {
    outlet_id: '',
    book_id: '',  // ✅ Tambah filter book_id
    end_date: new Date().toISOString().split('T')[0]
  }
}
```

**Methods:**

```javascript
// ✅ Method baru untuk load books
async loadBooks() {
  if (!this.filters.outlet_id) return;

  const params = new URLSearchParams({
    outlet_id: this.filters.outlet_id
  });
  const response = await fetch(`{{ route('finance.active-books.data') }}?${params}`);
  const result = await response.json();
  if (result.success) {
    this.books = result.data;
  }
}

// ✅ Update onOutletChange untuk reset book dan reload books
async onOutletChange() {
  this.filters.book_id = ''; // Reset book filter
  await this.loadBooks();
  await this.loadNeracaData();
}

// ✅ Update loadNeracaData untuk include book_id
async loadNeracaData() {
  const params = new URLSearchParams({
    outlet_id: this.filters.outlet_id,
    end_date: this.filters.end_date
  });

  if (this.filters.book_id) {
    params.append('book_id', this.filters.book_id);
  }

  const response = await fetch(`{{ route('finance.neraca.data') }}?${params}`);
  // ...
}
```

## Struktur Tabel `opening_balances`

```sql
CREATE TABLE opening_balances (
  id BIGINT PRIMARY KEY,
  outlet_id INT,
  book_id INT,
  account_id INT,
  debit DECIMAL(15,2),      -- ✅ Gunakan ini
  credit DECIMAL(15,2),     -- ✅ Gunakan ini
  effective_date DATE,      -- ✅ Gunakan ini (bukan period_date)
  description TEXT,
  status VARCHAR(20),
  created_by INT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

**Cara Hitung Balance:**

```php
$balance = $openingBalance->debit - $openingBalance->credit;
```

## Testing

### Manual Testing Checklist

-   [x] Pilih outlet → dropdown buku terisi
-   [x] Pilih buku → data neraca ter-filter
-   [x] Pilih "Semua Buku" → data neraca menampilkan semua
-   [x] Ganti outlet → dropdown buku ter-reset dan reload
-   [x] Klik akun → detail transaksi ter-filter berdasarkan buku
-   [x] Export XLSX → data sesuai filter buku
-   [x] Export PDF → data sesuai filter buku
-   [x] Tidak ada error SQL column not found

### SQL Query Test

```sql
-- Test query opening balance
SELECT
  account_id,
  debit,
  credit,
  (debit - credit) as balance,
  effective_date
FROM opening_balances
WHERE account_id = 1
  AND outlet_id = 1
  AND effective_date <= '2025-11-22'
ORDER BY effective_date DESC
LIMIT 1;
```

## Keuntungan Filter Buku

### 1. **Fleksibilitas Pelaporan**

-   Bisa lihat neraca per buku akuntansi
-   Bisa lihat neraca konsolidasi (semua buku)

### 2. **Akurasi Data**

-   Transaksi ter-filter dengan benar
-   Opening balance sesuai dengan buku yang dipilih

### 3. **Konsistensi dengan Modul Lain**

-   Sama seperti Laporan Laba Rugi yang juga punya filter buku
-   Sama seperti Buku Besar yang juga punya filter buku

## Troubleshooting

### Jika masih error "Column not found"

1. Clear cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

2. Verify tabel structure:

```sql
DESCRIBE opening_balances;
```

3. Check migration:

```bash
php artisan migrate:status
```

### Jika dropdown buku kosong

1. Pastikan ada data di tabel `accounting_books`
2. Pastikan status buku = 'active'
3. Pastikan `is_locked` = false
4. Check route `finance.active-books.data` accessible

## Kesimpulan

✅ **Error SQL column fixed** - Menggunakan kolom yang benar (`effective_date`, `debit`, `credit`)
✅ **Filter buku added** - User bisa filter neraca berdasarkan buku akuntansi
✅ **Konsistensi data** - Opening balance dan journal entries ter-filter dengan benar
✅ **UX improved** - Dropdown buku otomatis ter-reset saat ganti outlet

Halaman Neraca sekarang berfungsi dengan baik dan konsisten dengan modul finance lainnya!
