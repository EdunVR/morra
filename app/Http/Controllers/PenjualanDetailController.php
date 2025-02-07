<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Piutang;
use Illuminate\Http\Request;
use App\Models\ProdukTipe;
use App\Models\HppProduk;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $produk = Produk::withSum('hppProduk', 'stok')
            ->orderBy('nama_produk')
            ->get();
        $member = Member::orderBy('nama')->get();
        $diskon = Setting::first()->diskon ?? 0;

        // Cek apakah ada transaksi yang sedang berjalan
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Penjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();

            return view('penjualan_detail.index', compact('produk', 'member', 'diskon', 'id_penjualan', 'penjualan', 'memberSelected'));
        } else {
            return redirect()->route('transaksi.baru');
        }
    }

    public function data($id)
    {
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">'. $item->produk['kode_produk'] .'</span';
            $row['nama_produk'] = $item->produk['nama_produk'];
            $row['harga_jual']  = format_uang($item->harga_jual);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'" value="'. $item->jumlah .'">';
            $row['diskon']      = '<span class="diskon">'. $item->diskon .'%</span>';
            $row['subtotal']    = format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_penjualan_detail) .'`, `'. $item->id_produk .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';

            $data[] = $row;

            $total += $item->harga_jual * $item->jumlah - (($item->diskon * $item->jumlah) / 100 * $item->harga_jual);;
            $total_item += $item->jumlah;
        }

        $data[] = [
            'id_produk'   => '',
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah', 'diskon'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::where('id_produk', $request->id_produk)->first();
        if (! $produk) {
            return response()->json('Data gagal disimpan', 400);
        }

        $id_member = $request->id_member;
        $member = Member::find($id_member);
        $tipeCustomer = $member ? $member->id_tipe : null;

        // Ambil diskon dari produk_tipe
        $produkTipe = ProdukTipe::where('id_produk', $produk->id_produk)
            ->where('id_tipe', $tipeCustomer)
            ->first();

        $diskonTipe = $produkTipe ? $produkTipe->diskon : 0;

        $detail = new PenjualanDetail();
        $detail->id_penjualan = $request->id_penjualan;
        $detail->id_produk = $produk->id_produk;
        $detail->harga_jual = $produk->harga_jual;
        $detail->jumlah = 1;
        $detail->diskon = $diskonTipe;
        $detail->subtotal = $produk->harga_jual - ($diskonTipe / 100 * $produk->harga_jual);
        $detail->id_hpp = $request->id_hpp;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = PenjualanDetail::find($id);
        if (!$detail) {
            return response()->json('Detail tidak ditemukan', 404);
        }

        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_jual * $request->jumlah - (($detail->diskon * $request->jumlah) / 100 * $detail->harga_jual);
        $detail->save();
    }

    public function destroy($id)
    {
        $detail = PenjualanDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0, $piutang = 0, $isChecked = "false")
    {
        $bayar   = $total - ($diskon / 100 * $total);
        if ($isChecked== "true" && $piutang > 0) {
            $bayar += $piutang; // Tambahkan piutang ke bayar
        }
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah'),
            'piutang' => format_uang($piutang),
        ];

        session(['isChecked' => $isChecked]);
        return response()->json($data);
    }

    public function getDiscount(Request $request)
    {
        $produk = Produk::find($request->id_produk);
        $member = Member::find($request->id_member);
        $tipeCustomer = $member ? $member->id_tipe : null;

        $produkTipe = ProdukTipe::where('id_produk', $produk->id_produk)
            ->where('id_tipe', $tipeCustomer)
            ->first();

        $diskonTipe = $produkTipe ? $produkTipe->diskon : 0;

        return response()->json(['diskon' => $diskonTipe]);
    }

    public function updatePiutang(Request $request)
    {
        $member = Member::find($request->id_member);
        if (!$member) {
            return response()->json('Member tidak ditemukan', 404);
        }

        $bayar = $request->bayar; // Pastikan ini diambil dari input yang sesuai
        $diterima = $request->diterima; // Pastikan ini diambil dari input yang sesuai
        $isChecked = $request->isChecked;
        $piutang = $request->piutang;

        session(['piutang' => $piutang]);

        if ($diterima == 0) {
            // Jika diterima = 0, tambahkan piutang sebanyak bayar
            $member->piutang += $bayar;
            session(['isBon' => 'true']);
            $member->save();

            Piutang::create([
                'id_member' => $member->id_member,
                'nama' => $member->nama, // Ambil nama dari supplier
                'piutang' => $bayar, // Jumlah hutang yang baru
                'status' => 'belum_lunas', // Status hutang
            ]);
            return response()->json('Piutang ' . $bayar . ' berhasil ditambahkan ke' . $member->nama, 200);
        } elseif ($diterima < $bayar) {
            // Jika diterima < bayar, tambahkan piutang sebanyak kembali
            $kembali = $bayar - $diterima;
            $member->piutang += $kembali;
            session(['isBon' => 'true']);
            $member->save();

            Piutang::create([
                'id_member' => $member->id_member,
                'nama' => $member->nama, // Ambil nama dari supplier
                'piutang' => $kembali, // Jumlah hutang yang baru
                'status' => 'belum_lunas', // Status hutang
            ]);
            return response()->json('Piutang ' . $kembali . ' berhasil ditambahkan ke' . $member->nama, 200);
        } else {
            session(['isBon' => 'false']);
            if($isChecked == 'true') {
                $member->piutang = 0;
                $member->save();

                Piutang::create([
                    'id_member' => $member->id_member,
                    'nama' => $member->nama, // Ambil nama dari supplier
                    'piutang' => '-' .$piutang, // Jumlah hutang yang baru
                    'status' => 'lunas', // Status hutang
                ]);
                return response()->json('LUNAS DENGAN PIUTANG', 200);
            } else {
                $member->save();
                return response()->json('LUNAS', 200);
            }
            
        }
    }

    public function hapusProdukTerpilih(Request $request)
    {
        if ($request->ajax()) {
            $id_penjualan = $request->id_penjualan;

            // Hapus semua produk berdasarkan ID transaksi
            PenjualanDetail::where('id_penjualan', $id_penjualan)->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Request harus AJAX'], 400);
    }

    public function getHPP($id)
    {
        $detail = HppProduk::with('produk')->where('id_produk', $id)->orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($detail) {
                return tanggal_indonesia($detail->created_at, false);
            })
            ->addColumn('harga_jual', function ($detail) {
                return format_uang($detail->produk->harga_jual);
            })
            ->addColumn('hpp', function ($detail) {
                return format_uang($detail->hpp);
            })
            ->addColumn('stok', function ($detail) {
                return $detail->stok;
            })
            ->addColumn('aksi', function ($detail) {
                return '
                <div class="btn-group">
                    <button onclick="pilihProduk('.$detail->id_produk.', \''.$detail->produk->kode.'\', '.$detail->hpp.', '.$detail->stok.', '.$detail->id_hpp.')" class="btn btn-xs btn-success btn-flat"><i class="fa fa-check-circle"></i> Pilih Harga</button>
                </div>
            ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}