<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasKecilController extends Controller
{
    public function index(Request $request)
    {
        $mutasi = collect();
        $totalDebet = $totalKredit = 0;
        $saldoAwal = 0;

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $tanggalDari = $request->tanggal_dari;
            $tanggalSampai = $request->tanggal_sampai;

            $saldoAwal = DB::table('kas_kecil')
                ->where('tanggal', '<', $tanggalDari)
                ->selectRaw("SUM(CASE WHEN tipe = 'debet' THEN jumlah ELSE 0 END) -
                            SUM(CASE WHEN tipe = 'kredit' THEN jumlah ELSE 0 END) as saldo")
                ->value('saldo') ?? 0;

            $mutasi = DB::table('kas_kecil as kb')
                ->whereBetween('kb.tanggal', [$tanggalDari, $tanggalSampai])
                ->orderBy('kb.tanggal')
                ->select('kb.*')
                ->paginate(15);
        }

        return view('kaskecil.index', compact('mutasi', 'saldoAwal'));
    }

    public function store(Request $request)
    {
        DB::table('kas_kecil')->insert([
            'tanggal'     => $request->tanggal,
            'keterangan'  => $request->keterangan,
            'tipe'        => $request->tipe,
            'jumlah'      => (int) str_replace(['Rp', '.', ','], '', $request->jumlah),
            'id_user'     => Auth::user()->id ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('viewKasKecil')->with('success', 'Transaksi berhasil disimpan.');
    }


    public function update(Request $request)
    {
        DB::table('kas_kecil')->where('id', $request->id_mutasi)->update([
            'tanggal'     => $request->tanggal,
            'keterangan'  => $request->keterangan,
            'tipe'        => $request->tipe,
            'jumlah'      => (int) str_replace(['Rp', '.', ','], '', $request->jumlah),
            'id_user'     => Auth::user()->id ?? null,
            'updated_at'  => now(),
        ]);

        return redirect()->route('viewKasKecil')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        DB::table('kas_kecil')->where('id', $id)->delete();
        return redirect()->route('viewKasKecil')->with('success', 'Data berhasil dihapus.');
    }
}
