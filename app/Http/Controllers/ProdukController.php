<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\RabTemplate;
use App\Models\HppProduk;
use App\Models\Outlet;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdukExport;
use App\Imports\ProdukImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HasOutletFilter;

class ProdukController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('permission:inventaris.produk.view')->only(['index', 'data', 'show', 'getCategories', 'getUnits', 'getIdMappings', 'getOutlets']);
        $this->middleware('permission:inventaris.produk.create')->only(['store', 'getNewSku']);
        $this->middleware('permission:inventaris.produk.edit')->only(['update', 'edit']);
        $this->middleware('permission:inventaris.produk.delete')->only(['destroy']);
        $this->middleware('permission:inventaris.produk.export')->only(['exportPdf', 'exportExcel', 'downloadTemplate']);
        $this->middleware('permission:inventaris.produk.import')->only(['importExcel']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk frontend baru
        if (request()->expectsJson() || str_contains(request()->path(), 'inventaris/produk')) {
            return view('admin.inventaris.produk.index');
        }

        // Untuk frontend lama (tetap dipertahankan)
        $satuan = Satuan::all()->pluck('nama_satuan', 'id_satuan');
        
        // Get user's accessible outlets using trait
        $outlets = $this->getUserOutlets();
        $outletIds = $this->getUserOutletIds();
        
        $kategori = Kategori::when(!empty($outletIds), function ($query) use ($outletIds) {
            return $query->whereIn('id_outlet', $outletIds);
        })->pluck('nama_kategori', 'id_kategori');

        $rabTemplates = RabTemplate::all()->pluck('nama_template', 'id_rab');
        $productTypes = [
            'barang_dagang' => 'Barang Dagang',
            'jasa' => 'Jasa',
            'paket_travel' => 'Paket Travel',
            'produk_kustom' => 'Produk Kustom'
        ];

        return view('produk.index', compact(
            'outlets', 
            'kategori', 
            'satuan',
            'rabTemplates',
            'productTypes'
        ));
    }

    // ========== FUNGSI UNTUK FRONTEND BARU ==========

    /**
     * Data untuk frontend baru (AJAX)
     */
    public function data(Request $request)
    {
        Log::info('Fetching Produk Data with filters', $request->all());

        $query = Produk::with(['kategori', 'satuan', 'outlet', 'primaryImage'])
            ->withSum('hppProduk', 'stok');

        // Apply outlet filter using trait
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_produk', 'like', "%{$request->search}%")
                  ->orWhere('kode_produk', 'like', "%{$request->search}%")
                  ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }

        // Filter outlet
        if ($request->has('outlet_filter') && $request->outlet_filter !== 'ALL') {
            $query->whereHas('outlet', function($q) use ($request) {
                $q->where('nama_outlet', $request->outlet_filter);
            });
        }

        // Filter tipe
        if ($request->has('type_filter') && $request->type_filter !== 'ALL') {
            $query->where('tipe_produk', $request->type_filter);
        }

        // Filter stok
        if ($request->has('stock_filter') && $request->stock_filter !== 'ALL') {
            if ($request->stock_filter === 'READY') {
                $query->having('hpp_produk_sum_stok', '>', 0);
            } elseif ($request->stock_filter === 'EMPTY') {
                $query->having('hpp_produk_sum_stok', '<=', 0);
            }
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'nama_produk';
        $sortDirection = $request->sort_dir ?? 'asc';
        
        $columnMapping = [
            'name' => 'nama_produk',
            'sku' => 'kode_produk',
            'stock' => 'hpp_produk_sum_stok',
            'price' => 'harga_jual'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        
        if ($sortColumn === 'hpp_produk_sum_stok') {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $produks = $query->get();

        return datatables()
            ->of($produks)
            ->addIndexColumn()
            ->addColumn('outlet', function ($produk) {
                return $produk->outlet ? $produk->outlet->nama_outlet : '-';
            })
             ->addColumn('image', function ($produk) {
                if ($produk->primaryImage) {
                    $imageUrl = asset('storage/'.$produk->primaryImage->path);
                    return $imageUrl; // Kembalikan URL string, bukan HTML
                }
                return null; // Kembalikan null jika tidak ada gambar
            })
            ->addColumn('sku', function ($produk) {
                return $produk->kode_produk;
            })
            ->addColumn('name', function ($produk) {
                return $produk->nama_produk;
            })
            ->addColumn('type', function ($produk) {
                $types = [
                    'barang_dagang' => 'Barang Dagang',
                    'jasa' => 'Jasa',
                    'paket_travel' => 'Paket Travel',
                    'produk_kustom' => 'Produk Kustom'
                ];
                return $types[$produk->tipe_produk] ?? $produk->tipe_produk;
            })
            ->addColumn('category', function ($produk) {
                return $produk->kategori ? $produk->kategori->nama_kategori : '-';
            })
            ->addColumn('unit', function ($produk) {
                return $produk->satuan ? $produk->satuan->nama_satuan : '-';
            })
            ->addColumn('stock', function ($produk) {
                return $produk->hpp_produk_sum_stok ?? 0;
            })
            ->addColumn('price', function ($produk) {
                return $produk->harga_jual; // Kembalikan angka, bukan string format
            })
            ->addColumn('is_active', function ($produk) {
                return $produk->is_active ? 'ACTIVE' : 'INACTIVE';
            })
            ->addColumn('aksi', function ($produk) {
                return '
                    <div class="flex justify-end gap-2">
                        <button onclick="editForm(`'. route('admin.inventaris.produk.update', $produk->id_produk) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button onclick="deleteData(`'. route('admin.inventaris.produk.destroy', $produk->id_produk) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Store untuk frontend baru
     */
    public function store(Request $request)
    {
        // Jika request dari frontend baru
        if ($request->has('nama_produk') && !$request->has('_token')) {
            $validator = Validator::make($request->all(), [
                'nama_produk' => 'required|string|max:255',
                'id_outlet' => 'required|integer|exists:outlets,id_outlet',
                'tipe_produk' => 'required|string|in:barang_dagang,jasa,paket_travel,produk_kustom',
                'id_kategori' => 'required|integer|exists:kategori,id_kategori',
                'id_satuan' => 'required|integer|exists:satuan,id_satuan',
                'harga_jual' => 'required|numeric|min:0',
                'stok' => 'nullable|numeric|min:0',
                'diskon' => 'nullable|numeric|min:0|max:100',
                'merk' => 'nullable|string|max:255',
                'spesifikasi' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ], [
                'id_outlet.exists' => 'Outlet yang dipilih tidak valid.',
                'id_kategori.exists' => 'Kategori yang dipilih tidak valid.',
                'id_satuan.exists' => 'Satuan yang dipilih tidak valid.',
                'tipe_produk.in' => 'Tipe produk harus salah satu dari: barang_dagang, jasa, paket_travel, produk_kustom.',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            \Log::info('Store Product Data:', $request->all());

            try {
                // Generate kode produk otomatis
                $kodeProduk = $this->generateKodeProduk();

                $produk = Produk::create([
                    'kode_produk' => $kodeProduk,
                    'nama_produk' => $request->nama_produk,
                    'id_outlet' => $request->id_outlet,
                    'id_kategori' => $request->id_kategori,
                    'id_satuan' => $request->id_satuan,
                    'tipe_produk' => $request->tipe_produk,
                    'merk' => $request->merk ?? '',
                    'spesifikasi' => $request->spesifikasi ?? '',
                    'harga_jual' => is_numeric($request->harga_jual) ? floatval($request->harga_jual) : 0,
                    'diskon' => is_numeric($request->diskon ?? 0) ? floatval($request->diskon) : 0,
                    'stok_minimum' => is_numeric($request->stok_minimum ?? 0) ? intval($request->stok_minimum) : 0,
                    'is_active' => $request->is_active ?? true,
                ]);

                \Log::info('Product created successfully:', ['id' => $produk->id_produk]);

                // Handle stok awal
                if ($request->stok > 0) {
                    $produk->addStock($request->harga_jual * 0.7, $request->stok); // HPP diasumsikan 70% dari harga jual
                }

                // Handle images
                if ($request->hasFile('images')) {
                    \Log::info('Processing images', ['count' => count($request->file('images'))]);
                    foreach ($request->file('images') as $key => $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('product_images', 'public');
                            \Log::info('Image stored', ['key' => $key, 'path' => $path]);
                            
                            ProductImage::create([
                                'id_produk' => $produk->id_produk,
                                'path' => $path,
                                'is_primary' => $key === 0
                            ]);
                        } else {
                            \Log::warning('Invalid image file', ['key' => $key]);
                        }
                    }
                } else {
                    \Log::info('No images in request');
                }

                // Handle variants
                if ($request->has('variants') && is_array($request->variants)) {
                    foreach ($request->variants as $variant) {
                        ProductVariant::create([
                            'product_id' => $produk->id_produk,
                            'nama_varian' => $variant['name'] ?? '',
                            'sku' => $variant['sku'] ?? '',
                            'harga' => is_numeric($variant['price'] ?? 0) ? floatval($variant['price']) : 0,
                            'stok' => is_numeric($variant['stock'] ?? 0) ? intval($variant['stock']) : 0,
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'Data berhasil disimpan', 
                    'data' => $produk,
                    'generated_sku' => $kodeProduk
                ], 200);

            } catch (\Exception $e) {
                \Log::error('Error creating product: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Gagal menyimpan data: ' . $e->getMessage()
                ], 500);
            }
        }

        // Jika request dari frontend lama (tetap pertahankan)
        $request->validate([
            'images' => 'sometimes|array|max:4',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.nama_varian' => 'required_if:variants.*.is_default,false',
            'variants.*.harga' => 'required_if:variants.*.is_default,false|numeric',
        ]);

        $produk = Produk::latest()->first() ?? new Produk();
        $kode_produk = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);

        $produk = new Produk();
        $produk->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0];
        $produk->id_kategori = $request->id_kategori;
        $produk->id_satuan = $request->id_satuan;
        $produk->kode_produk = $kode_produk;
        $produk->nama_produk = $request->nama_produk;
        $produk->merk = $request->merk;
        $produk->diskon = $request->diskon;
        $produk->harga_jual = $request->harga_jual;
        $produk->spesifikasi = $request->spesifikasi;
        
        // Field baru
        if ($request->tipe_produk === 'barang_dagang') {
            $produk->metode_hpp = $request->metode_hpp ?? 'FIFO';
        } else {
            $produk->metode_hpp = 'RAB';
        }
        $produk->tipe_produk = $request->tipe_produk;
        $produk->track_inventory = $request->has('track_inventory');
        $produk->jenis_paket = $request->jenis_paket;
        $produk->keberangkatan_template_id = $request->keberangkatan_template_id;
        $produk->stok_minimum = $request->stok_minimum ?? 0;

        $produk->save();

        // Handle file uploads
        if ($request->hasFile('images')) {
            
            foreach ($request->file('images') as $key => $file) {
                try {
                    $path = $file->store('product_images', 'public');
                    
                    $isPrimary = $request->input("uploaded_images.{$key}.is_primary", $key === 0 ? 'true' : 'false');
                    
                    ProductImage::create([
                        'id_produk' => $produk->id_produk,
                        'path' => $path,
                        'is_primary' => $isPrimary === 'true'
                    ]);
                    
                
                } catch (\Exception $e) {
                    \Log::error("Image save failed: " . $e->getMessage());
                }
            }
        } else {
            \Log::warning('No images found in request');
        }

        // Simpan varian produk
        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                $isDefault = $request->is_default == $index;
                
                $produk->variants()->create([
                    'nama_varian' => $variantData['nama_varian'],
                    'deskripsi' => $variantData['deskripsi'],
                    'harga' => $isDefault ? $produk->harga_jual : str_replace('.', '', $variantData['harga']),
                    'is_default' => $isDefault
                ]);
            }
        }

        
        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Show untuk frontend baru
     */
    public function show($id)
    {
        $produk = Produk::with(['kategori', 'satuan', 'outlet', 'images', 'variants'])->find($id);
        
        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $produk->id_produk,
            'outlet' => $produk->outlet ? $produk->outlet->nama_outlet : '',
            'type' => $produk->tipe_produk,
            'sku' => $produk->kode_produk,
            'name' => $produk->nama_produk,
            'category' => $produk->kategori ? $produk->kategori->nama_kategori : '',
            'unit' => $produk->satuan ? $produk->satuan->nama_satuan : '',
            'stock' => $produk->hpp_produk_sum_stok ?? 0,
            'price' => $produk->harga_jual,
            'discount' => $produk->diskon,
            'brand' => $produk->merk,
            'desc' => $produk->spesifikasi,
            'is_active' => $produk->is_active,
            'images' => $produk->images->map(function($image) {
                return asset('storage/'.$image->path);
            })->toArray(),
            'variants' => $produk->variants->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->nama_varian,
                    'sku' => $variant->sku,
                    'price' => $variant->harga,
                    'stock' => $variant->stok
                ];
            })->toArray()
        ]);
    }

    /**
     * Update untuk frontend baru
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        
        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        // Jika request dari frontend baru (menggunakan FormData dengan Accept: application/json dan _method)
        if (($request->expectsJson() || $request->header('Accept') === 'application/json') && $request->has('_method')) {
            \Log::info('Update Product - Frontend Baru', [
                'id' => $id,
                'nama_produk' => $request->nama_produk,
                'all_data' => $request->all()
            ]);
            
            $validator = Validator::make($request->all(), [
                'nama_produk' => 'required',
                'id_outlet' => 'required',
                'tipe_produk' => 'required',
                'id_kategori' => 'required',
                'id_satuan' => 'required',
                'harga_jual' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $produk->update([
                'nama_produk' => $request->nama_produk,
                'id_outlet' => $request->id_outlet,
                'id_kategori' => $request->id_kategori,
                'id_satuan' => $request->id_satuan,
                'tipe_produk' => $request->tipe_produk,
                'merk' => $request->merk ?? '',
                'spesifikasi' => $request->spesifikasi ?? '',
                'harga_jual' => is_numeric($request->harga_jual) ? floatval($request->harga_jual) : 0,
                'diskon' => is_numeric($request->diskon ?? 0) ? floatval($request->diskon) : 0,
                'stok_minimum' => is_numeric($request->stok_minimum ?? 0) ? intval($request->stok_minimum) : 0,
                'is_active' => $request->is_active ?? true,
            ]);

            // Handle images update
            if ($request->has('deleted_images')) {
                $imagesToDelete = ProductImage::where('id_produk', $produk->id_produk)
                    ->whereIn('id_image', $request->deleted_images)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                \Log::info('Processing images for update', ['count' => count($request->file('images'))]);
                foreach ($request->file('images') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('product_images', 'public');
                        \Log::info('Image stored for update', ['key' => $key, 'path' => $path]);
                        
                        ProductImage::create([
                            'id_produk' => $produk->id_produk,
                            'path' => $path,
                            'is_primary' => false
                        ]);
                    }
                }
            }

            // Handle variants update - delete all old variants and create new ones
            // This is simpler and safer than trying to track which ones to update/delete
            if ($request->has('variants')) {
                // Delete all existing variants
                ProductVariant::where('product_id', $produk->id_produk)->delete();
                
                // Create new variants if any
                if (is_array($request->variants) && count($request->variants) > 0) {
                    foreach ($request->variants as $variant) {
                        // Skip empty variants
                        if (empty($variant['name'])) {
                            continue;
                        }
                        
                        // Sanitize data
                        ProductVariant::create([
                            'product_id' => $produk->id_produk,
                            'nama_varian' => $variant['name'] ?? '',
                            'sku' => $variant['sku'] ?? '',
                            'harga' => is_numeric($variant['price'] ?? 0) ? floatval($variant['price']) : 0,
                            'stok' => is_numeric($variant['stock'] ?? 0) ? intval($variant['stock']) : 0,
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Data berhasil diupdate', 'data' => $produk], 200);
        }

        // Jika request dari frontend lama (tetap pertahankan)
        $produk = Produk::findOrFail($id);

        // Update data produk
        $produk->update([
            'tipe_produk' => $request->tipe_produk,
            'nama_produk' => $request->nama_produk,
            'id_kategori' => $request->id_kategori,
            'merk' => $request->merk,
            'harga_jual' => str_replace('.', '', $request->harga_jual),
            'diskon' => $request->diskon,
            'id_satuan' => $request->id_satuan,
            'spesifikasi' => $request->spesifikasi,
            'metode_hpp' => $request->metode_hpp,
            'stok_minimum' => $request->stok_minimum,
            'jenis_paket' => $request->jenis_paket,
            'keberangkatan_template_id' => $request->keberangkatan_template_id
        ]);

        DB::transaction(function () use ($request, $produk) {
            // Hapus RAB yang ditandai untuk dihapus
            if ($request->has('deleted_rabs')) {
                DB::table('produk_rab')
                    ->where('id_produk', $produk->id_produk)
                    ->whereIn('id_rab', $request->deleted_rabs)
                    ->delete();
            }
    
            // Sync RAB yang baru dipilih
            if ($request->has('rabs')) {
                // Hapus semua yang tidak termasuk dalam rabs baru
                DB::table('produk_rab')
                    ->where('id_produk', $produk->id_produk)
                    ->whereNotIn('id_rab', $request->rabs)
                    ->delete();
    
                // Tambahkan yang baru
                $existingRabs = DB::table('produk_rab')
                    ->where('id_produk', $produk->id_produk)
                    ->pluck('id_rab')
                    ->toArray();
    
                $newRabs = array_diff($request->rabs, $existingRabs);
    
                foreach ($newRabs as $rabId) {
                    DB::table('produk_rab')->insert([
                        'id_produk' => $produk->id_produk,
                        'id_rab' => $rabId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
    
                // Update realisasi pemakaian jika ada
                if ($request->has('realisasi')) {
                    foreach ($request->realisasi as $rabId => $realisasi) {
                        $realisasiValue = str_replace('.', '', $realisasi);
                        $rab = RabTemplate::with('details')->find($rabId);
                        
                        if ($rab) {
                            $realisasiPerKomponen = $realisasiValue / max(1, $rab->details->count());
                            
                            foreach ($rab->details as $detail) {
                                $detail->update(['realisasi_pemakaian' => $realisasiPerKomponen]);
                            }
                        }
                    }
                }
            }

            // Hapus komponen yang dihapus
            if ($request->has('deleted_components')) {
                $produk->components()->whereIn('id', $request->deleted_components)->delete();
            }
            
            // Simpan/update komponen
            if ($request->has('components')) {
                foreach ($request->components as $componentData) {
                    $componentData['subtotal'] = str_replace('.', '', $componentData['subtotal']);
                    
                    if (isset($componentData['id'])) {
                        $produk->components()->where('id', $componentData['id'])->update([
                            'component_id' => $componentData['product_id'],
                            'qty' => $componentData['qty'],
                            'subtotal' => $componentData['subtotal']
                        ]);
                    } else {
                        $produk->components()->create([
                            'component_id' => $componentData['product_id'],
                            'qty' => $componentData['qty'],
                            'subtotal' => $componentData['subtotal']
                        ]);
                    }
                }
            }
        });

        // Handle existing images
        $existingImageIds = [];
        if ($request->has('existing_images')) {
            foreach ($request->existing_images as $index => $existingImage) {
                if (!isset($existingImage['id'])) continue;
                
                $image = ProductImage::where('id_image', $existingImage['id'])->first();
                if ($image) {
                    $isPrimary = $request->input("uploaded_images.{$index}.is_primary", 'false') === 'true';
                    
                    $image->update([
                        'is_primary' => $isPrimary
                    ]);
                    
                    $existingImageIds[] = $image->id_image;
                }
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('product_images', 'public');
                    
                    $isPrimary = $request->input("uploaded_images.{$index}.is_primary", 'false') === 'true';
                    
                    ProductImage::create([
                        'id_produk' => $produk->id_produk,
                        'path' => $path,
                        'is_primary' => $isPrimary
                    ]);
                }
            }
        }
    
        // Handle deleted images
        if ($request->filled('deleted_images')) { // Gunakan filled() bukan has()
            $deletedIds = is_array($request->deleted_images) ? $request->deleted_images : [$request->deleted_images];
            
            $imagesToDelete = ProductImage::where('id_produk', $produk->id_produk)
                ->whereIn('id_image', $deletedIds)
                ->get();

            if ($imagesToDelete->isEmpty()) {
                Log::warning('No images found to delete', ['ids' => $deletedIds]);
            }

            foreach ($imagesToDelete as $image) {
                try {
                    $filePath = 'public/' . $image->path;
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    } else {
                        Log::warning("File not found: " . $filePath);
                    }
                    
                    $image->delete();
                    
                } catch (\Exception $e) {
                    Log::error("Error deleting image ID {$image->id_image}: " . $e->getMessage());
                }
            }
        } else {
            Log::info('No deleted_images in request');
        }

        // Simpan varian produk
        if ($request->has('variants')) {
            // Hapus varian lama jika ada (untuk update)
            if ($produk->exists) {
                $produk->variants()->delete();
            }
            
            foreach ($request->variants as $index => $variantData) {
                // Gunakan $index dari loop sebagai pembanding
                $isDefault = $request->is_default == $index;
                
                $produk->variants()->create([
                    'nama_varian' => $variantData['nama_varian'],
                    'deskripsi' => $variantData['deskripsi'] ?? null,
                    'harga' => $isDefault ? $produk->harga_jual : str_replace('.', '', $variantData['harga']),
                    'is_default' => $isDefault
                ]);
            }
        }

        // Ensure only one primary image exists
        $primaryImage = ProductImage::where('id_produk', $produk->id_produk)
            ->where('is_primary', true)
            ->orderBy('updated_at', 'desc')
            ->first();
            
        if ($primaryImage) {
            ProductImage::where('id_produk', $produk->id_produk)
                ->where('is_primary', true)
                ->where('id_image', '!=', $primaryImage->id_image)
                ->update(['is_primary' => false]);
        } else {
            // If no primary image, set the first one as primary
            $firstImage = ProductImage::where('id_produk', $produk->id_produk)
                ->orderBy('id_image')
                ->first();
                
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Data berhasil diperbarui'], 200);
    }

    /**
     * Destroy untuk frontend baru
     */
    public function destroy($id)
    {
        $produk = Produk::with(['images', 'variants'])->find($id);
        
        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        // Delete images from storage
        foreach ($produk->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $produk->delete();

        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    // ========== FUNGSI BARU UNTUK FRONTEND BARU ==========

    // Generate SKU otomatis
    public function getNewSku()
    {
        $kodeProduk = $this->generateKodeProduk();
        return response()->json(['sku' => $kodeProduk]);
    }

    private function generateKodeProduk()
    {
        $lastProduk = Produk::latest()->first();
        $newNumber = $lastProduk ? ((int) substr($lastProduk->kode_produk, 1)) + 1 : 1;
        return 'P' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $query = Produk::with(['kategori', 'satuan', 'outlet'])
            ->withSum('hppProduk', 'stok');

        // Apply filters
        if ($request->has('outlet') && $request->outlet !== 'ALL') {
            $query->whereHas('outlet', function($q) use ($request) {
                $q->where('nama_outlet', $request->outlet);
            });
        }

        if ($request->has('type') && $request->type !== 'ALL') {
            $query->where('tipe_produk', $request->type);
        }

        $produks = $query->get();

        $pdf = PDF::loadView('admin.inventaris.produk.export_pdf', [
            'produks' => $produks,
            'filterOutlet' => $request->outlet,
            'filterType' => $request->type
        ]);

        return $pdf->download('produk-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new ProdukExport($request), 'produk-' . date('Y-m-d') . '.xlsx');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ProdukImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    // Download Template Excel
    public function downloadTemplate()
    {
        return Excel::download(new ProdukExport(true), 'template-import-produk.xlsx');
    }

    // Get data untuk filter
    public function getOutlets()
    {
        // Get user's accessible outlets using trait
        $userOutlets = $this->getUserOutlets();
        
        $outlets = $userOutlets->pluck('nama_outlet')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return response()->json($outlets);
    }

    public function getCategories()
    {
        $categories = Kategori::select('nama_kategori')
            ->whereNotNull('nama_kategori')
            ->where('nama_kategori', '!=', '')
            ->distinct()
            ->orderBy('nama_kategori')
            ->pluck('nama_kategori');

        return response()->json($categories);
    }

    public function getUnits()
    {
        $units = Satuan::select('nama_satuan')
            ->whereNotNull('nama_satuan')
            ->where('nama_satuan', '!=', '')
            ->distinct()
            ->orderBy('nama_satuan')
            ->pluck('nama_satuan');

        return response()->json($units);
    }

    // ========== FUNGSI LAMA TETAP DI PERTAHANKAN ==========

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::when(!empty(auth()->user()->akses_outlet), function($query) {
            return $query->whereIn('id_outlet', auth()->user()->akses_outlet);
        })->pluck('nama_kategori', 'id_kategori');

        $satuan = Satuan::all()->pluck('nama_satuan', 'id_satuan');

        // Tambahkan ini jika diperlukan di form create
        $productTypes = [
            'barang_dagang' => 'Barang Dagang',
            'jasa' => 'Jasa',
            'paket_travel' => 'Paket Travel',
            'produk_kustom' => 'Produk Kustom'
        ];

        return view('produk.create', compact('kategori', 'satuan', 'productTypes'));
    }

    public function storeHPP(Request $request)
    {
        try {
            $hppProduk = new HppProduk();
            $hppProduk->id_produk = $request->id_produk;
            $hppProduk->hpp = $request->hpp;
            $hppProduk->stok = $request->stok;
            $hppProduk->save();

            return response()->json(['message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showDetail($id)
    {
        $detail = HppProduk::with('produk')->where('id_produk', $id)->orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($detail) {
                return tanggal_indonesia($detail->created_at, false);
            })
            ->addColumn('hpp', function ($detail) {
                return format_uang($detail->hpp);
            })
            ->addColumn('stok', function ($detail) {
                return $detail->stok;
            })
            ->addColumn('aksi', function ($detail) {
                $updateUrl = $detail->id_hpp ? route('produk.edit_hpp', $detail->id_hpp) : '#';
                $deleteUrl = $detail->id_hpp ? route('produk.destroy_hpp', $detail->id_hpp) : '#';
            
                return '
                <div class="btn-group">
                    <button onclick="editForm_hpp(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData_hpp(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })            
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk = Produk::with(['rabs.details'])->findOrFail($id);
        
        // Ambil semua RAB yang sudah terhubung dengan produk ini
        $rabsTerpilih = $produk->rabs->pluck('id_rab')->toArray();
        
        // Ambil RAB yang tersedia (belum terpilih)
        $availableRabs = RabTemplate::whereNotIn('id_rab', $rabsTerpilih)
            ->with('details')
            ->get();
        
        return response()->json([
            'produk' => $produk,
            'availableRabs' => $availableRabs,
            'totalHpp' => $produk->rabs->sum(function($rab) {
                return $rab->details->sum('nilai_disetujui');
            })
        ]);
    }

    public function editHPP($id)
    {
    
        $hppProduk = HppProduk::find($id);

        if (!$hppProduk) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($hppProduk);
    }

    public function updateHPP(Request $request, string $id)
    {
        try {
            // Cari data HPP berdasarkan ID
            $hppProduk = HppProduk::find($id);

            // Jika data tidak ditemukan, kembalikan response error
            if (!$hppProduk) {
                return response()->json(['error' => 'Data HPP tidak ditemukan'], 404);
            }

            // Update data HPP
            $hppProduk->update($request->all());

            return response()->json(['message' => 'Data berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyHPP(string $id)
    {
        HppProduk::find($id)->delete();
        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }

    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $no  = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('produk.pdf');
    }

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        
        return response()->json(['message' => 'Gambar berhasil dihapus'], 200);
    }

    // Tambahkan method untuk set primary image
    public function setPrimaryImage($id)
    {
        $image = ProductImage::findOrFail($id);
        
        // Reset semua primary image untuk produk ini
        ProductImage::where('id_produk', $image->id_produk)
            ->update(['is_primary' => false]);
            
        // Set image ini sebagai primary
        $image->update(['is_primary' => true]);
        
        return response()->json(['message' => 'Gambar utama berhasil diubah'], 200);
    }

    public function getImages($id)
    {
        $images = ProductImage::where('id_produk', $id)
                    ->orderBy('is_primary', 'desc')
                    ->orderBy('id_image')
                    ->get();
        
        return response()->json($images);
    }

    public function uploadImages(Request $request, $id)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Produk::findOrFail($id);
        
        foreach ($request->file('images') as $image) {
            $path = $image->store('product_images', 'public');
            
            ProductImage::create([
                'id_produk' => $product->id_produk,
                'path' => $path,
                'is_primary' => false
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getRabs($id)
    {
        $product = Produk::with(['rabs.details'])->findOrFail($id);
        
        $rabs = $product->rabs->map(function($rab) {
            // Load details dengan relasi untuk menghitung status
            $rab->load('details');
            
            return [
                'id_rab' => $rab->id_rab,
                'nama_template' => $rab->nama_template,
                'deskripsi' => $rab->deskripsi,
                'total_nilai_disetujui' => $rab->details->sum('nilai_disetujui'),
                'total_realisasi' => $rab->details->sum('realisasi_pemakaian'),
                'status' => $rab->status, // Ini akan memanggil getStatusAttribute()
                'details' => $rab->details->map(function($detail) {
                    return [
                        'id_rab_detail' => $detail->id_rab_detail,
                        'nama_komponen' => $detail->nama_komponen,
                        'jumlah' => $detail->jumlah,
                        'satuan' => $detail->satuan,
                        'nilai_disetujui' => $detail->nilai_disetujui,
                        'realisasi_pemakaian' => $detail->realisasi_pemakaian,
                        'disetujui' => $detail->disetujui,
                        'bukti_transfer' => $detail->bukti_transfer
                    ];
                })->toArray()
            ];
        });
        
        return response()->json($rabs);
    }

    public function addRab(Request $request, $id)
    {
        $request->validate([
            'id_rab' => 'required|exists:rab_template,id_rab',
            'qty' => 'required|integer|min:1'
        ]);
        
        $product = Produk::findOrFail($id);
        $rab = RabTemplate::findOrFail($request->id_rab);
        
        $product->rabs()->attach($request->id_rab, [
            'subtotal' => $rab->total_biaya_per_orang * $request->qty,
            'qty' => $request->qty
        ]);
        
        return response()->json(['success' => true]);
    }

    public function removeRab(Request $request, $id)
    {
        $request->validate([
            'id_rab' => 'required|exists:rab_template,id_rab'
        ]);
        
        $product = Produk::findOrFail($id);
        $product->rabs()->detach($request->id_rab);
        
        return response()->json(['success' => true]);
    }

    public function getVariants($id)
    {
        $produk = Produk::with('variants')->findOrFail($id);
        return response()->json($produk->variants);
    }

    public function search(Request $request)
    {
        $term = $request->q;
        
        $produk = Produk::where('nama_produk', 'like', "%$term%")
            ->orWhere('kode_produk', 'like', "%$term%")
            ->paginate(10);
        
        return response()->json([
            'data' => $produk->items(),
            'next_page_url' => $produk->nextPageUrl()
        ]);
    }

    public function getComponents($id)
    {
        $produk = Produk::with(['components.component'])->findOrFail($id);
        
        return response()->json(
            $produk->components->map(function($component) {
                return [
                    'id' => $component->id,
                    'component_id' => $component->component_id,
                    'component' => [
                        'kode_produk' => $component->component->kode_produk,
                        'nama_produk' => $component->component->nama_produk,
                        'harga_jual' => $component->component->harga_jual
                    ],
                    'qty' => $component->qty,
                    'subtotal' => $component->subtotal
                ];
            })
        );
    }

    public function apiShow($id)
    {
        $product = Produk::with(['images', 'variants', 'kategori'])
            ->withSum('hppProduk', 'stok')
            ->findOrFail($id);
        
        return response()->json($product);
    }

    public function apiIndex(Request $request)
    {
        $query = Produk::with([
                'images',
                'variants',
                'kategori',
                'components.component' // Include components with their product data
            ])
            ->orderBy('created_at', 'asc')
            ->when($request->category && $request->category != 'all', function($query) use ($request) {
                return $query->where('id_kategori', $request->category);
            })
            ->when($request->search, function($query) use ($request) {
                return $query->where('nama_produk', 'like', '%'.$request->search.'%');
            });

        // Include components if requested
        if ($request->has('include') && str_contains($request->include, 'components')) {
            $query->with(['components.component']);
        }

        $products = $query->paginate(9);

        // Transform images to include full URL
        $products->getCollection()->transform(function ($product) {
            $product->images->transform(function ($image) {
                $image->url = asset('storage/'.$image->path);
                return $image;
            });
            return $product;
        });

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total()
            ]
        ]);
    }

    public function apiCategories()
    {
        $categories = Kategori::all();
        return response()->json($categories);
        //buat log info kategori apa saja, dan jika kosong
        Log::info('Categories:', ['categories' => $categories]);
    }

    public function apiDetail($id)
    {
        $product = Produk::with(['images', 'variants', 'kategori', 'components.component'])
            ->findOrFail($id);

        return response()->json([
            'product' => $product,
            'variants' => $product->variants,
            'components' => $product->components->map(function($component) {
                return [
                    'id' => $component->id,
                    'name' => $component->component->nama_produk,
                    'qty' => $component->qty,
                    'price' => $component->component->harga_jual,
                    'subtotal' => $component->subtotal
                ];
            })
        ]);
    }

    public function cari(Request $request)
    {
        $keyword = $request->get('keyword');
        
        $produk = Produk::where('nama_produk', 'like', "%$keyword%")
                    ->orWhere('kode_produk', 'like', "%$keyword%")
                    ->limit(10)
                    ->get();
        
        return response()->json($produk);
    }

    // Di ProdukController.php
    public function getIdMappings(Request $request)
    {
        try {
            $outlets = Outlet::pluck('id_outlet', 'nama_outlet')->toArray();
            $categories = Kategori::pluck('id_kategori', 'nama_kategori')->toArray();
            $units = Satuan::pluck('id_satuan', 'nama_satuan')->toArray();

            return response()->json([
                'success' => true,
                'outlets' => $outlets ?: [],
                'categories' => $categories ?: [],
                'units' => $units ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading ID mappings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'outlets' => [],
                'categories' => [],
                'units' => []
            ], 500);
        }
    }
}