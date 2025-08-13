@extends('layouts.print')

@section('title', 'Laporan Retur Penjualan (Detail)')

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Retur</th>
                <th>No. Faktur</th>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>Sales</th>
                <th>Jenis</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th class="text-end">Qty</th>
                <th>Satuan</th>
                <th class="text-end">Harga Retur</th>
                <th class="text-end">Subtotal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $grandTotal = 0;
            @endphp
            @forelse ($data as $d)
                @php
                    $rowspan = $d->detail->count();
                    $grandTotal += $d->total;
                @endphp
                @foreach ($d->detail as $i => $item)
                    <tr>
                        @if ($i === 0)
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ tanggal_indo($d->tanggal) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->no_retur }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->no_faktur }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->kode_pelanggan }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->nama_pelanggan }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->sales }}</td>
                            <td rowspan="{{ $rowspan }}">{{ ucfirst($d->jenis_retur) }}</td>
                        @endif

                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-end">{{ formatAngka($item->harga_retur) }}</td>
                        <td class="text-end">{{ formatAngka($item->subtotal_retur) }}</td>

                        @if ($i === 0)
                            <td rowspan="{{ $rowspan }}">{{ $d->keterangan ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="15" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="13" class="text-end fw-bold">GRAND TOTAL</td>
                <td class="text-end fw-bold">{{ formatAngka($grandTotal) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endsection
