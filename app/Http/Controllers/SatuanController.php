<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satuan;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('satuan.index', [
            'satuan' => Satuan::all()
        ]);
    }

    public function data()
    {
    $satuan = Satuan::latest()->get();
    $data = datatables()
        ->of($satuan)
        ->addIndexColumn()
        ->addColumn('aksi', function ($satuan) {
            $updateUrl = route('satuan.update', $satuan->id_satuan);
            $deleteUrl = route('satuan.destroy', $satuan->id_satuan);
            return '
                <div class="btn-group">
                    <button onclick="editForm(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
            ';
        })
        ->rawColumns(['aksi'])
        ->make(true);

    // Debugging: Check the raw data
    // dd($data); // Uncomment this line to see the raw JSON response
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
        $satuan = new Satuan;
        $satuan->nama_satuan = $request->nama_satuan;
        $satuan->save();
        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $satuan = Satuan::find($id);
        return response()->json($satuan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $satuan = Satuan::find($id);
        $satuan->nama_satuan = $request->nama_satuan;
        $satuan->update();
        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Satuan::find($id)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }
}
