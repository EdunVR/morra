<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\SparepartLog;
use Barryvdh\DomPDF\Facade\Pdf;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $spareparts = Sparepart::query();
            
            return DataTables::of($spareparts)
                ->addIndexColumn()
                ->addColumn('harga_formatted', function ($row) {
                    return 'Rp ' . number_format($row->harga, 0, ',', '.');
                })
                ->addColumn('stok_status', function ($row) {
                    if ($row->stok == 0) {
                        return '<span class="label label-danger">Habis</span>';
                    } elseif ($row->isStokMinimum()) {
                        return '<span class="label label-warning">Minimum</span>';
                    } else {
                        return '<span class="label label-success">Tersedia</span>';
                    }
                })
                ->addColumn('barcode', function ($row) {
                    // Generate barcode menggunakan CSS/SVG sederhana
                    return '
                        <div class="text-center">
                            <div class="barcode-simple" style="font-family: \'Libre Barcode 128\', cursive; font-size: 24px; letter-spacing: 2px;">
                                *' . $row->kode_sparepart . '* 
                            </div>
                            <div style="font-size: 10px; margin-top: 2px;">' . $row->kode_sparepart . '</div>
                        </div>
                    ';
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" onclick="editForm(`'.route('sparepart.update', $row->id_sparepart).'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>';
                    $btn .= '<button type="button" onclick="deleteData(`'.route('sparepart.destroy', $row->id_sparepart).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                    $btn .= '<button type="button" onclick="showLogModal(' . $row->id_sparepart . ')" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-history"></i> +Stok/Harga/Log</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['stok_status', 'aksi', 'barcode'])
                ->make(true);
        }

        //showBarcode
        
        return view('sparepart.index');
    }

    public function printBarcode(Request $request)
    {
        $ids = $request->get('ids');
        
        if (!$ids) {
            return redirect()->back()->with('error', 'Pilih sparepart terlebih dahulu');
        }
        
        $spareparts = Sparepart::whereIn('id_sparepart', $ids)->get();
        
        return view('sparepart.print-barcode', compact('spareparts'));
    }

    

    // Method untuk generate kode otomatis
    private function generateKodeSparepart($merk)
    {
        // Ambil 2 karakter pertama dari merk (uppercase)
        $prefix = strtoupper(substr($merk, 0, 2));
        
        // Cari kode terakhir dengan prefix yang sama
        $lastSparepart = Sparepart::where('kode_sparepart', 'like', $prefix . '-%')
            ->orderBy('kode_sparepart', 'desc')
            ->first();
        
        if ($lastSparepart) {
            // Ambil angka terakhir dan increment
            $lastNumber = intval(substr($lastSparepart->kode_sparepart, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format: AB-0001
        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function edit($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return response()->json($sparepart);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama_sparepart' => 'required|string|max:255',
        'merk' => 'required|string|max:100',
        'spesifikasi' => 'nullable|string',
        'harga' => 'required|numeric|min:0',
        'stok' => 'required|integer|min:0',
        'stok_minimum' => 'required|integer|min:0',
        'satuan' => 'required|string|max:50',
        'keterangan' => 'nullable|string'
    ], [
        'nama_sparepart.required' => 'Nama sparepart wajib diisi',
        'merk.required' => 'Merk wajib diisi',
        'harga.required' => 'Harga wajib diisi',
        'harga.numeric' => 'Harga harus berupa angka',
        'harga.min' => 'Harga tidak boleh negatif',
        'stok.required' => 'Stok wajib diisi',
        'stok.integer' => 'Stok harus berupa angka bulat',
        'stok.min' => 'Stok tidak boleh negatif',
        'stok_minimum.required' => 'Stok minimum wajib diisi',
        'stok_minimum.integer' => 'Stok minimum harus berupa angka bulat',
        'stok_minimum.min' => 'Stok minimum tidak boleh negatif',
        'satuan.required' => 'Satuan wajib diisi'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Generate kode otomatis
        $kodeSparepart = $this->generateKodeSparepart($request->merk);
        
        $data = $request->all();
        $data['kode_sparepart'] = $kodeSparepart;
        
        // Pastikan harga disimpan sebagai integer
        $data['harga'] = (int) str_replace('.', '', $data['harga']);
        
        $sparepart = Sparepart::create($data);

        // Buat log untuk stok awal
        SparepartLog::create([
            'id_sparepart' => $sparepart->id_sparepart,
            'id_user' => auth()->id(),
            'tipe_perubahan' => 'stok',
            'nilai_lama' => 0,
            'nilai_baru' => $request->stok,
            'selisih' => $request->stok,
            'keterangan' => 'Stok awal'
        ]);

        // Buat log untuk harga awal
        SparepartLog::create([
            'id_sparepart' => $sparepart->id_sparepart,
            'id_user' => auth()->id(),
            'tipe_perubahan' => 'harga',
            'nilai_lama' => 0,
            'nilai_baru' => $data['harga'],
            'selisih' => $data['harga'],
            'keterangan' => 'Harga awal'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Sparepart berhasil ditambahkan'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Sparepart store error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan sparepart: ' . $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'kode_sparepart' => 'required|unique:spareparts,kode_sparepart,' . $id . ',id_sparepart',
            'nama_sparepart' => 'required|string|max:255',
            'merk' => 'required|string|max:100',
            'spesifikasi' => 'nullable|string',
            // HAPUS: harga dan stok dari sini
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update hanya field yang diizinkan (tidak termasuk stok dan harga)
            $sparepart->update([
                'kode_sparepart' => $request->kode_sparepart,
                'nama_sparepart' => $request->nama_sparepart,
                'merk' => $request->merk,
                'spesifikasi' => $request->spesifikasi,
                'stok_minimum' => $request->stok_minimum,
                'satuan' => $request->satuan,
                'keterangan' => $request->keterangan
            ]);
            
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

    // API untuk pencarian sparepart (digunakan di modal)
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $spareparts = Sparepart::active()
            ->where(function($query) use ($search) {
                $query->where('kode_sparepart', 'like', "%{$search}%")
                    ->orWhere('nama_sparepart', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%");
            })
            ->orderBy('nama_sparepart')
            ->limit(50)
            ->get();
            
        return response()->json($spareparts);
    }

    // API untuk mendapatkan detail sparepart
    public function getDetail($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return response()->json($sparepart);
    }

    // Method untuk menambah stok
    public function tambahStok(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jumlah_tambah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sparepart = Sparepart::findOrFail($id);
            $stokLama = $sparepart->stok;
            $jumlahTambah = $request->jumlah_tambah;
            $stokBaru = $stokLama + $jumlahTambah;

            // Update stok
            $sparepart->update(['stok' => $stokBaru]);

            // Buat log
            SparepartLog::create([
                'id_sparepart' => $id,
                'id_user' => auth()->id(),
                'tipe_perubahan' => 'stok',
                'nilai_lama' => $stokLama,
                'nilai_baru' => $stokBaru,
                'selisih' => $jumlahTambah,
                'keterangan' => $request->keterangan ?: "Penambahan stok manual"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan',
                'stok_baru' => $stokBaru
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk update harga
    public function updateHarga(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'harga_baru' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sparepart = Sparepart::findOrFail($id);
            $hargaLama = $sparepart->harga;
            $hargaBaru = $request->harga_baru;
            $selisih = $hargaBaru - $hargaLama;

            // Update harga
            $sparepart->update(['harga' => $hargaBaru]);

            // Buat log
            SparepartLog::create([
                'id_sparepart' => $id,
                'id_user' => auth()->id(),
                'tipe_perubahan' => 'harga',
                'nilai_lama' => $hargaLama,
                'nilai_baru' => $hargaBaru,
                'selisih' => $selisih,
                'keterangan' => $request->keterangan ?: "Perubahan harga"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Harga berhasil diupdate',
                'harga_baru' => $hargaBaru
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate harga: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk get logs
    public function getLogs($id)
    {
        try {
            $logs = SparepartLog::with('user')
                ->where('id_sparepart', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportLogPdf(Request $request)
    {
        try {
            $sparepartId = $request->get('sparepart_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $tipePerubahan = $request->get('tipe_perubahan');
            
            $query = SparepartLog::with(['sparepart', 'user'])
                ->orderBy('created_at', 'desc');
            
            // Filter berdasarkan sparepart tertentu
            if ($sparepartId) {
                $query->where('id_sparepart', $sparepartId);
            }
            
            // Filter berdasarkan tanggal
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($startDate) {
                $query->where('created_at', '>=', $startDate . ' 00:00:00');
            } elseif ($endDate) {
                $query->where('created_at', '<=', $endDate . ' 23:59:59');
            }
            
            // Filter berdasarkan tipe perubahan
            if ($tipePerubahan && in_array($tipePerubahan, ['stok', 'harga'])) {
                $query->where('tipe_perubahan', $tipePerubahan);
            }
            
            $logs = $query->get();
            
            // Data untuk header laporan
            $reportData = [
                'logs' => $logs,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tipe_perubahan' => $tipePerubahan,
                'sparepart' => $sparepartId ? Sparepart::find($sparepartId) : null,
                'total_records' => $logs->count(),
                'generated_at' => now()->format('d/m/Y H:i:s'),
                'generated_by' => auth()->user()->name
            ];
            
            $pdf = Pdf::loadView('sparepart.export_log_pdf', $reportData);
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'laporan-log-sparepart-' . date('Ymd-His') . '.pdf';
            
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export log PDF error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk get spareparts (untuk filter)
    public function getSparepartsForFilter()
    {
        try {
            $spareparts = Sparepart::select('id_sparepart', 'kode_sparepart', 'nama_sparepart')
                ->orderBy('nama_sparepart')
                ->get();
                
            return response()->json([
                'success' => true,
                'spareparts' => $spareparts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sparepart'
            ]);
        }
    }
}