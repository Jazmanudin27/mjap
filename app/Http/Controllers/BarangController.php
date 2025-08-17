<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('barang');

        $data['PermissionShow'] = Permission::getPermission('Show Barang', Auth::user()->role_id);
        $data['PermissionDelete'] = Permission::getPermission('Delete Barang', Auth::user()->role_id);
        $data['PermissionTambah'] = Permission::getPermission('Tambah Barang', Auth::user()->role_id);
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier', 'ASC')->get();
        $query = DB::table('barang')
            ->leftJoin('supplier', 'supplier.kode_supplier', '=', 'barang.kode_supplier')
            ->select('barang.*', 'supplier.nama_supplier');

        $query->when($request->nama_barang, fn($q, $v) => $q->where('barang.nama_barang', 'like', "%$v%"));
        $query->when($request->kode_barang, fn($q, $v) => $q->where('barang.kode_barang', 'like', "%$v%"));
        $query->when($request->supplier, fn($q, $v) => $q->where('barang.kode_supplier', $v));
        $query->when($request->jenis, fn($q, $v) => $q->where('barang.jenis', $v));
        if ($request->filled('status')) {
            $query->where('barang.status', $request->status);
        }
        $data['barang'] = $query->orderBy('barang.nama_barang')
            ->paginate(20)
            ->appends(request()->query());

        return view('barang.index', $data);
    }

    public function create()
    {
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier', 'ASC')->get();
        return view('barang.create', $data);
    }

    public function store(Request $request)
    {
        // Ambil kode terakhir
        $lastKode = DB::table('barang')->orderByDesc('kode_barang')->value('kode_barang');
        if ($lastKode) {
            $lastNumber = (int) substr($lastKode, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $kode_barang = 'BRG' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        $simpan = DB::table('barang')->insert([
            'kode_barang' => $kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'merk' => $request->merk,
            'keterangan' => $request->keterangan,
            'stok_min' => $request->stok_min,
            'kode_supplier' => $request->kode_supplier,
            'status' => $request->status,
            'jenis' => $request->jenis,
            'kode_item' => $request->kode_item,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($simpan) {
            logActivity('Tambah Barang', 'Barang ' . $request->nama_barang . ' ditambahkan');
            return redirect('viewBarang')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('viewBarang')->with(['warning' => 'Data Gagal Disimpan']);
        }
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


    public function hapusSatuanHarga($id)
    {
        $satuan = DB::table('barang_satuan')->where('id', $id)->first();
        if (!$satuan) {
            return redirect()->back()->with(['warning' => 'Data tidak ditemukan.']);
        }
        logActivity('Hapus Satuan Barang', 'Barang satuan ' . $satuan->satuan . ' dihapus');
        $deleted = DB::table('barang_satuan')->where('id', $id)->delete();
        if ($deleted) {
            return redirect()->back()->with(['success' => 'Data berhasil dihapus.']);
        } else {
            return redirect()->back()->with(['warning' => 'Data gagal dihapus.']);
        }
    }

    public function detail($id)
    {
        $this->authorizePermission('barang');
        $data['PermissionTambahHarga'] = Permission::getPermission('Tambah Harga', Auth::user()->role_id);
        $data['PermissionEditHarga'] = Permission::getPermission('Edit Harga', Auth::user()->role_id);
        $data['PermissionEdit'] = Permission::getPermission('Edit Barang', Auth::user()->role_id);
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier', 'ASC')->get();

        $data['barang'] = DB::table('barang')->where('barang.kode_barang', $id)->first();
        $data['satuanBarang'] = DB::table(table: 'barang_satuan')
            ->where('barang_satuan.kode_barang', $id)
            ->orderBy('barang_satuan.satuan')
            ->get();

        $data['diskon'] = DB::table('diskon_strata')
            ->select('diskon_strata.*', 'diskon_strata.id', 'barang_satuan.harga_jual', 'barang_satuan.satuan')
            ->leftJoin('barang_satuan', 'barang_satuan.id', 'diskon_strata.satuan_id')
            ->where('diskon_strata.kode_barang', $id)
            ->where('diskon_strata.jenis_diskon', 'promo')
            ->orderBy('diskon_strata.kode_barang')
            ->paginate(20)
            ->appends(request()->query());

        $data['diskonReguler'] = DB::table('diskon_strata')
            ->select('diskon_strata.*', 'diskon_strata.id', 'barang_satuan.harga_jual', 'barang_satuan.satuan')
            ->leftJoin('barang_satuan', 'barang_satuan.id', 'diskon_strata.satuan_id')
            ->where('diskon_strata.kode_barang', $id)
            ->where('diskon_strata.jenis_diskon', 'reguler')
            ->orderBy('diskon_strata.kode_barang')
            ->paginate(20)
            ->appends(request()->query());

        return view('barang.detail', $data);
    }

    public function showSatuanBarang(Request $request)
    {
        $data['PermissionEditHarga'] = Permission::getPermission('Edit Harga', Auth::user()->role_id);

        $kode_barang = $request->kode_barang;
        $data['barang'] = DB::table('barang_satuan')
            ->where('barang_satuan.kode_barang', $kode_barang)
            ->orderBy('barang_satuan.satuan')
            ->get();
        return view('barang.showSatuanBarang', $data);
    }

    public function update(Request $request)
    {
        try {
            DB::table('barang')
                ->where('kode_barang', $request->kode_barang)
                ->update([
                    'nama_barang' => $request->nama_barang,
                    'kategori' => $request->jenis,
                    'merk' => $request->merk,
                    'stok_min' => $request->stok_min,
                    'kode_supplier' => $request->kode_supplier,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diupdate.'
            ], 200); // PAKSA STATUS 200
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

    public function storeBarangSatuan(Request $request)
    {
        $data = [
            'kode_barang' => $request->kode_barang,
            'satuan' => $request->satuan,
            'isi' => $request->isi,
            'harga_pokok' => str_replace(',', '', $request->harga_pokok),
            'harga_jual' => str_replace(',', '', $request->harga_jual),
        ];

        $id = $request->id;
        $duplikat = DB::table('barang_satuan')
            ->where('kode_barang', $data['kode_barang'])
            ->where('satuan', $data['satuan'])
            ->when($id, fn($q) => $q->where('id', '!=', $id))   // skip diri sendiri saat update
            ->exists();

        if ($duplikat) {
            return response()->json(
                ['message' => 'Satuan sudah ada untuk barang ini!'],
                409
            );
        }
        if ($id) {
            $ok = DB::table('barang_satuan')->where('id', $id)->update($data);
            return $ok
                ? response()->json(['message' => 'Data berhasil diperbarui!'])
                : response()->json(['message' => 'Gagal memperbarui data!'], 500);
        }

        $ok = DB::table('barang_satuan')->insert($data);
        return $ok
            ? response()->json(['message' => 'Data berhasil disimpan!'])
            : response()->json(['message' => 'Gagal menyimpan data!'], 500);
    }

    public function storeDiskonStarla(Request $request)
    {
        $insert = DB::table('diskon_strata')->insert([
            'kode_barang' => $request->kode_barang,
            'satuan_id' => $request->satuan_id,
            'persentase' => $request->persentase,
            'syarat' => $request->syarat,
            'tipe_syarat' => $request->tipe_syarat,
            'jenis_diskon' => $request->jenis_diskon,
            'cash' => $request->cash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($insert) {
            logActivity('Tambah Diskon', 'Diskon untuk ' . $request->kode_barang . ' ditambahkan');
            return redirect()->route('detailBarang', $request->kode_barang)->with('success', 'Diskon berhasil disimpan');
        }

        return redirect()->route('detailBarang', $request->kode_barang)->with('warning', 'Diskon gagal disimpan');
    }

    public function deleteDiskonStrata($id)
    {
        DB::table('diskon_strata')->where('id', $id)->delete();

        return response()->json(['message' => 'Diskon berhasil dihapus']);
    }

    public function cetakLaporanBarang(Request $request)
    {
        $query = DB::table('barang as b')
            ->leftJoin('barang_satuan as bs', 'bs.kode_barang', '=', 'b.kode_barang')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier');

        if ($request->filled('kode_supplier')) {
            $query->where('b.kode_supplier', $request->kode_supplier);
        }

        if ($request->filled('kode_barang')) {
            $query->where('b.kode_barang', $request->kode_barang);
        }

        $data = $query->select(
            'b.kode_barang',
            'b.nama_barang',
            'b.kategori',
            'b.keterangan',
            'b.stok_min',
            'b.kode_supplier',
            's.nama_supplier',
            'b.status',
            'b.jenis',
            'bs.satuan',
            'bs.isi',
            'bs.harga_pokok',
            'bs.harga_jual'
        )->orderBy('b.nama_barang')->orderBy('bs.isi')->get();

        return view('barang.cetakLaporanBarang', compact('data'));
    }

    public function hargaBarang(Request $request)
    {
        $query = DB::table('barang_satuan as bs')
            ->leftJoin('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier');
        if ($request->filled('kode_barang')) {
            $query->where('bs.kode_barang', $request->kode_barang);
        }

        if ($request->filled('kode_supplier')) {
            $query->where('b.kode_supplier', $request->kode_supplier);
        }

        // ✅ Tambahan filter nama barang
        if ($request->filled('nama_barang')) {
            $query->where('b.nama_barang', 'like', '%' . $request->nama_barang . '%');
        }

        $data['satuanBarang'] = $query->select(
            'bs.id',
            'bs.kode_barang',
            'b.kode_item',
            'b.nama_barang',
            'b.merk',
            'b.kategori',
            's.nama_supplier',
            'bs.satuan',
            'bs.isi',
            'bs.harga_pokok',
            'bs.harga_jual',
            'bs.created_at',
            'bs.updated_at'
        )
            ->where('b.status', '1')
            ->orderBy('b.nama_barang')
            ->orderBy('bs.isi')
            ->limit('100')
            ->get();
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier', 'ASC')->get();

        return view('barang.hargaBarang', $data);
    }
    public function updateHargaBarang(Request $request)
    {
        DB::table('barang_satuan')
            ->where('id', $request->id)
            ->update([
                $request->field => $request->value,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }
}
