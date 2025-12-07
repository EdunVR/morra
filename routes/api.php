<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProspekController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Attendance API for Fingerprint Integration
Route::prefix('attendance')->group(function () {
    Route::post('/check-in', [App\Http\Controllers\Api\AttendanceController::class, 'checkIn']);
    Route::post('/check-out', [App\Http\Controllers\Api\AttendanceController::class, 'checkOut']);
    Route::post('/store', [App\Http\Controllers\Api\AttendanceController::class, 'store']);
    Route::get('/employee/{fingerprint_id}', [App\Http\Controllers\Api\AttendanceController::class, 'getEmployeeByFingerprint']);
    Route::get('/today/{employee_id}', [App\Http\Controllers\Api\AttendanceController::class, 'getTodayAttendance']);
});

// Legacy routes (for backward compatibility)
Route::post('/attendance', [AttendanceController::class, 'storeApi']);
Route::get('/available-fingerprint-id', [FingerprintController::class, 'getAvailableId']);
Route::get('/employee/{fingerprint_id}', [FingerprintController::class, 'getEmployeeByFingerprintId']);

Route::group(['prefix' => 'api'], function() {
    Route::get('/wilayah/kabupaten/{provinsi_id}', [WilayahController::class, 'getKabupaten'])->name('api.wilayah.kabupaten');
    Route::get('/wilayah/kecamatan/{kabupaten_id}', [WilayahController::class, 'getKecamatan'])->name('api.wilayah.kecamatan');
    Route::get('/wilayah/desa/{kecamatan_id}', [WilayahController::class, 'getDesa'])->name('api.wilayah.desa');
    
    // Tambahkan endpoint untuk produk
    Route::get('/products', [ProdukController::class, 'apiIndex']);
    Route::get('/categories', [ProdukController::class, 'apiCategories']);
});

Route::get('/produk/search', [ProdukController::class, 'search']);
Route::get('/produk/{id}/components', [ProdukController::class, 'getComponents']);
Route::get('/categories', [ProdukController::class, 'apiCategories']);
Route::get('/products/{id}', [ProdukController::class, 'apiShow']);
Route::get('/prospek/locations', [ProspekController::class, 'getLocations']);

Route::get('/investor/{investor}/accounts', function($investorId) {
    $accounts = App\Models\InvestorAccount::where('investor_id', $investorId)
        ->where('status', 'active')
        ->get()
        ->map(function($account) {
            return [
                'id' => $account->id,
                'account_number' => $account->account_number,
                'bank_name' => $account->bank_name,
                'total_investment' => $account->total_investment,
                'status' => $account->status
            ];
        });
    
    return response()->json($accounts);
});

Route::get('/investors/search', function(Request $request) {
    $query = $request->q;
    $investors = App\Models\Investor::where('status', 'active') // Hanya ambil yang aktif
        ->where('name', 'like', "%{$query}%")
        ->paginate(10);
    
    return response()->json($investors);
});