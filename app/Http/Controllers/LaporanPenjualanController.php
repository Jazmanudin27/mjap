<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualanController extends Controller
{
    public function laporanPenjualan()
    {
        $data['sales'] = getSales();
        $data['pelanggan'] = DB::table('pelanggan')
            ->where('status', '1')
            ->orderBy('nama_pelanggan')
            ->get();

        return view('laporan.penjualan.laporanPenjualan', $data);
    }

    public function laporanSemuaPenjualan()
    {
        $data['sales'] = getSales();

        return view('laporan.penjualan.laporanSemuaPenjualan', $data);
    }

    public function cetaklaporanPenjualan(Request $request)
    {
        $mulai = $request->tanggal_dari ?? now()->startOfMonth();
        $akhir = $request->tanggal_sampai ?? now();
        $user = Auth::user();

        $subPembayaran = DB::table('penjualan_pembayaran')
            ->selectRaw('no_faktur, SUM(jumlah) as jumlah')
            ->groupBy('no_faktur');

        $subTransfer = DB::table('penjualan_pembayaran_transfer')
            ->selectRaw('no_faktur, SUM(jumlah) as jumlah')
            ->groupBy('no_faktur');

        $data = DB::table('penjualan as p')
            ->selectRaw('
                p.no_faktur,
                p.tanggal,
                p.batal,
                p.updated_at,
                p.created_at,
                p.jenis_transaksi,
                pl.kode_pelanggan,
                pl.nama_pelanggan,
                pl.alamat_toko,
                w.nama_wilayah,
                s.nama_lengkap as sales,
                k.nama_lengkap as penginput,
                p.total,
                p.diskon,
                p.grand_total,
                COALESCE(pt.jumlah, 0) + COALESCE(tr.jumlah, 0) as sudah_bayar,
                (p.grand_total - (COALESCE(pt.jumlah, 0) + COALESCE(tr.jumlah, 0))) as sisa,
                CASE
                    WHEN p.batal = 1 THEN "Batal"
                    WHEN (p.grand_total - (COALESCE(pt.jumlah, 0) + COALESCE(tr.jumlah, 0))) <= 0 THEN "Lunas"
                    ELSE "Belum Lunas"
                END AS status
            ')
            ->join('pelanggan as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->leftJoin('wilayah as w', 'w.kode_wilayah', '=', 'pl.kode_wilayah')
            ->join('hrd_karyawan as k', 'k.nik', '=', 'p.id_user')
            ->join('hrd_karyawan as s', 's.nik', '=', 'p.kode_sales')
            ->leftJoinSub($subPembayaran, 'pt', function ($join) {
                $join->on('pt.no_faktur', '=', 'p.no_faktur');
            })
            ->leftJoinSub($subTransfer, 'tr', function ($join) {
                $join->on('tr.no_faktur', '=', 'p.no_faktur');
            })
            ->when($request->filled('batal'), fn($q) => $q->where('p.batal', $request->batal))
            ->when($request->filled('jenis_transaksi'), fn($q) => $q->where('p.jenis_transaksi', $request->jenis_transaksi))
            ->when($request->filled('kode_pelanggan'), fn($q) => $q->where('p.kode_pelanggan', $request->kode_pelanggan))
            ->when($request->filled('salesman'), function ($q) use ($request) {
                $q->where('p.kode_sales', $request->salesman);
            })
            ->when($user->role == 'spv sales' && !$request->filled('salesman'), function ($q) use ($user) {
                $q->where('s.divisi', $user->nik);
            })
            ->whereBetween('p.tanggal', [$mulai, $akhir])
            ->orderBy('p.tanggal')
            ->orderBy('p.no_faktur')
            ->get();

        $fakturList = $data->pluck('no_faktur')->toArray();
        $details = DB::table('penjualan_detail as pd')
            ->join('barang_satuan as bs', 'bs.id', '=', 'pd.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->whereIn('pd.no_faktur', $fakturList)
            ->select('pd.*', 'bs.kode_barang', 'b.nama_barang', 'bs.satuan', 'b.jenis', 'b.merk', 'b.kategori', 'pd.is_promo')
            ->get()
            ->groupBy('no_faktur');

        foreach ($data as $d) {
            $d->detail = $details[$d->no_faktur] ?? collect();
        }

        if ($request->status_tempo == '1') {
            if ($request->has('export')) {
                $filename = 'Laporan_Penjualan_Format1.xls';
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=\"$filename\"");
                header("Cache-Control: max-age=0");
            }
            return view('laporan.penjualan.cetakLaporanPenjualan', compact('data', 'mulai', 'akhir'));
        } else {
            if ($request->has('export')) {
                $filename = 'Laporan_Penjualan_Format2.xls';
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=\"$filename\"");
                header("Cache-Control: max-age=0");
            }
            return view('laporan.penjualan.cetakLaporanPenjualan2', compact('data', 'mulai', 'akhir'));
        }
    }

    public function laporanPenjualanHarian()
    {
        $data['sales'] = getSales();

        return view('laporan.penjualan.laporanPenjualanHarian', $data);
    }

    public function cetaklaporanPenjualanHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $kodeSales = $request->salesman;

        $data = DB::table('pelanggan as pl')
            ->leftJoin('penjualan as p', function ($join) use ($tanggal, $kodeSales) {
                $join->on('p.kode_pelanggan', '=', 'pl.kode_pelanggan')
                    ->where('p.kode_sales', $kodeSales)
                    ->whereDate('p.tanggal_kirim', $tanggal);
            })
            ->leftJoin('penjualan_pembayaran as pp', function ($join) use ($tanggal, $kodeSales) {
                $join->on('pp.no_faktur', '=', 'p.no_faktur')
                    ->whereDate('pp.tanggal', $tanggal)
                    ->where('pp.kode_sales', $kodeSales);
            })
            ->leftJoin('penjualan_pembayaran_transfer as ppt', function ($join) use ($tanggal, $kodeSales) {
                $join->on('ppt.no_faktur', '=', 'p.no_faktur')
                    ->whereDate('ppt.tanggal', $tanggal)
                    ->where('ppt.kode_sales', $kodeSales);
            })
            ->leftJoin('penjualan_pembayaran_giro as ppg', function ($join) use ($tanggal, $kodeSales) {
                $join->on('ppg.no_faktur', '=', 'p.no_faktur')
                    ->whereDate('ppg.tanggal', $tanggal)
                    ->where('ppg.kode_sales', $kodeSales);
            })
            ->selectRaw('
                pl.kode_pelanggan,
                pl.nama_pelanggan,
                p.no_faktur,
                p.tanggal_kirim as tanggal,
                p.jenis_transaksi,
                p.batal,
                p.grand_total,
                SUM(CASE WHEN p.jenis_transaksi = "K" AND DATE(p.tanggal_kirim) = "' . $tanggal . '" THEN p.grand_total ELSE 0 END) as kredit,
                SUM(CASE WHEN pp.jenis_bayar = "tunai" THEN pp.jumlah ELSE 0 END) as tunai,
                SUM(CASE WHEN pp.jenis_bayar = "titipan" THEN pp.jumlah ELSE 0 END) as titipan,
                SUM(CASE WHEN pp.jenis_bayar = "voucher" THEN pp.jumlah ELSE 0 END) as voucher,
                SUM(COALESCE(ppt.jumlah, 0)) as transfer,
                SUM(COALESCE(ppg.jumlah, 0)) as giro
            ')
            ->where(function ($q) {
                $q->whereNotNull('p.no_faktur')
                    ->orWhereNotNull('pp.jumlah')
                    ->orWhereNotNull('ppt.jumlah')
                    ->orWhereNotNull('ppg.jumlah');
            })
            ->groupBy(
                'pl.kode_pelanggan',
                'pl.nama_pelanggan',
                'p.no_faktur',
                'p.tanggal_kirim',
                'p.jenis_transaksi',
                'p.grand_total'
            )
            ->orderBy('pl.nama_pelanggan')
            ->get();

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport('laporan.penjualan.cetakLaporanPenjualanHarian', [
                    'data' => $data,
                    'tanggal' => $tanggal,
                ]),
                'Laporan_Penjualan_Harian.xlsx'
            );
        }

        return view('laporan.penjualan.cetakLaporanPenjualanHarian', compact('data', 'tanggal'));
    }

    public function cetakLaporanRekapPerPelanggan(Request $request)
    {
        $mulai = $request->tanggal_dari ?? now()->startOfMonth();
        $akhir = $request->tanggal_sampai ?? now();

        $data = DB::table('penjualan as p')
            ->join('pelanggan as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->join('hrd_karyawan as s', 's.nik', '=', 'p.kode_sales')
            ->selectRaw('
                p.kode_pelanggan,
                pl.nama_pelanggan,
                s.nama_lengkap as salesman,
                COUNT(p.no_faktur) as total_transaksi,
                SUM(CASE WHEN p.jenis_transaksi = "K" THEN 1 ELSE 0 END) as jumlah_kredit,
                SUM(CASE WHEN p.jenis_transaksi = "T" THEN 1 ELSE 0 END) as jumlah_tunai,
                SUM(p.total) as total,
                SUM(p.diskon) as diskon,
                SUM(p.grand_total) as grand_total
            ')
            ->when($request->filled('kode_pelanggan'), fn($q) => $q->where('p.kode_pelanggan', $request->kode_pelanggan))
            ->when($request->filled('salesman'), fn($q) => $q->where('p.kode_sales', $request->salesman))
            ->when($request->filled('jenis_transaksi'), fn($q) => $q->where('p.jenis_transaksi', $request->jenis_transaksi))
            ->when($request->filled('batal'), fn($q) => $q->where('p.batal', $request->batal))
            ->whereBetween('p.tanggal', [$mulai, $akhir])
            ->groupBy('p.kode_pelanggan', 'pl.nama_pelanggan', 's.nama_lengkap')
            ->orderBy('pl.nama_pelanggan')
            ->get();

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport('laporan.penjualan.cetakLaporanRekapPerPelanggan', [
                    'data' => $data,
                    'mulai' => $mulai,
                    'akhir' => $akhir,
                ]),
                'Laporan_Penjualan_Per_Pelanggan.xlsx'
            );
        }

        return view('laporan.penjualan.cetakLaporanRekapPerPelanggan', compact('data', 'mulai', 'akhir'));
    }

    public function cetakLaporanReturPenjualan(Request $request)
    {
        $mulai = $request->tanggal_dari ?? now()->startOfMonth();
        $akhir = $request->tanggal_sampai ?? now();

        // Ambil data retur
        $data = DB::table('retur_penjualan as rj')
            ->join('pelanggan as pl', 'pl.kode_pelanggan', '=', 'rj.kode_pelanggan')
            ->join('hrd_karyawan as s', 's.nik', '=', 'rj.kode_sales')
            ->leftJoin('penjualan as p', 'p.no_faktur', '=', 'rj.no_faktur')
            ->select(
                'rj.no_retur',
                'rj.tanggal',
                'rj.jenis_retur',
                'rj.kode_pelanggan',
                'pl.nama_pelanggan',
                'rj.kode_sales',
                's.nama_lengkap as sales',
                'rj.no_faktur',
                'rj.total',
                'rj.keterangan',
                'rj.created_at'
            )
            ->when($request->filled('salesman'), fn($q) => $q->where('rj.kode_sales', $request->salesman))
            ->when($request->filled('kode_pelanggan'), fn($q) => $q->where('rj.kode_pelanggan', $request->kode_pelanggan))
            ->whereBetween('rj.tanggal', [$mulai, $akhir])
            ->orderBy('rj.tanggal')
            ->orderBy('rj.no_retur')
            ->get();

        // Ambil semua nomor retur
        $returNos = $data->pluck('no_retur')->toArray();

        // Ambil detail barang retur
        $details = DB::table('retur_penjualan_detail as d')
            ->join('barang_satuan as bs', 'bs.id', '=', 'd.id_satuan')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->whereIn('d.no_retur', $returNos)
            ->select('d.*', 'bs.kode_barang', 'bs.satuan', 'b.nama_barang')
            ->get()
            ->groupBy('no_retur');

        // Sisipkan detail ke dalam data utama
        foreach ($data as $d) {
            $d->detail = $details[$d->no_retur] ?? collect();
        }

        // Jika export Excel
        if ($request->has('export')) {
            $view = $request->status_tempo == 'detail'
                ? 'laporan.penjualan.cetakLaporanReturPenjualanDetail'
                : 'laporan.penjualan.cetakLaporanReturPenjualan';

            return Excel::download(
                new LaporanExport($view, [
                    'data' => $data,
                    'mulai' => $mulai,
                    'akhir' => $akhir,
                ]),
                'Laporan_Retur_Penjualan.xlsx'
            );
        }

        // Jika cetak ke view biasa
        if ($request->status_tempo == 'detail') {
            return view('laporan.penjualan.cetakLaporanReturPenjualanDetail', compact('data', 'mulai', 'akhir'));
        } else {
            return view('laporan.penjualan.cetakLaporanReturPenjualan', compact('data', 'mulai', 'akhir'));
        }
    }

    public function cetakKartuPiutang(Request $request)
    {
        $tanggal_dari = $request->tanggal_dari ?? Carbon::now()->startOfMonth()->toDateString();
        $tanggal_sampai = $request->tanggal_sampai ?? Carbon::now()->toDateString();
        $kode_pelanggan = $request->kode_pelanggan;
        $status_tempo = $request->status_tempo;

        $tanggalJatuhTempo = 'DATE_ADD(p.tanggal, INTERVAL IFNULL(pl.ljt, 30) DAY)';

        $data = DB::table('penjualan as p')
            ->leftJoin('pelanggan as pl', 'p.kode_pelanggan', '=', 'pl.kode_pelanggan')
            ->leftJoin('wilayah as dr', 'pl.kode_wilayah', '=', 'dr.kode_wilayah')
            ->leftJoin('hrd_karyawan as s', 'p.kode_sales', '=', 's.nik')
            ->leftJoin(DB::raw("(
                SELECT no_faktur, SUM(total) as total_retur
                FROM retur_penjualan
                WHERE jenis_retur = 'PF'
                GROUP BY no_faktur
            ) as rj"), 'p.no_faktur', '=', 'rj.no_faktur')

            // JOIN total pembayaran per sumber + last payment
            ->leftJoin(DB::raw("(
                SELECT no_faktur,
                    SUM(CASE WHEN sumber = 'tunai' THEN jumlah ELSE 0 END) as bayar_tunai,
                    SUM(CASE WHEN sumber = 'transfer' THEN jumlah ELSE 0 END) as bayar_transfer,
                    SUM(CASE WHEN sumber = 'giro' THEN jumlah ELSE 0 END) as bayar_giro,
                    SUM(jumlah) as total_pembayaran
                FROM (
                    SELECT no_faktur, tanggal, jumlah, 'tunai' as sumber FROM penjualan_pembayaran
                    UNION ALL
                    SELECT no_faktur, tanggal, jumlah, 'transfer' FROM penjualan_pembayaran_transfer
                    UNION ALL
                    SELECT no_faktur, tanggal, jumlah, 'giro' FROM penjualan_pembayaran_giro
                ) as semua
                WHERE tanggal <= '$tanggal_sampai'
                GROUP BY no_faktur
            ) as bayar"), 'p.no_faktur', '=', 'bayar.no_faktur')

            ->leftJoin(DB::raw("(
                SELECT no_faktur, MAX(tanggal) as last_payment
                FROM (
                    SELECT no_faktur, tanggal FROM penjualan_pembayaran
                    UNION ALL
                    SELECT no_faktur, tanggal FROM penjualan_pembayaran_transfer
                    UNION ALL
                    SELECT no_faktur, tanggal FROM penjualan_pembayaran_giro
                ) as semua_tanggal
                GROUP BY no_faktur
            ) as last_bayar"), 'p.no_faktur', '=', 'last_bayar.no_faktur')

            ->select([
                'p.no_faktur',
                'p.tanggal',
                'p.kode_pelanggan',
                'pl.nama_pelanggan',
                's.nama_lengkap as nama_sales',
                'dr.nama_wilayah as pasar_daerah',

                DB::raw("{$tanggalJatuhTempo} as jatuh_tempo"),
                DB::raw("DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) as usia_piutang"),

                DB::raw('IFNULL(rj.total_retur, 0) as retur_penjualan'),
                DB::raw('p.grand_total as grand_total'),

                DB::raw('IFNULL(bayar.bayar_tunai, 0) as bayar_tunai'),
                DB::raw('IFNULL(bayar.bayar_transfer, 0) as bayar_transfer'),
                DB::raw('IFNULL(bayar.bayar_giro, 0) as bayar_giro'),
                DB::raw('IFNULL(bayar.total_pembayaran, 0) as total_pembayaran'),
                DB::raw('last_bayar.last_payment as last_payment'),
                DB::raw('p.grand_total - IFNULL(bayar.total_pembayaran, 0) as saldo_akhir'),

                DB::raw("CASE
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 15 THEN '1 s/d 15 Hari'
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 30 THEN '16 Hari s/d 1 Bulan'
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 60 THEN '> 1 Bulan s/d 2 Bulan'
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 90 THEN '> 2 Bulan s/d 3 Bulan'
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 180 THEN '> 3 Bulan s/d 6 Bulan'
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 360 THEN '> 6 Bulan s/d 1 Tahun'
                    WHEN DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) <= 720 THEN '> 1 Tahun s/d 2 Tahun'
                    ELSE 'Lebih dari 2 Tahun'
                END as kategori_aup"),
            ])
            ->where('p.jenis_transaksi', 'K')
            ->where('p.batal', 0)
            ->when($kode_pelanggan, fn($q) => $q->where('p.kode_pelanggan', $kode_pelanggan))
            ->when($status_tempo == '1', fn($q) => $q->whereRaw("DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) > 30"))
            ->when($status_tempo == '2', fn($q) => $q->whereRaw("{$tanggalJatuhTempo} > CURDATE()"))
            ->whereRaw("DATEDIFF(CURDATE(), {$tanggalJatuhTempo}) >= 1")
            ->groupBy('p.no_faktur')
            ->orderBy('p.tanggal', 'asc')
            ->get();

        return view('laporan.penjualan.cetakKartuPiutangPerFaktur', [
            'data' => $data,
            'tanggal_dari' => $tanggal_dari,
            'tanggal_sampai' => $tanggal_sampai,
            'kode_pelanggan' => $kode_pelanggan,
            'status_tempo' => $status_tempo,
        ]);
    }

    public function cetakAnalisaUmurPiutang(Request $request)
    {
        $tanggal_sampai = $request->tanggal ?? now()->toDateString();

        $data = DB::table('penjualan as p')
            ->join('pelanggan as pl', 'p.kode_pelanggan', '=', 'pl.kode_pelanggan')
            ->leftJoin('wilayah as w', 'pl.kode_wilayah', '=', 'w.kode_wilayah')
            ->leftJoin('hrd_karyawan as s', 'p.kode_sales', '=', 's.nik')

            // Join pembayaran dari 3 sumber
            ->leftJoin(DB::raw("(
                SELECT no_faktur,
                    SUM(CASE WHEN sumber = 'tunai' THEN jumlah ELSE 0 END) as bayar_tunai,
                    SUM(CASE WHEN sumber = 'transfer' THEN jumlah ELSE 0 END) as bayar_transfer,
                    SUM(CASE WHEN sumber = 'giro' THEN jumlah ELSE 0 END) as bayar_giro,
                    SUM(jumlah) as total_pembayaran
                FROM (
                    SELECT no_faktur, tanggal, jumlah, 'tunai' as sumber FROM penjualan_pembayaran
                    UNION ALL
                    SELECT no_faktur, tanggal, jumlah, 'transfer' FROM penjualan_pembayaran_transfer
                    UNION ALL
                    SELECT no_faktur, tanggal, jumlah, 'giro' FROM penjualan_pembayaran_giro
                ) as semua
                WHERE tanggal <= '$tanggal_sampai'
                GROUP BY no_faktur
            ) as byr"), 'p.no_faktur', '=', 'byr.no_faktur')

            ->select([
                'p.kode_pelanggan',
                'pl.nama_pelanggan',
                'w.nama_wilayah as pasar_daerah',
                's.nama_lengkap as nama_sales',

                // Ambil Jatuh Tempo tertinggi
                DB::raw('MAX(DATE_ADD(p.tanggal, INTERVAL IFNULL(pl.ljt, 30) DAY)) as jatuh_tempo'),

                // Total saldo piutang dari semua faktur
                DB::raw('SUM(p.grand_total - IFNULL(byr.total_pembayaran, 0)) as saldo'),

                // Hitung usia piutang dari jatuh tempo terbaru
                DB::raw('DATEDIFF(CURDATE(), MAX(DATE_ADD(p.tanggal, INTERVAL IFNULL(pl.ljt, 30) DAY))) as usia')
            ])
            ->where('p.jenis_transaksi', 'K')
            ->where('p.batal', 0)
            ->whereRaw('(p.grand_total - IFNULL(byr.total_pembayaran, 0)) > 0')
            ->whereRaw('DATEDIFF(CURDATE(), DATE_ADD(p.tanggal, INTERVAL IFNULL(pl.ljt, 30) DAY)) >= 1')
            ->groupBy('p.kode_pelanggan', 'pl.nama_pelanggan', 'w.nama_wilayah', 's.nama_lengkap')
            ->orderBy('pl.nama_pelanggan')
            ->get();

        return view('laporan.penjualan.cetakAnalisaUmurPiutang', [
            'data' => $data,
            'tanggal_sampai' => $tanggal_sampai,
        ]);
    }
    public function cetakTargetSales(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;
        $bulan = $request->bulan ?? now()->month;
        $team = $request->team ?? '';

        $data = DB::table('target_sales as t')
            ->leftJoin('penjualan as p', function ($join) {
                $join->on('t.kode_sales', '=', 'p.kode_sales')
                    ->whereRaw('YEAR(p.tanggal) = t.tahun')
                    ->whereRaw('MONTH(p.tanggal) = t.bulan');
            })
            ->leftJoin('hrd_karyawan as k', 'k.nik', '=', 't.kode_sales')
            ->where('t.tahun', $tahun)
            ->where('t.bulan', $bulan)
            ->when($team, function ($query) use ($team) {
                return $query->where('k.divisi', $team);
            })
            ->selectRaw('
                t.kode_sales,
                k.nama_lengkap as nama_sales,
                t.tahun,
                t.bulan,

                t.target_1 as target_oa,
                t.target_2 as target_ec,
                t.target_3 as target_penjualan,
                t.target_4 as target_tagihan,

                COUNT(DISTINCT p.kode_pelanggan) as real_oa,
                COUNT(p.no_faktur) as real_ec,
                SUM(CASE WHEN p.batal = 0 THEN 1 ELSE 0 END) as real_faktur,
                SUM(CASE WHEN p.batal = 0 THEN p.grand_total ELSE 0 END) as real_penjualan,
                SUM(CASE WHEN p.jenis_bayar = "Kredit" AND p.batal = 0 THEN p.grand_total ELSE 0 END) as real_tagihan,

                ROUND(COUNT(DISTINCT p.kode_pelanggan) / NULLIF(t.target_1, 0) * 100, 2) as persen_oa,
                ROUND(COUNT(p.no_faktur) / NULLIF(t.target_2, 0) * 100, 2) as persen_ec,
                ROUND(SUM(CASE WHEN p.batal = 0 THEN p.grand_total ELSE 0 END) / NULLIF(t.target_3, 0) * 100, 2) as persen_penjualan,
                ROUND(SUM(CASE WHEN p.jenis_bayar = "Kredit" AND p.batal = 0 THEN p.grand_total ELSE 0 END) / NULLIF(t.target_4, 0) * 100, 2) as persen_tagihan
            ')
            ->groupBy(
                't.kode_sales',
                'k.nama_lengkap',
                't.tahun',
                't.bulan',
                't.target_1',
                't.target_2',
                't.target_3',
                't.target_4'
            )
            ->orderBy('t.kode_sales')
            ->get();

        return view('laporan.penjualan.cetakTargetSales', [
            'data' => $data,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

}
