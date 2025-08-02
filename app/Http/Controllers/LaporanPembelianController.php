<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPembelianController extends Controller
{

    public function laporanPembelian()
    {
        $data['suppliers'] = DB::table('supplier')
        ->where('status', '1')
        ->orderBy('nama_supplier')
        ->get();

        $data['barangs'] = DB::table('barang')
        ->where('status','1')
        ->orderBy('nama_barang')
        ->get();
        return view('laporan.pembelian.laporanPembelian', $data);
    }


    public function cetakLaporanSemuaPembelian(Request $request)
    {
        $mulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $akhir = $request->tanggal_sampai ?? now()->format('Y-m-d');

        $subPembayaran = DB::table('pembelian_pembayaran')
            ->selectRaw('no_faktur, SUM(jumlah) as jumlah')
            ->groupBy('no_faktur');
        $subRetur = DB::table('retur_pembelian')
            ->where('jenis_retur','PF')
            ->selectRaw('no_faktur as no_faktur, SUM(total) as jumlah_retur')
            ->groupBy('no_faktur');
        $data = DB::table('pembelian as p')
        ->selectRaw('
            p.no_faktur,
            p.tanggal,
            p.jatuh_tempo,
            p.jenis_transaksi,
            p.potongan,
            p.pajak,
            p.biaya_lain,
            p.grand_total,
            p.keterangan,
            p.created_at,
            p.updated_at,
            s.kode_supplier,
            s.nama_supplier,
            u.nama_lengkap as penginput,
            COALESCE(pb.jumlah, 0) as sudah_bayar,
            COALESCE(rp.jumlah_retur, 0) as jumlah_retur,
            (p.grand_total - COALESCE(pb.jumlah, 0)) as sisa,
            (p.grand_total - COALESCE(rp.jumlah_retur, 0)) as total_bersih,
            CASE
                WHEN (p.grand_total - COALESCE(pb.jumlah, 0)) <= 0 THEN "Lunas"
                ELSE "Belum Lunas"
            END AS status
        ')
        ->join('supplier as s', 's.kode_supplier', '=', 'p.kode_supplier')
        ->leftJoin('hrd_karyawan as u', 'u.nik', '=', 'p.id_user')
        ->leftJoinSub($subPembayaran, 'pb', function ($join) {
            $join->on('pb.no_faktur', '=', 'p.no_faktur');
        })
        ->leftJoinSub($subRetur, 'rp', function ($join) {
            $join->on('rp.no_faktur', '=', 'p.no_faktur');
        })
        ->when($request->filled('kode_supplier'), fn($q) => $q->where('p.kode_supplier', $request->kode_supplier))
        ->when($request->filled('jenis_transaksi'), fn($q) => $q->where('p.jenis_transaksi', $request->jenis_transaksi))
        ->whereBetween('p.tanggal', [$mulai, $akhir])
        ->orderBy('p.tanggal')
        ->orderBy('p.no_faktur')
        ->get();

        // Ambil detail
        $fakturList = $data->pluck('no_faktur')->toArray();
        $details = DB::table('pembelian_detail as pd')
            ->join('barang_satuan as bs', 'bs.id', '=', 'pd.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->whereIn('pd.no_faktur', $fakturList)
            ->select('pd.*', 'bs.kode_barang', 'b.nama_barang', 'bs.satuan')
            ->get()
            ->groupBy('no_faktur');

        // Gabungkan detail ke header
        foreach ($data as $d) {
            $d->detail = $details[$d->no_faktur] ?? collect();
        }

        // Ambil nama supplier (jika difilter)
        $nama_supplier = null;
        if ($request->filled('kode_supplier')) {
            $supplier = DB::table('supplier')->where('kode_supplier', $request->kode_supplier)->first();
            $nama_supplier = $supplier->nama_supplier ?? null;
        }

        // Cek format cetak
        if ($request->jenis_laporan == '1') {
            if ($request->has('export')) {
                return Excel::download(
                    new LaporanExport('laporan.pembelian.cetakLaporanSemuaPembelian', [
                        'data' => $data,
                        'mulai' => $mulai,
                        'akhir' => $akhir,
                        'nama_supplier' => $nama_supplier,
                    ]),
                    'Laporan_Pembelian_Format1.xlsx'
                );
            }
            return view('laporan.pembelian.cetakLaporanSemuaPembelian', compact('data', 'mulai', 'akhir', 'nama_supplier'));
        } elseif ($request->jenis_laporan == '2') {
            if ($request->has('export')) {
                return Excel::download(
                    new LaporanExport('laporan.pembelian.cetakLaporanSemuaPembelian2', [
                        'data' => $data,
                        'mulai' => $mulai,
                        'akhir' => $akhir,
                        'nama_supplier' => $nama_supplier,
                    ]),
                    'Laporan_Pembelian_Format2.xlsx'
                );
            }
            return view('laporan.pembelian.cetakLaporanSemuaPembelian2', compact('data', 'mulai', 'akhir', 'nama_supplier'));
        } else {
            if ($request->has('export')) {
                return Excel::download(
                    new LaporanExport('laporan.pembelian.cetakLaporanSemuaPembelian3', [
                        'data' => $data,
                        'mulai' => $mulai,
                        'akhir' => $akhir,
                        'nama_supplier' => $nama_supplier,
                    ]),
                    'Laporan_Pembelian_Format3.xlsx'
                );
            }
            return view('laporan.pembelian.cetakLaporanSemuaPembelian3', compact('data', 'mulai', 'akhir', 'nama_supplier'));
        }
    }

    public function cetakKartuHutang(Request $request)
    {
        $mulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $akhir = $request->tanggal_sampai ?? now()->format('Y-m-d');

        // Ambil pembayaran hutang
        $subPembayaran = DB::table('pembelian_pembayaran')
            ->selectRaw('no_faktur, SUM(jumlah) as jumlah_bayar')
            ->groupBy('no_faktur');

        // Header data pembelian
        $data = DB::table('pembelian as p')
            ->selectRaw('
                p.no_faktur,
                p.tanggal,
                p.jatuh_tempo,
                p.kode_supplier,
                s.nama_supplier,
                p.jenis_transaksi,
                p.potongan,
                p.pajak,
                p.biaya_lain,
                p.grand_total,
                COALESCE(pb.jumlah_bayar, 0) as sudah_bayar,
                (p.grand_total - COALESCE(pb.jumlah_bayar, 0)) as sisa,
                CASE
                    WHEN (p.grand_total - COALESCE(pb.jumlah_bayar, 0)) <= 0 THEN "Lunas"
                    ELSE "Belum Lunas"
                END AS status
            ')
            ->join('supplier as s', 's.kode_supplier', '=', 'p.kode_supplier')
            ->leftJoinSub($subPembayaran, 'pb', function ($join) {
                $join->on('pb.no_faktur', '=', 'p.no_faktur');
            })
            ->when($request->filled('kode_supplier'), fn($q) => $q->where('p.kode_supplier', $request->kode_supplier))
            ->whereBetween('p.tanggal', [$mulai, $akhir])
            ->orderBy('p.kode_supplier')
            ->orderBy('p.tanggal')
            ->get();

        // Ambil detail barang jika jenis laporan = 1
        $fakturList = $data->pluck('no_faktur')->toArray();
        $details = collect();

        if ($request->jenis_laporan == '1') {
            $details = DB::table('pembelian_detail as pd')
                ->join('barang_satuan as bs', 'bs.id', '=', 'pd.satuan_id')
                ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
                ->whereIn('pd.no_faktur', $fakturList)
                ->select('pd.*', 'bs.kode_barang', 'b.nama_barang', 'bs.satuan')
                ->get()
                ->groupBy('no_faktur');
        }

        // Gabungkan detail jika perlu
        foreach ($data as $d) {
            $d->detail = $details[$d->no_faktur] ?? collect();
        }

        // Ambil nama supplier (jika difilter)
        $nama_supplier = null;
        if ($request->filled('kode_supplier')) {
            $supplier = DB::table('supplier')->where('kode_supplier', $request->kode_supplier)->first();
            $nama_supplier = $supplier->nama_supplier ?? null;
        }

        $view = 'laporan.pembelian.cetakKartuHutang';
        if ($request->jenis_laporan == '2') {
            $view = 'laporan.pembelian.cetakKartuHutang2';
        } elseif ($request->jenis_laporan == '3') {
            $view = 'laporan.pembelian.cetakKartuHutang3';
        }

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport($view, [
                    'data' => $data,
                    'mulai' => $mulai,
                    'akhir' => $akhir,
                    'nama_supplier' => $nama_supplier,
                ]),
                'Kartu_Hutang_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        return view($view, compact('data', 'mulai', 'akhir', 'nama_supplier'));
    }

    public function cetakAnalisaUmurHutang(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');

        // Subquery: total pembayaran per faktur
        $subPembayaran = DB::table('pembelian_pembayaran')
            ->selectRaw('no_faktur, SUM(jumlah) as jumlah_bayar')
            ->groupBy('no_faktur');

        // Ambil data pembelian yang belum lunas
        $pembelian = DB::table('pembelian as p')
            ->selectRaw('
                p.no_faktur,
                p.tanggal,
                p.jatuh_tempo,
                p.kode_supplier,
                s.nama_supplier,
                p.grand_total,
                COALESCE(pb.jumlah_bayar, 0) as sudah_bayar,
                (p.grand_total - COALESCE(pb.jumlah_bayar, 0)) as sisa
            ')
            ->join('supplier as s', 's.kode_supplier', '=', 'p.kode_supplier')
            ->leftJoinSub($subPembayaran, 'pb', function ($join) {
                $join->on('pb.no_faktur', '=', 'p.no_faktur');
            })
            ->whereRaw('(p.grand_total - COALESCE(pb.jumlah_bayar, 0)) > 0')
            ->when($request->filled('kode_supplier'), fn($q) => $q->where('p.kode_supplier', $request->kode_supplier))
            ->whereDate('p.tanggal', '<=', $tanggal)
            ->get();

        // Kelompokkan & hitung umur hutang per supplier
        $grouped = $pembelian->groupBy('kode_supplier')->map(function ($items, $kode_supplier) use ($tanggal) {
            $nama_supplier = $items->first()->nama_supplier;
            $umur_0 = $umur_1 = $umur_2 = $umur_3 = 0;
            $saldo = 0;
            $today = \Carbon\Carbon::parse($tanggal);

            foreach ($items as $item) {
                $sisa = $item->sisa;
                $jatuhTempo = \Carbon\Carbon::parse($item->jatuh_tempo);
                $umurHari = $jatuhTempo->diffInDays($today, false); // bisa negatif jika belum jatuh tempo

                $saldo += $sisa;

                if ($umurHari <= 30) {
                    $umur_0 += $sisa;
                } elseif ($umurHari <= 60) {
                    $umur_1 += $sisa;
                } elseif ($umurHari <= 90) {
                    $umur_2 += $sisa;
                } else {
                    $umur_3 += $sisa;
                }
            }

            return (object)[
                'kode_supplier' => $kode_supplier,
                'nama_supplier' => $nama_supplier,
                'saldo' => $saldo,
                'umur_0' => $umur_0,
                'umur_1' => $umur_1,
                'umur_2' => $umur_2,
                'umur_3' => $umur_3,
            ];
        })->values();

        // Ambil nama supplier jika difilter
        $nama_supplier = null;
        if ($request->filled('kode_supplier')) {
            $nama_supplier = $grouped->first()->nama_supplier ?? null;
        }

        $view = 'laporan.pembelian.cetakAnalisaUmurHutang';

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport($view, [
                    'data' => $grouped,
                    'tanggal' => $tanggal,
                    'nama_supplier' => $nama_supplier,
                ]),
                'Analisa_Umur_Hutang_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        return view($view, [
            'data' => $grouped,
            'tanggal' => $tanggal,
            'nama_supplier' => $nama_supplier,
        ]);
    }


    public function cetakRekapPerSupplier(Request $request)
    {
        $mulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->tanggal_sampai ?? now()->format('Y-m-d');

        // Subquery: total pembayaran per faktur
        $subPembayaran = DB::table('pembelian_pembayaran')
            ->selectRaw('no_faktur, SUM(jumlah) as jumlah_bayar')
            ->groupBy('no_faktur');

        // Query utama: rekap per supplier
        $pembelian = DB::table('pembelian as p')
            ->selectRaw('
                p.kode_supplier,
                s.nama_supplier,
                SUM(p.grand_total) as total_pembelian,
                SUM(COALESCE(pb.jumlah_bayar, 0)) as jumlah_bayar,
                SUM(p.grand_total - COALESCE(pb.jumlah_bayar, 0)) as sisa_hutang
            ')
            ->join('supplier as s', 's.kode_supplier', '=', 'p.kode_supplier')
            ->leftJoinSub($subPembayaran, 'pb', function ($join) {
                $join->on('pb.no_faktur', '=', 'p.no_faktur');
            })
            ->when($request->filled('kode_supplier'), fn($q) => $q->where('p.kode_supplier', $request->kode_supplier))
            ->whereBetween('p.tanggal', [$mulai, $sampai])
            ->groupBy('p.kode_supplier', 's.nama_supplier')
            ->orderBy('p.kode_supplier')
            ->get();

        // Ambil nama supplier jika difilter
        $nama_supplier = null;
        if ($request->filled('kode_supplier')) {
            $nama_supplier = $pembelian->first()->nama_supplier ?? null;
        }

        $view = 'laporan.pembelian.cetakRekapPerSupplier';

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport($view, [
                    'data' => $pembelian,
                    'mulai' => $mulai,
                    'sampai' => $sampai,
                    'nama_supplier' => $nama_supplier,
                ]),
                'Rekap_Pembelian_Per_Supplier_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        return view($view, [
            'data' => $pembelian,
            'mulai' => $mulai,
            'sampai' => $sampai,
            'nama_supplier' => $nama_supplier,
        ]);
    }


    public function cetakLaporanReturPembelian(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $tanggal_sampai = $request->tanggal_sampai ?? now()->format('Y-m-d');

        $data = DB::table('retur_pembelian as r')
            ->join('supplier as s', 's.kode_supplier', '=', 'r.kode_supplier')
            ->whereBetween('r.tanggal', [$tanggal_mulai, $tanggal_sampai])
            ->select('r.*', 's.nama_supplier')
            ->orderBy('r.tanggal')
            ->get();

        $detail = DB::table('retur_pembelian_detail as d')
            ->join('barang_satuan as bs', 'bs.id', '=', 'd.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->select('d.*', 'bs.kode_barang', 'bs.satuan', 'b.nama_barang')
            ->whereIn('d.no_retur', $data->pluck('no_retur'))
            ->get()
            ->groupBy('no_retur');

        // Gabungkan detail ke header
        foreach ($data as $row) {
            $row->detail = $detail[$row->no_retur] ?? collect();
        }

        return view('laporan.pembelian.cetakLaporanReturPembelian', compact('data', 'tanggal_mulai', 'tanggal_sampai'));
    }


}
