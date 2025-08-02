@extends('layouts.print')
@section('title', 'Rekap Kartu Hutang per Supplier')
@section('content')
    <h4 class="text-center">Rekap Kartu Hutang per Supplier</h4>
    <p class="text-center">Periode {{ tanggal_indo2($mulai) }} s/d {{ tanggal_indo2($akhir) }}</p>

    @php
        $grouped = $data->groupBy('kode_supplier');
    @endphp

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Supplier</th>
                <th>Nama Supplier</th>
                <th>Total Pembelian</th>
                <th>Total Pembayaran</th>
                <th>Sisa Hutang</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach($grouped as $kode => $list)
                @php
                    $nama = $list->first()->nama_supplier;
                    $total = $list->sum('grand_total');
                    $bayar = $list->sum('sudah_bayar');
                    $sisa = $list->sum('sisa');
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $kode }}</td>
                    <td>{{ $nama }}</td>
                    <td class="text-end">{{ number_format($total, 0) }}</td>
                    <td class="text-end">{{ number_format($bayar, 0) }}</td>
                    <td class="text-end">{{ number_format($sisa, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
