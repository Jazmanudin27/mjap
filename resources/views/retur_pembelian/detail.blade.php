@extends('layouts.template')
@section('titlepage', 'Detail Retur Pembelian')
@section('contents')
    @php
        $totalQty = $detail->sum('qty');
        $totalRetur = $detail->sum('subtotal_retur');
    @endphp

    <div class="container-fluid mt-3 p-0 animate__animated animate__fadeIn">
        <div class="row">
            <div class="col-12 col-xl-12 mx-auto">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-arrow-counterclockwise"></i> Detail Retur Pembelian</h4>
                        <span class="badge bg-light text-danger fs-6">{{ $retur->no_retur }}</span>
                    </div>

                    <div class="card-body">
                        <div class="row g-4 mt-1 mb-3">
                            <!-- Info Retur -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-danger fw-bold mb-3"><i class="bi bi-info-circle"></i> Info Retur</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Tanggal</th>
                                            <td>{{ tanggal_indo2($retur->tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Retur</th>
                                            <td>{{ $retur->jenis_retur == 'GB' ? 'Ganti Barang' : 'Potong Nota' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No Faktur</th>
                                            <td>{{ $retur->no_faktur }}</td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $retur->keterangan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Input</th>
                                            <td>{{ $retur->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <th>Admin</th>
                                            <td>{{ $retur->nama_admin ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Info Supplier -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-danger fw-bold mb-3"><i class="bi bi-truck"></i> Info Supplier</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Kode Supplier</th>
                                            <td>{{ $retur->kode_supplier }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Supplier</th>
                                            <td>{{ $retur->nama_supplier ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $retur->alamat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>{{ $retur->no_hp ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Barang Retur -->
                        <h5 class="fw-semibold mb-3"><i class="bi bi-box-arrow-left"></i> Barang Retur</h5>

                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered table-hover text-nowrap">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th style="width:10%">Kode</th>
                                        <th>Nama Barang</th>
                                        <th style="width:10%">Qty</th>
                                        <th style="width:15%">Harga Retur</th>
                                        <th style="width:15%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detail as $i => $d)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $d->kode_barang }}</td>
                                            <td>{{ $d->nama_barang }}</td>
                                            <td class="text-end">{{ $d->qty }}</td>
                                            <td class="text-end">{{ rupiah($d->harga_retur) }}</td>
                                            <td class="text-end">{{ rupiah($d->subtotal_retur) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light text-end">
                                    <tr>
                                        <th colspan="5" class="text-end">Total Retur</th>
                                        <th>{{ rupiah($totalRetur) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
