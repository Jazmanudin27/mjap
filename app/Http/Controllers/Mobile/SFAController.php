<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PenjualanController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SFAController extends Controller
{
    public function viewDashboardSFAMobile()
    {
        $today = Date('Y-m-d');
        $bulanIni = Date('m');
        $tahunIni = Date('Y');
        $role = Auth::user()->role;
        $nik = Auth::user()->nik;

        if (in_array($role, ['admin', 'owner'])) {
            $salesList = DB::table('users')->where('team','!=','')->pluck('nik');
        } elseif ($role === 'spv sales') {
            $salesList = DB::table('users')->where('team', $nik)->pluck('nik');
        } else {
            $salesList = [$nik];
        }
        $data['salesOptions'] = DB::table('users')
            ->whereIn('nik', $salesList)
            ->select('nik', 'name')
            ->get();
        $data['penjualanHariIni'] = DB::table('penjualan')
            ->whereDate('tanggal', $today)
            ->where('batal', 0)
            ->whereIn('kode_sales', $salesList)
            ->sum('grand_total');

        $data['penjualanBulanIni'] = DB::table('penjualan')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('batal', 0)
            ->whereIn('kode_sales', $salesList)
            ->sum('grand_total');

        $data['pembayaranHariIni'] =
            DB::table('penjualan_pembayaran')->whereDate('tanggal', $today)->whereIn('kode_sales', $salesList)->sum('jumlah') +
            DB::table('penjualan_pembayaran_transfer')->whereDate('tanggal', $today)->whereIn('kode_sales', $salesList)->sum('jumlah') +
            DB::table('penjualan_pembayaran_giro')->whereDate('tanggal', $today)->whereIn('kode_sales', $salesList)->sum('jumlah');

        $data['pembayaranBulanIni'] =
            DB::table('penjualan_pembayaran')->whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->whereIn('kode_sales', $salesList)->sum('jumlah') +
            DB::table('penjualan_pembayaran_transfer')->whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->whereIn('kode_sales', $salesList)->sum('jumlah') +
            DB::table('penjualan_pembayaran_giro')->whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->whereIn('kode_sales', $salesList)->sum('jumlah');

        $data['targetBulanIni'] = 0;
        $data['persenTarget'] = $data['targetBulanIni'] > 0 ? round(($data['penjualanBulanIni'] / $data['targetBulanIni']) * 100) : 0;

        $data['history'] = DB::table('penjualan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->select('penjualan.*', 'pelanggan.nama_pelanggan as nama_pelanggan')
            ->whereMonth('penjualan.tanggal', Date('m'))
            ->whereIn('penjualan.kode_sales', $salesList)

            ->orderBy('penjualan.tanggal', 'desc')
            ->limit(100)
            ->get();

        $data['activity'] = DB::table('penjualan_checkin')
            ->join('pelanggan', 'penjualan_checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('penjualan_checkin.tanggal', now()->toDateString())
            ->whereIn('penjualan_checkin.kode_sales', $salesList)
            ->select('penjualan_checkin.*', 'pelanggan.nama_pelanggan')
            ->orderBy('penjualan_checkin.tanggal', 'desc')
            ->limit(10)
            ->get();

        return view('mobile.dashboard.viewDashboardSFAMobile', $data);
    }

    public function limitKreditMobile(Request $request)
    {
        $userTeam = Auth::user()->team;
        $userNik = Auth::user()->nik;
        $userRole = Auth::user()->role;

        $query = DB::table('pengajuan_limit_kredit')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', '=', 'pengajuan_limit_kredit.kode_pelanggan')
            ->join('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->join('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_limit_kredit.nik')
            ->select(
                'pengajuan_limit_kredit.*',
                'pelanggan.nama_pelanggan',
                'pelanggan.alamat_toko',
                'wilayah.nama_wilayah',
                'hrd_karyawan.nama_lengkap as dibuat_oleh'
            );


        if ($userRole === 'sales') {
            $query->where('pengajuan_limit_kredit.nik', $userNik);
        }
        if ($userRole === 'spv sales') {
            $query->where('hrd_karyawan.divisi', $userTeam);
        }

        if ($request->filled('kode_pelanggan')) {
            $query->where('pengajuan_limit_kredit.kode_pelanggan', $request->kode_pelanggan);
        }

        $pengajuanRaw = $query->orderBy('pengajuan_limit_kredit.status')->paginate(15);

        // Tambahkan status_global berdasarkan hasil approval dari semua level
        $pengajuan = $pengajuanRaw->getCollection()->map(function ($item) use ($userNik) {
            $approvals = DB::table('pengajuan_approvals')
                ->where('pengajuan_id', $item->id)
                ->where('jenis_pengajuan', 'limit_kredit')
                ->get();

            if ($approvals->contains('ditolak', true)) {
                $item->status_global = 'ditolak';
            } elseif ($approvals->every('disetujui', true)) {
                $item->status_global = 'disetujui';
            } else {
                $item->status_global = 'pending';
            }

            $userApproval = $approvals->firstWhere('user_id', $userNik);
            $item->approval_id = $userApproval->id ?? null;
            $item->pengajuan_id = $userApproval->pengajuan_id ?? null;
            $item->user_id = $userApproval->user_id ?? null;
            $item->disetujui = $userApproval->disetujui ?? null;
            $item->ditolak = $userApproval->ditolak ?? null;

            return $item;
        });

        $pengajuanRaw->setCollection($pengajuan);
        $data['pengajuan'] = $pengajuanRaw;
        $data['pelanggan'] = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();

        return view('mobile.sfa.limitKreditMobile', $data);
    }

    public function limitFakturMobile(Request $request)
    {
        $userNik = Auth::user()->nik;
        $userTeam = Auth::user()->team;
        $userRole = Auth::user()->role;

        $query = DB::table('pengajuan_limit_faktur')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', '=', 'pengajuan_limit_faktur.kode_pelanggan')
            ->join('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->join('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_limit_faktur.nik')
            ->select(
                'pengajuan_limit_faktur.*',
                'pelanggan.nama_pelanggan',
                'pelanggan.alamat_toko',
                'wilayah.nama_wilayah',
                'hrd_karyawan.nama_lengkap as dibuat_oleh'
            );

        if ($userRole == 'sales') {
            $query->where('pengajuan_limit_faktur.nik', $userNik);
        }

        if ($userRole === 'spv sales') {
            $query->where('hrd_karyawan.divisi', $userTeam);
        }
        if ($request->filled('kode_pelanggan')) {
            $query->where('pengajuan_limit_faktur.kode_pelanggan', $request->kode_pelanggan);
        }

        $pengajuanRaw = $query->orderBy('pengajuan_limit_faktur.status')->paginate(15);

        // Tambahkan status_global berdasarkan hasil approval dari semua level
        $pengajuan = $pengajuanRaw->getCollection()->map(function ($item) use ($userNik) {
            $approvals = DB::table('pengajuan_approvals')
                ->where('pengajuan_id', $item->id)
                ->where('jenis_pengajuan', 'double_faktur')
                ->get();

            // Status global
            if ($approvals->contains('ditolak', true)) {
                $item->status_global = 'ditolak';
            } elseif ($approvals->every('disetujui', true)) {
                $item->status_global = 'disetujui';
            } else {
                $item->status_global = 'pending';
            }

            // Cek apakah user login punya approval
            $userApproval = $approvals->firstWhere('user_id', $userNik);

            $item->approval_id = $userApproval->id ?? null;
            $item->pengajuan_id = $userApproval->pengajuan_id ?? null;
            $item->user_id = $userApproval->user_id ?? null;
            $item->disetujui = $userApproval->disetujui ?? null;
            $item->ditolak = $userApproval->ditolak ?? null;


            return $item;
        });

        // Ganti koleksi di pagination
        $pengajuanRaw->setCollection($pengajuan);
        $data['pengajuan'] = $pengajuanRaw;
        $data['pelanggan'] = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();

        return view('mobile.sfa.limitFakturMobile', $data);
    }


    public function filterTargetMobile(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;
        $bulan = $request->bulan ?? now()->month;

        $role = Auth::user()->role;
        $nik = Auth::user()->nik;

        $query = DB::table('target_sales as t')
            ->leftJoin('penjualan as p', function ($join) {
                $join->on('t.kode_sales', '=', 'p.kode_sales')
                    ->whereRaw('YEAR(p.tanggal) = t.tahun')
                    ->whereRaw('MONTH(p.tanggal) = t.bulan');
            })
            ->join('hrd_karyawan as k', 'k.nik', '=', 't.kode_sales')
            ->join('users as s', 'k.nik', '=', 's.nik')
            ->where('t.tahun', $tahun)
            ->where('t.bulan', $bulan);

        if ($role === 'spv sales') {
            $query->where('s.team', $nik);
        } elseif ($role === 'sales') {
            $query->where('t.kode_sales', $nik);
        }

        $data = $query->selectRaw('
            t.kode_sales,
            k.nama_lengkap as nama_sales,

            t.target_1 as target_oa,
            COUNT(DISTINCT p.kode_pelanggan) as real_oa,
            ROUND(COUNT(DISTINCT p.kode_pelanggan)/NULLIF(t.target_1, 0)*100, 2) as persen_oa,

            t.target_2 as target_ec,
            COUNT(p.no_faktur) as real_ec,
            ROUND(COUNT(p.no_faktur)/NULLIF(t.target_2, 0)*100, 2) as persen_ec,

            t.target_3 as target_penjualan,
            SUM(CASE WHEN p.batal = 0 THEN p.grand_total ELSE 0 END) as real_penjualan,
            ROUND(SUM(CASE WHEN p.batal = 0 THEN p.grand_total ELSE 0 END)/NULLIF(t.target_3, 0)*100, 2) as persen_penjualan,

            t.target_4 as target_tagihan,
            SUM(CASE WHEN p.jenis_bayar = "Kredit" AND p.batal = 0 THEN p.grand_total ELSE 0 END) as real_tagihan,
            ROUND(SUM(CASE WHEN p.jenis_bayar = "Kredit" AND p.batal = 0 THEN p.grand_total ELSE 0 END)/NULLIF(t.target_4, 0)*100, 2) as persen_tagihan
        ')
            ->groupBy('t.kode_sales', 'k.nama_lengkap', 't.target_1', 't.target_2', 't.target_3', 't.target_4')
            ->orderBy('k.nama_lengkap')
            ->get();

        return response()->json($data);
    }

    public function filterHistory(Request $request)
    {
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');
        $filterSales = $request->input('sales');

        $user = Auth::user();
        $userRole = $user->role;
        $userNik = $user->nik;

        $history = DB::table('penjualan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('hrd_karyawan', 'penjualan.kode_sales', '=', 'hrd_karyawan.nik')
            ->select(
                'penjualan.*',
                'pelanggan.nama_pelanggan as nama_pelanggan',
                'hrd_karyawan.nama_lengkap as nama_sales'
            )
            ->whereBetween('penjualan.tanggal', [$dari, $sampai]);

        // Apply filter berdasarkan Role:
        if ($userRole == 'sales') {
            $history->where('penjualan.kode_sales', $userNik);
        } else {
            if ($filterSales) {
                $history->where('penjualan.kode_sales', $filterSales);
            }
        }

        $history = $history->orderBy('penjualan.tanggal', 'desc')->get();

        return view('mobile.dashboard.filterHistoryPenjualan', compact('history'));
    }

    public function viewPelangganMobile(Request $request)
    {
        $query = DB::table('pelanggan');
        $query->join('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah');
        if ($request->filled('search')) {
            $query->where('nama_pelanggan', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('wilayah')) {
            $query->where('pelanggan.kode_wilayah', $request->wilayah);
        }
        $data['pelanggan'] = $query->orderBy('nama_pelanggan')
            ->limit(10)
            ->get();
        $data['wilayahList'] = DB::table('wilayah')
            ->pluck('nama_wilayah', 'wilayah.kode_wilayah')
            ->toArray();

        return view('mobile.sfa.viewPelangganMobile', $data);
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'kode_pelanggan' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        // Cek apakah sudah checkin hari ini
        $sudahCheckin = DB::table('penjualan_checkin')
            ->where('kode_sales', Auth::user()->nik)
            ->where('kode_pelanggan', $request->kode_pelanggan)
            ->whereDate('tanggal', now())
            ->whereNull('checkout')
            ->first();

        if ($sudahCheckin) {
            return redirect()->back()->with('warning', 'Sudah check-in untuk pelanggan ini hari ini.');
        }

        DB::table('penjualan_checkin')->insert([
            'kode_sales' => Auth::user()->nik,
            'kode_pelanggan' => $request->kode_pelanggan,
            'tanggal' => now()->format('Y-m-d'),
            'checkin' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'catatan' => 'Check-in otomatis',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Berhasil check-in.');
    }

    public function checkout(Request $request)
    {

        $kunjungan = DB::table('penjualan_checkin')
            ->where('id', $request->kunjungan_id)
            ->where('kode_sales', Auth::user()->nik)
            ->first();

        if (!$kunjungan) {
            return redirect()->back()->with('warning', 'Data kunjungan tidak ditemukan.');
        }

        DB::table('penjualan_checkin')
            ->where('id', $request->kunjungan_id)
            ->update([
                'checkout' => now(),
                'updated_at' => now(),
            ]);

        return redirect('mobile/viewPelangganMobile')->with('success', 'Check-out berhasil.');
    }

    public function viewDetailPelangganMobile($kode_pelanggan)
    {
        $data['pelanggan'] = DB::table('pelanggan')
            ->join('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->select('pelanggan.*', 'wilayah.nama_wilayah')
            ->first();

        $data['returList'] = DB::table('retur_penjualan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderByDesc('tanggal')
            ->get();

        if (!$data['pelanggan']) {
            abort(404, 'Pelanggan tidak ditemukan');
        }

        $data['kunjunganAktif'] = DB::table('penjualan_checkin')
            ->where('kode_sales', Auth::user()->nik)
            ->where('kode_pelanggan', $kode_pelanggan)
            ->whereDate('tanggal', now())
            ->first();

        $data['penjualanList'] = DB::table('penjualan as p')
            ->select(
                'p.no_faktur',
                'p.tanggal',
                'p.grand_total as total',
                'p.jenis_transaksi',
                DB::raw('
                    COALESCE(pp.total_bayar, 0) +
                    COALESCE(pt.total_bayar, 0) +
                    COALESCE(pg.total_bayar, 0) AS total_bayar
                '),
                DB::raw('
                    CASE
                        WHEN (COALESCE(pp.total_bayar, 0) + COALESCE(pt.total_bayar, 0) + COALESCE(pg.total_bayar, 0)) >= p.grand_total
                        THEN "Lunas" ELSE "Belum Lunas"
                    END AS status_bayar
                ')
            )
            ->leftJoin(DB::raw('(SELECT no_faktur, SUM(jumlah) AS total_bayar FROM penjualan_pembayaran GROUP BY no_faktur) as pp'), 'p.no_faktur', '=', 'pp.no_faktur')
            ->leftJoin(DB::raw('(SELECT no_faktur, SUM(jumlah) AS total_bayar FROM penjualan_pembayaran_transfer GROUP BY no_faktur) as pt'), 'p.no_faktur', '=', 'pt.no_faktur')
            ->leftJoin(DB::raw('(SELECT no_faktur, SUM(jumlah) AS total_bayar FROM penjualan_pembayaran_giro GROUP BY no_faktur) as pg'), 'p.no_faktur', '=', 'pg.no_faktur')
            ->where('p.kode_pelanggan', $kode_pelanggan)
            ->orderByDesc('p.tanggal')
            ->limit(20)
            ->get();

        return view('mobile.sfa.viewDetailPelangganMobile', $data);
    }


    public function createReturMobile($id)
    {
        $data['pelanggan'] = DB::table('pelanggan')->where('kode_pelanggan', $id)->first();
        $data['fakturList'] = DB::table('penjualan')->where('kode_pelanggan', $id)
            ->get();
        return view('mobile.sfa.createReturMobile', $data);
    }

    public function createPenjualanMobile($id)
    {
        $data['pelanggan'] = DB::table('pelanggan')->where('kode_pelanggan', $id)->first();
        return view('mobile.sfa.createPenjualanMobile', $data);
    }
    public function storePenjualanMobile(Request $request)
    {
        // dd($request->all());
        $keranjang = json_decode($request->keranjang, true) ?? [];
        if (empty($keranjang)) {
            return back()->with('error', 'Keranjang penjualan masih kosong!')->withInput();
        }

        if ($request->filled('no_faktur')) {
            $noFaktur = $request->no_faktur;
            $mode = 'edit';
        } else {
            $last = DB::table('penjualan')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->orderByDesc('no_faktur')
                ->value('no_faktur');

            $lastNumber = ($last && preg_match('/^(\d{4})-PJ-MJ-(\d{4})$/', $last, $m))
                ? (int) $m[1] + 1 : 1;

            $noFaktur = str_pad($lastNumber, 4, '0', STR_PAD_LEFT) . '-PJ-MJ-' . now()->format('my');
            $mode = 'tambah';
        }


        DB::beginTransaction();
        try {
            if ($mode === 'edit') {
                $kodeTransaksi = DB::table('mutasi_barang_keluar')
                    ->where('no_faktur', $noFaktur)
                    ->pluck('kode_transaksi');
                if ($kodeTransaksi->isNotEmpty()) {
                    DB::table('mutasi_barang_keluar_detail')
                        ->whereIn('kode_transaksi', $kodeTransaksi)
                        ->delete();
                }
                DB::table('mutasi_barang_keluar')
                    ->where('no_faktur', $noFaktur)
                    ->delete();
                DB::table('penjualan_detail')
                    ->where('no_faktur', $noFaktur)
                    ->delete();
                DB::table('penjualan_pembayaran')
                    ->where('no_faktur', $noFaktur)
                    ->delete();
                DB::table('penjualan_pembayaran_transfer')
                    ->where('no_faktur', $noFaktur)
                    ->delete();
                DB::table('penjualan_pembayaran_giro')
                    ->where('no_faktur', $noFaktur)
                    ->delete();
                DB::table('penjualan')
                    ->where('no_faktur', $noFaktur)
                    ->delete();
            }
            DB::table('penjualan')->insert([
                'no_faktur' => $noFaktur,
                'tanggal' => Date('Y-m-d'),
                'kode_pelanggan' => $request->kode_pelanggan,
                'tanggal_kirim' => $request->tanggal,
                'kode_sales' => Auth::user()->nik,
                'jenis_transaksi' => $request->jenis_transaksi,
                'jenis_bayar' => $request->jenis_bayar,
                'keterangan' => $request->keterangan,
                'id_user' => Auth::user()->nik,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $totalKotor = $totalDiskonAll = $totalBersih = 0;
            foreach ($keranjang as $item) {
                $qty = (float) $item['jumlah'];
                $harga = (int) str_replace('.', '', $item['harga_jual']);

                $d1 = (float) ($item['diskon1'] ?? 0);
                $d2 = (float) ($item['diskon2'] ?? 0);
                $d3 = (float) ($item['diskon3'] ?? 0);
                $d4 = (float) ($item['diskon4'] ?? 0);

                $hargaD = round($harga * (1 - $d1 / 100) * (1 - $d2 / 100) * (1 - $d3 / 100) * (1 - $d4 / 100));
                $subtotal = $harga * $qty;
                $total = $hargaD * $qty;
                $diskon = $subtotal - $total;

                DB::table('penjualan_detail')->insert([
                    'no_faktur' => $noFaktur,
                    'kode_barang' => $item['kode_barang'],
                    'satuan_id' => $item['satuan_id'] ?? null,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'diskon1_persen' => $d1,
                    'diskon2_persen' => $d2,
                    'diskon3_persen' => $d3,
                    'diskon4_persen' => $d4,
                    'total_diskon' => $diskon,
                    'total' => $total,
                    'is_promo' => $item['is_promo'] ?? false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalKotor += $subtotal;
                $totalDiskonAll += $diskon;
                $totalBersih += $total;
            }

            DB::table('penjualan')->where('no_faktur', $noFaktur)->update([
                'total' => $totalKotor,
                'diskon' => $totalDiskonAll,
                'grand_total' => $totalBersih,
            ]);


            // if ($request->jenis_bayar === 'tunai') {
            //     DB::table('penjualan_pembayaran')->insert([
            //         'no_bukti' => PenjualanController::generateNoBukti(), // contoh kode
            //         'tanggal' => Date('Y-m-d'),
            //         'no_faktur' => $noFaktur,
            //         'kode_pelanggan' => $request->kode_pelanggan,
            //         'kode_sales' => Auth::user()->nik,
            //         'jenis_bayar' => 'tunai',
            //         'jumlah' => $totalBersih,
            //         'keterangan' => 'Pembayaran tunai dari mobile',
            //         'id_user' => Auth::user()->nik,
            //         'created_at' => now(),
            //         'updated_at' => now(),
            //     ]);
            // } elseif ($request->jenis_bayar === 'transfer') {
            //     DB::table('penjualan_pembayaran_transfer')->insert([
            //         'kode_transfer' => PenjualanController::generateNoBuktiTf(),
            //         'no_faktur' => $noFaktur,
            //         'kode_pelanggan' => $request->kode_pelanggan,
            //         'kode_sales' => Auth::user()->nik,
            //         'jenis_bayar' => 'transfer',
            //         'jumlah' => $totalBersih,
            //         'tanggal' => $request->tanggal,
            //         'bank_pengirim' => $request->bank_pengirim ?? 'Unknown',
            //         'status' => 'pending', // default
            //         'keterangan' => 'Transfer dari mobile app',
            //         'tanggal_diterima' => null,
            //         'id_user' => Auth::user()->nik,
            //         'created_at' => now(),
            //         'updated_at' => now(),
            //     ]);
            // }

            $prefix = 'BK' . date('ym');
            $last = DB::table('mutasi_barang_keluar')
                ->where('kode_transaksi', 'like', "$prefix%")
                ->orderByDesc('kode_transaksi')
                ->value('kode_transaksi');

            $lastNumber = $last ? (int) substr($last, -4) + 1 : 1;
            $kodeTransaksi = $prefix . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

            DB::table('mutasi_barang_keluar')->insert([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal' => $request->tanggal,
                'jenis_pengeluaran' => 'penjualan',
                'tujuan' => $request->kode_pelanggan,
                'keterangan' => 'Penjualan',
                'no_faktur' => $noFaktur,
                'kode_pelanggan' => $request->kode_pelanggan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($keranjang as $item) {
                $barangSatuan = DB::table('barang_satuan')
                    ->where('id', $item['satuan_id'])
                    ->first();
                $konversi = $barangSatuan->isi ?? 1;
                $qty = $item['jumlah'];
                $qty_konversi = $qty * $konversi;
                DB::table('mutasi_barang_keluar_detail')->insert([
                    'kode_transaksi' => $kodeTransaksi,
                    'satuan_id' => $item['satuan_id'] ?? null,
                    'qty' => $qty,
                    'konversi' => $konversi,
                    'qty_konversi' => $qty_konversi,
                ]);
            }

            if ($request->jenis_transaksi === 'K') {
                $supplierTotals = [];

                foreach ($keranjang as $item) {
                    $kodeSupplier = $item['kode_supplier'] ?? null;
                    if (!$kodeSupplier)
                        continue;

                    $supplierTotals[$kodeSupplier] = ($supplierTotals[$kodeSupplier] ?? 0) + (int) str_replace('.', '', $item['total']);
                }

                foreach ($supplierTotals as $kodeSupplier => $totalDipakai) {
                    DB::table('pengajuan_limit_supplier')
                        ->where('kode_supplier', $kodeSupplier)
                        ->whereIn('pengajuan_id', function ($q) use ($request) {
                            $q->select('id')
                                ->from('pengajuan_limit_kredit')
                                ->where('kode_pelanggan', $request->kode_pelanggan);
                        })
                        ->decrement('sisa_limit', $totalDipakai);
                }
            }
            $aksi = $mode === 'edit' ? 'Update Penjualan' : 'Tambah Penjualan';
            logActivity($aksi, "$noFaktur (Pelanggan: {$request->kode_pelanggan})");

            DB::commit();
            return redirect()->route('viewDetailPelangganMobile', $request->kode_pelanggan)->with('success', $mode === 'edit' ? 'Penjualan berhasil diperbarui.' : 'Penjualan berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);
            return back()->with('error', 'Terjadi kesalahan, data gagal disimpan.')->withInput();
        }
    }

    public function detailReturMobile($no_retur)
    {
        // Ambil data retur
        $retur = DB::table('retur_penjualan as rp')
            ->join('pelanggan as p', 'p.kode_pelanggan', '=', 'rp.kode_pelanggan')
            ->select(
                'rp.*',
                'p.nama_pelanggan',
                'p.alamat_toko',
                'p.no_hp_pelanggan'
            )
            ->where('rp.no_retur', $no_retur)
            ->first();

        if (!$retur) {
            return redirect()->back()->with('error', 'Data retur tidak ditemukan.');
        }

        $detail = DB::table('retur_penjualan_detail as d')
            ->join('barang as b', 'b.kode_barang', '=', 'd.kode_barang')
            ->join('barang_satuan as s', 's.id', '=', 'd.id_satuan')
            ->select(
                'd.*',
                'b.nama_barang',
                's.satuan'
            )
            ->where('d.no_retur', $no_retur)
            ->get();

        return view('mobile.sfa.detailReturMobile', [
            'retur' => $retur,
            'detail' => $detail
        ]);
    }
    public function createPelangganMobile()
    {
        $wilayahList = DB::table('wilayah')->pluck('nama_wilayah as nama', 'kode_wilayah as kode')->toArray();
        return view('mobile.sfa.createPelangganMobile', compact('wilayahList'));
    }

    public function editPelangganMobile($kode)
    {
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode)->first();
        if (!$pelanggan) {
            return redirect()->back()->with('error', 'Pelanggan tidak ditemukan.');
        }
        return view('mobile.sfa.editPelangganMobile', compact('pelanggan'));
    }

    public function editFotoLokasiMobile($kode)
    {
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode)->first();
        if (!$pelanggan) {
            return redirect()->back()->with('error', 'Pelanggan tidak ditemukan.');
        }
        return view('mobile.sfa.editFotoLokasiMobile', compact('pelanggan'));
    }

    public function updatePelangganMobile(Request $request)
    {
        $data = [
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'no_hp_pelanggan' => $request->no_hp_pelanggan,
            'kepemilikan' => $request->kepemilikan,
            'omset_toko' => $request->omset_toko,
            'limit_pelanggan' => $request->limit_pelanggan,
            'kunjungan' => $request->kunjungan,
            'hari' => $request->hari,
            'status' => $request->status,
            'updated_at' => now(),
        ];

        $oldData = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = $request->kode_pelanggan . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/pelanggan', $filename);

            if ($oldData && $oldData->foto) {
                Storage::disk('public')->delete('pelanggan/' . $oldData->foto);
            }
            $data['foto'] = $filename;
        }

        $update = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->update($data);

        if ($update) {
            logActivity('Update Pelanggan', 'Pelanggan ' . $request->nama_pelanggan . ' diupdate');
            return redirect()->route('viewDetailPelangganMobile', $request->kode_pelanggan)->with('success', 'Data Berhasil Diupdate');
        } else {
            return redirect()->route('viewDetailPelangganMobile', $request->kode_pelanggan)->with('warning', 'Data Gagal Diupdate');
        }
    }

    public function storePelangganMobile(Request $request)
    {
        $prefix = "PLG";

        $lastPelanggan = DB::table('pelanggan')
            ->where('kode_pelanggan', 'LIKE', "$prefix%")
            ->orderBy('kode_pelanggan', 'desc')
            ->first();

        if ($lastPelanggan) {
            $lastUrut = (int) substr($lastPelanggan->kode_pelanggan, strlen($prefix));
            $nomorUrut = $lastUrut + 1;
        } else {
            $nomorUrut = 1;
        }

        $kodePelanggan = $prefix . str_pad($nomorUrut, 7, '0', STR_PAD_LEFT);
        $simpan = DB::table('pelanggan')->insert([
            'kode_pelanggan' => $kodePelanggan,
            'tanggal_register' => now()->toDateString(),
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'kode_wilayah' => $request->wilayah,
            'alamat_toko' => $request->alamat_toko,
            'no_hp_pelanggan' => $request->no_hp_pelanggan,
            'limit_pelanggan' => $request->limit_pelanggan ?? 0,
            'hari' => $request->hari,
            'kunjungan' => $request->kunjungan,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($simpan) {
            logActivity('Tambah Pelanggan', 'Pelanggan baru ' . $request->nama_pelanggan . ' ditambahkan');
            return redirect()->route('viewDetailPelangganMobile', $kodePelanggan)->with('success', 'Data pelanggan berhasil ditambahkan');
        } else {
            return back()->with('warning', 'Gagal menambahkan data pelanggan')->withInput();
        }
    }


    public function detailPenjualanMobile($id)
    {
        $data['penjualan'] = DB::table('penjualan')
            ->leftJoin('pelanggan', 'pelanggan.kode_pelanggan', 'penjualan.kode_pelanggan')
            ->leftJoin('wilayah', 'wilayah.kode_wilayah', 'pelanggan.kode_wilayah')
            ->where('no_faktur', $id)->first();
        $data['detail'] = DB::table('penjualan_detail')
            ->join('barang', 'barang.kode_barang', '=', 'penjualan_detail.kode_barang')
            ->leftJoin('barang_satuan', function ($join) {
                $join->on('barang_satuan.kode_barang', '=', 'penjualan_detail.kode_barang')
                    ->on('barang_satuan.id', '=', 'penjualan_detail.satuan_id');
            })
            ->select(
                'penjualan_detail.*',
                'barang.nama_barang',
                'barang_satuan.satuan'
            )
            ->where('penjualan_detail.no_faktur', $id)
            ->get();
        $data['pembayaran'] = DB::table('penjualan_pembayaran as pp')
            ->leftJoin('hrd_karyawan as u', 'pp.kode_sales', '=', 'u.nik')
            ->select('pp.*', 'u.nama_lengkap as nama_sales')
            ->where('pp.no_faktur', $id)
            ->orderBy('pp.tanggal')
            ->get();

        $data['transfer'] = DB::table('penjualan_pembayaran_transfer as pt')
            ->leftJoin('hrd_karyawan as u', 'pt.kode_sales', '=', 'u.nik')
            ->select('pt.*', 'u.nama_lengkap as nama_sales')
            ->where('pt.no_faktur', $id)
            ->orderBy('pt.tanggal')
            ->get();

        $data['giro'] = DB::table('penjualan_pembayaran_giro as gr')
            ->leftJoin('hrd_karyawan as u', 'gr.kode_sales', '=', 'u.nik')
            ->select('gr.*', 'u.nama_lengkap as nama_sales')
            ->where('gr.no_faktur', $id)
            ->orderBy('gr.tanggal')
            ->get();

        $data['retur'] = DB::table('retur_penjualan_detail as d')
            ->leftJoin('retur_penjualan as r', 'r.no_retur', '=', 'd.no_retur')
            ->leftJoin('barang', 'barang.kode_barang', '=', 'd.kode_barang')
            ->leftJoin('barang_satuan', function ($join) {
                $join->on('barang_satuan.kode_barang', '=', 'd.kode_barang')
                    ->where('barang_satuan.isi', '=', 1);
            })
            ->select(
                'r.no_retur',
                'r.tanggal',
                'r.jenis_retur',
                'd.kode_barang',
                'barang.nama_barang',
                'd.qty',
                'barang_satuan.satuan',
                'd.harga_retur',
                'd.subtotal_retur'
            )
            ->where('r.no_faktur', $id)
            ->orderBy('r.tanggal')
            ->get();
        $data['potongFaktur'] = DB::table('retur_penjualan_detail as d')
            ->join('retur_penjualan as r', 'r.no_retur', '=', 'd.no_retur')
            ->where('r.no_faktur', $id)
            ->whereRaw('LOWER(r.jenis_retur) = "PF"')
            ->sum('d.subtotal_retur');

        return view('mobile.sfa.detailPenjualanMobile', $data);
    }

    public function storeReturMobile(Request $request)
    {
        DB::beginTransaction();
        try {
            $kode_sales = DB::table('penjualan')
                ->where('no_faktur', $request->no_faktur)
                ->value('kode_sales');

            // Generate no_retur
            $prefix = 'R' . date('ym');
            $last = DB::table('retur_penjualan')
                ->where('no_retur', 'like', "$prefix%")
                ->orderByDesc('no_retur')
                ->value('no_retur');

            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $no_retur = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            DB::table('retur_penjualan')->insert([
                'no_retur' => $no_retur,
                'tanggal' => Date('Y-m-d'),
                'jenis_retur' => $request->jenis_retur,
                'kode_pelanggan' => $request->kode_pelanggan,
                'kode_sales' => Auth::user()->nik,
                'no_faktur' => $request->no_faktur,
                'total' => $request->total,
                'keterangan' => $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $detail = json_decode($request->keranjang, true);

            foreach ($detail as $item) {
                $satuan_id = DB::table('barang_satuan')
                    ->where('kode_barang', $item['kode_barang'])
                    ->where('isi', 1)
                    ->value('id');

                DB::table('retur_penjualan_detail')->insert([
                    'no_retur' => $no_retur,
                    'kode_barang' => $item['kode_barang'],
                    'id_satuan' => $satuan_id,
                    'qty' => $item['qty'],
                    'harga_retur' => $item['harga'],
                    'subtotal_retur' => $item['qty'] * $item['harga'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $prefix = 'BM' . date('ym');
            $last = DB::table('mutasi_barang_masuk')
                ->where('kode_transaksi', 'like', "$prefix%")
                ->orderByDesc('kode_transaksi')
                ->value('kode_transaksi');

            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $kode_transaksi = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            DB::table('mutasi_barang_masuk')->insert([
                'kode_transaksi' => $kode_transaksi,
                'tanggal' => Date('Y-m-d'),
                'jenis_pemasukan' => 'retur_penjualan',
                'no_faktur' => $request->no_faktur,
                'sumber' => 'pelanggan',
                'keterangan' => 'Retur dari pelanggan: ' . $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($detail as $item) {
                $satuan_id = DB::table('barang_satuan')
                    ->where('kode_barang', $item['kode_barang'])
                    ->where('isi', 1)
                    ->value('id');

                DB::table('mutasi_barang_masuk_detail')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'no_faktur' => $request->no_faktur,
                    'satuan_id' => $satuan_id,
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit();
            return redirect()->route('viewDetailPelangganMobile', $request->kode_pelanggan)->with('success', 'Retur berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan retur: ' . $e->getMessage());
        }
    }

    public function updateFotoLokasiPelanggan(Request $request)
    {
        $request->validate([
            'kode_pelanggan' => 'required',
            'foto' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $kodePelanggan = $request->kode_pelanggan;
        $namaFile = $kodePelanggan . '.jpg';
        $path = storage_path('app/public/pelanggan/' . $namaFile);

        // Hapus file lama jika ada
        if (file_exists($path)) {
            unlink($path);
        }

        // Simpan foto baru dari base64
        $foto = $request->foto;
        file_put_contents($path, file_get_contents($foto));

        // Update data pelanggan
        DB::table('pelanggan')
            ->where('kode_pelanggan', $kodePelanggan)
            ->update([
                'foto' => $namaFile,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'updated_at' => now()
            ]);

        return redirect()->route('viewDetailPelangganMobile', $kodePelanggan)->with('success', 'Foto dan lokasi berhasil diperbarui.');
    }


    public function profileMobile($nik)
    {
        $karyawan = DB::table('hrd_karyawan')->where('nik', $nik)->first();

        if (!$karyawan) {
            abort(404, 'Data tidak ditemukan');
        }

        return view('mobile.sfa.profileMobile', compact('karyawan'));
    }


    public function createPengajuanLimitMobile()
    {
        $pelanggan = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();
        return view('mobile.sfa.createPengajuanLimitMobile', compact('pelanggan'));
    }

    public function storePengajuanLimitMobile(Request $request)
    {
        // Generate kode pengajuan
        $prefix = 'LK' . date('ym');
        $last = DB::table('pengajuan_limit_kredit')
            ->where('id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('id');

        if ($last) {
            $lastNumber = (int) substr($last, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Simpan pengajuan limit kredit
        DB::table('pengajuan_limit_kredit')->insert([
            'id' => $newId,
            'tanggal' => now(),
            'kode_pelanggan' => $request->kode_pelanggan,
            'nilai_pengajuan' => (int) preg_replace('/[^\d]/', '', $request->nilai_pengajuan),
            'alasan' => $request->alasan,
            'nik' => Auth::user()->nik,
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $nilaiPengajuan = (int) preg_replace('/[^\d]/', '', $request->nilai_pengajuan);

        $team = Auth::user()->team;
        $approvalUsers = [];

        $approvalUsers[] = [
            'user_id' => ($team === '25.01.004') ? '25.01.004' : '25.01.006',
            'level_approval' => 1,
        ];
        if ($nilaiPengajuan >= 2000000) {
            $approvalUsers[] = [
                'user_id' => '25.01.013',
                'level_approval' => 3,
            ];
        }

        // Masukkan data approval ke tabel
        $approvalData = [];
        foreach ($approvalUsers as $user) {
            $approvalData[] = [
                'jenis_pengajuan' => 'limit_kredit',
                'pengajuan_id' => $newId,
                'user_id' => $user['user_id'],
                'level_approval' => $user['level_approval'],
                'disetujui' => false,
                'ditolak' => false,
                'keterangan' => null,
                'tanggal_approval' => null,
                'approved_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('pengajuan_approvals')->insert($approvalData);

        return redirect()->route('limitKreditMobile')->with('success', 'Pengajuan limit berhasil ditambahkan.');
    }

    public function createPengajuanFakturMobile()
    {
        $pelanggan = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();
        return view('mobile.sfa.createPengajuanFakturMobile', compact('pelanggan'));
    }

    public function storePengajuanFakturMobile(Request $request)
    {
        $prefix = 'LF' . date('ym');
        $last = DB::table('pengajuan_limit_faktur')
            ->where('id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('id');

        if ($last) {
            $lastNumber = (int) substr($last, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        DB::table('pengajuan_limit_faktur')->insert([
            'id' => $newId,
            'kode_pelanggan' => $request->kode_pelanggan,
            'alasan' => $request->alasan,
            'jumlah_faktur' => (int) preg_replace('/[^\d]/', '', $request->jumlah_faktur),
            'status' => 'diajukan',
            'nik' => Auth::user()->nik,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $team = Auth::user()->team;
        $approvalUsers = [
            ['user_id' => ($team === '25.01.004') ? '25.01.004' : '25.01.006', 'level_approval' => 1],
            ['user_id' => '25.01.013', 'level_approval' => 2],
        ];

        $approvalData = [];
        foreach ($approvalUsers as $user) {
            $approvalData[] = [
                'jenis_pengajuan' => 'double_faktur',
                'pengajuan_id' => $newId,
                'user_id' => $user['user_id'],
                'level_approval' => $user['level_approval'],
                'disetujui' => false,
                'ditolak' => false,
                'keterangan' => null,
                'tanggal_approval' => null,
                'approved_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('pengajuan_approvals')->insert($approvalData);

        return redirect()->route('limitFakturMobile')->with('success', 'Pengajuan limit berhasil ditambahkan.');
    }

    public function approvePengajuanLimitMobile(Request $request, $id)
    {
        $approval = DB::table('pengajuan_approvals')->where('id', $id)->first();

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $aksi = $request->input('aksi');
        $keterangan = $request->input('keterangan');
        $revisi = str_replace(['Rp', '.', ' '], '', $request->input('revisi_limit'));

        $update = [
            'keterangan' => $keterangan,
            'tanggal_approval' => now(),
            'approved_by' => Auth::user()->nik,
            'updated_at' => now(),
        ];

        if ($aksi === 'setujui') {
            $update['disetujui'] = true;
            $update['revisi_limit'] = is_numeric($revisi) ? $revisi : null;
        } else {
            $update['ditolak'] = true;
        }

        DB::table('pengajuan_approvals')->where('id', $id)->update($update);

        $pengajuanId = $approval->pengajuan_id;

        $allApprovals = DB::table('pengajuan_approvals')
            ->where('pengajuan_id', $pengajuanId)
            ->where('jenis_pengajuan', 'limit_kredit')
            ->get();

        $isSemuaDisetujui = $allApprovals->every(fn($a) => $a->disetujui && !$a->ditolak);
        $hasDitolak = $allApprovals->contains('ditolak', true);

        if ($isSemuaDisetujui && !$hasDitolak) {
            // Ambil pengajuan dan revisi terakhir (level 4)
            $pengajuan = DB::table('pengajuan_limit_kredit')->where('id', $pengajuanId)->first();
            $revisiDirektur = $allApprovals->where('level_approval', 4)->first()->revisi_limit ?? null;
            $nilaiFinal = $revisiDirektur ?: $pengajuan->nilai_pengajuan;

            // Update pengajuan
            DB::table('pengajuan_limit_kredit')
                ->where('id', $pengajuanId)
                ->update([
                    'nilai_disetujui' => $nilaiFinal,
                    'status' => 'disetujui',
                    'updated_at' => now()
                ]);

            DB::table('pelanggan')
                ->where('kode_pelanggan', $pengajuan->kode_pelanggan)
                ->update([
                    'limit_pelanggan' => $nilaiFinal
                ]);
        } elseif ($hasDitolak) {
            DB::table('pengajuan_limit_kredit')
                ->where('id', $pengajuanId)
                ->update([
                    'status' => 'ditolak',
                    'updated_at' => now()
                ]);
        }

        return back()->with('success', 'Approval berhasil diproses.');
    }

    public function approvePengajuanFakturMobile(Request $request, $id)
    {
        $approval = DB::table('pengajuan_approvals')->where('id', $id)->first();

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $aksi = $request->input('aksi');
        $keterangan = $request->input('keterangan');
        $revisi = str_replace(['Rp', '.', ' '], '', $request->input('revisi_limit'));

        $update = [
            'keterangan' => $keterangan,
            'tanggal_approval' => now(),
            'approved_by' => Auth::user()->nik,
            'updated_at' => now(),
        ];

        if ($aksi === 'setujui') {
            $update['disetujui'] = true;
            $update['revisi_limit'] = is_numeric($revisi) ? $revisi : null;
        } else {
            $update['ditolak'] = true;
        }

        DB::table('pengajuan_approvals')->where('id', $id)->update($update);

        $pengajuanId = $approval->pengajuan_id;

        $allApprovals = DB::table('pengajuan_approvals')
            ->where('pengajuan_id', $pengajuanId)
            ->where('jenis_pengajuan', 'double_faktur')
            ->get();

        $isSemuaDisetujui = $allApprovals->every(fn($a) => $a->disetujui && !$a->ditolak);
        $hasDitolak = $allApprovals->contains('ditolak', true);

        if ($isSemuaDisetujui && !$hasDitolak) {
            // Ambil pengajuan dan revisi terakhir (level 4)
            $pengajuan = DB::table('pengajuan_limit_faktur')->where('id', $pengajuanId)->first();
            $revisiDirektur = $allApprovals->where('level_approval', 4)->first()->revisi_limit ?? null;
            $nilaiFinal = $revisiDirektur ?: $pengajuan->jumlah_faktur;

            DB::table('pengajuan_limit_faktur')
                ->where('id', $pengajuanId)
                ->update([
                    'jumlah_faktur' => $nilaiFinal,
                    'status' => 'disetujui',
                    'updated_at' => now()
                ]);

            DB::table('pelanggan')
                ->where('kode_pelanggan', $pengajuan->kode_pelanggan)
                ->update([
                    'limit_pelanggan' => $nilaiFinal
                ]);
        } elseif ($hasDitolak) {
            DB::table('pengajuan_limit_faktur')
                ->where('id', $pengajuanId)
                ->update([
                    'status' => 'ditolak',
                    'updated_at' => now()
                ]);
        }

        return back()->with('success', 'Approval berhasil diproses.');
    }

    public function getApprovalHistoryFakturMobil($kode_pengajuan)
    {
        try {
            $pengajuan = DB::table('pengajuan_limit_faktur')
                ->where('id', $kode_pengajuan)
                ->first();

            if (!$pengajuan) {
                return response()->json(['error' => 'Pengajuan tidak ditemukan'], 404);
            }

            $riwayatApproval = DB::table('pengajuan_approvals')
                ->leftJoin('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_approvals.user_id')
                ->where('pengajuan_approvals.pengajuan_id', $kode_pengajuan)
                ->select(
                    'pengajuan_approvals.*',
                    'hrd_karyawan.nama_lengkap as nama'
                )
                ->orderBy('pengajuan_approvals.level_approval')
                ->get();

            return response()->json([
                'success' => true,
                'riwayat_approval' => $riwayatApproval
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        // Cek apakah karyawan ada
        $karyawan = DB::table('users')->where('id', $id)->first();

        if (!$karyawan) {
            return back()->with('error', 'Karyawan tidak ditemukan.');
        }

        // Update password (pakai bcrypt/Hash)
        DB::table('users')->where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function storeFoto(Request $request, $nik)
    {
        $request->validate([
            'foto_karyawan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ambil data karyawan dari DB Table berdasarkan NIK
        $karyawan = DB::table('hrd_karyawan')->where('nik', $nik)->first();

        if (!$karyawan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan.'
            ], 404);
        }

        // Ambil ekstensi asli file (png/jpg/jpeg)
        $extension = $request->file('foto_karyawan')->getClientOriginalExtension();

        // Buat nama file persis seperti NIK
        $filename = $nik . '.' . $extension;

        // Simpan file ke storage, overwrite jika sudah ada
        $request->file('foto_karyawan')->storeAs('public/karyawan', $filename);

        // Update field foto_karyawan di DB
        DB::table('hrd_karyawan')->where('nik', $nik)->update([
            'foto_karyawan' => $filename
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Foto berhasil diunggah.',
            'new_foto_url' => asset('storage/karyawan/' . $filename)
        ]);
    }

    public function updateDataKaryawan(Request $request, $nik)
    {

        $update = DB::table('hrd_karyawan')->where('nik', $nik)->update([
            'nama_lengkap' => $request->nama_lengkap,
            'alamat' => $request->alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'email' => $request->email,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tgl_masuk' => $request->tgl_masuk,
            'status_karyawan' => $request->status_karyawan,
            'nomor_ktp' => $request->nomor_ktp,
            'npwp' => $request->npwp,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'nomor_rekening_bank' => $request->nomor_rekening_bank,
            'nama_bank' => $request->nama_bank,
            'status_pernikahan' => $request->status_pernikahan,
            'jumlah_anak' => $request->jumlah_anak,
            'catatan' => $request->catatan,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate.'
        ]);
    }
}
