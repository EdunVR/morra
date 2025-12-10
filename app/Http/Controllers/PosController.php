<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Produk;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Piutang;
use App\Models\SettingCOAPos;
use App\Services\JournalEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller
{
    use \App\Traits\HasOutletFilter;

    protected $journalService;

    public function __construct(JournalEntryService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Display POS interface
     */
    public function index(Request $request)
    {
        $selectedOutlet = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.penjualan.pos.index', compact('selectedOutlet', 'outlets'));
    }

    /**
     * Get products for POS
     * Optimized with caching and eager loading
     */
    public function getProducts(Request $request)
    {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        // Cache key untuk products per outlet
        $cacheKey = "pos_products_outlet_{$outletId}";
        
        // Cache selama 5 menit (data produk bisa berubah saat ada transaksi)
        $products = \App\Services\CacheService::remember($cacheKey, function() use ($outletId) {
            return Produk::select([
                    'id_produk', 'kode_produk', 'nama_produk', 'harga_jual', 
                    'id_outlet', 'id_kategori', 'id_satuan', 'is_active'
                ])
                ->with([
                    'satuan:id_satuan,nama_satuan',
                    'kategori:id_kategori,nama_kategori',
                    'hppProduk' => function($query) {
                        $query->select('id_produk', 'hpp', 'stok')
                              ->where('stok', '>', 0);
                    },
                    'primaryImage:id_image,id_produk,path',
                    'images' => function($query) {
                        $query->select(['id_image', 'id_produk', 'path'])->limit(1);
                    }
                ])
                ->where('id_outlet', $outletId)
                ->where('is_active', true)
                ->get()
                ->filter(function($produk) {
                    // Filter hanya produk dengan stok > 0
                    return $produk->stok > 0;
                })
                ->map(function($produk) {
                    // Get primary image or first image
                    $imageUrl = null;
                    if ($produk->primaryImage) {
                        $imageUrl = asset('storage/' . $produk->primaryImage->path);
                    } elseif ($produk->images->count() > 0) {
                        $imageUrl = asset('storage/' . $produk->images->first()->path);
                    }
                    
                    return [
                        'id_produk' => $produk->id_produk,
                        'sku' => $produk->kode_produk,
                        'name' => $produk->nama_produk,
                        'category' => $produk->kategori ? $produk->kategori->nama_kategori : 'Barang',
                        'price' => $produk->harga_jual,
                        'stock' => $produk->stok,
                        'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'pcs',
                        'image' => $imageUrl,
                    ];
                })
                ->values()
                ->toArray();
        }, \App\Services\CacheService::SHORT_TTL);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get customers for POS with piutang info and tipe
     * Optimized with caching
     */
    public function getCustomers()
    {
        // Cache customers selama 10 menit
        $cacheKey = "pos_customers_all";
        
        $customers = \App\Services\CacheService::remember($cacheKey, function() {
            return Member::select('id_member', 'nama', 'telepon', 'id_tipe')
                ->with('tipe:id_tipe,nama_tipe')
                ->withTotalPiutang()
                ->orderBy('nama')
                ->get()
                ->map(function($customer) {
                    return [
                        'id' => $customer->id_member,
                        'name' => $customer->nama,
                        'telepon' => $customer->telepon,
                        'piutang' => $customer->total_piutang ?? 0,
                        'id_tipe' => $customer->id_tipe,
                        'tipe_name' => $customer->tipe ? $customer->tipe->nama_tipe : null
                    ];
                })
                ->toArray();
        }, 600); // 10 minutes

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Get product prices for customer type
     */
    public function getCustomerTypePrices(Request $request)
    {
        $idTipe = $request->get('id_tipe');
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        if (!$idTipe) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            // Get produk_tipe with diskon and harga_jual
            $produkTipe = \App\Models\ProdukTipe::where('id_tipe', $idTipe)
                ->with('produk:id_produk,kode_produk,harga_jual')
                ->get()
                ->map(function($pt) {
                    $hargaFinal = $pt->harga_jual; // Harga jual khusus
                    
                    // Jika tidak ada harga jual khusus, hitung dari diskon
                    if (!$hargaFinal || $hargaFinal == 0) {
                        $hargaNormal = $pt->produk->harga_jual;
                        $diskon = $pt->diskon ?? 0;
                        $hargaFinal = $hargaNormal * (1 - $diskon / 100);
                    }
                    
                    return [
                        'id_produk' => $pt->id_produk,
                        'sku' => $pt->produk->kode_produk,
                        'harga_normal' => $pt->produk->harga_jual,
                        'diskon' => $pt->diskon ?? 0,
                        'harga_khusus' => $pt->harga_jual,
                        'harga_final' => $hargaFinal
                    ];
                })
                ->keyBy('id_produk')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $produkTipe
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting customer type prices: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil harga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store POS transaction
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'nullable|exists:produk,id_produk',
            'items.*.nama_produk' => 'required|string',
            'items.*.sku' => 'nullable|string',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe' => 'required|in:produk,jasa',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'jenis_pembayaran' => 'required|in:cash,transfer,qris',
            'is_bon' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $saleData = null;
            
            DB::transaction(function () use ($request, &$saleData) {
                $outletId = $request->id_outlet;
                $isBon = $request->is_bon;
                
                // Generate nomor transaksi
                $noTransaksi = PosSale::generateTransactionNumber($outletId);
                
                // Hitung total
                $subtotal = $request->subtotal;
                $diskonNominal = $request->diskon_nominal ?? 0;
                $diskonPersen = $request->diskon_persen ?? 0;
                $totalDiskon = $diskonNominal;
                
                if ($diskonPersen > 0) {
                    $totalDiskon += ($subtotal * $diskonPersen / 100);
                }
                
                $ppn = $request->ppn ?? 0;
                $total = $subtotal - $totalDiskon + $ppn;
                
                // Create POS Sale
                $posSale = PosSale::create([
                    'no_transaksi' => $noTransaksi,
                    'tanggal' => $request->tanggal,
                    'id_outlet' => $outletId,
                    'id_member' => $request->id_member ?? null,
                    'id_user' => auth()->id(),
                    'subtotal' => $subtotal,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'total_diskon' => $totalDiskon,
                    'ppn' => $ppn,
                    'total' => $total,
                    'jenis_pembayaran' => $request->jenis_pembayaran,
                    'jumlah_bayar' => $isBon ? 0 : $request->jumlah_bayar,
                    'kembalian' => $isBon ? 0 : ($request->jumlah_bayar - $total),
                    'status' => $isBon ? 'menunggu' : 'lunas',
                    'catatan' => $request->catatan,
                    'is_bon' => $isBon,
                ]);

                // Create Penjualan record (untuk kompatibilitas dengan sistem lama)
                $totalItem = 0;
                foreach ($request->items as $item) {
                    if ($item['tipe'] === 'produk') {
                        $totalItem += $item['kuantitas'];
                    }
                }

                $penjualan = Penjualan::create([
                    'id_member' => $request->id_member ?? null,
                    'id_outlet' => $outletId,
                    'total_item' => $totalItem,
                    'total_harga' => $total,
                    'total_diskon' => $totalDiskon,
                    'diskon' => $diskonPersen,
                    'bayar' => $isBon ? 0 : $total,
                    'diterima' => $isBon ? 0 : $request->jumlah_bayar,
                    'id_user' => auth()->id(),
                    'created_at' => $request->tanggal,
                    'updated_at' => $request->tanggal,
                ]);

                // Update POS Sale dengan id_penjualan
                $posSale->update(['id_penjualan' => $penjualan->id_penjualan]);

                // Create POS Sale Items dan kurangi stok
                foreach ($request->items as $item) {
                    PosSaleItem::create([
                        'pos_sale_id' => $posSale->id,
                        'id_produk' => $item['id_produk'] ?? null,
                        'nama_produk' => $item['nama_produk'],
                        'sku' => $item['sku'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? 'pcs',
                        'harga' => $item['harga'],
                        'subtotal' => $item['subtotal'],
                        'tipe' => $item['tipe'],
                    ]);

                    // Kurangi stok dan buat penjualan detail jika produk
                    if ($item['tipe'] === 'produk' && !empty($item['id_produk'])) {
                        $produk = Produk::with('hppProduk')->find($item['id_produk']);
                        if ($produk) {
                            // Kurangi stok menggunakan FIFO
                            try {
                                $produk->reduceStock($item['kuantitas']);
                            } catch (\Exception $e) {
                                throw new \Exception("Gagal mengurangi stok: " . $e->getMessage());
                            }
                            
                            // Hitung HPP
                            $hpp = $produk->calculateHppBarangDagang();
                            
                            // Create penjualan detail
                            PenjualanDetail::create([
                                'id_penjualan' => $penjualan->id_penjualan,
                                'id_produk' => $item['id_produk'],
                                'harga_jual' => $item['harga'],
                                'jumlah' => $item['kuantitas'],
                                'diskon' => $diskonPersen,
                                'subtotal' => $item['subtotal'],
                                'hpp' => $hpp,
                            ]);
                        }
                    }
                }

                // Create Piutang jika bon
                if ($isBon) {
                    $dueDate = now()->addDays(30);
                    
                    Piutang::create([
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_member' => $request->id_member ?? null,
                        'id_outlet' => $outletId,
                        'tanggal_tempo' => $request->tanggal,
                        'tanggal_jatuh_tempo' => $dueDate,
                        'piutang' => $total,
                        'jumlah_piutang' => $total,
                        'jumlah_dibayar' => 0,
                        'sisa_piutang' => $total,
                        'status' => 'belum_lunas',
                        'nama' => $request->id_member ? Member::find($request->id_member)->nama : 'Pelanggan Umum',
                    ]);
                }

                // Create journal entry
                $this->createPosJournal($posSale);

                // Store data for return
                $saleData = [
                    'id' => $posSale->id,
                    'no_transaksi' => $noTransaksi,
                    'total' => $total,
                    'kembalian' => $isBon ? 0 : ($request->jumlah_bayar - $total)
                ];

                Log::info('POS transaction created successfully', [
                    'pos_sale_id' => $posSale->id,
                    'no_transaksi' => $noTransaksi,
                    'total' => $total
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi POS berhasil disimpan',
                'data' => $saleData
            ]);

        } catch (\Exception $e) {
            Log::error('POS transaction error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create journal entry for POS transaction
     */
    private function createPosJournal($posSale)
    {
        try {
            $setting = SettingCOAPos::getByOutlet($posSale->id_outlet);
            
            if (!$setting || !$setting->accounting_book_id) {
                Log::info('Setting COA POS belum diatur untuk outlet ' . $posSale->id_outlet);
                return null;
            }

            $customerName = $posSale->member ? $posSale->member->nama : 'Pelanggan Umum';
            $description = "POS {$posSale->no_transaksi} - {$customerName}";
            $entries = [];

            // Hitung total HPP
            $totalHpp = 0;
            foreach ($posSale->items as $item) {
                if ($item->tipe === 'produk' && $item->id_produk) {
                    $produk = Produk::find($item->id_produk);
                    if ($produk) {
                        $hpp = $produk->calculateHppBarangDagang();
                        $totalHpp += $hpp * $item->kuantitas;
                    }
                }
            }

            // Hitung pendapatan bersih (tanpa PPN)
            $pendapatanBersih = $posSale->subtotal - $posSale->total_diskon;
            $ppnAmount = $posSale->ppn;

            if ($posSale->is_bon) {
                // Bon (Piutang): Piutang Usaha (D) vs Pendapatan Penjualan (K) + PPN (K)
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $posSale->id_outlet),
                    'debit' => $posSale->total,
                    'credit' => 0,
                    'memo' => 'Piutang usaha dari ' . $customerName
                ];
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $posSale->id_outlet),
                    'debit' => 0,
                    'credit' => $pendapatanBersih,
                    'memo' => 'Pendapatan penjualan POS'
                ];
                
                // Pisahkan PPN jika ada dan akun PPN sudah diset
                if ($ppnAmount > 0 && !empty($setting->akun_ppn)) {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_ppn, $posSale->id_outlet),
                        'debit' => 0,
                        'credit' => $ppnAmount,
                        'memo' => 'PPN 10% dari penjualan'
                    ];
                }
            } else {
                // Cash/Transfer: Kas/Bank (D) vs Pendapatan Penjualan (K) + PPN (K)
                $akunKasBank = $posSale->jenis_pembayaran === 'cash' 
                    ? $this->getAccountIdByCode($setting->akun_kas, $posSale->id_outlet)
                    : $this->getAccountIdByCode($setting->akun_bank, $posSale->id_outlet);

                $entries[] = [
                    'account_id' => $akunKasBank,
                    'debit' => $posSale->total,
                    'credit' => 0,
                    'memo' => 'Penerimaan kas dari penjualan POS'
                ];
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $posSale->id_outlet),
                    'debit' => 0,
                    'credit' => $pendapatanBersih,
                    'memo' => 'Pendapatan penjualan POS'
                ];
                
                // Pisahkan PPN jika ada dan akun PPN sudah diset
                if ($ppnAmount > 0 && !empty($setting->akun_ppn)) {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_ppn, $posSale->id_outlet),
                        'debit' => 0,
                        'credit' => $ppnAmount,
                        'memo' => 'PPN 10% dari penjualan'
                    ];
                }
            }

            // Tambahkan jurnal HPP dan persediaan jika ada produk
            if ($totalHpp > 0 && !empty($setting->akun_hpp) && !empty($setting->akun_persediaan)) {
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_hpp, $posSale->id_outlet),
                    'debit' => $totalHpp,
                    'credit' => 0,
                    'memo' => 'HPP penjualan POS'
                ];
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_persediaan, $posSale->id_outlet),
                    'debit' => 0,
                    'credit' => $totalHpp,
                    'memo' => 'Pengurangan persediaan'
                ];
            }

            return $this->journalService->createAutomaticJournal(
                'pos',
                $posSale->id,
                $posSale->tanggal,
                $description,
                $entries,
                $setting->accounting_book_id,
                $posSale->id_outlet
            );

        } catch (\Exception $e) {
            Log::error('Gagal membuat jurnal POS: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get account ID by code
     */
    private function getAccountIdByCode(string $code, int $outletId): int
    {
        $account = \App\Models\ChartOfAccount::where('code', $code)
            ->where('outlet_id', $outletId)
            ->first();
        
        if (!$account) {
            throw new \Exception("Akun dengan kode {$code} tidak ditemukan untuk outlet {$outletId}");
        }
        
        return $account->id;
    }

    /**
     * Get transaction history
     */
    public function history(Request $request)
    {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.penjualan.pos.history', compact('outletId', 'outlets'));
    }

    /**
     * Get data for DataTable
     * Optimized with eager loading
     */
    public function historyData(Request $request)
    {
        $outletId = $request->get('outlet_id', 'all');
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');

        // Optimized eager loading - hanya load kolom yang diperlukan
        $query = PosSale::select([
                'id', 'no_transaksi', 'tanggal', 'id_outlet', 'id_member', 
                'id_user', 'total', 'status', 'jenis_pembayaran', 'jumlah_bayar'
            ])
            ->with([
                'outlet:id_outlet,nama_outlet',
                'member:id_member,nama',
                'user:id,name',
                'items:id,pos_sale_id' // Hanya untuk count
            ])
            ->byOutlet($outletId)
            ->status($status)
            ->dateRange($startDate, $endDate)
            ->when($search, function($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%");
            })
            ->orderBy('tanggal', 'desc');

        // If AJAX request for modal, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            $data = $query->limit(100)->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        // Otherwise return DataTables
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y H:i');
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->outlet ? $row->outlet->nama_outlet : '-';
            })
            ->addColumn('customer_name', function ($row) {
                return $row->member ? $row->member->nama : 'Pelanggan Umum';
            })
            ->addColumn('total_formatted', function ($row) {
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = $row->status === 'lunas' ? 'success' : 'warning';
                $statusText = $row->status === 'lunas' ? 'Lunas' : 'Menunggu';
                return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-' . $badgeClass . '-100 text-' . $badgeClass . '-800">' . $statusText . '</span>';
            })
            ->addColumn('payment_type', function ($row) {
                $types = [
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS'
                ];
                return $types[$row->jenis_pembayaran] ?? $row->jenis_pembayaran;
            })
            ->addColumn('items_count', function ($row) {
                return $row->items->count() . ' item';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                $actions .= '<a href="' . route('pos.print', $row->id) . '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                    <i class="bx bx-printer text-xs"></i> Print
                </a>';
                $actions .= '<button onclick="viewDetail(' . $row->id . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class="bx bx-show text-xs"></i> Detail
                </button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show transaction detail
     */
    public function show($id)
    {
        try {
            $posSale = PosSale::with(['outlet', 'member', 'user', 'items.produk'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $posSale
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Print receipt
     */
    public function print($id, Request $request)
    {
        $posSale = PosSale::with(['outlet', 'member', 'user', 'items'])
            ->findOrFail($id);
        
        // Get piutang if bon
        $piutang = null;
        if ($posSale->is_bon && $posSale->id_penjualan) {
            $piutang = Piutang::where('id_penjualan', $posSale->id_penjualan)->first();
        }
        
        // Determine nota type (default: besar)
        $type = $request->get('type', 'besar');
        
        // Generate PDF
        $viewName = $type === 'kecil' ? 'admin.penjualan.pos.nota_kecil' : 'admin.penjualan.pos.nota_besar';
        
        $pdf = Pdf::loadView($viewName, compact('posSale', 'piutang'))
            ->setPaper('a4', 'portrait');
        
        // Return PDF stream (untuk ditampilkan di modal)
        return $pdf->stream('Nota-POS-' . $posSale->invoice_number . '.pdf');
    }

    /**
     * Get/Update COA Settings
     */
    public function coaSettings(Request $request)
    {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        if ($request->isMethod('get')) {
            $setting = SettingCOAPos::with('accountingBook')->byOutlet($outletId)->first();
            
            // If AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $setting
                ]);
            }
            
            // Otherwise return view
            $books = \App\Models\AccountingBook::where('outlet_id', $outletId)->get();
            
            // Get all outlets for dropdown
            $outlets = Outlet::where('is_active', true)->get();
            
            // Get leaf accounts only (accounts without children) grouped by type
            $allAccounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
                ->orderBy('code')
                ->get();
            
            // Filter only leaf accounts (no children)
            $leafAccounts = $allAccounts->filter(function($account) use ($allAccounts) {
                return !$allAccounts->contains(function($child) use ($account) {
                    return $child->parent_code === $account->code;
                });
            });
            
            // Group by account type
            $accountsByType = [
                'asset' => $leafAccounts->filter(fn($a) => $a->type === 'asset')->values(),
                'liability' => $leafAccounts->filter(fn($a) => $a->type === 'liability')->values(),
                'equity' => $leafAccounts->filter(fn($a) => $a->type === 'equity')->values(),
                'revenue' => $leafAccounts->filter(fn($a) => $a->type === 'revenue')->values(),
                'expense' => $leafAccounts->filter(fn($a) => $a->type === 'expense')->values(),
            ];
            
            return view('admin.penjualan.pos.coa-settings', compact('setting', 'books', 'accountsByType', 'outletId', 'outlets'));
        }
        
        // POST - Update settings
        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'akun_kas' => 'required|string',
            'akun_bank' => 'required|string',
            'akun_piutang_usaha' => 'required|string',
            'akun_pendapatan_penjualan' => 'required|string',
            'akun_hpp' => 'nullable|string',
            'akun_persediaan' => 'nullable|string',
            'akun_ppn' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            SettingCOAPos::updateOrCreateForOutlet($outletId, $request->only([
                'accounting_book_id',
                'akun_kas',
                'akun_bank',
                'akun_piutang_usaha',
                'akun_pendapatan_penjualan',
                'akun_hpp',
                'akun_persediaan',
                'akun_ppn',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Setting COA POS berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
            ], 500);
        }
    }
}
