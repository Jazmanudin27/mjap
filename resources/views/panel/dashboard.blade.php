@extends('layouts.template')
@section('titlepage', 'Dashboard Admin')
@section('contents')

    @php
        use Illuminate\Support\Facades\DB;
        use Carbon\Carbon;

        $hariIni = Carbon::today();
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $barangTerlaris = DB::table('penjualan_detail as d')
            ->join('penjualan as p', 'p.no_faktur', '=', 'd.no_faktur')
            ->join('barang_satuan as bs', 'bs.id', '=', 'd.satuan_id')
            ->join('barang as b', 'b.kode_barang', '=', 'bs.kode_barang')
            ->leftJoin('supplier as s', 's.kode_supplier', '=', 'b.kode_supplier')
            ->select(
                'd.kode_barang',
                'b.nama_barang',
                's.nama_supplier',
                'bs.satuan',
                DB::raw('SUM(d.qty) as total_terjual')
            )
            ->where('p.batal', 0)
            ->groupBy('d.kode_barang', 'b.nama_barang', 's.nama_supplier', 'bs.satuan')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();
        // Total penjualan hari ini
        $penjualanHariIni = DB::table('penjualan')
            ->whereDate('tanggal', Date('Y-m-d'))
            ->where('batal', 0)
            ->sum('grand_total');

        // Total penjualan bulan ini
        $penjualanBulanIni = DB::table('penjualan')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('batal', 0)
            ->sum('grand_total');

        // Jumlah faktur
        $jumlahFaktur = DB::table('penjualan')
            ->whereYear('tanggal', $tahunIni)
            ->count();

        // Faktur batal
        $fakturBatal = DB::table('penjualan')
            ->where('batal', 1)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        // Format rupiah
        function formatRupiah($angka)
        {
            return 'Rp ' . number_format($angka, 0, ',', '.');
        }

        $dataKartu = [
            ['title' => 'Penjualan Hari Ini', 'icon' => 'bi-cash-coin', 'value' => formatRupiah($penjualanHariIni), 'bg' => 'success'],
            ['title' => 'Penjualan Bulan Ini', 'icon' => 'bi-calendar-range', 'value' => formatRupiah($penjualanBulanIni), 'bg' => 'primary'],
            ['title' => 'Jumlah Faktur', 'icon' => 'bi-receipt', 'value' => $jumlahFaktur, 'bg' => 'warning'],
            ['title' => 'Faktur Batal', 'icon' => 'bi-x-circle', 'value' => $fakturBatal, 'bg' => 'danger'],
        ];

        $colSize = count($dataKartu) <= 2 ? 'col-md-6' : 'col-md-3';

        // Grafik Penjualan Bulanan (dinamis)
        $dataBulan = [];
        $dataTotal = [];

        for ($i = 1; $i <= 12; $i++) {
            $bulanNama = Carbon::create()->month($i)->locale('id')->translatedFormat('M');
            $total = DB::table('penjualan')
                ->whereMonth('tanggal', $i)
                ->whereYear('tanggal', $tahunIni)
                ->where('batal', 0)
                ->sum('grand_total');

            $dataBulan[] = $bulanNama;
            $dataTotal[] = $total;
        }

        $topPelanggan = DB::table('penjualan as p')
            ->join('pelanggan as pl', 'pl.kode_pelanggan', '=', 'p.kode_pelanggan')
            ->select('p.kode_pelanggan', 'pl.nama_pelanggan', DB::raw('SUM(p.grand_total) as total_belanja'))
            ->where('p.batal', 0)
            ->whereYear('p.tanggal', $tahunIni)
            ->groupBy('p.kode_pelanggan', 'pl.nama_pelanggan')
            ->orderByDesc('total_belanja')
            ->limit(5)
            ->get();
        $stokMinim = DB::table('barang')
            ->leftJoin('barang_satuan', function ($q) {
                $q->on('barang.kode_barang', '=', 'barang_satuan.kode_barang')
                    ->where('barang_satuan.isi', 1);
            })
            ->leftJoin(DB::raw("( SELECT kode_barang, SUM(qty) AS saldo_awal FROM saldo_awal_gs WHERE bulan = {$bulanIni} AND tahun = {$tahunIni} GROUP BY kode_barang ) AS sa"), 'barang.kode_barang', '=', 'sa.kode_barang')
            ->leftJoin(DB::raw("(SELECT bs.kode_barang,SUM(CASE WHEN jenis_pemasukan = 'Pembelian' THEN mbd.qty ELSE 0 END) AS pembelian, SUM(CASE WHEN jenis_pemasukan = 'Retur Penjualan' THEN mbd.qty ELSE 0 END) AS retur_penjualan, SUM(CASE WHEN jenis_pemasukan = 'Penyesuaian' THEN mbd.qty ELSE 0 END) AS penyesuaian_masuk, SUM(CASE WHEN jenis_pemasukan = 'Lainnya' THEN mbd.qty ELSE 0 END) AS lainnya_masuk FROM mutasi_barang_masuk_detail mbd JOIN mutasi_barang_masuk mb ON mb.kode_transaksi = mbd.kode_transaksi JOIN barang_satuan bs ON bs.id = mbd.satuan_id WHERE mb.kondisi = 'gs' AND MONTH(mb.tanggal) = {$bulanIni} AND YEAR(mb.tanggal) = {$tahunIni} GROUP BY bs.kode_barang ) AS masuk"), 'barang.kode_barang', '=', 'masuk.kode_barang')
            ->leftJoin(DB::raw("(SELECT bs.kode_barang,SUM(CASE WHEN jenis_pengeluaran = 'Penjualan' THEN mkd.qty_konversi ELSE 0 END) AS penjualan, SUM(CASE WHEN jenis_pengeluaran = 'Retur Pembelian' THEN mkd.qty_konversi ELSE 0 END) AS retur_pembelian,SUM(CASE WHEN jenis_pengeluaran = 'Penyesuaian' THEN mkd.qty_konversi ELSE 0 END) AS penyesuaian_keluar, SUM(CASE WHEN jenis_pengeluaran = 'Lainnya' THEN mkd.qty_konversi ELSE 0 END) AS lainnya_keluar FROM mutasi_barang_keluar_detail mkd JOIN mutasi_barang_keluar mk ON mk.kode_transaksi = mkd.kode_transaksi JOIN barang_satuan bs ON bs.id = mkd.satuan_id WHERE mk.kondisi = 'gs' AND MONTH(mk.tanggal) = {$bulanIni} AND YEAR(mk.tanggal) = {$tahunIni} GROUP BY bs.kode_barang ) AS keluar"), 'barang.kode_barang', '=', 'keluar.kode_barang')
            ->select(
                'barang.kode_barang',
                'barang.nama_barang',
                'barang_satuan.satuan',
                DB::raw('COALESCE(saldo_awal, 0)+ COALESCE(pembelian, 0) + COALESCE(retur_penjualan, 0) + COALESCE(penyesuaian_masuk, 0) + COALESCE(lainnya_masuk, 0) - COALESCE(penjualan, 0) - COALESCE(retur_pembelian, 0) - COALESCE(penyesuaian_keluar, 0) - COALESCE(lainnya_keluar, 0) AS saldo_akhir')
            )
            ->having('saldo_akhir', '<=', 10)
            ->orderBy('saldo_akhir')
            ->limit(10)
            ->get();
    @endphp
    <div class="row g-4 mt-3">
        @foreach ($dataKartu as $card)
            <div class="{{ $colSize }}">
                <div class="card border-0 shadow-sm bg-{{ $card['bg'] }} text-white rounded-4 h-100">
                    <div class="card-body py-4 px-4 d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            <i class="bi {{ $card['icon'] }} fs-4 opacity-75"></i> {{-- dari fs-2 → fs-4 --}}
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-white text-opacity-75 small fw-semibold text-uppercase">
                                {{ $card['title'] }}
                            </div>
                            <div class="fs-6 fw-semibold">{{ $card['value'] }}</div> {{-- dari fs-4 → fs-6 --}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Grafik --}}
    <div class="row mt-4">
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Grafik Penjualan Bulanan</h5>
                    <canvas id="penjualanChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- 10 Barang Terlaris --}}
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Top 5 Pelanggan</h5>
                    <ul class="list-group list-group-flush">
                        @forelse ($topPelanggan as $pel)
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="bi bi-person-badge me-2 text-secondary"></i>
                                    {{ $pel->kode_pelanggan . " - " . $pel->nama_pelanggan }}</span>
                                <span class="badge bg-success">{{ formatRupiah($pel->total_belanja) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Tidak ada data pelanggan</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- Top Pelanggan --}}
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">10 Barang Terlaris</h5>
            <div class="table-responsive" style="max-height: 300px; overflow-y:auto">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Supplier</th>
                            <th>Total Terjual</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangTerlaris as $i => $brg)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $brg->kode_barang }}</td>
                                <td>{{ $brg->nama_barang }}</td>
                                <td>{{ $brg->nama_supplier ?? '-' }}</td>
                                <td class="text-end">{{ number_format($brg->total_terjual) }}</td>
                                <td>{{ $brg->satuan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Stok Minim --}}
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-danger">Barang dengan Stok Minim</h5>
            @if ($stokMinim->isEmpty())
                <p class="text-muted">Semua stok aman.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($stokMinim as $brg)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>
                                <i class="bi bi-exclamation-circle me-2 text-danger"></i>
                                {{ $brg->nama_barang }} ({{ $brg->kode_barang }})
                            </span>
                            <span class="badge bg-danger">Stok: {{ $brg->saldo_akhir }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('penjualanChart').getContext('2d');
        const penjualanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dataBulan) !!},
                datasets: [{
                    label: 'Total Penjualan',
                    data: {!! json_encode($dataTotal) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>

@endsection
