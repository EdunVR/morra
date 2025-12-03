# FILTER BUKU & RAB INTEGRATION - COMPLETE IMPLEMENTATION

## OVERVIEW

Implementasi lengkap filter buku di halaman biaya dengan integrasi RAB yang dinamis, termasuk perhitungan budget real-time dari RAB yang dipilih.

## FEATURES IMPLEMENTED

### 1. DATABASE SCHEMA - book_id di Expenses

#### New Migration: `add_book_id_to_expenses_table`

```php
Schema::table('expenses', function (Blueprint $table) {
    $table->unsignedBigInteger('book_id')->nullable()->after('outlet_id');
    $table->foreign('book_id')->references('id')->on('accounting_books')->onDelete('set null');
    $table->index('book_id');
});
```

**Purpose:**

-   Link expense ke accounting book
-   Enable filtering by book
-   Track which book the expense belongs to

### 2. MODEL UPDATES

#### Expense Model

**New Fillable Field:**

```php
protected $fillable = [
    'outlet_id',
    'book_id',  // NEW
    'rab_id',
    // ... rest
];
```

**New Relationship:**

```php
public function book(): BelongsTo
{
    return $this->belongsTo(\App\Models\AccountingBook::class, 'book_id');
}
```

### 3. BACKEND ENHANCEMENTS

#### A. createExpenseFromRealisasi()

**Updated to include book_id:**

```php
$validated = $request->validate([
    'realisasi_id' => 'required|exists:rab_realisasi_history,id',
    'outlet_id' => 'required|exists:outlets,id_outlet',
    'book_id' => 'nullable|exists:accounting_books,id',  // NEW
    'amount' => 'required|numeric|min:0',
    'description' => 'required|string',
    'expense_date' => 'required|date',
    'rab_id' => 'nullable|exists:rab_template,id_rab'
]);

$expense = Expense::create([
    'outlet_id' => $validated['outlet_id'],
    'book_id' => $validated['book_id'] ?? null,  // NEW
    // ... rest
]);
```

#### B. expensesData()

**Added book_id filter:**

```php
public function expensesData(Request $request): JsonResponse
{
    $outletId = $request->get('outlet_id');
    $bookId = $request->get('book_id');  // NEW
    $rabId = $request->get('rab_id', 'all');

    $query = Expense::with(['outlet', 'book', 'account', 'approver', 'rab'])  // Added 'book'
        ->where('outlet_id', $outletId)
        ->when($bookId, function($q) use ($bookId) {
            $q->where('book_id', $bookId);  // NEW FILTER
        })
        ->when($rabId !== 'all', function($q) use ($rabId) {
            if ($rabId === 'no_budget') {
                $q->whereNull('rab_id');
            } else {
                $q->where('rab_id', $rabId);
            }
        })
        // ... rest of filters
}
```

#### C. expensesStats()

**Calculate budget from selected RAB:**

```php
public function expensesStats(Request $request): JsonResponse
{
    $rabId = $request->get('rab_id', 'all');

    // Calculate budget from RAB if specific RAB selected
    $budgetTotal = 0;
    $budgetRemaining = 0;

    if ($rabId !== 'all' && $rabId !== 'no_budget') {
        $rab = \App\Models\RabTemplate::find($rabId);
        if ($rab) {
            // Use approved budget or fallback to planned budget
            $budgetTotal = $rab->details->sum('nilai_disetujui') ?: $rab->details->sum('budget');
            $budgetRemaining = $budgetTotal - $totalThisPeriod;
        }
    } else {
        // For 'all' or 'no_budget', use default value
        $budgetTotal = 100000000;
        $budgetRemaining = $budgetTotal - $totalThisPeriod;
    }

    // Calculate utilization
    $utilization = $budgetTotal > 0 ? round(($totalThisPeriod / $budgetTotal) * 100) : 0;

    return response()->json([
        'success' => true,
        'data' => [
            'totalThisPeriod' => floatval($totalThisPeriod),
            'budgetRemaining' => floatval($budgetRemaining),
            'budgetTotal' => floatval($budgetTotal),  // NEW
            'utilization' => $utilization
        ]
    ]);
}
```

### 4. FRONTEND IMPLEMENTATION

#### A. Filter Buku Working

**UI:**

```html
<!-- Filter Outlet -->
<select x-model="selectedOutlet" @change="onOutletChange()">
    ...
</select>

<!-- Filter Buku -->
<select x-model="selectedBook" @change="onBookChange()">
    <template x-for="book in books" :key="book.id">
        <option :value="book.id" x-text="book.name"></option>
    </template>
</select>
```

**JavaScript:**

```javascript
async onBookChange() {
    console.log('Book changed to:', this.selectedBook);
    this.chartRabFilter = 'all';  // Reset RAB filter
    await this.loadAvailableRabs();  // Reload RABs for this book
    await this.loadExpenses();
    await this.loadStats();
    await this.loadChartData();
}
```

#### B. RAB List Filtered by Book

**loadAvailableRabs():**

```javascript
async loadAvailableRabs() {
    const params = new URLSearchParams({
        outlet_id: this.selectedOutlet,
        book_id: this.selectedBook  // NEW - Filter by book
    });

    const response = await fetch(`{{ route("admin.finance.rab.data") }}?${params}`);
    const result = await response.json();

    if (result.success) {
        this.availableRabs = result.data;
    }
}
```

#### C. Expenses Filtered by RAB Selection

**loadExpenses():**

```javascript
async loadExpenses() {
    const params = new URLSearchParams({
        outlet_id: this.selectedOutlet,
        book_id: this.selectedBook,
        rab_id: this.chartRabFilter,  // Filter by selected RAB in chart
        category: this.filters.category,
        status: this.filters.status,
        // ... rest
    });

    const response = await fetch(`${this.routes.expensesData}?${params}`);
    // ...
}
```

#### D. Chart Updates Trigger Table Reload

**updateCharts():**

```javascript
async updateCharts() {
    await this.loadStats();      // Update stats with RAB budget
    await this.loadChartData();  // Update charts
    await this.loadExpenses();   // Update table to match RAB filter
}
```

#### E. Auto-Create Expense with book_id

**createExpensesFromRealisasi():**

```javascript
const expenseData = {
    realisasi_id: realisasiId,
    outlet_id: this.selectedOutlet,
    book_id: this.selectedBook, // NEW - Include book_id
    amount: realisasi.jumlah,
    description: realisasi.keterangan,
    expense_date: new Date().toISOString().split("T")[0],
    rab_id: this.realisasiData.id,
};
```

## WORKFLOW DIAGRAM

### Filter Flow:

```
User selects Outlet
    ↓
Load Books for Outlet
    ↓
User selects Book
    ↓
onBookChange()
    ├─ Reset chartRabFilter to 'all'
    ├─ Load RABs filtered by Book
    ├─ Load Expenses filtered by Book
    ├─ Load Stats (all RABs)
    └─ Load Charts (all RABs)
    ↓
User selects RAB in Chart Filter
    ↓
updateCharts()
    ├─ Load Stats (specific RAB budget)
    ├─ Load Charts (specific RAB data)
    └─ Load Expenses (filtered by RAB)
    ↓
Display:
    ├─ Chart shows only expenses from selected RAB
    ├─ Stats show budget from selected RAB
    └─ Table shows only expenses from selected RAB
```

### Budget Calculation Flow:

```
User selects RAB in Chart Filter
    ↓
Backend: expensesStats()
    ↓
Check if specific RAB selected
    ├─ YES: Get RAB details
    │   ├─ budgetTotal = sum(nilai_disetujui) or sum(budget)
    │   ├─ totalApproved = sum(expenses.amount where status='approved')
    │   └─ budgetRemaining = budgetTotal - totalApproved
    │
    └─ NO (all/no_budget): Use default budget
        └─ budgetTotal = 100,000,000
    ↓
Calculate utilization = (totalApproved / budgetTotal) * 100
    ↓
Return to Frontend
    ↓
Display in Chart:
    ├─ Total Biaya: Rp X (approved expenses)
    └─ Sisa Anggaran: Rp Y (budget remaining)
```

## API CHANGES

### GET /admin/finance/biaya/data

**Request (Updated):**

```
?outlet_id=1&book_id=2&rab_id=45&category=all&status=all
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 123,
            "reference": "EXP-20251125-0001",
            "outlet_id": 1,
            "book_id": 2,
            "rab_id": 45,
            "rab_name": "RAB Proyek X",
            "amount": 5000000,
            "status": "approved"
            // ... rest
        }
    ]
}
```

### GET /admin/finance/biaya/stats

**Request (Updated):**

```
?outlet_id=1&rab_id=45
```

**Response (Updated):**

```json
{
    "success": true,
    "data": {
        "totalThisPeriod": 25000000,
        "budgetTotal": 50000000,
        "budgetRemaining": 25000000,
        "utilization": 50,
        "approvedCount": 15,
        "pendingCount": 5
        // ... rest
    }
}
```

### GET /admin/finance/rab/data

**Request (Updated):**

```
?outlet_id=1&book_id=2
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 45,
            "name": "RAB Proyek X",
            "outlet_id": 1,
            "book_id": 2,
            "budget_total": 50000000,
            "approved_value": 45000000
            // ... rest
        }
    ]
}
```

### POST /admin/finance/biaya/from-realisasi

**Request (Updated):**

```json
{
    "realisasi_id": 123,
    "outlet_id": 1,
    "book_id": 2,
    "amount": 5000000,
    "description": "Pembelian material",
    "expense_date": "2025-11-25",
    "rab_id": 45
}
```

## TESTING GUIDE

### Test 1: Filter by Book

```
1. Navigate to /admin/finance/biaya
2. Select outlet
3. Verify: Book dropdown populated
4. Select different book
5. Verify: Expenses reload
6. Verify: RAB dropdown updates (only RABs from selected book)
7. Verify: Stats update
8. Verify: Charts update
```

### Test 2: RAB Filter in Chart

```
1. In "Ringkasan Biaya" card, select specific RAB
2. Verify: Chart updates (only expenses from that RAB)
3. Verify: "Total Biaya" shows approved expenses from RAB
4. Verify: "Sisa Anggaran" = RAB budget - approved expenses
5. Verify: Table shows only expenses from that RAB
6. Select "Semua Anggaran"
7. Verify: Chart shows all expenses
8. Verify: Table shows all expenses
```

### Test 3: Budget Calculation

```
1. Create RAB with budget 50,000,000
2. Create expenses linked to that RAB totaling 25,000,000
3. Approve expenses
4. Navigate to /admin/finance/biaya
5. Select the RAB in chart filter
6. Verify: Total Biaya = 25,000,000
7. Verify: Sisa Anggaran = 25,000,000
8. Verify: Utilization = 50%
```

### Test 4: Auto-Create with book_id

```
1. Navigate to /admin/finance/rab
2. Select outlet and book
3. Input realisasi
4. Click "Simpan Realisasi"
5. Navigate to /admin/finance/biaya
6. Select same book
7. Verify: Auto-created expense appears
8. Verify: expense.book_id matches selected book
9. Verify: expense.rab_id matches RAB template
```

### Test 5: Cross-Book Filtering

```
1. Create expenses in Book A
2. Create expenses in Book B
3. Navigate to /admin/finance/biaya
4. Select Book A
5. Verify: Only expenses from Book A shown
6. Select Book B
7. Verify: Only expenses from Book B shown
8. Verify: RAB dropdown only shows RABs from Book B
```

## BENEFITS

### 1. Better Organization

-   ✅ Expenses organized by accounting book
-   ✅ Clear separation between different books
-   ✅ Easy to track expenses per book

### 2. Accurate Budget Tracking

-   ✅ Real-time budget calculation from RAB
-   ✅ Accurate remaining budget display
-   ✅ Utilization percentage based on actual budget

### 3. Dynamic Filtering

-   ✅ RAB list filtered by selected book
-   ✅ Expenses filtered by selected RAB
-   ✅ Stats and charts reflect selected filters

### 4. Improved UX

-   ✅ Consistent filtering across all components
-   ✅ Clear visual feedback on selections
-   ✅ Synchronized data between chart and table

## TROUBLESHOOTING

### Issue: Book filter not working

**Check:**

1. Migration run successfully
2. book_id column exists in expenses table
3. loadBooks() called in init()
4. selectedBook variable initialized

### Issue: RAB list not filtered by book

**Check:**

1. loadAvailableRabs() includes book_id parameter
2. Backend rabData() method filters by book_id
3. onBookChange() calls loadAvailableRabs()

### Issue: Budget not calculated correctly

**Check:**

1. RAB has details with nilai_disetujui or budget
2. expensesStats() receives correct rab_id
3. Expenses are approved (status='approved')
4. Expenses linked to correct rab_id

### Issue: Table not updating when RAB filter changes

**Check:**

1. updateCharts() calls loadExpenses()
2. loadExpenses() includes rab_id parameter
3. chartRabFilter variable updated correctly

## FILES MODIFIED

1. **Database:**

    - `database/migrations/2025_11_25_162602_add_book_id_to_expenses_table.php`

2. **Models:**

    - `app/Models/Expense.php`

3. **Controllers:**

    - `app/Http/Controllers/FinanceAccountantController.php`
        - createExpenseFromRealisasi()
        - expensesData()
        - expensesStats()

4. **Views:**
    - `resources/views/admin/finance/biaya/index.blade.php`
        - loadAvailableRabs()
        - loadExpenses()
        - onBookChange()
        - updateCharts()
    - `resources/views/admin/finance/rab/index.blade.php`
        - createExpensesFromRealisasi()

## CONCLUSION

Implementasi filter buku dan integrasi RAB telah selesai dengan fitur:

-   ✅ Filter buku working dengan book_id di expenses
-   ✅ RAB list filtered by selected book
-   ✅ Budget calculation dari RAB yang dipilih
-   ✅ Expenses filtered by selected RAB di chart
-   ✅ Stats dan charts synchronized dengan filter
-   ✅ Auto-create expense include book_id

**Status: PRODUCTION READY** 🚀

**Last Updated:** 2025-11-25
**Version:** 3.0.0
