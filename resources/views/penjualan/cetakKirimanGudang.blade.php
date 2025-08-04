<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Kiriman Barang</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 14px;
            margin: 10px;
            line-height: 1.2;
            width: 210mm;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 4px;
            white-space: nowrap;
            overflow: hidden;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .header-title {
            font-weight: bold;
            font-size: 24px;
            text-align: center;
        }

        .header-subtitle {
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
        }

        .container {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .col-8 {
            width: 65%;
        }

        .col-4 {
            width: 35%;
        }

        .highlight {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .col-no {
            width: 20px;
        }

        .col-kode {
            width: 80px;
        }

        .col-qty {
            width: 50px;
        }

        .col-satuan {
            width: 50px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="header-title">REKAP KIRIMAN BARANG </div>
    <div class="header-subtitle">
        Tanggal Pengiriman: {{ tanggal_indo2($tanggal) }}<br>
        Wilayah: {{ $wilayah->nama_wilayah ?? '-' }}
    </div>

    <div class="container">
        <!-- Detail Barang (col-8) -->
        <div class="col-8">
            <table>
                <thead>
                    <tr class="text-center">
                        <th class="col-no">No</th>
                        <th class="col-kode">Kode</th>
                        <th>Nama Barang</th>
                        <th class="col-qty">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($kiriman as $d)
                        @foreach ($detail[$d->no_faktur] as $dt)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $dt->kode_barang }}</td>
                                <td class="text-start">{{ $dt->nama_barang }}</td>
                                <td class="text-center">
                                    {{ konversiQtySatuan($dt->qty, $barangSatuan[$dt->kode_barang]) }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Rekap Faktur (col-4) -->
        <div class="col-4">
            <table>
                <thead>
                    <tr class="text-center">
                        <th>No Faktur</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($kiriman as $k)
                        @php $total += $k->grand_total; @endphp
                        <tr>
                            <td>{{ $k->no_faktur }}</td>
                            <td>{{ $k->nama_pelanggan }}</td>
                            <td class="text-end">{{ number_format($k->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="highlight">
                        <td colspan="2" class="text-end">TOTAL</td>
                        <td class="text-end">{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>
