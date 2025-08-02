@extends('layouts.print')

@section('title', 'Laporan Target Salesman')

@section('periode')
    Periode: {{ bulan_indo($bulan) }} {{ $tahun }}
@endsection

@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 12px; border-collapse: collapse;">
        <thead style="background-color: #f1f1f1;">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Sales</th>
                <th colspan="3" class="text-center">OA</th>
                <th colspan="3" class="text-center">EC</th>
                <th colspan="3" class="text-center">Penjualan</th>
                <th colspan="3" class="text-center">Tagihan</th>
            </tr>
            <tr>
                @for ($i = 0; $i < 4; $i++)
                    <th class="text-center">Target</th>
                    <th class="text-center">Realisasi</th>
                    <th class="text-center">%</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                function warnaPersen($nilai)
                {
                    if ($nilai >= 100)
                        return '#d4edda';
                    elseif ($nilai >= 80)
                        return '#fff3cd';
                    else
                        return '#f8d7da';
                }
            @endphp

            @foreach ($data as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->nama_sales }}</td>

                    {{-- OA --}}
                    <td class="text-end" style="background-color:#f1f1f1;">{{ number_format($item->target_oa) }}</td>
                    <td class="text-end">{{ number_format($item->real_oa) }}</td>
                    <td class="text-end" style="background-color:{{ warnaPersen($item->persen_oa) }}">
                        {{ number_format($item->persen_oa, 2) }}%
                    </td>

                    {{-- EC --}}
                    <td class="text-end" style="background-color:#f1f1f1;">{{ number_format($item->target_ec) }}</td>
                    <td class="text-end">{{ number_format($item->real_ec) }}</td>
                    <td class="text-end" style="background-color:{{ warnaPersen($item->persen_ec) }}">
                        {{ number_format($item->persen_ec, 2) }}%
                    </td>

                    {{-- Penjualan --}}
                    <td class="text-end" style="background-color:#f1f1f1;">{{ rupiah($item->target_penjualan) }}</td>
                    <td class="text-end">{{ rupiah($item->real_penjualan) }}</td>
                    <td class="text-end" style="background-color:{{ warnaPersen($item->persen_penjualan) }}">
                        {{ number_format($item->persen_penjualan, 2) }}%
                    </td>

                    {{-- Tagihan --}}
                    <td class="text-end" style="background-color:#f1f1f1;">{{ rupiah($item->target_tagihan) }}</td>
                    <td class="text-end">{{ rupiah($item->real_tagihan) }}</td>
                    <td class="text-end" style="background-color:{{ warnaPersen($item->persen_tagihan) }}">
                        {{ number_format($item->persen_tagihan, 2) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
