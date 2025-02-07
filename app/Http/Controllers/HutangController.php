<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use App\Models\Supplier;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        return view('hutang.index', compact('suppliers'));
    }

    public function data()
    {
        $hutang = Hutang::orderBy('id_hutang', 'desc')->get();

        return datatables()
            ->of($hutang)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($hutang) {
                return tanggal_indonesia($hutang->created_at, false);
            })
            ->addColumn('supplier', function ($hutang) {
                return $hutang->nama; // Pastikan relasi supplier ada di model Hutang
            })
            ->addColumn('hutang', function ($hutang) {
                return format_uang($hutang->hutang);
            })
            ->addColumn('status', function ($hutang) {
                return $hutang->status == 'lunas' ? '<span class="label label-success">Lunas</span>' : '<span class="label label-danger">Belum Lunas</span>';
            })
            ->addColumn('aksi', function ($hutang) {
                return '
                <div class="btn-group">
                    <button onclick="deleteData(`'. route('hutang.destroy', $hutang->id_hutang) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Logic untuk menampilkan form tambah hutang
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logic untuk menyimpan hutang baru
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $hutang = Hutang::findOrFail($id);
        return response()->json($hutang);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hutang = Hutang::find($id);
        $hutang->delete();

        return response(null, 204);
    }
}