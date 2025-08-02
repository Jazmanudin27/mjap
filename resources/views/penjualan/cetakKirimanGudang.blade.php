<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Kiriman Barang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 10pt;
            margin: 20px;
            color: #333;
        }

        h3 {
            text-align: center;
            margin-bottom: 0;
            font-weight: 600;
            font-size: 14pt;
            color: #222;
        }

        .sub-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 9pt;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        thead th {
            border-bottom: 1.5px solid #333;
            padding: 6px 4px;
            background: #f5f5f5;
            font-weight: 500;
            text-align: center;
        }

        tbody td {
            padding: 6px 4px;
            border-bottom: 0.5px dashed #ccc;
            vertical-align: top;
        }

        tfoot td {
            padding-top: 8px;
            font-weight: 600;
            border-top: 1px solid #333;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .no-border {
            border: none !important;
        }

        @media print {
            body {
                margin: 0;
            }

            thead th,
            tfoot td {
                border-color: #000;
            }

            thead th {
                background: #eee !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <h3>REKAP KIRIMAN BARANG (DETAIL)</h3>
    <div class="sub-header">
        Tanggal Pengiriman: {{ tanggal_indo2($tanggal) }}<br>
        Wilayah: {{ $wilayah->nama_wilayah ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="7%">Kode</th>
                <th width="20%">Supplier</th>
                <th width="7%">Kode</th>
                <th>Barang</th>
                <th width="7%" class="text-center">Qty</th>
                <th width="10%" class="text-center">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($kiriman as $d)
                @foreach ($detail[$d->no_faktur] as $dt)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $dt->kode_supplier }}</td>
                        <td>{{ $dt->nama_supplier }}</td>
                        <td>{{ $dt->kode_barang }}</td>
                        <td>{{ $dt->nama_barang }}</td>
                        <td class="text-center">{{ number_format($dt->qty, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $dt->satuan }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
