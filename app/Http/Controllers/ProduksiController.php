<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\ProduksiDetail;
use App\Models\BahanDetail;
use App\Models\HppProduk;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::all();
        $bahans = Bahan::with('hargaBahan')->get(); // Pastikan harga bahan di-load
        return view('produksi.index', compact('produks', 'bahans'));
    }

    public function data()
    {
        $produksi = Produksi::orderBy('id_produksi', 'desc')->get();

        return datatables()
            ->of($produksi)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($produksi) {
                return tanggal_indonesia($produksi->created_at, false);
            })
            ->addColumn('produk', function ($produksi) {
                return $produksi->produk->nama_produk;
            })
            ->addColumn('aksi', function ($produksi) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="showDetail(`'. route('produksi.show', $produksi->id_produksi) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('produksi.destroy', $produksi->id_produksi) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::all();
        return view('produksi.create', compact('produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Simpan data produksi
        $produksi = new Produksi();
        $produksi->id_produk = $request->id_produk;
        $produksi->jumlah = $request->jumlah;
        $produksi->save();

        $total_harga_beli = 0;

        // Simpan detail produksi
        foreach ($request->bahan as $id_bahan => $data) {
            if (!isset($data['checked']) || $data['checked'] != 1) {
                continue; // Skip bahan yang tidak dipilih
            }
        
            $jumlah = $data['jumlah'] ?? 0;
            $harga_id = $data['harga_id'] ?? null;
        
            if (!$harga_id) {
                return redirect()->back()->withErrors(['harga' => 'Harga bahan harus dipilih!']);
            }
        
            $hargaBahan = BahanDetail::find($harga_id);
            if (!$hargaBahan || $jumlah > $hargaBahan->stok) {
                return redirect()->back()->withErrors(['bahan' => 'Stok tidak cukup untuk bahan yang dipilih.']);
            }
        
            // Kurangi stok dari harga_bahan
            $hargaBahan->stok -= $jumlah;
            $hargaBahan->save();
        
            // Simpan detail produksi
            $produksiDetail = new ProduksiDetail();
            $produksiDetail->id_produksi = $produksi->id_produksi;
            $produksiDetail->id_bahan = $id_bahan;
            $produksiDetail->jumlah = $jumlah;
            $produksiDetail->harga_beli = $hargaBahan->harga_beli;
            $produksiDetail->tanggal_harga = $hargaBahan->created_at;
            $produksiDetail->save();

            $total_harga_beli += $hargaBahan->harga_beli * $jumlah;
        }        

        // Update stok produk
        $produk = Produk::find($produksi->id_produk);
        if ($produk) {
            $produk->stok += $produksi->jumlah;
            $produk->harga_beli = $total_harga_beli;
            $produk->save();
        }

        // Simpan ke tabel hpp_produk
        $hppProduk = new HppProduk();
        $hppProduk->id_produk = $produksi->id_produk;
        $hppProduk->hpp = $total_harga_beli; // Simpan total harga beli ke kolom hpp
        $hppProduk->stok = $produksi->jumlah; // Simpan jumlah produksi ke kolom stok
        $hppProduk->save();

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produksi $produksi)
    {
        return view('produksi.detail', compact('produksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produksi $produksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produksi $produksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produksi = Produksi::find($id);
        $produksi->delete();

        return response(null, 204);
    }

    
}
