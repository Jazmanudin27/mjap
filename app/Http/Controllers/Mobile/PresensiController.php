<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class PresensiController extends Controller
{
    public function viewDashboardPresensi()
    {
        $nik = Auth::user()->nik;
        $today = date('Y-m-d');

        // Hitung Hadir (hrd_presensi)
        $hadir = DB::table('hrd_presensi')
            ->where('nik', $nik)
            ->where('status', 'H')
            ->whereDate('tanggal', $today)
            ->count();

        // Hitung Izin (hrd_surat_absen)
        $izin = DB::table('hrd_surat_absen')
            ->where('nik', $nik)
            ->where('jenis_absen', 'I')
            ->whereDate('tanggal', $today)
            ->count();

        // Hitung Sakit (hrd_surat_absen)
        $sakit = DB::table('hrd_surat_absen')
            ->where('nik', $nik)
            ->where('jenis_absen', 'S')
            ->whereDate('tanggal', $today)
            ->count();

        // Hitung Telat (jam_in > 08:00:00)
        $telat = DB::table('hrd_presensi')
            ->where('nik', $nik)
            ->whereDate('tanggal', $today)
            ->where(DB::raw('TIME(jam_in)'), '>', '07:00:00')
            ->count();

        $presensi = DB::table('hrd_presensi')
            ->where('nik', $nik)
            ->where('tanggal', $today)
            ->first();

        return view('mobile.presensi.viewDashboardPresensi', compact('presensi', 'hadir', 'izin', 'sakit', 'telat'));
    }

    public function scanPresensi()
    {
        return view('mobile.presensi.scanPresensi');
    }

    public function storePresensi(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Masuk,Pulang',
            'lat' => 'required',
            'lng' => 'required',
            'selfie' => 'required|string'
        ]);

        $nik = Auth::user()->nik;
        $type = $validated['type'];
        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $selfie = $validated['selfie'];
        $date = now()->format('Y-m-d');
        $time = now()->format('H-i-s'); // gunakan "-" agar aman untuk nama file
        $jamReal = now()->format('Y-m-d H:i:s');

        // Decode gambar base64
        if (preg_match('/^data:image\/(\w+);base64,/', $selfie, $typeMatch)) {
            $image = substr($selfie, strpos($selfie, ',') + 1);
            $image = base64_decode($image);

            $ext = $typeMatch[1]; // jpg/jpeg/png
            $filename = "{$nik}_{$date}_{$time}.{$ext}";
            $filepath = "presensi/{$filename}";

            Storage::disk('public')->put($filepath, $image);
        } else {
            return response()->json(['message' => 'Format gambar tidak valid!'], 422);
        }

        // Cek apakah sudah ada presensi hari ini
        $existing = DB::table('hrd_presensi')->where('nik', $nik)->where('tanggal', $date)->first();

        if (!$existing && $type === 'Masuk') {
            // Buat presensi baru
            DB::table('hrd_presensi')->insert([
                'nik' => $nik,
                'tanggal' => $date,
                'jam_in' => $jamReal,
                'foto_in' => $filepath,
                'lokasi_in' => "{$lat},{$lng}",
                'status' => 'H',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } elseif ($existing && $type === 'Pulang') {
            // Update presensi dengan jam pulang
            DB::table('hrd_presensi')->where('id', $existing->id)->update([
                'jam_out' => $jamReal,
                'foto_out' => $filepath,
                'lokasi_out' => "{$lat},{$lng}",
                'updated_at' => now()
            ]);
        } else {
            return response()->json(['message' => 'Presensi tidak valid atau sudah dilakukan.'], 400);
        }

        return response()->json(['message' => 'Presensi berhasil disimpan!']);
    }

    public function riwayatPresensi(Request $request)
    {
        $query = DB::table('hrd_presensi')->where('nik', Auth::user()->nik);

        $filtered = false;

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
            $filtered = true;
        }

        if (!$filtered) {
            $query->limit(10);
        }

        $presensiList = $query->orderBy('tanggal', 'desc')->get();

        return view('mobile.presensi.riwayatPresensi', compact('presensiList'));
    }

    public function suratAbsen(Request $request)
    {
        $query = DB::table('hrd_surat_absen')->where('nik', Auth::user()->nik);

        $filtered = false;

        // Filter dari tanggal
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
            $filtered = true;
        }

        // Filter sampai tanggal
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
            $filtered = true;
        }

        // Jika tidak difilter, limit 10
        if (!$filtered) {
            $query->limit(10);
        }

        $suratAbsenList = $query->orderBy('tanggal', 'desc')->get();

        return view('mobile.presensi.suratAbsen', compact('suratAbsenList'));
    }


    public function storeSuratAbsen(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'alasan' => 'required|string',
            'foto_surat' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_surat')) {
            $fotoPath = $request->file('foto_surat')->store('hrd_surat_absen', 'public');
        }

        DB::table('hrd_surat_absen')->insert([
            'nik' => Auth::user()->nik,
            'tanggal' => $request->tanggal,
            'jenis_absen' => $request->jenis,
            'alasan' => $request->alasan,
            'foto_surat' => $fotoPath,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['status' => 'success']);
    }

    public function approveSuratAbsen(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $affected = DB::table('hrd_surat_absen')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);

        if ($affected) {
            return response()->json(['message' => 'Surat absen berhasil di-' . strtolower($status)]);
        } else {
            return response()->json(['message' => 'Gagal memperbarui data'], 500);
        }
    }
}
