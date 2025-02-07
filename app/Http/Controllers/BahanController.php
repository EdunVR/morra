<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\Bahan;
use App\Models\BahanDetail;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuan = Satuan::all()->pluck('nama_satuan', 'id_satuan');
        return view('bahan.index', compact('satuan'));
    }

    public function data()
    {
        $bahan = Bahan::with('satuan')
            ->withSum('hargaBahan', 'stok')
            ->get();
        // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');

        $data = datatables()
            ->of($bahan)
            ->addIndexColumn()
            ->addColumn('select-all', function ($bahan) {
                return '
                <input type="checkbox" name="id_bahan[]" value="'.$bahan->id_bahan.'">
            ';
            })
            ->addColumn('nama_bahan', function ($bahan) {
                return '
                <span class="label label-success">'. $bahan->nama_bahan .'</span>';
            })
            ->addColumn('stok', function ($bahan) {
                // return $bahan->stok;
                return $bahan->harga_bahan_sum_stok ?? 0;
            })
            ->addColumn('nama_satuan', function ($bahan) {
                return $bahan->satuan ? $bahan->satuan->nama_satuan : '-';
            })
            ->addColumn('aksi', function ($bahan) {
                $updateUrl = route('bahan.update', $bahan->id_bahan);
                $deleteUrl = route('bahan.destroy', $bahan->id_bahan);
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('bahan.show', $bahan->id_bahan) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-eye"></i> Harga Beli</button>
                    <button onclick="editForm(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['aksi', 'select-all', 'nama_bahan'])
            ->make(true);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $bahan = Bahan::create($request->all());
        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detail = BahanDetail::with('bahan')->where('id_bahan', $id)->orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($detail) {
                return tanggal_indonesia($detail->created_at, false);
            })
            ->addColumn('harga_beli', function ($detail) {
                return format_uang($detail->harga_beli);
            })
            ->addColumn('stok', function ($detail) {
                return $detail->stok;
            })
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bahan = Bahan::find($id);

        if (!$bahan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($bahan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bahan = Kategori::find($id);
        $bahan->nama_bahan = $request->nama_bahan;
        $bahan->merk = $request->merk;
        $bahan->harga_beli = $request->harga_beli;
        $bahan->stok = $request->stok;
        $bahan->id_satuan = $request->id_satuan;
        $bahan->update();
        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Bahan::find($id)->delete();
        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_bahan as $id) {
            $bahan = Bahan::find($id);
            $bahan->delete();
        }

        return response(null, 204);
    }
}
