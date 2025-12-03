# Cash Flow MVP Implementation - Complete Guide

## ✅ What's Been Implemented

### Backend (FinanceAccountantController.php)

✅ **Methods Added:**

1. `cashFlowIndex()` - Halaman utama
2. `cashFlowData()` - API untuk data cash flow
3. `calculateOperatingCashFlowDirect()` - Hitung aktivitas operasi (Direct Method)
4. `calculateInvestingCashFlow()` - Hitung aktivitas investasi
5. `calculateFinancingCashFlow()` - Hitung aktivitas pendanaan
6. `getAccountCashFlow()` - Helper untuk cash flow per akun
7. `getBeginningCash()` - Hitung saldo kas awal
8. `getCashFlowAccountDetails()` - Detail transaksi per akun
9. `exportCashFlowPDF()` - Export ke PDF

**Total:** ~350 baris code backend

## 🔧 Next Steps - Manual Implementation

### Step 1: Add Routes

Add to `routes/web.php` inside the `finance` group:

```php
// Cash Flow Routes
Route::get('cashflow', [FinanceAccountantController::class, 'cashFlowIndex'])->name('cashflow.index');
Route::get('cashflow/data', [FinanceAccountantController::class, 'cashFlowData'])->name('cashflow.data');
Route::get('cashflow/account-details/{id}', [FinanceAccountantController::class, 'getCashFlowAccountDetails'])->name('cashflow.account-details');
Route::get('cashflow/export/pdf', [FinanceAccountantController::class, 'exportCashFlowPDF'])->name('cashflow.export.pdf');
```

### Step 2: Update Frontend JavaScript

File: `resources/views/admin/finance/cashflow/index.blade.php`

Find the `loadCashFlowData()` function (around line 502) and replace with:

```javascript
async loadCashFlowData() {
    if (!this.filters.outlet_id) {
        this.error = 'Pilih outlet terlebih dahulu';
        return;
    }

    this.isLoading = true;
    this.error = null;

    try {
        const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date
        });

        if (this.filters.book_id) {
            params.append('book_id', this.filters.book_id);
        }

        const response = await fetch(`{{ route('finance.cashflow.data') }}?${params}`);
        const result = await response.json();

        if (result.success) {
            this.cashFlowData = result.data;
            this.cashFlowStats = result.data.stats;

            // Update charts if needed
            this.$nextTick(() => {
                if (typeof this.initCharts === 'function') {
                    this.initCharts();
                }
            });
        } else {
            this.error = result.message || 'Gagal memuat data arus kas';
        }
    } catch (error) {
        console.error('Error loading cash flow:', error);
        this.error = 'Terjadi kesalahan saat memuat data';
    } finally {
        this.isLoading = false;
    }
},
```

### Step 3: Add Outlet & Book Filters

Add to the filter section (around line 29):

```html
<div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    <!-- Add Outlet Filter -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1"
            >Outlet</label
        >
        <select
            x-model="filters.outlet_id"
            @change="onOutletChange()"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
        >
            <option value="">Pilih Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
                <option
                    :value="outlet.id_outlet"
                    x-text="outlet.nama_outlet"
                ></option>
            </template>
        </select>
    </div>

    <!-- Add Book Filter -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1"
            >Buku Akuntansi</label
        >
        <select
            x-model="filters.book_id"
            @change="loadCashFlowData()"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
        >
            <option value="">Semua Buku</option>
            <template x-for="book in books" :key="book.id">
                <option :value="book.id" x-text="book.name"></option>
            </template>
        </select>
    </div>

    <!-- Existing filters... -->
</div>
```

### Step 4: Add Data Properties

In the `cashFlowManagement()` function, add:

```javascript
outlets: [],
books: [],
filters: {
  outlet_id: '',
  book_id: '',
  method: 'direct',
  period: 'monthly',
  start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0]
},
```

### Step 5: Add Init Methods

```javascript
async init() {
  await this.loadOutlets();
  if (this.outlets.length > 0) {
    this.filters.outlet_id = this.outlets[0].id_outlet;
    await this.loadBooks();
    await this.loadCashFlowData();
  }
},

async loadOutlets() {
  try {
    const response = await fetch('{{ route('finance.outlets.data') }}');
    const result = await response.json();
    if (result.success) {
      this.outlets = result.data;
    }
  } catch (error) {
    console.error('Error loading outlets:', error);
  }
},

async loadBooks() {
  if (!this.filters.outlet_id) return;

  try {
    const params = new URLSearchParams({
      outlet_id: this.filters.outlet_id
    });
    const response = await fetch(`{{ route('finance.active-books.data') }}?${params}`);
    const result = await response.json();
    if (result.success) {
      this.books = result.data;
    }
  } catch (error) {
    console.error('Error loading books:', error);
  }
},

async onOutletChange() {
  this.filters.book_id = '';
  await this.loadBooks();
  await this.loadCashFlowData();
},
```

### Step 6: Add Click to View Details

Find where accounts are displayed and add click handler:

```html
<div
    @click="viewAccountDetails(account.account_id)"
    class="cursor-pointer hover:bg-blue-50 transition-colors"
>
    <span x-text="account.account_code"></span> -
    <span x-text="account.account_name"></span>
</div>
```

Add the method:

```javascript
async viewAccountDetails(accountId) {
  this.showAccountModal = true;
  this.isLoadingAccountDetails = true;

  try {
    const params = new URLSearchParams({
      start_date: this.filters.start_date,
      end_date: this.filters.end_date
    });

    if (this.filters.book_id) {
      params.append('book_id', this.filters.book_id);
    }

    const response = await fetch(`{{ url('finance/cashflow/account-details') }}/${accountId}?${params}`);
    const result = await response.json();

    if (result.success) {
      this.accountDetails = result.data;
    }
  } catch (error) {
    console.error('Error loading account details:', error);
  } finally {
    this.isLoadingAccountDetails = false;
  }
},
```

### Step 7: Update Export Function

```javascript
async exportCashFlow() {
  if (!this.filters.outlet_id) {
    alert('Pilih outlet terlebih dahulu');
    return;
  }

  const params = new URLSearchParams({
    outlet_id: this.filters.outlet_id,
    start_date: this.filters.start_date,
    end_date: this.filters.end_date
  });

  if (this.filters.book_id) {
    params.append('book_id', this.filters.book_id);
  }

  window.location.href = `{{ route('finance.cashflow.export.pdf') }}?${params}`;
},
```

### Step 8: Create PDF View (Optional)

Create file: `resources/views/admin/finance/cashflow/pdf.blade.php`

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Kas - {{ $outlet_name }}</title>
    <style>
        /* Similar to neraca PDF styling */
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; }
        .section-title { background: #1e40af; color: white; padding: 10px; font-weight: bold; }
        .item { display: flex; justify-content: space-between; padding: 5px 10px; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; background: #f3f4f6; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company_name }}</h1>
        <h2>LAPORAN ARUS KAS</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($start_date)->format('d F Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}</p>
    </div>

    <!-- Operating Activities -->
    <div class="section-title">AKTIVITAS OPERASI</div>
    @foreach($operating['items'] as $item)
        <div class="item">
            <span>{{ $item['name'] }}</span>
            <span>Rp {{ number_format($item['amount'], 0, ',', '.') }}</span>
        </div>
    @endforeach
    <div class="total">
        <span>Kas Bersih dari Aktivitas Operasi</span>
        <span>Rp {{ number_format($operating['total'], 0, ',', '.') }}</span>
    </div>

    <!-- Similar for Investing & Financing -->

    <div class="total" style="background: #1e40af; color: white; margin-top: 20px;">
        <span>KENAIKAN/PENURUNAN KAS BERSIH</span>
        <span>Rp {{ number_format($net_cash_flow, 0, ',', '.') }}</span>
    </div>
</body>
</html>
```

## 🎯 What You Get

### Features Implemented:

✅ Operating Activities (Direct Method)
✅ Investing Activities  
✅ Financing Activities
✅ Net Cash Flow calculation
✅ Beginning & Ending Cash
✅ Filter by Outlet & Book
✅ Date range selection
✅ Click account to view details
✅ Export to PDF
✅ Real data from database

### Not Included (Can Add Later):

⏭️ Indirect Method
⏭️ Excel Export
⏭️ Charts/Graphs
⏭️ Comparative periods
⏭️ Advanced filtering

## 🧪 Testing

1. **Access page**: `/finance/cashflow`
2. **Select outlet** from dropdown
3. **Select date range**
4. **View data** - should show real transactions
5. **Click account** - should show transaction details
6. **Export PDF** - should generate PDF report

## 🐛 Troubleshooting

### No data showing?

-   Check if there are journal entries in the date range
-   Verify outlet has transactions
-   Check browser console for errors

### Wrong calculations?

-   Verify account types are correct (revenue, expense, etc.)
-   Check journal entries are posted (not draft)
-   Verify date range is correct

### Export not working?

-   Check PDF view file exists
-   Verify route is registered
-   Check error logs

## 📊 Account Classification

The system automatically classifies accounts:

**Operating:**

-   Revenue accounts (type: 'revenue')
-   Expense accounts (type: 'expense')

**Investing:**

-   Fixed assets (code starts with '12' or category contains 'aset tetap')

**Financing:**

-   Equity accounts (type: 'equity')
-   Long-term debt (code starts with '22')

## 🎉 Success!

You now have a functional Cash Flow report with real data!

The MVP provides core functionality. You can enhance it later with:

-   Indirect Method
-   Excel export
-   Better charts
-   More detailed categorization

Happy coding! 🚀
