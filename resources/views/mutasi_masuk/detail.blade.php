@extends('layouts.template')
@section('titlepage', 'Detail Barang Masuk')
@section('contents')
    @php
        $totalQty = $detail->sum('qty');
    @endphp

    <div class="container-fluid mt-3 p-0 animate__animated animate__fadeIn">
        <div class="row">
            <div class="col-12 col-xl-12 mx-auto">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-box-arrow-in-down"></i> Detail Barang Masuk</h4>
                        <span class="badge bg-light text-primary fs-6">{{ $barangmasuk->kode_transaksi }}</span>
                    </div>

                    <div class="card-body">
                        <div class="row g-4 mt-1 mb-3">
                            <!-- Info Transaksi -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle"></i> Info Transaksi
                                    </h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Tanggal</th>
                                            <td>{{ tanggal_indo2($barangmasuk->tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sumber</th>
                                            <td>{{ $barangmasuk->sumber }}</td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $barangmasuk->keterangan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Input</th>
                                            <td>{{ $barangmasuk->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Oleh</th>
                                            <td>{{ $barangmasuk->user->name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Barang -->
                        <h5 class="fw-semibold mb-3"><i class="bi bi-bag-plus"></i> Daftar Barang Masuk</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered table-hover text-nowrap">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th style="width:10%">Kode</th>
                                        <th>Nama Barang</th>
                                        <th style="width:6%">Satuan</th>
                                        <th style="width:6%">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $i => $d)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $d->kode_barang }}</td>
                                            <td>{{ $d->nama_barang }}</td>
                                            <td class="text-center">{{ $d->satuan }}</td>
                                            <td class="text-center">{{ $d->qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($barangmasuk->tanggal_diterima)
                            <div class="border rounded p-3 bg-light mb-3">
                                <h6 class="fw-bold text-success mb-3"><i class="bi bi-check-circle me-2"></i> Barang
                                    Sudah
                                    Dikirim</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th style="width: 30%">Tanggal Kirim</th>
                                        <td>{{ tanggal_indo2($barangmasuk->tanggal_diterima) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Catatan</th>
                                        <td>{{ $barangmasuk->catatan ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        @else
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <form action="{{ route('storeTerimaBarang') }}" autocomplete="off" method="POST"
                                            id="formSuratJalan">
                                            @csrf
                                            <input type="hidden" name="kode_transaksi"
                                                value="{{ $barangmasuk->kode_transaksi }}">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-12">
                                                    <label for="tanggal" class="form-label small text-muted">Tanggal
                                                        Kirim</label>
                                                    <input type="date" value="{{ date('Y-m-d') }}" name="tanggal"
                                                        class="form-control form-control-sm shadow-sm" required>
                                                </div>

                                                <div class="col-md-12">
                                                    <label for="keterangan"
                                                        class="form-label small text-muted">Catatan</label>
                                                    <input type="text" name="keterangan"
                                                        class="form-control form-control-sm shadow-sm"
                                                        placeholder="Catatan Terima Barang (opsional)">
                                                </div>

                                                <div class="col-md-12 d-grid">
                                                    <label class="form-label small text-muted">&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-sm shadow">
                                                        <i class="fa fa-paper-plane me-1"></i> Terima Barang
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
