@extends('layouts.template')
@section('titlepage', 'Detail Purchase Order')
@section('contents')
    @php
        $totalDiskon = $detail->sum('diskon');
        $grandTotal = $detail->sum('subtotal');
        $karyawan = DB::table('hrd_karyawan')->where('nik', $po->id_user)->first();
    @endphp
    <div class="container-fluid mt-3 p-0 animate__animated animate__fadeIn">
        <div class="row">
            <div class="col-12 col-xl-12 mx-auto">
                <div class="card border-0 shadow-lg">
                    <div
                        class="card-header bg-primary shadow-sm bg-gradient text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-file-earmark-text"></i> Detail Purchase Order</h4>
                        <span class="badge bg-light text-primary fs-6">{{ $po->no_po }}</span>
                    </div>

                    <div class="card-body">
                        <div class="row g-4 mt-1 mb-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle"></i> Info PO</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Tanggal</th>
                                            <td>{{ tanggal_indo2($po->tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jatuh Tempo</th>
                                            <td>{{ tanggal_indo2($po->jatuh_tempo) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Transaksi</th>
                                            <td>
                                                <span
                                                    class="badge {{ $po->jenis_transaksi == 'Tunai' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                    {{ $po->jenis_transaksi }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Potongan</th>
                                            <td>{{ rupiah($po->potongan) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Potongan Klaim</th>
                                            <td>{{ rupiah($po->potongan_claim) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total</th>
                                            <td>{{ rupiah($po->grand_total) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><span
                                                    class="badge bg-{{ $po->status == 'closed' ? 'success' : ($po->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($po->status) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $po->keterangan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Input</th>
                                            <td>{{ $po->created_at ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Penginput</th>
                                            <td>{{ $karyawan->nama_lengkap ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-truck"></i> Info Supplier</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Kode Supplier</th>
                                            <td>{{ $supplier->kode_supplier }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Supplier</th>
                                            <td>{{ $supplier->nama_supplier }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $supplier->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>{{ $supplier->no_hp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempo</th>
                                            <td>{{ $supplier->tempo }} Hari</td>
                                        </tr>
                                        <tr>
                                            <th>PPN</th>
                                            <td>{{ $supplier->ppn == 1 ? 'Include' : 'Exclude' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-semibold mb-3"><i class="bi bi-box-seam"></i> Detail Barang</h5>
                        <div class="table-responsive mb-4" style="max-height:500px; overflow-y:auto;">
                            <table class="table table-bordered table-sm table-hover text-nowrap">
                                <thead class="table-light text-center sticky-top bg-white shadow-sm">
                                    <tr>
                                        <th style="width:3%">No</th>
                                        <th style="width:5%">Kode</th>
                                        <th>Nama</th>
                                        <th style="width:6%">Jumlah</th>
                                        <th style="width:8%">Satuan</th>
                                        <th style="width:10%">Harga</th>
                                        <th style="width:10%">Potongan</th>
                                        <th style="width:12%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $d->kode_barang }}</td>
                                            <td>{{ $d->nama_barang }}</td>
                                            <td class="text-end">{{ $d->qty }}</td>
                                            <td><span class="badge bg-secondary">{{ strtoupper($d->satuan) }}</span></td>
                                            <td class="text-end">{{ rupiah($d->harga) }}</td>
                                            <td class="text-end">{{ rupiah($d->diskon) }}</td>
                                            <td class="text-end">{{ rupiah($d->harga * $d->qty - $d->diskon) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold text-end table-primary">
                                        <td colspan="7" class="text-end"><i class="fas fa-receipt"></i> Potongan Klaim
                                        </td>
                                        <td>{{ rupiah($po->potongan_claim) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-success">
                                        <td colspan="7" class="text-end"><i class="bi bi-cash-coin"></i> Grand Total</td>
                                        <td>{{ rupiah($po->grand_total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @php
                            $pembelian = DB::table('pembelian')->where('no_po', $po->no_po)->get();

                            $pembelianDetail = DB::table('pembelian_detail')
                                ->join('barang_satuan', 'barang_satuan.id', '=', 'pembelian_detail.satuan_id')
                                ->join('barang', 'barang.kode_barang', '=', 'barang_satuan.kode_barang')
                                ->join('pembelian', 'pembelian_detail.no_faktur', '=', 'pembelian.no_faktur')
                                ->where('pembelian.no_po', $po->no_po)
                                ->select('pembelian_detail.*', 'pembelian.tanggal','barang.nama_barang')
                                ->orderBy('pembelian.tanggal')
                                ->get()
                                ->groupBy('no_faktur');
                        @endphp

                        <h5 class="fw-semibold mb-3"><i class="bi bi-truck-flatbed"></i> Barang yang Sudah Diterima</h5>
                        @if ($pembelianDetail->isEmpty())
                            <p class="text-muted fst-italic">Belum ada barang yang diterima berdasarkan PO ini.</p>
                        @else
                            @foreach ($pembelianDetail as $no_faktur => $items)
                                <div class="mb-3">
                                    <h6 class="text-primary mb-1">
                                        <i class="bi bi-file-earmark-text-fill"></i> No. Faktur: <b>{{ $no_faktur }}</b>
                                        <span
                                            class="badge bg-secondary ms-2">{{ tanggal_indo2($items->first()->tanggal) }}</span>
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover text-nowrap mb-0">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th style="width: 3%">No</th>
                                                    <th style="width: 5%">Kode</th>
                                                    <th>Nama</th>
                                                    <th style="width: 6%">Jumlah</th>
                                                    <th style="width: 8%">Satuan</th>
                                                    <th style="width: 10%">Harga</th>
                                                    <th style="width: 10%">Potongan</th>
                                                    <th style="width: 12%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach ($items as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $no++ }}</td>
                                                        <td>{{ $item->kode_barang }}</td>
                                                        <td>{{ $item->nama_barang }}</td>
                                                        <td class="text-end">{{ $item->qty }}</td>
                                                        <td><span
                                                                class="badge bg-info text-dark">{{ strtoupper($item->satuan) }}</span>
                                                        </td>
                                                        <td class="text-end">{{ rupiah($item->harga) }}</td>
                                                        <td class="text-end">{{ rupiah($item->diskon) }}</td>
                                                        <td class="text-end">{{ rupiah($item->subtotal) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container-fluid -->
@endsection
