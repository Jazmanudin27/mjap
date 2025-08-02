@extends('layouts.print')

@section('title', 'Laporan Persediaan Stok Bad Stok')

@section('periode')
    Periode: {{ nama_bulan($bulan) }} {{ $tahun }} <br>
    @if($nama_supplier)
        Supplier: <span style="color:#0d6efd; font-weight:600;">{{ $nama_supplier }}</span>
    @endif
@endsection

@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 12px; border-collapse: collapse;">
        <thead style="background-color: #0d6efd; color: white;">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2">Satuan</th>
                <th rowspan="2">Saldo Awal</th>
                <th colspan="4" class="text-center" style="background-color: #28a745; color: white;">PENERIMAAN</th>
                <th colspan="3" class="text-center" style="background-color: #dc3545; color: white;">PENGELUARAN</th>
                <th rowspan="2">Saldo Akhir</th>
            </tr>
            <tr>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Retur Penjualan</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Reject</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Penyesuaian</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Lain-lain</th>

                <th class="text-center" style="background-color: #c82333; color: white;">Retur Pembelian</th>
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

                    <td class="text-end">@if ($item->saldo_awal > 0) {{ number_format($item->saldo_awal) }} @endif</td>

                    {{-- PENERIMAAN --}}
                    <td class="text-end">@if ($item->retur_penjualan > 0) {{ number_format($item->retur_penjualan) }} @endif
                    </td>
                    <td class="text-end">@if ($item->reject_gudang > 0) {{ number_format($item->reject_gudang) }} @endif</td>
                    <td class="text-end">@if ($item->penyesuaian_masuk > 0) {{ number_format($item->penyesuaian_masuk) }} @endif
                    </td>
                    <td class="text-end">@if ($item->lainnya_masuk > 0) {{ number_format($item->lainnya_masuk) }} @endif</td>

                    {{-- PENGELUARAN --}}
                    <td class="text-end">@if ($item->retur_pembelian > 0) {{ number_format($item->retur_pembelian) }} @endif
                    </td>
                    <td class="text-end">@if ($item->penyesuaian_keluar > 0) {{ number_format($item->penyesuaian_keluar) }}
                    @endif</td>
                    <td class="text-end">@if ($item->lainnya_keluar > 0) {{ number_format($item->lainnya_keluar) }} @endif</td>

                    <td class="text-end fw-bold">
                        @php
                            $saldoAkhir = ($item->retur_penjualan + $item->reject_gudang + $item->penyesuaian_masuk + $item->lainnya_masuk)
                                - ($item->retur_pembelian + $item->penyesuaian_keluar + $item->lainnya_keluar);
                        @endphp
                        @if ($saldoAkhir > 0)
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
            12 => 'Desember'
        ];
        return $bulanIndo[intval($bulan)] ?? '';
    }
@endphp
