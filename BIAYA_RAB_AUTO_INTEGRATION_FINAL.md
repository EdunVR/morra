# AUTO-INTEGRATION BIAYA & RAB - FINAL IMPLEMENTATION

## OVERVIEW

Implementasi auto-create expense dari input realisasi RAB dengan filter yang lebih baik di halaman biaya.

## CHANGES IMPLEMENTED

### 1. AUTO-CREATE EXPENSE DARI RAB REALISASI

#### Backend Changes

**File: `app/Http/Controllers/FinanceAccountantController.php`**

**Method: `saveRealisasiSimple()`**

-   Updated untuk mengembalikan `realisasi_ids` di response
-   Menggunakan `insertGetId()` untuk mendapatkan ID setiap realisasi yang disimpan

```php
// Before
DB::table('rab_realisasi_history')->insert([...]);

// After
$historyId = DB::table('rab_realisasi_history')->insertGetId([...]);
$realisasiIds[] = $historyId;

// Response includes realisasi_ids
return response()->json([
    'success' => true,
    'message' => '...',
    'realisasi_ids' => $realisasiIds  // NEW
]);
```

#### Frontend Changes

**File: `resources/views/admin/finance/rab/index.blade.php`**

**Function: `saveRealisasi()`**

-   Setelah realisasi berhasil disimpan, auto-call `createExpensesFromRealisasi()`
-   Pass realisasi IDs dan data ke fungsi baru

```javascript
if (result.success) {
    // Auto-create expense untuk setiap realisasi
    if (result.realisasi_ids && result.realisasi_ids.length > 0) {
        await this.createExpensesFromRealisasi(
            result.realisasi_ids,
            realisasiValid
        );
    }
    // ... rest of code
}
```

**New Function: `createExpensesFromRealisasi()`**

-   Loop setiap realisasi yang disimpan
-   Call API endpoint `admin.finance.expenses.from-realisasi`
-   Auto-create expense dengan data:
    -   `realisasi_id`: ID dari history
    -   `outlet_id`: Outlet yang dipilih
    -   `amount`: Jumlah realisasi
    -   `description`: Keterangan realisasi
    -   `expense_date`: Tanggal hari ini
    -   `rab_id`: ID RAB template

```javascript
async createExpensesFromRealisasi(realisasiIds, realisasiData){
    for(let i = 0; i < realisasiIds.length; i++){
        const expenseData = {
            realisasi_id: realisasiIds[i],
            outlet_id: this.selectedOutlet,
            amount: realisasiData[i].jumlah,
            description: realisasiData[i].keterangan,
            expense_date: new Date().toISOString().split('T')[0],
            rab_id: this.realisasiData.id
        };

        await fetch('{{ route("admin.finance.expenses.from-realisasi") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(expenseData)
        });
    }
}
```

### 2. FILTER BUKU DI HALAMAN BIAYA

**File: `resources/views/admin/finance/biaya/index.blade.php`**

#### UI Changes

-   Menambahkan dropdown filter buku di sebelah filter outlet
-   Menghapus filter RAB dari header (pindah ke chart)

```html
<!-- Filter Outlet -->
<select x-model="selectedOutlet" @change="onOutletChange()">
    ...
</select>

<!-- Filter Buku (NEW) -->
<select x-model="selectedBook" @change="onBookChange()">
    <template x-for="book in books" :key="book.id">
        <option :value="book.id" x-text="book.name"></option>
    </template>
</select>
```

#### JavaScript Changes

**New Variables:**

```javascript
selectedBook: 1,
books: [],
```

**New Function: `loadBooks()`**

```javascript
async loadBooks() {
    const response = await fetch(`{{ route("finance.accounting-books.data") }}?outlet_id=${this.selectedOutlet}`);
    const result = await response.json();
    if (result.success) {
        this.books = result.data;
        if (this.books.length > 0) {
            this.selectedBook = this.books[0].id;
        }
    }
}
```

**New Function: `onBookChange()`**

```javascript
async onBookChange() {
    await this.loadExpenses();
    await this.loadStats();
    await this.loadChartData();
}
```

**Updated: `init()`**

```javascript
async init() {
    await this.loadOutlets();
    await this.loadBooks();  // NEW
    // ... rest
}
```

**Updated: `onOutletChange()`**

```javascript
async onOutletChange() {
    await this.loadBooks();  // NEW
    // ... rest
}
```

### 3. FILTER ANGGARAN HANYA UNTUK CHART

#### UI Changes

-   Filter anggaran dipindahkan ke dalam card "Ringkasan Biaya"
-   Filter ini hanya mempengaruhi chart dan stats, tidak mempengaruhi tabel

```html
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
    <div class="flex items-center justify-between mb-4">
        <h3>Ringkasan Biaya</h3>
        <div class="flex gap-2">
            <!-- Filter Anggaran untuk Chart (NEW LOCATION) -->
            <select x-model="chartRabFilter" @change="updateCharts()">
                <option value="all">Semua Anggaran</option>
                <option value="no_budget">Tanpa Anggaran</option>
                <template x-for="rab in availableRabs">
                    <option
                        :value="rab.id_rab"
                        x-text="rab.nama_template"
                    ></option>
                </template>
            </select>

            <!-- Period Filter -->
            <select x-model="overviewPeriod" @change="updateCharts()">
                <option value="monthly">Bulan Ini</option>
                <option value="quarterly">Kuartal Ini</option>
                <option value="yearly">Tahun Ini</option>
            </select>
        </div>
    </div>
    <!-- Chart content -->
</div>
```

#### JavaScript Changes

**New Variable:**

```javascript
chartRabFilter: 'all',  // Separate from selectedRab
```

**Updated: `loadExpenses()`**

-   Removed `rab_id` parameter (tidak filter by RAB di tabel)

```javascript
async loadExpenses() {
    const params = new URLSearchParams({
        outlet_id: this.selectedOutlet,
        // rab_id: REMOVED
        category: this.filters.category,
        status: this.filters.status,
        // ...
    });
}
```

**Updated: `loadStats()`**

-   Added `rab_id: this.chartRabFilter` parameter

```javascript
async loadStats() {
    const params = new URLSearchParams({
        outlet_id: this.selectedOutlet,
        rab_id: this.chartRabFilter  // NEW
    });
}
```

**Updated: `loadChartData()`**

-   Added `rab_id: this.chartRabFilter` parameter

```javascript
async loadChartData() {
    const params = new URLSearchParams({
        outlet_id: this.selectedOutlet,
        rab_id: this.chartRabFilter,  // NEW
        period: this.overviewPeriod
    });
}
```

**Updated: `onRabChange()`**

-   Hanya reload expenses (tidak reload stats/chart)

```javascript
async onRabChange() {
    await this.loadExpenses();  // Only reload table
}
```

#### Backend Changes

**File: `app/Http/Controllers/FinanceAccountantController.php`**

**Method: `expensesStats()`**

-   Already supports `rab_id` filter ✅

**Method: `expensesChartData()`**

-   Updated untuk support `rab_id` filter

```php
public function expensesChartData(Request $request): JsonResponse
{
    $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
    $rabId = $request->get('rab_id', 'all');  // NEW

    // Trend data with RAB filter
    for ($i = 5; $i >= 0; $i--) {
        $query = Expense::where('outlet_id', $outletId)
            ->where('status', 'approved')
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month);

        // Apply RAB filter (NEW)
        if ($rabId === 'no_budget') {
            $query->whereNull('rab_id');
        } elseif ($rabId !== 'all') {
            $query->where('rab_id', $rabId);
        }

        $total = $query->sum('amount');
        $trendData[] = floatval($total);
    }

    // Category data with RAB filter (NEW)
    $query = Expense::where('outlet_id', $outletId)
        ->where('status', 'approved');

    if ($rabId === 'no_budget') {
        $query->whereNull('rab_id');
    } elseif ($rabId !== 'all') {
        $query->where('rab_id', $rabId);
    }

    $categoryData = $query->select('category', DB::raw('SUM(amount) as total'))
        ->groupBy('category')
        ->get();
}
```

## WORKFLOW DIAGRAM

### Auto-Create Expense Flow:

```
User Input Realisasi di RAB
    ↓
Submit Form
    ↓
Backend: saveRealisasiSimple()
    ├─ Save to rab_realisasi_history
    ├─ Get realisasi_ids
    └─ Return response with realisasi_ids
    ↓
Frontend: saveRealisasi()
    ├─ Receive realisasi_ids
    └─ Call createExpensesFromRealisasi()
        ↓
        Loop each realisasi
            ↓
            POST /admin/finance/biaya/from-realisasi
                ↓
                Backend: createExpenseFromRealisasi()
                    ├─ Auto-detect expense account
                    ├─ Auto-detect cash account
                    ├─ Generate reference number
                    ├─ Create expense (status: pending)
                    └─ Mark as auto-generated
    ↓
Success: Realisasi & Expenses Created
    ↓
Navigate to Biaya page
    ↓
See auto-generated expenses with "Auto" badge
```

### Filter Behavior:

**Tabel Expenses:**

-   Filter by: Outlet, Book, Category, Status, Date Range, Search
-   NOT filtered by RAB

**Chart & Stats:**

-   Filter by: Outlet, RAB (chartRabFilter), Period
-   Shows budget remaining based on selected RAB

## TESTING GUIDE

### Test 1: Auto-Create Expense

```
1. Login dan navigate ke /admin/finance/rab
2. Pilih outlet dan book
3. Klik "Input Realisasi" pada RAB template
4. Input beberapa realisasi:
   - Keterangan: "Pembelian material"
   - Jumlah: 5000000
5. Klik "Simpan Realisasi"
6. Verify: Alert "Realisasi berhasil disimpan"
7. Navigate ke /admin/finance/biaya
8. Verify: New expenses muncul dengan badge "Auto"
9. Verify: Status = Pending
10. Verify: is_auto_generated = true
```

### Test 2: Filter Buku

```
1. Navigate ke /admin/finance/biaya
2. Pilih outlet
3. Verify: Dropdown buku muncul
4. Pilih buku berbeda
5. Verify: Data expenses reload
6. Verify: Stats dan chart update
```

### Test 3: Filter Anggaran di Chart

```
1. Navigate ke /admin/finance/biaya
2. Di card "Ringkasan Biaya", pilih RAB template
3. Verify: Chart trend update (hanya expenses dari RAB tersebut)
4. Verify: Chart category update
5. Verify: Stats update (Total Biaya, Sisa Anggaran)
6. Verify: Tabel expenses TIDAK berubah (tetap show all)
7. Pilih "Tanpa Anggaran"
8. Verify: Chart hanya show expenses tanpa RAB
9. Pilih "Semua Anggaran"
10. Verify: Chart show all expenses
```

### Test 4: Multiple Realisasi

```
1. Input 3 realisasi sekaligus di RAB
2. Verify: 3 expenses auto-created
3. Verify: Semua memiliki badge "Auto"
4. Verify: Semua linked ke realisasi_id yang benar
5. Verify: Semua linked ke rab_id yang sama
```

## API CHANGES

### POST /admin/finance/rab/{id}/realisasi-simple

**Response (Updated):**

```json
{
    "success": true,
    "message": "Realisasi berhasil disimpan (3 item, total: Rp 15.000.000)",
    "realisasi_ids": [123, 124, 125] // NEW
}
```

### GET /admin/finance/biaya/stats

**Request (Updated):**

```
?outlet_id=1&rab_id=45
```

**Response:**

```json
{
    "success": true,
    "data": {
        "totalThisPeriod": 25000000,
        "budgetRemaining": 75000000, // Based on RAB filter
        "totalMonthly": 25000000,
        "categoriesCount": 4,
        "topCategory": "Operasional",
        "approvedCount": 15,
        "pendingCount": 5,
        "utilization": 25
    }
}
```

### GET /admin/finance/biaya/chart-data

**Request (Updated):**

```
?outlet_id=1&rab_id=45&period=monthly
```

**Response:**

```json
{
    "success": true,
    "data": {
        "trend": {
            "labels": ["Jun", "Jul", "Aug", "Sep", "Oct", "Nov"],
            "data": [5000000, 6000000, 5500000, 7000000, 6500000, 8000000]
        },
        "category": {
            "labels": [
                "Operasional",
                "Administratif",
                "Pemasaran",
                "Pemeliharaan"
            ],
            "data": [15000000, 5000000, 3000000, 2000000],
            "colors": ["#ef4444", "#8b5cf6", "#3b82f6", "#f59e0b"]
        }
    }
}
```

## BENEFITS

### 1. Automation

-   ✅ No manual entry needed untuk expenses dari RAB
-   ✅ Consistent data antara RAB dan Biaya
-   ✅ Reduce human error

### 2. Better UX

-   ✅ Filter buku untuk better organization
-   ✅ Filter anggaran di chart tidak mengganggu view tabel
-   ✅ Clear separation: tabel = all data, chart = filtered view

### 3. Flexibility

-   ✅ User bisa lihat all expenses di tabel
-   ✅ User bisa analyze specific RAB di chart
-   ✅ User bisa compare different RABs

### 4. Traceability

-   ✅ Auto-generated expenses marked dengan badge
-   ✅ Link ke realisasi_id untuk audit trail
-   ✅ Link ke rab_id untuk budget tracking

## TROUBLESHOOTING

### Issue: Expenses tidak auto-created

**Check:**

1. Response dari saveRealisasiSimple includes realisasi_ids
2. createExpensesFromRealisasi() dipanggil
3. API endpoint from-realisasi accessible
4. Accounts exist untuk outlet (expense & cash accounts)

### Issue: Filter buku tidak muncul

**Check:**

1. loadBooks() dipanggil di init()
2. Route finance.accounting-books.data exists
3. Books exist untuk outlet

### Issue: Chart tidak update saat filter RAB

**Check:**

1. chartRabFilter variable exists
2. updateCharts() calls loadStats() dan loadChartData()
3. Backend receives rab_id parameter
4. Backend applies filter correctly

## CONCLUSION

Implementasi auto-integration dan filter improvements telah selesai:

-   ✅ Auto-create expense dari RAB realisasi
-   ✅ Filter buku di halaman biaya
-   ✅ Filter anggaran hanya untuk chart (tidak untuk tabel)
-   ✅ Better separation of concerns
-   ✅ Improved UX

**Status: PRODUCTION READY** 🚀

**Last Updated:** 2025-11-25
**Version:** 2.0.0
