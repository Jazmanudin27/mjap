<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('logActivity')) {
    function logActivity($action, $description = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
}

if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tanggal, $format = 'd F Y')
    {
        Carbon::setLocale('id');
        return Carbon::parse($tanggal)->translatedFormat($format);
    }
}

if (!function_exists('tanggal_indo2')) {
    function tanggal_indo2($tanggal)
    {
        $bulan = [
            1 => 'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        $tgl = Carbon::parse($tanggal);
        $day = $tgl->format('d');
        $month = $bulan[(int) $tgl->format('m')];
        $year = $tgl->format('Y');

        return "{$day} {$month} {$year}";
    }
}

if (!function_exists('rupiah')) {
    function rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('formatAngka')) {
    function formatAngka($nilai)
    {
        if (!empty($nilai)) {
            return number_format($nilai, '0', ',', '.');
        }
    }
}


if (!function_exists('formatAngkaDesimal')) {
    function formatAngkaDesimal($nilai)
    {
        if (!empty($nilai)) {
            return number_format($nilai, '2', ',', '.');
        }
    }
}

if (!function_exists('isPeriodeTertutup')) {
    function isPeriodeTertutup($periode, $kategori)
    {
        return DB::table('tutup_laporan')
            ->where('periode', $periode)
            ->where('kategori', $kategori)
            ->where('status', 1)
            ->exists();
    }
}

if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return terbilang($angka / 10) . ' puluh ' . terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'seratus ' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return terbilang($angka / 100) . ' ratus ' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang($angka / 1000) . ' ribu ' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang($angka / 1000000) . ' juta ' . terbilang($angka % 1000000);
        }

        return 'Angka terlalu besar';
    }
}
class RupiahHelper
{
    public static function parse($value)
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        return (int) $clean;
    }

    public static function format($angka, $prefix = 'Rp ')
    {
        if (!is_numeric($angka)) {
            return $prefix . '0';
        }

        return $prefix . number_format($angka, 0, ',', '.');
    }
}

class PermissionHelper
{
    public static function userPermissions(...$names)
    {
        $roleId = Auth::user()->role_id;
        $result = [];

        foreach ($names as $name) {
            $key = str_replace(' ', '', ucwords($name));
            $result[$key] = Permission::getPermission($name, $roleId);
        }

        return $result;
    }
}

function rupiahKosong($nilai)
{
    return number_format($nilai, 0, ',', '.');
}

if (!function_exists('bulan_indo')) {
    function bulan_indo($bulan)
    {
        $nama = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        return $nama[(int) $bulan] ?? '-';
    }

}


if (!function_exists('getSubWilayah')) {
    function getSubWilayah()
    {
        $query = DB::table('sub_wilayah');
        return $query->orderBy('nama_wilayah')->get();
    }
}

if (!function_exists('getWilayah')) {
    function getWilayah()
    {
        $query = DB::table('wilayah');
        return $query->orderBy('nama_wilayah')->get();
    }
}


if (!function_exists('getSales')) {
    function getSales()
    {
        $user = Auth::user();

        $query = DB::table('users')
            ->select('*', 'users.name as nama_lengkap')
            ->where('team', '!=', '');

        // if ($user->role == 'spv sales') {
        //     $query->where('divisi', $user->nik);
        // }

        return $query->orderBy('name')->get();
    }
}

if (!function_exists('getKonversiSatuan')) {
    function getKonversiSatuan($kodeBarang)
    {
        $satuans = DB::table('barang_satuan')
            ->where('kode_barang', $kodeBarang)
            ->orderByDesc('isi')
            ->get();

        return $satuans->pluck('isi', 'satuan')->toArray();
    }
}

if (!function_exists('konversiQtySatuan')) {
    function konversiQtySatuan($qty, $satuanList)
    {
        $result = [];
        foreach ($satuanList as $s) {
            if ($s->isi == 1) {
                if ($qty > 0) {
                    $result[] = $qty . ' ' . $s->satuan;
                }
                break;
            }

            $jumlah = floor($qty / $s->isi);
            if ($jumlah > 0) {
                $result[] = $jumlah . ' ' . $s->satuan;
                $qty -= $jumlah * $s->isi;
            }
        }

        return implode(', ', $result);
    }
}
if (!function_exists('getSisaLimitKreditPelanggan')) {
    function getSisaLimitKreditPelanggan($kodePelanggan)
    {
        // Ambil limit kredit dari pengajuan yang disetujui
        $limitDisetujui = DB::table('pengajuan_limit_kredit')
            ->where('kode_pelanggan', $kodePelanggan)
            ->where('status', 'disetujui')
            ->orderByDesc('tanggal') // Ambil yang terbaru
            ->value('nilai_disetujui');

        // Jika tidak ada pengajuan disetujui, ambil dari tabel pelanggan
        if (!$limitDisetujui) {
            $limitDisetujui = DB::table('pelanggan')
                ->where('kode_pelanggan', $kodePelanggan)
                ->value('limit_pelanggan');
        }

        // Jika tetap tidak ada data, return 0
        if (!$limitDisetujui) {
            return 0;
        }

        // Hitung total faktur kredit yang belum lunas
        $fakturKredit = DB::table('penjualan')
            ->where('kode_pelanggan', $kodePelanggan)
            ->where('jenis_transaksi', 'K')
            ->where('batal', 0)
            ->get();

        $totalBelumLunas = 0;

        foreach ($fakturKredit as $faktur) {
            $totalPembayaran = DB::table('penjualan_pembayaran')->where('no_faktur', $faktur->no_faktur)->sum('jumlah');
            $totalPembayaran += DB::table('penjualan_pembayaran_transfer')->where('no_faktur', $faktur->no_faktur)->sum('jumlah');
            $totalPembayaran += DB::table('penjualan_pembayaran_giro')->where('no_faktur', $faktur->no_faktur)->sum('jumlah');

            if ($totalPembayaran < $faktur->grand_total) {
                $totalBelumLunas += ($faktur->grand_total - $totalPembayaran);
            }
        }

        $sisaLimit = $limitDisetujui - $totalBelumLunas;
        return $sisaLimit > 0 ? $sisaLimit : 0;
    }
}

