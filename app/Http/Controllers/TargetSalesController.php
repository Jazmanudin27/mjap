<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PermissionHelper;
use App\Models\Permission;

class TargetSalesController extends Controller
{
    public function __construct()
    {
        $this->authorizePermission('penjualan');
        view()->share(PermissionHelper::userPermissions(
            'Tambah Target Sales',
            'Edit Target Sales',
            'Delete Target Sales'
        ));
    }

    public function index(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $team = Auth::user()->nik;
        $data['karyawan'] = DB::table('hrd_karyawan')->where('status', 1)->where('id_jabatan', '1')->orderBy('nama_lengkap')->get();
        $data['targets'] = DB::table('target_sales as ts')
            ->join('hrd_karyawan as k', 'k.nik', '=', 'ts.kode_sales')
            ->select('ts.*', 'k.nama_lengkap')
            ->when($bulan, fn($q) => $q->where('ts.bulan', $bulan))
            ->when($tahun, fn($q) => $q->where('ts.tahun', $tahun))
            ->when(Auth::user()->role == 'spv sales', function ($query) use ($team) {
                return $query->where('k.divisi', $team);
            })
            ->distinct()
            ->orderBy('ts.bulan')
            ->orderBy('ts.kode_sales')
            ->paginate(100)
            ->appends($request->query());
        return view('target_sales.index', $data);
    }

    public function create()
    {
        $data['sales'] = DB::table('hrd_karyawan')->where('status', 1)->where('id_jabatan', '1')->orderBy('nama_lengkap')->get();
        return view('target_sales.create', $data);
    }

    public function store(Request $request)
    {
        $sales = DB::table('hrd_karyawan')
            ->where('status', '1')
            ->where('id_jabatan', '1')
            ->whereNotNull('divisi')
            ->get();

        foreach ($sales as $s) {
            $exists = DB::table('target_sales')
                ->where('kode_sales', $s->nik)
                ->where('bulan', $request->bulan)
                ->where('tahun', $request->tahun)
                ->exists();

            if (!$exists) {
                DB::table('target_sales')->insert([
                    'kode_sales' => $s->nik,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        logActivity('Tambah Target Sales', 'Target untuk seluruh sales ditambahkan');
        return redirect()->route('viewTargetSales')->with('success', 'Target berhasil disimpan untuk semua sales.');
    }

    public function update(Request $request)
    {
        $updated = DB::table('target_sales')
            ->where('id', $request->id)
            ->update([
                $request->field => $request->value,
                'updated_at' => now()
            ]);

        return response()->json([
            'status' => $updated ? 'success' : 'error'
        ]);
    }

    public function delete($id)
    {
        $target = DB::table('target_sales')->find($id);
        if ($target) {
            DB::table('target_sales')->delete($id);
            logActivity('Hapus Target Sales', 'Target kode_sales ' . $target->kode_sales . ' dihapus');
            return redirect()->route('viewTargetSales')->with('success', 'Target berhasil dihapus');
        }

        return redirect()->route('viewTargetSales')->with('warning', 'Data tidak ditemukan');
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
}
