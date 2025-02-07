<?php

namespace App\Http\Controllers;
use App\Models\LaporanPenjualan;

use Illuminate\Http\Request;

class LaporanPenjualanController extends Controller
{
    // Menampilkan daftar laporan penjualan
    public function index()
    {
        $laporan = LaporanPenjualan::orderBy('created_at', 'desc')->get();

        return view('laporan_penjualan.index', compact('laporan'));
    }

    // Menampilkan detail laporan penjualan
    public function show($id)
    {
        $laporan = LaporanPenjualan::findOrFail($id);
        return view('laporan_penjualan.detail', compact('laporan'));
    }

    // Fungsi untuk format tanggal Indonesia
    private function formatTanggalIndonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tanggal = date('d', strtotime($tanggal));
        $bulan = $bulan[date('n', strtotime($tanggal))];
        $tahun = date('Y', strtotime($tanggal));

        return "$tanggal $bulan $tahun";
    }
}
