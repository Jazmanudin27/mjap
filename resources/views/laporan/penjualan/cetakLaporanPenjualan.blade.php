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
                <th rowspan="2">Sales</th>
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
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>D1</th>
                <th>D2</th>
                <th>D3</th>
                <th>D4</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalAll = 0;
                $diskonAll = 0;
                $grandAll = 0;
                $bayarAll = 0;
                $sisaAll = 0;

                $totalBatal = 0;
                $diskonBatal = 0;
                $grandBatal = 0;
                $bayarBatal = 0;
                $sisaBatal = 0;
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
                @foreach($d->detail as $i => $item)
                    @php
                        $batalStyle = $d->batal ? 'background-color: rgba(255,0,0,0.1); color:#a00; text-decoration: line-through;' : '';
                    @endphp
                    <tr style="{{ $batalStyle }}">
                        @if ($i === 0)
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">
                                {{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}
                            </td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $d->no_faktur }}</td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $d->kode_pelanggan }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->nama_pelanggan }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $d->sales }}</td>
                        @endif
                        @php
                            $promoStyle = $item->is_promo == '1' ? 'background-color: #FF7F00;' : '';
                        @endphp

                        <td style="{{ $promoStyle }}">{{ $item->kode_barang }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->nama_barang }}</td>
                        <td class="text-end" style="{{ $promoStyle }}">{{ $item->qty }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->satuan }}</td>
                        <td class="text-end" style="{{ $promoStyle }}">{{ rupiah($item->harga) }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">{{ $item->diskon1_persen }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">{{ $item->diskon2_persen }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">{{ $item->diskon3_persen }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">{{ $item->diskon4_persen }}</td>
                        <td class="text-end" style="{{ $promoStyle }}">{{ rupiah($item->total) }}</td>
                        @if ($i === 0)
                            <td class="text-end" rowspan="{{ $rowspan }}">{{ rupiah($d->total) }}</td>
                            <td class="text-end" rowspan="{{ $rowspan }}">{{ rupiah($d->diskon) }}</td>
                            <td class="text-end fw-bold" rowspan="{{ $rowspan }}">{{ rupiah($d->grand_total) }}</td>
                            <td class="text-end text-success" rowspan="{{ $rowspan }}">{{ rupiah($d->sudah_bayar) }}</td>
                            <td class="text-end text-danger" rowspan="{{ $rowspan }}">{{ rupiah($d->sisa) }}</td>
                            <td class="text-center fw-bold" rowspan="{{ $rowspan }}"
                                style="{{ $d->status == 'Batal' ? 'color:red;' : ($d->status == 'Lunas' ? 'color:green;' : '') }}">
                                {{ $d->status }}
                            </td>
                            <td class="text-center fw-bold" rowspan="{{ $rowspan }}">{{ $d->nama_wilayah }}</td>
                            <td class="text-center fw-bold" rowspan="{{ $rowspan }}"
                                style="{{ $d->jenis_transaksi == 'K' ? 'color:orange;' : ($d->jenis_transaksi == 'T' ? 'color:green;' : '') }}">
                                {{ $d->jenis_transaksi }}
                            </td>
                            <td class="text-center" rowspan="{{ $rowspan }}">
                                {{ \Carbon\Carbon::parse($d->created_at)->format('d M Y H:i') }}
                            </td>
                            <td class="text-center" rowspan="{{ $rowspan }}">
                                {{ \Carbon\Carbon::parse($d->updated_at)->format('d M Y H:i') }}
                            </td>
                            <td class="text-center" rowspan="{{ $rowspan }}">{{ $d->penginput }}</td>
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
                <td colspan="16" class="text-end">TOTAL</td>
                <td class="text-end">{{ rupiah($totalAll) }}</td>
                <td class="text-end">{{ rupiah($diskonAll) }}</td>
                <td class="text-end">{{ rupiah($grandAll) }}</td>
                <td class="text-end">{{ rupiah($bayarAll) }}</td>
                <td class="text-end">{{ rupiah($sisaAll) }}</td>
                <td colspan="4"></td>
            </tr>
            @if($totalBatal > 0)
                <tr style="background:#ffe3e3;">
                    <td colspan="16" class="text-end text-danger fw-bold">TOTAL FAKTUR BATAL</td>
                    <td class="text-end text-danger fw-bold">{{ rupiah($totalBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ rupiah($diskonBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ rupiah($grandBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ rupiah($bayarBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ rupiah($sisaBatal) }}</td>
                    <td colspan="4"></td>
                </tr>
            @endif
        </tfoot>
    </table>

</body>

</html>
