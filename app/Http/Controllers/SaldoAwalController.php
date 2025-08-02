<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class SaldoAwalController extends Controller
{
    public function viewSaldoAwalGS(Request $request)
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier','ASC')->get();
        return view('saldoawal.viewSaldoAwalGS', $data);
    }
    public function viewSaldoAwalBS(Request $request)
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier','ASC')->get();
        return view('saldoawal.viewSaldoAwalBS', $data);
    }

    public function createSaldoAwalGS()
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier','ASC')->get();
        return view('saldoawal.createSaldoAwalGS',$data);
    }

    public function createSaldoAwalBS()
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier','ASC')->get();
        return view('saldoawal.createSaldoAwalBS',$data);
    }

    public function storeSaldoAwalGS(Request $request)
    {
        foreach ($request->items as $item) {
            if (isset($item['qty']) && $item['qty'] > 0) {
                DB::table('saldo_awal_gs')->updateOrInsert(
                    [
                        'kode_barang' => $item['kode_barang'],
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                    ],
                    [
                        'qty' => $item['qty'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'tanggal' => $request->tanggal,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        return back()->with('success', 'Saldo awal berhasil disimpan.');
    }

    public function storeSaldoAwalBS(Request $request)
    {
        foreach ($request->items as $item) {
            if (isset($item['qty']) && $item['qty'] > 0) {
                DB::table('saldo_awal_bs')->updateOrInsert(
                    [
                        'kode_barang' => $item['kode_barang'],
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                    ],
                    [
                        'qty' => $item['qty'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'tanggal' => $request->tanggal,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
        return back()->with('success', 'Saldo awal berhasil disimpan.');
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('barang')->where('kode_barang', $request->id)->first();
        logActivity('Hapus Barang', 'Barang ' . $hapus->nama_barang . ' dihapus');
        $hapus = DB::table('barang')->where('kode_barang', $request->id)->delete();
        if ($hapus) {
            return Redirect('viewBarang')->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect('viewBarang')->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
    public function getBarangBySupplier($kode_supplier)
    {
        $barang = DB::table('barang')
            ->join('barang_satuan', function ($join) {
                $join->on('barang.kode_barang', '=', 'barang_satuan.kode_barang')
                    ->where('barang_satuan.isi', 1);
            })
            ->where('barang.kode_supplier', $kode_supplier)
            ->select('barang.kode_barang', 'barang.nama_barang', 'barang_satuan.satuan')
            ->orderBy('barang.nama_barang')
            ->get();

        return response()->json($barang);
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

}
