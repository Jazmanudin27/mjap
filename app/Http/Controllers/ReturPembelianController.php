<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PermissionHelper;
use RupiahHelper;

class ReturPembelianController extends Controller
{
    protected string $kategori = 'pembelian';

    public function __construct()
    {
        $this->authorizePermission('retur pembelian');

        view()->share(PermissionHelper::userPermissions(
            'Edit Retur Pembelian',
            'Delete Retur Pembelian',
            'Tambah Retur Pembelian'
        ));
    }

    public function index(Request $request)
    {
        $data['retur'] = DB::table('retur_pembelian as r')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'r.kode_supplier')
            ->leftJoin('pembelian as p', 'p.no_faktur', '=', 'r.no_faktur')
            ->select('r.*', 's.nama_supplier', 'p.tanggal as tanggal_faktur')
            ->when($request->no_retur, fn($q) => $q->where('r.no_retur', 'like', "%{$request->no_retur}%"))
            ->when($request->no_faktur, fn($q) => $q->where('r.no_faktur', $request->no_faktur))
            ->when($request->kode_supplier, fn($q) => $q->where('r.kode_supplier', $request->kode_supplier))
            ->when($request->jenis_retur, fn($q) => $q->where('r.jenis_retur', $request->jenis_retur))
            ->when($request->tanggal_dari && $request->tanggal_sampai, fn($q) => $q->whereBetween('r.tanggal', [$request->tanggal_dari, $request->tanggal_sampai]))
            ->orderByDesc('r.tanggal')
            ->paginate(10)
            ->appends(request()->query());

        return view('retur_pembelian.index', $data);
    }

    public function create()
    {
        $data['pembelian'] = DB::table('pembelian')
            ->join('supplier', 'supplier.kode_supplier', 'pembelian.kode_supplier')
            ->get();
        $data['supplier'] = DB::table('supplier')->orderBy('nama_supplier')->get();
        return view('retur_pembelian.create', $data);
    }

    public function edit($no_retur)
    {
        // Ambil data retur utama
        $retur = DB::table('retur_pembelian')
            ->where('no_retur', $no_retur)
            ->first();

        if (!$retur) {
            return redirect()->route('viewReturPembelian')->with('error', 'Data retur tidak ditemukan.');
        }

        // Ambil detail retur
        $detailRetur = DB::table('retur_pembelian_detail as rd')
            ->join('barang as b', 'rd.kode_barang', '=', 'b.kode_barang')
            ->select('rd.*', 'b.nama_barang')
            ->where('rd.no_retur', $no_retur)
            ->get();

        // Ambil semua supplier untuk dropdown
        $supplierList = DB::table('supplier')
            ->select('kode_supplier', 'nama_supplier')
            ->orderBy('nama_supplier')
            ->get();

        // Ambil daftar faktur berdasarkan supplier terkait
        $fakturList = DB::table('pembelian')
            ->select('no_faktur')
            ->where('kode_supplier', $retur->kode_supplier)
            ->orderByDesc('tanggal')
            ->get();

        return view('retur_pembelian.edit', [
            'retur' => $retur,
            'detail' => $detailRetur,
            'suppliers' => $supplierList,
            'fakturs' => $fakturList,
        ]);
    }

    public function update(Request $request, $no_retur)
    {
        DB::beginTransaction();
        try {
            DB::table('retur_pembelian')->where('no_retur', $no_retur)->update([
                'tanggal' => $request->tanggal,
                'jenis_retur' => $request->jenis_retur,
                'kode_supplier' => $request->kode_supplier,
                'no_faktur' => $request->no_faktur,
                'total' => $request->total,
                'keterangan' => $request->keterangan,
                'updated_at' => now(),
            ]);

            DB::table('retur_pembelian_detail')->where('no_retur', $no_retur)->delete();

            $detail = json_decode($request->keranjang, true);
            foreach ($detail as $item) {
                DB::table('retur_pembelian_detail')->insert([
                    'no_retur' => $no_retur,
                    'kode_barang' => $item['kode_barang'],
                    'qty' => $item['qty'],
                    'satuan_id' => $item['id_satuan'],
                    'harga_retur' => $item['harga'],
                    'subtotal_retur' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('viewReturPembelian')->with('success', 'Retur berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update retur: ' . $e->getMessage());
        }
    }

    public function detail(string $id)
    {
        $data['retur'] = DB::table('retur_pembelian as r')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'r.kode_supplier')
            ->select('r.*', 's.nama_supplier', 's.alamat', 's.no_hp')
            ->where('r.no_retur', $id)
            ->first();

        $data['detail'] = DB::table('retur_pembelian_detail as d')
            ->join('barang as b', 'b.kode_barang', '=', 'd.kode_barang')
            ->select('d.*', 'b.nama_barang')
            ->where('d.no_retur', $id)
            ->get();

        return view('retur_pembelian.detail', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $prefix = 'RB' . date('ym');
            $last = DB::table('retur_pembelian')->where('no_retur', 'like', "$prefix%")
                ->orderByDesc('no_retur')->value('no_retur');
            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $no_retur = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            DB::table('retur_pembelian')->insert([
                'no_retur' => $no_retur,
                'tanggal' => $request->tanggal,
                'jenis_retur' => $request->jenis_retur,
                'kode_supplier' => $request->kode_supplier,
                'no_faktur' => $request->no_faktur,
                'total' => RupiahHelper::parse($request->total),
                'keterangan' => $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $detail = json_decode($request->keranjang, true);
            foreach ($detail as $item) {
                DB::table('retur_pembelian_detail')->insert([
                    'no_retur' => $no_retur,
                    'kode_barang' => $item['kode_barang'],
                    'satuan_id' => $item['satuan_id'] ?? null,
                    'qty' => $item['qty'],
                    'harga_retur' => $item['harga'],
                    'subtotal_retur' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert ke mutasi_barang_keluar
            $prefixBM = 'BK' . date('ym');
            $lastBM = DB::table('mutasi_barang_keluar')->where('kode_transaksi', 'like', "$prefixBM%")
                ->orderByDesc('kode_transaksi')->value('kode_transaksi');
            $nextBM = $lastBM ? ((int) substr($lastBM, -4)) + 1 : 1;
            $kode_transaksi = $prefixBM . str_pad($nextBM, 4, '0', STR_PAD_LEFT);

            DB::table('mutasi_barang_keluar')->insert([
                'kode_transaksi' => $kode_transaksi,
                'tanggal' => $request->tanggal,
                'jenis_pengeluaran' => 'Retur Pembelian',
                'no_faktur' => $no_retur,
                'kode_pelanggan' => $request->kode_supplier,
                'kondisi' => 'bs',
                'tujuan' => $request->kode_supplier,
                'keterangan' => 'Retur ke supplier: ' . $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
            return redirect()->route('viewReturPembelian')->with('success', 'Retur pembelian berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur pembelian: ' . $e->getMessage());
        }
    }

    public function delete($no_retur)
    {
        DB::beginTransaction();
        try {

            DB::table('retur_pembelian_detail')->where('no_retur', $no_retur)->delete();
            DB::table('retur_pembelian')->where('no_retur', $no_retur)->delete();

            DB::commit();
            return redirect()->route('viewReturPembelian')->with('success', 'Data retur berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus retur: ' . $e->getMessage());
        }
    }

    public function getFakturBySupplier($kode)
    {
        return DB::table('pembelian')
            ->where('kode_supplier', $kode)
            ->select('no_faktur')
            ->limit(3)
            ->orderBy('tanggal', 'DESC')
            ->get();
    }

    public function getDetailFakturPembelian($no)
    {
        $pembelian = DB::table('pembelian')
            ->join('supplier', 'supplier.kode_supplier', '=', 'pembelian.kode_supplier')
            ->where('no_faktur', $no)
            ->first();

        $detail = DB::table('pembelian_detail')
            ->join('barang_satuan', 'barang_satuan.id', '=', 'pembelian_detail.satuan_id')
            ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->where('no_faktur', $no)
            ->select('pembelian_detail.*', 'barang.nama_barang')
            ->get();

        return response()->json([
            'pembelian' => $pembelian,
            'detail' => $detail
        ]);
    }

    private function authorizePermission(string $permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
}
