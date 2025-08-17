@extends('layouts.print')

@section('title', 'Laporan Persediaan Stok Good Stok')

@section('periode')
    Periode: {{ nama_bulan($bulan) }} {{ $tahun }} <br>
    @if ($nama_supplier)
        Supplier: <span style="font-weight:600; color:#0d6efd;">{{ $nama_supplier }}</span>
    @endif
@endsection

@section('content')

    @php
        function konversiQty($qty, $satuanDefault, $satuanList)
        {
            if ($qty == 0) {
                return '';
            }

            $saldo = abs($qty);
            $konversi = [];

            foreach ($satuanList as $sat) {
                if ($sat->isi > 1) {
                    $jumlah = intdiv($saldo, $sat->isi);
                    if ($jumlah > 0) {
                        $konversi[] = $jumlah . ' ' . $sat->satuan;
                        $saldo = $saldo % $sat->isi;
                    }
                }
            }

            if ($saldo > 0) {
                $konversi[] = $saldo . ' ' . $satuanDefault;
            }

            $hasil = implode(', ', $konversi);
            if ($qty < 0) {
                $hasil = '-' . $hasil;
            }

            return $hasil;
        }

        function nama_bulan($bulan)
        {
            $bulanIndo = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];
            return $bulanIndo[intval($bulan)] ?? '';
        }
    @endphp

    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 12px; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #0d6efd; color: white;">
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Kode Item</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2">Satuan</th>
                <th rowspan="2">Jenis</th>
                <th rowspan="2">Merk</th>
                <th rowspan="2">Saldo Awal</th>
                <th colspan="5" class="text-center" style="background-color: #28a745; color: white;">PENERIMAAN</th>
                <th colspan="4" class="text-center" style="background-color: #dc3545; color: white;">PENGELUARAN</th>
                <th rowspan="2">Saldo Akhir</th>
                {{-- <th rowspan="2">Conversi</th> --}}
            </tr>

            <tr style="background-color: #e3f0ff;">
                <th class="text-center" style="background-color: #1e7e34; color: white;">Pembelian</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Retur Pengganti</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Repack</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Penyesuaian</th>
                <th class="text-center" style="background-color: #1e7e34; color: white;">Lain-lain</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Penjualan</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Reject</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Penyesuaian</th>
                <th class="text-center" style="background-color: #c82333; color: white;">Lain-lain</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $tanggal_awal = date('Y-m-01');
                $tanggal_akhir = date('Y-m-t');
            @endphp
            @foreach ($data as $item)
                @php
                    $listSatuan = $satuan_barang[$item->kode_barang] ?? collect();
                    $penerimaan =
                        $item->pembelian +
                        $item->repack +
                        $item->retur_pengganti +
                        $item->penyesuaian_masuk +
                        $item->lainnya_masuk;
                    $pengeluaran =
                        $item->penjualan + $item->reject_gudang + $item->penyesuaian_keluar + $item->lainnya_keluar;
                    $saldoAkhir = $item->saldo_awal + $penerimaan - $pengeluaran;
                @endphp
                <tr style="cursor:pointer" onclick="submitKartuStok('{{ $item->kode_barang }}')">
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->kode_item }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->merk }}</td>

                    <td class="text-start">{{ konversiQty($item->saldo_awal, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->pembelian, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->retur_pengganti, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->repack, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->penyesuaian_masuk, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->lainnya_masuk, $item->satuan, $listSatuan) }}</td>

                    <td class="text-start">{{ konversiQty($item->penjualan, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->reject_gudang, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->penyesuaian_keluar, $item->satuan, $listSatuan) }}</td>
                    <td class="text-start">{{ konversiQty($item->lainnya_keluar, $item->satuan, $listSatuan) }}</td>

                    <td class="text-start fw-bold" style="{{ $saldoAkhir < 0 ? 'color:red;' : '' }}">
                        {{ konversiQty($saldoAkhir, $item->satuan, $listSatuan) }}
                    </td>
                    {{-- <td class="text-start fw-bold" style="{{ $saldoAkhir < 0 ? 'color:red;' : '' }}">
                        {{ konversiQty($saldoAkhir, $item->satuan, $listSatuan) }}
                    </td> --}}
                </tr>
                <form id="form-kartu-stok" action="{{ route('cetakKartuStok') }}" method="POST" target="_blank"
                    style="display:none;">
                    @csrf
                    <input type="hidden" name="tanggal_awal" value="{{ $tanggal_awal }}">
                    <input type="hidden" name="tanggal_akhir" value="{{ $tanggal_akhir }}">
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <input type="hidden" name="kode_barang" id="input-kode-barang">
                </form>
            @endforeach
        </tbody>
    </table>

    <script>
        function submitKartuStok(kodeBarang) {
            document.getElementById('input-kode-barang').value = kodeBarang;
            document.getElementById('form-kartu-stok').submit();
        }
    </script>
@endsection
