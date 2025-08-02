<?php

use App\Http\Controllers\Mobile\LoginPresensiController;
use App\Http\Controllers\Mobile\PresensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('loginPresensi');
});
Route::get('loginPresensi', [LoginPresensiController::class, 'loginPresensi'])->name('loginPresensi');
Route::post('authPresensi', [LoginPresensiController::class, 'authPresensi'])->name('authPresensi');
Route::get('logoutPresensi', [LoginPresensiController::class, 'logoutPresensi'])->name('logoutPresensi');

Route::middleware('auth')->group(function () {

    Route::controller(PresensiController::class)->group(function () {
        Route::get('viewDashboardPresensi', 'viewDashboardPresensi')->name('viewDashboardPresensi');
        Route::get('scanPresensi', 'scanPresensi')->name('scanPresensi');
        Route::post('storePresensi', 'storePresensi')->name('storePresensi');
        Route::get('riwayatPresensi', 'riwayatPresensi')->name('riwayatPresensi');
        Route::get('suratAbsen', 'suratAbsen')->name('suratAbsen');
        Route::post('storeSuratAbsen', 'storeSuratAbsen')->name('storeSuratAbsen');
        Route::post('approveSuratAbsen', 'approveSuratAbsen')->name('approveSuratAbsen');
    });
});
