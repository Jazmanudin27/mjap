@extends('layouts.template')
@section('titlepage', 'Laporan Pembelian')

@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold">Laporan Pembelian</h5>
                </div>

                <div class="card-body pt-3">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Sidebar Tabs -->
                            <div class="col-md-3">
                                <div class="nav flex-column nav-pills me-3" id="tab-pembelian" role="tablist"
                                    aria-orientation="vertical">
                                    <button class="nav-link text-start active" id="tab-semua" data-bs-toggle="pill"
                                        data-bs-target="#panel-semua" type="button" role="tab" aria-controls="panel-semua"
                                        aria-selected="true">
                                        Laporan Pembelian
                                    </button>
                                    <button class="nav-link text-start" id="tab-per-supplier" data-bs-toggle="pill"
                                        data-bs-target="#panel-per-supplier" type="button" role="tab"
                                        aria-controls="panel-per-supplier" aria-selected="false">
                                        Laporan Per Supplier
                                    </button>
                                    <button class="nav-link text-start" id="tab-retur" data-bs-toggle="pill"
                                        data-bs-target="#panel-retur" type="button" role="tab" aria-controls="panel-retur"
                                        aria-selected="false">
                                        Laporan Retur Pembelian
                                    </button>
                                    <button class="nav-link text-start" id="tab-hutang" data-bs-toggle="pill"
                                        data-bs-target="#panel-hutang" type="button" role="tab" aria-controls="panel-hutang"
                                        aria-selected="false">
                                        Laporan Kartu Hutang
                                    </button>
                                    <button class="nav-link text-start" id="tab-umur" data-bs-toggle="pill"
                                        data-bs-target="#panel-umur" type="button" role="tab" aria-controls="panel-umur"
                                        aria-selected="false">
                                        Laporan Umur Hutang
                                    </button>
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="col-md-9">
                                <div class="tab-content" id="tab-content-pembelian">
                                    <div class="tab-pane fade show active" id="panel-semua" role="tabpanel"
                                        aria-labelledby="tab-semua">
                                        @include('laporan.pembelian.laporanSemuaPembelian')
                                    </div>
                                    <div class="tab-pane fade" id="panel-per-supplier" role="tabpanel"
                                        aria-labelledby="tab-per-supplier">
                                        @include('laporan.pembelian.laporanRekapPerSupplier')
                                    </div>
                                    <div class="tab-pane fade" id="panel-retur" role="tabpanel" aria-labelledby="tab-retur">
                                        @include('laporan.pembelian.laporanReturPembelian')
                                    </div>
                                    <div class="tab-pane fade" id="panel-hutang" role="tabpanel"
                                        aria-labelledby="tab-hutang">
                                        @include('laporan.pembelian.laporanKartuHutang')
                                    </div>
                                    <div class="tab-pane fade" id="panel-umur" role="tabpanel" aria-labelledby="tab-umur">
                                        @include('laporan.pembelian.laporanAnalisaUmurHutang')
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
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });
        });
    </script>
@endsection
