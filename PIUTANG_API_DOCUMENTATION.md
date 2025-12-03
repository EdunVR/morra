# API Documentation - Piutang Module

## Base URL

```
/finance/piutang
```

## Authentication

All endpoints require user authentication via Laravel session.

---

## Endpoints

### 1. Get Piutang List

Get list of piutang with filters and summary.

**Endpoint:** `GET /finance/piutang/data`

**Query Parameters:**

| Parameter  | Type    | Required | Default | Description                                     |
| ---------- | ------- | -------- | ------- | ----------------------------------------------- |
| outlet_id  | integer | No       | null    | Filter by outlet ID                             |
| status     | string  | No       | 'all'   | Filter by status: 'all', 'belum_lunas', 'lunas' |
| start_date | date    | No       | null    | Filter start date (Y-m-d)                       |
| end_date   | date    | No       | null    | Filter end date (Y-m-d)                         |
| search     | string  | No       | null    | Search by customer name                         |

**Example Request:**

```bash
GET /finance/piutang/data?outlet_id=1&status=belum_lunas&start_date=2025-01-01&end_date=2025-12-31&search=john
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id_piutang": 1,
            "id_penjualan": 123,
            "tanggal": "2025-11-24",
            "tanggal_jatuh_tempo": "2025-12-24",
            "nama_customer": "John Doe",
            "outlet": "Outlet A",
            "jumlah_piutang": 1000000.0,
            "jumlah_dibayar": 500000.0,
            "sisa_piutang": 500000.0,
            "status": "belum_lunas",
            "is_overdue": false,
            "days_overdue": 0,
            "invoice_number": "INV-000123"
        },
        {
            "id_piutang": 2,
            "id_penjualan": 124,
            "tanggal": "2025-10-15",
            "tanggal_jatuh_tempo": "2025-11-15",
            "nama_customer": "Jane Smith",
            "outlet": "Outlet A",
            "jumlah_piutang": 2000000.0,
            "jumlah_dibayar": 0.0,
            "sisa_piutang": 2000000.0,
            "status": "belum_lunas",
            "is_overdue": true,
            "days_overdue": 9,
            "invoice_number": "INV-000124"
        }
    ],
    "summary": {
        "total_piutang": 3000000.0,
        "total_dibayar": 500000.0,
        "total_sisa": 2500000.0,
        "count_belum_lunas": 2,
        "count_lunas": 0,
        "count_overdue": 1
    }
}
```

**Error Response (500):**

```json
{
    "success": false,
    "message": "Gagal mengambil data piutang: [error message]"
}
```

**Response Fields:**

| Field               | Type         | Description                      |
| ------------------- | ------------ | -------------------------------- |
| id_piutang          | integer      | Piutang ID                       |
| id_penjualan        | integer      | Sales invoice ID                 |
| tanggal             | string       | Transaction date (Y-m-d)         |
| tanggal_jatuh_tempo | string\|null | Due date (Y-m-d)                 |
| nama_customer       | string       | Customer name                    |
| outlet              | string       | Outlet name                      |
| jumlah_piutang      | float        | Total receivable amount          |
| jumlah_dibayar      | float        | Paid amount                      |
| sisa_piutang        | float        | Remaining amount                 |
| status              | string       | Status: 'belum_lunas' or 'lunas' |
| is_overdue          | boolean      | Is overdue                       |
| days_overdue        | integer      | Days overdue (0 if not overdue)  |
| invoice_number      | string       | Invoice number (INV-XXXXXX)      |

**Summary Fields:**

| Field             | Type    | Description                  |
| ----------------- | ------- | ---------------------------- |
| total_piutang     | float   | Sum of all jumlah_piutang    |
| total_dibayar     | float   | Sum of all jumlah_dibayar    |
| total_sisa        | float   | Sum of all sisa_piutang      |
| count_belum_lunas | integer | Count of unpaid receivables  |
| count_lunas       | integer | Count of paid receivables    |
| count_overdue     | integer | Count of overdue receivables |

---

### 2. Get Piutang Detail

Get detailed information of a specific piutang including transaction and journal entries.

**Endpoint:** `GET /finance/piutang/{id}/detail`

**Path Parameters:**

| Parameter | Type    | Required | Description |
| --------- | ------- | -------- | ----------- |
| id        | integer | Yes      | Piutang ID  |

**Example Request:**

```bash
GET /finance/piutang/1/detail
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "piutang": {
            "id_piutang": 1,
            "tanggal": "2025-11-24 10:30:00",
            "tanggal_jatuh_tempo": "2025-12-24",
            "nama_customer": "John Doe",
            "telepon": "081234567890",
            "alamat": "Jl. Contoh No. 123",
            "outlet": "Outlet A",
            "jumlah_piutang": 1000000.0,
            "jumlah_dibayar": 500000.0,
            "sisa_piutang": 500000.0,
            "status": "belum_lunas",
            "is_overdue": false,
            "days_overdue": 0
        },
        "penjualan": {
            "id_penjualan": 123,
            "invoice_number": "INV-000123",
            "tanggal": "2025-11-24 10:30:00",
            "total_item": 3,
            "total_harga": 1000000.0,
            "diskon": 0.0,
            "bayar": 500000.0,
            "items": [
                {
                    "nama_produk": "Product A",
                    "jumlah": 2,
                    "harga": 300000.0,
                    "diskon": 0.0,
                    "subtotal": 600000.0
                },
                {
                    "nama_produk": "Product B",
                    "jumlah": 1,
                    "harga": 400000.0,
                    "diskon": 0.0,
                    "subtotal": 400000.0
                }
            ]
        },
        "journals": [
            {
                "id": 45,
                "transaction_number": "JNL-001-000045",
                "transaction_date": "2025-11-24",
                "description": "Penjualan Invoice INV-000123",
                "status": "posted",
                "total_debit": 1000000.0,
                "total_credit": 1000000.0,
                "details": [
                    {
                        "account_code": "1103",
                        "account_name": "Piutang Usaha",
                        "description": "Piutang dari John Doe",
                        "debit": 1000000.0,
                        "credit": 0.0
                    },
                    {
                        "account_code": "4101",
                        "account_name": "Pendapatan Penjualan",
                        "description": "Penjualan produk",
                        "debit": 0.0,
                        "credit": 1000000.0
                    }
                ]
            }
        ]
    }
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Piutang not found"
}
```

**Error Response (500):**

```json
{
    "success": false,
    "message": "Gagal mengambil detail piutang: [error message]"
}
```

**Response Fields:**

**Piutang Object:**

| Field               | Type         | Description          |
| ------------------- | ------------ | -------------------- |
| id_piutang          | integer      | Piutang ID           |
| tanggal             | string       | Transaction datetime |
| tanggal_jatuh_tempo | string\|null | Due date             |
| nama_customer       | string       | Customer name        |
| telepon             | string       | Customer phone       |
| alamat              | string       | Customer address     |
| outlet              | string       | Outlet name          |
| jumlah_piutang      | float        | Total receivable     |
| jumlah_dibayar      | float        | Paid amount          |
| sisa_piutang        | float        | Remaining amount     |
| status              | string       | Status               |
| is_overdue          | boolean      | Is overdue           |
| days_overdue        | integer      | Days overdue         |

**Penjualan Object:**

| Field          | Type    | Description          |
| -------------- | ------- | -------------------- |
| id_penjualan   | integer | Sales ID             |
| invoice_number | string  | Invoice number       |
| tanggal        | string  | Transaction datetime |
| total_item     | integer | Total items          |
| total_harga    | float   | Total price          |
| diskon         | float   | Discount             |
| bayar          | float   | Payment amount       |
| items          | array   | Array of items       |

**Penjualan Item Object:**

| Field       | Type    | Description  |
| ----------- | ------- | ------------ |
| nama_produk | string  | Product name |
| jumlah      | integer | Quantity     |
| harga       | float   | Unit price   |
| diskon      | float   | Discount     |
| subtotal    | float   | Subtotal     |

**Journal Object:**

| Field              | Type    | Description                 |
| ------------------ | ------- | --------------------------- |
| id                 | integer | Journal entry ID            |
| transaction_number | string  | Transaction number          |
| transaction_date   | string  | Transaction date            |
| description        | string  | Description                 |
| status             | string  | Status: 'draft' or 'posted' |
| total_debit        | float   | Total debit                 |
| total_credit       | float   | Total credit                |
| details            | array   | Array of journal details    |

**Journal Detail Object:**

| Field        | Type   | Description   |
| ------------ | ------ | ------------- |
| account_code | string | Account code  |
| account_name | string | Account name  |
| description  | string | Description   |
| debit        | float  | Debit amount  |
| credit       | float  | Credit amount |

---

## Status Codes

| Code | Description        |
| ---- | ------------------ |
| 200  | Success            |
| 404  | Resource not found |
| 422  | Validation error   |
| 500  | Server error       |

---

## Data Types

### Status Enum

-   `belum_lunas` - Unpaid
-   `lunas` - Paid

### Date Format

-   Date: `Y-m-d` (e.g., 2025-11-24)
-   Datetime: `Y-m-d H:i:s` (e.g., 2025-11-24 10:30:00)

### Currency

-   Type: `float`
-   Precision: 2 decimal places
-   Format in response: `1000000.00`
-   Display format: `Rp 1.000.000`

---

## Error Handling

All endpoints return consistent error format:

```json
{
    "success": false,
    "message": "Error description"
}
```

Common errors:

-   Database connection error
-   Invalid outlet_id
-   Invalid date format
-   Piutang not found
-   Missing required parameters

---

## Rate Limiting

No rate limiting currently implemented. Consider adding if needed for production.

---

## Caching

No caching currently implemented. Data is fetched fresh on each request.

---

## Notes

1. **Outlet Filter**: If no outlet_id provided, returns data from all outlets (may be restricted by user permissions in future)

2. **Date Range**: If start_date and end_date not provided, returns all data

3. **Search**: Case-insensitive partial match on customer name

4. **Overdue Calculation**: Based on current date vs tanggal_jatuh_tempo

5. **Journal Entries**: May be empty if no journals created for the piutang

6. **Penjualan Data**: May be null if piutang not linked to a penjualan record

---

## Example Usage

### JavaScript (Fetch API)

```javascript
// Get piutang list
async function getPiutangList(filters) {
    const params = new URLSearchParams(filters);
    const response = await fetch(`/finance/piutang/data?${params}`);
    const data = await response.json();
    return data;
}

// Get piutang detail
async function getPiutangDetail(id) {
    const response = await fetch(`/finance/piutang/${id}/detail`);
    const data = await response.json();
    return data;
}

// Usage
const filters = {
    outlet_id: 1,
    status: "belum_lunas",
    start_date: "2025-01-01",
    end_date: "2025-12-31",
};

const piutangList = await getPiutangList(filters);
const piutangDetail = await getPiutangDetail(1);
```

### cURL

```bash
# Get piutang list
curl -X GET "http://localhost/finance/piutang/data?outlet_id=1&status=belum_lunas" \
  -H "Cookie: laravel_session=..."

# Get piutang detail
curl -X GET "http://localhost/finance/piutang/1/detail" \
  -H "Cookie: laravel_session=..."
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost']);

// Get piutang list
$response = $client->get('/finance/piutang/data', [
    'query' => [
        'outlet_id' => 1,
        'status' => 'belum_lunas'
    ]
]);
$data = json_decode($response->getBody(), true);

// Get piutang detail
$response = $client->get('/finance/piutang/1/detail');
$data = json_decode($response->getBody(), true);
```

---

## Changelog

### Version 1.0.0 (2025-11-24)

-   Initial release
-   GET /finance/piutang/data endpoint
-   GET /finance/piutang/{id}/detail endpoint
-   Filter by outlet, status, date range, search
-   Summary statistics
-   Journal integration
-   Overdue detection
