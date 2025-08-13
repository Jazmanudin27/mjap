@extends('layouts.print')

@section('title', 'Laporan Mutasi Barang')

@section('periode')
    PERIODE {{ tanggal_indo2($tanggal_mulai) }} s/d {{ tanggal_indo2($tanggal_akhir) }}<br>
    <strong>{{ $nama_barang ?? '' }}</strong><br>
    <strong>{{ $nama_supplier ?? '' }}</strong>
@endsection

@section('content')
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 1rem;
        }

        .table th,
        .table td {
            border: 1px solid #999;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .table thead {
            background-color: #003366;
            color: white;
        }

        .table tbody tr.bg-gs {
            background-color: #d4edda;
            /* Hijau muda */
        }

        .table tbody tr.bg-bs {
            background-color: #f8d7da;
            /* Merah muda */
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Approve</th>
                <th>Kode Transaksi</th>
                <th>Jenis Barang</th>
                <th>Jenis Mutasi</th>
                <th>Jenis Transaksi</th>
                <th>Supplier</th>
                <th>Nama Barang</th>
                <th class="text-end">Qty</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                <tr class="{{ $row->kondisi == 'gs' ? 'bg-gs' : '' }}{{ $row->kondisi == 'bs' ? 'bg-bs' : '' }}">
                    <td>{{ tanggal_indo2($row->tanggal) }}</td>
                    <td>
                        {{ $row->tanggal_dikirim ? tanggal_indo2($row->tanggal_dikirim) : '' }}
                    </td>
                    <td>{{ $row->kode_transaksi }}</td>
                    <td>{{ strtoupper($row->kondisi) }}</td> {{-- GS / BS --}}
                    <td>{{ $row->jenis }}</td>
                    <td>{{ $row->jenis_transaksi }}</td>
                    <td>{{ $row->nama_supplier ?? '-' }}</td>
                    <td>{{ $row->nama_barang }}</td>
                    <td class="text-end">{{ number_format($row->qty_konversi) }}</td>
                    <td>{{ $row->satuan }}</td>
                    <td>{{ $row->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data ditemukan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
