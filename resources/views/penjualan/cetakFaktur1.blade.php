<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 14px;
            margin: 0;
            line-height: 1.2;
            width: 210mm;
        }

        table {
            border-collapse: collapse;
            width: 100%;
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

        ul {
            padding-left: 16px;
            margin: 5px 0 0 0;
        }

        small {
            font-size: 11px;
        }

        .col-no {
            width: 20px;
        }

        .col-kode {
            width: 60px;
        }

        .col-nama {
            width: 200px;
        }

        .col-jml {
            width: 30px;
        }

        .col-satuan {
            width: 30px;
        }

        .col-harga {
            width: 70px;
        }

        .col-diskon {
            width: 30px;
        }

        .col-total {
            width: 90px;
        }

        .header-title {
            font-weight: bold;
            font-size: 18px;
        }

        .header-subtitle {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .header-address {
            font-size: 13px;
        }

        .info-table td {
            padding: 1px 3px;
        }

        .alamat-toko {
            white-space: normal;
            word-break: break-word;
        }
    </style>
</head>

<body>
    @php
        $no = 1;
        $grandTotal = 0;
        $totalDiskon = 0;
        $jmlhBayar = $data->jumlah_bayar ?? 0;

        // Cek apakah ada diskon
        $adaDiskon = false;
        foreach ($detail as $d) {
            if (
                ($d->diskon1_persen ?? 0) > 0 ||
                ($d->diskon2_persen ?? 0) > 0 ||
                ($d->diskon3_persen ?? 0) > 0 ||
                ($d->diskon4_persen ?? 0) > 0
            ) {
                $adaDiskon = true;
                break;
            }
        }
    @endphp

    <body>
        @php
            $no = 1;
            $grandTotal = 0;
            $totalDiskon = 0;

            $showD1 = false;
            $showD2 = false;
            $showD3 = false;

            foreach ($detail as $d) {
                if (($d->diskon1_persen ?? 0) > 0) {
                    $showD1 = true;
                }
                if (($d->diskon2_persen ?? 0) > 0) {
                    $showD2 = true;
                }
                if (($d->diskon3_persen ?? 0) > 0) {
                    $showD3 = true;
                }
            }

            $colspanCount = 6;
            $diskonCols = 0;
            if ($showD1) {
                $diskonCols++;
            }
            if ($showD2) {
                $diskonCols++;
            }
            if ($showD3) {
                $diskonCols++;
            }
            $colspanCount += $diskonCols;
        @endphp

        <table class="header-table" style="margin: 10px 0;">
            <tr>
                <td style="width: 30%;">
                    <div style="display: flex; align-items: center; gap: 5px;padding-bottom:8px;">
                        <img src="{{ asset('assets/img/MJAP.png') }}" alt="Logo MJAP" style="height: 50px;">
                        <div>
                            <div class="header-title">FAKTUR PENJUALAN</div>
                            <div class="header-subtitle">CV MITRA JAYA ABADI PERSADA</div>
                        </div>
                    </div>
                    <div class="header-address">SIRNAGALIH INDIHIANG</div>
                    <div class="header-address">TASIKMALAYA</div>
                    <div class="header-address">Rek: CIMB NIAGA</div>
                    <div class="header-address">800190458700</div>
                </td>
                <td style="width: 40%; font-size: 14px;">
                    <table class="info-table">
                        <tr>
                            <td>No Faktur</td>
                            <td>: {{ $penjualan->no_faktur }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Pelanggan</td>
                            <td>: {{ $penjualan->nama_pelanggan }}</td>
                        </tr>
                        <tr>
                            <td>Sales</td>
                            <td>: {{ $penjualan->nama_sales }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Alamat Toko</td>
                            <td class="alamat-toko">: {{ substr($penjualan->alamat_pelanggan, 0, 50) }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%; font-size: 14px;">
                    <table class="info-table">
                        <tr>
                            <td>Tgl. Jatuh Tempo</td>
                            <td>: {{ \Carbon\Carbon::parse($penjualan->created_at)->addDays(12)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Wilayah</td>
                            <td>: <b>{{ $penjualan->nama_wilayah }}</b></td>
                        </tr>
                        <tr>
                            <td>Input</td>
                            <td>: {{ $penjualan->nama_user }}</td>
                        </tr>
                        <tr>
                            <td>Dicetak</td>
                            <td>: {{ Auth::user()->name }}</td>
                        </tr>
                        <tr>
                            <td>Jenis Transaksi</td>
                            <td>: @if ($penjualan->jenis_transaksi == 'T')
                                    <b>Tunai</b>
                                @else
                                    <b>Kredit</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>: {{ $penjualan->keterangan }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr class="text-center">
                    <th class="col-no">No</th>
                    <th class="col-kode">Kode</th>
                    <th class="col-nama">Nama</th>
                    <th class="col-jml">Jml</th>
                    <th class="col-satuan">Satuan</th>
                    <th class="col-harga">Harga</th>
                    @if ($showD1)
                        <th class="col-diskon">D1</th>
                    @endif
                    @if ($showD2)
                        <th class="col-diskon">D2</th>
                    @endif
                    @if ($showD3)
                        <th class="col-diskon">D3</th>
                    @endif
                    <th class="col-total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $d)
                    @php
                        $d1 = $d->diskon1_persen ?? 0;
                        $d2 = $d->diskon2_persen ?? 0;
                        $d3 = $d->diskon3_persen ?? 0;
                        $d4 = $d->diskon4_persen ?? 0;
                        $harga = $d->harga;
                        $qty = $d->qty;

                        $hargaSetelahDiskon = $harga;
                        foreach ([$d1, $d2, $d3, $d4] as $diskon) {
                            $hargaSetelahDiskon -= ($hargaSetelahDiskon * $diskon) / 100;
                        }

                        $total = $hargaSetelahDiskon * $qty;
                        $potongan = $harga * $qty - $total;

                        $grandTotal += $total;
                        $totalDiskon += $potongan;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $d->kode_item }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td class="text-center">{{ round($qty) }}</td>
                        <td class="text-center">{{ strtoupper($d->satuan) }}</td>
                        <td class="text-end">{{ rupiah($harga) }}</td>
                        @if ($showD1)
                            <td class="text-center">{{ round($d1, 2) ?: '' }}</td>
                        @endif
                        @if ($showD2)
                            <td class="text-center">{{ round($d2, 2) ?: '' }}</td>
                        @endif
                        @if ($showD3)
                            <td class="text-center">{{ round($d3, 2) ?: '' }}</td>
                        @endif
                        <td class="text-end">{{ rupiah($total) }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold">
                    <td class="text-start" rowspan="3" colspan="3">
                        <small>
                            <ul style="margin:0; padding-left: 15px;">
                                1. Faktur asli adalah bukti pembayaran yang sah.<br>
                                2. Barang di mobil jadi tanggung jawab kurir/supir.<br>
                                3. Pembayaran dengan GIRO dianggap lunas setelah cair.<br>
                                4. Klaim tidak dilayani setelah faktur ditandatangani.<br>
                            </ul>
                        </small>
                    </td>
                    <td class=" text-end" colspan="{{ $colspanCount - 3 }}">Subtotal</td>
                    <td class=" text-end">{{ rupiah($grandTotal + $totalDiskon) }}</td>
                </tr>
                <tr class="fw-bold text-end">
                    <td colspan="{{ $colspanCount - 3 }}">Potongan</td>
                    <td>{{ rupiah($totalDiskon) }}</td>
                </tr>
                <tr class="fw-bold text-end highlight">
                    <td colspan="{{ $colspanCount - 3 }}">Total Keseluruhan</td>
                    <td>{{ rupiah($grandTotal) }}</td>
                </tr>
            </tbody>
        </table>

        <table style="margin-top: 10px; border-collapse: collapse; width: 100%;">
            <tr>
                <td class="text-center" style="width:30%; border: none;">Penerima</td>
                <td class="text-center" style="width:30%; border: none;">Pengirim</td>
                <td class="text-center" style="width:30%; border: none;">Hormat Kami</td>
                <td rowspan="3" style="border: none; vertical-align: top;">

                </td>
            </tr>
            <tr style="height: 35px;">
                <td style="border: none;"></td>
                <td style="border: none;"></td>
                <td style="border: none;"></td>
            </tr>
            <tr>
                <td class="text-center" style="border: none;">(...................)</td>
                <td class="text-center" style="border: none;">(...................)</td>
                <td class="text-center" style="border: none;">(...................)</td>
            </tr>
        </table>

    </body>

</html>
