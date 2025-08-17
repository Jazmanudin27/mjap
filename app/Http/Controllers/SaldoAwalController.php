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
        $data['suppliers'] = DB::table('supplier')
            ->orderBy('nama_supplier', 'ASC')
            ->get();

        // Ambil input filter
        $namaBarang = $request->nama_barang;
        $kodeBarang = $request->kode_barang;
        $supplier = $request->supplier;
        $status = $request->status;

        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        $barangQuery = DB::table('barang')
            ->join('barang_satuan', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->leftJoin('saldo_awal_gs', function ($join) use ($bulan, $tahun) {
                $join->on('barang.kode_barang', '=', 'saldo_awal_gs.kode_barang')
                    ->where('saldo_awal_gs.bulan', '=', $bulan)
                    ->where('saldo_awal_gs.tahun', '=', $tahun);
            })
            ->select(
                'barang.kode_item',
                'barang.kode_barang',
                'barang.nama_barang',
                'barang.kode_supplier',
                'barang.status',
                'barang_satuan.satuan',
                'barang_satuan.isi',
                DB::raw('COALESCE(saldo_awal_gs.qty, 0) as qty_saldo_awal')
            );

        if (!empty($namaBarang)) {
            $barangQuery->where('barang.nama_barang', 'like', "%$namaBarang%");
        }
        if (!empty($kodeBarang)) {
            $barangQuery->where('barang.kode_item', 'like', "%$kodeBarang%");
        }
        if (!empty($supplier)) {
            $barangQuery->where('barang.kode_supplier', $supplier);
        }
        if ($status !== null && $status !== '') {
            $barangQuery->where('barang.status', $status);
        }

        // Urutan data
        $barangList = $barangQuery
            ->orderByDesc('barang_satuan.isi')
            ->orderBy('barang.kode_barang')
            ->get();

        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;

        $data['barangList'] = $barangList
            ->groupBy('kode_barang')
            ->map(function ($items) {
                return collect($items)
                    ->sortByDesc('isi')
                    ->values();
            });

        return view('saldoawal.viewSaldoAwalGS', $data);
    }


    public function viewSaldoAwalBS(Request $request)
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier', 'ASC')->get();
        return view('saldoawal.viewSaldoAwalBS', $data);
    }

    public function createSaldoAwalGS()
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier', 'ASC')->get();
        return view('saldoawal.createSaldoAwalGS', $data);
    }

    public function createSaldoAwalBS()
    {
        $data['suppliers'] = DB::table('supplier')->orderBy('nama_supplier', 'ASC')->get();
        return view('saldoawal.createSaldoAwalBS', $data);
    }

    public function storeSaldoAwalGS(Request $request)
    {
        $kodeBarang = $request->kode_barang;
        $stokData = $request->stok;  // array stok per satuan
        $qtyTotal = $request->konversi ?? 0;

        // Ambil bulan dan tahun dari request, fallback ke bulan dan tahun sekarang
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        // Untuk tanggal, kamu bisa set ke awal bulan supaya konsisten
        $tanggal = date('Y-m-d', strtotime("$tahun-$bulan-01"));

        if ($qtyTotal > 0) {
            DB::table('saldo_awal_gs')->updateOrInsert(
                [
                    'kode_barang' => $kodeBarang,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'qty' => $qtyTotal,
                    'tanggal' => $tanggal,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return response()->json(['status' => 'success']);
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

    public function getBarangBSBySupplier($kode_supplier)
    {
        $barang = DB::table('barang')
            ->join('barang_satuan', function ($join) {
                $join->on('barang.kode_barang', '=', 'barang_satuan.kode_barang')
                    ->where('barang_satuan.isi', 1);
            })
            ->where('barang.kode_supplier', $kode_supplier)
            ->where('barang.status', 1)
            ->select('barang.kode_barang', 'barang.nama_barang', 'barang_satuan.satuan')
            ->orderBy('barang.nama_barang')
            ->get();

        return response()->json($barang);
    }
    public function getBarangGSBySupplier($kode_supplier)
    {
        $barang = DB::table('barang')
            ->join('barang_satuan', function ($join) {
                $join->on('barang.kode_barang', '=', 'barang_satuan.kode_barang')
                    ->where('barang_satuan.isi', 1);
            })
            ->leftJoin('saldo_awal_gs', 'barang.kode_barang', '=', 'saldo_awal_gs.kode_barang')
            ->where('barang.kode_supplier', $kode_supplier)
            ->where('barang.status', 1)
            ->select(
                'barang.kode_barang',
                'barang.nama_barang',
                'barang_satuan.satuan',
                DB::raw('COALESCE(saldo_awal_gs.qty, 0) as qty')
            )
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
