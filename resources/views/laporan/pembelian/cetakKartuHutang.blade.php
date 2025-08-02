@extends('layouts.print')
@section('title', 'Kartu Hutang Detail Barang')

@section('content')
    <p class="text-center">
        Periode {{ tanggal_indo2($mulai) }} s/d {{ tanggal_indo2($akhir) }}<br>
        @if($nama_supplier) Supplier: <strong>{{ $nama_supplier }}</strong> @endif
    </p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tgl Faktur</th>
                <th>Jatuh Tempo</th>
                <th>Supplier</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Grand Total</th>
                <th>Sudah Dibayar</th>
                <th>Sisa Hutang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $row)
                @php $rowspan = $row->detail->count(); @endphp
                @foreach($row->detail as $index => $d)
                    <tr>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->no_faktur }}</td>
                            <td rowspan="{{ $rowspan }}">{{ tanggal_indo2($row->tanggal) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ tanggal_indo2($row->jatuh_tempo) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $row->nama_supplier }}</td>
                        @endif
                        <td>{{ $d->kode_barang }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td>{{ $d->satuan }}</td>
                        <td class="text-end">{{ number_format($d->qty, 0) }}</td>
                        <td class="text-end">{{ number_format($d->harga, 0) }}</td>
                        <td class="text-end">{{ number_format($d->subtotal, 0) }}</td>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}" class="text-end">{{ number_format($row->grand_total, 0) }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-end">{{ number_format($row->sudah_bayar, 0) }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-end">{{ number_format($row->sisa, 0) }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center">
                                @if($row->status == 'Lunas')
                                    <span class="bg-lunas">Lunas</span>
                                @else
                                    <span class="bg-belum">Belum</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endsection
