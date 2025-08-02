@extends('layouts.print')

@section('title', 'Laporan Penjualan per Pelanggan')

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>Sales</th>
                <th class="text-center">Jumlah Faktur</th>
                <th class="text-center">Faktur Kredit</th>
                <th class="text-center">Faktur Tunai</th>
                <th class="text-end">Total</th>
                <th class="text-end">Diskon</th>
                <th class="text-end">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $total = $diskon = $grand_total = 0;
                $total_faktur = $total_kredit = $total_tunai = 0;
            @endphp
            @forelse ($data as $d)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->salesman }}</td>
                    <td class="text-center">{{ $d->total_transaksi }}</td>
                    <td class="text-center">{{ $d->jumlah_kredit }}</td>
                    <td class="text-center">{{ $d->jumlah_tunai }}</td>
                    <td class="text-end">{{ rupiah($d->total) }}</td>
                    <td class="text-end">{{ rupiah($d->diskon) }}</td>
                    <td class="text-end">{{ rupiah($d->grand_total) }}</td>
                </tr>
                @php
                    $total += $d->total;
                    $diskon += $d->diskon;
                    $grand_total += $d->grand_total;
                    $total_faktur += $d->total_transaksi;
                    $total_kredit += $d->jumlah_kredit;
                    $total_tunai += $d->jumlah_tunai;
                @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end fw-bold">TOTAL</td>
                <td class="text-center fw-bold">{{ $total_faktur }}</td>
                <td class="text-center fw-bold">{{ $total_kredit }}</td>
                <td class="text-center fw-bold">{{ $total_tunai }}</td>
                <td class="text-end fw-bold">{{ rupiah($total) }}</td>
                <td class="text-end fw-bold">{{ rupiah($diskon) }}</td>
                <td class="text-end fw-bold">{{ rupiah($grand_total) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
