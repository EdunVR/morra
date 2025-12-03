# Laporan Margin - Implementation Complete ✅

## Overview

Implementasi halaman Laporan Margin & Profit telah selesai. Fitur ini menampilkan analisis margin dan keuntungan per produk dari transaksi Invoice dan POS.

## Files Created/Modified

### 1. Controller

-   **app/Http/Controllers/MarginReportController.php** ✅
    -   `index()` - Menampilkan halaman laporan margin
    -   `getData()` - API untuk mengambil data margin (Invoice + POS)
    -   `exportPdf()` - Export laporan ke PDF

### 2. Views

-   **resources/views/admin/penjualan/margin/index.blade.php** ✅

    -   Interface laporan margin dengan Alpine.js
    -   Filter: Outlet, Tanggal Mulai, Tanggal Akhir, Search Produk
    -   Summary cards: Total Items, Total HPP, Total Penjualan, Total Profit
    -   Table dengan kolom: Source, Tanggal, Outlet, Produk, Qty, HPP, Harga Jual, Subtotal, Profit, Margin %, Pembayaran
    -   Color-coded margin badges:
        -   Hijau (≥30%): Margin tinggi
        -   Biru (15-29%): Margin medium
        -   Orange (5-14%): Margin rendah
        -   Merah (<5%): Margin sangat rendah
    -   Modal PDF preview

-   **resources/views/admin/penjualan/margin/pdf.blade.php** ✅
    -   Template PDF landscape A4
    -   Summary boxes dengan total HPP, penjualan, profit, dan rata-rata margin
    -   Table lengkap dengan color-coded badges
    -   Professional styling

### 3. Routes

-   **routes/web.php** ✅
    -   Added MarginReportController import
    -   Added routes:
        -   `GET /admin/penjualan/laporan-margin` → margin.index
        -   `GET /admin/penjualan/laporan-margin/data` → margin.data
        -   `GET /admin/penjualan/laporan-margin/export-pdf` → margin.export-pdf

### 4. Sidebar Menu

-   **resources/views/components/sidebar.blade.php** ✅
    -   Menu "Laporan Margin" sudah ada di section Penjualan & Pemasaran

## Features

### 1. Data Integration

-   ✅ Mengambil data dari Invoice (PenjualanDetail)
-   ✅ Mengambil data dari POS (PosSaleItem)
-   ✅ Exclude Invoice yang di-generate dari POS (no duplicate)
-   ✅ Menghitung HPP dari produk
-   ✅ Menghitung profit: Subtotal - (HPP × Qty)
-   ✅ Menghitung margin %: (Profit / Subtotal) × 100

### 2. Filters

-   ✅ Filter by Outlet
-   ✅ Filter by Date Range (Start Date - End Date)
-   ✅ Search by Product Name (client-side filtering)
-   ✅ Default: Last 7 days

### 3. Summary Cards

-   ✅ Total Items (jumlah transaksi)
-   ✅ Total HPP (total modal)
-   ✅ Total Penjualan (total revenue)
-   ✅ Total Profit (total keuntungan)
-   ✅ Average Margin % (rata-rata margin)

### 4. Table Display

-   ✅ Source badge (Invoice/POS)
-   ✅ Tanggal transaksi
-   ✅ Outlet
-   ✅ Nama Produk
-   ✅ Quantity
-   ✅ HPP per unit
-   ✅ Harga Jual per unit
-   ✅ Subtotal
-   ✅ Profit (color-coded: green for positive, red for negative)
-   ✅ Margin % (color-coded badge based on percentage)
-   ✅ Payment Type (Cash/QRIS/BON)

### 5. Export PDF

-   ✅ Landscape A4 format
-   ✅ Summary boxes at top
-   ✅ Complete table with all data
-   ✅ Color-coded badges for easy reading
-   ✅ Professional styling
-   ✅ Modal preview before download

### 6. UI/UX

-   ✅ Modern design with Tailwind CSS
-   ✅ Responsive layout
-   ✅ Loading states
-   ✅ Empty states
-   ✅ Smooth transitions
-   ✅ Color-coded visual indicators
-   ✅ Real-time summary calculations

## Data Flow

```
1. User opens /admin/penjualan/laporan-margin
2. MarginReportController@index loads outlets
3. Alpine.js init() calls loadData()
4. loadData() fetches from /laporan-margin/data
5. Controller queries:
   - PenjualanDetail (Invoice items)
   - PosSaleItem (POS items)
6. Calculate for each item:
   - HPP from produk
   - Profit = Subtotal - (HPP × Qty)
   - Margin % = (Profit / Subtotal) × 100
7. Return JSON with all margin data
8. Frontend displays in table + summary cards
9. User can filter by outlet, date, search
10. User can export to PDF
```

## Margin Calculation Logic

```php
// For Invoice Items
$profit = $detail->subtotal - ($detail->hpp * $detail->jumlah);
$marginPct = $detail->subtotal > 0 ? ($profit / $detail->subtotal) * 100 : 0;

// For POS Items
$hpp = $item->produk->calculateHppBarangDagang();
$profit = $item->subtotal - ($hpp * $item->kuantitas);
$marginPct = $item->subtotal > 0 ? ($profit / $item->subtotal) * 100 : 0;
```

## Color Coding

### Margin Badges

-   **Green (≥30%)**: Excellent margin
-   **Blue (15-29%)**: Good margin
-   **Orange (5-14%)**: Low margin
-   **Red (<5%)**: Very low margin

### Profit Display

-   **Green**: Positive profit
-   **Red**: Negative profit (loss)

### Payment Type

-   **Green**: Cash
-   **Blue**: QRIS
-   **Orange**: BON (credit)

## Testing Guide

### 1. Access Page

```
URL: http://your-domain/admin/penjualan/laporan-margin
Menu: Penjualan & Pemasaran → Laporan Margin
```

### 2. Test Filters

-   Select different outlets
-   Change date range
-   Search for specific products
-   Verify summary cards update correctly

### 3. Test Data Display

-   Verify Invoice items show correctly
-   Verify POS items show correctly
-   Check HPP calculations
-   Check profit calculations
-   Check margin % calculations
-   Verify color-coded badges

### 4. Test Export PDF

-   Click "Export PDF" button
-   Verify PDF opens in modal
-   Check all data is present
-   Verify formatting is correct
-   Check color-coded badges in PDF

### 5. Test Edge Cases

-   No data for selected filters
-   Products with zero HPP
-   Negative profit scenarios
-   Very high margin scenarios

## API Endpoints

### Get Margin Data

```
GET /admin/penjualan/laporan-margin/data
Parameters:
  - outlet_id (optional)
  - start_date (required)
  - end_date (required)

Response:
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
    }
  ]
}
```

### Export PDF

```
GET /admin/penjualan/laporan-margin/export-pdf
Parameters:
  - outlet_id (optional)
  - start_date (required)
  - end_date (required)

Response: PDF Stream
```

## Permissions

Required permission: `sales.invoice.view`

## Dependencies

-   Laravel 11
-   Alpine.js 3.x
-   Tailwind CSS
-   DomPDF (barryvdh/laravel-dompdf)

## Notes

-   HPP diambil dari field `hpp` di PenjualanDetail untuk Invoice
-   HPP dihitung dari `calculateHppBarangDagang()` untuk POS items
-   Margin dihitung berdasarkan subtotal (setelah qty)
-   Data diurutkan dari terbaru ke terlama
-   Filter search dilakukan di client-side untuk performa lebih baik

## Next Steps (Optional Enhancements)

1. ✨ Add Excel export
2. ✨ Add chart visualization (margin trend)
3. ✨ Add product grouping/aggregation
4. ✨ Add comparison with previous period
5. ✨ Add margin target indicators
6. ✨ Add drill-down to transaction details

---

**Status**: ✅ COMPLETE & READY TO USE
**Date**: December 1, 2024
**Version**: 1.0
