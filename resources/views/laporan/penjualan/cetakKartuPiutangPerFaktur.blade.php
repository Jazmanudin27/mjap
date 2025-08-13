@extends('layouts.print')

@section('title', 'Laporan Kartu Piutang')

@section('periode')
    Periode: {{ tanggal_indo($tanggal_dari) }} s/d {{ tanggal_indo($tanggal_sampai) }}
@endsection

@section('content')
    <table style="border-collapse: collapse; width: 100%; font-size:12px;" border="1" cellpadding="5">
        <thead style="background-color: #e9ecef;">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Last Payment</th>
                <th>Usia</th>
                <th>Kategori AUP</th>
                <th>No Faktur</th>
                <th>Kode Pel.</th>
                <th>Nama Pelanggan</th>
                <th>Nama Sales</th>
                <th>Pasar/Daerah</th>
                <th>Jatuh Tempo</th>
                <th class="text-end">Total Piutang</th>
                <th class="text-end">Retur</th>
                <th class="text-end">Pembayaran</th>
                <th class="text-end">Sisa Piutang</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalPiutang = $totalSaldoAwal = $totalRetur = $totalBayar = $totalSaldoAkhir = 0;
            @endphp

            @foreach ($data as $d)
                @php
                    $grand_total = $d->grand_total ?? 0;
                    $retur = $d->retur_penjualan ?? 0;
                    $bayar = $d->total_pembayaran ?? 0;

                    $saldo_awal = $grand_total;
                    $saldo_akhir = $grand_total - $bayar - $retur;

                    $totalPiutang += $grand_total;
                    $totalSaldoAwal += $saldo_awal;
                    $totalRetur += $retur;
                    $totalBayar += $bayar;
                    $totalSaldoAkhir += $saldo_akhir;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ tanggal_indo($d->tanggal) }}</td>
                    <td class="text-center">{{ !empty($d->last_payment) ? tanggal_indo($d->last_payment) : '-' }}</td>
                    <td class="text-center">{{ $d->usia_piutang }} hari</td>
                    <td class="text-center">{{ $d->kategori_aup ?? '-' }}</td>
                    <td>{{ $d->no_faktur }}</td>
                    <td class="text-center">{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->nama_sales }}</td>
                    <td>{{ $d->pasar_daerah }}</td>
                    <td class="text-center">{{ tanggal_indo($d->jatuh_tempo) }}</td>
                    <td class="text-end">{{ formatAngka($grand_total) }}</td>
                    <td class="text-end">{{ formatAngka($retur) }}</td>
                    <td class="text-end">{{ formatAngka($bayar) }}</td>
                    <td class="text-end fw-bold">{{ formatAngka($saldo_akhir) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="background:#f1f1f1;">
            <tr>
                <td colspan="11" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ formatAngka($totalPiutang) }}</td>
                <td class="text-end fw-bold">{{ formatAngka($totalRetur) }}</td>
                <td class="text-end fw-bold">{{ formatAngka($totalBayar) }}</td>
                <td class="text-end fw-bold">{{ formatAngka($totalSaldoAkhir) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
