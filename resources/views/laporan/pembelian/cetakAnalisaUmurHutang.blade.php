@extends('layouts.print')

@section('title', 'Laporan Analisa Umur Hutang')

@section('periode')
    Per {{ tanggal_indo2($tanggal) }}
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">KODE SUPPLIER</th>
                <th rowspan="2">NAMA SUPPLIER</th>
                <th colspan="4" class="text-center">UMUR HUTANG</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <th>BULAN BERJALAN</th>
                <th>1 BULAN</th>
                <th>2 BULAN</th>
                <th>LEBIH 3 BULAN</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $total_0 = $total_1 = $total_2 = $total_3 = $total_all = 0;
            @endphp
            @foreach ($data as $d)
                @php
                    $sub_total = $d->umur_0 + $d->umur_1 + $d->umur_2 + $d->umur_3;
                    $total_0 += $d->umur_0;
                    $total_1 += $d->umur_1;
                    $total_2 += $d->umur_2;
                    $total_3 += $d->umur_3;
                    $total_all += $sub_total;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $d->kode_supplier }}</td>
                    <td>{{ $d->nama_supplier }}</td>
                    <td class="text-end">{{ number_format($d->umur_0, 0) }}</td>
                    <td class="text-end">{{ number_format($d->umur_1, 0) }}</td>
                    <td class="text-end">{{ number_format($d->umur_2, 0) }}</td>
                    <td class="text-end">{{ number_format($d->umur_3, 0) }}</td>
                    <td class="text-end fw-bold">{{ number_format($sub_total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ number_format($total_0, 0) }}</td>
                <td class="text-end fw-bold">{{ number_format($total_1, 0) }}</td>
                <td class="text-end fw-bold">{{ number_format($total_2, 0) }}</td>
                <td class="text-end fw-bold">{{ number_format($total_3, 0) }}</td>
                <td class="text-end fw-bold">{{ number_format($total_all, 0) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
