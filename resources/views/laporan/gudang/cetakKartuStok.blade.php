@extends('layouts.print')

@section('title', 'Laporan Persediaan Stok Good Stok')

@section('periode')
    Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d M Y') }} s/d
    {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d M Y') }} <br>
    @if (!empty($nama_barang))
        Barang: <span class="text-primary fw-bold">{{ $nama_barang }}</span>
    @endif
@endsection

@section('content')

    @php
        // Fungsi konversi ke satuan besar
        function konversiQty($qty, $satuanDefault, $satuanList)
        {
            if ($qty == 0) {
                return '';
            }

            $saldo = abs($qty);
            $konversi = [];

            foreach ($satuanList as $sat) {
                if ($sat->isi > 1) {
                    $jumlah = intdiv($saldo, $sat->isi);
                    if ($jumlah > 0) {
                        $konversi[] = $jumlah . ' ' . $sat->satuan;
                        $saldo = $saldo % $sat->isi;
                    }
                }
            }

            if ($saldo > 0) {
                $konversi[] = $saldo . ' ' . $satuanDefault;
            }

            $hasil = implode(', ', $konversi);
            return $qty < 0 ? '-' . $hasil : $hasil;
        }
    @endphp

    <table class="table-bordered table-sm w-100" style="font-size: 12px; border-collapse: collapse;">
        <thead>
            <tr class="bg-primary text-white">
                <th rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No Faktur</th>
                <th rowspan="2">Kode Sales</th>
                <th rowspan="2">Nama Sales</th>
                <th colspan="5" class="text-center" style="background-color: #28a745; color: white;">PENERIMAAN</th>
                <th colspan="4" class="text-center" style="background-color: #dc3545; color: white;">PENGELUARAN</th>
                <th>Saldo</th>
            </tr>
            <tr style="background-color: #e3f0ff;">
                <th class="text-center" style="background-color: #1e7e34; color: white;">Pembelian</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Retur Pengganti</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Repack</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Penyesuaian</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Lain-lain</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Penjualan</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Reject</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Penyesuaian</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Lain-lain</th>
                <th class="text-white">
                    {{ konversiQty($saldoawal, $satuan, $satuan_barang) }}
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $running = $saldoawal ?? 0;
                $no = 1;
            @endphp

            @foreach ($data as $item)
                @php
                    $opening = $running;
                    $pembelian = $item->pembelian ?? 0;
                    $retur_pengganti = $item->retur_pengganti ?? 0;
                    $repack = $item->repack ?? 0;
                    $penyesuaian_masuk = $item->penyesuaian_masuk ?? 0;
                    $lainnya_masuk = $item->lainnya_masuk ?? 0;
                    $penjualan = $item->penjualan ?? 0;
                    $reject_gudang = $item->reject_gudang ?? 0;
                    $penyesuaian_keluar = $item->penyesuaian_keluar ?? 0;
                    $lainnya_keluar = $item->lainnya_keluar ?? 0;

                    $total_penerimaan = $pembelian + $retur_pengganti + $repack + $penyesuaian_masuk + $lainnya_masuk;
                    $total_pengeluaran = $penjualan + $reject_gudang + $penyesuaian_keluar + $lainnya_keluar;

                    $running = $opening + $total_penerimaan - $total_pengeluaran;
                @endphp

                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                    <td class="text-end">{{ $item->no_faktur }}</td>
                    <td class="text-end">{{ $item->kode_sales }}</td>
                    <td class="text-end">{{ $item->nama_sales }}</td>
                    <td class="text-end">{{ konversiQty($pembelian, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($retur_pengganti, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($repack, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($penyesuaian_masuk, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($lainnya_masuk, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($penjualan, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($reject_gudang, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($penyesuaian_keluar, $satuan, $satuan_barang) }}</td>
                    <td class="text-end">{{ konversiQty($lainnya_keluar, $satuan, $satuan_barang) }}</td>
                    <td class="text-end fw-bold {{ $running < 0 ? 'text-danger' : '' }}">
                        {{ konversiQty($running, $satuan, $satuan_barang) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
