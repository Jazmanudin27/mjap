@extends('layouts.print')

@section('title', 'Laporan Persediaan Stok Good Stok')

@section('periode')
    Periode: {{ nama_bulan($bulan) }} {{ $tahun }} <br>
    @if ($nama_supplier)
        Supplier: <span style="font-weight:600; color:#0d6efd;">{{ $nama_supplier }}</span>
    @endif
@endsection

@section('content')
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
                <th rowspan="2">Conversi</th>
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
                <tr style="cursor:pointer" onclick="submitKartuStok('{{ $item->kode_barang }}')">
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->kode_item }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->merk }}</td>
                    <td class="text-end">
                        @if ($item->saldo_awal > 0)
                            {{ formatAngka($item->saldo_awal) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->pembelian > 0)
                            {{ formatAngka($item->pembelian) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->retur_pengganti > 0)
                            {{ formatAngka($item->retur_pengganti) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->repack > 0)
                            {{ formatAngka($item->repack) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->penyesuaian_masuk > 0)
                            {{ formatAngka($item->penyesuaian_masuk) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->lainnya_masuk > 0)
                            {{ formatAngka($item->lainnya_masuk) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->penjualan > 0)
                            {{ formatAngka($item->penjualan) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->reject_gudang > 0)
                            {{ formatAngka($item->reject_gudang) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->penyesuaian_keluar > 0)
                            {{ formatAngka($item->penyesuaian_keluar) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->lainnya_keluar > 0)
                            {{ formatAngka($item->lainnya_keluar) }}
                        @endif
                    </td>

                    @php
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
                    <td class="text-end fw-bold" style="{{ $saldoAkhir < 0 ? 'color:red;' : '' }}">
                        @if ($saldoAkhir != 0)
                            {{ formatAngka($saldoAkhir) }}
                        @endif
                    </td>
                    <td class="text-start fw-bold" style="{{ $saldoAkhir < 0 ? 'color:red;' : '' }}">
                        @php
                            $saldo = abs($saldoAkhir);
                            $konversi = [];
                            $satuanList = $satuan_barang[$item->kode_barang] ?? collect();

                            foreach ($satuanList as $sat) {
                                if ($sat->isi > 1) {
                                    $qty = intdiv($saldo, $sat->isi);
                                    if ($qty > 0) {
                                        $konversi[] = $qty . ' ' . $sat->satuan;
                                        $saldo = $saldo % $sat->isi;
                                    }
                                }
                            }

                            if ($saldo > 0) {
                                $konversi[] = $saldo . ' ' . $item->satuan;
                            }

                            $hasilKonversi = implode(', ', $konversi);
                            if ($saldoAkhir < 0) {
                                $hasilKonversi = '-' . $hasilKonversi; // kasih tanda minus di depan
                            }

                            echo $hasilKonversi;
                        @endphp
                    </td>
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

    @php
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

@endsection
