@extends('layouts.template')
@section('titlepage', 'Data Saldo Awal')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-wallet me-2"></i> Data Saldo Awal GS</h5>
                        <a href="{{ route('createSaldoAwalGS') }}"
                            class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2">
                            <i class="fa fa-plus-circle"></i> <span>Tambah Saldo Awal</span>
                        </a>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewSaldoAwalGS') }}" class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-6 col-lg-4">
                                    <input type="text" name="nama_barang" class="form-control form-control-sm"
                                        placeholder="Nama Barang" value="{{ request('nama_barang') }}">
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <input type="text" name="kode_barang" class="form-control form-control-sm"
                                        placeholder="Kode Barang" value="{{ request('kode_barang') }}">
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <select name="supplier" class="form-select2 form-select-sm">
                                        <option value="">Supplier</option>
                                        @foreach ($suppliers as $s)
                                            <option value="{{ $s->kode_supplier }}"
                                                {{ request('supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                                {{ $s->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <select name="status" class="form-select2 form-select-sm">
                                        <option value="">Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non Aktif
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width: 3%">No</th>
                                        <th style="width: 8%">Kode</th>
                                        <th>Nama</th>
                                        <th style="width: 8%">Jenis</th>
                                        <th style="width: 8%">Stok Min</th>
                                        <th style="width: 10%">Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{-- {{ $barang->links('pagination::bootstrap-5') }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
