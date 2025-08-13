<!-- PASTIKAN INI FILE BLADE (.blade.php) -->

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian Penjualan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 12px;
        }

        .sheet {
            padding: 6mm 6mm;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
        }

        .info-row {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 5px;
            vertical-align: middle;
        }

        th {
            background-color: #eaeaea;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .batal {
            background-color: #ffe5e5;
            color: #a00;
            text-decoration: line-through;
        }

        .kotak-rekap {
            border: 1px solid #000;
            width: 100%;
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .kotak-rekap td {
            border: none;
            padding: 6px 8px;
            font-size: 13px;
        }

        .ttd {
            width: 100%;
            margin-top: 25px;
            text-align: center;
            font-size: 13px;
        }

        .ttd td {
            padding: 10 10px 0 10px;
            vertical-align: bottom;
        }

        .ttd .jabatan {
            display: block;
            font-size: 12px;
            color: #444;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>

<body class="A4">
    <section class="sheet">

        <div class="title">CV. MITRA JAYA ABADI PERSADA</div>
        <div class="subtitle">LAPORAN PENJUALAN HARIAN</div>

        <div class="info-row">
            <div>Rute: ____________________________</div>
            <div>{{ tanggal_indo($tanggal) }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    {{-- <th rowspan="2">KODE</th> --}}
                    <th rowspan="2">NO. FAKTUR</th>
                    <th rowspan="2">PELANGGAN</th>
                    <th colspan="2">PENJUALAN</th>
                    <th colspan="4">PEMBAYARAN</th>
                </tr>
                <tr>
                    <th>TUNAI</th>
                    <th>KREDIT</th>
                    <th>TITIPAN</th>
                    <th>TRANSFER</th>
                    <th>GIRO</th>
                    <th>VOUCHER</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTunai = 0;
                    $totalKredit = 0;
                    $batalTunai = 0;
                    $batalKredit = 0;
                    $totalTitipan = 0;
                    $totalTransfer = 0;
                    $totalGiro = 0;
                    $totalVoucher = 0;
                @endphp
                @foreach ($data as $d)
                    @php
                        $isBatal = $d->batal == 1;
                        $rowClass = $isBatal ? 'batal' : '';
                        $tunai = $d->tunai ?? 0;
                        $titipan = $d->titipan ?? 0;
                        $transfer = $d->transfer ?? 0;
                        $giro = $d->giro ?? 0;
                        $voucher = $d->voucher ?? 0;
                        $kredit = $d->kredit;
                    @endphp
                    <tr class="{{ $rowClass }}">
                        {{-- <td>{{ $d->kode_pelanggan }}</td> --}}
                        <td>{{ $d->no_faktur }}</td>
                        <td>{{ $d->nama_pelanggan }}</td>
                        <td class="text-right">{{ rupiahKosong($tunai) }}</td>
                        <td class="text-right">{{ rupiahKosong($kredit) }}</td>
                        <td class="text-right">{{ rupiahKosong($titipan) }}</td>
                        <td class="text-right">{{ rupiahKosong($transfer) }}</td>
                        <td class="text-right">{{ rupiahKosong($giro) }}</td>
                        <td class="text-right">{{ rupiahKosong($voucher) }}</td>
                    </tr>

                    @if ($isBatal)
                        @php
                            $batalTunai += $tunai;
                            $batalKredit += $kredit;
                        @endphp
                    @else
                        @php
                            $totalTunai += $tunai;
                            $totalKredit += $kredit;
                            $totalTitipan += $titipan;
                            $totalTransfer += $transfer;
                            $totalGiro += $giro;
                            $totalVoucher += $voucher;
                        @endphp
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="2">TOTAL</td>
                    <td class="text-right">{{ rupiahKosong($totalTunai) }}</td>
                    <td class="text-right">{{ rupiahKosong($totalKredit) }}</td>
                    <td class="text-right">{{ rupiahKosong($totalTitipan) }}</td>
                    <td class="text-right">{{ rupiahKosong($totalTransfer) }}</td>
                    <td class="text-right">{{ rupiahKosong($totalGiro) }}</td>
                    <td class="text-right">{{ rupiahKosong($totalVoucher) }}</td>
                </tr>
                @if ($batalTunai > 0 || $batalKredit > 0)
                    <tr class="fw-bold" style="background-color: #ffe5e5;">
                        <td colspan="2">BATAL</td>
                        <td class="text-right text-danger">{{ rupiahKosong($batalTunai) }}</td>
                        <td class="text-right text-danger">{{ rupiahKosong($batalKredit) }}</td>
                        <td colspan="4"></td>
                    </tr>
                @endif
            </tfoot>
        </table>

        <table class="kotak-rekap">
            <tr>
                <td style="width:15%">Uang Kertas</td>
                <td style="width:2%; text-align:center">:</td>
                <td style="width:30%; text-align:right"></td>
                <td style="width:6%; text-align:right"></td>
                <td style="width:15%">Penjualan Tunai</td>
                <td style="width:2%; text-align:center">:</td>
                <td style="width:30%; text-align:right">{{ rupiahKosong($totalTunai) }}</td>
            </tr>
            <tr>
                <td>Uang Logam</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right"></td>
                <td style="width:6%; text-align:right"></td>
                <td>Tagihan</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right">{{ rupiahKosong($totalTitipan) }}</td>
            </tr>
            <tr>
                <td>Cek / BG</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right">{{ rupiahKosong(nilai: $totalGiro) }}</td>
                <td style="width:6%; text-align:right"></td>
                <td>Dikurangi</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right"></td>
            </tr>
            <tr>
                <td>Transfer</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right">{{ rupiahKosong($totalTransfer) }}</td>
                <td style="width:6%; text-align:right"></td>
                <td>Retur / BS</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right"></td>
            </tr>
            <tr>
                <td>Voucher</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right">{{ rupiahKosong($totalVoucher) }}</td>
                <td style="width:6%; text-align:right"></td>
                <td></td>
                <td style="text-align:center">:</td>
                <td></td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right"></td>
                <td style="width:6%; text-align:right"></td>
                <td></td>
                <td style="text-align:center">:</td>
                <td></td>
            </tr>
            <tr>
                <td>Setor</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right"></td>
                <td style="width:6%; text-align:right"></td>
                <td></td>
                <td style="text-align:center">:</td>
                <td></td>
            </tr>
            <tr>
                <td>Selisih</td>
                <td style="text-align:center">:</td>
                <td style="text-align:right"></td>
                <td style="width:6%; text-align:right"></td>
                <td></td>
                <td style="text-align:center">:</td>
                <td></td>
            </tr>
        </table>

        <table class="ttd">
            <tr>
                <td>
                    Dibuat oleh,<br><br><br>________________________<br>
                    <span class="jabatan">Salesman</span>
                </td>
                <td>
                    Mengetahui,<br><br><br>________________________<br>
                    <span class="jabatan">PIC Salesman</span>
                </td>
                <td>
                    __________________________<br>
                    <span class="jabatan">Sales Manager</span>
                </td>
            </tr>
        </table>

    </section>
</body>

</html>
