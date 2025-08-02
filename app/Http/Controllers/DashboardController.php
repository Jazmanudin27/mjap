<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $bulanIni = Carbon::now()->format('Y-m');
        $penjualanHariIni = DB::table('penjualan')
            ->whereDate('tanggal', $today)
            ->where('batal', 0)
            ->sum('grand_total');
        $penjualanBulanIni = DB::table('penjualan')
            ->where('tanggal', 'like', "$bulanIni%")
            ->where('batal', 0)
            ->sum('grand_total');
        $jumlahFaktur = DB::table('penjualan')->count();
        $jumlahBatal = DB::table('penjualan')->where('batal', 1)->count();

       $barangTerlaris = DB::table('penjualan_detail as pd')
        ->join('penjualan as p', 'p.no_faktur', '=', 'pd.no_faktur')
        ->join('barang as b', 'b.kode_barang', '=', 'pd.kode_barang')
        ->join('barang_satuan as bs', 'bs.id', '=', 'pd.satuan_id') // join ke satuan
        ->leftJoin('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier')
        ->where('p.batal', 0)
        ->select(
            'pd.kode_barang',
            'b.nama_barang',
            'bs.satuan', // nama satuan
            's.nama_supplier',
            DB::raw('SUM(pd.qty) as total_terjual')
        )
        ->groupBy('pd.kode_barang', 'b.nama_barang', 'bs.satuan', 's.nama_supplier')
        ->orderByDesc('total_terjual')
        ->limit(20)
        ->get();
        return view('panel.dashboard', compact(
            'penjualanHariIni',
            'penjualanBulanIni',
            'jumlahFaktur',
            'jumlahBatal',
            'barangTerlaris'
        ));
    }
}
