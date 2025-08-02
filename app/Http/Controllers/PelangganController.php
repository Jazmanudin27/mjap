<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PermissionHelper;

class PelangganController extends Controller
{

    public function __construct()
    {
        $this->authorizePermission('pelanggan');

        view()->share(PermissionHelper::userPermissions(
            'Status Karyawan',
            'Edit Karyawan',
            'Detail Karyawan',
            'Delete Karyawan'
        ));
    }

    public function index(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $status = $request->status;
        $action = $request->action;

        $query = DB::table('pelanggan')
            ->select('pelanggan.*', 'wilayah.nama_wilayah')
            ->leftJoin('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->when($nama_pelanggan, function ($query) use ($nama_pelanggan) {
                return $query->where('pelanggan.nama_pelanggan', 'LIKE', '%' . $nama_pelanggan . '%');
            })
            ->when($kode_pelanggan, function ($query) use ($kode_pelanggan) {
                return $query->where('pelanggan.kode_pelanggan', 'LIKE', '%' . $kode_pelanggan . '%');
            })
            ->when($status != '', function ($query) use ($status) {
                return $query->where('pelanggan.status', $status);
            })
            ->orderBy('pelanggan.nama_pelanggan');

        // Cetak
        if ($action === 'cetak') {
            $data['pelanggan'] = $query->get();
            return view('pelanggan.cetakLaporanPelanggan', $data);
        }

        // Export Excel
        if ($action === 'export') {
            $data['pelanggan'] = $query->get();
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=Laporan Pelanggan.xls");
            return view('pelanggan.cetakLaporanPelanggan', $data);
        }

        // Default (Filter View) → Pakai Paginate
        $data['pelanggan'] = $query->paginate(10)->appends($request->query());
        return view('pelanggan.index', $data);
    }

    public function mapsPelanggan(Request $request)
    {
        $kode_wilayah = $request->wilayah;
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $status = $request->status;

        $data['customers'] = DB::table('pelanggan')
            ->leftJoin('wilayah', 'wilayah.kode_wilayah', '=', 'pelanggan.kode_wilayah')
            ->select('pelanggan.*', 'wilayah.nama_wilayah')
            ->when($kode_pelanggan, function ($query) use ($kode_pelanggan) {
                return $query->where('pelanggan.kode_pelanggan', $kode_pelanggan);
            })
            ->when($kode_wilayah, function ($query) use ($kode_wilayah) {
                return $query->where('pelanggan.kode_wilayah', $kode_wilayah);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('pelanggan.status', $status);
            })
            ->get();

        $data['wilayah'] = DB::table('wilayah')->orderBy('nama_wilayah')->get();

        return view('pelanggan.maps', $data);
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $tahunBulan = date('y') . date('m');
        $lastPelanggan = DB::table('pelanggan')
            ->where('kode_pelanggan', 'LIKE', "PLG$tahunBulan%")
            ->orderBy('kode_pelanggan', 'desc')
            ->first();

        $nomorUrut = $lastPelanggan ? ((int) substr($lastPelanggan->kode_pelanggan, -3) + 1) : 1;
        $kodePelanggan = "PLG$tahunBulan" . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = $kodePelanggan . '.jpg';
            $file->storeAs('public/pelanggan', $filename);
            $fotoName = 'pelanggan/' . $filename;
        } else {
            $fotoName = null;
        }

        $simpan = DB::table('pelanggan')->insert([
            'kode_pelanggan' => $kodePelanggan,
            'tanggal_register' => now()->toDateString(),
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'alamat_toko' => $request->alamat_toko,
            'no_hp_pelanggan' => $request->no_hp_pelanggan,
            'limit_pelanggan' => $request->limit_pelanggan,
            'hari' => $request->hari,
            'kunjungan' => $request->kunjungan,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status,
            'foto' => $fotoName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($simpan) {
            logActivity('Tambah Pelanggan', 'Pelanggan ' . $request->nama_pelanggan . ' ditambahkan');
            return Redirect('viewPelanggan')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect('viewPelanggan')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('pelanggan')->where('kode_pelanggan', $request->id)->first();
        logActivity('Hapus Pelanggan', 'Pelanggan ' . $hapus->nama_pelanggan . ' dihapus');
        $hapus = DB::table('pelanggan')->where('kode_pelanggan', $request->id)->delete();
        if ($hapus) {
            return Redirect('viewPelanggan')->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect('viewPelanggan')->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function detail($id)
    {
        $data['pelanggan'] = DB::table('pelanggan')->where('pelanggan.kode_pelanggan', $id)->first();
        return view('pelanggan.detail', $data);
    }

    public function update(Request $request)
    {
        $data = [
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'no_hp_pelanggan' => $request->no_hp_pelanggan,
            'kepemilikan' => $request->kepemilikan,
            'omset_toko' => $request->omset_toko,
            'limit_pelanggan' => $request->limit_pelanggan,
            'status' => $request->status,
            'updated_at' => now(),
        ];

        $oldData = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = $request->kode_pelanggan . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/pelanggan', $filename);

            if ($oldData && $oldData->foto) {
                Storage::disk('public')->delete('pelanggan/' . $oldData->foto);
            }
            $data['foto'] = $filename;
        }

        $update = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->update($data);

        if ($update) {
            logActivity('Update Pelanggan', 'Pelanggan ' . $request->nama_pelanggan . ' diupdate');
            return redirect()->route('viewPelanggan')->with('success', 'Data Berhasil Diupdate');
        } else {
            return redirect()->route('viewPelanggan')->with('warning', 'Data Gagal Diupdate');
        }
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

    public function toggleStatusPelanggan($id)
    {
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $id)->first();
        if (!$pelanggan) {
            return redirect()->back()->with('error', 'pelanggan tidak ditemukan');
        }
        $newStatus = $pelanggan->status == '1' ? '0' : '1';

        DB::table('pelanggan')->where('kode_pelanggan', $id)->update(['status' => $newStatus]);
        logActivity('Non Aktifkan Pelanggan', 'pelanggan  ' . $pelanggan->nama_pelanggan . ' di nonaktifkan');

        return redirect()->back()->with('success', 'Status pelanggan berhasil diperbarui');
    }
}
