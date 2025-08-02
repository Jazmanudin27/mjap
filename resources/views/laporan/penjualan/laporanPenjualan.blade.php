@extends('layouts.template')
@section('titlepage', 'Laporan Penjualan')

@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold">Laporan Penjualan</h5>
                </div>

                <div class="card-body pt-3">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Sidebar Tabs -->
                            <div class="col-md-3">
                                <div class="nav flex-column nav-pills me-3" id="tab-penjualan" role="tablist"
                                    aria-orientation="vertical">
                                    <button class="nav-link text-start active" id="tab-semua" data-bs-toggle="pill"
                                        data-bs-target="#panel-semua" type="button" role="tab"
                                        aria-controls="panel-semua" aria-selected="true">
                                        Laporan Penjualan
                                    </button>
                                    <button class="nav-link text-start" id="tab-per-pelanggan" data-bs-toggle="pill"
                                        data-bs-target="#panel-per-pelanggan" type="button" role="tab"
                                        aria-controls="panel-per-pelanggan" aria-selected="false">
                                        Laporan Per Pelanggan
                                    </button>
                                    <button class="nav-link text-start" id="tab-retur" data-bs-toggle="pill"
                                        data-bs-target="#panel-retur" type="button" role="tab"
                                        aria-controls="panel-retur" aria-selected="false">
                                        Laporan Retur Penjualan
                                    </button>
                                    <button class="nav-link text-start" id="tab-piutang" data-bs-toggle="pill"
                                        data-bs-target="#panel-piutang" type="button" role="tab"
                                        aria-controls="panel-piutang" aria-selected="false">
                                        Laporan Kartu Piutang
                                    </button>
                                    <button class="nav-link text-start" id="tab-umur" data-bs-toggle="pill"
                                        data-bs-target="#panel-umur" type="button" role="tab"
                                        aria-controls="panel-umur" aria-selected="false">
                                        Laporan Umur Piutang
                                    </button>
                                    <button class="nav-link text-start" id="tab-lph" data-bs-toggle="pill"
                                        data-bs-target="#panel-lph" type="button" role="tab" aria-controls="panel-lph"
                                        aria-selected="false">
                                        LPH
                                    </button>
                                    <button class="nav-link text-start" id="tab-target" data-bs-toggle="pill"
                                        data-bs-target="#panel-target" type="button" role="tab"
                                        aria-controls="panel-target" aria-selected="false">
                                        Target Sales
                                    </button>
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="col-md-9">
                                <div class="tab-content" id="tab-content-penjualan">
                                    <div class="tab-pane fade show active" id="panel-semua" role="tabpanel"
                                        aria-labelledby="tab-semua">
                                        @include('laporan.penjualan.laporanSemuaPenjualan')
                                    </div>
                                    <div class="tab-pane fade" id="panel-per-pelanggan" role="tabpanel"
                                        aria-labelledby="tab-per-pelanggan">
                                        @include('laporan.penjualan.laporanRekapPerPelanggan')
                                    </div>
                                    <div class="tab-pane fade" id="panel-retur" role="tabpanel" aria-labelledby="tab-retur">
                                        @include('laporan.penjualan.laporanReturPenjualan')
                                    </div>
                                    <div class="tab-pane fade" id="panel-piutang" role="tabpanel"
                                        aria-labelledby="tab-piutang">
                                        @include('laporan.penjualan.laporanKartuPiutang')
                                    </div>
                                    <div class="tab-pane fade" id="panel-umur" role="tabpanel" aria-labelledby="tab-umur">
                                        @include('laporan.penjualan.laporanAnalisaUmurPiutang')
                                    </div>
                                    <div class="tab-pane fade" id="panel-lph" role="tabpanel" aria-labelledby="tab-lph">
                                        @include('laporan.penjualan.laporanPenjualanHarian')
                                    </div>
                                    <div class="tab-pane fade" id="panel-target" role="tabpanel"
                                        aria-labelledby="tab-target">
                                        @include('laporan.penjualan.laporanTargetSales')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endsection
