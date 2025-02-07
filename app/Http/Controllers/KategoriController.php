<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kategori.index', [
            'kategori' => Kategori::all()
        ]);
    }

    public function data()
    {
    $kategori = Kategori::latest()->get();
    $data = datatables()
        ->of($kategori)
        ->addIndexColumn()
        ->addColumn('aksi', function ($kategori) {
            $updateUrl = route('kategori.update', $kategori->id_kategori);
            $deleteUrl = route('kategori.destroy', $kategori->id_kategori);
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
        $kategori = new Kategori;
        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->save();
        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kategori = Kategori::find($id);
        return response()->json($kategori);
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
        $kategori = Kategori::find($id);
        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->update();
        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Kategori::find($id)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }
}
