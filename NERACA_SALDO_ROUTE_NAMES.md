# Neraca Saldo - Route Names Reference

## 🎯 Mengapa Menggunakan Route Names?

Menggunakan route names Laravel (`route('name')`) lebih baik daripada hardcoded URL karena:

1. ✅ **Maintainable**: Jika URL berubah, hanya perlu update di routes, tidak perlu update di semua view
2. ✅ **Type-safe**: Laravel akan error jika route name tidak ada
3. ✅ **Best Practice**: Sesuai dengan Laravel best practices
4. ✅ **Konsisten**: Semua modul finance menggunakan pola yang sama

## 📋 Daftar Route Names

### 1. Data API Routes

| Route Name                               | URL                                       | Method | Description                     |
| ---------------------------------------- | ----------------------------------------- | ------ | ------------------------------- |
| `finance.outlets.data`                   | `/finance/outlets`                        | GET    | Get list outlets                |
| `finance.accounting-books.data`          | `/finance/accounting-books/data`          | GET    | Get list buku akuntansi         |
| `finance.trial-balance.data`             | `/finance/trial-balance/data`             | GET    | Get trial balance data          |
| `finance.general-ledger.account-details` | `/finance/general-ledger/account-details` | GET    | Get account transaction details |

### 2. Export Routes

| Route Name                           | URL                                   | Method | Description     |
| ------------------------------------ | ------------------------------------- | ------ | --------------- |
| `finance.trial-balance.export.pdf`   | `/finance/trial-balance/export/pdf`   | GET    | Export to PDF   |
| `finance.trial-balance.export.excel` | `/finance/trial-balance/export/excel` | GET    | Export to Excel |

### 3. View Routes

| Route Name                   | URL                     | Method | Description       |
| ---------------------------- | ----------------------- | ------ | ----------------- |
| `finance.neraca-saldo.index` | `/finance/neraca-saldo` | GET    | Neraca Saldo page |

## 💻 Cara Penggunaan di Blade Template

### Load Data dengan Route Names

```javascript
// ❌ JANGAN seperti ini (hardcoded URL)
const response = await fetch("/finance/trial-balance/data?outlet_id=1");

// ✅ GUNAKAN seperti ini (route name)
const response = await fetch(
    '{{ route("finance.trial-balance.data") }}?outlet_id=1'
);
```

### Load Outlets

```javascript
async loadOutlets() {
  try {
    const response = await fetch('{{ route("finance.outlets.data") }}');
    const result = await response.json();
    if (result.success) {
      this.outlets = result.data;
    }
  } catch (error) {
    console.error('Error loading outlets:', error);
  }
}
```

### Load Books

```javascript
async loadBooks() {
  try {
    const response = await fetch('{{ route("finance.accounting-books.data") }}');
    const result = await response.json();
    if (result.success) {
      this.books = result.data;
    }
  } catch (error) {
    console.error('Error loading books:', error);
  }
}
```

### Load Trial Balance Data

```javascript
async loadData() {
  this.isLoading = true;
  try {
    const params = new URLSearchParams(this.filters);
    const response = await fetch(`{{ route('finance.trial-balance.data') }}?${params}`);
    const result = await response.json();

    if (result.success) {
      this.trialBalanceData = result.data;
      this.summary = result.summary;
    }
  } catch (error) {
    console.error('Error loading trial balance data:', error);
  } finally {
    this.isLoading = false;
  }
}
```

### View Account Details

```javascript
async viewAccountDetails(account) {
  try {
    const params = new URLSearchParams({
      account_id: account.id,
      outlet_id: this.filters.outlet_id,
      book_id: this.filters.book_id || '',
      start_date: this.filters.start_date,
      end_date: this.filters.end_date
    });

    const response = await fetch(`{{ route('finance.general-ledger.account-details') }}?${params}`);
    const result = await response.json();

    if (result.success) {
      this.accountDetails.transactions = result.data.transactions;
      this.accountDetails.summary = result.data.summary;
    }
  } catch (error) {
    console.error('Error loading account details:', error);
  }
}
```

### Export to PDF

```javascript
exportPDF() {
  const params = new URLSearchParams(this.filters);
  window.open(`{{ route('finance.trial-balance.export.pdf') }}?${params}`, '_blank');
}
```

### Export to Excel

```javascript
exportExcel() {
  const params = new URLSearchParams(this.filters);
  window.location.href = `{{ route('finance.trial-balance.export.excel') }}?${params}`;
}
```

## 🔍 Cara Cek Route Name di Laravel

### 1. Menggunakan Artisan Command

```bash
# List semua routes
php artisan route:list

# Filter routes finance
php artisan route:list --name=finance

# Filter routes trial-balance
php artisan route:list --name=trial-balance
```

### 2. Menggunakan Tinker

```bash
php artisan tinker

# Get route URL by name
>>> route('finance.trial-balance.data')
=> "http://your-domain/finance/trial-balance/data"

# Check if route exists
>>> Route::has('finance.trial-balance.data')
=> true
```

## 📝 Definisi Routes di web.php

```php
Route::prefix('finance')->name('finance.')->group(function () {
    // Outlets
    Route::get('outlets', [FinanceAccountantController::class, 'getOutlets'])
        ->name('outlets.data');

    // Accounting Books
    Route::get('accounting-books/data', [FinanceAccountantController::class, 'accountingBooksData'])
        ->name('accounting-books.data');

    // Trial Balance (Neraca Saldo)
    Route::get('trial-balance/data', [FinanceAccountantController::class, 'trialBalanceData'])
        ->name('trial-balance.data');
    Route::get('trial-balance/export/pdf', [FinanceAccountantController::class, 'exportTrialBalancePDF'])
        ->name('trial-balance.export.pdf');
    Route::get('trial-balance/export/excel', [FinanceAccountantController::class, 'exportTrialBalanceXLSX'])
        ->name('trial-balance.export.excel');

    // General Ledger
    Route::get('general-ledger/account-details', [FinanceAccountantController::class, 'getAccountDetails'])
        ->name('general-ledger.account-details');

    // View
    Route::view('/neraca-saldo', 'admin.finance.neraca-saldo.index')
        ->name('neraca-saldo.index');
});
```

## 🎨 Pattern yang Digunakan

### Naming Convention

```
{module}.{resource}.{action}

Contoh:
- finance.trial-balance.data
- finance.trial-balance.export.pdf
- finance.trial-balance.export.excel
- finance.neraca-saldo.index
```

### Prefix & Name Group

```php
Route::prefix('finance')->name('finance.')->group(function () {
    // Semua routes di dalam group ini akan:
    // - URL prefix: /finance/...
    // - Route name prefix: finance....
});
```

## ⚠️ Common Mistakes

### ❌ Mistake 1: Hardcoded URL

```javascript
// JANGAN
fetch("/finance/trial-balance/data");
```

### ✅ Solution 1: Use Route Name

```javascript
// GUNAKAN
fetch('{{ route("finance.trial-balance.data") }}');
```

### ❌ Mistake 2: Wrong Quote Type

```javascript
// JANGAN (single quote di luar)
fetch('{{ route('finance.trial-balance.data') }}')
```

### ✅ Solution 2: Use Double Quote Outside

```javascript
// GUNAKAN (double quote di luar, single quote di dalam)
fetch('{{ route("finance.trial-balance.data") }}');
```

### ❌ Mistake 3: Typo in Route Name

```javascript
// JANGAN (typo: trial-balances dengan 's')
fetch('{{ route("finance.trial-balances.data") }}');
```

### ✅ Solution 3: Check Route Name

```javascript
// GUNAKAN (sesuai dengan definisi di routes)
fetch('{{ route("finance.trial-balance.data") }}');
```

## 🧪 Testing Route Names

### Test di Browser Console

```javascript
// Setelah halaman load, cek apakah route sudah benar
console.log(
    "Trial Balance Data URL:",
    '{{ route("finance.trial-balance.data") }}'
);
console.log(
    "Export PDF URL:",
    '{{ route("finance.trial-balance.export.pdf") }}'
);
console.log(
    "Export Excel URL:",
    '{{ route("finance.trial-balance.export.excel") }}'
);
```

### Expected Output

```
Trial Balance Data URL: http://your-domain/finance/trial-balance/data
Export PDF URL: http://your-domain/finance/trial-balance/export/pdf
Export Excel URL: http://your-domain/finance/trial-balance/export/excel
```

## 📚 Related Files

-   **Routes Definition**: `routes/web.php`
-   **Controller**: `app/Http/Controllers/FinanceAccountantController.php`
-   **View**: `resources/views/admin/finance/neraca-saldo/index.blade.php`

## 🎯 Benefits Summary

| Aspect          | Hardcoded URL | Route Names     |
| --------------- | ------------- | --------------- |
| Maintainability | ❌ Low        | ✅ High         |
| Type Safety     | ❌ No         | ✅ Yes          |
| Refactoring     | ❌ Hard       | ✅ Easy         |
| Best Practice   | ❌ No         | ✅ Yes          |
| Error Detection | ❌ Runtime    | ✅ Compile time |

---

**Kesimpulan**: Selalu gunakan route names (`{{ route('...') }}`) untuk semua URL di aplikasi Laravel. Ini adalah best practice yang akan membuat kode lebih maintainable dan mengurangi bug.
