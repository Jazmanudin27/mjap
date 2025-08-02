@extends('layouts.print')

@section('title', 'Kartu Piutang Per Pelanggan')

@section('periode')
    Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}<br>
    Pelanggan: <strong>{{ $pelanggan->nama_pelanggan ?? '-' }}</strong>
@endsection

@section('content')
    <table style="border-collapse: collapse; width: 100%; font-size:13px;" border="1" cellpadding="5">
        <thead style="background-color: #e9ecef;">
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th>Jenis</th>
                <th>No. Ref</th>
                <th>Keterangan</th>
                <th class="text-end">Debet</th>
                <th class="text-end">Kredit</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $saldo = 0;
            @endphp
            @forelse ($transaksi as $t)
                @php
                    $saldo += $t->debet - $t->kredit;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ tanggal_indo($t->tanggal) }}</td>
                    <td>{{ $t->jenis }}</td>
                    <td>{{ $t->nomor }}</td>
                    <td>{{ $t->keterangan }}</td>
                    <td class="text-end">{{ $t->debet > 0 ? rupiah($t->debet) : '' }}</td>
                    <td class="text-end">{{ $t->kredit > 0 ? rupiah($t->kredit) : '' }}</td>
                    <td class="text-end fw-bold">{{ rupiah($saldo) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="7" class="text-end fw-bold">Saldo Akhir</td>
                <td class="text-end fw-bold">{{ rupiah($saldo) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
