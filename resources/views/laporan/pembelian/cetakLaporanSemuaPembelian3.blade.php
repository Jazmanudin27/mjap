@extends('layouts.print')

@section('title')
    Laporan Pembelian Detail Barang V2 <br>
    <span style="font-size:13px; font-weight:normal;">
        {{ $nama_supplier ? 'Supplier: ' . $nama_supplier : 'Semua Supplier' }}
    </span>
@endsection

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No. Faktur</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Supplier</th>
                <th colspan="7">Data Barang</th>
                <th rowspan="2">Retur</th>
                <th rowspan="2">Total Bersih</th>
                <th rowspan="2">Bayar</th>
                <th rowspan="2">Sisa</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Input</th>
            </tr>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Potongan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $total = $retur = $total_bersih = $bayar = $sisa = 0;
            @endphp
            @forelse ($data as $d)
                @php
                    $rowspan = count($d->detail);
                    $statusClass = $d->status == 'Lunas' ? 'bg-lunas'
                        : ($d->status == 'Belum Lunas' ? 'bg-belum' : 'bg-batal');

                    $retur_val = $d->jumlah_retur ?? 0;
                    $total_bersih_val = $d->total_bersih ?? ($d->grand_total - $retur_val);
                    $sisa_bayar = $total_bersih_val - $d->sudah_bayar;
                @endphp

                @foreach ($d->detail as $i => $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ tanggal_indo($d->tanggal) }}</td>
                        <td>{{ $d->no_faktur }}</td>
                        <td>{{ $d->kode_supplier }}</td>
                        <td>{{ $d->nama_supplier }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-end">{{ rupiah($item->harga) }}</td>
                        <td class="text-end">{{ rupiah($item->diskon) }}</td>
                        <td class="text-end">{{ rupiah($item->subtotal) }}</td>
                        @if($i === 0)
                            <td class="text-end fw-bold" rowspan="{{ $rowspan }}">{{ rupiah($retur_val) }}</td>
                            <td class="text-end fw-bold" rowspan="{{ $rowspan }}">{{ rupiah($total_bersih_val) }}</td>
                            <td class="text-end fw-bold" rowspan="{{ $rowspan }}">{{ rupiah($d->sudah_bayar) }}</td>
                            <td class="text-end fw-bold" rowspan="{{ $rowspan }}">{{ rupiah($sisa_bayar) }}</td>
                            <td class="text-center {{ $statusClass }}" rowspan="{{ $rowspan }}">{{ $d->status }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $d->penginput }}</td>
                        @endif
                    </tr>
                @endforeach

                @php
                    $total += $d->grand_total;
                    $retur += $retur_val;
                    $total_bersih += $total_bersih_val;
                    $bayar += $d->sudah_bayar;
                    $sisa += $sisa_bayar;
                @endphp
            @empty
                <tr>
                    <td colspan="18" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="12" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ rupiah($retur) }}</td>
                <td class="text-end fw-bold">{{ rupiah($total_bersih) }}</td>
                <td class="text-end fw-bold">{{ rupiah($bayar) }}</td>
                <td class="text-end fw-bold">{{ rupiah($sisa) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endsection
