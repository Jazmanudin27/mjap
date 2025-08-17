<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\MutasiBarangKeluarController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KasBankController;
use App\Http\Controllers\KasKecilController;
use App\Http\Controllers\LaporanGudangController;
use App\Http\Controllers\LaporanPembelianController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\Mobile\SFAController;
use App\Http\Controllers\MutasiBarangMasukController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengajuanLimitFakturController;
use App\Http\Controllers\PengajuanLimitKreditController;
use App\Http\Controllers\POController;
use App\Http\Controllers\ReturPembelianController;
use App\Http\Controllers\ReturPenjualanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaldoAwalController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TargetSalesController;
use App\Http\Controllers\TransferGiroController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'auth_login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'useradmin'], function () {

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity/logs');
    Route::get('showLogs', [ActivityLogController::class, 'showLogs']);
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('listRole', [RoleController::class, 'listRole']);
    Route::get('tambahRole', [RoleController::class, 'tambahRole']);
    Route::post('storeRole', [RoleController::class, 'storeRole']);
    Route::get('editRole/{id}', [RoleController::class, 'editRole']);
    Route::post('updateRole/{id}', [RoleController::class, 'updateRole']);
    Route::get('deleteRole/{id}', [RoleController::class, 'deleteRole']);

    Route::get('/karyawan/toggle-status/{id}', [KaryawanController::class, 'toggleStatus'])->name('karyawan.toggleStatus');
    Route::get('detailKaryawan', [KaryawanController::class, 'detailKaryawan']);
    Route::post('storeJabatan', [KaryawanController::class, 'storeJabatan']);
    Route::resource('users', UsersController::class);
    Route::get('detailUsers', [UsersController::class, 'detailUsers']);
    Route::get('/users/toggle-status/{user}', [UsersController::class, 'toggleStatus'])->name('users.toggleStatus');

    Route::controller(KaryawanController::class)->group(function () {
        Route::get('viewKaryawan', 'index')->name('viewKaryawan');
        Route::get('tambahKaryawan', 'create')->name('tambahKaryawan');
        Route::get('editKaryawan/{id}', 'edit')->name('editKaryawan');
        Route::get('deleteKaryawan/{id}', 'delete')->name('deleteKaryawan');
        Route::post('storeKaryawan', 'store')->name('storeKaryawan');
        Route::put('updateKaryawan/{id}', 'update')->name('updateKaryawan');
        Route::get('detailKaryawan/{id}', 'detail')->name('detailKaryawan');
    });

    Route::controller(BarangController::class)->group(function () {
        Route::get('viewBarang', 'index')->name('viewBarang');
        Route::get('tambahBarang', 'create')->name('tambahBarang');
        Route::get('deleteBarang/{id}', 'delete')->name('deleteBarang');
        Route::post('storeBarang', 'store')->name('storeBarang');
        Route::post('updateBarang', 'update')->name('updateBarang');
        Route::get('detailBarang/{id}', 'detail')->name('detailBarang');
        Route::post('hapusSatuanHarga/{id}', 'hapusSatuanHarga')->name('hapusSatuanHarga');
        Route::post('storeBarangSatuan', 'storeBarangSatuan')->name('storeBarangSatuan');
        Route::post('storeDiskonStarla', 'storeDiskonStarla')->name('storeDiskonStarla');
        Route::post('deleteDiskonStrata/{id}', 'deleteDiskonStrata')->name('deleteDiskonStrata');
        Route::post('cetakLaporanBarang', 'cetakLaporanBarang')->name('cetakLaporanBarang');
        Route::get('hargaBarang', 'hargaBarang')->name('hargaBarang');
        Route::post('updateHargaBarang', 'updateHargaBarang')->name('updateHargaBarang');
    });

    Route::controller(DiskonController::class)->group(function () {
        Route::get('diskonBarang', 'diskonBarang')->name('diskonBarang');
        Route::get('diskonSupplier', 'diskonSupplier')->name('diskonSupplier');
        Route::post('storeDiskon', 'storeDiskon')->name('storeDiskon');
        Route::post('updateDiskon/{id}', 'updateDiskon')->name('updateDiskon');
        Route::get('deleteDiskon/{id}', 'deleteDiskon')->name('deleteDiskon');
    });
    Route::controller(PelangganController::class)->group(function () {
        Route::get('viewPelanggan', 'index')->name('viewPelanggan');
        Route::get('tambahPelanggan', 'create')->name('tambahPelanggan');
        Route::get('mapsPelanggan', 'mapsPelanggan')->name('mapsPelanggan');
        Route::get('deletePelanggan/{id}', 'delete')->name('deletePelanggan');
        Route::post('storePelanggan', 'store')->name('storePelanggan');
        Route::post('updatePelanggan', 'update')->name('updatePelanggan');
        Route::get('detailPelanggan/{id}', 'detail')->name('detailPelanggan');
        Route::get('toggleStatusPelanggan/{id}', 'toggleStatusPelanggan')->name('toggleStatusPelanggan');
        Route::post('cetakLaporanPelanggan', 'cetakLaporanPelanggan')->name('cetakLaporanPelanggan');
    });

    Route::controller(SupplierController::class)->group(function () {
        Route::get('viewSupplier', 'index')->name('viewSupplier');
        Route::get('tambahSupplier', 'create')->name('tambahSupplier');
        Route::get('deleteSupplier/{id}', 'delete')->name('deleteSupplier');
        Route::post('storeSupplier', 'store')->name('storeSupplier');
        Route::post('updateSupplier', 'update')->name('updateSupplier');
        Route::get('detailSupplier/{id}', 'detail')->name('detailSupplier');
        Route::get('toggleStatusSupplier/{id}', 'toggleStatusSupplier')->name('toggleStatusSupplier');
    });

    Route::controller(PembelianController::class)->group(function () {
        Route::get('viewPembelian', 'index')->name('viewPembelian');
        Route::get('tambahPembelian', 'create')->name('tambahPembelian');
        Route::get('getSupplier', 'getSupplier')->name('getSupplier');
        Route::get('getBarangPembelian/{id}', 'getBarangPembelian')->name('getBarangPembelian');
        Route::get('getSatuanBarang/{id}', 'getSatuanBarang')->name('getSatuanBarang');
        Route::get('editPembelian/{id}', 'edit')->name('editPembelian');
        Route::get('deletePembelian/{id}', 'delete')->name('deletePembelian');
        Route::post('storePembelian', 'store')->name('storePembelian');
        Route::post('updatePembelian', 'update')->name('updatePembelian');
        Route::get('detailPembelian/{id}', 'detail')->name('detailPembelian');
        Route::get('deletePembayaranPembelian/{id}', 'deletePembayaranPembelian')->name('deletePembayaranPembelian');
        Route::post('storePembayaranPembelian', 'storePembayaranPembelian')->name('storePembayaranPembelian');
        Route::post('updatePembayaranPembelian/{id}', 'updatePembayaranPembelian')->name('updatePembayaranPembelian');
        Route::get('getPOBySupplier/{id}', 'getPOBySupplier')->name('getPOBySupplier');
        Route::get('getDetailPO/{id}', 'getDetailPO')->name('getDetailPO');
    });

    Route::controller(POController::class)->group(function () {
        Route::get('viewPO', 'index')->name('viewPO');
        Route::get('tambahPO', 'create')->name('tambahPO');
        Route::get('getBarangPO/{id}', 'getBarangPO')->name('getBarangPO');
        Route::get('editPO/{id}', 'edit')->name('editPO');
        Route::get('deletePO/{id}', 'delete')->name('deletePO');
        Route::post('storePO', 'store')->name('storePO');
        Route::post('updatePO', 'update')->name('updatePO');
        Route::get('detailPO/{id}', 'detail')->name('detailPO');
    });

    Route::controller(PenjualanController::class)->group(function () {
        Route::get('viewPenjualan', 'index')->name('viewPenjualan');
        Route::get('tambahPenjualan', 'create')->name('tambahPenjualan');
        Route::get('editPenjualan/{id}', 'edit')->name('editPenjualan');
        Route::get('deletePenjualan/{id}', 'delete')->name('deletePenjualan');
        Route::post('storePenjualan', 'store')->name('storePenjualan');
        Route::post('updatePenjualan', 'update')->name('updatePenjualan');
        Route::post('showPenjualan', 'show')->name('showPenjualan');
        Route::get('detailPenjualan/{id}', 'detail')->name('detailPenjualan');
        Route::get('deletePembayaranPenjualan/{id}', 'deletePembayaranPenjualan')->name('deletePembayaranPenjualan');
        Route::get('deletePembayaranPenjualanTransfer/{id}', 'deletePembayaranPenjualanTransfer')->name('deletePembayaranPenjualanTransfer');
        Route::post('storePembayaranPenjualan', 'storePembayaranPenjualan')->name('storePembayaranPenjualan');
        Route::post('updatePembayaranPenjualan/{id}', 'updatePembayaranPenjualan')->name('updatePembayaranPenjualan');
        Route::post('batalFaktur/{id}', 'batalFaktur')->name('batalFaktur');
        Route::get('getPelanggan', 'getPelanggan')->name('getPelanggan');
        Route::get('getBarang', 'getBarang')->name('getBarang');
        Route::get('cekDiskon/{kode_barang}/{jumlah}', 'cekDiskon')->name('cekDiskon');
        Route::get('getDiskonStrataSemua/{kode_barang}/{jumlah}/{tipe}', 'getDiskonStrataSemua')->name('getDiskonStrataSemua');
        Route::get('getDiskonStrataSupplier/{kode_supplier}/{jumlah}/{tipe}', 'getDiskonStrataSupplier')->name('getDiskonStrataSupplier');
        Route::get('getFakturByWilayah/{id}', 'getFakturByWilayah')->name('getFakturByWilayah');
        Route::post('deleteKirimanSales/{id}', 'deleteKirimanSales')->name('deleteKirimanSales');
        Route::get('cetakFaktur1/{id}', 'cetakFaktur1')->name('cetakFaktur1');
        Route::get('viewKirimanSales', 'viewKirimanSales')->name('viewKirimanSales');
        Route::get('createKirimanSales', 'createKirimanSales')->name('createKirimanSales');
        Route::post('storeKirimanSales', 'storeKirimanSales')->name('storeKirimanSales');
        Route::get('cetakKirimanSales', 'cetakKirimanSales')->name('cetakKirimanSales');
        Route::get('cetakKirimanGudang', 'cetakKirimanGudang')->name('cetakKirimanGudang');
        Route::get('trackingSales', 'trackingSales')->name('trackingSales');
        Route::get('trackingSales', 'trackingSales')->name('trackingSales');
        Route::get('trackingSales', 'trackingSales')->name('trackingSales');
        Route::post('deleteDetailPenjualan/{id}', 'deleteDetailPenjualan')->name('deleteDetailPenjualan');
        Route::post('deleteGroupKirimanSales', 'deleteGroupKirimanSales')->name('deleteGroupKirimanSales');
    });

    Route::get('/getKonversiSatuan/{kode_barang}', function ($kode_barang) {
        return response()->json(getKonversiSatuan($kode_barang));
    });

    Route::get('/getDiskonStrataSemuaGlobal', function () {
        $diskon = DB::table('diskon_strata')
            ->whereNull('kode_barang')
            ->get();
        return response()->json($diskon);
    });

    Route::controller(ReturPembelianController::class)->group(function () {
        Route::get('viewReturPembelian', 'index')->name('viewReturPembelian');
        Route::get('tambahReturPembelian', 'create')->name('tambahReturPembelian');
        Route::get('editReturPembelian/{id}', 'edit')->name('editReturPembelian');
        Route::get('deleteReturPembelian/{id}', 'delete')->name('deleteReturPembelian');
        Route::post('storeReturPembelian', 'store')->name('storeReturPembelian');
        Route::post('updateReturPembelian/{id}', 'update')->name('updateReturPembelian');
        Route::get('detailReturPembelian/{id}', 'detail')->name('detailReturPembelian');
        Route::get('getFakturBySupplier/{id}', 'getFakturBySupplier')->name('getFakturBySupplier');
        Route::get('getDetailFakturPembelian/{id}', 'getDetailFakturPembelian')->name('getDetailFakturPembelian');
    });

    Route::controller(ReturPenjualanController::class)->group(function () {
        Route::get('viewReturPenjualan', 'index')->name('viewReturPenjualan');
        Route::get('tambahReturPenjualan', 'create')->name('tambahReturPenjualan');
        Route::get('editReturPenjualan/{id}', 'edit')->name('editReturPenjualan');
        Route::get('deleteReturPenjualan/{id}', 'delete')->name('deleteReturPenjualan');
        Route::post('storeReturPenjualan', 'store')->name('storeReturPenjualan');
        Route::post('updateReturPenjualan/{id}', 'update')->name('updateReturPenjualan');
        Route::get('detailReturPenjualan/{id}', 'detail')->name('detailReturPenjualan');
        Route::get('getFakturByPelanggan/{id}', 'getFakturByPelanggan')->name('getFakturByPelanggan');
        Route::get('getDetailFakturPenjualan/{id}', 'getDetailFakturPenjualan')->name('getDetailFakturPenjualan');
    });

    Route::controller(MutasiBarangMasukController::class)->group(function () {
        Route::get('viewMutasiBarangMasuk', 'index')->name('viewMutasiBarangMasuk');
        Route::get('tambahMutasiBarangMasuk', 'create')->name('tambahMutasiBarangMasuk');
        Route::get('editMutasiBarangMasuk/{id}', 'edit')->name('editMutasiBarangMasuk');
        Route::get('deleteMutasiBarangMasuk/{id}', 'delete')->name('deleteMutasiBarangMasuk');
        Route::get('detailMutasiBarangMasuk/{id}', 'detail')->name('detailMutasiBarangMasuk');
        Route::post('storeMutasiBarangMasuk', 'store')->name('storeMutasiBarangMasuk');
        Route::post('updateMutasiBarangMasuk/{id}', 'update')->name('updateMutasiBarangMasuk');
        Route::post('storeTerimaBarang', 'storeTerimaBarang')->name('storeTerimaBarang');
    });

    Route::controller(MutasiBarangKeluarController::class)->group(function () {
        Route::get('viewMutasiBarangKeluar', 'index')->name('viewMutasiBarangKeluar');
        Route::get('tambahMutasiBarangKeluar', 'create')->name('tambahMutasiBarangKeluar');
        Route::get('editMutasiBarangKeluar/{id}', 'edit')->name('editMutasiBarangKeluar');
        Route::get('deleteMutasiBarangKeluar/{id}', 'delete')->name('deleteMutasiBarangKeluar');
        Route::get('detailMutasiBarangKeluar/{id}', 'detail')->name('detailMutasiBarangKeluar');
        Route::post('storeMutasiBarangKeluar', 'store')->name('storeMutasiBarangKeluar');
        Route::post('storeKirimBarang', 'storeKirimBarang')->name('storeKirimBarang');
        Route::post('updateMutasiBarangKeluar/{id}', 'update')->name('updateMutasiBarangKeluar');
    });

    Route::controller(LaporanPenjualanController::class)->group(function () {
        Route::get('laporanPenjualan', 'laporanPenjualan')->name('laporanPenjualan');
        Route::post('cetakLaporanPenjualan', 'cetakLaporanPenjualan')->name('cetakLaporanPenjualan');
        Route::post('cetakLaporanPenjualanHarian', 'cetakLaporanPenjualanHarian')->name('cetakLaporanPenjualanHarian');
        Route::post('cetakRekapTagihan', 'cetakRekapTagihan')->name('cetakRekapTagihan');
        Route::post('cetakLaporanRekapPerPelanggan', 'cetakLaporanRekapPerPelanggan')->name('cetakLaporanRekapPerPelanggan');
        Route::post('cetakLaporanReturPenjualan', 'cetakLaporanReturPenjualan')->name('cetakLaporanReturPenjualan');
        Route::post('cetakKartuPiutang', 'cetakKartuPiutang')->name('cetakKartuPiutang');
        Route::post('cetakAnalisaUmurPiutang', 'cetakAnalisaUmurPiutang')->name('cetakAnalisaUmurPiutang');
        Route::post('cetakTargetSales', 'cetakTargetSales')->name('cetakTargetSales');
    });

    Route::controller(KasKecilController::class)->group(function () {
        Route::get('viewKasKecil', 'index')->name('viewKasKecil');
        Route::get('tambahKasKecil', 'create')->name('tambahKasKecil');
        Route::post('storeKasKecil', 'store')->name('storeKasKecil');
        Route::post('updateKasKecil', 'update')->name('updateKasKecil');
        Route::get('deleteKasKecil/{id}', 'delete')->name('deleteKasKecil');
    });

    Route::controller(KasBankController::class)->group(function () {
        Route::get('viewKasBank', 'index')->name('viewKasBank');
        Route::get('tambahKasBank', 'create')->name('tambahKasBank');
        Route::post('storeKasBank', 'store')->name('storeKasBank');
        Route::post('updateKasBank', 'update')->name('updateKasBank');
        Route::get('deleteKasBank/{id}', 'delete')->name('deleteKasBank');
    });

    Route::controller(SetoranController::class)->group(function () {
        Route::get('viewSetoranPenjualan', 'viewSetoranPenjualan')->name('viewSetoranPenjualan');
        Route::get('tambahSetoranPenjualan', 'tambahSetoranPenjualan')->name('tambahSetoranPenjualan');
        Route::get('getSetoranPenjualan', 'getSetoranPenjualan')->name('getSetoranPenjualan');
        Route::post('storeSetoranPenjualan', 'storeSetoranPenjualan')->name('storeSetoranPenjualan');
        Route::post('updateSetoranPenjualan', 'updateSetoranPenjualan')->name('updateSetoranPenjualan');
        Route::delete('deleteSetoranPenjualan/{id}', 'deleteSetoranPenjualan')->name('deleteSetoranPenjualan');
    });

    Route::controller(TransferGiroController::class)->group(function () {
        Route::get('viewGiro', 'viewGiro')->name('viewGiro');
        Route::get('viewTransfer', 'viewTransfer')->name('viewTransfer');
        Route::patch('verifikasiPembayaran/{kode}', 'verifikasiPembayaran')->name('verifikasiPembayaran');
    });

    Route::controller(SaldoAwalController::class)->group(function () {
        Route::get('viewSaldoAwalGS', 'viewSaldoAwalGS')->name('viewSaldoAwalGS');
        Route::get('viewSaldoAwalBS', 'viewSaldoAwalBS')->name('viewSaldoAwalBS');
        Route::get('createSaldoAwalGS', 'createSaldoAwalGS')->name('createSaldoAwalGS');
        Route::get('createSaldoAwalBS', 'createSaldoAwalBS')->name('createSaldoAwalBS');
        Route::post('storeSaldoAwalGS', 'storeSaldoAwalGS')->name('storeSaldoAwalGS');
        Route::post('storeSaldoAwalBS', 'storeSaldoAwalBS')->name('storeSaldoAwalBS');
        Route::get('getBarangBSBySupplier/{id}', 'getBarangBSBySupplier')->name('getBarangBSBySupplier');
        Route::get('getBarangGSBySupplier/{id}', 'getBarangGSBySupplier')->name('getBarangGSBySupplier');
        Route::post('storeSaldoAwalGS', 'storeSaldoAwalGS')->name('storeSaldoAwalGS');
    });

    Route::controller(LaporanGudangController::class)->group(function () {
        Route::get('laporanGudang', 'laporanGudang')->name('laporanGudang');
        Route::post('cetakLaporanPersediaanGS', 'cetakLaporanPersediaanGS')->name('cetakLaporanPersediaanGS');
        Route::post('cetakLaporanPersediaanBS', 'cetakLaporanPersediaanBS')->name('cetakLaporanPersediaanBS');
        Route::post('cetakKartuStok', 'cetakKartuStok')->name('cetakKartuStok');
        Route::post('cetakLaporanPersediaan', 'cetakLaporanPersediaan')->name('cetakLaporanPersediaan');
        Route::post('cetakLaporanMutasiBarang', 'cetakLaporanMutasiBarang')->name('cetakLaporanMutasiBarang');
    });

    Route::controller(LaporanPembelianController::class)->group(function () {
        Route::get('laporanPembelian', 'laporanPembelian')->name('laporanPembelian');
        Route::post('cetakLaporanSemuaPembelian', 'cetakLaporanSemuaPembelian')->name('cetakLaporanSemuaPembelian');
        Route::post('cetakKartuHutang', 'cetakKartuHutang')->name('cetakKartuHutang');
        Route::post('cetakAnalisaUmurHutang', 'cetakAnalisaUmurHutang')->name('cetakAnalisaUmurHutang');
        Route::post('cetakRekapPerSupplier', 'cetakRekapPerSupplier')->name('cetakRekapPerSupplier');
        Route::post('cetakLaporanReturPembelian', 'cetakLaporanReturPembelian')->name('cetakLaporanReturPembelian');
    });

    Route::controller(TargetSalesController::class)->group(function () {
        Route::get('viewTargetSales', 'index')->name('viewTargetSales');
        Route::get('deleteTargetSales/{id}', 'delete')->name('deleteTargetSales');
        Route::post('storeTargetSales', 'store')->name('storeTargetSales');
        Route::post('updateTargetSales', 'update')->name('updateTargetSales');
    });

    Route::controller(PengajuanLimitKreditController::class)->group(function () {
        Route::get('viewPengajuanLimit', 'viewPengajuanLimit')->name('viewPengajuanLimit');
        Route::get('tambahPengajuanLimit', 'tambahPengajuanLimit')->name('tambahPengajuanLimit');
        Route::get('deletePengajuanLimit/{id}', 'deletePengajuanLimit')->name('deletePengajuanLimit');
        Route::post('storePengajuanLimit', 'storePengajuanLimit')->name('storePengajuanLimit');
        Route::post('updatePengajuanLimit', 'updatePengajuanLimit')->name('updatePengajuanLimit');
        Route::post('approvePengajuanLimit/{id}', 'approvePengajuanLimit')->name('approvePengajuanLimit');
        Route::get('detailPengajuanLimit/{id}', 'detailPengajuanLimit')->name('detailPengajuanLimit');
        Route::get('getLimitSupplier/{id}', 'getLimitSupplier')->name('getLimitSupplier');
        Route::post('simpanLimitSupplier', 'simpanLimitSupplier')->name('simpanLimitSupplier');
        Route::post('hapusLimitSupplier', 'hapusLimitSupplier')->name('hapusLimitSupplier');
        Route::get('getApprovalHistory/{id}', 'getApprovalHistory')->name('getApprovalHistory');
        Route::get('getLimitPelangganSupplier/{id}', 'getLimitPelangganSupplier')->name('getLimitPelangganSupplier');
    });

    Route::controller(PengajuanLimitFakturController::class)->group(function () {
        Route::get('viewPengajuanFaktur', 'viewPengajuanFaktur')->name('viewPengajuanFaktur');
        Route::get('tambahPengajuanFaktur', 'tambahPengajuanFaktur')->name('tambahPengajuanFaktur');
        Route::get('deletePengajuanFaktur/{id}', 'deletePengajuanFaktur')->name('deletePengajuanFaktur');
        Route::post('storePengajuanFaktur', 'storePengajuanFaktur')->name('storePengajuanFaktur');
        Route::post('updatePengajuanFaktur', 'updatePengajuanFaktur')->name('updatePengajuanFaktur');
        Route::post('approvePengajuanFaktur/{id}', 'approvePengajuanFaktur')->name('approvePengajuanFaktur');
        Route::get('detailPengajuanFaktur/{id}', 'detailPengajuanFaktur')->name('detailPengajuanFaktur');
        Route::get('getApprovalHistoryFaktur/{id}', 'getApprovalHistoryFaktur')->name('getApprovalHistoryFaktur');
    });

});
