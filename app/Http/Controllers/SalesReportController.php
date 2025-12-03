<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PosSale;
use App\Models\Piutang;
use App\Models\Outlet;
use App\Models\JournalEntry;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    /**
     * Display sales report page
     */
    public function index()
    {
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.penjualan.laporan.index', compact('outlets'));
    }

    /**
     * Get sales data (Invoice + POS combined)
     * Optimized with eager loading and selective columns
     */
    public function getData(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search');

            $salesData = [];

            // Get Invoice data (exclude POS-generated penjualan)
            // POS creates penjualan records, so we need to exclude them
            $posGeneratedPenjualanIds = PosSale::pluck('id_penjualan')->filter()->toArray();
            
            // Optimized query dengan select specific columns
            $invoices = Penjualan::select([
                    'id_penjualan', 'id_member', 'id_outlet', 'id_user',
                    'total_item', 'total_harga', 'total_diskon', 'bayar', 'created_at'
                ])
                ->with([
                    'member:id_member,nama',
                    'outlet:id_outlet,nama_outlet',
                    'user:id,name'
                ])
                ->whereNotIn('id_penjualan', $posGeneratedPenjualanIds)
                ->when($outletId, fn($q) => $q->where('id_outlet', $outletId))
                ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                    $q->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($invoices as $invoice) {
                // Check if there's a SalesInvoice record (new system)
                $salesInvoice = \App\Models\SalesInvoice::where('id_penjualan', $invoice->id_penjualan)->first();
                
                // Determine payment status and amount paid
                $paymentStatus = 'Lunas';
                $totalBayar = $invoice->bayar;
                
                if ($salesInvoice) {
                    // Use SalesInvoice data (more accurate)
                    $totalBayar = $salesInvoice->total_dibayar;
                    
                    if ($salesInvoice->sisa_tagihan > 0) {
                        if ($salesInvoice->total_dibayar > 0) {
                            $paymentStatus = 'Dibayar Sebagian';
                        } else {
                            $paymentStatus = 'Belum Lunas';
                        }
                    } else {
                        $paymentStatus = 'Lunas';
                    }
                } else {
                    // Fallback to Piutang data (old system)
                    $piutang = Piutang::where('id_penjualan', $invoice->id_penjualan)->first();
                    
                    if ($piutang) {
                        $totalBayar = $piutang->jumlah_dibayar;
                        if ($piutang->sisa_piutang > 0) {
                            if ($piutang->jumlah_dibayar > 0) {
                                $paymentStatus = 'Dibayar Sebagian';
                            } else {
                                $paymentStatus = 'Belum Lunas';
                            }
                        }
                    }
                }
                
                $salesData[] = [
                    'id' => 'invoice_' . $invoice->id_penjualan,
                    'source' => 'invoice',
                    'source_id' => $invoice->id_penjualan,
                    'tanggal' => $invoice->created_at,
                    'outlet' => $invoice->outlet->nama_outlet ?? '-',
                    'outlet_id' => $invoice->id_outlet,
                    'customer' => $invoice->member->nama ?? 'Pelanggan Umum',
                    'total_item' => $invoice->total_item,
                    'total_harga' => $invoice->total_harga,
                    'diskon' => $invoice->total_diskon,
                    'total_bayar' => $totalBayar,
                    'payment_status' => $paymentStatus,
                    'payment_method' => null, // Invoice doesn't have payment method
                    'kasir' => $invoice->user->name ?? '-',
                    'invoice_number' => 'INV-' . str_pad($invoice->id_penjualan, 6, '0', STR_PAD_LEFT),
                ];
            }

            // Get POS data - optimized query
            $posSales = PosSale::select([
                    'id', 'no_transaksi', 'tanggal', 'id_outlet', 'id_member', 
                    'id_user', 'id_penjualan', 'subtotal', 'total_diskon', 'total',
                    'jenis_pembayaran', 'jumlah_bayar', 'is_bon'
                ])
                ->with([
                    'member:id_member,nama',
                    'outlet:id_outlet,nama_outlet',
                    'user:id,name',
                    'items:id,pos_sale_id,kuantitas'
                ])
                ->when($outletId, fn($q) => $q->where('id_outlet', $outletId))
                ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                    $q->whereDate('tanggal', '>=', $startDate)
                      ->whereDate('tanggal', '<=', $endDate);
                })
                ->orderBy('tanggal', 'desc')
                ->get();

            foreach ($posSales as $pos) {
                // Determine payment status and amount paid for POS
                $paymentStatus = 'Lunas';
                $totalBayar = $pos->jumlah_bayar;
                $paymentMethod = ucfirst($pos->jenis_pembayaran); // Cash/Transfer/Qris
                
                if ($pos->is_bon && $pos->id_penjualan) {
                    $piutang = Piutang::where('id_penjualan', $pos->id_penjualan)->first();
                    if ($piutang) {
                        $totalBayar = $piutang->jumlah_dibayar;
                        $paymentMethod = 'BON';
                        
                        // Check payment status
                        if ($piutang->sisa_piutang > 0) {
                            if ($piutang->jumlah_dibayar > 0) {
                                $paymentStatus = 'Dibayar Sebagian';
                            } else {
                                $paymentStatus = 'Belum Lunas';
                            }
                        } else {
                            $paymentStatus = 'Lunas';
                        }
                    }
                } else {
                    // Non-BON: Check if fully paid
                    if ($totalBayar >= $pos->total) {
                        $paymentStatus = 'Lunas';
                    } else if ($totalBayar > 0) {
                        $paymentStatus = 'Dibayar Sebagian';
                    } else {
                        $paymentStatus = 'Belum Lunas';
                    }
                }
                
                $salesData[] = [
                    'id' => 'pos_' . $pos->id,
                    'source' => 'pos',
                    'source_id' => $pos->id,
                    'tanggal' => $pos->tanggal,
                    'outlet' => $pos->outlet->nama_outlet ?? '-',
                    'outlet_id' => $pos->id_outlet,
                    'customer' => $pos->member->nama ?? 'Pelanggan Umum',
                    'total_item' => $pos->items->sum('kuantitas'),
                    'total_harga' => $pos->subtotal,
                    'diskon' => $pos->total_diskon,
                    'total_bayar' => $totalBayar,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'kasir' => $pos->user->name ?? '-',
                    'invoice_number' => $pos->no_transaksi,
                ];
            }

            // Sort by date descending
            usort($salesData, function($a, $b) {
                return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });

            // Apply search filter
            if ($search) {
                $salesData = array_filter($salesData, function($item) use ($search) {
                    return stripos($item['customer'], $search) !== false ||
                           stripos($item['invoice_number'], $search) !== false;
                });
                $salesData = array_values($salesData); // Re-index
            }

            return response()->json([
                'success' => true,
                'data' => $salesData
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading sales report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete sales transaction (cascade delete)
     */
    public function delete(Request $request, $source, $id)
    {
        try {
            DB::beginTransaction();

            if ($source === 'invoice') {
                $this->deleteInvoice($id);
            } elseif ($source === 'pos') {
                $this->deletePos($id);
            } else {
                throw new \Exception('Invalid source type');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sales: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Invoice and related data
     */
    private function deleteInvoice($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        // Restore stock for each product
        foreach ($penjualan->details as $detail) {
            if ($detail->id_produk) {
                $produk = Produk::find($detail->id_produk);
                if ($produk) {
                    // Add back the sold quantity to stock
                    // Use HPP from detail, or calculate current HPP if not available
                    $hpp = $detail->hpp ?? $produk->calculateHppBarangDagang();
                    $produk->addStock($hpp, $detail->jumlah);
                    Log::info("Stock restored for product {$produk->id_produk}: +{$detail->jumlah} @ HPP {$hpp}");
                }
            }
        }

        // Delete related journal entries using reference_type and reference_number
        JournalEntry::where('reference_type', 'invoice')
            ->where('reference_number', 'LIKE', "%{$id}%")
            ->delete();

        // Delete related piutang
        Piutang::where('id_penjualan', $id)->delete();

        // Delete penjualan details
        $penjualan->details()->delete();

        // Delete penjualan
        $penjualan->delete();

        Log::info("Invoice deleted: {$id}");
    }

    /**
     * Delete POS and related data
     */
    private function deletePos($id)
    {
        $posSale = PosSale::findOrFail($id);

        // Restore stock for each POS item
        foreach ($posSale->items as $item) {
            if ($item->id_produk && $item->tipe === 'produk') {
                $produk = Produk::find($item->id_produk);
                if ($produk) {
                    // Add back the sold quantity to stock
                    // Calculate current HPP for the product
                    $hpp = $produk->calculateHppBarangDagang();
                    $produk->addStock($hpp, $item->kuantitas);
                    Log::info("Stock restored for product {$produk->id_produk}: +{$item->kuantitas} @ HPP {$hpp}");
                }
            }
        }

        // Delete related journal entries using reference_type and reference_number
        JournalEntry::where('reference_type', 'pos')
            ->where('reference_number', 'LIKE', "%{$id}%")
            ->delete();

        // Delete related piutang (if BON)
        if ($posSale->id_penjualan) {
            Piutang::where('id_penjualan', $posSale->id_penjualan)->delete();
            
            // Delete related Penjualan record
            Penjualan::where('id_penjualan', $posSale->id_penjualan)->delete();
        }

        // Delete POS items
        $posSale->items()->delete();

        // Delete POS sale
        $posSale->delete();

        Log::info("POS sale deleted: {$id}");
    }

    /**
     * Export sales report to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Get data using same logic as getData
            $response = $this->getData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if (!$responseData['success']) {
                throw new \Exception('Failed to load data');
            }

            $salesData = $responseData['data'];

            // Calculate summary
            $summary = [
                'total_transaksi' => count($salesData),
                'total_invoice' => collect($salesData)->where('source', 'invoice')->count(),
                'total_pos' => collect($salesData)->where('source', 'pos')->count(),
                'total_penjualan' => collect($salesData)->sum('total_harga'),
                'total_diskon' => collect($salesData)->sum('diskon'),
                'total_dibayar' => collect($salesData)->sum('total_bayar'),
                'lunas' => collect($salesData)->where('payment_status', 'Lunas')->count(),
                'dibayar_sebagian' => collect($salesData)->where('payment_status', 'Dibayar Sebagian')->count(),
                'belum_lunas' => collect($salesData)->where('payment_status', 'Belum Lunas')->count(),
            ];

            // Get outlet name
            $outletName = 'Semua Outlet';
            if ($outletId) {
                $outlet = Outlet::find($outletId);
                $outletName = $outlet ? $outlet->nama_outlet : 'Semua Outlet';
            }

            $data = [
                'salesData' => $salesData,
                'summary' => $summary,
                'outletName' => $outletName,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'generatedAt' => now()->format('d/m/Y H:i'),
            ];

            $pdf = Pdf::loadView('admin.penjualan.laporan.pdf', $data)
                ->setPaper('a4', 'landscape');

            return $pdf->stream('Laporan-Penjualan-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error exporting sales report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
