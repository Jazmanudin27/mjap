<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PermissionHelper;

class MutasiBarangKeluarController extends Controller
{
    protected string $kategori = 'gudang';

    public function __construct()
    {
        $this->authorizePermission('Mutasi Keluar');

        view()->share(PermissionHelper::userPermissions(
            'Edit Mutasi Keluar',
            'Delete Mutasi Keluar',
            'Tambah Mutasi Keluar'
        ));
    }

    public function index(Request $request)
    {
        $kode_transaksi = $request->kode_transaksi;
        $no_faktur = $request->no_faktur;
        $pelanggan = $request->kode_pelanggan;
        $tujuan = $request->tujuan;
        $tgl_dari = $request->tanggal_dari;
        $tgl_sampai = $request->tanggal_sampai;
        $wilayah = $request->wilayah;

        $data['list_wilayah'] = DB::table('wilayah')->orderBy('nama_wilayah')->pluck('nama_wilayah', 'kode_wilayah');
        $data['mutasi'] = DB::table('mutasi_barang_keluar')
            ->leftJoin('penjualan', 'mutasi_barang_keluar.no_faktur', '=', 'penjualan.no_faktur')
            ->leftJoin('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin('wilayah', 'pelanggan.kode_wilayah', '=', 'wilayah.kode_wilayah')
            ->when($kode_transaksi, fn($q) => $q->where('mutasi_barang_keluar.kode_transaksi', 'like', "%$kode_transaksi%"))
            ->when($no_faktur, fn($q) => $q->where('penjualan.no_faktur', 'like', "%$no_faktur%"))
            ->when($pelanggan, fn($q) => $q->where('penjualan.kode_pelanggan', $pelanggan))
            ->when($tujuan, fn($q) => $q->where('mutasi_barang_keluar.tujuan', 'like', "%$tujuan%"))
            ->when($wilayah, fn($q) => $q->where('wilayah.nama_wilayah', 'like', "%$wilayah%"))
            ->when($tgl_dari && $tgl_sampai, fn($q) => $q->whereBetween('mutasi_barang_keluar.tanggal', [$tgl_dari, $tgl_sampai]))
            ->select(
                'mutasi_barang_keluar.*',
                'penjualan.no_faktur',
                'penjualan.jenis_transaksi',
                'pelanggan.nama_pelanggan',
                'wilayah.nama_wilayah'
            )
            ->orderByDesc('mutasi_barang_keluar.tanggal')
            ->paginate(10)
            ->appends($request->query());

        return view('mutasi_keluar.index', $data);
    }

    public function create()
    {
        $data['barang'] = DB::table('barang')->orderBy('nama_barang')->get();
        $data['satuan'] = DB::table('barang_satuan')->orderBy('satuan')->get();
        return view('mutasi_keluar.create', $data);
    }

    public function edit($kode_transaksi)
    {
        $transaksi = DB::table('mutasi_barang_keluar')->where('kode_transaksi', $kode_transaksi)->first();

        $detail = DB::table('mutasi_barang_keluar_detail as d')
            ->leftJoin('barang_satuan as bs', 'bs.id', '=', 'd.satuan_id')
            ->leftJoin('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->where('d.kode_transaksi', $kode_transaksi)
            ->select(
                'b.kode_barang',
                'b.nama_barang',
                'd.satuan_id',
                'bs.satuan',
                'd.qty'
            )
            ->get();

        return view('mutasi_keluar.edit', compact('transaksi', 'detail'));
    }

    public function storeKirimBarang(Request $request)
    {
        try {
            DB::table('mutasi_barang_keluar')
                ->where('kode_transaksi', $request->kode_transaksi)
                ->update([
                    'tanggal_dikirim' => $request->tanggal,
                    'catatan' => $request->keterangan,
                    'updated_at' => now()
                ]);

            return back()->with('success', 'Barang berhasil diterima dan dicatat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $prefix = 'BK' . date('ym');
            $last = DB::table('mutasi_barang_keluar')
                ->where('kode_transaksi', 'like', "$prefix%")
                ->orderByDesc('kode_transaksi')
                ->value('kode_transaksi');

            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $kode_transaksi = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            DB::table('mutasi_barang_keluar')->insert([
                'kode_transaksi' => $kode_transaksi,
                'tanggal' => $request->tanggal,
                'jenis_pengeluaran' => $request->jenis_pengeluaran,
                'kondisi' => $request->kondisi,
                'no_faktur' => $request->no_faktur ?? Null,
                'kode_pelanggan' => $request->kode_pelanggan ?? Null,
                'tujuan' => '',
                'keterangan' => $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $detail = json_decode($request->keranjang, true);

            foreach ($detail as $item) {
                $barangSatuan = DB::table('barang_satuan')
                    ->where('id', $item['satuan_id'])
                    ->first();
                $konversi = $barangSatuan->isi ?? 1;
                $qty = $item['qty'];
                $qty_konversi = $qty * $konversi;
                DB::table('mutasi_barang_keluar_detail')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'satuan_id' => $item['satuan_id'] ?? null,
                    'qty' => $item['qty'],
                    'konversi' => $konversi,
                    'qty_konversi' => $qty_konversi,
                ]);
            }

            DB::commit();
            return redirect()->route('viewMutasiBarangKeluar')->with('success', 'Barang keluar berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $kode_transaksi)
    {
        DB::beginTransaction();
        try {
            DB::table('mutasi_barang_keluar')
                ->where('kode_transaksi', $kode_transaksi)
                ->update([
                    'tanggal' => $request->tanggal,
                    'tujuan' => $request->tujuan,
                    'keterangan' => $request->keterangan,
                    'updated_at' => now(),
                ]);

            DB::table('mutasi_barang_keluar_detail')
                ->where('kode_transaksi', $kode_transaksi)
                ->delete();

            $detail = json_decode($request->keranjang, true);
            foreach ($detail as $item) {
                DB::table('mutasi_barang_keluar_detail')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'kode_barang' => $item['kode_barang'],
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit();
            return redirect()->route('viewMutasiBarangKeluar')->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function detail(string $kode_transaksi)
    {
        $data['barangkeluar'] = DB::table('mutasi_barang_keluar')
            ->where('kode_transaksi', $kode_transaksi)
            ->first();

        $data['detail'] = DB::table('mutasi_barang_keluar_detail')
            ->join('barang_satuan', 'barang_satuan.id', '=', 'mutasi_barang_keluar_detail.satuan_id')
            ->leftJoin('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->select(
                'mutasi_barang_keluar_detail.*',
                'barang.nama_barang',
                'barang.kode_barang',
                'barang_satuan.satuan'
            )
            ->where('mutasi_barang_keluar_detail.kode_transaksi', $kode_transaksi)
            ->get();

        return view('mutasi_keluar.detail', $data);
    }

    public function delete($kode_transaksi)
    {
        DB::beginTransaction();
        try {
            DB::table('mutasi_barang_keluar_detail')->where('kode_transaksi', $kode_transaksi)->delete();
            DB::table('mutasi_barang_keluar')->where('kode_transaksi', $kode_transaksi)->delete();

            DB::commit();
            return redirect()->route('viewMutasiBarangKeluar')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function authorizePermission(string $permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
}
