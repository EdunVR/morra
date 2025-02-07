<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('supplier.index');
    }

    public function data()
    {
        $supplier = Supplier::latest()->get();
        $data = datatables()
            ->of($supplier)
            ->addIndexColumn()
            ->addColumn('hutang', function ($supplier) {
                return '<span class="label label-danger">'. format_uang($supplier->hutang) .'</span>';
            })
            ->addColumn('aksi', function ($supplier) {
                $updateUrl = route('supplier.update', $supplier->id_supplier);
                $deleteUrl = route('supplier.destroy', $supplier->id_supplier);
                return '
                <div class="btn-group">
                    <button onclick="editForm(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['aksi', 'hutang'])
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
        $supplier = Supplier::create($request->all());
        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);
        return response()->json($supplier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);
        $supplier->nama = $request->nama;
        $supplier->alamat = $request->alamat;
        $supplier->telepon = $request->telepon;
        $supplier->update();
        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supplier::find($id)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }
}
