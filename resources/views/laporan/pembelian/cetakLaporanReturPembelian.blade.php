@extends('layouts.print')

@section('title', 'Laporan Retur Pembelian')

@section('periode')
    Periode: {{ tanggal_indo($tanggal_mulai) }} s/d {{ tanggal_indo($tanggal_sampai) }}
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No. Retur</th>
                <th rowspan="2">No. Faktur</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Supplier</th>
                <th colspan="4" class="text-center">Data Barang</th>
                <th rowspan="2">Jenis</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Qty</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1;
            $grand_total = 0; @endphp
            @forelse ($data as $row)
                @php $rowspan = count($row->detail); @endphp
                @foreach ($row->detail as $i => $item)
                    <tr>
                        @if ($i == 0)
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ tanggal_indo($row->tanggal) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->no_retur }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->no_faktur }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->kode_supplier }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->nama_supplier }}</td>
                        @endif
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="text-end">{{ number_format($item->qty, 0) }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        @if ($i == 0)
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ ucfirst($row->jenis_retur) }}</td>
                            <td class="text-end" rowspan="{{ $rowspan }}">{{ number_format($row->total, 0) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->keterangan }}</td>
                        @endif
                    </tr>
                @endforeach
                @php $grand_total += $row->total; @endphp
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="text-end">GRAND TOTAL</td>
                <td class="text-end">{{ number_format($grand_total, 0) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endsection
