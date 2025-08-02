@extends('layouts.template')
@section('titlepage', 'Data Supplier')

@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa fa-truck me-2"></i> Data Supplier
                        </h5>
                        @if (!empty($TambahSupplier))
                            <a href="{{ route('tambahSupplier') }}"
                                class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2">
                                <i class="fa fa-plus-circle"></i> <span>Tambah Data</span>
                            </a>
                        @endif
                    </div>

                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewSupplier') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="nama_supplier" class="form-control form-control-sm"
                                        placeholder="Nama Supplier" value="{{ request('nama_supplier') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="kode_supplier" class="form-control form-control-sm"
                                        placeholder="Kode Supplier" value="{{ request('kode_supplier') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select2 form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kode</th>
                                        <th>Nama Supplier</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($supplier as $index => $s)
                                        <tr>
                                            <td class="text-center">{{ $supplier->firstItem() + $index }}</td>
                                            <td>{{ $s->kode_supplier }}</td>
                                            <td>{{ $s->nama_supplier }}</td>
                                            <td>{{ $s->alamat }}</td>
                                            <td>{{ $s->no_hp }}</td>
                                            <td>{{ $s->email }}</td>
                                            <td class="text-center">
                                                @if (!empty($StatusSupplier))
                                                    <a href="{{ route('toggleStatusSupplier', $s->kode_supplier) }}"
                                                        class="btn btn-sm {{ $s->status == '1' ? 'btn-success' : 'btn-danger' }}">
                                                        <i
                                                            class="fa {{ $s->status == '1' ? 'fa-thumbs-up' : 'fa-thumbs-down' }}"></i>
                                                    </a>
                                                @else
                                                    <span class="btn btn-sm {{ $s->status == '1' ? 'btn-success' : 'btn-danger' }}">
                                                        {{ $s->status == '1' ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($DeleteSupplier))
                                                    <button type="button" class="btn btn-sm btn-danger delete"
                                                        data-href="{{ route('deleteSupplier', $s->kode_supplier) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endif
                                                @if (!empty($DetailSupplier))
                                                    <a href="{{ route('detailSupplier', $s->kode_supplier) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Tidak ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $supplier->links('pagination::bootstrap-5') }}
                        </div>
                    </div> <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $(document).on("click", ".delete", function (e) {
                e.preventDefault();
                const url = $(this).data('href');

                Swal.fire({
                    title: 'Hapus data?',
                    text: 'Data supplier akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endsection
