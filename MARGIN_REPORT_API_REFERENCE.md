# Margin Report API Reference

## Endpoints

### 1. Get Margin Report Page

```
GET /admin/penjualan/laporan-margin
```

**Description:** Menampilkan halaman laporan margin

**Authentication:** Required (middleware: auth)

**Permission:** `sales.invoice.view`

**Response:** HTML View

**Controller Method:** `MarginReportController@index`

---

### 2. Get Margin Data (API)

```
GET /admin/penjualan/laporan-margin/data
```

**Description:** Mengambil data margin dari Invoice dan POS

**Authentication:** Required

**Permission:** `sales.invoice.view`

**Query Parameters:**

| Parameter  | Type    | Required | Description         | Example      |
| ---------- | ------- | -------- | ------------------- | ------------ |
| outlet_id  | integer | No       | Filter by outlet ID | `1`          |
| start_date | date    | Yes      | Start date (Y-m-d)  | `2024-12-01` |
| end_date   | date    | Yes      | End date (Y-m-d)    | `2024-12-31` |

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": "invoice_123",
            "source": "invoice",
            "tanggal": "2024-12-01 10:30:00",
            "outlet": "Outlet Pusat",
            "produk": "Produk A",
            "qty": 5,
            "hpp": 10000,
            "harga_jual": 15000,
            "subtotal": 75000,
            "profit": 25000,
            "margin_pct": 33.33,
            "payment_type": "Cash"
        },
        {
            "id": "pos_456",
            "source": "pos",
            "tanggal": "2024-12-01 14:20:00",
            "outlet": "Outlet Cabang",
            "produk": "Produk B",
            "qty": 3,
            "hpp": 20000,
            "harga_jual": 25000,
            "subtotal": 75000,
            "profit": 15000,
            "margin_pct": 20.0,
            "payment_type": "QRIS"
        }
    ]
}
```

**Error Response (500):**

```json
{
    "success": false,
    "message": "Gagal memuat data: [error message]"
}
```

**Data Fields:**

| Field        | Type     | Description                           |
| ------------ | -------- | ------------------------------------- |
| id           | string   | Unique identifier (source_id)         |
| source       | string   | Data source: "invoice" or "pos"       |
| tanggal      | datetime | Transaction date                      |
| outlet       | string   | Outlet name                           |
| produk       | string   | Product name                          |
| qty          | integer  | Quantity sold                         |
| hpp          | decimal  | Cost per unit (HPP)                   |
| harga_jual   | decimal  | Selling price per unit                |
| subtotal     | decimal  | Total amount (qty × price)            |
| profit       | decimal  | Profit amount (subtotal - total_hpp)  |
| margin_pct   | decimal  | Margin percentage                     |
| payment_type | string   | Payment method: "Cash", "QRIS", "BON" |

**Controller Method:** `MarginReportController@getData`

---

### 3. Export Margin Report to PDF

```
GET /admin/penjualan/laporan-margin/export-pdf
```

**Description:** Export laporan margin ke PDF format

**Authentication:** Required

**Permission:** `sales.invoice.view`

**Query Parameters:**

| Parameter  | Type    | Required | Description         | Example      |
| ---------- | ------- | -------- | ------------------- | ------------ |
| outlet_id  | integer | No       | Filter by outlet ID | `1`          |
| start_date | date    | Yes      | Start date (Y-m-d)  | `2024-12-01` |
| end_date   | date    | Yes      | End date (Y-m-d)    | `2024-12-31` |

**Success Response (200):**

-   Content-Type: `application/pdf`
-   PDF Stream (can be viewed in browser or downloaded)

**Error Response (500):**

```json
{
    "success": false,
    "message": "Gagal export PDF: [error message]"
}
```

**PDF Content:**

-   Header with title and period
-   Outlet information
-   Summary boxes (Total HPP, Penjualan, Profit, Avg Margin)
-   Complete data table
-   Footer with generation timestamp

**Controller Method:** `MarginReportController@exportPdf`

---

## Data Sources

### Invoice Data

**Table:** `penjualan_detail`

**Joins:**

-   `penjualan` (main transaction)
-   `produk` (product info)
-   `outlet` (outlet info)
-   `member` (customer info)

**Filters:**

-   Exclude POS-generated invoices (not in `pos_sales.id_penjualan`)
-   Filter by outlet_id (if provided)
-   Filter by date range (created_at)

**Calculations:**

```php
$profit = $detail->subtotal - ($detail->hpp * $detail->jumlah);
$marginPct = $detail->subtotal > 0 ? ($profit / $detail->subtotal) * 100 : 0;
```

**Payment Type Logic:**

```php
$piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
$paymentType = $piutang && $piutang->sisa_piutang > 0 ? 'BON' : 'Cash';
```

---

### POS Data

**Table:** `pos_sale_items`

**Joins:**

-   `pos_sales` (main transaction)
-   `produk` (product info)
-   `outlet` (outlet info)

**Filters:**

-   Only items with `tipe = 'produk'`
-   Filter by outlet_id (if provided)
-   Filter by date range (tanggal)

**Calculations:**

```php
$hpp = $item->produk ? $item->produk->calculateHppBarangDagang() : 0;
$profit = $item->subtotal - ($hpp * $item->kuantitas);
$marginPct = $item->subtotal > 0 ? ($profit / $item->subtotal) * 100 : 0;
```

**Payment Type Logic:**

```php
$paymentType = $item->posSale->is_bon ? 'BON' : ucfirst($item->posSale->jenis_pembayaran);
```

---

## Business Logic

### Margin Calculation Formula

```
Margin % = (Profit / Subtotal) × 100

Where:
- Profit = Subtotal - (HPP × Quantity)
- Subtotal = Selling Price × Quantity
```

### Margin Categories

| Category  | Range  | Color  | Description        |
| --------- | ------ | ------ | ------------------ |
| Excellent | ≥ 30%  | Green  | High profit margin |
| Good      | 15-29% | Blue   | Healthy margin     |
| Low       | 5-14%  | Orange | Minimal margin     |
| Very Low  | < 5%   | Red    | Risky margin       |

### Profit Categories

| Category | Condition  | Color |
| -------- | ---------- | ----- |
| Positive | Profit ≥ 0 | Green |
| Negative | Profit < 0 | Red   |

---

## Frontend Integration

### Alpine.js Component

```javascript
function marginReportApp() {
  return {
    isLoading: false,
    outlets: [],
    marginData: [],
    filteredData: [],
    filters: {
      outlet_id: '',
      start_date: '',
      end_date: '',
      search: ''
    },
    summary: {
      total_items: 0,
      total_hpp: 0,
      total_penjualan: 0,
      total_profit: 0,
      avg_margin: 0
    },

    async loadData() { ... },
    filterData() { ... },
    calculateSummary() { ... },
    exportPdf() { ... }
  }
}
```

### API Call Example

```javascript
const params = new URLSearchParams({
    outlet_id: this.filters.outlet_id || "",
    start_date: this.filters.start_date,
    end_date: this.filters.end_date,
});

const response = await fetch(`/admin/penjualan/laporan-margin/data?${params}`, {
    headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});

const data = await response.json();
```

---

## Database Schema

### Required Tables

#### penjualan_detail

```sql
- id_penjualan_detail (PK)
- id_penjualan (FK)
- id_produk (FK)
- jumlah
- harga_jual
- subtotal
- hpp
- created_at
```

#### penjualan

```sql
- id_penjualan (PK)
- id_outlet (FK)
- id_member (FK)
- created_at
```

#### pos_sale_items

```sql
- id (PK)
- id_pos_sale (FK)
- id_produk (FK)
- nama_produk
- kuantitas
- harga
- subtotal
- tipe
```

#### pos_sales

```sql
- id (PK)
- id_penjualan (FK, nullable)
- id_outlet (FK)
- tanggal
- jenis_pembayaran
- is_bon
```

#### produk

```sql
- id_produk (PK)
- nama_produk
- hpp (for invoice)
- calculateHppBarangDagang() method (for POS)
```

#### piutang

```sql
- id_piutang (PK)
- id_penjualan (FK)
- sisa_piutang
```

---

## Error Handling

### Common Errors

**1. Missing Date Parameters**

```json
{
    "success": false,
    "message": "Start date and end date are required"
}
```

**2. Invalid Outlet ID**

-   System will return empty data
-   No error thrown

**3. Database Connection Error**

```json
{
    "success": false,
    "message": "Gagal memuat data: SQLSTATE[...]"
}
```

**4. PDF Generation Error**

```json
{
    "success": false,
    "message": "Gagal export PDF: [error details]"
}
```

### Error Logging

All errors are logged to `storage/logs/laravel.log`:

```php
Log::error('Error loading margin report: ' . $e->getMessage());
```

---

## Performance Considerations

### Optimization Tips

1. **Use Eager Loading**

    ```php
    ->with(['produk', 'penjualan.outlet', 'penjualan.member'])
    ```

2. **Index Database Columns**

    - `penjualan.id_outlet`
    - `penjualan.created_at`
    - `pos_sales.id_outlet`
    - `pos_sales.tanggal`
    - `pos_sale_items.tipe`

3. **Limit Date Range**

    - Default: 7 days
    - Recommended max: 3 months

4. **Client-Side Search**
    - Search filtering done in frontend
    - Reduces server load

### Expected Performance

-   **< 100 records**: < 1 second
-   **100-1000 records**: 1-3 seconds
-   **1000+ records**: 3-5 seconds
-   **PDF generation**: +2-3 seconds

---

## Security

### Authentication

-   All endpoints require authentication
-   Middleware: `auth`

### Authorization

-   Permission check: `sales.invoice.view`
-   Implemented in controller or middleware

### CSRF Protection

-   All POST/DELETE requests require CSRF token
-   GET requests are CSRF-exempt

### SQL Injection Prevention

-   Using Eloquent ORM
-   Parameterized queries
-   No raw SQL with user input

### XSS Prevention

-   Blade template escaping
-   `{{ }}` syntax auto-escapes
-   PDF generation uses safe HTML

---

## Testing

### Unit Tests (Recommended)

```php
// Test margin calculation
public function test_margin_calculation()
{
    $subtotal = 75000;
    $hpp = 10000;
    $qty = 5;

    $profit = $subtotal - ($hpp * $qty);
    $margin = ($profit / $subtotal) * 100;

    $this->assertEquals(25000, $profit);
    $this->assertEquals(33.33, round($margin, 2));
}
```

### Integration Tests

```php
// Test API endpoint
public function test_get_margin_data()
{
    $response = $this->get('/admin/penjualan/laporan-margin/data?start_date=2024-12-01&end_date=2024-12-31');

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
}
```

---

## Changelog

### Version 1.0 (December 1, 2024)

-   ✅ Initial implementation
-   ✅ Invoice data integration
-   ✅ POS data integration
-   ✅ Margin calculation
-   ✅ PDF export
-   ✅ Filter by outlet and date
-   ✅ Search by product name
-   ✅ Summary cards
-   ✅ Color-coded indicators

---

## Support

For issues or questions:

1. Check `storage/logs/laravel.log`
2. Check browser console for JS errors
3. Verify database connections
4. Verify permissions
5. Contact development team

---

**Last Updated:** December 1, 2024
**Version:** 1.0
**Status:** Production Ready ✅
