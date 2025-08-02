@extends('layouts.print')

@section('title', 'Laporan Persediaan Stok Good Stok')

@section('periode')
    Periode: {{ nama_bulan($bulan) }} {{ $tahun }} <br>
    @if ($nama_supplier)
        Supplier: <span style="font-weight:600; color:#0d6efd;">{{ $nama_supplier }}</span>
    @endif
@endsection

@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 12px; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #0d6efd; color: white;">
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2">Satuan</th>
                <th rowspan="2">Saldo Awal</th>
                <th colspan="5" class="text-center" style="background-color: #28a745; color: white;">PENERIMAAN</th>
                <th colspan="4" class="text-center" style="background-color: #dc3545; color: white;">PENGELUARAN</th>
                <th rowspan="2">Saldo Akhir</th>
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
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($data as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td class="text-end">
                        @if ($item->saldo_awal > 0)
                            {{ number_format($item->saldo_awal) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->pembelian > 0)
                            {{ number_format($item->pembelian) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->retur_pengganti > 0)
                            {{ number_format($item->retur_pengganti) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->repack > 0)
                            {{ number_format($item->repack) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->penyesuaian_masuk > 0)
                            {{ number_format($item->penyesuaian_masuk) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->lainnya_masuk > 0)
                            {{ number_format($item->lainnya_masuk) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->penjualan > 0)
                            {{ number_format($item->penjualan) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->reject_gudang > 0)
                            {{ number_format($item->reject_gudang) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->penyesuaian_keluar > 0)
                            {{ number_format($item->penyesuaian_keluar) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->lainnya_keluar > 0)
                            {{ number_format($item->lainnya_keluar) }}
                        @endif
                    </td>

                    {{-- Saldo Akhir --}}
                    <td class="text-end fw-bold">
                        @php
                            $penerimaan =
                                $item->pembelian +
                                $item->repack +
                                $item->retur_pengganti +
                                $item->penyesuaian_masuk +
                                $item->lainnya_masuk;
                            $pengeluaran =
                                $item->penjualan +
                                $item->reject_gudang +
                                $item->penyesuaian_keluar +
                                $item->lainnya_keluar;
                            $saldoAkhir = $item->saldo_awal + $penerimaan - $pengeluaran;
                        @endphp
                        @if ($saldoAkhir != 0)
                            {{ number_format($saldoAkhir) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@php
    function nama_bulan($bulan)
    {
        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $bulanIndo[intval($bulan)] ?? '';
    }
@endphp
