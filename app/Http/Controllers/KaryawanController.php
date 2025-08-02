<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use PermissionHelper;

class KaryawanController extends Controller
{
    public function __construct()
    {
        $this->authorizePermission('karyawan');

        view()->share(PermissionHelper::userPermissions(
            'Status Karyawan',
            'Edit Karyawan',
            'Tambah Karyawan',
            'Detail Karyawan',
            'Delete Karyawan'
        ));
    }
    public function index(Request $request)
    {
        $query = DB::table('hrd_karyawan as k')
            ->leftJoin('hrd_jabatan',    'hrd_jabatan.id',    '=', 'k.id_jabatan')
            ->leftJoin('hrd_group',      'hrd_group.id',      '=', 'k.id_group')
            ->leftJoin('hrd_kantor',     'hrd_kantor.id',     '=', 'k.id_kantor')
            ->leftJoin('hrd_department', 'hrd_department.id', '=', 'k.id_department')
            ->select(
                'k.*',
                'hrd_jabatan.nama_jabatan',
                'hrd_group.nama_group',
                'hrd_kantor.nama_kantor',
                'hrd_department.nama_department'
            );
        if ($request->filled('nama_lengkap')) {
            $query->where('k.nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }
        if ($request->filled('nik')) {
            $query->where('k.nik', 'like', '%' . $request->nik . '%');
        }
        if ($request->filled('id_jabatan')) {
            $query->where('k.id_jabatan', $request->id_jabatan);
        }
        if ($request->filled('id_kantor')) {
            $query->where('k.id_kantor', $request->id_kantor);
        }
        if ($request->filled('id_department')) {
            $query->where('k.id_department', $request->id_department);
        }
        if ($request->filled('id_group')) {
            $query->where('k.id_group', $request->id_group);
        }
        if ($request->filled('status')) {
            $query->where('k.status', $request->status);
        }
        $data['data']       = $query->orderBy('k.nama_lengkap')->paginate(perPage: 10)->appends(request()->query());
        $data['jabatan']    = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $data['kantor']     = DB::table('hrd_kantor')->orderBy('nama_kantor')->get();
        $data['department'] = DB::table('hrd_department')->orderBy('nama_department')->get();
        $data['group']      = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('karyawan.index',$data);
    }

    public function detail($id)
    {
        $data['karyawan'] = DB::table('hrd_karyawan')
        ->leftJoin('hrd_jabatan','hrd_jabatan.id','hrd_karyawan.id_jabatan')
        ->leftJoin('hrd_group','hrd_group.id','hrd_karyawan.id_group')
        ->leftJoin('hrd_kantor','hrd_kantor.id','hrd_karyawan.id_kantor')
        ->leftJoin('hrd_department','hrd_department.id','hrd_karyawan.id_department')
        ->where('hrd_karyawan.nik',$id)
        ->first();
        return view('karyawan.detail', $data);
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        DB::table('hrd_karyawan')->insert($request->except('_token'));
        logActivity('Update Karyawan','Karyawan  '. $request->nama_lengkap.' di update');
        return redirect()->route('viewKaryawan')->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data['jabatan']    = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $data['kantor']     = DB::table('hrd_kantor')->orderBy('nama_kantor')->get();
        $data['department'] = DB::table('hrd_department')->orderBy('nama_department')->get();
        $data['group']      = DB::table('hrd_group')->orderBy('nama_group')->get();
        $data['karyawan'] = DB::table('hrd_karyawan')
        ->leftJoin('hrd_jabatan','hrd_jabatan.id','hrd_karyawan.id_jabatan')
        ->leftJoin('hrd_group','hrd_group.id','hrd_karyawan.id_group')
        ->leftJoin('hrd_kantor','hrd_kantor.id','hrd_karyawan.id_kantor')
        ->leftJoin('hrd_department','hrd_department.id','hrd_karyawan.id_department')
        ->where('hrd_karyawan.nik',$id)
        ->first();
        return view('karyawan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        DB::table('hrd_karyawan')->where('nik', $id)->update($request->except(['_token', '_method']));
        logActivity('Update Karyawan','Karyawan  '. $request->nama_lengkap.' di update');
        return redirect()->route('viewKaryawan')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function delete($id)
    {
        $karyawan = DB::table('hrd_karyawan')->where('nik', $id)->first();
        logActivity('Hapus Karyawan','Karyawan  '. $karyawan->nama_lengkap.' di hapus');
        DB::table('hrd_karyawan')->where('nik', $id)->delete();
        return redirect()->route('viewKaryawan')->with('success', 'Karyawan berhasil dihapus!');
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

    public function toggleStatus($id)
    {
        $karyawan = DB::table('hrd_karyawan')->where('nik', $id)->first();
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Karyawan tidak ditemukan');
        }
        $newStatus = $karyawan->status == '1' ? '0' : '1';
        DB::table('hrd_karyawan')->where('nik', $id)->update(['status' => $newStatus]);
        logActivity('Non Aktifkan Karyawan','Karyawan  '. $karyawan->nama_lengkap.' di nonaktifkan');
        return redirect()->back()->with('success', 'Status karyawan berhasil diperbarui');
    }
}
