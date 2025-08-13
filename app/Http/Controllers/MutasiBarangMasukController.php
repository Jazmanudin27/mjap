<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PermissionHelper;
use RupiahHelper;

class MutasiBarangMasukController extends Controller
{
    protected string $kategori = 'gudang';

    public function __construct()
    {
        $this->authorizePermission('Mutasi Masuk');

        view()->share(PermissionHelper::userPermissions(
            'Edit Mutasi Masuk',
            'Delete Mutasi Masuk',
            'Tambah Mutasi Masuk',
            'Barang Masuk Pembelian'
        ));
    }

    public function index(Request $request)
    {
        $kode_transaksi = $request->kode_transaksi;
        $no_faktur = $request->no_faktur;
        $sumber = $request->sumber;
        $tgl_dari = $request->tanggal_dari;
        $tgl_sampai = $request->tanggal_sampai;
        $kondisi = $request->kondisi; // gs / bs
        $jenis_pemasukan = $request->jenis_pemasukan; // Repack, Retur Pengganti, dll

        $data['mutasi'] = DB::table('mutasi_barang_masuk')
            ->when($kode_transaksi, function ($q) use ($kode_transaksi) {
                return $q->where('kode_transaksi', 'like', "%$kode_transaksi%");
            })
            ->when($no_faktur, function ($q) use ($no_faktur) {
                return $q->where('no_faktur', 'like', "%$no_faktur%");
            })
            ->when($sumber, function ($q) use ($sumber) {
                return $q->where('sumber', 'like', "%$sumber%");
            })
            ->when($kondisi, function ($q) use ($kondisi) {
                return $q->where('kondisi', $kondisi); // 'gs' atau 'bs'
            })
            ->when($jenis_pemasukan, function ($q) use ($jenis_pemasukan) {
                return $q->where('jenis_pemasukan', $jenis_pemasukan);
            })
            ->when($tgl_dari && $tgl_sampai, function ($q) use ($tgl_dari, $tgl_sampai) {
                return $q->whereBetween('tanggal', [$tgl_dari, $tgl_sampai]);
            })
            ->orderByDesc('tanggal')
            ->paginate(20)
            ->appends($request->query()); // Pertahankan semua parameter filter

        return view('mutasi_masuk.index', $data);
    }
    public function create()
    {
        $data['supplier'] = DB::table('supplier')->orderBy('nama_supplier')->get();
        return view('mutasi_masuk.create', $data);
    }

    public function edit($kode_transaksi)
    {
        $transaksi = DB::table('mutasi_barang_masuk')->where('kode_transaksi', $kode_transaksi)->first();
        $detail = DB::table('mutasi_barang_masuk_detail as d')
            ->leftJoin('barang_satuan as s', 'd.satuan_id', '=', 's.id')
            ->leftJoin('barang as b', 'b.kode_barang', '=', 's.kode_barang')
            ->where('d.kode_transaksi', $kode_transaksi)
            ->select(
                'b.kode_barang',
                'b.nama_barang',
                's.satuan',
                'd.satuan_id',
                'd.qty'
            )
            ->get();
        return view('mutasi_masuk.edit', compact('transaksi', 'detail'));
    }

    public function storeTerimaBarang(Request $request)
    {
        // dd($request->all());
        // DB::beginTransaction();
        // try {
        $kodeTransaksi = $request->kode_transaksi;

        // update header mutasi_barang_masuk (tanggal diterima)
        DB::table('mutasi_barang_masuk')
            ->where('kode_transaksi', $kodeTransaksi)
            ->update([
                'tanggal_diterima' => $request->tanggal,
                'updated_at' => now()
            ]);

        // ambil repack (default array kosong)
        $repack = $request->input('repack', []);

        $repackDetails = [];

        foreach ($repack as $detailId => $repackQty) {
            $repackQty = intval($repackQty);
            $detail = DB::table('mutasi_barang_masuk_detail')->where('id', $detailId)->first();

            if (!$detail)
                continue;

            $returQty = intval($detail->qty);
            // safety clamp
            if ($repackQty < 0)
                $repackQty = 0;
            if ($repackQty > $returQty)
                $repackQty = $returQty;

            $badQty = $returQty - $repackQty;

            if ($badQty > 0) {
                // Update qty untuk sisa retur/bad stock
                DB::table('mutasi_barang_masuk_detail')
                    ->where('id', $detailId)
                    ->update([
                        'qty' => $badQty,
                        'qty_konversi' => $badQty * $detail->konversi,
                    ]);
            }

            // repackQty masuk ke transaksi baru
            if ($repackQty > 0) {
                $repackDetails[] = [
                    'satuan_id' => $detail->satuan_id,
                    'qty' => $repackQty,
                    'konversi' => $detail->konversi,
                    'qty_konversi' => $repackQty * $detail->konversi,
                ];
            }
        }

        // jika ada repack, buat transaksi barang keluar (BK...)
        if (count($repackDetails) > 0) {
            $prefix = 'BK' . date('ym');
            $last = DB::table('mutasi_barang_masuk')
                ->where('kode_transaksi', 'like', "$prefix%")
                ->orderByDesc('kode_transaksi')
                ->value('kode_transaksi');
            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $newKodeTransaksi = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            DB::table('mutasi_barang_masuk')->insert([
                'kode_transaksi' => $newKodeTransaksi,
                'tanggal' => $request->tanggal,
                'jenis_pemasukan' => 'repack',
                'keterangan' => 'Auto Repack from ' . $kodeTransaksi,
                'kondisi' => 'gs',
                'tanggal_diterima' => $request->tanggal,
            ]);

            foreach ($repackDetails as $item) {
                DB::table('mutasi_barang_masuk_detail')->insert([
                    'kode_transaksi' => $newKodeTransaksi,
                    'satuan_id' => $item['satuan_id'],
                    'qty' => $item['qty'],
                    'konversi' => $item['konversi'],
                    'qty_konversi' => $item['qty_konversi'],
                ]);
            }
        }

        DB::commit();
        return back()->with('success', 'Barang berhasil dikirim & Repack dipisahkan.');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        // }
    }
    public function update(Request $request, $kode_transaksi)
    {
        DB::beginTransaction();
        try {
            // Update header mutasi
            DB::table('mutasi_barang_masuk')
                ->where('kode_transaksi', $kode_transaksi)
                ->update([
                    'tanggal' => $request->tanggal,
                    'jenis_pemasukan' => $request->jenis_pemasukan,
                    'no_faktur' => $request->no_faktur,
                    'sumber' => $request->sumber,
                    'keterangan' => $request->keterangan,
                    'updated_at' => now(),
                ]);

            // Hapus detail lama
            DB::table('mutasi_barang_masuk_detail')
                ->where('kode_transaksi', $kode_transaksi)
                ->delete();

            // Tambah detail baru
            $detail = json_decode($request->keranjang, true);

            foreach ($detail as $item) {
                $barangSatuan = DB::table('barang_satuan')
                    ->where('id', $item['satuan_id'])
                    ->first();
                $konversi = $barangSatuan->isi ?? 1;
                $qty = $item['jumlah'];
                $qty_konversi = $qty * $konversi;
                DB::table('mutasi_barang_masuk_detail')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'satuan_id' => $item['satuan_id'] ?? Null,
                    'qty' => $item['qty'],
                    'konversi' => $konversi,
                    'qty_konversi' => $qty_konversi,
                ]);
            }

            DB::commit();
            return redirect()->route('viewMutasiBarangMasuk')->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function detail(string $kode_transaksi)
    {
        $data['barangmasuk'] = DB::table('mutasi_barang_masuk')
            ->where('kode_transaksi', $kode_transaksi)
            ->first();

        $data['detail'] = DB::table('mutasi_barang_masuk_detail')
            ->join('barang_satuan', 'mutasi_barang_masuk_detail.satuan_id', '=', 'barang_satuan.id')
            ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->select(
                'mutasi_barang_masuk_detail.*',
                'barang.kode_barang',
                'barang.nama_barang',
                'barang_satuan.satuan'
            )
            ->where('mutasi_barang_masuk_detail.kode_transaksi', $kode_transaksi)
            ->get();

        return view('mutasi_masuk.detail', $data);
    }

    public function store(Request $request)
    {

        // dd($request->all());
        DB::beginTransaction();
        try {
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
                'jenis_pemasukan' => $request->jenis_pemasukan,
                'no_faktur' => $request->no_faktur,
                'kondisi' => $request->kondisi,
                'sumber' => '',
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
                DB::table('mutasi_barang_masuk_detail')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'satuan_id' => $item['satuan_id'] ?? Null,
                    'qty' => $item['qty'],
                    'konversi' => $konversi,
                    'qty_konversi' => $qty_konversi,
                ]);
            }

            DB::commit();
            return redirect()->route('viewMutasiBarangMasuk')->with('success', 'Barang masuk berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }


    public function delete($kode_transaksi)
    {
        DB::beginTransaction();
        try {
            DB::table('mutasi_barang_masuk_detail')->where('kode_transaksi', $kode_transaksi)->delete();
            DB::table('mutasi_barang_masuk')->where('kode_transaksi', $kode_transaksi)->delete();

            DB::commit();
            return redirect()->route('viewMutasiBarangMasuk')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
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
