@extends('layouts.template')
@section('titlepage', 'Data Barang')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-box me-2"></i> Data Barang</h5>
                        @if ($PermissionTambah)
                            <a href="{{ route('tambahBarang') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Barang
                            </a>
                        @endif
                    </div>

                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewBarang') }}" class="mb-4" id="filterForm">
                            <div class="row g-2">
                                <!-- Filter Input -->
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
                                <div class="col-md-6 col-lg-4 d-flex gap-2">
                                    <button type="submit" name="action" value="filter"
                                        class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                    <button type="submit" name="action" value="cetak"
                                        class="btn btn-secondary btn-sm w-100 btn-open-blank">
                                        <i class="fa fa-print me-1"></i> Cetak
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
                                        <th style="width: 9%">Kode Barang</th>
                                        <th style="width: 9%">Kode Item</th>
                                        <th>Nama</th>
                                        <th style="width: 8%">Jenis</th>
                                        <th style="width: 11%">Merk</th>
                                        <th style="width: 8%">Stok Min</th>
                                        <th style="width: 10%">Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($barang->currentPage() - 1) * $barang->perPage() + 1; @endphp
                                    @foreach ($barang as $row)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $row->kode_barang }}</td>
                                            <td>{{ $row->kode_item }}</td>
                                            <td>{{ $row->nama_barang }}</td>
                                            <td class="text-capitalize">{{ $row->kategori }}</td>
                                            <td class="text-capitalize">{{ $row->merk }}</td>
                                            <td class="text-center">{{ $row->stok_min }}</td>
                                            <td class="text-center">
                                                @if ($row->status == '1')
                                                    <span class="btn btn-sm btn-success"><i class="fa fa-check"></i>
                                                        Aktif</span>
                                                @else
                                                    <span class="btn btn-sm btn-danger"><i class="fa fa-times"></i> Non
                                                        Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($PermissionShow)
                                                    <a href="{{ route('detailBarang', $row->kode_barang) }}"
                                                        class="btn btn-sm btn-info me-1">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if ($PermissionDelete)
                                                    <button data-href="{{ route('deleteBarang', $row->kode_barang) }}"
                                                        class="btn btn-sm btn-danger delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $barang->links('pagination::bootstrap-5') }}
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
        $(document).ready(function() {
            $('.btn-open-blank').on('click', function() {
                $('#filterForm').attr('target', '_blank');
            });

            $('button[name="action"][value="filter"]').on('click', function() {
                $('#filterForm').removeAttr('target');
            });
        });
    </script>
@endsection
