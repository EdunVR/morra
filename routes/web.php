<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PembelianDetailController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProduksiDetailController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TipeController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\LaporanPenjualanController;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', fn () => redirect()->route('login'));

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        //return Inertia::render('Dashboard');
        return view('home');
    })->name('dashboard');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
    Route::resource('/kategori', KategoriController::class); 

    Route::get('/satuan/data', [SatuanController::class, 'data'])->name('satuan.data');
    Route::resource('/satuan', SatuanController::class); 

    Route::get('/tipe/data', [TipeController::class, 'data'])->name('tipe.data');
    Route::resource('/tipe', TipeController::class);

    Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::resource('/supplier', SupplierController::class);

    Route::get('/bahan/data', [BahanController::class, 'data'])->name('bahan.data');
    Route::get('/bahan/{id}', [BahanController::class, 'show'])->name('bahan.show');
    Route::delete('bahan/delete-selected', [BahanController::class, 'deleteSelected'])->name('bahan.delete_selected');
    Route::get('/bahan/{id}/edit', [BahanController::class, 'edit'])->name('bahan.edit');
    Route::resource('/bahan', BahanController::class);

    Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
    Route::get('/pembelian/{id}/create', [PembelianController::class, 'create'])->name('pembelian.create');
    Route::resource('/pembelian', PembelianController::class)
        ->except(['create']);

    Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
    Route::get('/pembelian_detail/{id}', [PembelianDetailController::class, 'getHargaBeli'])->name('getHargaBeli');
    Route::post('/pembelian_detail/simpan-harga-bahan', [PembelianDetailController::class, 'simpanHargaBahan'])->name('simpanHargaBahan');
    Route::get('/pembelian_detail/loadform/{diskon}/{total}/{isChecked}/{isBayarHutang}/{hutang}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
    Route::post('/pembelian_detail/update-hutang', [PembelianDetailController::class, 'updateHutang'])->name('pembelian_detail.updateHutang');
    Route::resource('/pembelian_detail', PembelianDetailController::class)
        ->except('create', 'show', 'edit');

    Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
    Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
    Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
    Route::resource('/produk', ProdukController::class);

    Route::get('/produksi/data', [ProduksiController::class, 'data'])->name('produksi.data');
    Route::get('/produksi/create', [ProduksiController::class, 'create'])->name('produksi.create');
    Route::resource('/produksi', ProduksiController::class)->except(['create']);
    Route::get('/produksi_detail/{id}/data', [ProduksiDetailController::class, 'data'])->name('produksi_detail.data');
    Route::resource('/produksi_detail', ProduksiDetailController::class)->except(['create', 'show', 'edit']);

    Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
    Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
    Route::resource('/member', MemberController::class);

    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');

        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');

        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan/{isChecked}', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar/{isChecked}', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');

        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}/{piutang}/{isChecked}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::post('/transaksi/update-piutang', [PenjualanDetailController::class, 'updatePiutang'])->name('transaksi.updatePiutang');
        Route::get('/getDiscount', [PenjualanDetailController::class, 'getDiscount'])->name('getDiscount');
        Route::post('/hapus-produk-terpilih', [PenjualanDetailController::class, 'hapusProdukTerpilih'])->name('hapus.produk');
        Route::get('/transaksi/{id}', [PenjualanDetailController::class, 'getHPP'])->name('getHPP');
        Route::resource('/transaksi', PenjualanDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}/{totalPenjualan}/{totalPembelian}/{totalPengeluaran}/{totalPendapatan}/{totalHutang}/{totalPiutang}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

        // Route::get('/laporan-hutang-piutang', [LaporanHutangPiutangController::class, 'index'])->name('laporan_hutang_piutang.index');
        // Route::get('/laporan-hutang-piutang/data/{awal}/{akhir}', [LaporanHutangPiutangController::class, 'data'])->name('laporan_hutang_piutang.data');
        // Route::get('/laporan-hutang-piutang/pdf/{awal}/{akhir}/{totalHutang}/{totalPiutang}', [LaporanHutangPiutangController::class, 'exportPDF'])->name('laporan_hutang_piutang.export_pdf');

        Route::get('/hutang', [HutangController::class, 'index'])->name('hutang.index');
        Route::get('/hutang/data', [HutangController::class, 'data'])->name('hutang.data');
        Route::delete('/hutang/{id}', [HutangController::class, 'destroy'])->name('hutang.destroy');

        Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
        Route::get('/piutang/data', [PiutangController::class, 'data'])->name('piutang.data');
        Route::delete('/piutang/{id}', [PiutangController::class, 'destroy'])->name('piutang.destroy');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route untuk menampilkan halaman tipe
Route::get('/tipe', [TipeController::class, 'index'])->name('tipe.index');
Route::post('/tipe', [TipeController::class, 'store'])->name('tipe.store');
Route::get('/tipe/{id}/edit', [TipeController::class, 'edit'])->name('tipe.edit');
Route::put('/tipe/{id}', [TipeController::class, 'update'])->name('tipe.update');
Route::delete('/tipe/{id}', [TipeController::class, 'destroy'])->name('tipe.destroy');
Route::resource('tipe', TipeController::class);

Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan_penjualan.index');
Route::get('/laporan-penjualan/{id}', [LaporanPenjualanController::class, 'show'])->name('laporan_penjualan.detail');
    
});



