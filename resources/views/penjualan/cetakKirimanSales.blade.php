<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Kiriman Sales</title>
    <style>
        body {
            font-family: "Courier New", monospace;
            font-size: 10pt;
            margin: 20px;
            color: #000;
        }

        h3 {
            text-align: center;
            margin-bottom: 0;
        }

        .sub-header {
            text-align: center;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding-bottom: 4px;
        }

        tbody td {
            padding: 4px 2px;
            border-bottom: 0.5px dotted #999;
            vertical-align: top;
        }

        tfoot td {
            padding-top: 6px;
            font-weight: bold;
            border-top: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            body {
                margin: 0;
            }

            thead th,
            tfoot td {
                border-color: #000;
            }
        }
    </style>
</head>

<body>
    <h3>REKAP KIRIMAN SALES</h3>
    <div class="sub-header">
        Tanggal Pengiriman: {{ tanggal_indo2($tanggal) }}<br>
        Wilayah: {{ $wilayah->nama_wilayah ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">No Faktur</th>
                <th width="10%">Tanggal</th>
                <th width="25%">Pelanggan</th>
                <th width="20%">Sales</th>
                <th width="15%">Wilayah</th>
                <th width="12%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($data as $i => $d)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $d->no_faktur }}</td>
                    <td>{{ tanggal_indo2($d->tanggal) }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->nama_sales }}</td>
                    <td>{{ $d->nama_wilayah }}</td>
                    <td class="text-right">
                        @php $total += $d->grand_total; @endphp
                        {{ number_format($d->grand_total, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right">Rp{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
