<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Member;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\LaporanPenjualan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kategori = Kategori::count();
        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();
        $data_penjualan = array();
        $data_profit = array();
        $data_pengeluaran = array();
        $data_pembelian = array();
        $data_produk = [];
        $total_omset = 0;
        $total_profit = 0;

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;
            

            $penjualan_harian = LaporanPenjualan::whereDate('created_at', $tanggal_awal)
                ->selectRaw('nama_produk, SUM(jumlah) as total_terjual, SUM(harga_jual * jumlah) as total_omset, SUM((harga_jual - hpp) * jumlah) as total_profit')
                ->groupBy('nama_produk')
                ->get();

            $profit_olah = 0;
            if($total_pengeluaran != 0) {
                $profit_olah = $penjualan_harian->sum('total_profit') - $total_pengeluaran;
            } else {
                $profit_olah = $penjualan_harian->sum('total_profit');
            }

            $data_penjualan[] += $penjualan_harian->sum('total_omset');
            $data_pembelian[] += $total_pembelian;
            $data_profit[] += $profit_olah;
            $data_pengeluaran[] += $total_pengeluaran;

            if ($tanggal_awal == date('Y-m-d')) {
                foreach ($penjualan_harian as $penjualan) {
                    $data_produk[$penjualan->nama_produk][] = (int) $penjualan->total_terjual;
                    $profit_olah_harian = 0;
                    if($total_pengeluaran != 0) {
                        $profit_olah_harian = $penjualan->total_profit - $total_pengeluaran;
                    } else {
                        $profit_olah_harian = $penjualan->total_profit;
                    }

                    $total_omset += $penjualan->total_omset;
                    $total_profit += $profit_olah_harian;
                }
            }

            

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        return view('admin.dashboard', compact('kategori', 'produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan', 'data_penjualan', 'data_profit', 'data_pengeluaran', 'data_pembelian', 'data_produk', 'total_omset', 'total_profit'));
    }
}