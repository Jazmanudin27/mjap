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

class LaporanGudangController extends Controller
{

    public function laporanGudang()
    {
        $data['suppliers'] = DB::table('supplier')
            ->where('status', '1')
            ->orderBy('nama_supplier')
            ->get();

        $data['barangs'] = DB::table('barang')

            ->orderBy('nama_barang')
            ->get();

        return view('laporan.gudang.laporanGudang', $data);
    }

    public function cetakKartuStok(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-t');
        $kode_barang = $request->kode_barang;
        $nama_barang = null;

        if (!empty($kode_barang)) {
            $nama_barang = DB::table('barang')
                ->where('kode_barang', $kode_barang)
                ->value('nama_barang');
        }

        $saldoawal = DB::table('saldo_awal_gs')
            ->where('kode_barang', $kode_barang)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->sum('qty');

        $sql ="
            SELECT x.tanggal, x.kode_barang, x.no_faktur, x.kode_sales, x.nama_sales,
                SUM(x.pembelian) AS pembelian,
                SUM(x.retur_pengganti) AS retur_pengganti,
                SUM(x.repack) AS repack,
                SUM(x.penyesuaian_masuk) AS penyesuaian_masuk,
                SUM(x.lainnya_masuk) AS lainnya_masuk,
                SUM(x.penjualan) AS penjualan,
                SUM(x.reject_gudang) AS reject_gudang,
                SUM(x.penyesuaian_keluar) AS penyesuaian_keluar,
                SUM(x.lainnya_keluar) AS lainnya_keluar
            FROM (
                -- Mutasi masuk
                SELECT mb.tanggal, bs.kode_barang, mb.no_faktur,
                    NULL AS kode_sales, NULL AS nama_sales,
                    CASE WHEN mb.jenis_pemasukan = 'Pembelian' THEN mbd.qty_konversi ELSE 0 END AS pembelian,
                    CASE WHEN mb.jenis_pemasukan = 'Retur Pengganti' THEN mbd.qty_konversi ELSE 0 END AS retur_pengganti,
                    CASE WHEN mb.jenis_pemasukan = 'Repack' THEN mbd.qty_konversi ELSE 0 END AS repack,
                    CASE WHEN mb.jenis_pemasukan = 'Penyesuaian' THEN mbd.qty_konversi ELSE 0 END AS penyesuaian_masuk,
                    CASE WHEN mb.jenis_pemasukan = 'Lainnya' THEN mbd.qty_konversi ELSE 0 END AS lainnya_masuk,
                    0 AS penjualan, 0 AS reject_gudang, 0 AS penyesuaian_keluar, 0 AS lainnya_keluar
                FROM mutasi_barang_masuk mb
                JOIN mutasi_barang_masuk_detail mbd ON mb.kode_transaksi = mbd.kode_transaksi
                JOIN barang_satuan bs ON bs.id = mbd.satuan_id
                WHERE mb.kondisi = 'gs' AND mb.tanggal BETWEEN ? AND ?
                " . (!empty($kode_barang) ? " AND bs.kode_barang = ? " : "") . "

                UNION ALL

                -- Mutasi keluar
                SELECT mk.tanggal, bs.kode_barang, mk.no_faktur,
                    pj.kode_sales, k.nama_lengkap AS nama_sales,
                    0,0,0,0,0,
                    CASE WHEN mk.jenis_pengeluaran = 'Penjualan' THEN mkd.qty_konversi ELSE 0 END AS penjualan,
                    CASE WHEN mk.jenis_pengeluaran = 'Reject' THEN mkd.qty_konversi ELSE 0 END AS reject_gudang,
                    CASE WHEN mk.jenis_pengeluaran = 'Penyesuaian' THEN mkd.qty_konversi ELSE 0 END AS penyesuaian_keluar,
                    CASE WHEN mk.jenis_pengeluaran = 'Lainnya' THEN mkd.qty_konversi ELSE 0 END AS lainnya_keluar
                FROM mutasi_barang_keluar mk
                JOIN mutasi_barang_keluar_detail mkd ON mk.kode_transaksi = mkd.kode_transaksi
                JOIN barang_satuan bs ON bs.id = mkd.satuan_id
                LEFT JOIN penjualan pj ON pj.no_faktur = mk.no_faktur
                LEFT JOIN hrd_karyawan k ON k.nik = pj.kode_sales
                WHERE mk.kondisi = 'gs' AND mk.tanggal BETWEEN ? AND ?
                " . (!empty($kode_barang) ? " AND bs.kode_barang = ? " : "") . "
            ) AS x
            GROUP BY x.tanggal, x.kode_barang, x.no_faktur, x.kode_sales, x.nama_sales
            ORDER BY x.tanggal ASC
        ";
        $bindings = [$tanggal_awal, $tanggal_akhir];
        if (!empty($kode_barang))
            $bindings[] = $kode_barang;
        $bindings[] = $tanggal_awal;
        $bindings[] = $tanggal_akhir;
        if (!empty($kode_barang))
            $bindings[] = $kode_barang;

        $rows = DB::select($sql, $bindings);
        $data = collect($rows);

        // satuan untuk barang (dipakai kalau mau konversi)
        $satuan_barang = DB::table('barang_satuan')
            ->where('kode_barang', $kode_barang)
            ->orderBy('isi', 'desc')
            ->get()
            ->groupBy('kode_barang');
        return view('laporan.gudang.cetakKartuStok', [
            'data' => $data,
            'satuan_barang' => $satuan_barang,
            'saldoawal' => $saldoawal,
            'nama_barang' => $nama_barang,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
        ]);
    }


    public function cetakLaporanPersediaanGS(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $kode_supplier = $request->kode_supplier;
        $nama_supplier = null;

        if (!empty($kode_supplier)) {
            $nama_supplier = DB::table('supplier')
                ->where('kode_supplier', $kode_supplier)
                ->value('nama_supplier');
        }

        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['kode_supplier'] = $kode_supplier;
        $data['nama_supplier'] = $nama_supplier;

        $query = DB::table('barang')
            ->leftJoin('barang_satuan', function ($q) {
                $q->on('barang.kode_barang', '=', 'barang_satuan.kode_barang')
                    ->where('barang_satuan.isi', 1);
            })
            ->leftJoin(DB::raw("(
                SELECT kode_barang, SUM(qty) AS saldo_awal
                FROM saldo_awal_gs
                WHERE bulan = {$bulan} AND tahun = {$tahun}
                GROUP BY kode_barang
            ) AS sa"), 'barang.kode_barang', '=', 'sa.kode_barang')
            ->leftJoin(DB::raw("(
                SELECT bs.kode_barang,
                    SUM(CASE WHEN jenis_pemasukan = 'Pembelian' THEN mbd.qty_konversi ELSE 0 END) AS pembelian,
                    SUM(CASE WHEN jenis_pemasukan = 'Repack' THEN mbd.qty_konversi ELSE 0 END) AS repack,
                    SUM(CASE WHEN jenis_pemasukan = 'Retur Pengganti' THEN mbd.qty_konversi ELSE 0 END) AS retur_pengganti,
                    SUM(CASE WHEN jenis_pemasukan = 'Penyesuaian' THEN mbd.qty_konversi ELSE 0 END) AS penyesuaian_masuk,
                    SUM(CASE WHEN jenis_pemasukan = 'Lainnya' THEN mbd.qty_konversi ELSE 0 END) AS lainnya_masuk
                FROM mutasi_barang_masuk_detail mbd
                JOIN mutasi_barang_masuk mb ON mb.kode_transaksi = mbd.kode_transaksi
                JOIN barang_satuan bs ON bs.id = mbd.satuan_id
                WHERE mb.kondisi = 'gs' AND MONTH(mb.tanggal) = {$bulan} AND YEAR(mb.tanggal) = {$tahun}
                GROUP BY bs.kode_barang
            ) AS masuk"), 'barang.kode_barang', '=', 'masuk.kode_barang')

            ->leftJoin(DB::raw("(
                SELECT bs.kode_barang,
                    SUM(CASE WHEN jenis_pengeluaran = 'Penjualan' THEN mkd.qty_konversi ELSE 0 END) AS penjualan,
                    SUM(CASE WHEN jenis_pengeluaran = 'Reject' THEN mkd.qty_konversi ELSE 0 END) AS reject_gudang,
                    SUM(CASE WHEN jenis_pengeluaran = 'Penyesuaian' THEN mkd.qty_konversi ELSE 0 END) AS penyesuaian_keluar,
                    SUM(CASE WHEN jenis_pengeluaran = 'Lainnya' THEN mkd.qty_konversi ELSE 0 END) AS lainnya_keluar
                FROM mutasi_barang_keluar_detail mkd
                JOIN mutasi_barang_keluar mk ON mk.kode_transaksi = mkd.kode_transaksi
                JOIN barang_satuan bs ON bs.id = mkd.satuan_id
                WHERE mk.kondisi = 'gs' AND MONTH(mk.tanggal) = {$bulan} AND YEAR(mk.tanggal) = {$tahun}
                GROUP BY bs.kode_barang
            ) AS keluar"), 'barang.kode_barang', '=', 'keluar.kode_barang')

            ->select(
                'barang.kode_barang',
                'barang.nama_barang',
                'barang.kode_item',
                'barang.kategori',
                'barang.merk',
                'barang_satuan.satuan',
                DB::raw('COALESCE(saldo_awal, 0) AS saldo_awal'),
                // Penerimaan
                DB::raw('COALESCE(pembelian, 0) AS pembelian'),
                DB::raw('COALESCE(retur_pengganti, 0) AS retur_pengganti'),
                DB::raw('COALESCE(repack, 0) AS repack'),
                DB::raw('COALESCE(penyesuaian_masuk, 0) AS penyesuaian_masuk'),
                DB::raw('COALESCE(lainnya_masuk, 0) AS lainnya_masuk'),
                // Pengeluaran
                DB::raw('COALESCE(penjualan, 0) AS penjualan'),
                DB::raw('COALESCE(reject_gudang, 0) AS reject_gudang'),
                DB::raw('COALESCE(penyesuaian_keluar, 0) AS penyesuaian_keluar'),
                DB::raw('COALESCE(lainnya_keluar, 0) AS lainnya_keluar'),
                DB::raw('
                (COALESCE(saldo_awal, 0) +
                COALESCE(pembelian, 0) +
                COALESCE(retur_pengganti, 0) +
                COALESCE(repack, 0) +
                COALESCE(penyesuaian_masuk, 0) +
                COALESCE(lainnya_masuk, 0) -
                COALESCE(penjualan, 0) -
                COALESCE(reject_gudang, 0) -
                COALESCE(penyesuaian_keluar, 0) -
                COALESCE(lainnya_keluar, 0)
                ) AS saldo_akhir')
            );

        if (!$request->barang_tidak_aktif) {
            $query->where('barang.status', 1);
            // $query->having('saldo_akhir', '>', 0);
        }
        if (!empty($kode_supplier)) {
            $query->where('barang.kode_supplier', $kode_supplier);
        }

        $query->orderBy('barang.nama_barang');
        $data['data'] = $query->get();
        $satuan_barang = DB::table('barang_satuan')
            ->whereIn('kode_barang', $data['data']->pluck('kode_barang'))
            ->orderBy('isi', 'desc')
            ->get()
            ->groupBy('kode_barang');

        $data['satuan_barang'] = $satuan_barang;
        if ($request->has('export')) {

            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=Laporan Barang.xls");
            return view('laporan.gudang.cetakLaporanPersediaanGS', $data);
        }

        return view('laporan.gudang.cetakLaporanPersediaanGS', $data);
    }


    public function cetakLaporanPersediaanBS(Request $request)
    {
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');
        $kode_supplier = $request->kode_supplier;
        $nama_supplier = null;

        if (!empty($kode_supplier)) {
            $nama_supplier = DB::table('supplier')
                ->where('kode_supplier', $kode_supplier)
                ->value('nama_supplier');
        }

        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['kode_supplier'] = $kode_supplier;
        $data['nama_supplier'] = $nama_supplier;

        $query = DB::table('barang')
            ->leftJoin('barang_satuan', function ($q) {
                $q->on('barang.kode_barang', '=', 'barang_satuan.kode_barang')
                    ->where('barang_satuan.isi', 1); // ambil satuan terkecil
            })

            // SALDO AWAL
            ->leftJoin(DB::raw("(
            SELECT kode_barang, SUM(qty) AS saldo_awal
            FROM saldo_awal_bs
            WHERE bulan = {$bulan} AND tahun = {$tahun}
            GROUP BY kode_barang
        ) AS sa"), 'barang.kode_barang', '=', 'sa.kode_barang')

            // PENERIMAAN - Retur Penjualan, Penyesuaian Masuk, Lainnya Masuk
            ->leftJoin(DB::raw("(
            SELECT bs.kode_barang,
                SUM(CASE WHEN jenis_pemasukan = 'Retur Penjualan' THEN mbd.qty_konversi ELSE 0 END) AS retur_penjualan,
                SUM(CASE WHEN jenis_pemasukan = 'Penyesuaian' THEN mbd.qty_konversi ELSE 0 END) AS penyesuaian_masuk,
                SUM(CASE WHEN jenis_pemasukan = 'Lainnya' THEN mbd.qty_konversi ELSE 0 END) AS lainnya_masuk
            FROM mutasi_barang_masuk_detail mbd
            JOIN mutasi_barang_masuk mb ON mb.kode_transaksi = mbd.kode_transaksi
            JOIN barang_satuan bs ON bs.id = mbd.satuan_id
            WHERE mb.kondisi = 'bs' AND MONTH(mb.tanggal) = {$bulan} AND YEAR(mb.tanggal) = {$tahun}
            GROUP BY bs.kode_barang
        ) AS masuk"), 'barang.kode_barang', '=', 'masuk.kode_barang')

            // PENGELUARAN - Retur Pembelian, Penyesuaian Keluar, Lainnya Keluar
            ->leftJoin(DB::raw("(
            SELECT bs.kode_barang,
                SUM(CASE WHEN jenis_pengeluaran = 'Retur Pembelian' THEN mkd.qty_konversi ELSE 0 END) AS retur_pembelian,
                SUM(CASE WHEN jenis_pengeluaran = 'Penyesuaian' THEN mkd.qty_konversi ELSE 0 END) AS penyesuaian_keluar,
                SUM(CASE WHEN jenis_pengeluaran = 'Lainnya' THEN mkd.qty_konversi ELSE 0 END) AS lainnya_keluar
            FROM mutasi_barang_keluar_detail mkd
            JOIN mutasi_barang_keluar mk ON mk.kode_transaksi = mkd.kode_transaksi
            JOIN barang_satuan bs ON bs.id = mkd.satuan_id
            WHERE mk.kondisi = 'bs' AND MONTH(mk.tanggal) = {$bulan} AND YEAR(mk.tanggal) = {$tahun}
            GROUP BY bs.kode_barang
        ) AS keluar"), 'barang.kode_barang', '=', 'keluar.kode_barang')

            // REJECT GUDANG (dari GS ke BS)
            ->leftJoin(DB::raw("(
            SELECT bs.kode_barang,
                SUM(CASE WHEN jenis_pengeluaran = 'Reject' THEN mkd.qty_konversi ELSE 0 END) AS reject_gudang
            FROM mutasi_barang_keluar_detail mkd
            JOIN mutasi_barang_keluar mk ON mk.kode_transaksi = mkd.kode_transaksi
            JOIN barang_satuan bs ON bs.id = mkd.satuan_id
            WHERE mk.kondisi = 'gs' AND MONTH(mk.tanggal) = {$bulan} AND YEAR(mk.tanggal) = {$tahun}
            GROUP BY bs.kode_barang
        ) AS keluarReject"), 'barang.kode_barang', '=', 'keluarReject.kode_barang')

            ->select(
                'barang.kode_barang',
                'barang.nama_barang',
                'barang.kode_item',
                'barang.kode_item',
                'barang.kode_item',
                'barang.kode_item',
                'barang_satuan.satuan',
                DB::raw('COALESCE(saldo_awal, 0) AS saldo_awal'),
                DB::raw('COALESCE(retur_penjualan, 0) AS retur_penjualan'),
                DB::raw('COALESCE(penyesuaian_masuk, 0) AS penyesuaian_masuk'),
                DB::raw('COALESCE(lainnya_masuk, 0) AS lainnya_masuk'),
                DB::raw('COALESCE(retur_pembelian, 0) AS retur_pembelian'),
                DB::raw('COALESCE(penyesuaian_keluar, 0) AS penyesuaian_keluar'),
                DB::raw('COALESCE(lainnya_keluar, 0) AS lainnya_keluar'),
                DB::raw('COALESCE(reject_gudang, 0) AS reject_gudang')
            );

        if (!empty($kode_supplier)) {
            $query->where('barang.kode_supplier', $kode_supplier);
        }

        $query->where(function ($q) {
            $q->where('sa.saldo_awal', '>', 0)
                ->orWhere('masuk.retur_penjualan', '>', 0)
                ->orWhere('masuk.penyesuaian_masuk', '>', 0)
                ->orWhere('masuk.lainnya_masuk', '>', 0)
                ->orWhere('keluarReject.reject_gudang', '>', 0)
                ->orWhere('keluar.retur_pembelian', '>', 0)
                ->orWhere('keluar.penyesuaian_keluar', '>', 0)
                ->orWhere('keluar.lainnya_keluar', '>', 0);
        })->orderBy('barang.nama_barang');
        $data['data'] = $query->get();

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport('laporan.gudang.cetakLaporanPersediaanBS', $data),
                'Laporan_Persediaan_BS.xlsx'
            );
        }

        return view('laporan.gudang.cetakLaporanPersediaanBS', $data);
    }

    public function cetakLaporanPersediaan(Request $request)
    {
        $mulai = $request->mulai ?? date('Y-m-01');
        $akhir = $request->akhir ?? date('Y-m-d');
        $kode_barang = $request->kode_barang;

        $barang = DB::table('barang')->where('kode_barang', $kode_barang)->first();

        // Hitung saldo awal
        $masukSebelum = DB::table('mutasi_barang_masuk_detail as md')
            ->join('mutasi_barang_masuk as m', 'm.kode_transaksi', '=', 'md.kode_transaksi')
            ->join('barang_satuan as bs', 'bs.id', '=', 'md.satuan_id')
            ->where('bs.kode_barang', $kode_barang)
            ->where('m.tanggal', '<', $mulai)
            ->sum('md.qty_konversi');

        $keluarSebelum = DB::table('mutasi_barang_keluar_detail as kd')
            ->join('mutasi_barang_keluar as k', 'k.kode_transaksi', '=', 'kd.kode_transaksi')
            ->join('barang_satuan as bs', 'bs.id', '=', 'kd.satuan_id')
            ->where('bs.kode_barang', $kode_barang)
            ->where('k.tanggal', '<', $mulai)
            ->sum('kd.qty_konversi');

        $saldoAwal = $masukSebelum - $keluarSebelum;

        // Ambil transaksi masuk
        $masuk = DB::table('mutasi_barang_masuk_detail as md')
            ->join('mutasi_barang_masuk as m', 'm.kode_transaksi', '=', 'md.kode_transaksi')
            ->join('barang_satuan as bs', 'bs.id', '=', 'md.satuan_id')
            ->where('m.kondisi', 'gs')
            ->where('bs.kode_barang', $kode_barang)
            ->whereBetween('m.tanggal', [$mulai, $akhir])
            ->select(
                'm.tanggal',
                'm.kode_transaksi',
                'm.no_faktur',
                'm.jenis_pemasukan',
                DB::raw("'' as jenis_pengeluaran"),
                'm.keterangan',
                DB::raw('md.qty_konversi as qty_masuk'),
                DB::raw('0 as qty_keluar')
            );

        // Ambil transaksi keluar
        $keluar = DB::table('mutasi_barang_keluar_detail as kd')
            ->join('mutasi_barang_keluar as k', 'k.kode_transaksi', '=', 'kd.kode_transaksi')
            ->join('barang_satuan as bs', 'bs.id', '=', 'kd.satuan_id')
            ->where('bs.kode_barang', $kode_barang)
            ->where('k.kondisi', 'gs')
            ->whereBetween('k.tanggal', [$mulai, $akhir])
            ->select(
                'k.tanggal',
                'k.kode_transaksi',
                'k.no_faktur',
                DB::raw("'' as jenis_pemasukan"),
                'k.jenis_pengeluaran',
                'k.keterangan',
                DB::raw('0 as qty_masuk'),
                DB::raw('kd.qty_konversi as qty_keluar')
            );

        // Gabungkan dan urutkan
        $transaksiGabung = $masuk->unionAll($keluar)
            ->orderBy('tanggal')
            ->orderBy('kode_transaksi')
            ->get();

        $data = [];
        $saldo = $saldoAwal;

        foreach ($transaksiGabung as $t) {
            $saldo += ($t->qty_masuk - $t->qty_keluar);

            // PENERIMAAN
            $pembelian = $t->jenis_pemasukan === 'Pembelian' ? $t->qty_masuk : 0;
            $repack = $t->jenis_pemasukan === 'Repack' ? $t->qty_masuk : 0;
            $penyesuaian_masuk = $t->jenis_pemasukan === 'Penyesuaian' ? $t->qty_masuk : 0;
            $lainnya_masuk = $t->jenis_pemasukan === 'Lainnya' ? $t->qty_masuk : 0;

            // PENGELUARAN
            $penjualan = $t->jenis_pengeluaran === 'Penjualan' ? $t->qty_keluar : 0;
            $retur_pembelian = $t->jenis_pengeluaran === 'Reject' ? $t->qty_keluar : 0;
            $penyesuaian_keluar = $t->jenis_pengeluaran === 'Penyesuaian' ? $t->qty_keluar : 0;
            $lainnya_keluar = $t->jenis_pengeluaran === 'Lainnya' ? $t->qty_keluar : 0;

            $data[] = [
                'tanggal' => $t->tanggal,
                'pembelian' => $t->jenis_pemasukan === 'Pembelian' ? $t->no_faktur : '',
                'penjualan' => $t->jenis_pengeluaran === 'Penjualan' ? $t->no_faktur : '',
                'repack' => $t->jenis_pemasukan === 'Repack' ? $t->no_faktur : '',
                'retur_beli' => $t->jenis_pengeluaran === 'Reject' ? $t->no_faktur : '',
                'mutasi_masuk' => $t->jenis_pemasukan === 'mutasi_masuk' ? $t->no_faktur : '',
                'mutasi_keluar' => $t->jenis_pengeluaran === 'mutasi_keluar' ? $t->no_faktur : '',
                'keterangan' => $t->keterangan,

                // PENERIMAAN
                'masuk_pembelian' => $pembelian,
                'masuk_repack' => $repack,
                'masuk_penyesuaian' => $penyesuaian_masuk,
                'masuk_lainnya' => $lainnya_masuk,

                // PENGELUARAN
                'keluar_penjualan' => $penjualan,
                'keluar_retur_beli' => $retur_pembelian,
                'keluar_penyesuaian' => $penyesuaian_keluar,
                'keluar_lainnya' => $lainnya_keluar,

                'saldo_akhir' => $saldo,
            ];
        }

        if ($request->has('export')) {
            return Excel::download(
                new LaporanExport('laporan.gudang.cetakLaporanPersediaan', [
                    'data' => $data,
                    'nama_barang' => $barang->nama_barang ?? '',
                    'tanggal_mulai' => $mulai,
                    'tanggal_akhir' => $akhir,
                    'saldo_awal' => $saldoAwal,
                ]),
                'Laporan_Persediaan_' . $barang->nama_barang . '_' . $mulai . '_' . $akhir . '.xlsx'
            );
        }

        return view('laporan.gudang.cetakLaporanPersediaan', [
            'data' => $data,
            'nama_barang' => $barang->nama_barang ?? '',
            'tanggal_mulai' => $mulai,
            'tanggal_akhir' => $akhir,
            'saldo_awal' => $saldoAwal,
        ]);
    }

    public function cetakLaporanMutasiBarang(Request $request)
    {
        $mulai = $request->mulai ?? date('Y-m-01');
        $akhir = $request->akhir ?? date('Y-m-d');
        $kode_barang = $request->kode_barang;
        $kode_supplier = $request->kode_supplier;
        $jenis = $request->jenis;

        $queryMasuk = DB::table('mutasi_barang_masuk_detail as md')
            ->join('mutasi_barang_masuk as m', 'm.kode_transaksi', '=', 'md.kode_transaksi')
            ->join('barang_satuan as bs', 'bs.id', '=', 'md.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->leftJoin(DB::raw("(
                SELECT kode_barang, satuan
                FROM barang_satuan
                WHERE isi = 1
                GROUP BY kode_barang
            ) as bs_kecil"), 'bs_kecil.kode_barang', '=', 'b.kode_barang')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier')
            ->whereBetween('m.tanggal', [$mulai, $akhir]);

        if ($kode_barang) {
            $queryMasuk->where('bs.kode_barang', $kode_barang);
        }

        if ($kode_supplier) {
            $queryMasuk->where('b.kode_supplier', $kode_supplier);
        }

        $masuk = $queryMasuk->select(
            'm.tanggal_diterima as tanggal_dikirim',
            'm.catatan',
            'm.tanggal',
            'm.kode_transaksi',
            'm.kondisi',
            DB::raw("'Masuk' as jenis"),
            'm.jenis_pemasukan as jenis_transaksi',
            's.nama_supplier',
            'b.nama_barang',
            'md.qty',
            'md.konversi',
            'md.qty_konversi',
            'bs_kecil.satuan as satuan',
            'm.keterangan'
        );

        $queryKeluar = DB::table('mutasi_barang_keluar_detail as kd')
            ->join('mutasi_barang_keluar as k', 'k.kode_transaksi', '=', 'kd.kode_transaksi')
            ->join('barang_satuan as bs', 'bs.id', '=', 'kd.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->leftJoin(DB::raw("(
                SELECT kode_barang, satuan
                FROM barang_satuan
                WHERE isi = 1
                GROUP BY kode_barang
            ) as bs_kecil"), 'bs_kecil.kode_barang', '=', 'b.kode_barang')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier')
            ->whereBetween('k.tanggal', [$mulai, $akhir]);

        if ($kode_barang) {
            $queryKeluar->where('bs.kode_barang', $kode_barang);
        }

        if ($kode_supplier) {
            $queryKeluar->where('b.kode_supplier', $kode_supplier);
        }

        $keluar = $queryKeluar->select(
            'k.tanggal_dikirim',
            'k.catatan',
            'k.tanggal',
            'k.kode_transaksi',
            'k.kondisi',
            DB::raw("'Keluar' as jenis"),
            'k.jenis_pengeluaran as jenis_transaksi',
            's.nama_supplier',
            'b.nama_barang',
            'kd.qty',
            'kd.konversi',
            'kd.qty_konversi',
            'bs_kecil.satuan as satuan',
            'k.keterangan'
        );

        // --- Gabung atau Filter Jenis ---
        if ($jenis == 'Masuk') {
            $transaksi = $masuk->orderBy('tanggal')->orderBy('kode_transaksi')->get();
        } elseif ($jenis == 'Keluar') {
            $transaksi = $keluar->orderBy('tanggal')->orderBy('kode_transaksi')->get();
        } else {
            $transaksi = $masuk->unionAll($keluar)
                ->orderBy('tanggal')
                ->orderBy('kode_transaksi')
                ->get();
        }

        return view('laporan.gudang.cetakLaporanMutasiBarang', [
            'data' => $transaksi,
            'tanggal_mulai' => $mulai,
            'tanggal_akhir' => $akhir,
            'kode_barang' => $kode_barang,
            'kode_supplier' => $kode_supplier,
        ]);
    }
}
