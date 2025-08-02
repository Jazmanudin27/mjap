<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PermissionHelper;

class SetoranController extends Controller
{
    public function __construct()
    {
        $this->authorizePermission('setoran');

        view()->share(PermissionHelper::userPermissions(
            'Setoran',
            'Tambah Setoran Penjualan',
        ));
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
    public function viewSetoranPenjualan(Request $request)
    {
        $query = DB::table('setoran_penjualan as s')
            ->leftJoin('hrd_karyawan as k', 'k.nik', '=', 's.kode_sales')
            ->select('s.*', 'k.nama_lengkap as nama_sales')
            ->when($request->tanggal_dari, function ($q) use ($request) {
                $q->whereDate('s.tanggal', '>=', $request->tanggal_dari);
            })
            ->when($request->tanggal_sampai, function ($q) use ($request) {
                $q->whereDate('s.tanggal', '<=', $request->tanggal_sampai);
            })
            ->when($request->kode_sales, function ($q) use ($request) {
                $q->where('s.kode_sales', $request->kode_sales);
            });
        $setoran = (clone $query)->orderByDesc('s.tanggal')->paginate(10);
        $total = (clone $query)->selectRaw('
            SUM(s.lhp_tunai) as lhp_tunai,
            SUM(s.lhp_tagihan) as lhp_tagihan,
            SUM(s.setoran_kertas) as setoran_kertas,
            SUM(s.setoran_logam) as setoran_logam,
            SUM(s.setoran_giro) as setoran_giro,
            SUM(s.setoran_transfer) as setoran_transfer,
            SUM(s.setoran_lainnya) as setoran_lainnya
        ')->first();

        $sales = DB::table('hrd_karyawan')
            ->where('status', '1')
            ->where('id_jabatan', '1')
            ->get();

        return view('setoran.viewSetoranPenjualan', compact('setoran', 'sales', 'total'));
    }

    public function createSetoranPenjualan(Request $request)
    {
        $data['sales'] = DB::table('hrd_karyawan')
            ->where('status', '1')
            ->where('id_jabatan', '1')
            ->get();
        return view('setoran.createSetoranPenjualan', $data);
    }
    public function storeSetoranPenjualan(Request $request)
    {
        // dd($request->all());
        // die;
        DB::table('setoran_penjualan')->insert([
            'kode_setoran'     => 'ST' . now()->format('ymdHis'),
            'tanggal'          => $request->tanggal_setoran,
            'kode_sales'       => $request->kode_sales_setoran,
            'lhp_tunai'        => (int) str_replace(['Rp', '.', ','], '', $request->tunai),
            'lhp_tagihan'      => (int) str_replace(['Rp', '.', ','], '', $request->tagihan),
            'setoran_kertas'   => (int) str_replace(['Rp', '.', ','], '', $request->setoran_kertas),
            'setoran_logam'    => (int) str_replace(['Rp', '.', ','], '', $request->setoran_logam),
            'setoran_lainnya'  => (int) str_replace(['Rp', '.', ',', ' '], '', $request->setoran_lainnya ?? 0) *
                (strpos($request->setoran_lainnya, '-') !== false ? -1 : 1),
            'setoran_transfer' => (int) str_replace(['Rp', '.', ','], '', $request->setoran_transfer),
            'setoran_giro'     => (int) str_replace(['Rp', '.', ','], '', $request->setoran_giro),
            'keterangan'       => $request->keterangan,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->route('viewSetoranPenjualan')->with('success', 'Data setoran berhasil disimpan.');
    }

    public function updateSetoranPenjualan(Request $request)
    {
        function parseRupiah($value)
        {
            return (int) str_replace(['Rp', '.', ',', '-', ' '], '', $value);
        }

        $id = $request->id_setoran;

        $data = [
            'tanggal'         => $request->tanggal_setoran,
            'kode_sales'      => $request->kode_sales_setoran,
            'setoran_kertas'  => parseRupiah($request->setoran_kertas),
            'setoran_logam'   => parseRupiah($request->setoran_logam),
            'setoran_lainnya' => parseRupiah($request->setoran_lainnya),
            'setoran_transfer' => parseRupiah($request->setoran_transfer),
            'setoran_giro'    => parseRupiah($request->setoran_giro),
            'keterangan'      => $request->keterangan,
            'updated_at'      => now()
        ];

        DB::table('setoran_penjualan')->where('kode_setoran', $id)->update($data);

        return redirect()->route('viewSetoranPenjualan')->with('success', 'Data setoran berhasil diperbarui.');
    }
    public function getSetoranPenjualan(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $kodeSales = $request->kode_sales ?? $request->salesman;

        $tunai = DB::table('penjualan')
            ->whereDate('tanggal', $tanggal)
            ->where('kode_sales', $kodeSales)
            ->where('jenis_transaksi', 'T')
            ->sum('grand_total');

        $tagihan = DB::table('penjualan_pembayaran')
            ->whereDate('tanggal', $tanggal)
            ->where('kode_sales', $kodeSales)
            ->where('jenis_bayar', 'titipan')
            ->sum('jumlah');

        $tagihanTf = DB::table('penjualan_pembayaran_transfer')
            ->whereDate('tanggal', $tanggal)
            ->where('jenis_bayar', 'titipan')
            ->where('kode_sales', $kodeSales)
            ->sum('jumlah');

        $tagihanGiro = DB::table('penjualan_pembayaran_giro')
            ->whereDate('tanggal', $tanggal)
            ->where('jenis_bayar', 'titipan')
            ->where('kode_sales', $kodeSales)
            ->sum('jumlah');

        $transfer = DB::table('penjualan_pembayaran_transfer')
            ->whereDate('tanggal', $tanggal)
            ->where('kode_sales', $kodeSales)
            ->sum('jumlah');

        $giro = DB::table('penjualan_pembayaran_giro')
            ->whereDate('tanggal', $tanggal)
            ->where('kode_sales', $kodeSales)
            ->sum('jumlah');

        return response()->json([
            'tunai'    => round($tunai) ?? 0,
            'tagihan'  => round($tagihan + $tagihanTf + $tagihanGiro) ?? 0,
            'transfer' => round($transfer) ?? 0,
            'giro'     => round($giro) ?? 0,
        ]);
    }


    public function deleteSetoranPenjualan($id)
    {
        DB::table('setoran_penjualan')->where('kode_setoran', $id)->delete();
        return redirect()->route('viewSetoranPenjualan')->with('success', 'Data berhasil dihapus.');
    }
}
