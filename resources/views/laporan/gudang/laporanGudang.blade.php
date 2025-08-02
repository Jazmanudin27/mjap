@extends('layouts.template')
@section('titlepage', 'Laporan Gudang')

@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold">Laporan Gudang</h5>
                </div>

                <div class="card-body pt-3">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Sidebar Tabs -->
                            <div class="col-md-3">
                                <div class="nav flex-column nav-pills me-3" id="tab-pesediaan" role="tablist"
                                    aria-orientation="vertical">
                                    <button class="nav-link text-start active" id="tab-mutasi" data-bs-toggle="pill"
                                        data-bs-target="#panel-mutasi" type="button" role="tab" aria-controls="panel-mutasi"
                                        aria-selected="true">
                                        Laporan Mutasi Barang
                                    </button>
                                    {{-- <button class="nav-link text-start" id="tab-persediaan" data-bs-toggle="pill"
                                        data-bs-target="#panel-persediaan" type="button" role="tab"
                                        aria-controls="panel-persediaan" aria-selected="false">
                                        Laporan Persediaan
                                    </button> --}}
                                    <button class="nav-link text-start" id="tab-gs" data-bs-toggle="pill"
                                        data-bs-target="#panel-gs" type="button" role="tab" aria-controls="panel-gs"
                                        aria-selected="false">
                                        Rekap Persediaan GS
                                    </button>
                                    <button class="nav-link text-start" id="tab-bs" data-bs-toggle="pill"
                                        data-bs-target="#panel-bs" type="button" role="tab" aria-controls="panel-bs"
                                        aria-selected="false">
                                        Rekap Persediaan BS
                                    </button>
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="col-md-9">
                                <div class="tab-content" id="tab-content-persediaan">
                                    <div class="tab-pane fade show active" id="panel-mutasi" role="tabpanel"
                                        aria-labelledby="tab-mutasi">
                                        @include('laporan.gudang.laporanMutasiBarang')
                                    </div>
                                    {{-- <div class="tab-pane fade" id="panel-persediaan" role="tabpanel"
                                        aria-labelledby="tab-persediaan">
                                        @include('laporan.gudang.laporanPersediaan')
                                    </div> --}}
                                    <div class="tab-pane fade" id="panel-gs" role="tabpanel" aria-labelledby="tab-gs">
                                        @include('laporan.gudang.laporanPersediaanGS')
                                    </div>
                                    <div class="tab-pane fade" id="panel-bs" role="tabpanel" aria-labelledby="tab-bs">
                                        @include('laporan.gudang.laporanPersediaanBS')
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
