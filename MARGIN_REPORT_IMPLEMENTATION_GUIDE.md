# 📋 Laporan Margin - Implementation Guide

## 🎯 Overview

Laporan Margin menampilkan analisis profit dari penjualan dengan menghitung:

-   HPP (Harga Pokok Penjualan)
-   Harga Jual
-   Profit (Harga Jual - HPP)
-   Margin % ((Profit / Harga Jual) × 100)
-   Metode Pembayaran (Cash/BON)

## 📝 Implementation Steps

### 1. Create Controller

**File:** `app/Http/Controllers/MarginReportController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Piutang;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MarginReportController extends Controller
{
    public function index()
    {
        $outlets = Outlet::where('is_active', true)->get();
        return view('admin.penjualan.margin.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $marginData = [];

        // Get Invoice details
        $invoiceDetails = PenjualanDetail::with(['produk', 'penjualan.outlet', 'penjualan.member'])
            ->whereHas('penjualan', function($q) use ($outletId, $startDate, $endDate) {
                if ($outletId) $q->where('id_outlet', $outletId);
                if ($startDate && $endDate) {
                    $q->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
                }
            })
            ->get();

        foreach ($invoiceDetails as $detail) {
            $profit = $detail->subtotal - ($detail->hpp * $detail->jumlah);
            $marginPct = $detail->subtotal > 0 ? ($profit / $detail->subtotal) * 100 : 0;

            $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
            $paymentType = $piutang && $piutang->sisa_piutang > 0 ? 'BON' : 'Cash';

            $marginData[] = [
                'id' => 'invoice_' . $detail->id_penjualan_detail,
                'source' => 'invoice',
                'tanggal' => $detail->penjualan->created_at,
                'outlet' => $detail->penjualan->outlet->nama_outlet ?? '-',
                'produk' => $detail->produk->nama_produk ?? '-',
                'qty' => $detail->jumlah,
                'hpp' => $detail->hpp,
                'harga_jual' => $detail->harga_jual,
                'subtotal' => $detail->subtotal,
                'profit' => $profit,
                'margin_pct' => $marginPct,
                'payment_type' => $paymentType,
            ];
        }

        // Get POS items
        $posItems = PosSaleItem::with(['produk', 'posSale.outlet'])
            ->where('tipe', 'produk')
            ->whereHas('posSale', function($q) use ($outletId, $startDate, $endDate) {
                if ($outletId) $q->where('id_outlet', $outletId);
                if ($startDate && $endDate) {
                    $q->whereDate('tanggal', '>=', $startDate)
                      ->whereDate('tanggal', '<=', $endDate);
                }
            })
            ->get();

        foreach ($posItems as $item) {
            $hpp = $item->produk ? $item->produk->calculateHppBarangDagang() : 0;
            $profit = $item->subtotal - ($hpp * $item->kuantitas);
            $marginPct = $item->subtotal > 0 ? ($profit / $item->subtotal) * 100 : 0;

            $paymentType = $item->posSale->is_bon ? 'BON' : ucfirst($item->posSale->jenis_pembayaran);

            $marginData[] = [
                'id' => 'pos_' . $item->id,
                'source' => 'pos',
                'tanggal' => $item->posSale->tanggal,
                'outlet' => $item->posSale->outlet->nama_outlet ?? '-',
                'produk' => $item->nama_produk,
                'qty' => $item->kuantitas,
                'hpp' => $hpp,
                'harga_jual' => $item->harga,
                'subtotal' => $item->subtotal,
                'profit' => $profit,
                'margin_pct' => $marginPct,
                'payment_type' => $paymentType,
            ];
        }

        // Sort by date
        usort($marginData, fn($a, $b) => strtotime($b['tanggal']) - strtotime($a['tanggal']));

        return response()->json([
            'success' => true,
            'data' => $marginData
        ]);
    }

    public function exportPdf(Request $request)
    {
        // Similar to SalesReportController
        $response = $this->getData($request);
        $data = json_decode($response->getContent(), true)['data'];

        $summary = [
            'total_items' => count($data),
            'total_hpp' => collect($data)->sum(fn($i) => $i['hpp'] * $i['qty']),
            'total_penjualan' => collect($data)->sum('subtotal'),
            'total_profit' => collect($data)->sum('profit'),
            'avg_margin' => collect($data)->avg('margin_pct'),
        ];

        $pdf = Pdf::loadView('admin.penjualan.margin.pdf', compact('data', 'summary'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Margin-' . date('Y-m-d') . '.pdf');
    }
}
```

### 2. Add Routes

**File:** `routes/web.php`

```php
Route::get('/laporan-margin', [MarginReportController::class, 'index'])
    ->name('margin.index');
Route::get('/laporan-margin/data', [MarginReportController::class, 'getData'])
    ->name('margin.data');
Route::get('/laporan-margin/export-pdf', [MarginReportController::class, 'exportPdf'])
    ->name('margin.export-pdf');
```

### 3. Update View

**File:** `resources/views/admin/penjualan/margin/index.blade.php`

Update Alpine.js untuk fetch data dari API:

```javascript
function marginRpt() {
    return {
        filter: {
            outlet: "all",
            start_date: new Date(
                new Date().getFullYear(),
                new Date().getMonth(),
                1
            )
                .toISOString()
                .split("T")[0],
            end_date: new Date().toISOString().split("T")[0],
        },
        marginData: [],
        isLoading: false,

        async init() {
            await this.loadData();
        },

        async loadData() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    outlet_id:
                        this.filter.outlet === "all" ? "" : this.filter.outlet,
                    start_date: this.filter.start_date,
                    end_date: this.filter.end_date,
                });

                const response = await fetch(
                    `{{ route('penjualan.margin.data') }}?${params}`
                );
                const data = await response.json();

                if (data.success) {
                    this.marginData = data.data;
                }
            } catch (error) {
                console.error("Error loading data:", error);
            } finally {
                this.isLoading = false;
            }
        },

        exportPdf() {
            const params = new URLSearchParams({
                outlet_id:
                    this.filter.outlet === "all" ? "" : this.filter.outlet,
                start_date: this.filter.start_date,
                end_date: this.filter.end_date,
            });

            window.open(
                `{{ route('penjualan.margin.export-pdf') }}?${params}`,
                "_blank"
            );
        },
    };
}
```

### 4. Create PDF Template

**File:** `resources/views/admin/penjualan/margin/pdf.blade.php`

Similar structure to sales report PDF with margin-specific columns.

## 📊 Data Structure

```php
[
    'id' => 'invoice_123',
    'source' => 'invoice', // or 'pos'
    'tanggal' => '2025-12-01',
    'outlet' => 'Outlet Name',
    'produk' => 'Product Name',
    'qty' => 10,
    'hpp' => 50000,
    'harga_jual' => 75000,
    'subtotal' => 750000,
    'profit' => 250000,
    'margin_pct' => 33.33,
    'payment_type' => 'Cash' // or 'BON'
]
```

## 🧪 Testing Checklist

-   [ ] Filter by outlet works
-   [ ] Date range filter works
-   [ ] Data shows correct HPP
-   [ ] Profit calculation correct
-   [ ] Margin % calculation correct
-   [ ] Payment type shows correctly
-   [ ] Export PDF works
-   [ ] No errors in console

## 📝 Notes

-   HPP for Invoice: dari `penjualan_detail.hpp`
-   HPP for POS: calculated using `calculateHppBarangDagang()`
-   Profit = Subtotal - (HPP × Qty)
-   Margin % = (Profit / Subtotal) × 100

---

**Status:** Implementation Guide Ready
**Next Steps:** Create controller, add routes, update view
