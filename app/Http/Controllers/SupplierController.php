<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use PermissionHelper;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizePermission('supplier');
        view()->share(
            PermissionHelper::userPermissions(
                'Status Supplier',
                'Edit Supplier',
                'Detail Supplier',
                'Delete Supplier',
                'Tambah Supplier'
            )
        );
    }
    public function index(Request $request)
    {
        $this->authorizePermission('supplier');

        $kode_supplier  = $request->kode_supplier;
        $nama_supplier  = $request->nama_supplier;
        $status         = $request->status;

        $data['supplier'] = DB::table('supplier')
            ->when($nama_supplier, function ($q) use ($nama_supplier) {
                return $q->where('nama_supplier', 'LIKE', "%$nama_supplier%");
            })
            ->when($kode_supplier, function ($q) use ($kode_supplier) {
                return $q->where('kode_supplier', 'LIKE', "%$kode_supplier%");
            })
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->orderBy('nama_supplier')
            ->paginate(10)
            ->appends($request->query());

        return view('supplier.index', $data);
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $last = DB::table('supplier')
            ->where('kode_supplier', 'LIKE', 'SP%')
            ->orderByDesc('kode_supplier')
            ->first();

        $urut = $last ? ((int)substr($last->kode_supplier, 2)) + 1 : 1;
        $kodeSupplier = 'SP' . str_pad($urut, 4, '0', STR_PAD_LEFT);
        $simpan = DB::table('supplier')->insert([
            'kode_supplier' => $kodeSupplier,
            'nama_supplier' => $request->nama_supplier,
            'alamat'        => $request->alamat,
            'telepon'       => $request->telepon,
            'email'         => $request->email,
            'status'        => $request->status,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        if ($simpan) {
            logActivity('Tambah Supplier', 'Supplier ' . $request->nama_supplier . ' ditambahkan');
            return Redirect('viewSupplier')->with('success', 'Data Berhasil Disimpan');
        }
        return Redirect('viewSupplier')->with('warning', 'Data Gagal Disimpan');
    }

    public function delete(Request $request)
    {
        $row = DB::table('supplier')->where('kode_supplier', $request->id)->first();
        if (!$row) {
            return Redirect('viewSupplier')->with('warning', 'Data tidak ditemukan');
        }

        DB::table('supplier')->where('kode_supplier', $request->id)->delete();
        logActivity('Hapus Supplier', 'Supplier ' . $row->nama_supplier . ' dihapus');

        return Redirect('viewSupplier')->with('success', 'Data Berhasil Dihapus');
    }

    public function detail($id)
    {
        $this->authorizePermission('supplier');

        $data['PermissionEdit']   = Permission::getPermission('Edit Supplier',   Auth::user()->role_id);
        $data['PermissionDelete'] = Permission::getPermission('Delete Supplier', Auth::user()->role_id);

        $data['supplier'] = DB::table('supplier')->where('kode_supplier', $id)->first();
        return view('supplier.detail', $data);
    }

    public function update(Request $request)
    {
        $update = DB::table('supplier')->where('kode_supplier', $request->kode_supplier)->update([
            'nama_supplier' => $request->nama_supplier,
            'alamat'        => $request->alamat,
            'telepon'       => $request->telepon,
            'email'         => $request->email,
            'status'        => $request->status,
            'updated_at'    => now(),
        ]);

        if ($update) {
            logActivity('Update Supplier', 'Supplier ' . $request->nama_supplier . ' diupdate');
            return redirect()->route('viewSupplier')->with('success', 'Data Berhasil Diupdate');
        }

        return redirect()->route('viewSupplier')->with('warning', 'Data Gagal Diupdate');
    }

    public function toggleStatusSupplier($id)
    {
        $row = DB::table('supplier')->where('kode_supplier', $id)->first();
        if (!$row) {
            return redirect()->back()->with('error', 'Supplier tidak ditemukan');
        }

        $newStatus = $row->status == '1' ? '0' : '1';
        DB::table('supplier')->where('kode_supplier', $id)->update(['status' => $newStatus]);

        $judulLog = $newStatus == '1' ? 'Aktifkan Supplier' : 'Nonaktifkan Supplier';
        logActivity($judulLog, 'Supplier ' . $row->nama_supplier . ' diperbarui statusnya');

        return redirect()->back()->with('success', 'Status supplier berhasil diperbarui');
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
}
