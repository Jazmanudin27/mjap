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

class POController extends Controller
{
    protected $kategori = 'pembelian';

    public function __construct()
    {
        $this->authorizePermission('pembelian');

        view()->share(PermissionHelper::userPermissions(
            'Edit PO',
            'Delete PO',
            'Tambah PO',
            'Detail PO'
        ));
    }

    public function index(Request $request)
    {
        $no_po         = $request->no_po;
        $kode_supplier = $request->kode_supplier;
        $nama_supplier = $request->nama_supplier;
        $status        = $request->status;
        $tgl_dari      = $request->tanggal_dari;
        $tgl_sampai    = $request->tanggal_sampai;

        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();
        $data['po'] = DB::table('purchase_orders as po')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'po.kode_supplier')
            ->select('po.*', 's.nama_supplier')
            ->when($no_po,         fn($q) => $q->where('po.no_po',    'like', "%$no_po%"))
            ->when($kode_supplier, fn($q) => $q->where('po.kode_supplier',     $kode_supplier))
            ->when($nama_supplier, fn($q) => $q->where('s.nama_supplier', 'like', "%$nama_supplier%"))
            ->when($status,        fn($q) => $q->where('po.status',            $status))
            ->when($tgl_dari && $tgl_sampai, function ($q) use ($tgl_dari, $tgl_sampai) {
                $q->whereBetween('po.tanggal', [$tgl_dari, $tgl_sampai]);
            })
            ->orderByDesc('po.tanggal')
            ->paginate(10)
            ->appends(request()->query());

        $data['PermissionTambah'] = true;
        return view('po.index', $data);
    }

    public function create()
    {
        $data['barang'] = DB::table('barang')
            ->leftJoin('barang_satuan', 'barang_satuan.kode_barang', 'barang.kode_barang')
            ->where('barang.status', '1')
            ->get();

        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();
        return view('po.create', $data);
    }

    public function edit($id)
    {
        $data['po'] = DB::table('purchase_orders')->where('no_po', $id)->first();
        $data['supplier'] = DB::table('supplier')->where('kode_supplier', $data['po']->kode_supplier)->first();
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();

        $data['detail'] = DB::table('purchase_order_detail')
            ->join('barang_satuan', 'barang_satuan.id', '=', 'purchase_order_detail.satuan_id')
            ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->select('purchase_order_detail.*', 'barang.nama_barang', 'barang.kode_barang', 'barang_satuan.satuan')
            ->where('no_po', $id)
            ->get();

        return view('po.edit', $data);
    }

    public function detail($id)
    {
        $data['po'] = DB::table('purchase_orders')->where('no_po', $id)->first();
        $data['karyawan'] = DB::table('hrd_karyawan')->where('nik', $data['po']->id_user)->first();
        $data['supplier'] = DB::table('supplier')->where('kode_supplier', $data['po']->kode_supplier)->first();
        $data['suppliers'] = DB::table('supplier')->where('status', '1')->orderBy('nama_supplier')->get();

        $data['detail'] = DB::table('purchase_order_detail')
            ->join('barang_satuan', 'barang_satuan.id', '=', 'purchase_order_detail.satuan_id')
            ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
            ->select('purchase_order_detail.*', 'barang.nama_barang', 'barang.kode_barang', 'barang_satuan.satuan')
            ->where('no_po', $id)
            ->get();

        return view('po.detail', $data);
    }

    public function store(Request $request)
    {
        $keranjang = json_decode($request->keranjang, true) ?? [];
        if (empty($keranjang)) {
            return back()->with('error', 'Keranjang PO masih kosong!')->withInput();
        }

        $noPo = $request->no_po;
        $mode = $noPo ? 'edit' : 'tambah';

        if (!$noPo) {
            $prefixBulan = now()->format('my');
            $prefixFaktur = '-PO-MJ-' . $prefixBulan;

            $last = DB::table('purchase_orders')
                ->where('no_po', 'like', "%$prefixFaktur")
                ->selectRaw("CAST(SUBSTRING_INDEX(no_po, '-', 1) AS UNSIGNED) as nomor_urut")
                ->orderByDesc('nomor_urut')
                ->first();

            $lastNumber = $last?->nomor_urut ?? 0;
            $nextNumber = $lastNumber + 1;

            $noPo = str_pad($nextNumber, 4, '0', STR_PAD_LEFT) . $prefixFaktur;
        }

        $pajak          = RupiahHelper::parse($request->pajak);
        $potonganKlaim  = RupiahHelper::parse($request->potongan_claim);
        $grandTotal     = RupiahHelper::parse($request->grand_total);

        $totalPotongan = array_sum(array_column($keranjang, 'diskon'));

        DB::beginTransaction();

        if ($mode === 'edit') {
            DB::table('purchase_order_detail')->where('no_po', $noPo)->delete();
            DB::table('purchase_orders')->where('no_po', $noPo)->delete();
        }

        DB::table('purchase_orders')->insert([
            'no_po'           => $noPo,
            'tanggal'         => $request->tanggal,
            'jatuh_tempo'     => $request->jatuh_tempo,
            'kode_supplier'   => $request->kode_supplier,
            'jenis_transaksi' => $request->jenis_transaksi,
            'potongan'        => $totalPotongan,
            'pajak'           => $pajak,
            'potongan_claim'  => $potonganKlaim,
            'grand_total'     => $grandTotal,
            'status'          => 'open',
            'keterangan'      => $request->keterangan,
            'id_user'         => Auth::user()->nik,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        foreach ($keranjang as $item) {
            $qty      = (float) $item['qty'];
            $harga    = (int)  $item['harga'];
            $diskon   = (int)  $item['diskon'];
            $total    = $qty * $harga;
            $subtotal = $total - $diskon;

            DB::table('purchase_order_detail')->insert([
                'no_po'       => $noPo,
                'satuan_id'   => $item['satuan_id'] ?? $item['id_satuan'] ?? null,
                'qty'         => $qty,
                'harga'       => $harga,
                'diskon'      => $diskon,
            ]);
        }

        logActivity(($mode === 'edit' ? 'Update PO' : 'Tambah PO'), "{$mode} {$noPo} (Supplier: {$request->kode_supplier})");

        DB::commit();
        return redirect()->route('viewPO')->with('success', $mode === 'edit' ? 'PO berhasil diperbarui.' : 'PO berhasil disimpan.');
    }

    public function delete($id)
    {
        $po = DB::table('purchase_orders')->where('no_po', $id)->first();

        if (!$po) {
            return redirect()->back()->with('error', 'Data PO tidak ditemukan.');
        }

        DB::table('purchase_order_detail')->where('no_po', $po->no_po)->delete();
        DB::table('purchase_orders')->where('no_po', $id)->delete();

        return redirect()->route('viewPO')->with('success', 'Data PO berhasil dihapus.');
    }

    private function authorizePermission($permission)
    {
        if (!Permission::getPermission($permission, Auth::user()->role_id)) {
            abort(404);
        }
    }

    public function getBarangPO($kode_supplier)
    {
        $barang = DB::table('barang')
            ->where('kode_supplier', $kode_supplier)
            ->select('kode_barang', 'nama_barang')
            ->get();

        return response()->json($barang);
    }
}
