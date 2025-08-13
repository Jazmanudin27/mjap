@extends('layouts.template')
@section('titlepage', 'Data Diskon Strata')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-percent me-2"></i> Data Diskon Strata</h5>
                        <a href="{{ route('diskon_strata.create') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Diskon
                        </a>
                    </div>

                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('diskon_strata.index') }}" class="mb-4" id="filterForm">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="kode_barang" class="form-control form-control-sm"
                                        placeholder="Kode Barang" value="{{ request('kode_barang') }}">
                                </div>
                                <div class="col-md-4">
                                    <select name="supplier" class="form-select2 form-select-sm">
                                        <option value="">Supplier</option>
                                        @foreach ($suppliers as $s)
                                            <option value="{{ $s->kode_supplier }}"
                                                {{ request('supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                                {{ $s->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" name="action" value="filter"
                                        class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                    <button type="submit" name="action" value="export"
                                        class="btn btn-success btn-sm w-100 btn-open-blank">
                                        <i class="fa fa-file-excel me-1"></i> Export
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width: 3%">No</th>
                                        <th>Kode Barang</th>
                                        <th>Satuan</th>
                                        <th>Persentase</th>
                                        <th>Syarat</th>
                                        <th>Tipe Syarat</th>
                                        <th>Jenis Diskon</th>
                                        <th>Cash</th>
                                        <th>Supplier</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($diskon as $row)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $row->kode_barang }}</td>
                                            <td>{{ $row->nama_satuan }}</td>
                                            <td class="text-center">{{ $row->persentase }}%</td>
                                            <td>{{ number_format($row->syarat, 0, ',', '.') }}</td>
                                            <td class="text-capitalize">{{ $row->tipe_syarat }}</td>
                                            <td class="text-capitalize">{{ $row->jenis_diskon }}</td>
                                            <td class="text-center">{{ $row->cash ? 'Ya' : 'Tidak' }}</td>
                                            <td>{{ $row->nama_supplier }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('diskon_strata.edit', $row->id) }}"
                                                    class="btn btn-sm btn-warning me-1">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button data-href="{{ route('diskon_strata.destroy', $row->id) }}"
                                                    class="btn btn-sm btn-danger delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert Delete --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin Hapus?',
                        text: "Data akan hilang permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = this.getAttribute('data-href');
                        }
                    });
                });
            });
        });
    </script>
@endsection
