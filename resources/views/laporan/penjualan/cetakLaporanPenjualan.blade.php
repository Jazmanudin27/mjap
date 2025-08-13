<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: 'tahoma';
            font-size: 13px;
            background: #fff;
        }

        h2,
        .periode {
            text-align: center;
            margin: 0;
        }

        .periode {
            margin: 5px 0 20px;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px 8px;
            white-space: nowrap;
        }

        th {
            font-size: 14px;
            color: white;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: green;
        }

        .text-danger {
            color: red;
        }

        .fw-bold {
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background: #f8f9fa;
        }
    </style>
</head>

<body>

    <div style="text-align:center; margin-bottom:20px;">
        <div style="font-size:22px; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">
            Laporan Penjualan
        </div>
        <div style="font-size:14px; color:#555;">
            Periode: {{ tanggal_indo($mulai) }} s/d {{ tanggal_indo($akhir) }}
        </div>
    </div>

    <table>
        <thead>
            <tr style="background:#0d6efd;">
                <th rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No. Faktur</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">Alamat</th>
                <th rowspan="2">Sales</th>
                <th rowspan="2">Wilayah</th>
                <th colspan="10">Data Barang</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Diskon</th>
                <th rowspan="2">Grand Total</th>
                <th rowspan="2">Bayar</th>
                <th rowspan="2">Sisa</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Wilayah</th>
                <th rowspan="2">JT</th>
                <th rowspan="2">Diinput</th>
                <th rowspan="2">Update</th>
                <th rowspan="2">Input Oleh</th>
            </tr>
            <tr style="background:#00771a; color:#000;">
                <th>Kode</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Merk</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>D1</th>
                <th>D2</th>
                <th>D3</th>
                {{-- <th>D4</th> --}}
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalAll = $diskonAll = $grandAll = $bayarAll = $sisaAll = 0;
                $totalBatal = $diskonBatal = $grandBatal = $bayarBatal = $sisaBatal = 0;
            @endphp
            @forelse($data as $d)
                @php
                    $rowspan = count($d->detail);
                    if (!$d->batal) {
                        $totalAll += $d->total;
                        $diskonAll += $d->diskon;
                        $grandAll += $d->grand_total;
                        $bayarAll += $d->sudah_bayar;
                        $sisaAll += $d->sisa;
                    } else {
                        $totalBatal += $d->total;
                        $diskonBatal += $d->diskon;
                        $grandBatal += $d->grand_total;
                        $bayarBatal += $d->sudah_bayar;
                        $sisaBatal += $d->sisa;
                    }
                @endphp
                @foreach ($d->detail as $i => $item)
                    @php $batalStyle = $d->batal ? 'background-color: rgba(255,0,0,0.1); color:#a00; text-decoration: line-through;' : ''; @endphp
                    <tr style="{{ $batalStyle }}">
                        @if ($i === 0)
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">
                                {{ tanggal_indo($d->tanggal) }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $d->no_faktur }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $d->kode_pelanggan }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->nama_pelanggan }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->alamat_toko }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->sales }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->nama_wilayah }}</td>
                        @endif

                        @php $promoStyle = $item->is_promo == '1' ? 'background-color: #FF7F00;' : ''; @endphp

                        <td style="{{ $promoStyle }}">{{ $item->kode_barang }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->nama_barang }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->kategori }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->merk }}</td>

                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->qty > 0 ? number_format($item->qty, 2, ',', '') : '' }}
                        </td>
                        <td style="{{ $promoStyle }}">{{ $item->satuan }}</td>
                        <td class="text-end num-format" style="{{ $promoStyle }} mso-number-format:'#,##0';">
                            {{ formatAngka($item->harga) }}
                        </td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon1_persen > 0 ? number_format($item->diskon1_persen, 2, ',', '') : '' }}
                        </td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon2_persen > 0 ? number_format($item->diskon2_persen, 2, ',', '') : '' }}
                        </td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon3_persen > 0 ? number_format($item->diskon3_persen, 2, ',', '') : '' }}
                        </td>
                        {{-- <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon4_persen > 0 ? number_format($item->diskon4_persen, 2, ',', '') : '' }}
                        </td> --}}
                        </td>
                        <td class="text-end num-format" style="{{ $promoStyle }} mso-number-format:'#,##0';">
                            {{ formatAngka($item->total) }}
                        </td>

                        @if ($i === 0)
                            <td class="text-end num-format" rowspan="{{ $rowspan }}"
                                style="mso-number-format:'#,##0';">
                                {{ formatAngka($d->total) }}
                            </td>
                            <td class="text-end num-format" rowspan="{{ $rowspan }}"
                                style="mso-number-format:'#,##0';">
                                {{ formatAngka($d->diskon) }}
                            </td>
                            <td class="text-end fw-bold num-format" rowspan="{{ $rowspan }}"
                                style="mso-number-format:'#,##0';">
                                {{ formatAngka($d->grand_total) }}
                            </td>
                            <td class="text-end text-success num-format" rowspan="{{ $rowspan }}"
                                style="mso-number-format:'#,##0';">
                                {{ formatAngka($d->sudah_bayar) }}
                            </td>
                            <td class="text-end text-danger num-format" rowspan="{{ $rowspan }}"
                                style="mso-number-format:'#,##0';">
                                {{ formatAngka($d->sisa) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="24" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="15" class="text-end">TOTAL</td>
                <td class="text-end num-format" style="mso-number-format:'#,##0';">
                    {{ formatAngka($totalAll) }}</td>
                <td class="text-end num-format" style="mso-number-format:'#,##0';">
                    {{ formatAngka($diskonAll) }}</td>
                <td class="text-end num-format" style="mso-number-format:'#,##0';">
                    {{ formatAngka($grandAll) }}</td>
                <td class="text-end num-format" style="mso-number-format:'#,##0';">
                    {{ formatAngka($bayarAll) }}</td>
                <td class="text-end num-format" style="mso-number-format:'#,##0';">
                    {{ formatAngka($sisaAll) }}</td>
                <td colspan="4"></td>
            </tr>
            @if ($totalBatal > 0)
                <tr style="background:#ffe3e3;">
                    <td colspan="16" class="text-end text-danger fw-bold">TOTAL FAKTUR BATAL</td>
                    <td class="text-end text-danger fw-bold num-format" style="mso-number-format:'#,##0';">
                        {{ formatAngka($totalBatal) }}</td>
                    <td class="text-end text-danger fw-bold num-format" style="mso-number-format:'#,##0';">
                        {{ formatAngka($diskonBatal) }}</td>
                    <td class="text-end text-danger fw-bold num-format" style="mso-number-format:'#,##0';">
                        {{ formatAngka($grandBatal) }}</td>
                    <td class="text-end text-danger fw-bold num-format" style="mso-number-format:'#,##0';">
                        {{ formatAngka($bayarBatal) }}</td>
                    <td class="text-end text-danger fw-bold num-format" style="mso-number-format:'#,##0';">
                        {{ formatAngka($sisaBatal) }}</td>
                    <td colspan="4"></td>
                </tr>
            @endif
        </tfoot>
    </table>

</body>

</html>
