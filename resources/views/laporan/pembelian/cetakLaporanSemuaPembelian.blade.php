@extends('layouts.print')

@section('title', 'Laporan Pembelian')

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Faktur</th>
                <th>Kode</th>
                <th>Supplier</th>
                <th class="text-end">Potongan</th>
                <th class="text-end">Biaya Lain</th>
                <th class="text-end">Grand Total</th>
                <th class="text-end">Retur</th>
                <th class="text-end">Total Bersih</th>
                <th class="text-end">Bayar</th>
                <th class="text-end">Sisa</th>
                <th>Status</th>
                <th>Input</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $total = $bayar = $sisa = $retur = $total_bersih = 0;
            @endphp
            @forelse ($data as $d)
                @php
                    $statusClass = $d->status == 'Lunas' ? 'bg-lunas'
                        : ($d->status == 'Belum Lunas' ? 'bg-belum'
                            : 'bg-batal');
                    $retur_val = $d->jumlah_retur ?? 0;
                    $total_bersih_val = $d->total_bersih ?? ($d->grand_total - $retur_val);
                    $sisa_bayar = $total_bersih_val - $d->sudah_bayar;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ tanggal_indo($d->tanggal) }}</td>
                    <td>{{ $d->no_faktur }}</td>
                    <td>{{ $d->kode_supplier }}</td>
                    <td>{{ $d->nama_supplier }}</td>
                    <td class="text-end">{{ rupiah($d->potongan) }}</td>
                    <td class="text-end">{{ rupiah($d->biaya_lain) }}</td>
                    <td class="text-end">{{ rupiah($d->grand_total) }}</td>
                    <td class="text-end">{{ rupiah($retur_val) }}</td>
                    <td class="text-end">{{ rupiah($total_bersih_val) }}</td>
                    <td class="text-end">{{ rupiah($d->sudah_bayar) }}</td>
                    <td class="text-end">{{ rupiah($sisa_bayar) }}</td>
                    <td class="text-center {{ $statusClass }}">{{ $d->status }}</td>
                    <td class="text-center">{{ $d->penginput }}</td>
                </tr>
                @php
                    $total += $d->grand_total;
                    $retur += $retur_val;
                    $total_bersih += $total_bersih_val;
                    $bayar += $d->sudah_bayar;
                    $sisa += $sisa_bayar;
                @endphp
            @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ rupiah($total) }}</td>
                <td class="text-end fw-bold">{{ rupiah($retur) }}</td>
                <td class="text-end fw-bold">{{ rupiah($total_bersih) }}</td>
                <td class="text-end fw-bold">{{ rupiah($bayar) }}</td>
                <td class="text-end fw-bold">{{ rupiah($sisa) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endsection
