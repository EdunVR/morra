<?php

namespace App\Http\Controllers;

use App\Models\Tipe;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerTypeController extends Controller
{
    /**
     * Display customer type management page
     */
    public function index(Request $request)
    {
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.crm.tipe.index', compact('outlets'));
    }

    /**
     * Get customer type data
     */
    public function getData(Request $request)
    {
        $search = $request->get('search', '');

        $query = Tipe::query();

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_tipe', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $types = $query->orderBy('nama_tipe', 'asc')->get();

        // Transform data for frontend
        $data = $types->map(function($type) {
            return [
                'id_tipe' => $type->id_tipe,
                'nama_tipe' => $type->nama_tipe,
                'keterangan' => $type->keterangan,
                'member_count' => \App\Models\Member::where('id_tipe', $type->id_tipe)->count(),
                'created_at' => $type->created_at ? $type->created_at->format('d/m/Y') : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new customer type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_tipe' => 'required|string|max:255|unique:tipe,nama_tipe',
            'keterangan' => 'nullable|string',
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

            $tipe = Tipe::create($request->only(['nama_tipe', 'keterangan']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipe customer berhasil ditambahkan',
                'data' => $tipe
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating customer type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show customer type detail
     */
    public function show($id)
    {
        try {
            $tipe = Tipe::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $tipe
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe customer tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update customer type
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_tipe' => 'required|string|max:255|unique:tipe,nama_tipe,' . $id . ',id_tipe',
            'keterangan' => 'nullable|string',
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

            $tipe = Tipe::findOrFail($id);
            $tipe->update($request->only(['nama_tipe', 'keterangan']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipe customer berhasil diupdate',
                'data' => $tipe
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating customer type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tipe customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer type
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $tipe = Tipe::findOrFail($id);
            
            // Check if type has members
            $memberCount = \App\Models\Member::where('id_tipe', $id)->count();
            if ($memberCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe customer tidak dapat dihapus karena masih digunakan oleh ' . $memberCount . ' pelanggan'
                ], 422);
            }

            $tipe->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipe customer berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting customer type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $totalTypes = Tipe::count();
            $totalMembers = \App\Models\Member::count();
            
            $typeUsage = Tipe::withCount('produkTipe')
                ->orderBy('produk_tipe_count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_types' => $totalTypes,
                    'total_members' => $totalMembers,
                    'type_usage' => $typeUsage
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }
}
