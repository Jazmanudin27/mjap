<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PermissionHelper;
use RupiahHelper;

class PembelianController extends Controller
{
    protected $kategori = 'pembelian';
    public function __construct()
    {
        $this->authorizePermission('pembelian');

        view()->share(PermissionHelper::userPermissions(
            'Edit Pembelian',
            'Delete Pembelian',
            'Tambah Pembelian',
            'Detail Pembelian'
        ));
    }
    public function index(Request $request)
    {
        $this->authorizePermission('pembelian');

        $no_faktur = $request->no_faktur;
        $kode_supplier = $request->kode_supplier;
        $nama_supplier = $request->nama_supplier;
        $jenis_bayar = $request->jenis_bayar;
        $tgl_dari = $request->tanggal_dari;
        $tgl_sampai = $request->tanggal_sampai;

        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();
        $data['pembelian'] = DB::table('pembelian as p')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'p.kode_supplier')
            ->leftJoin(
                DB::raw('(SELECT no_faktur, SUM(jumlah) AS jumlah_bayar
                            FROM pembelian_pembayaran
                            GROUP BY no_faktur) pay'),
                'pay.no_faktur',
                '=',
                'p.no_faktur'
            )
            ->select('p.*', 's.nama_supplier', DB::raw('COALESCE(pay.jumlah_bayar,0) AS jumlah_bayar'))
            ->when($no_faktur, fn($q) => $q->where('p.no_faktur', 'like', "%$no_faktur%"))
            ->when($kode_supplier, fn($q) => $q->where('p.kode_supplier', $kode_supplier))
            ->when($nama_supplier, fn($q) => $q->where('s.nama_supplier', 'like', "%$nama_supplier%"))
            ->when($jenis_bayar, fn($q) => $q->where('p.jenis_bayar', $jenis_bayar))
            ->when($tgl_dari && $tgl_sampai, function ($q) use ($tgl_dari, $tgl_sampai) {
                $q->whereBetween('p.tanggal', [$tgl_dari, $tgl_sampai]);
            })
            ->orderByDesc('p.tanggal')
            ->paginate(10)
            ->appends(request()->query());
        $data['PermissionTambah'] = true;
        return view('pembelian.index', $data);
    }

    public function create()
    {
        $data['barang'] = DB::table('barang')
            ->leftJoin('barang_satuan', 'barang_satuan.kode_barang', 'barang.kode_barang')
            ->where('status', '1')->get();
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();
        return view('pembelian.create', $data);
    }

    public function edit($id)
    {
        $data['pembelian'] = DB::table('pembelian')->where('pembelian.no_faktur', $id)->first();
        $data['supplier'] = DB::table('supplier')->where('supplier.kode_supplier', $data['pembelian']->kode_supplier)->first();
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();
        $data['detail'] = DB::table('pembelian_detail')
            ->join('barang', 'barang.kode_barang', '=', 'pembelian_detail.kode_barang')
            ->select('pembelian_detail.*', 'barang.nama_barang')
            ->where('no_faktur', $id)
            ->get();
        return view('pembelian.edit', $data);
    }
    public function detail($id)
    {

        $data['pembelian'] = DB::table('pembelian')->where('pembelian.no_faktur', $id)->first();
        $data['supplier'] = DB::table('supplier')->where('supplier.kode_supplier', $data['pembelian']->kode_supplier)->first();
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();
        $data['detail'] = DB::table('pembelian_detail')
            ->join('barang', 'barang.kode_barang', '=', 'pembelian_detail.kode_barang')
            ->select('pembelian_detail.*', 'barang.nama_barang')
            ->where('no_faktur', $id)
            ->get();
        $data['pembayaran'] = DB::table('pembelian_pembayaran')
            ->where('pembelian_pembayaran.no_faktur', $id)
            ->get();
        return view('pembelian.detail', $data);
    }

    public function store(Request $request)
    {
        $keranjang = json_decode($request->keranjang, true) ?? [];
        if (empty($keranjang)) {
            return back()->with('error', 'Keranjang pembelian masih kosong!')->withInput();
        }

        if ($request->filled('no_faktur')) {
            $noFaktur = $request->no_faktur;
            $mode = 'edit';
        } else {
            $prefixBulan = now()->format('my');
            $prefixFaktur = '-BL-MJ-' . $prefixBulan;

            // Ambil hanya faktur bulan & tahun ini
            $last = DB::table('pembelian')
                ->where('no_faktur', 'like', "%$prefixFaktur")
                ->selectRaw("CAST(SUBSTRING_INDEX(no_faktur, '-', 1) AS UNSIGNED) as nomor_urut")
                ->orderByDesc('nomor_urut')
                ->first();

            $lastNumber = $last?->nomor_urut ?? 0;
            $nextNumber = $lastNumber + 1;

            $noFaktur = str_pad($nextNumber, 4, '0', STR_PAD_LEFT) . $prefixFaktur;
            $mode = 'tambah';
        }

        $pajak = RupiahHelper::parse($request->pajak);
        $biayaLain = RupiahHelper::parse($request->biaya_lain);
        $grandTotal = RupiahHelper::parse($request->grand_total);
        $potonganClaim = RupiahHelper::parse($request->potongan_claim);

        $totalPotongan = array_sum(array_column($keranjang, 'diskon'));

        DB::beginTransaction();

        if ($mode === 'edit') {
            DB::table('pembelian_detail')->where('no_faktur', $noFaktur)->delete();
            DB::table('mutasi_barang_masuk_detail')->where('no_faktur', $noFaktur)->delete();
            DB::table('mutasi_barang_masuk')->where('no_faktur', $noFaktur)->delete();
            DB::table('pembelian')->where('no_faktur', $noFaktur)->delete();
        }

        DB::table('pembelian')->insert([
            'no_faktur' => $noFaktur,
            'tanggal' => $request->tanggal,
            'jatuh_tempo' => $request->jatuh_tempo,
            'no_po' => $request->no_po ?? '',
            'kode_supplier' => $request->kode_supplier,
            'jenis_transaksi' => $request->jenis_transaksi,
            'potongan' => $totalPotongan,
            'pajak' => $pajak,
            'biaya_lain' => $biayaLain,
            'grand_total' => $grandTotal,
            'keterangan' => $request->keterangan,
            'potongan_claim' => $potonganClaim,
            'id_user' => Auth::user()->nik,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $prefix = 'BM' . date('ym');
        $last = DB::table('mutasi_barang_masuk')
            ->where('kode_transaksi', 'like', "$prefix%")
            ->orderByDesc('kode_transaksi')
            ->value('kode_transaksi');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;
        $kode_transaksi = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

        DB::table('mutasi_barang_masuk')->insert([
            'kode_transaksi' => $kode_transaksi,
            'tanggal' => $request->tanggal,
            'jenis_pemasukan' => 'pembelian',
            'no_faktur' => $noFaktur,
            'sumber' => 'supplier',
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($keranjang as $item) {
            $qty = (float) $item['qty'];
            $harga = (int) $item['harga'];
            $diskon = (int) $item['diskon'];
            $total = $qty * $harga;
            $subtotal = $total - $diskon;
            $barangSatuan = DB::table('barang_satuan')
                ->where('id', $item['satuan_id'] ?? $item['id_satuan'] ?? null)
                ->first();
            $konversi = $barangSatuan->isi ?? 1;
            $qty = $item['qty'];
            $qty_konversi = $qty * $konversi;
            DB::table('pembelian_detail')->insert([
                'no_faktur' => $noFaktur,
                'kode_barang' => $item['kode_barang'],
                'satuan' => $item['satuan'],
                'satuan_id' => $item['satuan_id'] ?? $item['id_satuan'] ?? null,
                'qty' => $qty,
                'harga' => $harga,
                'diskon' => $diskon,
                'total' => $total,
                'subtotal' => $subtotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('mutasi_barang_masuk_detail')->insert([
                'kode_transaksi' => $kode_transaksi,
                'no_faktur' => $noFaktur,
                'satuan_id' => $item['satuan_id'] ?? $item['id_satuan'] ?? null,
                'qty' => $item['qty'],
                'konversi' => $konversi,
                'qty_konversi' => $qty_konversi,
            ]);
        }

        // Cek apakah semua item PO sudah dibeli penuh (bisa ditambahkan logika ini jika dibutuhkan)
        if ($request->filled('no_po')) {
            $po = DB::table('purchase_orders')->where('no_po', $request->no_po)->first();
            $poDetails = DB::table('purchase_order_detail')->where('no_po', $request->no_po)->get();

            $totalQtyPO = $poDetails->sum('qty');
            $totalQtyPembelian = DB::table('pembelian_detail')
                ->join('pembelian', 'pembelian.no_faktur', '=', 'pembelian_detail.no_faktur')
                ->where('pembelian.no_po', $request->no_po)
                ->sum('pembelian_detail.qty');

            // Kalau semua item sudah terpenuhi (atau logikamu), baru ubah jadi closed
            if ($totalQtyPembelian >= $totalQtyPO) {
                DB::table('purchase_orders')
                    ->where('no_po', $request->no_po)
                    ->update(['status' => 'closed']);
            }
        }

        $aksi = $mode === 'edit' ? 'Update Pembelian' : 'Tambah Pembelian';
        logActivity($aksi, "{$aksi} {$noFaktur} (Supplier: {$request->kode_supplier})");

        DB::commit();
        $pesan = $mode === 'edit'
            ? 'Pembelian berhasil diperbarui.'
            : 'Pembelian berhasil disimpan.';
        return redirect()->route('viewPembelian')->with('success', $pesan);
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }
    public function getBarangPembelian($kode_supplier)
    {
        $barang = DB::table('barang')
            ->where('kode_supplier', $kode_supplier)
            ->select('kode_barang', 'nama_barang')
            ->get();

        return response()->json($barang);
    }

    public function delete($id)
    {
        $pembelian = DB::table('pembelian')->where('no_faktur', $id)->first();

        if (!$pembelian) {
            return redirect()->back()->with('error', 'Data pembelian tidak ditemukan.');
        }
        DB::table('pembelian_detail')->where('no_faktur', $pembelian->no_faktur)->delete();
        DB::table('mutasi_barang_masuk_detail')->where('kode_transaksi', $pembelian->no_faktur)->delete();
        DB::table('mutasi_barang_masuk')->where('kode_transaksi', $pembelian->no_faktur)->delete();
        DB::table('pembelian')->where('no_faktur', $id)->delete();
        if ($pembelian->no_po) {
            DB::table('purchase_orders')
                ->where('no_po', $pembelian->no_po)
                ->update(['status' => 'open']);
        }
        return redirect()->route('viewPembelian')->with('success', 'Data pembelian dan mutasi terkait berhasil dihapus.');
    }

    public function getSatuanBarang($kode_barang)
    {
        $satuan = DB::table('barang_satuan')
            ->join('barang', 'barang.kode_barang', 'barang_satuan.kode_barang')
            ->where('barang_satuan.kode_barang', $kode_barang)
            ->select('id', 'satuan', 'isi', 'harga_pokok', 'harga_jual', 'barang.kode_supplier')
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($satuan);
    }

    public function getSupplier(Request $request)
    {
        $term = $request->kode_supplier;

        $data = DB::table('supplier')
            ->select('kode_supplier as id', DB::raw("CONCAT(kode_supplier, ' - ', nama_supplier) as text"))
            ->when($term, function ($query, $term) {
                return $query->where('kode_supplier', 'like', "%{$term}%")
                    ->orWhere('nama_supplier', 'like', "%{$term}%");
            })
            ->orderBy('nama_supplier')
            ->limit(20)
            ->get();

        return response()->json(['results' => $data]);
    }

    public function storePembayaranPembelian(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_bayar' => 'required',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $periode = date('Y-m', strtotime($request->tanggal));
        if (isPeriodeTertutup($periode, $this->kategori)) {
            return response()->json([
                'success' => false,
                'message' => "Gagal, laporan $this->kategori periode $periode sudah ditutup."
            ], 400);
        }

        $ym = date('ym', strtotime($request->tanggal)); // contoh: 2506
        $prefix = 'PB' . $ym;

        $last = DB::table('pembelian_pembayaran')
            ->where('no_bukti', 'like', $prefix . '%')
            ->orderByDesc('no_bukti')
            ->first();

        $nextNumber = 1;
        if ($last && preg_match('/^' . $prefix . '(\d{5})$/', $last->no_bukti, $match)) {
            $nextNumber = intval($match[1]) + 1;
        }

        $no_bukti = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT); // hasil: PB250600001

        DB::table('pembelian_pembayaran')->insert([
            'no_bukti' => $no_bukti,
            'tanggal' => $request->tanggal,
            'no_faktur' => $request->no_faktur,
            'jenis_bayar' => $request->jenis_bayar,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'no_bukti' => $no_bukti]);
    }

    public function updatePembayaranPembelian(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_bayar' => 'required',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $periode = date('Y-m', strtotime($request->tanggal));
        if (isPeriodeTertutup($periode, $this->kategori)) {
            return response()->json([
                'success' => false,
                'message' => "Gagal, laporan $this->kategori periode $periode sudah ditutup."
            ], 400);
        }
        DB::table('pembelian_pembayaran')->where('no_bukti', $id)->update([
            'tanggal' => $request->tanggal,
            'no_faktur' => $request->no_faktur,
            'jenis_bayar' => $request->jenis_bayar,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function deletePembayaranPembelian($id)
    {
        $data = DB::table('pembelian_pembayaran')->where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan.'
            ], 404);
        }

        $periode = date('Y-m', strtotime($data->tanggal));
        if (isPeriodeTertutup($periode, $this->kategori)) {
            return response()->json([
                'success' => false,
                'message' => "Gagal menghapus, laporan $this->kategori periode $periode sudah ditutup."
            ], 400);
        }

        DB::table('pembelian_pembayaran')->where('id', $id)->delete();
        return redirect()->back();
    }

    public function getPOBySupplier($kode)
    {
        $data = DB::table('purchase_orders')
            ->where('kode_supplier', $kode)
            ->where('status', '!=', 'closed')
            ->orderBy('created_at', 'desc')
            ->get(['no_po', 'tanggal', 'potongan_claim']);

        return response()->json($data);
    }

    public function getDetailPO($no_po)
    {

        $detail = DB::table('purchase_order_detail')
            ->join('barang_satuan', 'barang_satuan.id', '=', 'purchase_order_detail.satuan_id')
            ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->select('purchase_order_detail.*', 'barang.nama_barang', 'barang.kode_barang', 'barang_satuan.satuan')
            ->where('no_po', $no_po)
            ->get();

        return response()->json($detail);
    }
}
