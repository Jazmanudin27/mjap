<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiskonController extends Controller
{
    public function diskonBarang(Request $request)
    {
        $query = DB::table('diskon_strata')
            ->leftJoin('barang_satuan', 'diskon_strata.satuan_id', '=', 'barang_satuan.id')
            ->leftJoin('barang', 'barang_satuan.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin('supplier', 'barang.kode_supplier', '=', 'supplier.kode_supplier')
            ->select(
                'diskon_strata.*',
                'barang.nama_barang',
                'barang.merk',
                'barang.kategori',
                'barang_satuan.satuan',
                'supplier.nama_supplier'
            );

        if ($request->filled('kode_barang')) {
            $query->where('diskon_strata.kode_barang', $request->kode_barang);
        }
        $query->where('diskon_strata.kode_supplier', NULL);
        $diskon = $query->orderBy('diskon_strata.id', 'desc')->get();

        // ambil list supplier untuk dropdown filter
        $barang = DB::table('barang')
            ->select('kode_barang', 'nama_barang')
            ->where('status', '1')
            ->orderBy('nama_barang')
            ->get();

        $satuan = DB::table('barang_satuan')
            ->orderBy('satuan')
            ->get();

        return view('diskon.diskonBarang', compact('diskon', 'satuan', 'barang'));
    }


    public function diskonSupplier(Request $request)
    {
        $query = DB::table('diskon_strata')
            ->join('supplier', 'diskon_strata.kode_supplier', '=', 'supplier.kode_supplier')
            ->select(
                'diskon_strata.*',
                'supplier.nama_supplier'
            );
        if ($request->filled('kode_supplier')) {
            $query->where('diskon_strata.kode_supplier', $request->kode_supplier);
        }
        $diskon = $query->orderBy('diskon_strata.id', 'desc')->get();

        $supplier = DB::table('supplier')
            ->select('kode_supplier', 'nama_supplier')
            ->where('status', '1')
            ->orderBy('nama_supplier')
            ->get();

        return view('diskon.diskonSupplier', compact('diskon', 'supplier'));
    }
    public function storeDiskon(Request $request)
    {
        DB::table('diskon_strata')->insert([
            'kode_barang' => $request->kode_barang,
            'satuan_id' => $request->satuan_id,
            'persentase' => $request->persentase,
            'syarat' => $request->syarat,
            'tipe_syarat' => $request->tipe_syarat,
            'jenis_diskon' => $request->jenis_diskon,
            'cash' => $request->cash ?? 0,
            'kode_supplier' => $request->kode_supplier,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // return redirect()->route('diskon')->with('success', 'Diskon strata berhasil ditambahkan');
    }

    public function updateDiskon(Request $request, $id)
    {

        DB::table('diskon_strata')->where('id', $id)->update([
            'kode_barang' => $request->kode_barang,
            'satuan_id' => $request->satuan_id,
            'persentase' => $request->persentase,
            'syarat' => $request->syarat,
            'tipe_syarat' => $request->tipe_syarat,
            'jenis_diskon' => $request->jenis_diskon,
            'cash' => $request->cash ?? 0,
            'kode_supplier' => $request->kode_supplier,
            'updated_at' => Carbon::now(),
        ]);

    }

    public function deleteDiskon($id)
    {
        DB::table('diskon_strata')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Diskon strata berhasil dihapus');
    }

}
