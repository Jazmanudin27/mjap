<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            margin-left: 15px;
            margin-right: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 4px;
            white-space: nowrap;
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
            padding: 2px;
            border: none;
        }

        .header-left {
            width: 60%;
        }

        .header-right {
            width: 40%;
        }

        .big-date {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            text-decoration: underline;
        }

        ul {
            padding-left: 16px;
        }

        .highlight {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
    </style>
</head>

<body>
    @php
        $no = 1;
        $grandTotal = 0;
        $totalDiskon = 0;
        $jmlhBayar = $data->jumlah_bayar ?? 0;
    @endphp

    <table class="header-table" style="margin-bottom: 10px; margin-top: 20px; width: 100%; font-size: 16px;zoom:110%">
        <tr>
            <td style="width: 20%; vertical-align: top;">
                <div style="font-weight: bold; font-size: 22px;">FAKTUR PENJUALAN</div>
                <div style="font-weight: bold; font-size: 18px;">CV MITRA JAYA ABADI PERSADA</div>
                <div>SIRNAGALIH INDIHIANG</div>
                <div>TASIKMALAYA</div>
                <div>Rek: CIMB NIAGA</div>
                <div>800190458700</div>
            </td>
            <td style="width: 40%; vertical-align: top; font-size: 14px">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%;">No Faktur</td>
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
                        <td>Alamat Toko</td>
                        <td colspan="3">: {{ $penjualan->alamat_pelanggan }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; vertical-align: top; font-size: 14px">
                <table style="width: 100%;">
                    <tr>
                        <td>Tgl. Jatuh Tempo</td>
                        <td>: {{ \Carbon\Carbon::parse($penjualan->created_at)->addDays(12)->format('d/m/Y') }}</td>
                    </tr>
                    <tr style="zoom:150%">
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
                        <td>:
                            @if ($penjualan->jenis_transaksi == 'T')
                                <b>Tunai</b>
                            @elseif ($penjualan->jenis_transaksi == 'K')
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
                <th style="width:2%">No</th>
                <th style="width:5%">Kode</th>
                <th>Nama</th>
                <th style="width:3%">Jml</th>
                <th style="width:3%">Satuan</th>
                <th style="width:9%">Harga</th>
                <th style="width:3%">D1</th>
                <th style="width:3%">D2</th>
                <th style="width:3%">D3</th>
                <th style="width:9%">Potongan</th>
                <th style="width:12%">Total</th>
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
                    <td>{{ $d->kode_barang }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td class="text-center">{{ round($qty) }}</td>
                    <td class="text-center">{{ strtoupper($d->satuan) }}</td>
                    <td class="text-end">{{ rupiah($harga) }}</td>
                    <td class="text-center">{{ round($d1, 2) ?: '' }}</td>
                    <td class="text-center">{{ round($d2, 2) ?: '' }}</td>
                    <td class="text-center">{{ round($d3, 2) ?: '' }}</td>
                    {{-- <td class="text-center">{{ round($d4, 2) ?: '' }}</td> --}}
                    <td class="text-end">{{ $potongan > 0 ? rupiah($potongan) : '' }}</td>
                    <td class="text-end">{{ rupiah($total) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold text-end">
                <td colspan="10">Subtotal</td>
                <td>{{ rupiah($grandTotal + $totalDiskon) }}</td>
            </tr>
            <tr class="fw-bold text-end">
                <td colspan="10">Potongan</td>
                <td>{{ rupiah($totalDiskon) }}</td>
            </tr>
            <tr class="fw-bold text-end highlight">
                <td colspan="10">Total Keseluruhan</td>
                <td>{{ rupiah($grandTotal) }}</td>
            </tr>
            <tr class="fw-bold text-end">
                <td colspan="10">Jumlah Bayar</td>
                <td>{{ rupiah($jmlhBayar) }}</td>
            </tr>
            @php
                $sisaBayar = $grandTotal - $jmlhBayar;
            @endphp
            <tr class="fw-bold text-end">
                <td colspan="10">Sisa Bayar</td>
                <td>
                    @if ($sisaBayar == 0)
                        LUNAS
                    @else
                        {{ rupiah($sisaBayar) }}
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    <table style="margin-top: 10px; border: none;">
        <tr>
            <td class="text-center">Penerima</td>
            <td class="text-center">Pengirim</td>
            <td class="text-center">Hormat Kami</td>
        </tr>
        <tr style="height: 50px;">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="text-center">(...................)</td>
            <td class="text-center">(...................)</td>
            <td class="text-center">(...................)</td>
        </tr>
    </table>

    <small>
        <ul>
            <li>Faktur asli adalah bukti pembayaran yang sah.</li>
            <li>Barang di mobil jadi tanggung jawab kurir/supir.</li>
            <li>Pembayaran dengan GIRO dianggap lunas setelah cair.</li>
            <li>Klaim tidak dilayani setelah faktur ditandatangani.</li>
        </ul>
    </small>
</body>

</html>
