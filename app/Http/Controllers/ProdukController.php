<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use App\Models\HppProduk;
use PDF;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        $satuan = Satuan::all()->pluck('nama_satuan', 'id_satuan');
        return view('produk.index', compact('kategori', 'satuan'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->leftJoin('satuan', 'satuan.id_satuan', 'produk.id_satuan')
            ->select('produk.*', 'nama_kategori', 'nama_satuan')
            ->withSum('hppProduk', 'stok')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('stok', function ($produk) {
                return $produk->hpp_produk_sum_stok ?? 0;
            })
            ->addColumn('nama_satuan', function ($produk) { // Tambahkan kolom nama_satuan
                return $produk->nama_satuan ?? '-';
            })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="showDetail(`'. route('produk.show', $produk->id_produk) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-eye"></i> List HPP</button>
                    <button type="button" onclick="editForm(`'. route('produk.update', $produk->id_produk) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
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
        $produk = Produk::latest()->first() ?? new Produk();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);

        $produk = Produk::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
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
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($produk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->update($request->all());

        return response()->json(['message' => 'Data berhasil diperbarui'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

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
}
