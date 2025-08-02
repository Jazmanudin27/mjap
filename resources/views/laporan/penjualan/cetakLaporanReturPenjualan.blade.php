@extends('layouts.print')

@section('title', 'Laporan Retur Penjualan')

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Retur</th>
                <th>No. Faktur</th>
                <th>Jenis Retur</th>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>Sales</th>
                <th class="text-end">Total Retur</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $total_retur = 0;
            @endphp
            @forelse ($data as $d)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ tanggal_indo($d->tanggal) }}</td>
                    <td>{{ $d->no_retur }}</td>
                    <td>{{ $d->no_faktur }}</td>
                    <td>{{ ucfirst($d->jenis_retur) }}</td>
                    <td>{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->sales }}</td>
                    <td class="text-end">{{ rupiah($d->total) }}</td>
                    <td>{{ $d->keterangan }}</td>
                </tr>
                @php
                    $total_retur += $d->total;
                @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ rupiah($total_retur) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endsection
