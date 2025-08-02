<?php

use App\Http\Controllers\Mobile\SFAController;
use App\Http\Controllers\Mobile\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('loginMobile');
});
Route::get('loginMobile', [LoginController::class, 'loginMobile'])->name('loginMobile');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('mobile.logout');

Route::middleware('auth')->group(function () {

    Route::controller(SFAController::class)->group(function () {
        Route::get('viewDashboardSFAMobile', 'viewDashboardSFAMobile')->name('viewDashboardSFAMobile');
        Route::get('viewPelangganMobile', 'viewPelangganMobile')->name('viewPelangganMobile');
        Route::get('viewDetailPelangganMobile/{id}', 'viewDetailPelangganMobile')->name('viewDetailPelangganMobile');
        Route::get('createPenjualanMobile/{id}', 'createPenjualanMobile')->name('createPenjualanMobile');
        Route::get('createReturMobile/{id}', 'createReturMobile')->name('createReturMobile');
        Route::post('storeReturMobile', 'storeReturMobile')->name('storeReturMobile');
        Route::post('storePenjualanMobile', 'storePenjualanMobile')->name('storePenjualanMobile');
        Route::post('updatePelangganMobile', 'updatePelangganMobile')->name('updatePelangganMobile');
        Route::post('updateFotoLokasiPelanggan', 'updateFotoLokasiPelanggan')->name('updateFotoLokasiPelanggan');
        Route::get('editFotoLokasiMobile/{id}', 'editFotoLokasiMobile')->name('editFotoLokasiMobile');
        Route::get('createPelangganMobile', 'createPelangganMobile')->name('createPelangganMobile');
        Route::get('editPelangganMobile/{id}', 'editPelangganMobile')->name('editPelangganMobile');
        Route::get('detailReturMobile/{no_retur}', 'detailReturMobile')->name('detailReturMobile');
        Route::get('detailPenjualanMobile/{no_faktur}', 'detailPenjualanMobile')->name('detailPenjualanMobile');
        Route::get('profileMobile/{nik}', 'profileMobile')->name('profileMobile');
        Route::post('checkin', 'checkin')->name('checkin');
        Route::post('checkout', 'checkout')->name('checkout');
        Route::get('filterTargetMobile', 'filterTargetMobile')->name('filterTargetMobile');
        Route::get('filterHistory', 'filterHistory')->name('filterHistory');
        Route::post('storePelangganMobile', 'storePelangganMobile')->name('storePelangganMobile');
        Route::get('createPengajuanLimitMobile/{id}', 'createPengajuanLimitMobile')->name('createPengajuanLimitMobile');
        Route::post('storePengajuanLimitMobile', 'storePengajuanLimitMobile')->name('storePengajuanLimitMobile');
        Route::get('createPengajuanFakturMobile/{id}', 'createPengajuanFakturMobile')->name('createPengajuanFakturMobile');
        Route::post('storePengajuanFakturMobile', 'storePengajuanFakturMobile')->name('storePengajuanFakturMobile');
        Route::get('limitKreditMobile', 'limitKreditMobile')->name('limitKreditMobile');
        Route::get('limitFakturMobile', 'limitFakturMobile')->name('limitFakturMobile');
        Route::post('approvePengajuanLimitMobile/{id}', 'approvePengajuanLimitMobile')->name('approvePengajuanLimitMobile');
        Route::post('approvePengajuanFakturMobile/{id}', 'approvePengajuanFakturMobile')->name('approvePengajuanFakturMobile');
        Route::get('getApprovalHistoryFakturMobil/{id}', 'getApprovalHistoryFakturMobil')->name('getApprovalHistoryFakturMobil');
        Route::put('/karyawan/{id}/password', 'updatePassword')->name('karyawan.password.update');
        Route::put('/karyawan/{id}/upload-foto', 'storeFoto')->name('karyawan.upload-foto.store');
        Route::post('/karyawan/{nik}/update-data', 'updateDataKaryawan')->name('karyawan.update-data');
    });
});
