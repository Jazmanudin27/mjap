@extends('layouts.print')

@section('title', 'Kartu Piutang Rekap per Pelanggan')

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
@endsection

@section('content')
    <table style="border-collapse: collapse; width: 100%; font-size:13px;" border="1" cellpadding="5">
        <thead style="background-color: #e9ecef;">
            <tr>
                <th>No</th>
                <th>Nama Pelanggan</th>
                <th>Kode</th>
                <th class="text-end">Total</th>
                <th class="text-end">Dibayar</th>
                <th class="text-end">Retur</th>
                <th class="text-end">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalAll = $bayarAll = $returAll = $sisaAll = 0;
            @endphp
            @foreach ($data as $d)
                @php
                    $totalAll += $d->total;
                    $bayarAll += $d->bayar;
                    $returAll += $d->retur;
                    $sisaAll += $d->sisa;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td class="text-center">{{ $d->kode_pelanggan }}</td>
                    <td class="text-end">{{ formatAngka($d->total) }}</td>
                    <td class="text-end">{{ formatAngka($d->bayar) }}</td>
                    <td class="text-end">{{ formatAngka($d->retur) }}</td>
                    <td class="text-end fw-bold">{{ formatAngka($d->sisa) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="3" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ formatAngka($totalAll) }}</td>
                <td class="text-end fw-bold">{{ formatAngka($bayarAll) }}</td>
                <td class="text-end fw-bold">{{ formatAngka($returAll) }}</td>
                <td class="text-end fw-bold">{{ formatAngka($sisaAll) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
