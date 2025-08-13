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

        .kotak-rekap td,
        .kotak-rekap {
            border: none;
        }
    </style>
</head>

<body class="A4">
    <section class="sheet">
        <header style="text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 22px;">CV MITRA JAYA ABADI PERSADA</h1>
            <h2 style="margin: 5px 0; font-size: 18px; text-transform: uppercase;">
                Rekap Sisa Piutang Penjualan Customer
            </h2>
            <hr style="border: 1px solid #000; margin-top: 10px;">
        </header>
        <table class="kotak-rekap" style="margin-top: 10px;">
            <tr>
                <td style="width: 10%;">SALES</td>
                <td style="width: 2%; text-align:center;">:</td>
                <td style="width: 25%;"></td>
                <td style="width: 10%;">FAKTUR KELUAR</td>
                <td style="width: 2%; text-align:center;">:</td>
                <td style="width: 20%;"></td>
                <td style="width: 15%;">TOTAL HITUNG ADM (Rp)</td>
                <td style="width: 2%; text-align:center;">:</td>
                <td style="width: 15%;"></td>
            </tr>
            <tr>
                <td>WILAYAH</td>
                <td style="text-align:center;">:</td>
                <td></td>
                <td>FAKTUR KEMBALI</td>
                <td style="text-align:center;">:</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>FAKTUR OVERDUE</td>
                <td style="text-align:center;">:</td>
                <td></td>
            </tr>
        </table>

        <br>

        <table>
            <thead>
                <tr>
                    <th style="width: 3%">No</th>
                    <th style="width: 8%">TGL FAKTUR</th>
                    <th style="width: 8%">KODE TRANSAKSI</th>
                    <th>NAMA PELANGGAN</th>
                    <th style="width: 7%">JUMLAH</th>
                    <th style="width: 14%">TITIP</th>
                    <th style="width: 12%">RETUR/POT.</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($data as $d)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ tanggal_indo2($d->tanggal) }}</td>
                        <td class="text-center">{{ $d->no_faktur }}</td>
                        <td>{{ $d->nama_pelanggan }}</td>
                        <td style="text-align: right">{{ number_format($d->sisa_tagihan) }}</td>
                        <td style="text-align: right"></td>
                        <td style="text-align: right"></td>
                    </tr>
                @endforeach
                <tr class="highlight">
                    <td colspan="4" class="text-center fw-bold">TOTAL</td>
                    <td class="text-right fw-bold">
                        {{ number_format($data->sum('sisa_tagihan')) }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </section>
</body>
