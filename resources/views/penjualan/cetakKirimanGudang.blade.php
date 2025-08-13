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
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td colspan="2"
                style="text-align: center; font-size: 24px; font-weight: bold; padding-bottom: 5px; border: none;">
                REKAP KIRIMAN BARANG
            </td>
        </tr>
        <tr>
            <td style="width: 60%; border: none; vertical-align: top;">
                <table style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <td style="width: 20%; font-size: 15px; border: none;">Tanggal Pengiriman</td>
                        <td style="width: 5%; text-align: center; border: none;">:</td>
                        <td style="border: none; font-size: 15px;">{{ tanggal_indo2($tanggal) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px; border: none;">Wilayah</td>
                        <td style="text-align: center; border: none;">:</td>
                        <td style="border: none; font-size: 15px;">{{ $wilayah->nama_wilayah ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            {{-- <td style="width: 40%; border: none; vertical-align: top;">
                <table style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <td style="width: 40%; font-size: 15px; border: none;">Dropping</td>
                        <td style="width: 5%; text-align: center; border: none;">:</td>
                        <td style="border: none; font-size: 15px;">__________________</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px; border: none;">Kenek</td>
                        <td style="text-align: center; border: none;">:</td>
                        <td style="border: none; font-size: 15px;">__________________</td>
                    </tr>
                </table>
            </td> --}}

        </tr>
    </table>

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
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($groupedDetails as $item)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $item['kode_barang'] }}</td>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td class="text-start">
                                @php
                                    $qtyStrings = [];
                                    foreach ($item['satuan'] as $satuan => $qty) {
                                        $qtyStrings[] = number_format($qty, 0, ',', '.') . ' ' . $satuan;
                                    }
                                    echo implode(' ', $qtyStrings);
                                @endphp
                            </td>
                        </tr>
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
