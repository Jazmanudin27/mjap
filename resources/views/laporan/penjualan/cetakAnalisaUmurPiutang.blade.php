@extends('layouts.print')

@section('title', 'Laporan AUP (Analisa Umur Piutang)')

@section('periode')
    Periode: {{ tanggal_indo($tanggal_sampai) }}
@endsection

@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 12px; border-collapse: collapse;">
        <thead style="background-color: #f1f1f1;">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Pelanggan</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">Pasar/Daerah</th>
                <th rowspan="2">Salesman</th>
                <th rowspan="2">Jatuh Tempo</th>
                <th rowspan="2">Saldo Piutang</th>
                <th colspan="9" class="text-center">KATEGORI AUP</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <th>1-15 Hari</th>
                <th>16 Hari - 1 Bulan</th>
                <th>> 1 - 45 Hari</th>
                <th>46 Hari - 2 Bulan</th>
                <th>2 - 3 Bulan</th>
                <th>3 - 6 Bulan</th>
                <th>6 Bulan - 1 Tahun</th>
                <th>1 - 2 Tahun</th>
                <th>> 2 Tahun</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $grandTotal = 0;
                $kategori = [
                    '1_15' => 0,
                    '16_30' => 0,
                    '31_45' => 0,
                    '46_60' => 0,
                    '61_90' => 0,
                    '91_180' => 0,
                    '181_360' => 0,
                    '361_720' => 0,
                    'lebih_720' => 0
                ];
            @endphp

            @foreach ($data as $item)
                @php
                    $saldo = $item->saldo;
                    $grandTotal += $saldo;

                    $usia = (int) $item->usia;
                    $kolom = '';

                    if ($usia <= 15)
                        $kolom = '1_15';
                    elseif ($usia <= 30)
                        $kolom = '16_30';
                    elseif ($usia <= 45)
                        $kolom = '31_45';
                    elseif ($usia <= 60)
                        $kolom = '46_60';
                    elseif ($usia <= 90)
                        $kolom = '61_90';
                    elseif ($usia <= 180)
                        $kolom = '91_180';
                    elseif ($usia <= 360)
                        $kolom = '181_360';
                    elseif ($usia <= 720)
                        $kolom = '361_720';
                    else
                        $kolom = 'lebih_720';

                    $kategori[$kolom] += $saldo;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $item->kode_pelanggan }}</td>
                    <td>{{ $item->nama_pelanggan }}</td>
                    <td>{{ $item->pasar_daerah }}</td>
                    <td>{{ $item->nama_sales }}</td>
                    <td class="text-center">{{ tanggal_indo($item->jatuh_tempo) }}</td>
                    <td class="text-end">{{ rupiah($saldo) }}</td>
                    <td class="text-end">{{ $kolom == '1_15' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '16_30' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '31_45' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '46_60' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '61_90' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '91_180' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '181_360' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == '361_720' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end">{{ $kolom == 'lebih_720' ? rupiah($saldo) : '-' }}</td>
                    <td class="text-end fw-bold">{{ rupiah($saldo) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="background-color: #f9f9f9;">
            <tr>
                <td colspan="6" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ rupiah($grandTotal) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['1_15']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['16_30']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['31_45']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['46_60']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['61_90']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['91_180']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['181_360']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['361_720']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($kategori['lebih_720']) }}</td>
                <td class="text-end fw-bold">{{ rupiah($grandTotal) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
