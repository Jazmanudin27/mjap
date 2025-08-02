<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PermissionHelper;

class PengajuanLimitKreditController extends Controller
{
    public function __construct()
    {
        $this->authorizePermission('penjualan');
        view()->share(PermissionHelper::userPermissions(
            'Limit Kredit',
            'Edit Limit Kredit',
            'Detail Limit Kredit',
            'Delete Limit Kredit'
        ));
    }

    public function getLimitSupplier($pengajuan_id)
    {
        $data = DB::table('pengajuan_limit_supplier')
            ->join('supplier', 'supplier.kode_supplier', '=', 'pengajuan_limit_supplier.kode_supplier')
            ->where('pengajuan_id', $pengajuan_id)
            ->select('supplier.nama_supplier', 'pengajuan_limit_supplier.limit_per_supplier as limit')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function viewPengajuanLimit(Request $request)
    {
        $userNik = Auth::user()->nik;

        $query = DB::table('pengajuan_limit_kredit')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', '=', 'pengajuan_limit_kredit.kode_pelanggan')
            ->join('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_limit_kredit.nik')
            ->select(
                'pengajuan_limit_kredit.*',
                'pelanggan.nama_pelanggan',
                'hrd_karyawan.nama_lengkap as dibuat_oleh'
            );

        if ($request->filled('kode_pelanggan')) {
            $query->where('pengajuan_limit_kredit.kode_pelanggan', $request->kode_pelanggan);
        }

        $pengajuanRaw = $query->orderByDesc('pengajuan_limit_kredit.created_at')->paginate(15);

        // Tambahkan status_global berdasarkan hasil approval dari semua level
        $pengajuan = $pengajuanRaw->getCollection()->map(function ($item) use ($userNik) {
            $approvals = DB::table('pengajuan_approvals')
                ->where('pengajuan_id', $item->id)
                ->where('jenis_pengajuan', 'limit_kredit')
                ->get();

            // Status global
            if ($approvals->contains('ditolak', true)) {
                $item->status_global = 'ditolak';
            } elseif ($approvals->every('disetujui', true)) {
                $item->status_global = 'disetujui';
            } else {
                $item->status_global = 'pending';
            }

            // Cek apakah user login punya approval
            $userApproval = $approvals->firstWhere('user_id', $userNik);

            $item->approval_id = $userApproval->id ?? null;
            $item->pengajuan_id = $userApproval->pengajuan_id ?? null;
            $item->user_id = $userApproval->user_id ?? null;
            $item->disetujui = $userApproval->disetujui ?? null;
            $item->ditolak = $userApproval->ditolak ?? null;


            return $item;
        });

        // Ganti koleksi di pagination
        $pengajuanRaw->setCollection($pengajuan);
        $data['pengajuan'] = $pengajuanRaw;
        $data['pelanggan'] = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();

        return view('pengajuan_limit.index', $data);
    }

    public function getApprovalHistory($kode_pengajuan)
    {
        try {
            $pengajuan = DB::table('pengajuan_limit_kredit')
                ->where('id', $kode_pengajuan)
                ->first();

            if (!$pengajuan) {
                return response()->json(['error' => 'Pengajuan tidak ditemukan'], 404);
            }

            $riwayatApproval = DB::table('pengajuan_approvals')
                ->leftJoin('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_approvals.user_id')
                ->where('pengajuan_approvals.pengajuan_id', $kode_pengajuan)
                ->select(
                    'pengajuan_approvals.*',
                    'hrd_karyawan.nama_lengkap as nama'
                )
                ->orderBy('pengajuan_approvals.level_approval')
                ->get();

            return response()->json([
                'success' => true,
                'riwayat_approval' => $riwayatApproval
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function tambahPengajuanLimit()
    {
        $pelanggan = DB::table('pelanggan')->orderBy('nama_pelanggan')->get();
        return view('pengajuan_limit.create', compact('pelanggan'));
    }

    public function storePengajuanLimit(Request $request)
    {
        // Generate kode pengajuan
        $prefix = 'LK' . date('ym');
        $last = DB::table('pengajuan_limit_kredit')
            ->where('id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('id');

        if ($last) {
            $lastNumber = (int) substr($last, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Simpan pengajuan limit kredit
        DB::table('pengajuan_limit_kredit')->insert([
            'id' => $newId,
            'tanggal' => now(),
            'kode_pelanggan' => $request->kode_pelanggan,
            'nilai_pengajuan' => $request->nilai_pengajuan,
            'alasan' => $request->alasan,
            'nik' => Auth::user()->nik,
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $nilaiPengajuan = (int) $request->nilai_pengajuan;

        if ($nilaiPengajuan >= 2000000) {
            // Jika lebih dari atau sama dengan 2jt, butuh level 1, 2, 3
            $approvalUsers = [
                ['user_id' => '25.01.004', 'level_approval' => 1],
                ['user_id' => '25.01.006', 'level_approval' => 2],
                ['user_id' => '25.01.013', 'level_approval' => 3],
            ];
        } else {
            // Jika kurang dari 2jt, cukup level 1 dan 2
            $approvalUsers = [
                ['user_id' => '25.01.004', 'level_approval' => 1],
                ['user_id' => '25.01.006', 'level_approval' => 2],
            ];
        }

        // Masukkan data approval ke tabel
        $approvalData = [];
        foreach ($approvalUsers as $user) {
            $approvalData[] = [
                'jenis_pengajuan' => 'limit_kredit',
                'pengajuan_id' => $newId,
                'user_id' => $user['user_id'],
                'level_approval' => $user['level_approval'],
                'disetujui' => false,
                'ditolak' => false,
                'keterangan' => null,
                'tanggal_approval' => null,
                'approved_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('pengajuan_approvals')->insert($approvalData);

        return redirect()->route('viewPengajuanLimit')->with('success', 'Pengajuan limit berhasil ditambahkan.');
    }

    public function detailPengajuanLimit($id)
    {
        $data = DB::table('pengajuan_limit_kredit')
            ->join('pelanggan', 'pelanggan.kode_pelanggan', '=', 'pengajuan_limit_kredit.kode_pelanggan')
            ->where('pengajuan_limit_kredit.id', $id)
            ->select('pengajuan_limit_kredit.*', 'pelanggan.nama_pelanggan')
            ->first();

        if (!$data) {
            return abort(404);
        }

        return view('pengajuan_limit.detail', compact('data'));
    }

    public function updatePengajuanLimit(Request $request)
    {

        DB::table('pengajuan_limit_kredit')->where('id', $request->id)->update([
            'nilai_pengajuan' => $request->nilai_pengajuan,
            'alasan' => $request->alasan,
            'updated_at' => now(),
        ]);

        return redirect()->route('viewPengajuanLimit')->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function deletePengajuanLimit($id)
    {
        DB::table('pengajuan_limit_kredit')->where('id', $id)->delete();
        DB::table('pengajuan_approvals')->where('pengajuan_id', $id)->delete();

        return redirect()->back()->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function approvePengajuanLimit(Request $request, $id)
    {
        $approval = DB::table('pengajuan_approvals')->where('id', $id)->first();

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $aksi = $request->input('aksi');
        $keterangan = $request->input('keterangan');
        $revisi = str_replace(['Rp', '.', ' '], '', $request->input('revisi_limit'));

        $update = [
            'keterangan' => $keterangan,
            'tanggal_approval' => now(),
            'approved_by' => Auth::user()->nik,
            'updated_at' => now(),
        ];

        if ($aksi === 'setujui') {
            $update['disetujui'] = true;
            $update['revisi_limit'] = is_numeric($revisi) ? $revisi : null;
        } else {
            $update['ditolak'] = true;
        }

        DB::table('pengajuan_approvals')->where('id', $id)->update($update);

        $pengajuanId = $approval->pengajuan_id;

        $allApprovals = DB::table('pengajuan_approvals')
            ->where('pengajuan_id', $pengajuanId)
            ->where('jenis_pengajuan', 'limit_kredit')
            ->get();

        $isSemuaDisetujui = $allApprovals->every(fn($a) => $a->disetujui && !$a->ditolak);
        $hasDitolak = $allApprovals->contains('ditolak', true);

        if ($isSemuaDisetujui && !$hasDitolak) {
            // Ambil pengajuan dan revisi terakhir (level 4)
            $pengajuan = DB::table('pengajuan_limit_kredit')->where('id', $pengajuanId)->first();
            $revisiDirektur = $allApprovals->where('level_approval', 4)->first()->revisi_limit ?? null;
            $nilaiFinal = $revisiDirektur ?: $pengajuan->nilai_pengajuan;

            // Update pengajuan
            DB::table('pengajuan_limit_kredit')
                ->where('id', $pengajuanId)
                ->update([
                    'nilai_disetujui' => $nilaiFinal,
                    'status' => 'disetujui',
                    'updated_at' => now()
                ]);

            DB::table('pelanggan')
                ->where('kode_pelanggan', $pengajuan->kode_pelanggan)
                ->update([
                    'limit_pelanggan' => $nilaiFinal
                ]);
        } elseif ($hasDitolak) {
            DB::table('pengajuan_limit_kredit')
                ->where('id', $pengajuanId)
                ->update([
                    'status' => 'ditolak',
                    'updated_at' => now()
                ]);
        }

        return back()->with('success', 'Approval berhasil diproses.');
    }
    public function simpanLimitSupplier(Request $request)
    {
        try {
            $pengajuanId = $request->input('pengajuan_id');

            // Hapus dulu data lama jika ada

            // Simpan data baru
            foreach ($request->input('supplier') as $index => $kodeSupplier) {
                DB::table('pengajuan_limit_supplier')
                    ->where('pengajuan_id', $pengajuanId)
                    ->where('kode_supplier', $kodeSupplier)
                    ->delete();

                $limit = (int) str_replace(['Rp', '.', ','], '', $request->input('limit_supplier')[$index]);

                DB::table('pengajuan_limit_supplier')->insert([
                    'pengajuan_id' => $pengajuanId,
                    'kode_supplier' => $kodeSupplier,
                    'limit_per_supplier' => $limit,
                    'sisa_limit' => $limit,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan pembagian limit per supplier'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function hapusLimitSupplier(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID tidak ditemukan.']);
        }

        try {
            DB::table('pengajuan_limit_supplier')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function riwayatApprovalLimit($id)
    {
        $riwayat = DB::table('pengajuan_approvals')
            ->leftJoin('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_approvals.user_id')
            ->where('pengajuan_approvals.pengajuan_id', $id)
            ->select(
                'pengajuan_approvals.*',
                'hrd_karyawan.nama_lengkap as nama'
            )
            ->orderBy('pengajuan_approvals.level_approval')
            ->get();

        return response()->json(['data' => $riwayat]);
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

}
