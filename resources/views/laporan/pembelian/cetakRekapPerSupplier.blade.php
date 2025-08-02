@extends('layouts.print')

@section('title', 'Rekap Pembelian Per Supplier')

@section('periode')
    Periode {{ tanggal_indo2($mulai) }} s/d {{ tanggal_indo2($sampai) }}
@endsection

@section('content')
    <table class="table table-bordered" style="width:50%">
        <thead>
            <tr>
                <th>NO</th>
                <th>KODE SUPPLIER</th>
                <th>NAMA SUPPLIER</th>
                <th class="text-end">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $total_grand = 0;
                $total_bayar = 0;
                $total_sisa = 0;
            @endphp
            @foreach ($data as $d)
                @php
                    $total_grand += $d->total_pembelian;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $d->kode_supplier }}</td>
                    <td>{{ $d->nama_supplier }}</td>
                    <td class="text-end">{{ number_format($d->total_pembelian, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">GRAND TOTAL</td>
                <td class="text-end fw-bold">{{ number_format($total_grand, 0) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
