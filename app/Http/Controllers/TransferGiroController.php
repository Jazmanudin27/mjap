<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PermissionHelper;

class TransferGiroController extends Controller
{
    public function __construct()
    {
        $this->authorizePermission('Transfer Giro');

        view()->share(PermissionHelper::userPermissions(
            'Transfer Giro',
        ));
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

    public function viewTransfer(Request $request)
    {
        $data = DB::table('penjualan_pembayaran_transfer')
            ->leftJoin('penjualan', 'penjualan.no_faktur', '=', 'penjualan_pembayaran_transfer.no_faktur')
            ->leftJoin('pelanggan', 'pelanggan.kode_pelanggan', '=', 'penjualan.kode_pelanggan')
            ->leftJoin('hrd_karyawan', 'hrd_karyawan.nik', '=', 'penjualan.kode_sales')
            ->select(
                'penjualan_pembayaran_transfer.*',
                'penjualan_pembayaran_transfer.status',
                'pelanggan.nama_pelanggan',
                'hrd_karyawan.nama_lengkap as nama_sales',
                'penjualan.kode_pelanggan',
                'penjualan.kode_sales'
            )
            ->when($request->tanggal_dari, fn($q) => $q->whereDate('penjualan_pembayaran_transfer.tanggal', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn($q) => $q->whereDate('penjualan_pembayaran_transfer.tanggal', '<=', $request->tanggal_sampai))
            ->when($request->kode_pelanggan, fn($q) => $q->where('penjualan.kode_pelanggan', $request->kode_pelanggan))
            ->when($request->status, fn($q) => $q->where('penjualan_pembayaran_transfer.status', $request->status))
            ->orderByDesc('tanggal')
            ->paginate(20);
        $bankList = DB::table('bank')->get();
        return view('transfer_giro.viewTransfer', compact('data', 'bankList'));
    }

    public function viewGiro(Request $request)
    {
        $data = DB::table('penjualan_pembayaran_giro')
            ->leftJoin('penjualan', 'penjualan.no_faktur', '=', 'penjualan_pembayaran_giro.no_faktur')
            ->leftJoin('pelanggan', 'pelanggan.kode_pelanggan', '=', 'penjualan.kode_pelanggan')
            ->leftJoin('hrd_karyawan', 'hrd_karyawan.nik', '=', 'penjualan.kode_sales')
            ->select(
                'penjualan_pembayaran_giro.*',
                'pelanggan.nama_pelanggan',
                'hrd_karyawan.nama_lengkap as nama_sales',
                'penjualan.kode_pelanggan',
                'penjualan.kode_sales'
            )
            ->when($request->tanggal_dari, fn($q) => $q->whereDate('penjualan_pembayaran_giro.tanggal', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn($q) => $q->whereDate('penjualan_pembayaran_giro.tanggal', '<=', $request->tanggal_sampai))
            ->when($request->kode_pelanggan, fn($q) => $q->where('penjualan.kode_pelanggan', $request->kode_pelanggan))
            ->when($request->status, fn($q) => $q->where('penjualan_pembayaran_giro.status', $request->status))
            ->orderByDesc('tanggal')
            ->paginate(20);

        return view('transfer_giro.viewGiro', compact('data'));
    }

    public function verifikasiPembayaran(Request $request, $kode)
    {
        $tab = $request->tab;
        $aksi = $request->aksi;
        $tanggal = $request->tanggal;
        $bankId = $request->bank_id;
        $status = $aksi === 'setuju' ? 'disetujui' : 'ditolak';
        $now = now();

        if ($tab === 'transfer') {
            // Ambil data transfer terkait
            $transfer = DB::table('penjualan_pembayaran_transfer')
                ->where('kode_transfer', $kode)
                ->first();

            // Update status transfer
            DB::table('penjualan_pembayaran_transfer')
                ->where('kode_transfer', $kode)
                ->update([
                    'status' => $status,
                    'tanggal_diterima' => $tanggal,
                    'updated_at' => $now
                ]);

            // Jika disetujui, catat ke mutasi
            if ($status === 'disetujui') {
                DB::table('keuangan_mutasi')->insert([
                    'tanggal'     => $tanggal,
                    'keterangan'  => 'Verifikasi transfer dari pelanggan - ' . $transfer->no_faktur,
                    'tipe'        => 'debet',
                    'jumlah'      => $transfer->jumlah,
                    'kode_bank'   => $bankId,
                    'id_user'     => Auth::user()->nik,
                    'created_at'  => $now,
                    'updated_at'  => $now
                ]);
            }
        } elseif ($tab === 'giro') {
            // Ambil data giro
            $giro = DB::table('penjualan_pembayaran_giro')
                ->where('kode_giro', $kode)
                ->first();

            DB::table('penjualan_pembayaran_giro')
                ->where('kode_giro', $kode)
                ->update([
                    'status' => $status,
                    'tanggal_cair' => $tanggal,
                    'updated_at' => $now
                ]);

            if ($status === 'disetujui') {
                DB::table('keuangan_mutasi')->insert([
                    'tanggal'     => $tanggal,
                    'keterangan'  => 'Verifikasi giro dari pelanggan - ' . $giro->no_faktur,
                    'tipe'        => 'debet',
                    'jumlah'      => $giro->jumlah,
                    'kode_bank'   => $bankId,
                    'id_user'     => Auth::user()->nik,
                    'created_at'  => $now,
                    'updated_at'  => $now
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
