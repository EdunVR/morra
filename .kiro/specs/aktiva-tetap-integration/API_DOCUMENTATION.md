# API Documentation - Fixed Assets Integration

## Overview

This document provides comprehensive API documentation for the Fixed Assets (Aktiva Tetap) integration with the accounting journal system. All endpoints are prefixed with `/finance/fixed-assets` and require authentication.

## Base URL

```
/finance/fixed-assets
```

## Authentication

All endpoints require authentication via Laravel Sanctum or session-based authentication. Include the appropriate authentication headers or cookies with each request.

---

## Endpoints

### 1. Get Fixed Assets Data

**Endpoint:** `GET /finance/fixed-assets/data`

**Description:** Retrieve paginated list of fixed assets with statistics and filtering options.

**Query Parameters:**

| Parameter | Type    | Required | Description                                                                  |
| --------- | ------- | -------- | ---------------------------------------------------------------------------- |
| outlet_id | integer | No       | Filter by outlet ID                                                          |
| category  | string  | No       | Filter by category (land, building, vehicle, equipment, furniture, computer) |
| status    | string  | No       | Filter by status (active, inactive, sold, disposed)                          |
| search    | string  | No       | Search by asset code or name                                                 |
| per_page  | integer | No       | Items per page (default: 10)                                                 |
| page      | integer | No       | Page number (default: 1)                                                     |

**Response Format:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "AST-202401-001",
            "name": "Gedung Kantor",
            "category": "building",
            "location": "Jakarta",
            "acquisition_date": "2024-01-15",
            "acquisition_cost": 500000000.0,
            "salvage_value": 50000000.0,
            "useful_life": 20,
            "depreciation_method": "straight_line",
            "accumulated_depreciation": 50000000.0,
            "book_value": 450000000.0,
            "status": "active",
            "outlet": {
                "id_outlet": 1,
                "nama_outlet": "Kantor Pusat"
            }
        }
    ],
    "stats": {
        "totalAssets": 24,
        "activeAssets": 22,
        "totalAcquisitionCost": 1250000000.0,
        "totalDepreciation": 250000000.0,
        "totalBookValue": 1000000000.0,
        "depreciationRate": 20.0
    },
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 24,
        "last_page": 3
    }
}
```

**Error Codes:**

-   `401`: Unauthorized - Authentication required
-   `403`: Forbidden - User doesn't have permission
-   `500`: Internal Server Error

---

### 2. Store Fixed Asset

**Endpoint:** `POST /finance/fixed-assets`

**Description:** Create a new fixed asset and automatically generate acquisition journal entry.

**Request Body:**

```json
{
    "outlet_id": 1,
    "code": "AST-202401-001",
    "name": "Gedung Kantor",
    "category": "building",
    "location": "Jakarta",
    "acquisition_date": "2024-01-15",
    "acquisition_cost": 500000000.0,
    "salvage_value": 50000000.0,
    "useful_life": 20,
    "depreciation_method": "straight_line",
    "asset_account_id": 101,
    "depreciation_expense_account_id": 201,
    "accumulated_depreciation_account_id": 102,
    "payment_account_id": 301,
    "description": "Gedung kantor pusat 3 lantai"
}
```

**Validation Rules:**

| Field                               | Rules                                                                             |
| ----------------------------------- | --------------------------------------------------------------------------------- |
| outlet_id                           | required, exists:outlets,id_outlet                                                |
| code                                | required, string, max:50, unique:fixed_assets,code                                |
| name                                | required, string, max:255                                                         |
| category                            | required, in:land,building,vehicle,equipment,furniture,computer                   |
| location                            | nullable, string, max:255                                                         |
| acquisition_date                    | required, date, before_or_equal:today                                             |
| acquisition_cost                    | required, numeric, min:0                                                          |
| salvage_value                       | required, numeric, min:0, lt:acquisition_cost                                     |
| useful_life                         | required, integer, min:1                                                          |
| depreciation_method                 | required, in:straight_line,declining_balance,double_declining,units_of_production |
| asset_account_id                    | required, exists:chart_of_accounts,id                                             |
| depreciation_expense_account_id     | required, exists:chart_of_accounts,id                                             |
| accumulated_depreciation_account_id | required, exists:chart_of_accounts,id                                             |
| payment_account_id                  | required, exists:chart_of_accounts,id                                             |
| description                         | nullable, string                                                                  |

**Response Format:**

```json
{
    "success": true,
    "message": "Aktiva tetap berhasil ditambahkan dan jurnal perolehan telah dibuat",
    "data": {
        "asset": {
            "id": 1,
            "code": "AST-202401-001",
            "name": "Gedung Kantor",
            "book_value": 500000000.0,
            "status": "active"
        },
        "journal": {
            "id": 1001,
            "transaction_number": "FA-ACQ-AST-202401-001",
            "transaction_date": "2024-01-15",
            "total_debit": 500000000.0,
            "total_credit": 500000000.0,
            "status": "posted"
        }
    }
}
```

**Error Codes:**

-   `400`: Bad Request - Validation failed
-   `401`: Unauthorized
-   `422`: Unprocessable Entity - Business logic validation failed
-   `500`: Internal Server Error - Journal creation failed

**Common Validation Errors:**

-   "Akun aset harus memiliki tipe 'asset'"
-   "Akun beban penyusutan harus memiliki tipe 'expense'"
-   "Akun akumulasi penyusutan harus memiliki tipe 'asset' dengan kategori 'contra_asset'"
-   "Nilai residu tidak boleh lebih besar dari nilai perolehan"
-   "Tanggal perolehan tidak boleh lebih besar dari hari ini"

---

### 3. Update Fixed Asset

**Endpoint:** `PUT /finance/fixed-assets/{id}`

**Description:** Update fixed asset data (master data only, does not modify existing journals).

**Request Body:** Same as Store Fixed Asset (except code is optional)

**Validation:** Asset cannot be updated if it has posted depreciation entries.

**Response Format:**

```json
{
    "success": true,
    "message": "Aktiva tetap berhasil diperbarui",
    "data": {
        "id": 1,
        "code": "AST-202401-001",
        "name": "Gedung Kantor (Updated)",
        "book_value": 500000000.0
    }
}
```

**Error Codes:**

-   `400`: Bad Request - Validation failed
-   `404`: Not Found - Asset not found
-   `422`: Unprocessable Entity - Asset has posted depreciation
-   `500`: Internal Server Error

---

### 4. Delete Fixed Asset

**Endpoint:** `DELETE /finance/fixed-assets/{id}`

**Description:** Delete a fixed asset (only if no posted journals or depreciation records exist).

**Response Format:**

```json
{
    "success": true,
    "message": "Aktiva tetap berhasil dihapus"
}
```

**Error Codes:**

-   `404`: Not Found - Asset not found
-   `422`: Unprocessable Entity - Asset has posted journals or depreciation records
-   `500`: Internal Server Error

---

### 5. Toggle Fixed Asset Status

**Endpoint:** `PATCH /finance/fixed-assets/{id}/toggle`

**Description:** Toggle asset status between active and inactive.

**Response Format:**

```json
{
    "success": true,
    "message": "Status aktiva tetap berhasil diubah",
    "data": {
        "id": 1,
        "status": "inactive"
    }
}
```

---

### 6. Show Fixed Asset Detail

**Endpoint:** `GET /finance/fixed-assets/{id}`

**Description:** Get detailed information about a specific fixed asset including depreciation history and related journals.

**Response Format:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "AST-202401-001",
        "name": "Gedung Kantor",
        "category": "building",
        "acquisition_cost": 500000000.0,
        "book_value": 450000000.0,
        "accumulated_depreciation": 50000000.0,
        "depreciations": [
            {
                "id": 1,
                "period": 1,
                "depreciation_date": "2024-02-01",
                "amount": 2083333.33,
                "status": "posted",
                "journal_entry": {
                    "id": 1002,
                    "transaction_number": "FA-DEP-AST-202401-001-1"
                }
            }
        ],
        "acquisition_journal": {
            "id": 1001,
            "transaction_number": "FA-ACQ-AST-202401-001"
        }
    }
}
```

---

### 7. Generate Asset Code

**Endpoint:** `GET /finance/fixed-assets/generate-code`

**Description:** Generate a unique asset code.

**Query Parameters:**

| Parameter | Type    | Required | Description                   |
| --------- | ------- | -------- | ----------------------------- |
| outlet_id | integer | Yes      | Outlet ID for code generation |

**Response Format:**

```json
{
    "success": true,
    "code": "AST-202411-001"
}
```

**Code Format:** `AST-{YYYYMM}-{sequence}`

---

### 8. Calculate Depreciation

**Endpoint:** `POST /finance/fixed-assets/depreciation/calculate`

**Description:** Calculate depreciation for active assets in a specific period.

**Request Body:**

```json
{
    "outlet_id": 1,
    "period_month": 11,
    "period_year": 2024,
    "asset_ids": [1, 2, 3]
}
```

**Validation Rules:**

| Field        | Rules                              |
| ------------ | ---------------------------------- |
| outlet_id    | required, exists:outlets,id_outlet |
| period_month | required, integer, between:1,12    |
| period_year  | required, integer, min:2000        |
| asset_ids    | nullable, array                    |
| asset_ids.\* | exists:fixed_assets,id             |

**Response Format:**

```json
{
    "success": true,
    "message": "Perhitungan penyusutan berhasil",
    "data": {
        "assets_processed": 15,
        "total_depreciation": 25000000.0,
        "depreciations_created": 15,
        "period": "November 2024"
    }
}
```

---

### 9. Batch Depreciation

**Endpoint:** `POST /finance/fixed-assets/depreciation/batch`

**Description:** Batch process depreciation calculation and optional auto-posting for all active assets.

**Request Body:**

```json
{
    "outlet_id": 1,
    "period_month": 11,
    "period_year": 2024,
    "auto_post": true
}
```

**Response Format:**

```json
{
    "success": true,
    "message": "Batch penyusutan berhasil diproses",
    "data": {
        "assets_processed": 24,
        "total_depreciation": 50000000.0,
        "journals_created": 24,
        "errors": []
    }
}
```

---

### 10. Post Depreciation

**Endpoint:** `POST /finance/fixed-assets/depreciation/{id}/post`

**Description:** Post a draft depreciation entry and create journal entry.

**Response Format:**

```json
{
    "success": true,
    "message": "Penyusutan berhasil diposting",
    "data": {
        "depreciation": {
            "id": 1,
            "status": "posted",
            "amount": 2083333.33
        },
        "journal": {
            "id": 1002,
            "transaction_number": "FA-DEP-AST-202401-001-1",
            "status": "posted"
        }
    }
}
```

**Error Codes:**

-   `404`: Not Found - Depreciation not found
-   `422`: Unprocessable Entity - Already posted or invalid status
-   `500`: Internal Server Error - Journal creation failed

---

### 11. Reverse Depreciation

**Endpoint:** `POST /finance/fixed-assets/depreciation/{id}/reverse`

**Description:** Reverse a posted depreciation entry by creating a reversing journal entry.

**Response Format:**

```json
{
    "success": true,
    "message": "Penyusutan berhasil di-reverse",
    "data": {
        "depreciation": {
            "id": 1,
            "status": "reversed"
        },
        "reversing_journal": {
            "id": 1003,
            "transaction_number": "FA-REV-AST-202401-001-1",
            "status": "posted"
        }
    }
}
```

**Error Codes:**

-   `404`: Not Found - Depreciation not found
-   `422`: Unprocessable Entity - Not posted or already reversed

---

### 12. Depreciation History Data

**Endpoint:** `GET /finance/fixed-assets/depreciation/history`

**Description:** Get depreciation history with filtering options.

**Query Parameters:**

| Parameter | Type    | Required | Description                                |
| --------- | ------- | -------- | ------------------------------------------ |
| asset_id  | integer | No       | Filter by asset ID                         |
| month     | integer | No       | Filter by month (1-12)                     |
| year      | integer | No       | Filter by year                             |
| status    | string  | No       | Filter by status (draft, posted, reversed) |
| per_page  | integer | No       | Items per page                             |

**Response Format:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "asset_code": "AST-202401-001",
            "asset_name": "Gedung Kantor",
            "period": 1,
            "depreciation_date": "2024-02-01",
            "amount": 2083333.33,
            "accumulated_depreciation": 2083333.33,
            "book_value": 497916666.67,
            "status": "posted",
            "journal_number": "FA-DEP-AST-202401-001-1"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 24
    }
}
```

---

### 13. Dispose Asset

**Endpoint:** `POST /finance/fixed-assets/{id}/dispose`

**Description:** Record asset disposal and create journal entries with gain/loss calculation.

**Request Body:**

```json
{
    "disposal_date": "2024-11-15",
    "disposal_value": 400000000.0,
    "disposal_notes": "Dijual ke PT ABC"
}
```

**Validation Rules:**

| Field          | Rules                    |
| -------------- | ------------------------ |
| disposal_date  | required, date           |
| disposal_value | required, numeric, min:0 |
| disposal_notes | nullable, string         |

**Response Format:**

```json
{
    "success": true,
    "message": "Pelepasan aktiva tetap berhasil dicatat",
    "data": {
        "asset": {
            "id": 1,
            "status": "sold",
            "disposal_date": "2024-11-15",
            "disposal_value": 400000000.0
        },
        "gain_loss": {
            "type": "loss",
            "amount": 50000000.0
        },
        "journal": {
            "id": 1004,
            "transaction_number": "FA-DSP-AST-202401-001",
            "status": "posted"
        }
    }
}
```

**Error Codes:**

-   `404`: Not Found - Asset not found
-   `422`: Unprocessable Entity - Asset not active or already disposed

---

### 14. Fixed Assets Statistics

**Endpoint:** `GET /finance/fixed-assets/stats`

**Description:** Get statistics for dashboard display.

**Query Parameters:**

| Parameter | Type    | Required | Description         |
| --------- | ------- | -------- | ------------------- |
| outlet_id | integer | No       | Filter by outlet ID |

**Response Format:**

```json
{
    "success": true,
    "data": {
        "total_assets": 24,
        "active_assets": 22,
        "total_acquisition_cost": 1250000000.0,
        "total_depreciation": 250000000.0,
        "total_book_value": 1000000000.0,
        "depreciation_rate": 20.0,
        "by_category": [
            {
                "category": "building",
                "count": 5,
                "total_value": 800000000.0
            },
            {
                "category": "vehicle",
                "count": 10,
                "total_value": 300000000.0
            }
        ]
    }
}
```

---

### 15. Asset Value Chart Data

**Endpoint:** `GET /finance/fixed-assets/chart/value`

**Description:** Get data for asset value chart (acquisition cost vs book value over time).

**Query Parameters:**

| Parameter | Type    | Required | Description         |
| --------- | ------- | -------- | ------------------- |
| outlet_id | integer | No       | Filter by outlet ID |

**Response Format:**

```json
{
    "success": true,
    "data": {
        "labels": ["2020", "2021", "2022", "2023", "2024"],
        "datasets": [
            {
                "label": "Nilai Perolehan",
                "data": [
                    500000000, 750000000, 1000000000, 1200000000, 1250000000
                ]
            },
            {
                "label": "Nilai Buku",
                "data": [
                    500000000, 700000000, 900000000, 1050000000, 1000000000
                ]
            }
        ]
    }
}
```

---

### 16. Asset Distribution Data

**Endpoint:** `GET /finance/fixed-assets/chart/distribution`

**Description:** Get data for asset distribution pie chart by category.

**Query Parameters:**

| Parameter | Type    | Required | Description         |
| --------- | ------- | -------- | ------------------- |
| outlet_id | integer | No       | Filter by outlet ID |

**Response Format:**

```json
{
    "success": true,
    "data": {
        "labels": ["Building", "Vehicle", "Equipment", "Furniture", "Computer"],
        "datasets": [
            {
                "data": [800000000, 300000000, 100000000, 30000000, 20000000],
                "backgroundColor": [
                    "#FF6384",
                    "#36A2EB",
                    "#FFCE56",
                    "#4BC0C0",
                    "#9966FF"
                ]
            }
        ]
    }
}
```

---

### 17. Export Fixed Assets

**Endpoint:** `GET /finance/fixed-assets/export`

**Description:** Export fixed assets data to Excel format.

**Query Parameters:**

| Parameter | Type    | Required | Description                                 |
| --------- | ------- | -------- | ------------------------------------------- |
| outlet_id | integer | No       | Filter by outlet ID                         |
| category  | string  | No       | Filter by category                          |
| status    | string  | No       | Filter by status                            |
| format    | string  | No       | Export format (excel, pdf) - default: excel |

**Response:** File download (Excel or PDF)

---

## Common Error Response Format

All error responses follow this format:

```json
{
    "success": false,
    "message": "Error message description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

## HTTP Status Codes

-   `200`: Success
-   `201`: Created
-   `400`: Bad Request - Invalid input
-   `401`: Unauthorized - Authentication required
-   `403`: Forbidden - Insufficient permissions
-   `404`: Not Found - Resource not found
-   `422`: Unprocessable Entity - Business logic validation failed
-   `500`: Internal Server Error - Server-side error

## Rate Limiting

API requests are subject to rate limiting. Default limits:

-   60 requests per minute for authenticated users
-   10 requests per minute for unauthenticated requests

## Depreciation Methods

### Straight Line

```
Monthly Depreciation = (Acquisition Cost - Salvage Value) / Useful Life / 12
```

### Declining Balance (150%)

```
Rate = 1.5 / Useful Life
Monthly Depreciation = Book Value × Rate / 12
```

### Double Declining Balance (200%)

```
Rate = 2 / Useful Life
Monthly Depreciation = Book Value × Rate / 12
```

## Journal Entry Formats

### Acquisition Journal

```
Debit: Asset Account = Acquisition Cost
Credit: Payment Account = Acquisition Cost
```

### Depreciation Journal

```
Debit: Depreciation Expense Account = Depreciation Amount
Credit: Accumulated Depreciation Account = Depreciation Amount
```

### Disposal Journal (with Gain)

```
Debit: Payment Account = Disposal Value
Debit: Accumulated Depreciation Account = Total Accumulated Depreciation
Credit: Asset Account = Acquisition Cost
Credit: Gain on Disposal = Gain Amount
```

### Disposal Journal (with Loss)

```
Debit: Payment Account = Disposal Value
Debit: Accumulated Depreciation Account = Total Accumulated Depreciation
Debit: Loss on Disposal = Loss Amount
Credit: Asset Account = Acquisition Cost
```

## Notes

-   All monetary values are in Indonesian Rupiah (IDR)
-   All dates follow ISO 8601 format (YYYY-MM-DD)
-   Decimal values use 2 decimal places
-   All journal entries are automatically posted with status 'posted'
-   Account balances are automatically updated when journals are posted
-   Transactions use database transactions to ensure data integrity
