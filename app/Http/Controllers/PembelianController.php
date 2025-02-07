<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\PembelianDetail;
use App\Models\Bahan;
use App\Models\BahanDetail;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplier = Supplier::orderBy('nama')->get();
        return view('pembelian.index', compact('supplier'));
    }

    public function data()
    {
        $pembelian = Pembelian::orderBy('id_pembelian', 'desc')->get();

        return datatables()
            ->of($pembelian)
            ->addIndexColumn()
            ->addColumn('total_item', function ($pembelian) {
                return $pembelian->total_item;
            })
            ->addColumn('total_harga', function ($pembelian) {
                return format_uang($pembelian->total_harga);
            })
            ->addColumn('bayar', function ($pembelian) {
                if ($pembelian->total_harga > $pembelian->bayar) {
                    return '<span class="label label-danger">' .format_uang($pembelian->bayar). '</span>';
                } elseif ($pembelian->total_harga < $pembelian->bayar) {
                    return '<span class="label label-success">' .format_uang($pembelian->bayar). '</span>';
                }
                return format_uang($pembelian->bayar);
            })
            ->addColumn('tanggal', function ($pembelian) {
                return tanggal_indonesia($pembelian->created_at, false);
            })
            ->addColumn('supplier', function ($pembelian) {
                return $pembelian->supplier->nama;
            })
            ->editColumn('diskon', function ($pembelian) {
                return $pembelian->diskon . '%';
            })
            ->addColumn('aksi', function ($pembelian) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('pembelian.show', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('pembelian.destroy', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'bayar'])
            ->make(true);
    }

    public function create($id)
    {
        $pembelian = new Pembelian();
        $pembelian->id_supplier = $id;
        $pembelian->total_item = 0;
        $pembelian->total_harga = 0;
        $pembelian->diskon = 0;
        $pembelian->bayar = 0;
        $pembelian->save();

        session(['id_pembelian' => $pembelian->id_pembelian]);
        session(['id_supplier' => $pembelian->id_supplier]);

        return redirect()->route('pembelian_detail.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('hutang') && $request->hutang == 'on') {
            $bayar = 0; // Jika "Berhutang" dicentang, nilai bayar menjadi 0
        } else {
            $bayar = $request->bayar; // Jika tidak dicentang, gunakan nilai bayar dari input
        }

        $pembelian = Pembelian::findOrFail($request->id_pembelian);
        $pembelian->total_item = $request->total_item;
        $pembelian->total_harga = $request->total;
        $pembelian->diskon = $request->diskon;
        $pembelian->bayar = $bayar;
        $pembelian->update();

        $detail = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $hargaBahan = BahanDetail::where('id_bahan', $item->id_bahan)
                                ->where('id', $item->id_harga_bahan) // Sesuai ID harga bahan
                                ->first();
                if ($hargaBahan) {
                    // Tambahkan stok berdasarkan jumlah yang dibeli
                    $hargaBahan->stok += $item->jumlah;
                    $hargaBahan->update();
                }
        }

        return redirect()->route('pembelian.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detail = PembelianDetail::with('bahan')->where('id_pembelian', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('nama_bahan', function ($detail) {
                return '<span class="label label-success">'. $detail->bahan->nama_bahan .'</span>';
            })
            ->addColumn('harga_beli', function ($detail) {
                return format_uang($detail->harga_beli);
            })
            ->addColumn('jumlah', function ($detail) {
                return $detail->jumlah;
            })
            ->addColumn('subtotal', function ($detail) {
                return format_uang($detail->subtotal);
            })
            ->rawColumns(['nama_bahan'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembelian = Pembelian::find($id);
        $detail    = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $bahan = Bahan::find($item->id_bahan);
            if ($bahan) {
                $bahan->stok -= $item->jumlah;
                $bahan->update();
            }
            $item->delete();
        }

        $pembelian->delete();

        return response(null, 204);
    }
}
