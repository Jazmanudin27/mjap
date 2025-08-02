@extends('layouts.print')

@section('title', 'Kartu Hutang Per Faktur')

@section('content')
    <div class="text-center mb-3">
        <small>Periode: <strong>{{ tanggal_indo2($mulai) }} s/d {{ tanggal_indo2($akhir) }}</strong></small><br>
        @if($nama_supplier)
            <small>Supplier: <strong>{{ $nama_supplier }}</strong></small>
        @endif
    </div>

    <table class="table table-bordered table-sm" style="font-size: 13px;">
        <thead class="table-light text-center align-middle">
            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Jatuh Tempo</th>
                <th>Supplier</th>
                <th>Grand Total</th>
                <th>Sudah Dibayar</th>
                <th>Sisa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $row)
                @php
                    $sudahJatuhTempo = \Carbon\Carbon::parse($row->jatuh_tempo)->lt(now()) && strtolower($row->status) !== 'lunas';
                @endphp
                <tr style="{{ $sudahJatuhTempo ? 'color:red; font-weight:bold;' : '' }}">
                    <td>{{ $no++ }}</td>
                    <td>{{ $row->no_faktur }}</td>
                    <td>{{ tanggal_indo2($row->tanggal) }}</td>
                    <td>
                        {{ tanggal_indo2($row->jatuh_tempo) }}
                    </td>
                    <td>{{ $row->nama_supplier }}</td>
                    <td class="text-end">Rp {{ number_format($row->grand_total, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row->sudah_bayar, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row->sisa, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if(strtolower($row->status) === 'lunas')
                            <span style="background-color:#198754; color:#fff; padding:2px 8px; border-radius:4px;">Lunas</span>
                        @else
                            <span style="background-color:#dc3545; color:#fff; padding:2px 8px; border-radius:4px;">Belum
                                Lunas</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
