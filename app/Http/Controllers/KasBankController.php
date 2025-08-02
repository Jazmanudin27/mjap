<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasBankController extends Controller
{
    public function index(Request $request)
    {
        $bank = DB::table('bank')->get();

        $mutasi = collect();
        $totalDebet = $totalKredit = 0;
        $saldoAwal = 0;

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $tanggalDari = $request->tanggal_dari;
            $tanggalSampai = $request->tanggal_sampai;

            $saldoAwal = DB::table('keuangan_mutasi')
                ->where('tanggal', '<', $tanggalDari)
                ->when($request->kode_bank, function ($q) use ($request) {
                    $q->where('kode_bank', $request->kode_bank);
                })
                ->selectRaw("SUM(CASE WHEN tipe = 'debet' THEN jumlah ELSE 0 END) -
                            SUM(CASE WHEN tipe = 'kredit' THEN jumlah ELSE 0 END) as saldo")
                ->value('saldo') ?? 0;

            $mutasi = DB::table('keuangan_mutasi as kb')
                ->join('bank as b', 'b.id', '=', 'kb.kode_bank')
                ->whereBetween('kb.tanggal', [$tanggalDari, $tanggalSampai])
                ->when($request->kode_bank, function ($q) use ($request) {
                    $q->where('kb.kode_bank', $request->kode_bank);
                })
                ->orderBy('kb.tanggal')
                ->select('kb.*', 'b.nama_bank')
                ->paginate(15);
        }

        return view('kasbank.index', compact('bank', 'mutasi', 'saldoAwal'));
    }

    public function store(Request $request)
    {
        DB::table('keuangan_mutasi')->insert([
            'tanggal'     => $request->tanggal,
            'keterangan'  => $request->keterangan,
            'tipe'        => $request->tipe,
            'jumlah'      => (int) str_replace(['Rp', '.', ','], '', $request->jumlah),
            'kode_bank'   => $request->kode_bank,
            'id_user'     => Auth::user()->id ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('viewKasBank')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function delete($id)
    {
        DB::table('keuangan_mutasi')->where('id', $id)->delete();
        return redirect()->route('viewKasBank')->with('success', 'Data berhasil dihapus.');
    }
}
