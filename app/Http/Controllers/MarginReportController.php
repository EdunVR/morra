<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Piutang;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class MarginReportController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        $outlets = Outlet::where('is_active', true)->get();
        return view('admin.penjualan.margin.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $marginData = [];
            $posGeneratedPenjualanIds = PosSale::pluck('id_penjualan')->filter()->toArray();

            // Get Invoice details (exclude POS-generated) - optimized
            $invoiceDetails = PenjualanDetail::select([
                    'id_penjualan_detail', 'id_penjualan', 'id_produk',
                    'harga_jual', 'jumlah', 'subtotal', 'hpp'
                ])
                ->with([
                    'produk:id_produk,nama_produk',
                    'penjualan' => function($query) {
                        $query->select('id_penjualan', 'id_outlet', 'created_at')
                              ->with('outlet:id_outlet,nama_outlet');
                    }
                ])
                ->whereHas('penjualan', function($q) use ($outletId, $startDate, $endDate, $posGeneratedPenjualanIds) {
                    $q->whereNotIn('id_penjualan', $posGeneratedPenjualanIds);
                    if ($outletId) $q->where('id_outlet', $outletId);
                    if ($startDate && $endDate) {
                        $q->whereDate('created_at', '>=', $startDate)
                          ->whereDate('created_at', '<=', $endDate);
                    }
                })
                ->get();

            foreach ($invoiceDetails as $detail) {
                $hpp = floatval($detail->hpp ?? 0);
                $jumlah = floatval($detail->jumlah ?? 0);
                $subtotal = floatval($detail->subtotal ?? 0);
                $hargaJual = floatval($detail->harga_jual ?? 0);
                
                $profit = $subtotal - ($hpp * $jumlah);
                $marginPct = $subtotal > 0 ? ($profit / $subtotal) * 100 : 0;
                
                $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
                $paymentType = $piutang && $piutang->sisa_piutang > 0 ? 'BON' : 'Cash';

                $marginData[] = [
                    'id' => 'invoice_' . $detail->id_penjualan_detail,
                    'source' => 'invoice',
                    'tanggal' => $detail->penjualan->created_at,
                    'outlet' => $detail->penjualan->outlet->nama_outlet ?? '-',
                    'produk' => $detail->produk->nama_produk ?? '-',
                    'qty' => $jumlah,
                    'hpp' => $hpp,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal,
                    'profit' => $profit,
                    'margin_pct' => round($marginPct, 2),
                    'payment_type' => $paymentType,
                ];
            }

            // Get POS items - optimized
            $posItems = PosSaleItem::select([
                    'id', 'pos_sale_id', 'id_produk', 'nama_produk',
                    'kuantitas', 'harga', 'subtotal', 'tipe'
                ])
                ->with([
                    'produk:id_produk,nama_produk',
                    'posSale' => function($query) {
                        $query->select('id', 'id_outlet', 'tanggal', 'is_bon', 'jenis_pembayaran')
                              ->with('outlet:id_outlet,nama_outlet');
                    }
                ])
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
                $hpp = $item->produk ? floatval($item->produk->calculateHppBarangDagang()) : 0;
                $kuantitas = floatval($item->kuantitas ?? 0);
                $subtotal = floatval($item->subtotal ?? 0);
                $harga = floatval($item->harga ?? 0);
                
                $profit = $subtotal - ($hpp * $kuantitas);
                $marginPct = $subtotal > 0 ? ($profit / $subtotal) * 100 : 0;
                
                $paymentType = $item->posSale->is_bon ? 'BON' : ucfirst($item->posSale->jenis_pembayaran);

                $marginData[] = [
                    'id' => 'pos_' . $item->id,
                    'source' => 'pos',
                    'tanggal' => $item->posSale->tanggal,
                    'outlet' => $item->posSale->outlet->nama_outlet ?? '-',
                    'produk' => $item->nama_produk,
                    'qty' => $kuantitas,
                    'hpp' => $hpp,
                    'harga_jual' => $harga,
                    'subtotal' => $subtotal,
                    'profit' => $profit,
                    'margin_pct' => round($marginPct, 2),
                    'payment_type' => $paymentType,
                ];
            }

            usort($marginData, fn($a, $b) => strtotime($b['tanggal']) - strtotime($a['tanggal']));

            return response()->json([
                'success' => true,
                'data' => $marginData
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading margin report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $response = $this->getData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if (!$responseData['success']) {
                throw new \Exception('Failed to load data');
            }

            $marginData = $responseData['data'];

            $summary = [
                'total_items' => count($marginData),
                'total_hpp' => collect($marginData)->sum(fn($i) => $i['hpp'] * $i['qty']),
                'total_penjualan' => collect($marginData)->sum('subtotal'),
                'total_profit' => collect($marginData)->sum('profit'),
                'avg_margin' => collect($marginData)->avg('margin_pct'),
            ];

            $outletId = $request->get('outlet_id');
            $outletName = 'Semua Outlet';
            if ($outletId) {
                $outlet = Outlet::find($outletId);
                $outletName = $outlet ? $outlet->nama_outlet : 'Semua Outlet';
            }

            $data = [
                'marginData' => $marginData,
                'summary' => $summary,
                'outletName' => $outletName,
                'startDate' => $request->get('start_date'),
                'endDate' => $request->get('end_date'),
                'generatedAt' => now()->format('d/m/Y H:i'),
            ];

            $pdf = Pdf::loadView('admin.penjualan.margin.pdf', $data)
                ->setPaper('a4', 'landscape');

            return $pdf->stream('Laporan-Margin-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error exporting margin report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
