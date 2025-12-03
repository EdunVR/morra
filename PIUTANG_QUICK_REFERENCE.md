# Quick Reference - Halaman Piutang

## 📁 File Locations

### Backend

-   **Model**: `app/Models/Piutang.php`
-   **Controller**: `app/Http/Controllers/FinanceAccountantController.php`
    -   Method: `piutangIndex()`, `getPiutangData()`, `getPiutangDetail()`
-   **Routes**: `routes/web.php` (line ~512)

### Frontend

-   **View**: `resources/views/admin/finance/piutang/index.blade.php`
-   **Sidebar**: `resources/views/components/sidebar.blade.php` (line ~32)

## 🔗 Routes

```php
// View
GET /finance/piutang                    → finance.piutang.index

// API
GET /finance/piutang/data               → finance.piutang.data
GET /finance/piutang/{id}/detail        → finance.piutang.detail
```

## 📊 Database Schema

```sql
Table: piutang
- id_piutang (PK)
- id_penjualan (FK → penjualan)
- id_member (FK → member)
- id_outlet (FK → outlets)
- tanggal_tempo (datetime)
- tanggal_jatuh_tempo (date)
- nama (varchar)
- piutang (decimal) - legacy
- jumlah_piutang (decimal)
- jumlah_dibayar (decimal)
- sisa_piutang (decimal)
- status (enum: 'belum_lunas', 'lunas')
- created_at, updated_at
```

## 🔧 Model Methods

```php
// Relationships
$piutang->outlet()          // BelongsTo Outlet
$piutang->member()          // BelongsTo Member
$piutang->penjualan()       // BelongsTo Penjualan
$piutang->journalEntries()  // Get related journals

// Scopes
Piutang::byOutlet($outletId)
Piutang::byStatus($status)
Piutang::byDateRange($start, $end)

// Helpers
$piutang->isOverdue()       // bool
$piutang->getDaysOverdue()  // int
```

## 🎨 Frontend Components

### Alpine.js Data

```javascript
{
  filters: {
    outlet_id: '',
    status: 'all',
    start_date: '',
    end_date: '',
    search: ''
  },
  piutangData: [],
  summary: {
    total_piutang: 0,
    total_dibayar: 0,
    total_sisa: 0,
    count_overdue: 0
  },
  detailData: null
}
```

### Key Methods

```javascript
init(); // Initialize
loadPiutangData(); // Load data with filters
showDetail(id); // Show detail modal
closeDetailModal(); // Close modal
refreshData(); // Refresh data
formatCurrency(value); // Format to IDR
formatDate(dateString); // Format date
```

## 📡 API Response Format

### GET /finance/piutang/data

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
            "jumlah_piutang": 1000000,
            "jumlah_dibayar": 500000,
            "sisa_piutang": 500000,
            "status": "belum_lunas",
            "is_overdue": false,
            "days_overdue": 0,
            "invoice_number": "INV-000123"
        }
    ],
    "summary": {
        "total_piutang": 5000000,
        "total_dibayar": 2000000,
        "total_sisa": 3000000,
        "count_belum_lunas": 5,
        "count_lunas": 3,
        "count_overdue": 2
    }
}
```

### GET /finance/piutang/{id}/detail

```json
{
  "success": true,
  "data": {
    "piutang": {
      "id_piutang": 1,
      "tanggal": "2025-11-24",
      "nama_customer": "John Doe",
      "jumlah_piutang": 1000000,
      "status": "belum_lunas"
    },
    "penjualan": {
      "invoice_number": "INV-000123",
      "total_harga": 1000000,
      "items": [...]
    },
    "journals": [
      {
        "transaction_number": "JNL-001",
        "total_debit": 1000000,
        "total_credit": 1000000,
        "details": [...]
      }
    ]
  }
}
```

## 🎯 Common Tasks

### Add New Filter

1. Add to `filters` object in Alpine.js
2. Add filter UI in view
3. Update `loadPiutangData()` to include new param
4. Update `getPiutangData()` in controller

### Modify Table Columns

1. Update table header in view
2. Update table body template
3. Ensure data is available in API response

### Add New Action Button

1. Add button in table actions column
2. Create new Alpine.js method
3. Create new controller method if needed
4. Add new route if needed

## 🔍 Debugging

### Check Data Loading

```javascript
// In browser console
console.log(Alpine.$data(document.querySelector("[x-data]")));
```

### Check API Response

```bash
# Using curl
curl http://localhost/finance/piutang/data?outlet_id=1

# Or check Network tab in DevTools
```

### Common Issues

1. **No data showing**: Check outlet_id filter
2. **Modal not opening**: Check console for JS errors
3. **Wrong currency format**: Check `formatCurrency()` method
4. **Journals not showing**: Check reference_type and reference_number

## 📝 Code Snippets

### Add Custom Filter

```php
// In controller
$customFilter = $request->get('custom_filter');
if ($customFilter) {
    $query->where('custom_field', $customFilter);
}
```

### Add Custom Scope

```php
// In model
public function scopeByCustom($query, $value)
{
    return $query->where('custom_field', $value);
}
```

### Add Custom Summary

```javascript
// In Alpine.js
summary.custom_count = piutangData.filter((p) => p.custom_field).length;
```

## 🚀 Performance Tips

1. **Eager Loading**: Already implemented with `with(['outlet', 'member', 'penjualan'])`
2. **Indexing**: Ensure database indexes on:
    - `id_outlet`
    - `id_member`
    - `status`
    - `created_at`
    - `tanggal_jatuh_tempo`
3. **Pagination**: Consider adding if data > 100 records
4. **Caching**: Consider caching summary data

## 📚 Related Files

-   `app/Models/Member.php` - Customer model
-   `app/Models/Penjualan.php` - Sales invoice model
-   `app/Models/JournalEntry.php` - Journal model
-   `app/Models/Outlet.php` - Outlet model
-   `resources/views/components/layouts/admin.blade.php` - Main layout

## 🔐 Security Notes

-   All queries filtered by outlet_id
-   User authentication required (via middleware)
-   SQL injection protected (using Eloquent)
-   XSS protected (Blade escaping)

## 📞 Support

For issues or questions:

1. Check `PIUTANG_TESTING_GUIDE.md`
2. Check `PIUTANG_IMPLEMENTATION_COMPLETE.md`
3. Review Laravel logs: `storage/logs/laravel.log`
4. Check browser console for JS errors
