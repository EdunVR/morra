<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SparepartController extends Controller
{
    /**
     * Display sparepart page
     */
    public function index()
    {
        return view('admin.inventaris.sparepart.index');
    }

    /**
     * Get sparepart data for DataTables
     */
    public function getData(Request $request)
    {
        $query = Sparepart::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('harga_formatted', function ($row) {
                return 'Rp ' . number_format($row->harga, 0, ',', '.');
            })
            ->addColumn('stok_status', function ($row) {
                if ($row->stok <= 0) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Habis</span>';
                } elseif ($row->isStokMinimum()) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Stok Minimum</span>';
                } else {
                    return '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Tersedia</span>';
                }
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->is_active) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Aktif</span>';
                } else {
                    return '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Nonaktif</span>';
                }
            })
            ->addColumn('aksi', function ($row) {
                return '<div class="flex gap-1 justify-center">
                    <button onclick="viewDetail(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50" title="Detail">
                        <i class="bx bx-show text-lg"></i>
                    </button>
                    <button onclick="editSparepart(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-yellow-200 text-yellow-700 hover:bg-yellow-50" title="Edit">
                        <i class="bx bx-edit text-lg"></i>
                    </button>
                    <button onclick="adjustStok(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50" title="Sesuaikan Stok">
                        <i class="bx bx-package text-lg"></i>
                    </button>
                    <button onclick="deleteSparepart(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50" title="Hapus">
                        <i class="bx bx-trash text-lg"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['stok_status', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Store new sparepart
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_sparepart' => 'required|string|max:50|unique:spareparts,kode_sparepart',
            'nama_sparepart' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $sparepart = Sparepart::create([
                    'kode_sparepart' => $request->kode_sparepart,
                    'nama_sparepart' => $request->nama_sparepart,
                    'merk' => $request->merk,
                    'spesifikasi' => $request->spesifikasi,
                    'harga' => $request->harga,
                    'stok' => $request->stok,
                    'stok_minimum' => $request->stok_minimum,
                    'satuan' => $request->satuan,
                    'is_active' => true,
                    'keterangan' => $request->keterangan
                ]);

                // Log stok awal
                if ($request->stok > 0) {
                    SparepartLog::create([
                        'id_sparepart' => $sparepart->id_sparepart,
                        'id_user' => auth()->id(),
                        'tipe_perubahan' => 'stok',
                        'nilai_lama' => 0,
                        'nilai_baru' => $request->stok,
                        'selisih' => $request->stok,
                        'keterangan' => 'Stok awal'
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sparepart by ID
     */
    public function show($id)
    {
        $sparepart = Sparepart::with(['logs.user'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $sparepart
        ]);
    }

    /**
     * Update sparepart
     */
    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'kode_sparepart' => 'required|string|max:50|unique:spareparts,kode_sparepart,' . $id . ',id_sparepart',
            'nama_sparepart' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request, $sparepart) {
                // Log perubahan harga
                if ($request->harga != $sparepart->harga) {
                    SparepartLog::create([
                        'id_sparepart' => $sparepart->id_sparepart,
                        'id_user' => auth()->id(),
                        'tipe_perubahan' => 'harga',
                        'nilai_lama' => $sparepart->harga,
                        'nilai_baru' => $request->harga,
                        'selisih' => $request->harga - $sparepart->harga,
                        'keterangan' => 'Perubahan harga'
                    ]);
                }

                $sparepart->update([
                    'kode_sparepart' => $request->kode_sparepart,
                    'nama_sparepart' => $request->nama_sparepart,
                    'merk' => $request->merk,
                    'spesifikasi' => $request->spesifikasi,
                    'harga' => $request->harga,
                    'stok_minimum' => $request->stok_minimum,
                    'satuan' => $request->satuan,
                    'is_active' => $request->is_active,
                    'keterangan' => $request->keterangan
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adjust stock
     */
    public function adjustStok(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'tipe' => 'required|in:tambah,kurang',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $sparepart) {
                $stokLama = $sparepart->stok;
                $jumlah = $request->jumlah;

                if ($request->tipe === 'tambah') {
                    $sparepart->tambahStok($jumlah);
                    $selisih = $jumlah;
                } else {
                    if (!$sparepart->isStokMencukupi($jumlah)) {
                        throw new \Exception('Stok tidak mencukupi');
                    }
                    $sparepart->kurangiStok($jumlah);
                    $selisih = -$jumlah;
                }

                // Log perubahan stok
                SparepartLog::create([
                    'id_sparepart' => $sparepart->id_sparepart,
                    'id_user' => auth()->id(),
                    'tipe_perubahan' => 'stok',
                    'nilai_lama' => $stokLama,
                    'nilai_baru' => $sparepart->stok,
                    'selisih' => $selisih,
                    'keterangan' => $request->keterangan
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil disesuaikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyesuaikan stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete sparepart
     */
    public function destroy($id)
    {
        try {
            $sparepart = Sparepart::findOrFail($id);
            $sparepart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sparepart logs
     */
    public function getLogs($id)
    {
        $logs = SparepartLog::with(['user'])
            ->where('id_sparepart', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Search sparepart (for autocomplete)
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');
        
        $spareparts = Sparepart::active()
            ->where(function($query) use ($search) {
                $query->where('kode_sparepart', 'like', '%' . $search . '%')
                    ->orWhere('nama_sparepart', 'like', '%' . $search . '%')
                    ->orWhere('merk', 'like', '%' . $search . '%');
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $spareparts
        ]);
    }

    /**
     * Generate next kode sparepart
     */
    public function generateKode()
    {
        $lastSparepart = Sparepart::orderBy('kode_sparepart', 'desc')->first();
        
        $nextNumber = 1;
        if ($lastSparepart) {
            $lastCode = $lastSparepart->kode_sparepart;
            if (preg_match('/SP(\d+)/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }
        
        $newCode = 'SP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return response()->json([
            'success' => true,
            'kode' => $newCode
        ]);
    }
}
