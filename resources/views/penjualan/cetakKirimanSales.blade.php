<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Kiriman Sales</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 13px;
            margin: 0;
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
            padding: 2px 3px;
            white-space: nowrap;
            overflow: hidden;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .header-table td {
            vertical-align: top;
            padding: 2px 4px;
            border: none;
        }

        .highlight {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .col-no {
            width: 20px;
        }

        .col-faktur {
            width: 100px;
        }

        .col-tanggal {
            width: 80px;
        }


        .col-total {
            width: 90px;
        }

        .header-title {
            font-weight: bold;
            font-size: 24px;
            text-align: center;
        }

        .header-subtitle {
            font-size: 18px;
            text-align: center;
            margin-bottom: 10px;
        }


        .header-right {
            position: absolute;
            width: 210mm;
            top: 20px;
            right: 20px;
            text-align: left;
            font-size: 12px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td colspan="2"
                style="text-align: center; font-size: 24px; font-weight: bold; padding-bottom: 5px; border: none;">
                REKAP KIRIMAN
            </td>
        </tr>
        <tr>
            <td style="width: 60%; border: none; vertical-align: top;">
                <table style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <td style="width: 50%; font-size: 15px; border: none;">Tanggal Pengiriman</td>
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
            <td style="width: 40%; border: none; vertical-align: top;">
                <table style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <td style="width: 40%; font-size: 15px; border: none;">Driver</td>
                        <td style="width: 5%; text-align: center; border: none;">:</td>
                        <td style="border: none; font-size: 15px;">__________________</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px; border: none;">Dropping</td>
                        <td style="text-align: center; border: none;">:</td>
                        <td style="border: none; font-size: 15px;">__________________</td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>
    <table>
        <thead>
            <tr class="text-center">
                <th class="col-no">No</th>
                <th class="col-pelanggan">Pelanggan</th>
                <th class="col-faktur">No Faktur</th>
                <th class="col-tanggal">Tanggal</th>
                <th class="col-sales">Sales</th>
                <th class="col-total">Total</th>
                <th class="col-keterangan">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($data as $i => $d)
                @php $total += $d->grand_total; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->no_faktur }}</td>
                    <td>{{ tanggal_indo2($d->tanggal) }}</td>
                    <td>{{ $d->nama_sales }}</td>
                    <td class="text-end">{{ number_format($d->grand_total, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr class="highlight">
                <td colspan="5" class="text-end">TOTAL</td>
                <td class="text-end">Rp{{ number_format($total, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
