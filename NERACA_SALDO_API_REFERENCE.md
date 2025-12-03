# Neraca Saldo API Reference

## 📡 Endpoints

### 1. Get Trial Balance Data

**Endpoint**: `GET /finance/trial-balance/data`

**Description**: Mengambil data neraca saldo berdasarkan filter yang diberikan.

**Query Parameters**:
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| outlet_id | integer | No | User's outlet | ID outlet |
| book_id | integer | No | null (all books) | ID buku akuntansi |
| start_date | date | No | First day of current month | Tanggal mulai periode (YYYY-MM-DD) |
| end_date | date | No | Today | Tanggal akhir periode (YYYY-MM-DD) |

**Example Request**:

```javascript
fetch(
    "/finance/trial-balance/data?outlet_id=1&start_date=2024-01-01&end_date=2024-01-31"
)
    .then((response) => response.json())
    .then((data) => console.log(data));
```

**Success Response** (200 OK):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "1-1000",
            "name": "Kas",
            "type": "asset",
            "level": 1,
            "opening_balance": 1000000,
            "debit": 500000,
            "credit": 200000,
            "ending_balance": 1300000,
            "normal_balance": "debit"
        },
        {
            "id": 2,
            "code": "1-1100",
            "name": "Bank",
            "type": "asset",
            "level": 1,
            "opening_balance": 5000000,
            "debit": 2000000,
            "credit": 1000000,
            "ending_balance": 6000000,
            "normal_balance": "debit"
        }
    ],
    "summary": {
        "total_debit": 2500000,
        "total_credit": 1200000,
        "difference": 1300000,
        "is_balanced": true
    },
    "filters": {
        "outlet_id": 1,
        "book_id": null,
        "start_date": "2024-01-01",
        "end_date": "2024-01-31"
    }
}
```

**Error Response** (500 Internal Server Error):

```json
{
    "success": false,
    "message": "Gagal mengambil data neraca saldo: [error message]"
}
```

---

### 2. Export Trial Balance to PDF

**Endpoint**: `GET /finance/trial-balance/export/pdf`

**Description**: Export neraca saldo ke format PDF (stream untuk preview).

**Query Parameters**:
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| outlet_id | integer | No | User's outlet | ID outlet |
| book_id | integer | No | null (all books) | ID buku akuntansi |
| start_date | date | No | First day of current month | Tanggal mulai periode (YYYY-MM-DD) |
| end_date | date | No | Today | Tanggal akhir periode (YYYY-MM-DD) |

**Example Request**:

```javascript
// Open in new tab
window.open(
    "/finance/trial-balance/export/pdf?outlet_id=1&start_date=2024-01-01&end_date=2024-01-31",
    "_blank"
);
```

**Success Response**: PDF file stream

**Error Response** (500 Internal Server Error):

```json
{
    "success": false,
    "message": "Gagal mengekspor neraca saldo: [error message]"
}
```

---

### 3. Export Trial Balance to Excel

**Endpoint**: `GET /finance/trial-balance/export/excel`

**Description**: Export neraca saldo ke format Excel (XLSX).

**Query Parameters**:
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| outlet_id | integer | No | User's outlet | ID outlet |
| book_id | integer | No | null (all books) | ID buku akuntansi |
| start_date | date | No | First day of current month | Tanggal mulai periode (YYYY-MM-DD) |
| end_date | date | No | Today | Tanggal akhir periode (YYYY-MM-DD) |

**Example Request**:

```javascript
// Download file
window.location.href =
    "/finance/trial-balance/export/excel?outlet_id=1&start_date=2024-01-01&end_date=2024-01-31";
```

**Success Response**: Excel file download

**Error Response** (500 Internal Server Error):

```json
{
    "success": false,
    "message": "Gagal mengekspor neraca saldo: [error message]"
}
```

---

### 4. Get Account Details (for Modal)

**Endpoint**: `GET /finance/general-ledger/account-details`

**Description**: Mengambil detail transaksi untuk akun tertentu (digunakan untuk modal detail).

**Query Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| account_id | integer | Yes | ID akun |
| outlet_id | integer | Yes | ID outlet |
| book_id | integer | No | ID buku akuntansi |
| start_date | date | Yes | Tanggal mulai periode (YYYY-MM-DD) |
| end_date | date | Yes | Tanggal akhir periode (YYYY-MM-DD) |

**Example Request**:

```javascript
fetch(
    "/finance/general-ledger/account-details?account_id=1&outlet_id=1&start_date=2024-01-01&end_date=2024-01-31"
)
    .then((response) => response.json())
    .then((data) => console.log(data));
```

**Success Response** (200 OK):

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
            },
            {
                "id": 2,
                "transaction_date": "2024-01-20",
                "transaction_number": "JU-2024-002",
                "description": "Pembayaran beban listrik",
                "book_name": "Buku Kas",
                "debit": 0,
                "credit": 200000
            }
        ],
        "summary": {
            "transaction_count": 2,
            "total_debit": 500000,
            "total_credit": 200000,
            "current_balance": 300000
        }
    }
}
```

---

## 📊 Data Models

### Trial Balance Item

```typescript
interface TrialBalanceItem {
    id: number; // ID akun
    code: string; // Kode akun (e.g., "1-1000")
    name: string; // Nama akun (e.g., "Kas")
    type: AccountType; // Tipe akun
    level: number; // Level hierarki (1, 2, 3, ...)
    opening_balance: number; // Saldo awal periode
    debit: number; // Total debit dalam periode
    credit: number; // Total kredit dalam periode
    ending_balance: number; // Saldo akhir periode
    normal_balance: "debit" | "credit"; // Normal balance side
}
```

### Account Type

```typescript
type AccountType =
    | "asset" // Aset
    | "liability" // Kewajiban
    | "equity" // Ekuitas
    | "revenue" // Pendapatan
    | "expense" // Beban
    | "otherrevenue" // Pendapatan Lain
    | "otherexpense"; // Beban Lain
```

### Summary

```typescript
interface Summary {
    total_debit: number; // Total semua debit
    total_credit: number; // Total semua kredit
    difference: number; // Selisih (debit - kredit)
    is_balanced: boolean; // true jika difference < 0.01
}
```

### Transaction Detail

```typescript
interface TransactionDetail {
    id: number;
    transaction_date: string; // Format: YYYY-MM-DD
    transaction_number: string; // e.g., "JU-2024-001"
    description: string;
    book_name: string;
    debit: number;
    credit: number;
}
```

---

## 🔐 Authentication & Authorization

Semua endpoint memerlukan:

-   User harus login (authenticated)
-   User harus memiliki akses ke modul Finance
-   User hanya dapat melihat data outlet yang memiliki akses

---

## ⚡ Performance Considerations

### Caching

-   Data neraca saldo tidak di-cache karena sifatnya yang real-time
-   Setiap request akan query database langsung

### Optimization

-   Query menggunakan eager loading untuk relasi
-   Index pada kolom yang sering di-filter (outlet_id, book_id, transaction_date)
-   Hanya akun dengan transaksi yang ditampilkan

### Rate Limiting

-   Tidak ada rate limiting khusus untuk endpoint ini
-   Mengikuti rate limiting global aplikasi

---

## 🐛 Error Handling

### Common Errors

#### 1. Invalid Date Format

```json
{
    "success": false,
    "message": "Format tanggal tidak valid. Gunakan format YYYY-MM-DD"
}
```

#### 2. Outlet Not Found

```json
{
    "success": false,
    "message": "Outlet tidak ditemukan"
}
```

#### 3. Book Not Found

```json
{
    "success": false,
    "message": "Buku akuntansi tidak ditemukan"
}
```

#### 4. Database Error

```json
{
    "success": false,
    "message": "Gagal mengambil data neraca saldo: [database error]"
}
```

---

## 📝 Usage Examples

### Example 1: Load Trial Balance with Filters

```javascript
async function loadTrialBalance() {
    const filters = {
        outlet_id: 1,
        book_id: 2,
        start_date: "2024-01-01",
        end_date: "2024-01-31",
    };

    const params = new URLSearchParams(filters);
    const response = await fetch(`/finance/trial-balance/data?${params}`);
    const result = await response.json();

    if (result.success) {
        console.log("Trial Balance Data:", result.data);
        console.log("Summary:", result.summary);
    } else {
        console.error("Error:", result.message);
    }
}
```

### Example 2: Export to PDF

```javascript
function exportToPDF() {
    const filters = {
        outlet_id: 1,
        start_date: "2024-01-01",
        end_date: "2024-01-31",
    };

    const params = new URLSearchParams(filters);
    window.open(`/finance/trial-balance/export/pdf?${params}`, "_blank");
}
```

### Example 3: Export to Excel

```javascript
function exportToExcel() {
    const filters = {
        outlet_id: 1,
        start_date: "2024-01-01",
        end_date: "2024-01-31",
    };

    const params = new URLSearchParams(filters);
    window.location.href = `/finance/trial-balance/export/excel?${params}`;
}
```

### Example 4: View Account Details

```javascript
async function viewAccountDetails(accountId) {
    const params = new URLSearchParams({
        account_id: accountId,
        outlet_id: 1,
        start_date: "2024-01-01",
        end_date: "2024-01-31",
    });

    const response = await fetch(
        `/finance/general-ledger/account-details?${params}`
    );
    const result = await response.json();

    if (result.success) {
        console.log("Account:", result.data.account);
        console.log("Transactions:", result.data.transactions);
        console.log("Summary:", result.data.summary);
    }
}
```

---

## 🔄 Integration with Other Modules

### Journal Entry

-   Trial balance data berasal dari `journal_entries` dan `journal_entry_details`
-   Hanya transaksi dengan status "posted" yang dihitung

### General Ledger

-   Menggunakan endpoint yang sama untuk detail transaksi
-   Konsisten dengan tampilan buku besar

### Chart of Accounts

-   Menggunakan hierarki akun dari `chart_of_accounts`
-   Respek terhadap parent-child relationship

---

## 📚 Related Documentation

-   [Implementasi Neraca Saldo](NERACA_SALDO_IMPLEMENTATION.md)
-   [Testing Guide](NERACA_SALDO_TESTING_GUIDE.md)
-   [Finance Module Documentation](docs/finance-module.md)
-   [API General Documentation](docs/api-documentation.md)

---

**Last Updated**: November 23, 2024
