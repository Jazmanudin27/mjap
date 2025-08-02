@extends('layouts.print')

@section('title', 'Laporan Persediaan Barang')

@section('periode')
    PERIODE {{ tanggal_indo2($tanggal_mulai) }} s/d {{ tanggal_indo2($tanggal_akhir) }}<br>
    <strong>{{ $nama_barang }}</strong>
@endsection

@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 11px; border-collapse: collapse;">
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #003366; color: white;">Tanggal</th>
                <th colspan="2" style="background-color: #003366; color: white;">Bukti</th>
                <th rowspan="2" style="background-color: #003366; color: white;">Keterangan</th>
                <th colspan="4" style="background-color: #198754; color: white;">Penerimaan</th>
                <th colspan="4" style="background-color: #dc3545; color: white;">Pengeluaran</th>
                <th rowspan="2" style="background-color: #003366; color: white;">Saldo Akhir</th>
            </tr>
            <tr>
                <th style="background-color: #003366; color: white;">Mutasi Masuk</th>
                <th style="background-color: #003366; color: white;">Mutasi Keluar</th>

                <th style="background-color: #198754; color: white;">Pembelian</th>
                <th style="background-color: #198754; color: white;">Repack</th>
                <th style="background-color: #198754; color: white;">Penyesuaian</th>
                <th style="background-color: #198754; color: white;">Lain Lain</th>

                <th style="background-color: #dc3545; color: white;">Penjualan</th>
                <th style="background-color: #dc3545; color: white;">Retur Pembelian</th>
                <th style="background-color: #dc3545; color: white;">Penyesuaian</th>
                <th style="background-color: #dc3545; color: white;">Lain Lain</th>
            </tr>
        </thead>
        <tbody>
            {{-- Saldo Awal --}}
            <tr>
                <td class="text-center">{{ tanggal_indo2($tanggal_mulai) }}</td>
                <td colspan="11" class="text-center"><strong>SALDO AWAL</strong></td>
                <td class="text-end"><strong>{{ number_format($saldo_awal) }}</strong></td>
            </tr>

            {{-- Loop Data --}}
            @foreach ($data as $row)
                <tr>
                    <td class="text-center">{{ tanggal_indo2($row['tanggal']) }}</td>
                    <td class="text-center">{{ $row['mutasi_masuk'] ?? '' }}</td>
                    <td class="text-center">{{ $row['mutasi_keluar'] ?? '' }}</td>
                    <td>{{ $row['keterangan'] }}</td>

                    {{-- PENERIMAAN --}}
                    <td class="text-end">
                        @if (!empty($row['masuk_pembelian']) && $row['masuk_pembelian'] != 0)
                            {{ number_format($row['masuk_pembelian']) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if (!empty($row['masuk_repack']) && $row['masuk_repack'] != 0)
                            {{ number_format($row['masuk_repack']) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if (!empty($row['masuk_penyesuaian']) && $row['masuk_penyesuaian'] != 0)
                            {{ number_format($row['masuk_penyesuaian']) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if (!empty($row['masuk_lainnya']) && $row['masuk_lainnya'] != 0)
                            {{ number_format($row['masuk_lainnya']) }}
                        @endif
                    </td>

                    {{-- PENGELUARAN --}}
                    <td class="text-end">
                        @if (!empty($row['keluar_penjualan']) && abs($row['keluar_penjualan']) != 0)
                            {{ number_format(abs($row['keluar_penjualan'])) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if (!empty($row['keluar_retur_beli']) && abs($row['keluar_retur_beli']) != 0)
                            {{ number_format(abs($row['keluar_retur_beli'])) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if (!empty($row['keluar_penyesuaian']) && abs($row['keluar_penyesuaian']) != 0)
                            {{ number_format(abs($row['keluar_penyesuaian'])) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if (!empty($row['keluar_lainnya']) && abs($row['keluar_lainnya']) != 0)
                            {{ number_format(abs($row['keluar_lainnya'])) }}
                        @endif
                    </td>

                    <td class="text-end"><strong>{{ number_format($row['saldo_akhir']) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
