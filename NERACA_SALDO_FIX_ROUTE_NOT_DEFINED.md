# Fix: Route [finance.general-ledger.account-details] not defined

## 🐛 Problem

Error saat mengakses halaman Neraca Saldo:

```
Route [finance.general-ledger.account-details] not defined.
```

## 🔍 Root Cause

Route `finance.general-ledger.account-details` belum didefinisikan di `routes/web.php`, padang view neraca saldo menggunakannya untuk menampilkan detail transaksi akun.

## ✅ Solution

### 1. Membuat Method Baru di Controller

**File**: `app/Http/Controllers/FinanceAccountantController.php`

Menambahkan method `getAccountTransactionDetailsForModal()` yang menerima query parameters:

```php
/**
 * Get account transaction details for modal (used by Trial Balance, etc)
 */
public function getAccountTransactionDetailsForModal(Request $request): JsonResponse
{
    try {
        $accountId = $request->get('account_id');
        $outletId = $request->get('outlet_id');
        $bookId = $request->get('book_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Validation
        if (!$accountId || !$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Account ID dan Outlet ID diperlukan'
            ], 422);
        }

        // Get account
        $account = ChartOfAccount::find($accountId);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan'
            ], 404);
        }

        // Get transactions with filters
        $query = JournalEntryDetail::with(['journalEntry.book'])
            ->whereHas('journalEntry', function($q) use ($outletId, $bookId, $startDate, $endDate) {
                $q->where('outlet_id', $outletId)
                  ->where('status', 'posted');

                if ($bookId) {
                    $q->where('book_id', $bookId);
                }

                if ($startDate && $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                }
            })
            ->where('account_id', $accountId)
            ->orderBy('created_at', 'desc');

        $transactions = $query->get()->map(function($detail) {
            return [
                'id' => $detail->id,
                'transaction_date' => $detail->journalEntry->transaction_date->format('Y-m-d'),
                'transaction_number' => $detail->journalEntry->transaction_number,
                'description' => $detail->description ?: $detail->journalEntry->description,
                'book_name' => $detail->journalEntry->book->name ?? '-',
                'debit' => floatval($detail->debit),
                'credit' => floatval($detail->credit)
            ];
        });

        // Calculate summary
        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $currentBalance = $totalDebit - $totalCredit;

        return response()->json([
            'success' => true,
            'data' => [
                'account' => [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type
                ],
                'transactions' => $transactions,
                'summary' => [
                    'transaction_count' => $transactions->count(),
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'current_balance' => $currentBalance
                ]
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting account transaction details: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil detail transaksi: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. Menambahkan Route

**File**: `routes/web.php`

```php
// General Ledger Routes
Route::get('general-ledger/data', [FinanceAccountantController::class, 'generalLedgerData'])
    ->name('general-ledger.data');
Route::get('general-ledger/stats', [FinanceAccountantController::class, 'generalLedgerStats'])
    ->name('general-ledger.stats');
Route::get('general-ledger/accounts', [FinanceAccountantController::class, 'getActiveAccounts'])
    ->name('general-ledger.accounts');
Route::get('general-ledger/account-details', [FinanceAccountantController::class, 'getAccountTransactionDetailsForModal'])
    ->name('general-ledger.account-details');  // ← NEW ROUTE
```

## 📊 API Specification

### Endpoint

```
GET /finance/general-ledger/account-details
```

### Route Name

```
finance.general-ledger.account-details
```

### Query Parameters

| Parameter  | Type    | Required | Description                             |
| ---------- | ------- | -------- | --------------------------------------- |
| account_id | integer | Yes      | ID akun yang akan ditampilkan detailnya |
| outlet_id  | integer | Yes      | ID outlet                               |
| book_id    | integer | No       | ID buku akuntansi (optional filter)     |
| start_date | date    | No       | Tanggal mulai periode (YYYY-MM-DD)      |
| end_date   | date    | No       | Tanggal akhir periode (YYYY-MM-DD)      |

### Response Format

**Success (200 OK)**:

```json
{
    "success": true,
    "data": {
        "account": {
            "id": 1,
            "code": "1-1000",
            "name": "Kas",
            "type": "asset"
        },
        "transactions": [
            {
                "id": 1,
                "transaction_date": "2024-01-15",
                "transaction_number": "JU-2024-001",
                "description": "Penerimaan kas dari penjualan",
                "book_name": "Buku Kas",
                "debit": 500000,
                "credit": 0
            }
        ],
        "summary": {
            "transaction_count": 10,
            "total_debit": 5000000,
            "total_credit": 2000000,
            "current_balance": 3000000
        }
    }
}
```

**Error (422 Unprocessable Entity)**:

```json
{
    "success": false,
    "message": "Account ID dan Outlet ID diperlukan"
}
```

**Error (404 Not Found)**:

```json
{
    "success": false,
    "message": "Akun tidak ditemukan"
}
```

**Error (500 Internal Server Error)**:

```json
{
    "success": false,
    "message": "Gagal mengambil detail transaksi: [error message]"
}
```

## 🎯 Usage in View

**File**: `resources/views/admin/finance/neraca-saldo/index.blade.php`

```javascript
async viewAccountDetails(account) {
  this.showAccountModal = true;
  this.isLoadingAccountDetails = true;
  this.accountDetails = {
    account: account,
    transactions: [],
    summary: null
  };

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
    this.showNotification('error', 'Gagal memuat detail transaksi');
  } finally {
    this.isLoadingAccountDetails = false;
  }
}
```

## 🧪 Testing

### 1. Clear Cache

```bash
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 2. Verify Route

```bash
php artisan route:list --name=general-ledger
```

Expected output:

```
GET|HEAD  finance/general-ledger/account-details ... finance.general-ledger.account-details
```

### 3. Test in Browser

1. Buka halaman: `http://your-domain/finance/neraca-saldo`
2. Klik pada salah satu baris akun
3. Modal detail transaksi harus muncul
4. Pastikan data transaksi tampil dengan benar

### 4. Test API Directly

```bash
curl "http://your-domain/finance/general-ledger/account-details?account_id=1&outlet_id=1&start_date=2024-01-01&end_date=2024-01-31"
```

## 📝 Features

### Filtering Support

-   ✅ Filter by outlet
-   ✅ Filter by book (optional)
-   ✅ Filter by date range (optional)
-   ✅ Only posted transactions

### Data Returned

-   ✅ Account information (code, name, type)
-   ✅ List of transactions with details
-   ✅ Summary (count, total debit, total credit, balance)

### Error Handling

-   ✅ Validation for required parameters
-   ✅ Account not found handling
-   ✅ Database error handling
-   ✅ Proper HTTP status codes

## 🔄 Related Files

### Modified:

1. `app/Http/Controllers/FinanceAccountantController.php` - Added new method
2. `routes/web.php` - Added new route

### Unchanged:

-   `resources/views/admin/finance/neraca-saldo/index.blade.php` - Already using correct route name

## 📚 Documentation Updated

-   `NERACA_SALDO_ROUTE_NAMES.md` - Already documented this route
-   `NERACA_SALDO_API_REFERENCE.md` - Already documented this endpoint
-   `NERACA_SALDO_FIX_ROUTE_NOT_DEFINED.md` - This file (new)

## ✅ Status

**FIXED** - Route `finance.general-ledger.account-details` telah didefinisikan dan method controller telah dibuat.

Halaman Neraca Saldo sekarang dapat menampilkan detail transaksi akun dengan benar.

---

**Fixed Date**: November 24, 2024
**Fixed By**: Kiro AI Assistant
