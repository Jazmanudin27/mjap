<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PermissionHelper;

class PenjualanController extends Controller
{

    protected $kategori = 'penjualan';
    public function __construct()
    {
        $this->authorizePermission('penjualan');

        view()->share(PermissionHelper::userPermissions(
            'Edit Penjualan',
            'Delete Penjualan',
            'Tambah Penjualan'
        ));
    }
    public function index(Request $request)
    {
        $this->authorizePermission('penjualan');

        $no_faktur = $request->no_faktur;
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $kode_sales = $request->kode_sales;
        $jenis_transaksi = $request->jenis_transaksi;
        $tgl_dari = $request->tanggal_dari;
        $tgl_sampai = $request->tanggal_sampai;

        $data['penjualan'] = DB::table('penjualan as p')
            ->leftJoin('pelanggan as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->leftJoin('hrd_karyawan as sl', 'sl.nik', '=', 'p.kode_sales')

            ->leftJoin(DB::raw('(
            SELECT no_faktur, SUM(jumlah) AS bayar_tunai
            FROM penjualan_pembayaran
            GROUP BY no_faktur
        ) tunai'), 'tunai.no_faktur', '=', 'p.no_faktur')

            ->leftJoin(DB::raw('(
            SELECT no_faktur, SUM(jumlah) AS bayar_transfer
            FROM penjualan_pembayaran_transfer
            GROUP BY no_faktur
        ) transfer'), 'transfer.no_faktur', '=', 'p.no_faktur')

            ->leftJoin(DB::raw('(
            SELECT no_faktur, SUM(jumlah) AS bayar_giro
            FROM penjualan_pembayaran_giro
            GROUP BY no_faktur
        ) giro'), 'giro.no_faktur', '=', 'p.no_faktur')

            ->select(
                'p.*',
                'p.tanggal_kirim',
                'pl.nama_pelanggan',
                'sl.nik',
                'sl.nama_lengkap',
                DB::raw('COALESCE(tunai.bayar_tunai, 0) AS bayar_tunai'),
                DB::raw('COALESCE(transfer.bayar_transfer, 0) AS bayar_transfer'),
                DB::raw('COALESCE(giro.bayar_giro, 0) AS bayar_giro'),
                DB::raw('COALESCE(tunai.bayar_tunai,0) + COALESCE(transfer.bayar_transfer,0) + COALESCE(giro.bayar_giro,0) AS jumlah_bayar')
            )

            ->when($no_faktur, fn($q) => $q->where('p.no_faktur', 'like', "%$no_faktur%"))
            ->when($kode_pelanggan, fn($q) => $q->where('p.kode_pelanggan', $kode_pelanggan))
            ->when($nama_pelanggan, fn($q) => $q->where('pl.nama_pelanggan', 'like', "%$nama_pelanggan%"))
            ->when($kode_sales, fn($q) => $q->where('p.kode_sales', $kode_sales))
            ->when($jenis_transaksi, fn($q) => $q->where('p.jenis_transaksi', $jenis_transaksi))
            ->when($tgl_dari && $tgl_sampai, function ($q) use ($tgl_dari, $tgl_sampai) {
                $q->whereBetween('p.tanggal', [$tgl_dari, $tgl_sampai]);
            })

            ->orderByDesc('p.tanggal')
            ->orderByDesc('p.no_faktur')
            ->paginate(20)
            ->appends(request()->query());
        $data['sales'] = DB::table('hrd_karyawan')->where('id_jabatan', '1')->select('nik', 'nama_lengkap')->orderBy('nama_lengkap')->get();
        return view('penjualan.index', $data);
    }

    public function create()
    {
        $data['barang'] = DB::table('barang')
            ->leftJoin('barang_satuan', 'barang_satuan.kode_barang', 'barang.kode_barang')
            ->where('status', '1')
            ->get();
        $data['pelanggans'] = DB::table('pelanggan')->where('status', '1')->orderBy('nama_pelanggan')->get();
        $data['sales'] = DB::table('hrd_karyawan')->where('id_jabatan', '1')->where('status', '1')->orderBy('nama_lengkap')->get();
        return view('penjualan.create', $data);
    }

    public function edit($id)
    {
        $this->authorizePermission('penjualan');
        $data['penjualan'] = DB::table('penjualan as p')
            ->leftJoin('pelanggan  as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->leftJoin('wilayah    as w', 'w.kode_wilayah', '=', 'pl.kode_wilayah')
            ->leftJoin('users      as u', 'u.nik', '=', 'p.id_user')
            ->select([
                'p.no_faktur',
                'p.tanggal',
                'p.kode_pelanggan',
                'p.kode_sales',
                'p.total',
                'p.diskon',
                'p.grand_total',
                'p.jenis_transaksi',
                'p.jenis_bayar',
                'p.keterangan',
                'p.batal',
                'p.alasan_batal',
                'p.id_user',
                'p.created_at  as tgl_input',
                'p.updated_at  as tgl_update',
                'pl.nama_pelanggan',
                'pl.alamat_pelanggan',
                'pl.alamat_toko',
                'pl.no_hp_pelanggan',
                'pl.email',
                'pl.kepemilikan',
                'pl.omset_toko',
                'pl.limit_pelanggan',
                'pl.status      as status_pelanggan',
                'w.kode_wilayah',
                'w.nama_wilayah',
                'u.name as nama_user'
            ])
            ->where('p.no_faktur', $id)
            ->first();

        $data['pelangganEdit'] = [
            'id' => $data['penjualan']->kode_pelanggan,
            'text' => $data['penjualan']->kode_pelanggan . ' - ' . $data['penjualan']->nama_pelanggan,
            'kode_wilayah' => $data['penjualan']->kode_wilayah,
            'nama_wilayah' => $data['penjualan']->nama_wilayah,
            'limit_pelanggan' => $data['penjualan']->limit_pelanggan,
        ];
        $data['sales'] = DB::table('hrd_karyawan')->where('status', '1')->where('id_jabatan', '1')->orderBy('nama_lengkap')->get();
        $data['pelanggan'] = DB::table('pelanggan')->where('status', '1')->orderBy('nama_pelanggan')->get();
        $data['barang'] = DB::table('barang')->where('status', '1')->orderBy('nama_barang')->get();

        $data['detailItems'] = DB::table('penjualan_detail as d')
            ->leftJoin('barang as b', 'b.kode_barang', '=', 'd.kode_barang')
            ->leftJoin('barang_satuan as s', function ($q) {
                $q->on('s.id', '=', 'd.satuan_id');
            })
            ->where('d.no_faktur', $id)
            ->select([
                'd.id',
                'd.kode_barang',
                'b.nama_barang',
                's.satuan as nama_satuan',
                'd.satuan_id',
                'd.harga',
                'd.qty',
                DB::raw('d.harga * d.qty as subtotal'),
                'd.diskon1_persen',
                'd.diskon2_persen',
                'd.diskon3_persen',
                'd.diskon4_persen',
                'd.total_diskon',
                'd.total',
                'd.is_promo',
            ])
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'kode_barang' => $d->kode_barang,
                    'nama_barang' => $d->nama_barang,
                    'satuan' => $d->nama_satuan,
                    'satuan_id' => $d->satuan_id,
                    'harga_jual' => (int) $d->harga,
                    'harga_asli' => (int) $d->harga,
                    'jumlah' => (int) $d->qty,
                    'subtotal' => (int) $d->subtotal,
                    'diskon1_persen' => (float) $d->diskon1_persen,
                    'diskon2_persen' => (float) $d->diskon2_persen,
                    'diskon3_persen' => (float) $d->diskon3_persen,
                    'diskon4_persen' => (float) $d->diskon4_persen,
                    'total_diskon' => (int) $d->total_diskon,
                    'total' => (int) $d->total,
                    'is_promo' => (bool) $d->is_promo,
                ];
            });
        session(['redirect_penjualan_url' => url()->previous()]);
        return view('penjualan.edit', $data);
    }

    public function detail(string $id)
    {
        $data['sales'] = DB::table('hrd_karyawan')->where('status', '1')->where('id_jabatan', '1')->orderBy('nama_lengkap')->get();
        $data['penjualan'] = DB::table('penjualan')->where('no_faktur', $id)->first();
        $data['pelanggan'] = DB::table('pelanggan')
            ->leftJoin('wilayah', 'wilayah.kode_wilayah', 'pelanggan.kode_wilayah')
            ->where('kode_pelanggan', $data['penjualan']->kode_pelanggan)->first();
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
        return view('penjualan.detail', $data);
    }

    public function store(Request $request)
    {
        $keranjang = json_decode($request->keranjang, true) ?? [];
        if (empty($keranjang)) {
            return back()->with('error', 'Keranjang penjualan masih kosong!')->withInput();
        }

        DB::beginTransaction();
        try {
            // Tentukan mode: tambah atau edit
            $isEdit = $request->filled('no_faktur');
            $noFaktur = $isEdit ? $request->no_faktur : null;

            // Jika tambah, generate no_faktur baru
            if (!$isEdit) {
                $last = DB::table('penjualan')
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->orderByDesc('no_faktur')
                    ->value('no_faktur');

                $lastNumber = 1;
                if ($last && preg_match('/^(\d{4})-PJ-MJ-(\d{4})$/', $last, $matches)) {
                    $lastNumber = (int) $matches[1] + 1;
                }
                $noFaktur = str_pad($lastNumber, 4, '0', STR_PAD_LEFT) . '-PJ-MJ-' . now()->format('my');
            }

            // --- 1. Simpan/Update data utama penjualan ---
            $dataPenjualan = [
                'tanggal' => $request->tanggal,
                'kode_pelanggan' => $request->kode_pelanggan,
                'kode_sales' => $request->kode_sales,
                'jenis_transaksi' => $request->jenis_transaksi,
                'jenis_bayar' => $request->jenis_bayar,
                'keterangan' => $request->keterangan,
                'id_user' => $isEdit ? $request->kode_sales : Auth::user()->nik,
                'updated_at' => now(),
            ];

            if (!$isEdit) {
                $dataPenjualan['no_faktur'] = $noFaktur;
                $dataPenjualan['created_at'] = now();
                DB::table('penjualan')->insert($dataPenjualan);
            } else {
                DB::table('penjualan')
                    ->where('no_faktur', $noFaktur)
                    ->update($dataPenjualan);
            }


            // --- 2. Update detail penjualan: Hapus dulu, lalu insert baru (lebih aman) ---
            // Karena detail bisa berubah jumlah & jenis barang, lebih mudah hapus-insert
            DB::table('penjualan_detail')->where('no_faktur', $noFaktur)->delete();

            $totalKotor = $totalDiskonAll = $totalBersih = 0;
            foreach ($keranjang as $item) {
                $qty = (float) $item['jumlah'];
                $harga = (int) str_replace('.', '', $item['harga_jual']);
                $d1 = (float) ($item['diskon1_persen'] ?? 0);
                $d2 = (float) ($item['diskon2_persen'] ?? 0);
                $d3 = (float) ($item['diskon3_persen'] ?? 0);
                $d4 = (float) ($item['diskon4_persen'] ?? 0);
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

            // Update total di header
            DB::table('penjualan')->where('no_faktur', $noFaktur)->update([
                'total' => $totalKotor,
                'diskon' => $totalDiskonAll,
                'grand_total' => $totalBersih,
            ]);


            // --- 3. Update limit supplier (jika kredit) ---
            if ($request->jenis_transaksi === 'K') {
                // Kembalikan limit yang sebelumnya dipakai (jika edit)
                if ($isEdit) {
                    $oldSupplierTotals = DB::table('penjualan_detail')
                        ->join('barang', 'barang.kode_barang', '=', 'penjualan_detail.kode_barang')
                        ->where('penjualan_detail.no_faktur', $noFaktur)
                        ->groupBy('barang.kode_supplier')
                        ->select('barang.kode_supplier', DB::raw('SUM(penjualan_detail.total) as total'))
                        ->pluck('total', 'kode_supplier');

                    foreach ($oldSupplierTotals as $kodeSupplier => $totalDipakai) {
                        DB::table('pengajuan_limit_supplier')
                            ->where('kode_supplier', $kodeSupplier)
                            ->whereIn('pengajuan_id', function ($q) use ($request) {
                                $q->select('id')
                                    ->from('pengajuan_limit_kredit')
                                    ->where('kode_pelanggan', $request->kode_pelanggan);
                            })
                            ->increment('sisa_limit', $totalDipakai); // Kembalikan limit
                    }
                }

                // Potong limit baru
                $supplierTotals = [];
                foreach ($keranjang as $item) {
                    $kodeSupplier = $item['kode_supplier'] ?? null;
                    if (!$kodeSupplier)
                        continue;
                    $supplierTotals[$kodeSupplier] = ($supplierTotals[$kodeSupplier] ?? 0) + $item['total'];
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

            // --- 4. Update mutasi barang keluar ---
            $prefix = 'BK' . date('ym');
            $kodeTransaksi = null;

            if ($isEdit) {
                // Ambil kode_transaksi lama
                $kodeTransaksi = DB::table('mutasi_barang_keluar')
                    ->where('no_faktur', $noFaktur)
                    ->value('kode_transaksi');

                // Update header mutasi
                DB::table('mutasi_barang_keluar')
                    ->where('no_faktur', $noFaktur)
                    ->update([
                        'tanggal' => $request->tanggal,
                        'updated_at' => now(),
                    ]);

                // Hapus detail lama
                DB::table('mutasi_barang_keluar_detail')->where('kode_transaksi', $kodeTransaksi)->delete();
            } else {
                // Generate kode_transaksi baru
                $last = DB::table('mutasi_barang_keluar')
                    ->where('kode_transaksi', 'like', "$prefix%")
                    ->orderByDesc('kode_transaksi')
                    ->value('kode_transaksi');
                $lastNumber = $last ? (int) substr($last, -4) + 1 : 1;
                $kodeTransaksi = $prefix . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

                // Insert header mutasi baru
                DB::table('mutasi_barang_keluar')->insert([
                    'kode_transaksi' => $kodeTransaksi,
                    'tanggal' => $request->tanggal,
                    'jenis_pengeluaran' => 'penjualan',
                    'tujuan' => $request->kode_pelanggan,
                    'keterangan' => 'Pengeluaran karena penjualan',
                    'no_faktur' => $noFaktur,
                    'kode_pelanggan' => $request->kode_pelanggan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert detail mutasi baru
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

            // --- 5. Log dan commit ---
            $aksi = $isEdit ? 'Update Penjualan' : 'Tambah Penjualan';
            logActivity($aksi, "$noFaktur (Pelanggan: {$request->kode_pelanggan})");

            DB::commit();

            if ($isEdit) {
                $redirectUrl = session('redirect_penjualan_url', route('viewPenjualan'));
                return redirect($redirectUrl)->with('success', 'Penjualan berhasil diperbarui.');
            } else {
                return redirect()->route('viewPenjualan')->with('success', 'Penjualan berhasil disimpan.');
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan, data gagal disimpan.')->withInput();
        }
    }

    public static function generateNoBuktiGiro()
    {
        $bulan = date('m');
        $tahun = date('y');
        $prefix = 'GR' . $tahun . $bulan;

        $last = DB::table('penjualan_pembayaran_giro')
            ->where('kode_giro', 'like', $prefix . '%')
            ->orderBy('kode_giro', 'desc')
            ->value('kode_giro');

        $newNumber = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // Contoh: TF25060001
    }

    public static function generateNoBuktiTf()
    {
        $bulan = date('m');
        $tahun = date('y');
        $prefix = 'TF' . $tahun . $bulan;

        $last = DB::table('penjualan_pembayaran_transfer')
            ->where('kode_transfer', 'like', $prefix . '%')
            ->orderBy('kode_transfer', 'desc')
            ->value('kode_transfer');
        $newNumber = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateNoBukti()
    {
        $bulan = date('m');
        $tahun = date('Y');
        $prefix = $bulan . substr($tahun, 2, 2);

        $last = DB::table('penjualan_pembayaran')
            ->where('no_bukti', 'like', $prefix . '%')
            ->orderBy('no_bukti', 'desc')
            ->value('no_bukti');

        $newNumber = $last ? ((int) substr($last, 4)) + 1 : 1;

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // 06250001
    }
    public function delete($no_faktur)
    {
        try {
            DB::beginTransaction();

            $penjualan = DB::table('penjualan')->where('no_faktur', $no_faktur)->first();
            if (!$penjualan) {
                return redirect()->route('viewPenjualan')->with('error', 'Data penjualan tidak ditemukan.');
            }

            // Kembalikan sisa limit jika jenis transaksi Kredit
            if ($penjualan->jenis_transaksi === 'K') {
                $details = DB::table('penjualan_detail')
                    ->join('barang', 'barang.kode_barang', '=', 'penjualan_detail.kode_barang')
                    ->where('penjualan_detail.no_faktur', $no_faktur)
                    ->select('barang.kode_supplier', DB::raw('SUM(penjualan_detail.subtotal) as total'))
                    ->groupBy('barang.kode_supplier')
                    ->get();

                foreach ($details as $detail) {
                    DB::table('pengajuan_limit_supplier')
                        ->where('kode_supplier', $detail->kode_supplier)
                        ->update([
                            'sisa_limit' => DB::raw("sisa_limit + {$detail->total}")
                        ]);
                }
            }

            // DELETE Mutasi Barang Keluar Detail (safe if no data)
            DB::table('mutasi_barang_keluar_detail')
                ->whereIn('kode_transaksi', function ($query) use ($no_faktur) {
                    $query->select('kode_transaksi')
                        ->from('mutasi_barang_keluar')
                        ->where('no_faktur', $no_faktur);
                })->delete();

            // DELETE Mutasi Barang Keluar (safe if no data)
            DB::table('mutasi_barang_keluar')
                ->where('no_faktur', $no_faktur)
                ->delete();

            // DELETE Penjualan Detail (safe)
            DB::table('penjualan_detail')->where('no_faktur', $no_faktur)->delete();

            // DELETE Semua Pembayaran (safe even if data not exists)
            DB::table('penjualan_pembayaran')->where('no_faktur', $no_faktur)->delete();
            DB::table('penjualan_pembayaran_transfer')->where('no_faktur', $no_faktur)->delete();
            DB::table('penjualan_pembayaran_giro')->where('no_faktur', $no_faktur)->delete();

            // DELETE Penjualan (safe)
            DB::table('penjualan')->where('no_faktur', $no_faktur)->delete();

            logActivity('Delete Penjualan', "$no_faktur");

            DB::commit();
            return redirect()->route('viewPenjualan')->with('success', 'Data penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('viewPenjualan')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }




    public function getBarangPenjualan()
    {
        $barang = DB::table('barang')
            ->select('kode_barang', 'nama_barang')
            ->get();
        return response()->json($barang);
    }

    public function storePembayaranPenjualan(Request $request)
    {

        $periode = date('Y-m', strtotime($request->tanggal));
        if (isPeriodeTertutup($periode, $this->kategori)) {
            return response()->json([
                'success' => false,
                'message' => "Gagal, laporan {$this->kategori} periode $periode sudah ditutup."
            ], 400);
        }

        $ym = date('ym', strtotime($request->tanggal));
        $prefix = 'PJ' . $ym;
        $last = DB::table('penjualan_pembayaran')
            ->where('no_bukti', 'like', $prefix . '%')
            ->orderByDesc('no_bukti')
            ->first();

        DB::beginTransaction();
        try {
            if ($request->jenis_bayar === 'transfer') {
                DB::table('penjualan_pembayaran_transfer')->insert([
                    'kode_transfer' => $this->generateNoBuktiTf(),
                    'no_faktur' => $request->no_faktur,
                    'kode_pelanggan' => $request->kode_pelanggan,
                    'kode_sales' => $request->kode_sales,
                    'jumlah' => $request->jumlah,
                    'tanggal' => $request->tanggal,
                    'bank_pengirim' => $request->bank_pengirim,
                    'jenis_bayar' => 'titipan',
                    'status' => 'pending',
                    'keterangan' => $request->keterangan,
                    'id_user' => Auth::user()->nik,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                logActivity('Input Pembayaran transfer', $this->generateNoBukti());
            } else if ($request->jenis_bayar === 'giro') {
                DB::table('penjualan_pembayaran_giro')->insert([
                    'kode_giro' => $this->generateNoBuktiGiro(),
                    'no_faktur' => $request->no_faktur,
                    'kode_pelanggan' => $request->kode_pelanggan,
                    'kode_sales' => $request->kode_sales,
                    'jumlah' => $request->jumlah,
                    'tanggal' => $request->tanggal,
                    'bank_pengirim' => $request->bank_pengirim,
                    'jenis_bayar' => 'titipan',
                    'status' => 'pending',
                    'keterangan' => $request->keterangan,
                    'id_user' => Auth::user()->nik,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                logActivity('Input Pembayaran transfer', $this->generateNoBukti());
            } else {
                DB::table('penjualan_pembayaran')->insert([
                    'no_bukti' => $this->generateNoBukti(),
                    'no_faktur' => $request->no_faktur,
                    'kode_pelanggan' => $request->kode_pelanggan,
                    'kode_sales' => $request->kode_sales,
                    'jenis_bayar' => 'titipan',
                    'jumlah' => $request->jumlah,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'id_user' => Auth::user()->nik,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                logActivity('Input Pembayaran titipan', $this->generateNoBukti());
            }

            DB::commit();
            return response()->json(['success' => true, 'no_bukti' => $this->generateNoBukti()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updatePembayaranPenjualan(Request $request, string $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'kode_pelanggan' => 'required',
            'kode_sales' => 'required',
            'bank_pengirim' => 'required_if:jenis_bayar,transfer|max:30'
        ]);

        $periode = date('Y-m', strtotime($request->tanggal));
        if (isPeriodeTertutup($periode, $this->kategori)) {
            return response()->json([
                'success' => false,
                'message' => "Gagal, laporan {$this->kategori} periode $periode sudah ditutup."
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($request->jenis_bayar === 'transfer') {
                DB::table('penjualan_pembayaran_transfer')->updateOrInsert(
                    ['no_faktur' => $request->no_faktur],
                    [
                        'kode_pelanggan' => $request->kode_pelanggan,
                        'kode_sales' => $request->kode_sales,
                        'jumlah' => $request->jumlah,
                        'tanggal' => $request->tanggal,
                        'bank_pengirim' => $request->bank_pengirim,
                        'status' => 'pending',
                        'keterangan' => $request->keterangan,
                        'id_user' => Auth::user()->nik,
                        'updated_at' => now(),
                    ]
                );
            } else if ($request->jenis_bayar === 'giro') {
                DB::table('penjualan_pembayaran_giro')->updateOrInsert(
                    ['no_faktur' => $request->no_faktur],
                    [
                        'kode_pelanggan' => $request->kode_pelanggan,
                        'kode_sales' => $request->kode_sales,
                        'jumlah' => $request->jumlah,
                        'tanggal' => $request->tanggal,
                        'bank_pengirim' => $request->bank_pengirim,
                        'status' => 'pending',
                        'keterangan' => $request->keterangan,
                        'id_user' => Auth::user()->nik,
                        'updated_at' => now(),
                    ]
                );
            } else if ($request->jenis_bayar === 'voucher') {
                DB::table('penjualan_pembayaran')
                    ->where('no_bukti', $id)
                    ->update([
                        'tanggal' => $request->tanggal,
                        'no_faktur' => $request->no_faktur,
                        'kode_pelanggan' => $request->kode_pelanggan,
                        'kode_sales' => $request->kode_sales,
                        'jenis_bayar' => 'voucher',
                        'jumlah' => $request->jumlah,
                        'keterangan' => $request->keterangan,
                        'id_user' => Auth::user()->nik,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('penjualan_pembayaran')
                    ->where('no_bukti', $id)
                    ->update([
                        'tanggal' => $request->tanggal,
                        'no_faktur' => $request->no_faktur,
                        'kode_pelanggan' => $request->kode_pelanggan,
                        'kode_sales' => $request->kode_sales,
                        'jenis_bayar' => 'titipan',
                        'jumlah' => $request->jumlah,
                        'keterangan' => $request->keterangan,
                        'id_user' => Auth::user()->nik,
                        'updated_at' => now(),
                    ]);
                DB::table('penjualan_pembayaran_transfer')->where('no_faktur', $request->no_faktur)->delete();
            }

            logActivity('Update Pembayaran', $id);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // tampilkan error asli
            ], 500);
        }
    }

    private function authorizePermission(string $permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

    public function getPelanggan(Request $request)
    {
        $term = $request->kode_pelanggan;
        $subBayar = DB::table('penjualan as p')
            ->leftJoin(DB::raw('(
                SELECT no_faktur, SUM(jumlah) AS jumlah_tunai
                FROM penjualan_pembayaran
                GROUP BY no_faktur
            ) tunai'), 'tunai.no_faktur', '=', 'p.no_faktur')
            ->leftJoin(DB::raw('(
                SELECT no_faktur, SUM(jumlah) AS jumlah_transfer
                FROM penjualan_pembayaran_transfer
                GROUP BY no_faktur
            ) transfer'), 'transfer.no_faktur', '=', 'p.no_faktur')
            ->leftJoin(DB::raw('(
                SELECT no_faktur, SUM(jumlah) AS jumlah_giro
                FROM penjualan_pembayaran_giro
                GROUP BY no_faktur
            ) giro'), 'giro.no_faktur', '=', 'p.no_faktur')
            ->select(
                'p.no_faktur',
                DB::raw('
                    COALESCE(tunai.jumlah_tunai, 0) +
                    COALESCE(transfer.jumlah_transfer, 0) +
                    COALESCE(giro.jumlah_giro, 0)
                    AS total_bayar
                ')
            );

        $subPiutang = DB::table('penjualan')
            ->leftJoinSub($subBayar, 'bayar', function ($join) {
                $join->on('bayar.no_faktur', '=', 'penjualan.no_faktur');
            })
            ->select(
                'penjualan.kode_pelanggan',
                DB::raw('SUM(penjualan.grand_total - COALESCE(bayar.total_bayar, 0)) as sisa_piutang'),
                DB::raw('SUM(CASE WHEN COALESCE(bayar.total_bayar, 0) < penjualan.grand_total THEN 1 ELSE 0 END) as faktur_kredit'),
                DB::raw('COUNT(*) as total_faktur')
            )
            ->groupBy('penjualan.kode_pelanggan');

        // Ambil data pelanggan + wilayah + piutang
        $pelanggan = DB::table('pelanggan')
            ->select(
                'pelanggan.kode_pelanggan',
                'pelanggan.nama_pelanggan',
                'pelanggan.limit_pelanggan',
                'pelanggan.max_faktur',
                'wilayah.kode_wilayah',
                'wilayah.nama_wilayah',
                DB::raw('COALESCE(piutang.sisa_piutang,0) as sisa_piutang'),
                DB::raw('COALESCE(piutang.faktur_kredit,0) as faktur_kredit'),
                DB::raw('COALESCE(piutang.total_faktur,0) as total_faktur'),
                DB::raw('(pelanggan.limit_pelanggan - COALESCE(piutang.sisa_piutang,0)) as sisa_limit')
            )
            ->leftJoin('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->leftJoinSub($subPiutang, 'piutang', function ($join) {
                $join->on('piutang.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            })
            ->when($term, function ($q) use ($term) {
                $q->where('pelanggan.nama_pelanggan', 'like', "%{$term}%")
                    ->orWhere('pelanggan.kode_pelanggan', 'like', "%{$term}%");
            })
            ->orderBy('pelanggan.kode_pelanggan', 'ASC')
            ->orderBy('pelanggan.nama_pelanggan', 'ASC')
            ->limit(10)
            ->get();

        // Format hasil
        $results = $pelanggan->map(function ($p) {
            return [
                'id' => $p->kode_pelanggan,
                'text' => "{$p->kode_pelanggan} – {$p->nama_pelanggan} | {$p->nama_wilayah}",
                'limit_pelanggan' => round($p->limit_pelanggan),
                'sisa_piutang' => round($p->sisa_piutang),
                'sisa_limit' => round($p->sisa_limit),
                'faktur_kredit' => (int) $p->faktur_kredit,
                'total_faktur' => (int) $p->total_faktur,
                'max_faktur' => (int) $p->max_faktur,
                'kode_wilayah' => $p->kode_wilayah,
                'nama_wilayah' => $p->nama_wilayah,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function getBarang(Request $request)
    {
        $term = $request->q;
        $userKategori = Auth::user()->divisi;

        // Ambil bulan dan tahun saat ini (bisa diganti dari input jika perlu)
        $bulan = now()->month;
        $tahun = now()->year;

        $barang = DB::table('barang')
            ->select(
                'barang.kode_barang',
                'barang.nama_barang',
                'barang.kategori',
                'barang.merk',
                'barang.jenis',
                DB::raw('COALESCE(sa.saldo_awal, 0) +
                     COALESCE(masuk.pembelian, 0) +
                     COALESCE(masuk.repack, 0) +
                     COALESCE(masuk.retur_pengganti, 0) +
                     COALESCE(masuk.penyesuaian_masuk, 0) +
                     COALESCE(masuk.lainnya_masuk, 0) -
                     COALESCE(keluar.penjualan, 0) -
                     COALESCE(keluar.reject_gudang, 0) -
                     COALESCE(keluar.penyesuaian_keluar, 0) -
                     COALESCE(keluar.lainnya_keluar, 0) AS saldo_akhir')
            )
            // Join ke satuan dasar (untuk konsistensi stok)
            ->leftJoin('barang_satuan as bs', function ($join) {
                $join->on('barang.kode_barang', '=', 'bs.kode_barang')
                    ->where('bs.isi', 1); // satuan dasar
            })
            // Saldo Awal Bulan Ini
            ->leftJoin(
                DB::raw("(SELECT kode_barang, SUM(qty) AS saldo_awal FROM saldo_awal_gs WHERE bulan = {$bulan} AND tahun = {$tahun} GROUP BY kode_barang) AS sa"),
                'barang.kode_barang',
                '=',
                'sa.kode_barang'
            )
            // Pemasukan (Mutasi Masuk)
            ->leftJoin(
                DB::raw("(SELECT bs2.kode_barang,
                                   SUM(CASE WHEN mbm.jenis_pemasukan = 'Pembelian' THEN mbd.qty_konversi ELSE 0 END) AS pembelian,
                                   SUM(CASE WHEN mbm.jenis_pemasukan = 'Repack' THEN mbd.qty_konversi ELSE 0 END) AS repack,
                                   SUM(CASE WHEN mbm.jenis_pemasukan = 'Retur Pengganti' THEN mbd.qty_konversi ELSE 0 END) AS retur_pengganti,
                                   SUM(CASE WHEN mbm.jenis_pemasukan = 'Penyesuaian' THEN mbd.qty_konversi ELSE 0 END) AS penyesuaian_masuk,
                                   SUM(CASE WHEN mbm.jenis_pemasukan = 'Lainnya' THEN mbd.qty_konversi ELSE 0 END) AS lainnya_masuk
                            FROM mutasi_barang_masuk_detail mbd
                            JOIN mutasi_barang_masuk mbm ON mbm.kode_transaksi = mbd.kode_transaksi
                            JOIN barang_satuan bs2 ON bs2.id = mbd.satuan_id
                            WHERE mbm.kondisi = 'gs' AND MONTH(mbm.tanggal) = {$bulan} AND YEAR(mbm.tanggal) = {$tahun}
                            GROUP BY bs2.kode_barang) AS masuk"),
                'barang.kode_barang',
                '=',
                'masuk.kode_barang'
            )
            // Pengeluaran (Mutasi Keluar)
            ->leftJoin(
                DB::raw("(SELECT bs3.kode_barang,
                                   SUM(CASE WHEN mbk.jenis_pengeluaran = 'Penjualan' THEN mkd.qty_konversi ELSE 0 END) AS penjualan,
                                   SUM(CASE WHEN mbk.jenis_pengeluaran = 'Reject' THEN mkd.qty_konversi ELSE 0 END) AS reject_gudang,
                                   SUM(CASE WHEN mbk.jenis_pengeluaran = 'Penyesuaian' THEN mkd.qty_konversi ELSE 0 END) AS penyesuaian_keluar,
                                   SUM(CASE WHEN mbk.jenis_pengeluaran = 'Lainnya' THEN mkd.qty_konversi ELSE 0 END) AS lainnya_keluar
                            FROM mutasi_barang_keluar_detail mkd
                            JOIN mutasi_barang_keluar mbk ON mbk.kode_transaksi = mkd.kode_transaksi
                            JOIN barang_satuan bs3 ON bs3.id = mkd.satuan_id
                            WHERE mbk.kondisi = 'gs' AND MONTH(mbk.tanggal) = {$bulan} AND YEAR(mbk.tanggal) = {$tahun}
                            GROUP BY bs3.kode_barang) AS keluar"),
                'barang.kode_barang',
                '=',
                'keluar.kode_barang'
            )
            // Filter
            ->when($term, function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('barang.nama_barang', 'like', "%{$term}%")
                        ->orWhere('barang.kode_barang', 'like', "%{$term}%");
                });
            })
            ->when($userKategori, function ($query) use ($userKategori) {
                $query->where('barang.kategori', $userKategori);
            })
            ->where('barang.status', 1)
            // Hanya yang stoknya ada
            // ->having('saldo_akhir', '>', 0)
            // Urutkan & batasi
            ->orderBy('barang.nama_barang')
            ->limit(50)
            ->get();

        $results = $barang->map(function ($item) {
            // Ambil semua satuan barang terkait (dari besar ke kecil)
            $satuanList = DB::table('barang_satuan')
                ->where('kode_barang', $item->kode_barang)
                ->orderByDesc('isi')
                ->get();

            $qty = max(0, (float) $item->saldo_akhir);
            $konversiText = [];

            foreach ($satuanList as $satuan) {
                $jumlah = intdiv($qty, $satuan->isi); // ambil banyaknya satuan ini
                if ($jumlah > 0) {
                    $konversiText[] = $jumlah . ' ' . strtoupper($satuan->satuan);
                }
                $qty %= $satuan->isi; // sisanya dipakai untuk satuan berikutnya
            }

            return [
                'id' => $item->kode_barang,
                'text' => $item->nama_barang . ' (Stok: ' . implode(', ', $konversiText) . ')',
                'nama_barang' => $item->nama_barang,
                'kategori' => $item->kategori,
                'merk' => $item->merk,
                'saldo_akhir' => (float) $item->saldo_akhir,
                'stok_konversi' => implode(', ', $konversiText)
            ];
        });

        return response()->json(['results' => $results]);
    }

    // public function cekDiskon($kode_barang, $jumlah)
    // {
    //     $diskon = DB::table('diskon_barang')
    //         ->where('kode_barang', $kode_barang)
    //         ->where('minimal_qty', '<=', $jumlah)
    //         ->whereDate('berlaku_dari', '<=', now())
    //         ->whereDate('berlaku_sampai', '>=', now())
    //         ->orderByDesc('minimal_qty')
    //         ->first();

    //     return response()->json($diskon);
    // }

    public function getDiskonStrata($kode_barang, $jumlah, $tipe)
    {
        $diskon = DB::table('diskon_strata')
            ->where('kode_barang', $kode_barang)
            ->where('tipe_syarat', $tipe)
            ->where('syarat', '<=', $jumlah)
            ->orderByDesc('persentase')
            ->orderByDesc('syarat')
            ->select('id', 'kode_barang', 'persentase', 'syarat', 'tipe_syarat', 'created_at', 'updated_at')
            ->first();

        return response()->json($diskon);
    }

    public function batalFaktur(Request $request, $no_faktur)
    {
        $penjualan = DB::table('penjualan')->where('no_faktur', $no_faktur)->first();

        if (!$penjualan) {
            return response()->json(['message' => 'Faktur tidak ditemukan.'], 404);
        }

        if ($penjualan->batal) {
            return response()->json(['message' => 'Faktur sudah pernah dibatalkan.'], 400);
        }

        DB::beginTransaction();

        try {
            if ($penjualan->jenis_transaksi === 'K') {
                $details = DB::table('penjualan_detail')
                    ->join('barang', 'barang.kode_barang', '=', 'penjualan_detail.kode_barang')
                    ->where('penjualan_detail.no_faktur', $no_faktur)
                    ->select('barang.kode_supplier', DB::raw('SUM(penjualan_detail.subtotal) as total'))
                    ->groupBy('barang.kode_supplier')
                    ->get();

                foreach ($details as $detail) {
                    DB::table('pengajuan_limit_supplier')
                        ->where('kode_supplier', $detail->kode_supplier)
                        ->update([
                            'sisa_limit' => DB::raw("sisa_limit + {$detail->total}")
                        ]);
                }
            }

            DB::table('mutasi_barang_keluar_detail')
                ->whereIn('kode_transaksi', function ($query) use ($no_faktur) {
                    $query->select('kode_transaksi')
                        ->from('mutasi_barang_keluar')
                        ->where('no_faktur', $no_faktur);
                })->delete();

            DB::table('mutasi_barang_keluar')
                ->where('no_faktur', $no_faktur)
                ->delete();

            DB::table('penjualan')
                ->where('no_faktur', $no_faktur)
                ->update([
                    'batal' => true,
                    'alasan_batal' => $request->alasan,
                    'updated_at' => now()
                ]);

            logActivity('Batal Faktur Penjualan', $no_faktur);

            DB::commit();

            return response()->json(['message' => 'Faktur berhasil dibatalkan dan data mutasi dihapus.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Terjadi kesalahan saat membatalkan faktur.'], 500);
        }
    }

    public function storePembayaranTransfer(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required',
            'kode_pelanggan' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        $filePath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        DB::table('penjualan_pembayaran_transfer')->insert([
            'no_faktur' => $request->no_faktur,
            'kode_pelanggan' => $request->kode_pelanggan,
            'kode_sales' => $request->kode_sales,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'bank_pengirim' => $request->bank_pengirim,
            'status' => 'pending',
            'id_sales' => Auth::user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Data pembayaran berhasil disimpan. Menunggu verifikasi.');
    }

    public function verifikasiPembayaranTransfer($id, Request $request)
    {
        $status = $request->status;
        $keterangan = $request->keterangan;

        DB::table('penjualan_pembayaran_transfer')->where('id', $id)->update([
            'status' => $status,
            'keterangan' => $keterangan,
            'id_verifikasi' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }

    public function deletePembayaranPenjualanTransfer(string $id)
    {
        DB::beginTransaction();
        try {
            DB::table('penjualan_pembayaran_transfer')->where('kode_transfer', $id)->delete();
            logActivity('Hapus Pembayaran Transfer', $id);
            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus pembayaran.');
        }
    }
    public function deletePembayaranPenjualan(string $id)
    {
        DB::beginTransaction();
        try {
            DB::table('penjualan_pembayaran')->where('no_bukti', $id)->delete();
            logActivity('Hapus Pembayaran', $id);
            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus pembayaran.');
        }
    }

    public function cetakFaktur1(string $id)
    {

        $data['penjualan'] = DB::table('penjualan')
            ->leftJoin('pelanggan', 'pelanggan.kode_pelanggan', '=', 'penjualan.kode_pelanggan')
            ->leftJoin('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->leftJoin('hrd_karyawan as sales', 'sales.nik', '=', 'penjualan.kode_sales')
            ->leftJoin('hrd_karyawan as user', 'user.nik', '=', 'penjualan.id_user')
            ->where('penjualan.no_faktur', $id)
            ->select(
                'penjualan.*',
                'pelanggan.nama_pelanggan',
                'pelanggan.alamat_toko as alamat_toko',
                'pelanggan.alamat_pelanggan as alamat_pelanggan',
                'wilayah.nama_wilayah',
                'sales.nama_lengkap as nama_sales',
                'user.nama_lengkap as nama_user'
            )
            ->first();
        $data['detail'] = DB::table('penjualan_detail')
            ->leftJoin('barang_satuan', 'penjualan_detail.satuan_id', 'barang_satuan.id')
            ->leftJoin('barang', 'barang.kode_barang', 'barang_satuan.kode_barang')
            ->where('no_faktur', $id)
            ->orderBy('barang.kode_barang')
            ->get();
        DB::table('penjualan')
            ->where('no_faktur', $id)
            ->increment('cetak');
        $data['grandTotal'] = $data['faktur']->total ?? 0;
        $data['totalDiskon'] = $data['detail']->sum('total_diskon');
        $data['jumlahBayar'] = $faktur->jumlah_bayar ?? 0;
        $data['sisaBayar'] = $data['grandTotal'] - $data['jumlahBayar'];

        return view('penjualan.cetakFaktur1', $data);
    }
    public function viewKirimanSales(Request $request)
    {
        $tanggal = $request->tanggal;
        $wilayah = DB::table('wilayah as w')
            ->select('w.kode_wilayah', 'w.nama_wilayah')
            ->get();

        $data = DB::table('penjualan_kiriman as pk')
            ->join('penjualan as p', 'p.no_faktur', '=', 'pk.no_faktur')
            ->join('wilayah as w', 'w.kode_wilayah', '=', 'pk.kode_wilayah')
            ->select(
                'pk.tanggal',
                'pk.kirimanke',
                'w.kode_wilayah',
                'w.nama_wilayah',
                DB::raw('SUM(p.grand_total) as total')
            )
            ->when($request->tanggal, fn($q) => $q->where('pk.tanggal', '=', $request->tanggal))
            ->when($request->kode_wilayah, fn($q) => $q->where('pk.kode_wilayah', $request->kode_wilayah))
            ->when($request->kirimanke, fn($q) => $q->where('pk.kirimanke', $request->kirimanke))
            ->groupBy('pk.tanggal', 'w.kode_wilayah', 'w.nama_wilayah', 'pk.kirimanke')
            ->orderBy('pk.tanggal', 'desc')
            ->orderBy('w.nama_wilayah')
            ->limit('20')
            ->get();

        return view('penjualan.viewKirimanSales', compact('wilayah', 'data'));
    }

    public function deleteGroupKirimanSales(Request $request)
    {
        $tanggal = $request->tanggal;
        $kodeWilayah = $request->kode_wilayah;

        $kiriman = DB::table('penjualan_kiriman')
            ->where('tanggal', $tanggal)
            ->where('kode_wilayah', $kodeWilayah)
            ->get();

        if (!$kiriman) {
            return response()->json(['message' => 'Data kiriman tidak ditemukan.'], 404);
        }

        $noFakturList = $kiriman->pluck('no_faktur');

        DB::table('penjualan')
            ->whereIn('no_faktur', $noFakturList)
            ->update([
                'tanggal_kirim' => null,
                'updated_at' => now()
            ]);

        DB::table('mutasi_barang_keluar')
            ->whereIn('no_faktur', $noFakturList)
            ->update([
                'tanggal_dikirim' => null,
                'updated_at' => now()
            ]);
        // Delete semua kiriman dengan tanggal & wilayah tersebut
        DB::table('penjualan_kiriman')
            ->where('tanggal', $tanggal)
            ->where('kode_wilayah', $kodeWilayah)
            ->delete();

        return response()->json(['status' => 'success']);
    }
    public function cetakKirimanGudang(Request $request)
    {
        $kodeWilayah = $request->kode_wilayah;
        $kirimanKe = $request->kirimanke;
        $tanggal = $request->tanggal;

        $kiriman = DB::table('penjualan_kiriman as pk')
            ->join('penjualan as p', 'p.no_faktur', '=', 'pk.no_faktur')
            ->join('pelanggan as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->join('hrd_karyawan as s', 's.nik', '=', 'p.kode_sales')
            ->join('wilayah as w', 'w.kode_wilayah', '=', 'pk.kode_wilayah')
            ->when($tanggal, function ($q) use ($tanggal) {
                return $q->where('pk.tanggal', $tanggal);
            })
            ->when($kodeWilayah, function ($q) use ($kodeWilayah) {
                return $q->where('pk.kode_wilayah', $kodeWilayah);
            })
            ->when($kirimanKe, function ($q) use ($kirimanKe) {
                return $q->where('pk.kirimanke', $kirimanKe);
            })
            ->select(
                'pk.tanggal',
                'pk.no_faktur',
                'pl.nama_pelanggan',
                's.nama_lengkap as nama_sales',
                'w.nama_wilayah',
                'p.grand_total'
            )
            ->orderBy('pl.nama_pelanggan', 'asc')
            ->get();

        // Ambil detail barang per faktur
        $details = DB::table('penjualan_detail as pd')
            ->join('barang_satuan as bs', 'bs.id', '=', 'pd.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->join('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier')
            ->whereIn('pd.no_faktur', $kiriman->pluck('no_faktur'))
            ->select(
                'pd.no_faktur',
                's.kode_supplier',
                's.nama_supplier',
                'b.kode_barang',
                'b.nama_barang',
                'bs.satuan',
                'pd.qty',
                'pd.harga'
            )
            ->orderBy('b.nama_barang', 'asc')
            ->get()
            ->groupBy('no_faktur')
            ->map(function ($group) {
                return $group->sortBy('nama_barang');
            });


        $groupedDetails = [];

        foreach ($details as $no_faktur => $items) {
            foreach ($items as $item) {
                $key = $item->kode_barang;

                if (!isset($groupedDetails[$key])) {
                    $groupedDetails[$key] = [
                        'kode_barang' => $item->kode_barang,
                        'nama_barang' => $item->nama_barang,
                        'satuan' => [],
                    ];
                }

                if (!isset($groupedDetails[$key]['satuan'][$item->satuan])) {
                    $groupedDetails[$key]['satuan'][$item->satuan] = 0;
                }

                $groupedDetails[$key]['satuan'][$item->satuan] += $item->qty;
            }
        }

        $data['groupedDetails'] = collect($groupedDetails)
            ->values()
            ->sortBy('nama_barang');

        $data['tanggal'] = $tanggal;
        $data['wilayah'] = DB::table('wilayah')->where('kode_wilayah', $kodeWilayah)->first();
        $data['kiriman'] = $kiriman;
        $data['detail'] = $details;

        return view('penjualan.cetakKirimanGudang', $data);
    }


    public function deleteDetailPenjualan($id)
    {
        try {
            DB::table('penjualan_detail')->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function cetakKirimanSales(Request $request)
    {
        $kode_wilayah = $request->kode_wilayah;
        $kirimanke = $request->kirimanke;
        $data['tanggal'] = $request->tanggal;
        $data['wilayah'] = DB::table('wilayah')->where('kode_wilayah', $kode_wilayah)->first();
        $data['data'] = DB::table('penjualan_kiriman as pk')
            ->join('penjualan as p', 'p.no_faktur', '=', 'pk.no_faktur')
            ->join('pelanggan as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->join('hrd_karyawan as s', 's.nik', '=', 'p.kode_sales')
            ->join('wilayah as w', 'w.kode_wilayah', '=', 'pk.kode_wilayah')
            ->select(
                'p.tanggal',
                'pk.no_faktur',
                'pl.nama_pelanggan',
                's.nama_lengkap as nama_sales',
                'w.nama_wilayah',
                'p.grand_total'
            )
            ->when($request->tanggal, fn($q) => $q->where('pk.tanggal', '>=', $request->tanggal))
            ->when($request->kode_wilayah, fn($q) => $q->where('pk.kode_wilayah', $request->kode_wilayah))
            ->when($request->kirimanke, fn($q) => $q->where('pk.kirimanke', $request->kirimanke))
            ->orderBy('pl.nama_pelanggan', 'asc')
            ->get();

        return view('penjualan.cetakKirimanSales', $data);
    }

    public function createKirimanSales()
    {
        $wilayah = DB::table('wilayah')->get();
        return view('penjualan.createKirimanSales', compact('wilayah'));
    }

    public function getFakturByWilayah(Request $request, $kode_wilayah)
    {
        $tanggal_dari = $request->tanggal_dari;
        $tanggal_sampai = $request->tanggal_sampai;

        $data = DB::table('penjualan as faktur')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', '=', 'faktur.kode_pelanggan')
            ->join('hrd_karyawan', 'hrd_karyawan.nik', '=', 'faktur.kode_sales')
            ->join('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->select(
                'faktur.*',
                'pelanggan.nama_pelanggan',
                'hrd_karyawan.nama_lengkap as nama_sales',
                'faktur.tanggal',
                'wilayah.nama_wilayah'
            )
            ->where('faktur.batal', '0')
            ->when($kode_wilayah && $kode_wilayah !== 'null', function ($query) use ($kode_wilayah) {
                $query->where('pelanggan.kode_wilayah', $kode_wilayah);
            })
            ->when($tanggal_dari && $tanggal_sampai, function ($query) use ($tanggal_dari, $tanggal_sampai) {
                $query->whereBetween('faktur.tanggal', [$tanggal_dari, $tanggal_sampai]);
            })
            ->whereNotIn('faktur.no_faktur', function ($sub) {
                $sub->select('no_faktur')->from('penjualan_kiriman');
            })
            ->get()
            ->map(function ($item) {
                $item->tanggal = tanggal_indo2($item->tanggal);
                return $item;
            });

        return response()->json($data);
    }


    public function storeKirimanSales(Request $request)
    {
        // DB::beginTransaction();
        // try {
        $tanggal = $request->tanggal;
        $kodeWilayah = $request->kode_wilayah_pengiriman;
        $kirimanke = $request->kirimanke;

        foreach ($request->items as $item) {
            // Cek apakah data sudah ada di penjualan_kiriman
            $exists = DB::table('penjualan_kiriman')
                ->where('tanggal', $tanggal)
                ->where('kirimanke', $kirimanke)
                ->where('no_faktur', $item['no_faktur'])
                ->exists();

            // Update or Insert penjualan_kiriman
            if ($exists) {
                DB::table('penjualan_kiriman')
                    ->where('tanggal', $tanggal)
                    ->where('no_faktur', $item['no_faktur'])
                    ->update([
                        'kode_wilayah' => $kodeWilayah,
                        'kirimanke' => $kirimanke,
                        'keterangan' => null,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('penjualan_kiriman')->insert([
                    'tanggal' => $tanggal,
                    'no_faktur' => $item['no_faktur'],
                    'kode_wilayah' => $kodeWilayah,
                    'kirimanke' => $kirimanke,
                    'keterangan' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update tanggal_kirim di tabel penjualan
            DB::table('penjualan')
                ->where('no_faktur', $item['no_faktur'])
                ->update([
                    'tanggal_kirim' => $tanggal,
                    'updated_at' => now()
                ]);

            // Update tanggal_kirim di tabel mutasi_barang_keluar
            DB::table('mutasi_barang_keluar')
                ->where('no_faktur', $item['no_faktur'])
                ->update([
                    'tanggal_dikirim' => $tanggal,
                    'updated_at' => now()
                ]);
        }

        DB::commit();
        return redirect()->route('viewKirimanSales')->with('success', 'Kiriman berhasil disimpan / diperbarui!');
        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'Gagal menyimpan kiriman: ' . $e->getMessage());
        // }
    }

    public function deleteKirimanSales($id)
    {
        // Ambil data kiriman berdasarkan ID
        $kiriman = DB::table('penjualan_kiriman')->where('id', $id)->first();

        if (!$kiriman) {
            return response()->json(['message' => 'Data kiriman tidak ditemukan.'], 404);
        }

        DB::table('penjualan_kiriman')->where('id', $id)->delete();

        DB::table('penjualan')
            ->where('no_faktur', $kiriman->no_faktur)
            ->update([
                'tanggal_kirim' => null,
                'updated_at' => now()
            ]);

        DB::table('mutasi_barang_keluar')
            ->where('no_faktur', $kiriman->no_faktur)
            ->update([
                'tanggal_kirim' => null,
                'updated_at' => now()
            ]);
        return response()->json(['message' => 'Data berhasil dihapus dan tanggal_kirim dikosongkan.']);
    }

    public function trackingSales(Request $request)
    {
        $kode_sales = $request->kode_sales;
        $kode_pelanggan = $request->kode_pelanggan;
        $tanggal = $request->tanggal;

        $data['logs'] = DB::table('penjualan_checkin')
            ->leftJoin('pelanggan', 'pelanggan.kode_pelanggan', 'penjualan_checkin.kode_pelanggan')
            ->leftJoin('wilayah as w', 'w.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->leftJoin('hrd_karyawan as sales', 'sales.nik', 'penjualan_checkin.kode_sales') // gunakan alias "sales"
            ->when($kode_sales, function ($query) use ($kode_sales) {
                return $query->where('penjualan_checkin.kode_sales', 'LIKE', "%$kode_sales%");
            })
            ->when($kode_pelanggan, function ($query) use ($kode_pelanggan) {
                return $query->where('penjualan_checkin.kode_pelanggan', 'LIKE', "%$kode_pelanggan%");
            })
            ->whereDate('penjualan_checkin.tanggal', $tanggal)
            ->orderByDesc('checkin')
            ->select('penjualan_checkin.*', 'pelanggan.nama_pelanggan', 'sales.nama_lengkap as nama_sales', 'w.nama_wilayah') // tambahkan kolom yang diambil
            ->get();

        return view('penjualan.trackingSales', $data);
    }

    public function getDiskonStrataSemua($kode_barang, $qty, $nominal)
    {
        $diskon = DB::table('diskon_strata')
            ->where(function ($q) use ($kode_barang, $qty, $nominal) {
                $q->where('kode_barang', $kode_barang)
                    ->where(function ($sub) use ($qty, $nominal) {
                        $sub->where('tipe_syarat', 'qty')
                            ->where('syarat', '<=', $qty);
                    })
                    ->orWhere(function ($sub) use ($nominal) {
                        $sub->where('tipe_syarat', 'nominal')
                            ->where('syarat', '<=', $nominal);
                    });

                // Diskon per supplier
                $q->orWhereNull('kode_barang')
                    ->where('kode_supplier', '!=', '')
                    ->where(function ($sub) use ($nominal) {
                    $sub->where('tipe_syarat', 'nominal')
                        ->where('syarat', '<=', $nominal);
                });
            })
            ->orderBy('persentase', 'desc')
            ->get();

        return response()->json($diskon);
    }

    public function getDiskonStrataSupplier($kode_supplier, $jumlah, $tipe = 'nominal')
    {
        $diskon = DB::table('diskon_strata')
            ->where('kode_supplier', $kode_supplier)
            ->where('jenis_diskon', 'supplier')
            ->where('tipe_syarat', $tipe)
            ->where('syarat', '<=', $jumlah) // total belanja >= syarat
            ->orderByDesc('persentase') // ambil yang diskonnya paling besar
            ->first();

        if ($diskon) {
            return response()->json([
                'active' => true,
                'persentase' => (float) $diskon->persentase,
                'syarat' => (float) $diskon->syarat,
            ]);
        }

        return response()->json([
            'active' => false,
            'persentase' => 0,
            'syarat' => 0,
        ]);
    }

}
