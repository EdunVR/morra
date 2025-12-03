<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Tipe;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Traits\HasOutletFilter;

class CustomerManagementController extends Controller
{
    use HasOutletFilter;

    /**
     * Display customer management page
     */
    public function index(Request $request)
    {
        // Get user's accessible outlets only
        $outlets = $this->getUserOutlets();
        $tipes = Tipe::all();
        
        return view('admin.crm.pelanggan.index', compact('outlets', 'tipes'));
    }

    /**
     * Get customer data for grid/table view
     */
    public function getData(Request $request)
    {
        $outletFilter = $request->get('outlet_filter', 'all');
        $tipeFilter = $request->get('tipe_filter', 'all');
        $search = $request->get('search', '');

        $query = Member::with(['tipe', 'outlet'])
            ->withTotalPiutang();

        // Apply outlet filter based on user access
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Additional outlet filter from request
        if ($outletFilter !== 'all') {
            $query->where('id_outlet', $outletFilter);
        }

        // Filter tipe
        if ($tipeFilter !== 'all') {
            $query->where('id_tipe', $tipeFilter);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('kode_member', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('nama', 'asc')->get();

        // Transform data for frontend
        $data = $customers->map(function($customer) {
            return [
                'id_member' => $customer->id_member,
                'kode_display' => $customer->getMemberCodeWithPrefix() ?? $customer->kode_member ?? '-',
                'kode_member' => $customer->kode_member,
                'nama' => $customer->nama,
                'telepon' => $customer->telepon,
                'alamat' => $customer->alamat,
                'tipe_nama' => $customer->tipe ? $customer->tipe->nama_tipe : '-',
                'outlet_nama' => $customer->outlet ? $customer->outlet->nama_outlet : '-',
                'total_piutang' => $customer->total_piutang ?? 0,
                'total_piutang_formatted' => 'Rp ' . number_format($customer->total_piutang ?? 0, 0, ',', '.'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new customer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'id_tipe' => 'required|exists:tipe,id_tipe',
            'id_outlet' => 'required|exists:outlets,id_outlet',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->authorizeOutletAccess($request->id_outlet);

        try {
            DB::beginTransaction();

            $data = $request->only(['nama', 'telepon', 'alamat', 'id_tipe', 'id_outlet']);
            
            // Generate kode member - ambil yang terbesar dari semua outlet
            $lastMember = Member::orderBy('kode_member', 'desc')
                ->whereNotNull('kode_member')
                ->first();
            
            if ($lastMember && $lastMember->kode_member) {
                // Ambil angka dari kode_member dan tambah 1
                $lastNumber = intval($lastMember->kode_member);
                $nextNumber = $lastNumber + 1;
            } else {
                // Jika belum ada member, mulai dari 1
                $nextNumber = 1;
            }
            
            $data['kode_member'] = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $member = Member::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan',
                'data' => $member
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating customer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show customer detail
     */
    public function show($id)
    {
        try {
            $member = Member::with(['tipe', 'outlet', 'salesInvoices', 'piutangs'])
                ->withTotalPiutang()
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $member
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'id_tipe' => 'required|exists:tipe,id_tipe',
            'id_outlet' => 'required|exists:outlets,id_outlet',
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

            $member = Member::findOrFail($id);
            
            // Validate outlet access for both old and new outlet
            $this->authorizeOutletAccess($member->id_outlet);
            $this->authorizeOutletAccess($request->id_outlet);
            $member->update($request->only(['nama', 'telepon', 'alamat', 'id_tipe', 'id_outlet']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diupdate',
                'data' => $member
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating customer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $member = Member::findOrFail($id);
            
            // Validate outlet access
            $this->authorizeOutletAccess($member->id_outlet);
            
            // Check if customer has transactions
            if ($member->salesInvoices()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak dapat dihapus karena memiliki transaksi'
                ], 422);
            }

            $member->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting customer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $outletFilter = $request->get('outlet_filter', 'all');
            $tipeFilter = $request->get('tipe_filter', 'all');
            
            return Excel::download(
                new CustomerExport($outletFilter, $tipeFilter), 
                'pelanggan_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Error exporting customers to Excel: ' . $e->getMessage());
            return back()->with('error', 'Gagal export data pelanggan');
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $outletFilter = $request->get('outlet_filter', 'all');
            $tipeFilter = $request->get('tipe_filter', 'all');

            $query = Member::with(['tipe', 'outlet'])->withTotalPiutang();

            if ($outletFilter !== 'all') {
                $query->where('id_outlet', $outletFilter);
            }

            if ($tipeFilter !== 'all') {
                $query->where('id_tipe', $tipeFilter);
            }

            $customers = $query->get();

            $pdf = Pdf::loadView('admin.crm.pelanggan.pdf', compact('customers'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('pelanggan_' . date('Y-m-d_His') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error exporting customers to PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal export PDF pelanggan');
        }
    }

    /**
     * Import customers from Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $import = new \App\Imports\CustomerImport();
            Excel::import($import, $request->file('file'));

            DB::commit();

            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();
            $errors = $import->getErrors();

            if ($errorCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}",
                    'errors' => $errors,
                    'success_count' => $successCount,
                    'error_count' => $errorCount
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$successCount} pelanggan",
                'success_count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error importing customers: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'kode_member');
            $sheet->setCellValue('B1', 'nama');
            $sheet->setCellValue('C1', 'telepon');
            $sheet->setCellValue('D1', 'alamat');
            $sheet->setCellValue('E1', 'tipe_customer');
            $sheet->setCellValue('F1', 'outlet');

            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            // Auto width
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Sample data
            $sheet->setCellValue('A2', '');
            $sheet->setCellValue('B2', 'John Doe');
            $sheet->setCellValue('C2', '08123456789');
            $sheet->setCellValue('D2', 'Jl. Contoh No. 123');
            $sheet->setCellValue('E2', 'Umum');
            $sheet->setCellValue('F2', 'PBU');

            // Add notes
            $sheet->setCellValue('A4', 'CATATAN:');
            $sheet->setCellValue('A5', '1. kode_member boleh dikosongkan (akan di-generate otomatis)');
            $sheet->setCellValue('A6', '2. nama, telepon, tipe_customer, dan outlet wajib diisi');
            $sheet->setCellValue('A7', '3. tipe_customer harus sesuai dengan data yang ada di sistem');
            $sheet->setCellValue('A8', '4. outlet harus sesuai dengan nama outlet yang ada di sistem');
            $sheet->getStyle('A4:A8')->getFont()->setItalic(true)->getColor()->setRGB('666666');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $fileName = 'template_import_pelanggan.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Error downloading template: ' . $e->getMessage());
            return back()->with('error', 'Gagal download template');
        }
    }

    /**
     * Get customer statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $outletFilter = $request->get('outlet_filter', 'all');

            $query = Member::query();

            if ($outletFilter !== 'all') {
                $query->where('id_outlet', $outletFilter);
            }

            $totalCustomers = $query->count();
            $totalPiutang = DB::table('piutang')
                ->where('status', 'belum_lunas')
                ->sum('piutang');

            $customersByTipe = Member::select('tipe.nama_tipe', DB::raw('count(*) as total'))
                ->join('tipe', 'member.id_tipe', '=', 'tipe.id_tipe')
                ->when($outletFilter !== 'all', function($q) use ($outletFilter) {
                    return $q->where('member.id_outlet', $outletFilter);
                })
                ->groupBy('tipe.nama_tipe')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_customers' => $totalCustomers,
                    'total_piutang' => $totalPiutang,
                    'customers_by_tipe' => $customersByTipe
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting customer statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik pelanggan'
            ], 500);
        }
    }
}
