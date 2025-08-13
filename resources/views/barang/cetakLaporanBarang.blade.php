@extends('layouts.print')

@section('title', 'Laporan Barang')

@section('periode')
    {{ $nama_supplier ?? 'Semua Supplier' }} <br>
@endsection
@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 12px; border-collapse: collapse;">
        <thead style="background-color: #f1f1f1;">
            <tr>
                <th>No</th>
                <th>Supplier</th>
                <th>Kode Barang</th>
                <th>Kode Item</th>
                <th>Nama Barang</th>
                <th>Stok Min</th>
                <th>Satuan</th>
                <th>Isi</th>
                <th>Harga Pokok</th>
                <th>Harga Jual</th>
                <th>Margin (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($barang as $item)
                @php
                    $margin =
                        $item->harga_pokok > 0
                            ? (($item->harga_jual - $item->harga_pokok) / $item->harga_pokok) * 100
                            : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->nama_supplier }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->kode_item }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-end">{{ number_format($item->stok_min) }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td class="text-end">{{ number_format($item->isi) }}</td>
                    <td class="text-end">{{ rupiah($item->harga_pokok) }}</td>
                    <td class="text-end">{{ rupiah($item->harga_jual) }}</td>
                    <td class="text-end">{{ number_format($margin, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
