<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\BahanDetail;
use App\Models\HppProduk;
use App\Models\Outlet;
use App\Traits\HasOutletFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display production index page
     */
    public function index(Request $request)
    {
        $selectedOutlet = $this->getSelectedOutlet($request);
        $outlets = $this->getAccessibleOutlets();
        
        return view('admin.produksi.produksi.index', compact('selectedOutlet', 'outlets'));
    }

    /**
     * Get production data for DataTables
     */
    public function getData(Request $request)
    {
        $outletId = $this->getSelectedOutlet($request);
        
        $query = Production::with(['product', 'outlet'])
            ->where('outlet_id', $outletId);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by production line
        if ($request->filled('production_line') && $request->production_line !== 'all') {
            $query->where('production_line', $request->production_line);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        return DataTables::of($query)
            ->addColumn('product_name', function ($production) {
                return $production->product->nama_produk ?? '-';
            })
            ->addColumn('progress', function ($production) {
                return $production->progress_percentage;
            })
            ->addColumn('status_badge', function ($production) {
                $color = $production->status_color;
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-' . $color . '-100 text-' . $color . '-700">' 
                    . $production->status_label . 
                    '</span>';
            })
            ->addColumn('actions', function ($production) {
                return view('admin.produksi.produksi.partials.actions', compact('production'))->render();
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Store new production
     */
    public function store(Request $request)
    {
        // Log request data for debugging
        Log::info('Production Store Request:', $request->all());
        
        $outletId = $this->getSelectedOutlet($request);
        Log::info('Selected Outlet ID:', ['outlet_id' => $outletId]);
        
        $validator = Validator::make($request->all(), [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('produk', 'id_produk')->where(function ($query) use ($outletId) {
                    $query->where('id_outlet', $outletId);
                })
            ],
            'production_line' => 'required|string|max:50',
            'target_quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'nullable|in:normal,high,urgent',
            'max_reject_rate' => 'nullable|numeric|min:0|max:100',
            'min_efficiency' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'materials' => 'nullable|array',
            'materials.*.material_id' => 'required_with:materials|integer',
            'materials.*.material_type' => 'required_with:materials|in:bahan,produk',
            'materials.*.quantity' => 'required_with:materials|numeric|min:0',
            'materials.*.unit' => 'required_with:materials|string',
        ]);

        if ($validator->fails()) {
            Log::error('Production Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $outletId = $this->getSelectedOutlet($request);
            
            // Generate production code
            $productionCode = Production::generateCode($outletId);

            // Create production
            $production = Production::create([
                'production_code' => $productionCode,
                'outlet_id' => $outletId,
                'product_id' => $request->product_id,
                'production_line' => $request->production_line,
                'target_quantity' => $request->target_quantity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'draft',
                'priority' => $request->priority ?? 'normal',
                'max_reject_rate' => $request->max_reject_rate ?? 3.00,
                'min_efficiency' => $request->min_efficiency ?? 85.00,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Add materials if provided
            if ($request->filled('materials')) {
                foreach ($request->materials as $material) {
                    if (!empty($material['material_id']) && !empty($material['quantity'])) {
                        ProductionMaterial::create([
                            'production_id' => $production->id,
                            'material_id' => $material['material_id'],
                            'material_type' => $material['material_type'] ?? 'bahan',
                            'quantity_required' => $material['quantity'],
                            'unit' => $material['unit'] ?? 'pcs',
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dibuat',
                'data' => $production
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show production detail
     */
    public function show($id)
    {
        try {
            $production = Production::with(['product.hppProduk', 'materials.material', 'realizations', 'creator', 'approver'])
                ->findOrFail($id);

            // Calculate product stock and HPP
            $productStock = $production->product->hppProduk->sum('stok');
            $productHpp = $production->product->calculateHpp();

            $data = $production->toArray();
            $data['product_stock'] = $productStock;
            $data['product_hpp'] = $productHpp;

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update production
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:produk,id_produk',
            'production_line' => 'required|string|max:50',
            'target_quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'nullable|in:normal,high,urgent',
            'max_reject_rate' => 'nullable|numeric|min:0|max:100',
            'min_efficiency' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $production = Production::findOrFail($id);

            // Only allow update if status is draft
            if ($production->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft yang dapat diubah'
                ], 403);
            }

            $production->update([
                'product_id' => $request->product_id,
                'production_line' => $request->production_line,
                'target_quantity' => $request->target_quantity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority ?? 'normal',
                'max_reject_rate' => $request->max_reject_rate ?? 3.00,
                'min_efficiency' => $request->min_efficiency ?? 85.00,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil diperbarui',
                'data' => $production
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete production
     */
    public function destroy($id)
    {
        try {
            $production = Production::findOrFail($id);

            // Only allow delete if status is draft or cancelled
            if (!in_array($production->status, ['draft', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft atau dibatalkan yang dapat dihapus'
                ], 403);
            }

            $production->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve production
     */
    public function approve($id)
    {
        try {
            $production = Production::findOrFail($id);

            if ($production->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft yang dapat disetujui'
                ], 403);
            }

            $production->approve(auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start production
     */
    public function start($id)
    {
        try {
            $production = Production::findOrFail($id);

            if ($production->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi yang sudah disetujui yang dapat dimulai'
                ], 403);
            }

            $production->start();

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dimulai'
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel production
     */
    public function cancel($id)
    {
        try {
            $production = Production::findOrFail($id);

            if (in_array($production->status, ['completed', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produksi yang sudah selesai atau dibatalkan tidak dapat dibatalkan lagi'
                ], 403);
            }

            $production->cancel();

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add realization with stock management
     */
    public function addRealization(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity_produced' => 'required|integer|min:1',
            'quantity_rejected' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $production = Production::with('materials')->findOrFail($id);

            if ($production->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi yang sedang berjalan yang dapat ditambahkan realisasi'
                ], 403);
            }

            // Validate stock availability for all materials
            // quantity_required is for target_quantity, calculate proportionally for actual production
            foreach ($production->materials as $material) {
                $bahan = Bahan::with('hargaBahan')->find($material->material_id);
                if (!$bahan) continue;
                
                $totalStok = $bahan->hargaBahan->sum('stok');
                
                // Calculate required quantity based on production ratio
                // Example: target=100, material=5kg, realization=50 → need 2.5kg
                $ratio = $request->quantity_produced / $production->target_quantity;
                $requiredQty = $material->quantity_required * $ratio;
                
                if ($totalStok < $requiredQty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$bahan->nama_bahan} tidak mencukupi. Tersedia: {$totalStok} {$material->unit}, Dibutuhkan: {$requiredQty} {$material->unit} untuk {$request->quantity_produced} unit produk. Silahkan lakukan pembelian bahan di menu PO."
                    ], 400);
                }
            }

            // Add realization record
            $realization = $production->addRealization(
                $request->quantity_produced,
                $request->quantity_rejected ?? 0,
                $request->notes
            );

            // Reduce material stock & calculate total cost using FIFO
            $totalMaterialCost = 0;
            foreach ($production->materials as $material) {
                $bahan = Bahan::with('hargaBahan')->find($material->material_id);
                if (!$bahan) continue;
                
                // Calculate required quantity based on production ratio
                // quantity_required is for target_quantity, so we need to calculate proportionally
                $ratio = $request->quantity_produced / $production->target_quantity;
                $requiredQty = $material->quantity_required * $ratio;
                
                // Reduce stock using FIFO method
                $sisaKurang = $requiredQty;
                $materialCost = 0;
                
                $bahanDetails = $bahan->hargaBahan()
                    ->where('stok', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($bahanDetails as $detail) {
                    if ($sisaKurang <= 0) break;

                    if ($detail->stok >= $sisaKurang) {
                        $materialCost += $detail->harga_beli * $sisaKurang;
                        $detail->stok -= $sisaKurang;
                        $detail->save();
                        $sisaKurang = 0;
                    } else {
                        $materialCost += $detail->harga_beli * $detail->stok;
                        $sisaKurang -= $detail->stok;
                        $detail->stok = 0;
                        $detail->save();
                    }
                }
                
                $totalMaterialCost += $materialCost;
            }

            // Calculate HPP per unit
            $hppPerUnit = $request->quantity_produced > 0 
                ? $totalMaterialCost / $request->quantity_produced 
                : 0;

            // Add product stock to hpp_produk table
            HppProduk::create([
                'id_produk' => $production->product_id,
                'hpp' => $hppPerUnit,
                'stok' => $request->quantity_produced,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil ditambahkan. Stok bahan berkurang dan stok produk bertambah.',
                'data' => [
                    'realization' => $realization,
                    'hpp_per_unit' => number_format($hppPerUnit, 2),
                    'total_cost' => number_format($totalMaterialCost, 2),
                    'quantity_produced' => $request->quantity_produced,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding realization: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products for autocomplete search
     */
    public function getProducts(Request $request)
    {
        $outletId = $this->getSelectedOutlet($request);
        $search = $request->get('search', '');
        
        $query = Produk::select('id_produk', 'kode_produk', 'nama_produk', 'harga_jual')
            ->where('id_outlet', $outletId)
            ->where('is_active', true);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produk', 'like', "%{$search}%")
                  ->orWhere('nama_produk', 'like', "%{$search}%");
            });
        }
        
        $products = $query->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id_produk,
                    'code' => $product->kode_produk,
                    'name' => $product->nama_produk,
                    'price' => $product->harga_jual,
                    'stock' => $product->stok ?? 0,
                    'hpp' => $product->calculateHpp(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get materials (only bahan) with stock info
     */
    public function getMaterials(Request $request)
    {
        $outletId = $this->getSelectedOutlet($request);
        
        // Only get bahan (not produk) with stock information
        $bahan = Bahan::select('id_bahan', 'nama_bahan', 'id_satuan')
            ->with(['satuan:id_satuan,nama_satuan', 'hargaBahan'])
            ->where('id_outlet', $outletId)
            ->where('is_active', true)
            ->get()
            ->map(function($item) {
                $totalStok = $item->hargaBahan->sum('stok');
                return [
                    'id' => $item->id_bahan,
                    'name' => $item->nama_bahan,
                    'type' => 'bahan',
                    'unit' => $item->satuan->nama_satuan ?? 'kg',
                    'stock' => $totalStok,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $bahan
        ]);
    }

    /**
     * Get statistics
     */
    public function getStatistics(Request $request)
    {
        $outletId = $this->getSelectedOutlet($request);
        
        $stats = [
            'active' => Production::where('outlet_id', $outletId)->active()->count(),
            'draft' => Production::where('outlet_id', $outletId)->byStatus('draft')->count(),
            'in_progress' => Production::where('outlet_id', $outletId)->byStatus('in_progress')->count(),
            'completed' => Production::where('outlet_id', $outletId)->byStatus('completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
