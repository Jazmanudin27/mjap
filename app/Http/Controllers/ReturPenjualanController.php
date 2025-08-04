<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PermissionHelper;
use RupiahHelper;

class ReturPenjualanController extends Controller
{
    protected string $kategori = 'penjualan';
    public function __construct()
    {
        $this->authorizePermission('retur penjualan');

        view()->share(PermissionHelper::userPermissions(
            'Edit Retur Penjualan',
            'Delete Retur Penjualan',
            'Tambah Retur Penjualan'
        ));
    }
    public function index(Request $request)
    {
        $no_retur = $request->no_retur;
        $no_faktur = $request->no_faktur;
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $jenis_retur = $request->jenis_retur;
        $tgl_dari = $request->tanggal_dari;
        $tgl_sampai = $request->tanggal_sampai;

        $data['retur'] = DB::table('retur_penjualan as r')
            ->leftJoin('pelanggan as p', 'p.kode_pelanggan', '=', 'r.kode_pelanggan')
            ->leftJoin('penjualan as j', 'j.no_faktur', '=', 'r.no_faktur')
            ->leftJoin('hrd_karyawan as k', 'k.nik', '=', 'r.kode_sales') // JOIN sales
            ->select(
                'r.*',
                'p.nama_pelanggan',
                'j.tanggal as tanggal_faktur',
                'k.nama_lengkap as nama_sales', // SELECT nama sales
                'k.nik' // SELECT nama sales
            )
            ->when($no_retur, fn($q) => $q->where('r.no_retur', 'like', "%$no_retur%"))
            ->when($no_faktur, fn($q) => $q->where('r.no_faktur', $no_faktur))
            ->when($kode_pelanggan, fn($q) => $q->where('r.kode_pelanggan', $kode_pelanggan))
            ->when($nama_pelanggan, fn($q) => $q->where('p.nama_pelanggan', 'like', "%$nama_pelanggan%"))
            ->when($jenis_retur, fn($q) => $q->where('r.jenis_retur', $jenis_retur))
            ->when($tgl_dari && $tgl_sampai, fn($q) => $q->whereBetween('r.tanggal', [$tgl_dari, $tgl_sampai]))
            ->orderByDesc('r.tanggal')
            ->paginate(10)
            ->appends(request()->query());

        return view('retur_penjualan.index', $data);
    }

    public function create()
    {
        $data['penjualan'] = DB::table('penjualan')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', 'penjualan.kode_pelanggan')
            ->get();
        $data['pelanggan'] = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();
        return view('retur_penjualan.create', $data);
    }

    public function edit($no_retur)
    {
        $retur = DB::table('retur_penjualan')->where('no_retur', $no_retur)->first();

        $detail = DB::table('retur_penjualan_detail')
            ->leftJoin('barang', 'barang.kode_barang', '=', 'retur_penjualan_detail.kode_barang')
            ->leftJoin('barang_satuan', function ($join) {
                $join->on('barang_satuan.kode_barang', '=', 'retur_penjualan_detail.kode_barang')
                    ->where('barang_satuan.isi', 1); // ambil satuan utama (misalnya PCS)
            })
            ->where('retur_penjualan_detail.no_retur', $no_retur)
            ->select(
                'retur_penjualan_detail.*',
                'barang.nama_barang',
                'barang_satuan.satuan as nama_satuan' // alias supaya jelas
            )
            ->get();

        return view('retur_penjualan.edit', compact('retur', 'detail'));
    }

    public function update(Request $request, $no_retur)
    {
        DB::beginTransaction();
        try {
            DB::table('retur_penjualan')->where('no_retur', $no_retur)->update([
                'tanggal' => $request->tanggal,
                'jenis_retur' => $request->jenis_retur,
                'kode_pelanggan' => $request->kode_pelanggan,
                'no_faktur' => $request->no_faktur,
                'total' => $request->total,
                'keterangan' => $request->keterangan,
                'updated_at' => now(),
            ]);

            DB::table('retur_penjualan_detail')->where('no_retur', $no_retur)->delete();

            $detail = json_decode($request->keranjang, true);
            foreach ($detail as $item) {
                DB::table('retur_penjualan_detail')->insert([
                    'no_retur' => $no_retur,
                    'kode_barang' => $item['kode_barang'],
                    'qty' => $item['qty'],
                    'harga_retur' => $item['harga'],
                    'subtotal_retur' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('viewReturPenjualan')->with('success', 'Retur berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update retur: ' . $e->getMessage());
        }
    }

    public function detail(string $id)
    {
        $data['retur'] = DB::table('retur_penjualan as r')
            ->leftJoin('pelanggan as p', 'p.kode_pelanggan', '=', 'r.kode_pelanggan')
            ->leftJoin('hrd_karyawan as k', 'k.nik', '=', 'r.kode_sales')
            ->select('r.*', 'p.nama_pelanggan', 'p.alamat_pelanggan', 'p.no_hp_pelanggan', 'k.nama_lengkap as nama_sales')
            ->where('r.no_retur', $id)
            ->first();

        $data['detail'] = DB::table('retur_penjualan_detail as d')
            ->join('barang as b', 'b.kode_barang', '=', 'd.kode_barang')
            ->select('d.*', 'b.nama_barang')
            ->where('d.no_retur', $id)
            ->get();

        return view('retur_penjualan.detail', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $kode_sales = DB::table('penjualan')
                ->where('no_faktur', $request->no_faktur)
                ->value('kode_sales');

            // Generate no_retur
            $prefix = 'RP' . date('ym');
            $last = DB::table('retur_penjualan')
                ->where('no_retur', 'like', "$prefix%")
                ->orderByDesc('no_retur')
                ->value('no_retur');

            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $no_retur = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            DB::table('retur_penjualan')->insert([
                'no_retur' => $no_retur,
                'tanggal' => $request->tanggal,
                'jenis_retur' => $request->jenis_retur,
                'kode_pelanggan' => $request->kode_pelanggan,
                'kode_sales' => $kode_sales,
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
                    'subtotal_retur' => $item['subtotal'],
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
                'tanggal' => $request->tanggal,
                'jenis_pemasukan' => 'Retur Penjualan',
                'no_faktur' => $request->no_faktur,
                'sumber' => 'pelanggan',
                'kondisi' => 'bs',
                'keterangan' => 'Retur dari pelanggan: ' . $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($detail as $item) {
                $barangSatuan = DB::table('barang_satuan')
                    ->where('kode_barang', $item['kode_barang'])
                    ->where('isi', 1)
                    ->first();
                $konversi = $barangSatuan->isi ?? 1;
                $qty = $item['qty'];
                $qty_konversi = $qty * $konversi;
                DB::table('mutasi_barang_masuk_detail')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'no_faktur' => $request->no_faktur,
                    'satuan_id' => $barangSatuan->id,
                    'qty' => $item['qty'],
                    'konversi' => $konversi,
                    'qty_konversi' => $qty_konversi,
                ]);
            }

            DB::commit();
            return redirect()->route('viewReturPenjualan')->with('success', 'Retur berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan retur: ' . $e->getMessage());
        }
    }

    public function getFakturByPelanggan($kode)
    {
        return DB::table('penjualan')
            ->where('kode_pelanggan', $kode)
            ->where('batal', 0)
            ->select('no_faktur')
            ->get();
    }

    public function getDetailFakturPenjualan($no)
    {
        $penjualan = DB::table('penjualan')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', '=', 'penjualan.kode_pelanggan')
            ->where('no_faktur', $no)
            ->first();

        $detail = DB::table('penjualan_detail')
            ->join('barang_satuan', 'barang_satuan.id', '=', 'penjualan_detail.satuan_id')
            ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->where('no_faktur', $no)
            ->select('penjualan_detail.*', 'barang.nama_barang')
            ->get();

        return response()->json([
            'penjualan' => $penjualan,
            'detail' => $detail
        ]);
    }

    public function delete($no_retur)
    {
        DB::beginTransaction();
        try {
            DB::table('retur_penjualan_detail')->where('no_retur', $no_retur)->delete();
            DB::table('retur_penjualan')->where('no_retur', $no_retur)->delete();

            DB::commit();
            return redirect()->route('viewReturPenjualan')->with('success', 'Data retur berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus retur: ' . $e->getMessage());
        }
    }
    private function authorizePermission(string $permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
}
