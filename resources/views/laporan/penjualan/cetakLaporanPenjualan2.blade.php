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
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Faktur</th>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>Wilayah</th>
                <th>Sales</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Merk</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>D1</th>
                <th>D2</th>
                <th>D3</th>
                <th>D4</th>
                <th>Total Barang</th>
                <th>Bayar</th>
                <th>Sisa</th>
                <th>Status</th>
                <th>JT</th>
                <th>Diinput</th>
                <th>Update</th>
                <th>Input Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalAll = $diskonAll = $grandAll = $bayarAll = $sisaAll = 0;
                $totalBatal = $diskonBatal = $grandBatal = $bayarBatal = $sisaBatal = 0;
                $prevFaktur = '';
                $rowColor = '#ffffff';
            @endphp

            @forelse($data as $d)
                @foreach ($d->detail as $item)
                    @php
                        $isNewFaktur = $d->no_faktur !== $prevFaktur;
                        if ($isNewFaktur) {
                            $rowColor = $rowColor === '#f9f9f9' ? '#ffffff' : '#f9f9f9';
                            $prevFaktur = $d->no_faktur;

                            if (!$d->batal) {
                                $bayarAll += $d->sudah_bayar;
                                $sisaAll += $d->sisa;
                            } else {
                                $bayarBatal += $d->sudah_bayar;
                                $sisaBatal += $d->sisa;
                            }
                        }

                        $batalStyle = $d->batal
                            ? 'background-color: rgba(255,0,0,0.1); color:#a00; text-decoration: line-through;'
                            : '';
                        $promoStyle = $item->is_promo == '1' ? 'background-color: #FF7F00;' : '';
                    @endphp
                    <tr style="background-color: {{ $rowColor }}; {{ $batalStyle }}">
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</td>
                        <td class="text-center">{{ $d->no_faktur }}</td>
                        <td class="text-center">{{ $d->kode_pelanggan }}</td>
                        <td>{{ $d->nama_pelanggan }}</td>
                        <td>{{ $d->alamat_toko }}</td>
                        <td>{{ $d->nama_wilayah }}</td>
                        <td>{{ $d->sales }}</td>

                        <td style="{{ $promoStyle }}">{{ $item->kode_barang }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->nama_barang }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->kategori }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->merk }}</td>
                        <td class="text-end" style="{{ $promoStyle }}">{{ $item->qty }}</td>
                        <td style="{{ $promoStyle }}">{{ $item->satuan }}</td>
                        <td class="text-end" style="{{ $promoStyle }}">{{ formatAngka($item->harga) }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon1_persen > 0 ? number_format($item->diskon1_persen, 2) : '' }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon2_persen > 0 ? number_format($item->diskon2_persen, 2) : '' }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon3_persen > 0 ? number_format($item->diskon3_persen, 2) : '' }}</td>
                        <td class="text-center" style="{{ $promoStyle }}">
                            {{ $item->diskon4_persen > 0 ? number_format($item->diskon4_persen, 2) : '' }}</td>

                        <td class="text-end" style="{{ $promoStyle }}">{{ formatAngka($item->total) }}</td>
                        <td class="text-end text-success">{{ formatAngka($d->sudah_bayar) }}</td>
                        <td class="text-end text-danger">{{ formatAngka($d->sisa) }}</td>
                        <td class="text-center fw-bold"
                            style="{{ $d->status == 'Batal' ? 'color:red;' : ($d->status == 'Lunas' ? 'color:green;' : '') }}">
                            {{ $d->status }}
                        </td>
                        <td class="text-center fw-bold"
                            style="{{ $d->jenis_transaksi == 'K' ? 'color:orange;' : ($d->jenis_transaksi == 'T' ? 'color:green;' : '') }}">
                            {{ $d->jenis_transaksi }}
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($d->created_at)->format('d M Y H:i') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($d->updated_at)->format('d M Y H:i') }}</td>
                        <td class="text-center">{{ $d->penginput }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="26" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="17" class="text-end">TOTAL</td>
                <td class="text-end">{{ formatAngka($totalAll) }}</td>
                <td class="text-end">{{ formatAngka($diskonAll) }}</td>
                <td class="text-end">{{ formatAngka($grandAll) }}</td>
                <td class="text-end">{{ formatAngka($bayarAll) }}</td>
                <td class="text-end">{{ formatAngka($sisaAll) }}</td>
                <td colspan="5"></td>
            </tr>
            @if ($totalBatal > 0)
                <tr style="background:#ffe3e3;">
                    <td colspan="17" class="text-end text-danger fw-bold">TOTAL FAKTUR BATAL</td>
                    <td class="text-end text-danger fw-bold">{{ formatAngka($totalBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ formatAngka($diskonBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ formatAngka($grandBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ formatAngka($bayarBatal) }}</td>
                    <td class="text-end text-danger fw-bold">{{ formatAngka($sisaBatal) }}</td>
                    <td colspan="5"></td>
                </tr>
            @endif
        </tfoot>
    </table>

</body>

</html>
